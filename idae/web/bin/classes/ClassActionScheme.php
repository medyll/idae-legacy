<?php
	require_once __DIR__ . '/../../appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/04/2018
	 * Time: 20:11
	 */
	class ActionScheme extends App {

		public function __construct($table = null) {
			return parent::__construct($table);
		}

		public function updateForm($ARGS) {
			$APP_SCH   = new App('appscheme');
			$APP_HAS   = new App('appscheme_has_field');
			$APP_FIELD = new App('appscheme_field');

			if (empty($_POST['idappscheme']))return $ARGS;

			$idappscheme = (int)$_POST['idappscheme'];

			$arr     = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
			$ARR_SCH = $APP_SCH->findOne(['idappscheme' => $idappscheme]);
			if (sizeof($arr) != 0) {
				$this->plug('sitebase_app', 'appscheme')->update(['idappscheme' => $idappscheme], ['$set' => $arr]);
			}

			foreach ($_POST['vars_has_field'] as $idappscheme_field => $val) {
				$idappscheme_field = (int)$idappscheme_field;
				$ARR_FIELD         = $APP_FIELD->findOne(['idappscheme_field' => $idappscheme_field]);

				$HAS = $this->plug('sitebase_app', 'appscheme_has_field')->findOne(['idappscheme' => $idappscheme, 'idappscheme_field' => $idappscheme_field]);

				if (empty($val)):
					$this->plug('sitebase_app', 'appscheme_has_field')->remove(['idappscheme' => $idappscheme, 'idappscheme_field' => $idappscheme_field]);
				else:
					$out                            = [];
					$out['field_raw']               = $ARR_FIELD['codeAppscheme_field'];
					$out['idappscheme']             = $idappscheme;
					$out['idappscheme_field']       = $idappscheme_field;
					$out['collection']              = $ARR_SCH['codeAppscheme'];
					$out['codeAppscheme']           = $ARR_SCH['codeAppscheme'];
					$out['nomAppscheme']            = $ARR_SCH['nomAppscheme'];
					$out['nomAppscheme_field']      = $ARR_FIELD['nomAppscheme_field'];
					$out['nomAppscheme_has_field']  = $ARR_FIELD['nomAppscheme_field'] . ' ' . $ARR_SCH['nomAppscheme'];
					$out['codeAppscheme_has_field'] = $ARR_FIELD['codeAppscheme_field'] . ucfirst($ARR_SCH['codeAppscheme']);
					$out['required']                = (int)$_POST['vars_has_required_field'][$idappscheme_field];
					$out['in_mini_fiche']           = (int)$_POST['vars_in_mini_fiche'][$idappscheme_field];

					if (empty($HAS['idappscheme_has_field'])) {
						$APP_HAS->insert($out);
					} else {
						$out['idappscheme_has_field'] = $HAS['idappscheme_has_field'];
						$APP_HAS->update_native(['_id' => $HAS['_id']], $out);
						$APP_HAS->consolidate_scheme($out['idappscheme_has_field']);
					}

					// SORT + SORT ORDER
					if (!empty($_POST['vars_sort']) && $idappscheme_field == $_POST['vars_sort']) {
						$APP_SCH->update(['idappscheme' => $idappscheme], ['sortFieldId' => $idappscheme_field, 'sortFieldName' => $ARR_FIELD['codeAppscheme_field'] . ucfirst($ARR_SCH['codeAppscheme'])]);
						if (!empty($_POST['vars_sort_order'][$idappscheme_field])) {
							$APP_SCH->update(['idappscheme' => $idappscheme], ['sortFieldOrder' => $_POST['vars_sort_order'][$idappscheme_field]]);
						}
					}
					// SECOND SORT + SORT ORDER
					if (!empty($_POST['vars_sort_second']) && $idappscheme_field == $_POST['vars_sort_second']) {
						$APP_SCH->update(['idappscheme' => $idappscheme], ['sortFieldSecondId' => $idappscheme_field, 'sortFieldSecondName' => $ARR_FIELD['codeAppscheme_field'] . ucfirst($ARR_SCH['codeAppscheme'])]);
						if (!empty($_POST['vars_sort_second_order'][$idappscheme_field])) {
							$APP_SCH->update(['idappscheme' => $idappscheme], ['sortFieldSecondOrder' => $_POST['vars_sort_second_order'][$idappscheme_field]]);
						}
					}
				endif;
			}

			return $ARGS;
		}

		public function update_appscheme_has_table_field($ARGS) {
			$APP_SCH           = new App('appscheme');
			$APP_HAS           = new App('appscheme_has_field');
			$APP_SCH_HAS_TABLE = new App('appscheme_has_table_field');


			if (empty($_POST['idappscheme'])) return $ARGS;
			$idappscheme = (int)$_POST['idappscheme'];

			$grilleCount  = [];
			$APP_SCH->update(['idappscheme' => $idappscheme], ['grilleCount' => NULL]);
			foreach ($_POST['vars_has_table_count'] as $for_ins => $val) {
				$grilleCount[$for_ins] = $val;
			}
			$APP_SCH->update(['idappscheme' => $idappscheme], ['grilleCount' => $grilleCount]);
			foreach ($_POST['vars_has_table_field'] as $for_ins => $val) {

				$for_ins                = explode('_', $for_ins);
				$idappscheme_tmp        = (int)$for_ins[0];
				$idappscheme_link       = (int)$for_ins[1];
				$idappscheme_field_link = (int)$for_ins[2];

				$ins = ['idappscheme' => $idappscheme, 'idappscheme_link' => $idappscheme_link, 'idappscheme_field' => $idappscheme_field_link];

				$ARR_FIELD = $APP_HAS->findOne(['idappscheme' => $idappscheme_link, 'idappscheme_field' => $idappscheme_field_link]);

				$arr_test_has = $APP_SCH_HAS_TABLE->findOne($ins);

				if (empty($val)):
					$APP_SCH_HAS_TABLE->remove($ins);
				else:
					$out                                  = [];
					$out['field_raw']                     = $ARR_FIELD['field_raw'];
					$out['field_name']                    = $ARR_FIELD['field_raw'] . ucfirst($ARR_FIELD['collection']);
					$out['nomAppscheme_has_table_field']  = $ARR_FIELD['nomAppscheme_field'] . ' ' . $ARR_FIELD['nomAppscheme'];
					$out['codeAppscheme_has_table_field'] = $ARR_FIELD['codeAppscheme_field'] . ucfirst($ARR_FIELD['codeAppscheme']);
					$out['idappscheme']                   = $idappscheme_tmp;
					$out['idappscheme_link']              = $idappscheme_link;
					$out['idappscheme_field']             = $idappscheme_field_link;
					$out['collection']                    = $ARR_FIELD['collection'];
					$out['nomAppscheme']                  = $ARR_FIELD['collection'];

					$APP_SCH_HAS_TABLE->create_update($ins, $out);

				endif;
			}

			return $ARGS;
		}

		public function addFK($ARGS) {
			$_id               = MongoCompat::toObjectId($_POST['_id']); 
			$arr               = fonctionsProduction::cleanPostMongo($_POST['vars']);
			$arr['uid']        = uniqid();
			$arrskel           = $this->plug('sitebase_app', 'appscheme')->findOne(['_id' => $_id]);
			$arr['ordreTable'] = (int)(sizeof($arrskel['grilleFK'])) + 1;
			$this->plug('sitebase_app', 'appscheme')->update(['_id' => $_id], ['$push' => ['grilleFK' => $arr]]);
			$_POST['reloadModule']['app/app_scheme/app_scheme_grille'] = '*';

			return $ARGS;
		}

		public function updFK($ARGS) {

			$vars = fonctionsProduction::cleanPostMongo($_POST['vars']);

			$_id = MongoCompat::toObjectId($_POST['_id']);
			$uid = $_POST['uid'];
			if (empty($uid) || empty($_id)) return $ARGS;

			foreach ($vars as $key => $value):
				$this->plug('sitebase_app', 'appscheme')->update(['_id' => $_id, 'grilleFK.uid' => $uid],
					['$set' => ['grilleFK.$.' . $key => $value]]);
			endforeach;

			return $ARGS;
		}

		public function reorderFK($ARGS) {
			// mode generique
			$outFK    = [];
			$arrskel  = $this->plug('sitebase_app', 'appscheme')->findOne(['codeAppscheme' => $_POST['table']]);
			$grilleFK = $arrskel['grilleFK'];
			$i=0;
			foreach ($_POST['ordreFK'] as $index => $uid_grille_block):
				$i++;
				$this->plug('sitebase_app', 'appscheme')->update(['codeAppscheme' => $_POST['table'],
				                                                 'grilleFK.uid'  => $uid_grille_block], ['$set' => ['grilleFK.$.ordreTable' => $i]]);
			endforeach;
			// redo
			$arrskel   = $this->plug('sitebase_app', 'appscheme')->findOne(['codeAppscheme' => $_POST['table']]);
			$grilleFK  = $arrskel['grilleFK'];
			$outGrille = [];
			foreach ($grilleFK as $index => $grilleItem):
				$outGrille[$grilleItem['ordreTable']] = $grilleItem;
			endforeach;
			ksort($outGrille);
			$outGrille = array_values($outGrille);
			$this->plug('sitebase_app', 'appscheme')->update(['codeAppscheme' => $_POST['table']], ['$set' => ['grilleFK' => $outGrille]]);

			return $ARGS;
		}

		public function deleteFK($ARGS) {
			$_POST['reloadModule']['app/app_scheme/app_scheme_grille'] = '*';

			$idappscheme = (int)$_POST['idappscheme'];
			$uid         = $_POST['uid'];
			if (empty($uid) || empty($idappscheme)) return $ARGS;

			$this->plug('sitebase_app', 'appscheme')->update(['idappscheme' => $idappscheme], ['$pull' => ['grilleFK' => ['uid' => $uid]]]);

			return $ARGS;
		}

	}