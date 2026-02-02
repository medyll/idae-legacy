<?php
	require_once __DIR__ . '/../../appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/04/2018
	 * Time: 20:11
	 */
	class Action extends App {

		public function __construct($table = null) {
			return parent::__construct($table);
		}

		public function app_create($ARGS) {
			if ($ARGS['table'] == 'agent_note') {
				if (is_array($ARGS['vars']['idagent'])) {
					$daarr = $ARGS['vars']['idagent'];
					unset($ARGS['vars']['idagent']);
					foreach ($daarr as $key => $value) {
						$ARGS['vars']['idagent'] = $value;
						skelMdl::runModule('app/actions', ['F_action' => 'app_create'] + $ARGS);
					}
					exit;
				}
			}
			$table = $ARGS['table'];
			$Table = ucfirst($table);
			$vars  = empty($ARGS['vars']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['vars'], 1);
			$APP   = new App($table);
			//
			$APP_TABLE = $APP->app_table_one;
			$GRILLE_FK = $APP->get_grille_fk();
			$BASE_APP  = $APP_TABLE['base'];

			// insert
			$name_id             = 'id' . $table;
			$value_id            = (int)$APP->getNext('id' . $table);
			$ARGS['table_value'] = $value_id;
			$vars[$name_id]      = $value_id;

			$vars['dateCreation' . ucfirst($table)]  = date('Y-m-d');
			$vars['heureCreation' . ucfirst($table)] = date('H:i:s');
			$vars['timeCreation' . ucfirst($table)]  = time();

			foreach ($GRILLE_FK as $field):
				$id_fk = $field['idtable_fk'];
				if (empty($arr[$id_fk])) continue;
				$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
				$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
				//
				$vars['nom' . ucfirst($field['table_fk'])] = $dsp_name;
			endforeach;

			if (!empty($vars['password' . $Table]) && $table != 'agent'):
				$vars['password' . $Table] = md5($vars['password' . $Table]);
				$vars['private_key']       = md5($vars['password' . $Table] . $vars['dateCreation' . ucfirst($table)]);
			endif;
			//
			if (!empty($APP_TABLE['hasPrixScheme']) && !empty($APP_TABLE['hasQuantiteScheme'])):
				$vars['total' . $Table] = $vars['total' . $Table] * $vars['quantite' . $Table];
			endif;
			// INSERT
			$APP->insert($vars);
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
			if (!empty($ARGS['table_value_duplique'])) {
				for ($i = 1; $i <= (int)$ARGS['vars_duplique_occurence']; $i++) {
					// echo "$i <br>";
					if ($i > 1) {
						$APP = new App($table);
						unset($vars['_id']);
						$vars[$name_id] = (int)$APP->getNext($name_id);
						$value_id       = $APP->insert($vars);
					}
					if (!empty($ARGS['vars_duplique'])) {
						foreach ($ARGS['vars_duplique'] as $key_dupl => $value_dupl) {
							$APP_TMP = new App($key_dupl);
							$RS_TMP  = $APP_TMP->find([$name_id => (int)$ARGS['table_value_duplique']]);
							while ($ARR_TMP = $RS_TMP->getNext()) {
								unset($ARR_TMP['_id'], $ARR_TMP['id' . $key_dupl]);
								$ARR_TMP[$name_id]                   = (int)$value_id;
								$ARR_TMP['nom' . ucfirst($key_dupl)] = 'copie - ' . $ARR_TMP['nom' . ucfirst($key_dupl)];
								$APP_TMP->insert($ARR_TMP);
							}
						}
					}
				}
			}

			if ($table == 'opportunite' && !empty($ARGS['vars']['descriptionOpportunite'])) {
				$pattern = "/(\\d*)(\\s*)(\\w*)(\\s*)/";
				preg_match_all($pattern, $ARGS['vars']['descriptionOpportunite'], $woul, PREG_SET_ORDER);
				foreach ($woul as $key => $arr_match) {
					$APP_PROD = new App('produit');
					$APP_LGN  = new App('opportunite_ligne');
					if (!empty($arr_match[1]) && !empty($arr_match[3])) {
						$qte      = $arr_match[1];
						$prod     = $arr_match[3];
						$reg      = MongoCompat::toRegex('^' . preg_quote($prod, '/'), 'i');
						$ARR_PROD = $APP_PROD->findOne(['codeProduit' => $reg]);

						if (!empty($ARR_PROD['idproduit'])) {
							$APP_LGN->insert(['quantiteOpportunite_ligne' => $qte, 'idproduit' => (int)$ARR_PROD['idproduit'], 'idopportunite' => (int)$vars[$name_id]]);
						}
					}
				}
			}

			skelMdl::send_cmd('act_notify', ['msg'     => 'Création ok ',
			                                 'options' => [
				                                 'mdl'  => 'app/app/app_fiche_mini',
				                                 'vars' => 'table=' . $table . '&table_value=' . $vars[$name_id]]], session_id());

			return $ARGS;

		}

		public function app_multi_create($ARGS) {
			if (empty($ARGS['table']) || empty($ARGS['occurence'])) return $ARGS;

			$table = $ARGS['table'];
			$Table = ucfirst($table);
			$vars  = empty($ARGS['vars']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['vars'], 1);
			$APP   = new App($table);
			unset($ARGS['F_action']);
			if (empty($ARGS['vars']['nom' . $Table])) $make_oc = true;
			if (empty($ARGS['vars']['ordre' . $Table])) $make_or = true;
			if ($table == 'newsletter_item') {
				$APP_BLOCK                           = new App('newsletter_block');
				$ARGS['vars']['idnewsletter_block'] = $APP_BLOCK->insert($vars);
			}
			$ordre = $APP->find($vars)->count();
			for ($i = 1; $i <= (int)$ARGS['occurence']; $i++) {
				if (!empty($make_oc)) $ARGS['vars']['nom' . $Table] = $Table . ' - ' . ($ordre + $i);
				if (!empty($make_or)) $ARGS['vars']['ordre' . $Table] = $ordre + $i;
				skelMdl::runModule('app/actions', ['F_action' => 'app_create'] + $ARGS);
				usleep(2500);
			}
			return $ARGS;
		}
		public function app_update($ARGS) {
			$table       = $ARGS['table'];
			$name_id     = 'id' . $table;
			$Table       = ucfirst($table);
			$table_value = (int)$ARGS['table_value'];
			if (empty($table_value)) {
				return $ARGS;
			}
			$vars = empty($ARGS['vars']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['vars'], 1);

			$APP = new App($table);

			$APP_TABLE = $APP->app_table_one;

			$arr_one = $APP->findOne([$name_id => $table_value]);
			// get id :
			$name_id = 'id' . $table;
			//
			if (isset($vars['password' . $Table]) && empty(trim($vars['password' . $Table]))) {
				unset($vars['password' . $Table]);
			}
			if ($table == 'opportunite'):
				$vars['nom' . $Table] = $vars['nomProspect'] . $vars['nomClient'] . ' ' . date('m-Y', strtotime($vars['dateFin' . $Table])) . ' ' . maskNbre($vars['montantOpportunite'], 0);
			endif;
			//
			if (empty($vars['nom' . $Table]) && isset($ARGS['vars']['nom' . $Table])):
				if (!empty($APP_TABLE['hasTypeScheme'])):
					//$vars['nom' . $Table] = $vars['nom' . $Table . '_type'];
				endif;
			endif;
			if (!empty($vars['gps' . $Table]) && is_string($vars['gps' . $Table])):
				$gps_content                               = [json_decode($vars['gps' . $Table], JSON_OBJECT_AS_ARRAY)];
				$vars['gps_index' . $Table]                = [];
				$vars['gps_index' . $Table]['type']        = 'Polygon';
				$vars['gps_index' . $Table]['coordinates'] = $gps_content;
				$APP->plug($APP->codeAppscheme_base, $APP->codeAppscheme)->update([$name_id => (int)$arr_one["$name_id"]], ['$unset' => ["gps_index$Table" => 1]]);
			endif;
			if (!empty($APP_TABLE['hasCodeScheme'])):
				if (!empty($vars['code' . $Table])):
					if (strpos($vars['code' . $Table], ';') === false) :
					else:
						$vars['code' . $Table] = explode(';', $vars['code' . $Table]);
					endif;
				endif;
			endif;
			// totaux
			if (!empty($APP_TABLE['hasPrixScheme']) && !empty($APP_TABLE['hasQuantiteScheme'])):
				$vars['total' . $Table] = $vars['prix' . $Table] * $vars['quantite' . $Table];
			endif;
			if (!empty($vars['dateDebut' . $Table]) && !empty($APP_TABLE['hasDureeScheme'])) {
				$time_debut = strtotime($vars['dateDebut' . $Table]);
				$time_fin   = strtotime($arr_one['dateFin' . $Table]);
				$time_duree = (int)$arr_one['duree' . $Table] * 86400;

				// date de fin ?
				if (empty($vars['duree' . $Table])) {
					$vars['dateFin' . $Table] = date('Y-m-d', $time_debut + $time_duree);
				}
			}
			if (!empty($vars['password' . $Table]) && $table != 'agent' && $arr_one['password' . $Table] != $vars['password' . $Table]):
				$vars['password' . $Table] = md5($vars['password' . $Table]);
				$vars['private_key']       = md5($vars['password' . $Table] . $arr_one['dateCreation' . ucfirst($table)]);
			endif;
			// UPDATE
			$APP->update([$name_id => $table_value], $vars, true);

			// HAS LIGNE
			if (str_find($table, '_ligne')) {
				$uptable   = str_replace('_ligne', '', $table);
				$iduptable = 'id' . $uptable;
				if (!empty($vars[$iduptable])) {
					$arr_sum = $APP->distinct_all('total' . $Table, [$iduptable => (int)$vars[$iduptable]], 10000, 'normal');

					$total   = array_sum($arr_sum);
					$tmp_APP = new App($uptable);
					$tmp_APP->update([$iduptable => (int)$vars[$iduptable]], ['total' . ucfirst($uptable) => $total]);
				}
			}

			if (empty($ARGS['scope'])) $ARGS['scope'] = 'id' . $table;
			$ARGS['id' . $table] = $table_value;

			// LOG_CODE
			$APP->set_log($_SESSION['idagent'], $table, $table_value, 'UPDATE');

			return $ARGS;
		}

		public function app_multi_update($ARGS) {
			// on reçoit un tableau / switch fact_action
			$table = $ARGS['table'];
			if (empty($table)) {
				return $ARGS;
			}
			$vars   = empty($ARGS['vars']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['vars'], 1);
			$verify = empty($ARGS['verify']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['verify'], 1);
			$arr_id = empty($ARGS['arr_id']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['arr_id'], 1);

			$APP = new App($table);
			// get id :
			$name_id = 'id' . $table;
			// update
			$mg_vars['dateModification' . ucfirst($table)]  = date('Y-m-d');
			$mg_vars['heureModification' . ucfirst($table)] = date('H:i:s');
			$mg_vars['timeModification' . ucfirst($table)]  = time();
			foreach ($verify as $verify_table => $key):
				if (!empty($vars['id' . $verify_table])):
					$mg_vars['id' . $verify_table]           = (int)$vars['id' . $verify_table];
					$APP_TMP                                 = new App($verify_table);
					$arr_name                                = $APP_TMP->findOne(['id' . $verify_table => (int)$vars['id' . $verify_table]]);
					$mg_vars['nom' . ucfirst($verify_table)] = $arr_name['nom' . ucfirst($verify_table)];
				endif;

				if (isset($vars[$verify_table])):
					$mg_vars[$verify_table] = $vars[$verify_table];
				endif;
			endforeach;
			if (sizeof($mg_vars) != 0):
				$i = 0;
				foreach ($arr_id as $value_id):
					$i++;
					// UPDATE
					$APP->update([$name_id => (int)$value_id], $mg_vars);
					// CONSOLIDATE
					// $APP->consolidate_scheme((int)$value_id);
					//
					$ARGS['scope']       = 'id' . $table;
					$ARGS['id' . $table] = $value_id;

				endforeach;
			endif;
		}

		public function app_delete($ARGS) {
			$table       = $ARGS['table'];
			$table_value = (int)$ARGS['table_value'];
			$id          = 'id' . $table;
			$APP         = new App($table);
			//
			$APP_TABLE = $APP->app_table_one;
			$BASE_APP  = $APP_TABLE['codeAppscheme_base'];
			$ARR       = $APP->query_one([$id => $table_value]);
			if (empty($table_value)) {
				return false;
			}
			// on met en trash : table + table value + data
			unset($ARR['_id']);
			$arr_trashed_data = ['table' => $table, 'table_value' => $table_value, 'table_data' => $ARR];
			$APP->plug('sitebase_app', 'trash')->insert($arr_trashed_data);
			//
			$APP->plug($BASE_APP, $table)->remove([$id => $table_value]);
			if ($table == 'document') {
				$APP->plug('sitebase_ged', 'ged_bin')->remove([['metadata.' . $id] => $table_value]);
			}
			//
			$vars = ['table' => $table, 'table_value' => $table_value];
			skelMdl::send_cmd('act_close_mdl', $vars);
			// LOG_CODE
			$APP->set_log($_SESSION['idagent'], $table, $table_value, 'DELETE');

			return $ARGS;
		}

		public function app_multi_delete($ARGS) {
			$table = $ARGS['table'];
			if (empty($table)) {
				return $ARGS;
			}

			$arr_id = empty($ARGS['arr_id']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['arr_id'], 1);

			$APP = new App($table);
			//
			$APP_TABLE = $APP->app_table_one;
			$BASE_APP  = $APP_TABLE['codeAppscheme_base'];
			// get id :
			$name_id = 'id' . $table;
			// update
			$mg_vars['dateSuppression' . ucfirst($table)]  = date('Y-m-d');
			$mg_vars['heureSuppression' . ucfirst($table)] = date('H:i:s');
			$mg_vars['timeSuppression' . ucfirst($table)]  = time();
			//
			$i = 0;
			foreach ($arr_id as $table_value):
				$i++;
				// QUERY
				$ARR = $APP->query_one([$name_id => (int)$table_value]);
				// on met en trash : table + table value + data
				unset($ARR['_id']);
				$arr_trashed_data = ['table' => $table, 'table_value' => $table_value, 'table_data' => $ARR];
				$APP->plug('sitebase_app', 'trash')->insert($arr_trashed_data);
				// LOG_CODE
				$APP->set_log($_SESSION['idagent'], $table, $table_value, 'DELETE');
				// delete real
				$APP->plug($BASE_APP, $table)->remove([$name_id => (int)$table_value]);
				if ($table == 'document') {
					$APP->plug_base('sitebase_ged')->getGridFS('ged_bin')->remove([['metadata.iddocument'] => (int)$table_value]);
				}

				$vars = ['table' => $table, 'table_value' => $table_value];
				skelMdl::send_cmd('act_close_mdl', $vars);

				$vars = ['progress_name' => 'progress_multi_delete', 'progress_value' => $i, 'progress_max' => sizeof($arr_id), 'progress_message' => $i];
				skelMdl::send_cmd('act_progress', $vars, session_id());
			endforeach;

			return $ARGS;
		}

		public function app_save_liste_multi($ARGS) {
			$table = $ARGS['table'];
			$Table = ucfirst($table);;
			if (empty($table) || empty($ARGS['vars']['idagent_liste'])) {
				return $ARGS;
			}
			$arr_id = empty($ARGS['arr_id']) ? [] : fonctionsProduction::cleanPostMongo($ARGS['arr_id'], 1);

			$APP     = new App($table);
			$APP_LGN = new App('agent_liste_ligne');
			//
			$idagent_liste = (int)$ARGS['vars']['idagent_liste'];
			// get id :
			$name_id = 'id' . $table;
			$i       = 0;
			foreach ($arr_id as $value_id):
				$i++;
				// UPDATE
				$ARR_TMP = $APP->findOne([$name_id => (int)$value_id]);
				// CONSOLIDATE
				$APP_LGN->create_update(['idagent_liste' => (int)$idagent_liste, 'valeurAgent_liste_ligne' => (int)$value_id, 'codeAgent_liste_ligne' => $table], ['nomAgent_liste_ligne' => $ARR_TMP['nom' . $Table]]);
				//
				$ARGS['scope']       = 'id' . $table;
				$ARGS['id' . $table] = $value_id;
			endforeach;

			return $ARGS;
		}

		public function app_sort($ARGS) {
			$table   = $ARGS['table'];
			$name_id = 'id' . $table;
			$Table   = ucfirst($table);
			$APPSORT = new App($table);

			foreach ($ARGS['ordre' . $Table] as $ordre => $value_id):
				$APPSORT->update([$name_id => (int)$value_id], ['ordre' . $Table => (int)$ordre]);
			endforeach;

			return $ARGS;
		}

		public function init_settings($ARGS) {
			$APP = new App();
			$APP->init_scheme('sitebase_pref', 'agent_pref');
			$APP = new App('agent_pref');

			foreach (array_keys($ARGS['vars']['arr_settings']) as $key => $val) {
				skelMdl::send_cmd('act_notify', ['msg' => json_encode($val)]);
				$APP->plug('sitebase_pref', 'agent_pref')->remove(['idagent' => (int)$ARGS['vars']['idagent'], 'codeAgent_pref' => MongoCompat::toRegex(preg_quote($val, '/'), 'i')]);
				switch ($val) {
					case "app_search":
						foreach (droit_table_multi($ARGS['vars']['idagent'], 'R') as $ks => $table) {
							if (strpos($table, '_ligne') !== false) continue;
							if (strpos($table, '_type') !== false) continue;
							if (strpos($table, '_statut') !== false) continue;
							$APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
						}
						break;
					case "app_menu_start":
						foreach (droit_table_multi($ARGS['vars']['idagent'], 'R') as $ks => $table) {
							if (strpos($table, '_ligne') !== false) continue;
							if (strpos($table, '_type') !== false) continue;
							if (strpos($table, '_statut') !== false) continue;
							$APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
						}
						break;
					case "app_menu_create":
						foreach (droit_table_multi($ARGS['vars']['idagent'], 'C') as $ks => $table) {
							if (strpos($table, '_ligne') !== false) continue;
							if (strpos($table, '_type') !== false) continue;
							if (strpos($table, '_statut') !== false) continue;
							$APP->set_settings($_SESSION['idagent'], ["$val" . "_$table" => true]);
						}
						break;
				}
			}
			return $ARGS;
		}

		public function set_settings($ARGS) {
			$APP   = new App();
			$key   = $ARGS['key'];
			$value = $ARGS['value'];
			$APP->set_settings($_SESSION['idagent'], [$key => $value]);

			return $ARGS;
		}

		public function del_settings($ARGS) {
			$APP   = new App();
			$key   = $ARGS['key'];
			$value = $ARGS['value'];
			$test  = $APP->del_settings($_SESSION['idagent'], [$key => $value]);

			return $ARGS;
		}
	}