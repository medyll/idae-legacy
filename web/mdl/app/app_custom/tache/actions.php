<?
	include_once($_SERVER['CONF_INC']);
	array_walk_recursive($_POST, 'CleanStr', $_POST);
	ini_set('display_errors', 55);
	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	}
	else {
		exit;
	}
	var_dump($_POST);
	$APP = new App('tache');
	
	switch ($F_action) {
		case "createTache":
			
			if (!empty($_POST['heureDebutTache'])) {
				$_POST['timeDebutTache'] = (int)strtotime($_POST['dateDebutTache'] . ' ' . $_POST['heureDebutTache']);
			}
			$arr                        = fonctionsProduction::cleanPostMongo($_POST, true);
			$arr['idtache']             = (int)$APP->getNext('idtache');
			$arr['idtacheMain']         = $arr['idtache'];
			$arr['timeRappelTache']     = (int)($arr['timeDebutTache'] - ($arr['timeRappelTache'] * 60));
			$arr['dateTimeRappelTache'] = date('d/m/y H:i:00', $arr['timeRappelTache']);
			// on insert
			$APP->insert($arr);
			//
			$delta = 0;
			$TASK  = $arr;
			unset($TASK['_id']);
			// plagePeriodiciteTache
			if ($TASK['plagePeriodiciteTache'] == 'ROLL'):
				unset($TASK['maxPlagePeriodiciteTache']);
				unset($TASK['dateFinPeriodiciteTache']);
				$max_iteration = 100;
				$max_time      = 31556926; // 3 ans
			endif;
			if ($TASK['plagePeriodiciteTache'] == 'MAX'):
				unset($TASK['dateFinPeriodiciteTache']);
				$max_iteration = (empty($TASK['maxPlagePeriodiciteTache'])) ? 10 : $TASK['maxPlagePeriodiciteTache'];
				$max_time      = 31556926; // 3 ans
			endif;
			if ($TASK['plagePeriodiciteTache'] == 'ONDATE'):
				unset($TASK['maxPlagePeriodiciteTache']);
				$max_iteration = 100;
				$max_time      = strtotime($TASK['dateFinPeriodiciteTache']);
			endif;
			// pasPeriodiciteTache
			$modulo       = empty($TASK['pasPeriodiciteTache']) ? 1 : (int)$TASK['pasPeriodiciteTache'];
			$modulo_count = 1;
			switch ($_POST['typePeriodiciteTache']) :
				case 'ONE':

					break;
				case 'DAY':
					// pour les 100 prochains jours
					for ($i = 1; $i <= $max_iteration; $i++) {
						$delta += 86400;
						$TASK['idtache']        = (int)$APP->getNext('idtache');
						$TASK['dateDebutTache'] = date('Y-m-d', strtotime($_POST['dateDebutTache'] . ' ' . $_POST['heureDebutTache']) + $delta);
						$TASK['timeDebutTache'] += $delta;
						//
						$modulo_count++;
						if (($modulo_count % $modulo) == 0) {
							$TASK = fonctionsProduction::cleanPostMongo($TASK);
							// on crée
							$APP->insert( $TASK);
						}
					}
					break;
				case 'WORK': // pour les 100 prochains jours ouvrés
					for ($i = 1; $i <= $max_iteration; $i++) {
						$delta += 86400;
						$theday = date('w', $TASK['timeDebutTache']);
						if ($theday != 0 && $theday != 1) {
							$TASK['idtache']        = (int)$APP->getNext('idtache');
							$TASK['dateDebutTache'] = date('Y-m-d', strtotime($_POST['dateDebutTache'] . ' ' . $_POST['heureDebutTache']) + $delta);
							$TASK['timeDebutTache'] += $delta;
							//
							$modulo_count++;
							if (($modulo_count % $modulo) == 0) {
								$TASK               = fonctionsProduction::cleanPostMongo($TASK);
								$TASK['objetTache'] = ($max_iteration . ' ' . ($modulo_count % $modulo));
								// on crée
								$APP->insert( $TASK);
							}
						}
					}
					break;
				case 'WEEK': // le meme jour de chaque semaine
					// pour les 100 prochains jours ouvrés
					for ($i = 1; $i <= $max_iteration; $i++) {
						$delta += 604800;
						$TASK['idtache']        = (int)$APP->getNext('idtache');
						$TASK['dateDebutTache'] = date('Y-m-d', strtotime($_POST['dateDebutTache'] . ' ' . $_POST['heureDebutTache']) + $delta);
						$TASK['timeDebutTache'] += $delta;
						//
						if (($modulo_count % $modulo) == 0) {
							$TASK = fonctionsProduction::cleanPostMongo($TASK);
							// on crée
							$APP->insert( $TASK);
						}
						$modulo_count++;
					}
					break;
				case 'MONTH':

					break;
			endswitch;
			break;
		case "updateTache":
			if (empty($_POST['idtache'])) break;
			$idtache = (int)$_POST['idtache'];
			if (!empty($_POST['timeDebutTacheSet'])) {

				$_POST['timeDebutTache']  = time() + ($_POST['timeDebutTacheSet'] * 60);
				$_POST['dateDebutTache']  = date('Y-m-d', $_POST['timeDebutTache']);
				$_POST['heureDebutTache'] = date('H:i:s', $_POST['timeDebutTache']);

				$_POST['timeRappelTache']     = strtotime($_POST['timeDebutTache']);
				$_POST['dateTimeRappelTache'] = date('d/m/y H:i:s', $_POST['timeRappelTache']);
			}
			$arr = fonctionsProduction::cleanPostMongo($_POST);
			unset($arr['idtache']);

			$APP->update(array('idtache' => (int)$idtache), array('$set' => $arr), array('upsert' => true));

			break;
		case "deleteTache":
			if (empty($_POST['idtache'])) break;
			$idtache = (int)$_POST['idtache'];
			$APP->remove(array('idtache' => (int)$idtache));
			break;
		case "sendMailTache":
			if (empty($_POST['idtache'])) break;
			$idtache = (int)$_POST['idtache'];
			$APP->update(array('idtache' => (int)$idtache), array('$set' => array('mailSent' => true)), array('upsert' => true));
			break;
	}

	include_once(DOCUMENTROOT . '/postAction.php');
?> 