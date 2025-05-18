<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	array_walk_recursive($_POST, 'CleanStr', $_POST);

	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}
	// vardump($_POST);
	if (empty($_POST['table'])) {
		// unset($F_action);
	}
	switch ($F_action) {

		case "app_create":
			$table = $_POST['table'];
			$Table = ucfirst($table);
			$vars  = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$APP   = new App($table);
			//
			$APP_TABLE = $APP->app_table_one;
			$GRILLE_FK = $APP->get_grille_fk();
			$BASE_APP  = $APP_TABLE['base'];

			// insert
			$name_id              = 'id' . $table;
			$value_id             = (int)$APP->getNext('id' . $table);
			$_POST['table_value'] = $value_id;
			$vars[$name_id]       = $value_id;

			$vars['dateCreation' . ucfirst($table)]  = date('Y-m-d');
			$vars['heureCreation' . ucfirst($table)] = date('H:i:s');
			$vars['timeCreation' . ucfirst($table)]  = time();

			foreach ($GRILLE_FK as $field):
				$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
				$id_fk = $field['idtable_fk'];
				if (empty($arr[$id_fk])) continue;
				$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
				$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
				//
				$vars['nom' . ucfirst($field['table_fk'])] = $dsp_name;
			endforeach;

			//
			if (!empty($APP_TABLE['hasPrixScheme']) && !empty($APP_TABLE['hasQuantiteScheme'])):
				$vars['total' . $Table] = $vars['total' . $Table] * $vars['quantite' . $Table];
			endif;
			// INSERT
			$APP->insert($vars);
			// CONSOLIDATE
			$APP->consolidate_scheme($value_id);
			// LOG_CODE
			$APP->set_log($_SESSION['idagent'], $table, $vars['id' . $table], 'CREATE');

			// HAS LIGNE
			if (str_find($table, '_ligne')) {
				$uptable   = str_replace('_ligne', '', $table);
				$iduptable = 'id' . $uptable;
				if (!empty($vars[$iduptable])) {
					// distinct sur ligne -> totalUpper
					$arr_sum = $APP->distinct_all('total' . $Table, [$iduptable => (int)$vars[$iduptable]], 10000, 'normal');
					$total   = array_sum($arr_sum);

					$tmp_APP = new App($uptable);
					$tmp_APP->update([$iduptable => (int)$vars[$iduptable]], ['total' . ucfirst($uptable) => $total]);
				}
			}
			// DUPLIC
			if (!empty($_POST['table_value_duplique'])) {
				for ($i = 1; $i <= (int)$_POST['vars_duplique_occurence']; $i++) {
					// echo "$i <br>";
					if ($i > 1) {
						$APP = new App($table);
						unset($vars['_id']);
						$vars[$name_id] = (int)$APP->getNext($name_id);
						$value_id       = $APP->insert($vars);
					}
					if (!empty($_POST['vars_duplique'])) {
						foreach ($_POST['vars_duplique'] as $key_dupl => $value_dupl) {
							$APP_TMP = new App($key_dupl);
							$RS_TMP  = $APP_TMP->find([$name_id => (int)$_POST['table_value_duplique']]);
							while ($ARR_TMP = $RS_TMP->getNext()) {
								unset($ARR_TMP['_id']);
								$ARR_TMP[$name_id]                   = $value_id;
								$ARR_TMP['nom' . ucfirst($key_dupl)] = 'copie - ' . $ARR_TMP['nom' . ucfirst($key_dupl)];
								$APP_TMP->insert($ARR_TMP);
							}
						}
					}
				}
			}
			//
			$g_vars = array('table' => $table);
			// skelMdl::send_cmd('act_add_data' , $g_vars);
			//			 
			skelMdl::send_cmd('act_notify', array('msg'     => 'Création ok ',
			                                      'options' => array(
				                                      'mdl'  => 'app/app/app_fiche_mini',
				                                      'vars' => 'table=' . $table . '&table_value=' . $vars[$name_id])), session_id());

			break;

		case "app_update":
			$table       = $_POST['table'];
			$name_id     = 'id' . $table;
			$Table       = ucfirst($table);
			$table_value = (int)$_POST['table_value'];
			if (empty($table_value)) {
				break;
			}
			$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

			$APP = new App($table);
			//
			$APP_TABLE = $APP->app_table_one;
			$BASE_APP  = $APP_TABLE['base'];
			//
			$arr_one = $APP->query_one([$name_id => $table_value]);
			// get id :
			$name_id = 'id' . $table;
			//
			if (!empty($APP_TABLE['hasCodeScheme'])):
				if (!empty($vars['code' . $Table])):
					if (strpos($vars['code' . $Table], ';') === false) :
					else:
						$vars['code' . $Table] = explode(';', $vars['code' . $Table]);
					endif;
				endif;
			endif;
			// totaux
			if (!empty($vars['idproduit_tarif'])):
				$APP_DATE = new App('produit_tarif');
				$arr = $APP_DATE->query_one(['idproduit_tarif' =>(int) $vars['idproduit_tarif']]);
				$vars['dateDebutDevis']=$arr['dateDebutProduit_tarif'];
			endif;
			if (!empty($vars['idproduit_tarif_gamme'])):
				$APP_GAMME = new App('produit_tarif_gamme');
				$arr = $APP_GAMME->query_one(['idproduit_tarif_gamme' =>(int) $vars['idproduit_tarif_gamme']]);
				//vardump($arr);
				$vars['cabineDevis']=$arr['nomProduit_tarif_gamme'];
			endif;
			if (!empty($vars['idtransport_gamme'])):
				$APP_GAMME = new App('transport_gamme');
				$arr = $APP_GAMME->query_one(['idtransport_gamme' =>(int) $vars['idtransport_gamme']]);
				$vars['cabineDevis']=$arr['nomTransport_gamme'];
			endif;

			if (!empty($APP_TABLE['hasPrixScheme']) && !empty($APP_TABLE['hasQuantiteScheme'])):
				$vars['total' . $Table] = $vars['prix' . $Table] * $vars['quantite' . $Table];
			endif;
			if (!empty($vars['dateDebut' . $Table]) && !empty($APP_TABLE['hasDureeScheme'])) {
				$time_debut = strtotime($vars['dateDebut' . $Table]);
				$time_fin   = strtotime($arr_one['dateFin' . $Table]);
				$time_duree = (int)$arr_one['duree' . $Table] * 86400;
				// duree

				// date de fin ?
				if (empty($vars['duree' . $Table])) {
					$vars['dateFin' . $Table] = date('Y-m-d', $time_debut + $time_duree);
				}
			}
			// UPDATE
			$APP->update([$name_id => $table_value], $vars, true);
			// CONSOLIDATE
			$APP->consolidate_scheme($table_value);
			// HAS LIGNE
			if (str_find($table, '_ligne')) {
				$uptable   = str_replace('_ligne', '', $table);
				$iduptable = 'id' . $uptable;
				if (!empty($vars[$iduptable])) {
					// distinct sur ligne -> totalUpper
					$arr_sum = $APP->distinct_all('total' . $Table, [$iduptable => (int)$vars[$iduptable]], 10000, 'normal');
					//
					$total   = array_sum($arr_sum);
					$tmp_APP = new App($uptable);
					$tmp_APP->update([$iduptable => (int)$vars[$iduptable]], ['total' . ucfirst($uptable) => $total]);
				}
			}
			//
			if (empty($_POST['scope'])) $_POST['scope'] = 'id' . $table;
			$_POST['id' . $table] = $table_value;

			// LOG_CODE
			$APP->set_log($_SESSION['idagent'], $table, $table_value, 'UPDATE');

			break;

		default:

	}
if($table=='devis'){
	skelMdl::runModule('mdl/dyn/dyn_devis_html',['iddevis'=>$table_value]);

}
	include_once(DOCUMENTROOT . '/postAction.php');
?>
