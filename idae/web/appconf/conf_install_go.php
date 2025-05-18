<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();

	$schemes = $schemes_values = [];

	$schemes['appscheme_base']        = ['fields' => ['code', 'nom', 'icon']];
	$schemes_values['appscheme_base'] = ['sitebase_app', 'sitebase_base', 'sitebase_pref'];

	$schemes['appscheme_field']        = ['fields' => ['code', 'nom', 'icon'], 'has' => ['type', 'group']];
	$schemes_values['appscheme_field'] = ['adresse', 'atout', 'bgcolor', 'code', 'codePostal', 'color', 'commentaire', 'date', 'dateCreation', 'dateDebut', 'dateFin', 'description', 'descriptionHTML', 'duree', 'email', 'estActif', 'estTop', 'estVisible', 'fax', 'heure', 'heureCreation', 'html', 'icon', 'login', 'mailPassword', 'nom', 'ordre', 'password', 'petitNom', 'prenom', 'prix', 'quantite', 'rang', 'rewiteTag', 'telephone', 'total', 'totalHt', 'totalTtc', 'totalTva', 'url', 'valeur', 'ville'];

	$schemes['appscheme']      = ['fields' => ['code', 'nom', 'icon'], 'has' => ['type'], 'grilleFK' => ['appscheme_base']];
	$schemes['appscheme_type'] = ['fields' => ['code', 'nom', 'icon']];

	$schemes['appscheme_field_group']        = ['fields' => ['code', 'nom', 'icon', 'ordre']];
	$schemes_values['appscheme_field_group'] = ['classification', 'codification', 'date', 'heure', 'identification', 'localisation', 'image', 'prix', 'telephonie', 'texte', 'valeur', 'www'];

	$schemes['appscheme_field_type']        = ['fields' => ['code', 'nom', 'icon', 'ordre']];
	$schemes_values['appscheme_field_type'] = ['bool', 'codification', 'color', 'date', 'email', 'heure', 'icon', 'image', 'phone', 'pourcentage', 'prix', 'prix_precis', 'texte', 'textelibre', 'valeur'];

	$schemes['appscheme_has_field']       = ['fields' => ['code', 'nom', 'icon', 'ordre'], 'grilleFK' => ['appscheme', 'appscheme_field']];
	$schemes['appscheme_has_table_field'] = ['fields' => ['code', 'nom', 'icon', 'ordre'], 'grilleFK' => ['appscheme', 'appscheme_field']];

	$schemes['appscheme_icon'] = ['fields' => ['code', 'nom', 'icon']];

	$json        = file_get_contents(APPCONFDIR . 'conf_modele_export.json');
	$json_scheme = file_get_contents(APPCONFDIR . 'models/commercial/idae_scheme.json');

	$out_json        = json_decode($json);
	$out_json_scheme = json_decode($json_scheme, JSON_OBJECT_AS_ARRAY);

	//vardump(array_values($out_json));
	// last
	foreach ($out_json_scheme as $index_scheme => $scheme) {
		$codeAppscheme = $scheme['codeAppscheme'];
		$nomAppscheme  = $scheme['nomAppscheme'];
		$name_id       = 'id' . $codeAppscheme;
		$insert_scheme = $scheme;
		$base          = empty($scheme['codeAppscheme_base']) ? 'sitebase_app' : $scheme['codeAppscheme_base'];

		var_dump($codeAppscheme);
		unset($insert_scheme['fields'], $insert_scheme['values']);
		$insert_scheme = fonctionsProduction::cleanPostMongo($insert_scheme, true);

		$APP->plug_base($base)->createCollection($codeAppscheme);
		$APP->plug('sitebase_app', 'appscheme')->update(['idappscheme' => $insert_scheme['idappscheme']], ['$set' => $insert_scheme], ['upsert' => 1]);
		//$APP->init_scheme($base,$codeAppscheme);

		$idnext     = $APP->readNext($name_id);
		$set_idnext = (int)$insert_scheme['idappscheme'];
		if ($set_idnext > $idnext) {
			$new_index = ($set_idnext > $idnext) ? $set_idnext : $idnext;
			$APP->setNext($name_id, $new_index);
		}

	}

	foreach ($out_json_scheme as $index_scheme => $scheme) {
		$codeAppscheme_base = $scheme['codeAppscheme_base'];
		$codeAppscheme = $scheme['codeAppscheme'];
		$nomAppscheme  = $scheme['nomAppscheme'];
		$name_id       = 'id' . $codeAppscheme;

	 	//if ($codeAppscheme=='appscheme') continue;
	 	if (!$scheme['values']) continue;

		//var_dump($codeAppscheme);
		foreach ($scheme['values'] as $index_values => $values) {
			$values = fonctionsProduction::cleanPostMongo($values, true);
			$values = array_filter($values);
			unset($values['_id']);
			$APP->plug($codeAppscheme_base, $codeAppscheme)->update([$name_id=>$values[$name_id]], ['$set' => $values], ['upsert' => 1]);

			if($nomAppscheme=='appscheme_field'){
			}
		}
	}

	foreach ($out_json_scheme as $index_scheme => $scheme) {
		$codeAppscheme = $scheme['codeAppscheme'];
		$APP->consolidate_app_scheme($codeAppscheme);
		$APP_TMP = new App($codeAppscheme);
		$APP_TMP->consolidate_scheme();
	}


	$APP_AGENT_GR = new App('agent_groupe');
	$APP_AGENT    = new App('agent');

	$idagent_groupe = $APP_AGENT_GR->create_update(['codeAgent_groupe' => 'ADMIN'], ['nomAgent_groupe' => 'Administrateur']);
	$idagent        = $APP_AGENT->create_update(['agent_auto' => 1],
		['nomAgent'       => 'Mydde',
		 'idagent_groupe' => $idagent_groupe,
		 'prenomAgent'    => 'Mydde',
		 'estActifAgent'  => 1,
		 'loginAgent'     => 'Mydde',
			'droit_app'=>['ADMIN'=>1,'DEV'=>1],
		 'passwordAgent'  => 'malaterre']);
	$APP_AGENT->consolidate_scheme($idagent);

echo "terminé ";
	exit;
	echo sizeof($out_json);
	//
	foreach ($out_json as $scheme => $detail) {
		$Scheme = ucfirst($scheme);
		$ins    = (array)$detail->$scheme;
		$base   = empty($ins['codeAppscheme_base']) ? 'sitebase_app' : $ins['codeAppscheme_base'];

		$APP->plug_base($base)->createCollection($scheme);
		// vardump( $detail->$scheme );
		unset($ins['grilleFK'], $ins['grilleCount'], $ins['has'], $ins['fields'], $ins['values']);
		//
		$test = $APP->plug('sitebase_app', 'appscheme')->findOne(['codeAppscheme' => $scheme]);

		if (empty($test['idappscheme'])) {
			$ins['idappscheme'] = $APP->getNext('idappscheme');
		} else {
			$ins['idappscheme'] = (int)$test['idappscheme'];
		}
		$APP->plug('sitebase_app', 'appscheme')->update(['idappscheme' => $ins['idappscheme']], ['$set' => $ins], ['upsert' => 1]);

	}

	foreach ($out_json as $scheme => $detail) {
		$Scheme = ucfirst($scheme);
		$opt    = [];
		$ins    = (array)$detail->$scheme;
		$base   = empty($ins['codeAppscheme_base']) ? 'sitebase_app' : $ins['codeAppscheme_base'];
		if (!empty($ins['grilleFK'])) $opt['grilleFK'] = $ins['grilleFK'];
		if (!empty($ins['grilleCount'])) $opt['grilleCount'] = $ins['grilleCount'];
		if (!empty($ins['has'])) $opt['has'] = $ins['has'];
		if (!empty($ins['fields'])) $opt['fields'] = $ins['fields'];

		$APP->init_scheme($base, $scheme, $opt, true);
		// codeAppscheme_type
		if (!empty($ins['codeAppscheme_type'])) {

		}
	}

	foreach ($out_json as $scheme => $detail) {
		$APP_TMP = new App($scheme);
		$Scheme  = ucfirst($scheme);
		$opt     = [];
		$ins     = (array)$detail->$scheme;
		if (empty($ins['values'])) continue;
		foreach ($ins['values'] as $key_val => $field) {
			// vardump($ins['values']);
			$code = "code$Scheme";
			if (!empty($field->$code)) {
				vardump($field);
				// $APP_TMP->create_update([$code=>$field->$code],(array)$field);
			}

		}

	}

	foreach ($schemes_values as $scheme => $detail) {
		foreach ($detail as $key_detail => $value_field) {
			$out = [];

			$out['codeAppscheme_base']      = 'sitebase_app';
			$out['nomAppscheme_base']       = 'sitebase_app';
			$out['nom' . ucfirst($scheme)]  = $value_field;
			$out['code' . ucfirst($scheme)] = $value_field;

			$test = $APP->plug('sitebase_app', $scheme)->findOne(['code' . ucfirst($scheme) => $value_field]);

			if (empty($test['code' . ucfirst($scheme)])) {
				$out['id' . $scheme] = $APP->getNext('id' . $scheme);
			} else {
				$out['id' . $scheme] = (int)$test['id' . $scheme];
			}
			$APP->plug('sitebase_app', $scheme)->update(['id' . $scheme => $out['id' . $scheme]], ['$set' => $out], ['upsert' => 1]);

		}
	}
	// dans appscheme_base

	foreach ($schemes as $scheme => $detail) {
		// vardump($detail);
		$id      = $APP->init_scheme('sitebase_app', $scheme, $detail);
		$app_tmp = new App($scheme);
		$app_tmp->consolidate_scheme($id);
		buffer_flush();
		flush(); ?>
		<div class="padding borderb"><?= $scheme ?></div>
		<? flush();
		buffer_flush();
	}
	$APP_AGENT_GR = new App('agent_groupe');
	$APP_AGENT    = new App('agent');

	$idagent_groupe = $APP_AGENT_GR->create_update(['codeAgent_groupe' => 'ADMIN'], ['nomAgent_groupe' => 'Administrateur']);
	$idagent        = $APP_AGENT->create_update(['agent_auto' => 1],
		['nomAgent'       => 'Mydde',
		 'idagent_groupe' => $idagent_groupe,
		 'prenomAgent'    => 'Mydde',
		 'estActifAgent'  => 1,
		 'loginAgent'     => 'Mydde',
		 'passwordAgent'  => 'malaterre']);
	$APP_AGENT->consolidate_scheme($idagent);
?>
<style>
	.padding { padding : 0.5em; }
	.border4 { border : 1px solid #ccc; }
	.borderb { border-bottom : 1px solid #ccc; }
</style>