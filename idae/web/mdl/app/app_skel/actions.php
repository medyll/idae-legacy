<?
	include_once($_SERVER['CONF_INC']);	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	array_walk_recursive($_POST, 'CleanStr', $_POST);

	$APP = new App();
	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	switch ($F_action) {
		case "appscheme_update":
			if (empty($_POST['idappscheme']))
				break;
			$idappscheme = (int)$_POST['idappscheme'];
			// appscheme direct
			$arr = fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$APP->plug('sitebase_app', 'appscheme')->update(array('idappscheme' => $idappscheme), array('$set' => $arr));

			break;
		case "updateForm":
			$APP_SCH   = new App('appscheme');
			$APP_HAS   = new App('appscheme_has_field');
			$APP_FIELD = new App('appscheme_field');
			ini_set('display_errors', 55);
			if (empty($_POST['idappscheme']))
				break;
			$idappscheme = (int)$_POST['idappscheme'];

			// appscheme direct
			$arr     = fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$ARR_SCH = $APP_SCH->findOne(['idappscheme' => $idappscheme]);
			$APP->plug('sitebase_app', 'appscheme')->update(array('idappscheme' => $idappscheme), array('$set' => $arr));
			//   print_r($_POST['vars_has_field']);

			foreach ($_POST['vars_has_field'] as $idappscheme_field => $val) {
				$idappscheme_field = (int)$idappscheme_field;
				$ARR_FIELD         = $APP_FIELD->findOne(['idappscheme_field' => $idappscheme_field]);

				$HAS = $APP->plug('sitebase_app', 'appscheme_has_field')->findOne(array('idappscheme' => $idappscheme, 'idappscheme_field' => $idappscheme_field));
				if (empty($val)):
					$APP->plug('sitebase_app', 'appscheme_has_field')->remove(array('idappscheme' => $idappscheme, 'idappscheme_field' => $idappscheme_field));
				else:
					$out                      = [];
					$out['field_raw']         = $ARR_FIELD['codeAppscheme_field'];
					$out['idappscheme']       = $idappscheme;
					$out['idappscheme_field'] = $idappscheme_field;
					$out['collection']        = $ARR_SCH['codeAppscheme'];;
					//	$out['nomAppscheme']        = $ARR_SCH['collection'];
					$out['nomAppscheme_has_field']        = $ARR_FIELD['codeAppscheme_field'].ucfirst($ARR_SCH['codeAppscheme']);
					$out['codeAppscheme_has_field']        = $ARR_FIELD['codeAppscheme_field'].ucfirst($ARR_SCH['codeAppscheme']);
					$out['required']          = (int)$_POST['vars_has_required_field'][$idappscheme_field];
					$APP_HAS->create_update(array('idappscheme' => $idappscheme, 'idappscheme_field' => $idappscheme_field), $out);

					// SORT + SORT ORDER
					if(!empty($_POST['vars_sort']) && $idappscheme_field==$_POST['vars_sort'] ){
						$APP_SCH->update(['idappscheme'=>$idappscheme],['sortFieldId'=>$idappscheme_field,'sortFieldName'=>$ARR_FIELD['codeAppscheme_field'].ucfirst($ARR_SCH['codeAppscheme'])]);
						if(!empty($_POST['vars_sort_order'][$idappscheme_field])){
							$APP_SCH->update(['idappscheme'=>$idappscheme],['sortFieldOrder'=>$_POST['vars_sort_order'][$idappscheme_field]]);
						}
					}
				endif;
			}


			break;

		case "updateForm_modele_perso":
			$APP_SCH   = new App('appscheme');
			$APP_HAS   = new App('appscheme_has_field');
			$APP_SCH_HAS_TABLE   = new App('appscheme_has_table_field');
			$APP_FIELD = new App('appscheme_field');
			ini_set('display_errors', 55);
			if (empty($_POST['idappscheme']))
				break;
			$idappscheme = (int)$_POST['idappscheme'];
			$ARR_SCH    = $APP_SCH->findOne(['idappscheme' => $idappscheme]);

			$arr     = fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$grilleCount = [];
			$APP_SCH->update(['idappscheme' => $idappscheme],['grilleCount' => NULL]);
			foreach ($_POST['vars_has_table_count'] as $for_ins => $val) {
				$grilleCount[$for_ins]=$val;
			}
			$APP_SCH->update(['idappscheme' => $idappscheme],['grilleCount' => $grilleCount]);
			foreach ($_POST['vars_has_table_field'] as $for_ins => $val) {

				$for_ins = explode('_',$for_ins);
				$idappscheme = (int)$for_ins[0];
				$idappscheme_link = (int)$for_ins[1];
				$idappscheme_field_link = (int)$for_ins[2];

				$ins               = ['idappscheme' =>$idappscheme, 'idappscheme_link' => $idappscheme_link, 'idappscheme_field' => $idappscheme_field_link];

 				$ARR_FIELD         = $APP_HAS->findOne(['idappscheme' =>$idappscheme_link,'idappscheme_field' => $idappscheme_field_link]);


				$arr_test_has = $APP_SCH_HAS_TABLE->findOne($ins);

				if (empty($val)):
					$APP_SCH_HAS_TABLE->remove($ins);
				else:
					$out                      = [];
					$out['field_raw']         = $ARR_FIELD['field_raw'];
					$out['field_name']         = $ARR_FIELD['field_raw'].ucfirst($ARR_FIELD['collection']);
					$out['nomAppscheme_has_table_field']         = $ARR_FIELD['codeAppscheme_field'].ucfirst($ARR_FIELD['codeAppscheme']);
					$out['codeAppscheme_has_table_field']         = $ARR_FIELD['codeAppscheme_field'] ;
					$out['idappscheme']       = $idappscheme;
					$out['idappscheme_link']       = $idappscheme_link;
					$out['idappscheme_field'] = $idappscheme_field_link;
					$out['collection']        = $ARR_FIELD['collection'];
					$out['nomAppscheme']        = $ARR_FIELD['collection'];

					$APP_SCH_HAS_TABLE->create_update($ins, $out);

				endif;
			}

			break;
		case "createForm":
			$APPSC = new App('appscheme');
		//	$APPSC->insert($_POST['vars']);

			break;
		case "add_field":
			$APP_FIELD = new App('appscheme_field');
			$arr = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arr['appscheme_field'] = $APP_FIELD->getNext('appscheme_field');
			$APP_FIELD->insert($arr);
			break;
		case "addInput":
			$_id            = MongoCompat::toObjectId($_POST['_id']);
			$arr            = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arrskel        = $APP->plug('sitebase_skelbuilder', 'skel_builder')->findOne(array('_id' => $_id));
			$arr['idinput'] = (int)(sizeof($arrskel['grilleInput'])) + 1;
			$APP->plug('sitebase_skelbuilder', 'skel_builder')->update(array('_id' => $_id), array('$push' => array('grilleInput' => $arr)));
			$_POST['reloadModule']['app/app_skel/skelbuilder_input'] = $_POST['_id'];
			break;
		case "addInput_":
			$_id            = MongoCompat::toObjectId($_POST['_id']);
			$arr            = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arrskel        = $APP->plug('sitebase_app', 'appscheme')->findOne(array('_id' => $_id));
			$arr['idinput'] = (int)(sizeof($arrskel['grilleInput'])) + 1;
			$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id), array('$push' => array('grilleInput' => $arr)));
			$_POST['reloadModule']['app/app_skel/skelbuilder_input'] = $_POST['_id'];
			break;
		case "addFK":
			$_id               = MongoCompat::toObjectId($_POST['_id']);
			$arr               = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arr['uid']        = uniqid();
			$arrskel           = $APP->plug('sitebase_app', 'appscheme')->findOne(array('_id' => $_id));
			$arr['ordreTable'] = (int)(sizeof($arrskel['grilleFK'])) + 1;
			$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id), array('$push' => array('grilleFK' => $arr)));
			$_POST['reloadModule']['app/app_scheme/app_scheme_grille'] = '*';
			break;
		case "updFK":
			// mode generique
			// on reçoit :
			// le nom de la table :
			//exit;
			$vars = fonctionsProduction::cleanPostMongo($_POST['vars']);

			$_id = MongoCompat::toObjectId($_POST['_id']);
			$uid = $_POST['uid'];
			if (empty($uid) || empty($_id))
				break;
			// le nom du champ maj
			$upd_name = 'table';
			// faire boucle dans vars, probleme resolu
			//$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grilleFK.uid' => $uid), array('$push' => array('grilleFK.$' => $vars)));
			// pour fragment => $APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grilleFK.uid' => $table),
			//	array('$set' => array('grilleFK.$.table' =>$new_table)));
			foreach ($vars as $key => $value):
				$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grilleFK.uid' => $uid),
					array('$set' => array('grilleFK.$.' . $key => $value)));
			endforeach;
			//$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grilleFK.uid' => $uid),
			//	array('$set' => array('grilleFK.$' => $vars)));
			break;
		case "updFK_fragment":
			// mode generique
			// on reçoit :
			// le nom de la table :
			//exit;
			$vars        = fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$_id         = MongoCompat::toObjectId($_POST['_id']);
			$uid         = $_POST['uid'];
			$input_name  = $_POST['input_name'];
			$input_value = $_POST['input_value'];
			// pour fragment =>
			$APP->plug('sitebase_app', 'appscheme')->update(array('grilleFK.uid' => $uid), array('$set' => array('grilleFK.$.' . $input_name => $input_value)),
				array('upsert' => true));

			break;

		case "addGrille":
			$_id               = MongoCompat::toObjectId($_POST['_id']);
			$nomGrille         = $_POST['nomGrille'];
			$arr               = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arr['uid']        = uniqid();
			$arrskel           = $APP->plug('sitebase_app', 'appscheme')->findOne(array('_id' => $_id));
			$arr['ordreTable'] = (int)(sizeof($arrskel['grille'])) + 1;
			$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id), array('$push' => array('grille' => $arr)));
			$_POST['reloadModule']['app/app_skel/skelbuilder_input'] = $_POST['_id'];
			break;
		case "updGrille":
			// mode generique
			// on reçoit :
			// le nom de la table :
			//exit;
			$vars = fonctionsProduction::cleanPostMongo($_POST['vars']);

			$_id = MongoCompat::toObjectId($_POST['_id']);
			$uid = $_POST['uid'];
			if (empty($uid) || empty($_id))
				break;
			// le nom du champ maj
			$upd_name = 'table';

			foreach ($vars as $key => $value):
				$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grille.uid' => $uid),
					array('$set' => array('grille.$.' . $key => $value)));
			endforeach;
			//$APP->plug('sitebase_app', 'appscheme')->update(array('_id' => $_id, 'grilleFK.uid' => $uid),
			//	array('$set' => array('grilleFK.$' => $vars)));
			break;
		case "deleteForm":
			if (empty($_POST['_id']))
				break;
			$_id = MongoCompat::toObjectId($_POST['_id']);
			//
			$base = skelMongo::connect('skel_builder', 'sitebase_skelbuilder');
			$base->remove(array('_id' => $_id));
			break;
	}
//
	include_once(DOCUMENTROOT . '/postAction.php');
?>