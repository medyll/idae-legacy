<?
	include_once($_SERVER['CONF_INC']);
	//
	//CRUISE-ID;
	//DEP-PORT;
	//DEP-NAME-PORT;
	//DEP-DATE;
	//DEP-DAY;
	//DEP-WEEKDAY;
	//DEP-TIME;
	//ARR-PORT;
	//ARR-NAME-PORT;
	//ARR-DATE;
	//ARR-DAY;
	//ARR-WEEKDAY;
	//ARR-TIME;
	//ITIN-CD;
	//AREA/DEST
	//CREATION-DATE
	//LAST-CHANGE-DATE

	ignore_user_abort(true);
	set_time_limit(0);

	$App    = new App();
	$handle = fopen(XMLDIR . 'msc/itinff_fra_fra.csv', 'r');
	$out    = [];
	//
	$col      = $App->plug('sitebase_csv', 'msc_csv_iti');
	$colMsc   = $App->plug('sitebase_csv', 'msc_csv');
	$col_iata = $App->plug('sitebase_csv', 'msc_csv_iata');

	$col->ensureIndex(['CRUISE-ID' => 1]);
	$col->ensureIndex(['UNIQUE-ID' => 1]);

	$col_iata->ensureIndex(['PORT_CD' => 1]);
	$col_iata->ensureIndex(['CRUISE-ID' => 1]);

	$colMsc->ensureIndex(['CRUISE-ID' => 1]);
	$colMsc->ensureIndex(['UNIQUE-ID' => 1]);

	$vars = ['notify' => 'HTTP XML MSC ITI'];
	skelMdl::reloadModule('activity/appActivity', '*', $vars);

	//
	$ports   = [];
	$COLLECT = [];
	if ($handle) {
		set_time_limit(0);
		$fields       = fgetcsv($handle, 4096, ';');
		$modulo       = 150;
		$modulo_count = 0;

		while (($data = fgetcsv($handle, 4096, ';')) !== FALSE) {

			if (($modulo_count % $modulo) == 0 || $modulo_count == 0) {
				$vars = ['notify' => 'iti 1  ' . $modulo_count . '/' . $max];
				skelMdl::reloadModule('activity/appActivity', $_SESSION['idagent'], $vars);
			}
			$modulo_count++;

			$out = safeArrayCombine($fields, $data);

			$out['CRUISE']    = $out['CRUISE-ID'];
			$out['PORT']      = $out['PORT_CD'] = $out['DEP-PORT'];
			$out['PORT_NAME'] = $out['DEP-NAME-PORT'];
			$out['DATE']      = $out['DEP-DATE'];
			$out['DAY']       = (int)$out['DEP-DAY'];
			if (!empty($out['DEP-TIME'])) {
				$dateAR         = DateTime::createFromFormat('d/m/y', $out['DATE']);
				$out['DATE-US'] = $dateAR->format('Y-m-d');
			}
			if (!empty($out['DEP-TIME'])) {
				$dateAR           = DateTime::createFromFormat('Hi', $out['DEP-TIME']);
				$out['DEPARTURE'] = $dateAR->format('H:i');
			}
			if (!empty($out['ARR-TIME'])) {
				$dateAR         = DateTime::createFromFormat('Hi', $out['ARR-TIME']);
				$out['ARRIVAL'] = $dateAR->format('H:i');
			}

			unset($out['DEP-DATE'], $out['DEP-DAY'], $out['DEP-TIME'], $out['DEP-PORT'], $out['DEP-NAME-PORT']);

			$ports[$out['PORT']] = $out['PORT'];
			$uniqueid            = $out['CRUISE'] . '_' . $out['DAY'];

			$col->update(['CRUISE-ID' => $out['CRUISE-ID'], 'DATE-US' => $out['DATE-US']], ['$set' => $out], ['upsert' => true]);

			$COLLECT[$out['ITIN-CD']]['ITI']['DAY_' . $out['DAY']] = $out;
			//
			$PORT_CD   = $out['PORT_CD'] = trim($out['PORT']);
			$PORT_NAME = trim($out['PORT_NAME']);

			$test    = $col_iata->findOne(['PORT_CD' => $PORT_CD]);
			$rsVille = skelMongo::connect('ville')->find(['codeVille' => $PORT_CD]);
			$arrV    = $rsVille->getNext();
			$vars    = ['PORT_CD' => $PORT_CD, 'PORT_NAME' => $PORT_NAME];
			if ($rsVille->count() != 0 && empty($test['idville'])) {
				$vars['idville']   = (int)$arrV['idville'];
				$vars['codeVille'] = $arrV['codeVille'];
				$vars['nomVille']  = $arrV['nomVille'];
			}
			$col_iata->update(['PORT_CD' => $PORT_CD], ['$set' => $vars], ['upsert' => true]);
			//echo"<hr>";
		}
		fclose($handle);
	}
	echo "part 1 / 2 ";
	// boucle ds csv
	$rs = $colMsc->find();
	while ($arr = $rs->getNext()):
		$rsITI = $col->find(['CRUISE-ID' => $arr['CRUISE-ID']])->sort(['DATE-US' => 1]);
		$i     = 0;
		$last  = [];
		$out   = [];
		while ($arrIti = $rsITI->getNext()):
			$i++;
			unset($arrIti['_id']);
			$out[$i]           = $arrIti;
			$DISEMBARK_PORT_CD = $arrIti['PORT_CD'];
		endwhile;
		$colMsc->update(['CRUISE-ID' => $arr['CRUISE-ID']], ['$set' => ['DISEMBARK_PORT_CD' => $DISEMBARK_PORT_CD, 'ITI' => $out]], ['upsert' => true]);
	endwhile;

	// nettoyage
	$colMsc->remove(['ITI' => ['$size' => 0]], ['multiple' => true]);
	$colMsc->remove(['ITI' => ['$size' => 1]], ['multiple' => true]);
	$colMsc->remove(['ITI' => ['$size' => 2]], ['multiple' => true]);
	$colMsc->remove(['ITI' => ['$size' => 3]], ['multiple' => true]);

	$colMsc->remove(['GRILLE' => ['$size' => 0]], ['multiple' => true]);

