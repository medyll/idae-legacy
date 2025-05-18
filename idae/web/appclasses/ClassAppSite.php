<?php

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/10/2016
	 * Time: 00:29
	 */

	// dans le hub , tous vas vers liste produit ...
	// dans le hub, on liste les grille_fk de produit ( en filtrant ceux appartenant à vars )

	// dans la fiche, grande fiche table table_value + petites icones liste reverse_grille_fk
	// dans la fiche détaillée, petite fiche table table_value + full listes reverse_grille_fk

	// dans la fiche produits
	// dans les liste de produits

	class AppSite extends App {

		public $arr_page_type = ['hub' => 'Intermédiaire', 'liste' => 'Liste', 'fiche_detail' => 'fiche détaillée', 'fiche' => 'Fiche', 'home' => 'Home page', 'search' => 'recherche'];
		public $product_table = 'produit';
		public $tarif_table = 'produit_tarif';
		// tri  sur les tables
		public $FK_PRODUIT;
		public $RFK_PRODUIT;
		//
		private $filter = ['estSiteAppscheme' => 1];
		private $filter_produit = ['estActifProduit' => 1];

		private $RS_PRODUIT;
		private $query_cache = [];
		private $table_cache;

		private $img_size_arr = ['square' => ['120', '120'], 'small' => ['210', '140'], 'large' => ['650', '430'], 'wallpaper' => ['1920', '1080']];

		public function __construct() {
			parent::__construct();

			$this->APP_SITE           = new App('appsite');
			$this->APP_SITE_PAGE      = new App('appsite_page');
			$this->APP_SITE_PAGE_TYPE = new App('appsite_page_type');
			$this->APP_SCH            = new App('appscheme');
			$this->APP_PRODUIT        = new App($this->product_table);
			$this->APP_PRODUIT_TARIF  = new App($this->tarif_table);

			$this->table_cache = new stdClass();

			$this->FK_PRODUIT  = $this->get_grille_fk($this->product_table);
			$this->RFK_PRODUIT = $this->get_reverse_grille_fk($this->product_table);
		}

		public function get_grille_fk($table = '', $vars = []) {
			$vars = array_merge($this->filter, $vars);

			return parent::get_grille_fk($table, $vars);;
		}

		public function get_reverse_grille_fk($table, $table_value = '', $add = []) {
			$add = array_merge($this->filter, $add);

			return parent::get_reverse_grille_fk($table, $table_value, $add);
		}

		public function install() {

			$this->init_scheme('sitebase_web', 'appsite', ['fields' => ['code', 'nom', 'url']]);
			$this->init_scheme('sitebase_web', 'appsite_page_ligne', ['fields' => ['code', 'nom', 'ordre']]); // par defaut, tout grille fk de la table de appsite_page
			$this->init_scheme('sitebase_web', 'appsite_page_type', ['fields' => ['code', 'nom', 'ordre']]);
			$this->init_scheme('sitebase_web', 'appsite_page', ['fields' => ['code', 'nom', 'ordre'], ['has' => ['ligne', 'type']]]);
			$this->init_scheme('sitebase_web', 'appsite_template_type', ['fields' => ['code', 'nom', 'description', 'descriptionHTML']]);
			$this->init_scheme('sitebase_web', 'appsite_template', ['fields' => ['code', 'nom', 'description'], ['has' => ['type']]]);

			$host = APPSITE;

			$idappsite = $this->APP_SITE->create_update(['hostname' => $host], ['urlAppsite' => $host, 'nomAppsite' => $host]);

			// pages
			foreach ($this->arr_page_type as $code_page_type => $nom_page_type) {
				$this->APP_SITE_PAGE_TYPE->create_update(['idappsite' => (int)$idappsite, 'codeAppsite_page_type' => 'page_' . $code_page_type], ['nomAppsite_page_type' => $nom_page_type]);
			}
		}

		public function get_site_scheme($vars = []) {
			$vars   = array_merge($this->filter, $vars);
			$RS_SCH = $this->APP_SCH->find($vars)->sort(['nomAppscheme' => 1]);

			return $RS_SCH;
		}

		public function get_indirect_produit($table, $vars = [], $sort = '') {
			$key = http_build_query($vars) . $sort;
			if (!empty($this->query_cache[$key])) {
				return $this->query_cache[$key];
			}
			$vars             = array_merge($this->filter_produit, $vars);
			$this->RS_PRODUIT = $this->APP_PRODUIT->find($vars);

			if (!empty($sort)) $this->RS_PRODUIT->sort(['estTopProduit' => -1]);
			$this->query_cache[$key] = $this->RS_PRODUIT;

			return $this->RS_PRODUIT;
		}

		public function get_page($code_page, $data_vars = [], $mode = 'template') {
			ini_set('display_errors', 55);
			if (empty($this->arr_page_type[$code_page])) return false;

			if (!empty($data_vars['table']) && !empty($data_vars['table_value'])) {
				// rajouter data_table_filter = estActifTable
				$data_table       = $data_vars['table'];
				$data_table_value = (int)$data_vars['table_value'];
				$data_table_id    = 'id' . $data_table;
				$data_table_vars  = empty($data_vars['table_vars']) ? [] : $data_vars['table_vars'];
				$data_Table       = ucfirst($data_table);
				//
				$data_vars['vars'] = empty($data_vars['vars']) ? [] : $data_vars['vars'];
				//
				$dsp_out = $tpl_out = [];
				//
				$APP_TABLE        = $this->get_scheme($data_table);
				$data_table_actif = empty($APP_TABLE->has_field('estActif')) ? [] : ['estActif' . $data_Table => ['$in' => [1, true, 'on']]]; // rajouter data_table_filter = estActifTable

				$ARR_TABlE = $APP_TABLE->findOne(array_merge([$data_table_id => $data_table_value], $data_table_vars, $data_table_actif));
				// vardump(array_merge([$data_table_id => $data_table_value], $data_table_vars,$data_table_actif));

				if (empty($ARR_TABlE)) return [];
				$ARR_TABlE = array_filter($ARR_TABlE);//

				$dsp_out['page_data'] = $ARR_TABlE;
				$dsp_out['page_code'] = $code_page;
				$dsp_out['data_vars'] = $data_vars;

				$dsp_out['page_data_html']             = $this->get_fiche_fields($data_table, $ARR_TABlE);
				$dsp_out['page_data_html']['link']     = $this->set_lien_liste(array_merge($data_vars), $ARR_TABlE, ['fiche', 'fiche_detail', 'hub']);
				$dsp_out['page_data_html']['img']      = $this->get_image($data_table, $data_table_value);
				$dsp_out['page_data_html']['liste_fk'] = $this->get_fiche_fk($data_table, $ARR_TABlE, $data_vars);

				$dsp_out['page_data']['link'] = $this->set_lien_liste(array_merge($data_vars), $ARR_TABlE, ['fiche', 'fiche_detail', 'hub']);
				$dsp_out['page_data']['img']  = $this->get_image($data_table, $data_table_value);
				$dsp_out['page_name']         = $this->vars_to_titre(array_merge([$data_table_id => $data_table_value], $data_table_vars));//$code_page . ' ' . $data_table . ' ' . $data_table_value;

				$calendar                 = $this->get_calendar($data_vars);
				$tpl_out['page_calendar'] = AppTemplate::cf_template('appsite/item', ['data' => $calendar], 'calendar');

				// produit
				$arr_test     = $this->get_produit([$data_table_id => $data_table_value],'',4);
				$out_tpl_prod = [];
				// $out_tpl_prod['summary count ...']
				foreach ($arr_test as $key_test => $arr_prod) {
					$arr_tpl_data_prod = $this->get_tpl_data_produit($arr_prod);
					$out_arrtpl        = $this->filter_table_data('produit', $arr_prod);
					$out_tpl           = array_merge($out_arrtpl, $arr_tpl_data_prod);

					$out_tpl['table']       = 'produit';
					$out_tpl['table_value'] = $arr_prod['idproduit'];
					$out_tpl['link']        = $this->set_lien_liste($data_vars, $arr_prod, ['fiche']);
					$out_tpl['img']         = $this->get_image('produit', $arr_prod['idproduit']);

					$out_tpl_prod['liste_produit_small'][] = $out_tpl; // AppTemplate::cf_template('appsite/item', ['data' => $out_tpl], 'fiche_produit');
				}

				$tpl_out['liste_produit'] = AppTemplate::cf_template('appsite/item', ['data' => $out_tpl_prod], 'liste');

				switch ($code_page) {
					case "fiche": // dans la fiche, grande fiche table table_value + petites icones liste reverse_grille_fk

						$dsp_out['page_rfk'] = $this->get_fiche_rfk($data_table, [$data_table_id => $data_table_value], 'liste_micro');
						$tpl_out['page_rfk'] = AppTemplate::cf_template('appsite/item', $dsp_out, 'fiche_rfk');
						// vardump($dsp_out['page_rfk']);

						$dsp_out['page_fk'] = $this->get_fiche_fk($data_table, $ARR_TABlE, $data_vars);
						foreach ($dsp_out['page_fk'] as $key => $val) {
							$tpl_out['page_fk'] .= AppTemplate::cf_template('appsite/item', ['data' => array_merge($val['data_vars'], $val['page_data_html'])], 'fiche_mini');
						}
						// entete
						$tpl_out['page_entete'] = AppTemplate::cf_template('appsite/item', $dsp_out['page_data_html'] + $dsp_out['data_vars'], 'entete_fiche');
						// carrousel
						if (!$APP_TABLE->has_field_fk('fournisseur') && $APP_TABLE->table != 'fournisseur') {
							$test                      = $this->get_indirect_table('fournisseur', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');

						} else if (!$APP_TABLE->has_field_fk('ville') && $APP_TABLE->table != 'ville') {
							$test                      = $this->get_indirect_table('ville', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');
						} else if (!$APP_TABLE->has_field_fk('destination') && $APP_TABLE->table != 'destination') {
							$test                      = $this->get_indirect_table('destination', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');
						}

						$dsp_tpl_out = AppTemplate::cf_template('appsite/page/page_fiche', $tpl_out);

						break;
					case "fiche_detail": // dans la fiche détaillée, petite fiche table table_value + full listes reverse_grille_fk

						$dsp_out['page_rfk'] = $this->get_fiche_rfk($data_table, [$data_table_id => $data_table_value], 'liste_mini');
						$tpl_out['page_rfk'] = AppTemplate::cf_template('appsite/item', $dsp_out, 'fiche_rfk');

						$dsp_out['page_fk'] = $this->get_fiche_fk($data_table, $ARR_TABlE, $data_vars);
						foreach ($dsp_out['page_fk'] as $key => $val) {
							$tpl_out['page_fk'] .= AppTemplate::cf_template('appsite/item', ['data' => array_merge($val['data_vars'], $val['page_data_html'])], 'fiche_micro');
						}
						// entete
						$tpl_out['page_entete'] = AppTemplate::cf_template('appsite/item', array_merge($dsp_out['page_data_html'], $dsp_out['data_vars']), 'entete_fiche_detail');
						// carrousel
						if (!$APP_TABLE->has_field_fk('fournisseur') && $APP_TABLE->table != 'fournisseur') {
							$test                      = $this->get_indirect_table('fournisseur', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');

						} else if (!$APP_TABLE->has_field_fk('ville') && $APP_TABLE->table != 'ville') {
							$test                      = $this->get_indirect_table('ville', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');
						} else if (!$APP_TABLE->has_field_fk('destination') && $APP_TABLE->table != 'destination') {
							$test                      = $this->get_indirect_table('destination', $data_vars, $data_table_vars);
							$exp                       = ['liste' => $test['liste'], 'data' => $test['data']];
							$tpl_out['page_carrousel'] = AppTemplate::cf_template('appsite/item', ['data' => $exp, 'cartouche' => 'cartouche_carrousel'], 'liste');
						}

						$dsp_tpl_out = AppTemplate::cf_template('appsite/page/page_fiche_detail', $tpl_out);
						break;
					case "liste":

						break;
				}
			}

			if (!empty($dsp_tpl_out) && $mode == 'template') {
				return $dsp_tpl_out;
			} else if (!empty($tpl_out) && $mode == 'raw') {
				return $tpl_out;
			}

		}

		private function get_scheme($table) {

			if (!empty($this->table_cache->$table)) {
				return $this->table_cache->$table;
			}

			return new App($table);

		}

		public function get_fiche_fields($data_table, $ARR_TABlE) {

			$APP_TABLE  = $this->get_scheme($data_table);
			$data_Table = ucfirst($data_table);
			$dsp_out    = [];
			//$dsp_out['page_data']['link'] = $this->build_lien_liste(array_merge($data_vars), $ARR_TABlE, ['fiche', 'fiche_detail', 'hub']);
			// $dsp_out['img']     = $this->get_image($data_table, $ARR_TABlE["id" . $data_table]);
			$get_field_list_raw = $APP_TABLE->get_field_list_raw(); // codeAppscheme_field => field_name_raw ?
			foreach ($get_field_list_raw as $kk => $vv) {
				if (!empty($ARR_TABlE[$kk . $data_Table])) {
					$vv['field_value'] = $ARR_TABlE[$kk . $data_Table];
					$dsp_out[$kk]      = $APP_TABLE->cast_field($vv);
				} else if (!empty($ARR_TABlE[$kk])) {
					$vv['field_value'] = $ARR_TABlE[$kk];
					$dsp_out[$kk]      = $APP_TABLE->cast_field($vv); // pas touche
				}
			}

			return $dsp_out;
		}

		public function set_lien_liste($data_vars, $arr_rs = null, $all = []) {
			$page_fk         = [];
			$page_type_liste = empty($all) ? $this->arr_page_type : array_intersect_key($this->arr_page_type, array_flip($all));

			foreach ($page_type_liste as $key_page => $nom_page) {
				$link               = $this->set_lien($key_page, $data_vars, $arr_rs);
				$page_fk[$key_page] = $link;
			}

			return $page_fk;

		}

		public function set_lien($type_page, $data_vars, $arr_rs = null) {
			$page = $this->vars_to_titre($data_vars);
			switch ($type_page) {
				case "fiche": // description

					break;
				case "fiche_detail": // informations

					break;
				case "liste": // liste item + entete si table et table_value

					break;
				case "hub":

					break;
			}
			if (!empty($data_vars['table']) && !empty($data_vars['table_value'])) {
				if (!empty($arr_rs)) {
					$str = $data_vars['table'] . '-' . $data_vars['table_value'];
				} else {
					$str = 'ici---' . $data_vars['table'] . $data_vars['table_value'];
				}
			} else if (!empty($data_vars['table'])) {
				$str = $data_vars['table'];
			} else {
				$str = "NADA";
			}

			unset($data_vars['table'], $data_vars['table_value']);

			return HTTPCUSTOMERSITE . $type_page . "/$str/" . http_build_query($data_vars) . "/$page.html";

		}

		public function get_image($table = '', $value_id) {
			global $IMG_SIZE_ARR, $buildArr;
			$out       = [];
			$table     = empty($table) ? $this->table : $table;
			$app_table = $this->get_scheme($table);

			if (!$app_table->hasImageScheme) return $app_table->hasImageScheme;
			$arr_size = !empty($IMG_SIZE_ARR) ? array_merge($buildArr, $IMG_SIZE_ARR) : $this->img_size_arr;

			foreach ($arr_size as $k_size => $size):
				if ($app_table->app_table_one['hasImage' . $k_size . 'Scheme']) {
					$out['img_' . $k_size]['href'] = Act::imgSrc($table . '-' . $k_size . '-' . $value_id);
					$out['img_' . $k_size]['size'] = $size;
				}

			endforeach;

			return $out;

		}

		/** @return array */
		public function get_fiche_fk($data_table, $ARR_TABlE, $data_vars = []) {
			$page_fk = [];

			$fk_liste = $this->get_grille_fk($data_table);

			foreach ($fk_liste as $key_fk => $arr_fk) {
				$table_fk       = $arr_fk['codeAppscheme'];
				$Table_fk       = ucfirst($table_fk);
				$table_fk_id    = 'id' . $table_fk;
				$table_fk_value = (int)$ARR_TABlE[$table_fk_id];
				$APP_FK         = new App($table_fk);
				$vars_fk        = [$table_fk_id => (int)$ARR_TABlE[$table_fk_id]];
				$ARR_FK         = $APP_FK->findOne($vars_fk);
				unset($ARR_FK['_id']);
				if (empty($ARR_FK)) continue;// un jour ... Array
				$page_fk[$table_fk]['data_vars']   = $arr_fk;
				$page_fk[$table_fk]['table']       = $page_fk[$table_fk]['data_vars']['table'] = $table_fk;
				$page_fk[$table_fk]['table_value'] = $page_fk[$table_fk]['data_vars']['table_value'] = $table_fk_value;
				//$page_fk[$table_fk]['link']        = $this->build_lien_liste(array_merge($data_vars, $vars_fk), $arr_fk, ['fiche', 'fiche_detail', 'hub']);
				$get_field_list_raw = $APP_FK->get_field_list_raw();

				$page_fk[$table_fk]['page_data_html'] = $this->get_fiche_fields($table_fk, $ARR_FK);

				/*foreach ($get_field_list_raw as $kk => $vv) {

					if (!empty($ARR_TABlE[$kk . $Table_fk])) {
						$vv['field_value']                         = $ARR_TABlE[$kk . $Table_fk];
						$page_fk[$table_fk]['page_data_html'][$kk] = $APP_FK->cast_field($vv);
						$page_fk[$table_fk]['data_vars'][$kk]      = $APP_FK->cast_field($vv);
					} else {
						$vv['field_value']                         = $ARR_TABlE[$kk];
						$page_fk[$table_fk]['page_data_html'][$kk] = $APP_FK->cast_field($vv); // pas touche
						$page_fk[$table_fk]['data_vars'][$kk]      = $APP_FK->cast_field($vv); // pas touche
					}
					//
				}*/
				$exp                                     = ['table' => $table_fk, 'table_value' => $table_fk_value];
				$page_fk[$table_fk]['data_vars']['link'] = $this->set_lien_liste(array_merge($exp, $vars_fk), $arr_fk, ['fiche', 'fiche_detail', 'hub']);
				$page_fk[$table_fk]['data_vars']['img']  = $this->get_image($table_fk, $table_fk_value);
			}

			return $page_fk;

		}

		public function get_calendar($DBVARS = []) {

			unset($DBVARS['dateProduit_tarif']);
			$vars = ['id' . $DBVARS['table'] => (int)$DBVARS['table_value']];

			$emptyArr     = [];
			$collection   = $this->APP_PRODUIT;
			$collection_e = $this->APP_PRODUIT_TARIF;
			//
			$arr_p = $collection->distinct_all('idproduit', $vars + $this->filter_produit);

			$rs    = $collection_e->distinct_all('dateDebutProduit_tarif', ['idproduit' => ['$in' => $arr_p]]);
			$rs_px = $collection->distinct_all('prixProduit', ['idproduit' => ['$in' => $arr_p]]);

			//
			foreach ($rs as $key) {
				$ext                         = explode('-', $key);
				$emptyArr[$ext[0] . $ext[1]] = $ext[0] . '-' . $ext[1];
			}
			foreach ($rs_px as $key) {
				$emptyPxArr[(int)$key] = maskNbre($key);
			}
			ksort($emptyArr);
			ksort($emptyPxArr);

			$out = [];
			foreach ($emptyArr as $key => $arrP):
				$extract                             = explode('-', $arrP);
				$out[$key]['dateDebutProduit_tarif'] = $extract[0] . $extract[1];
				//
				$ct                        = $collection_e->distinct_all('idproduit', ['dateDebutProduit_tarif' => new MongoRegex("/$extract[0]-$extract[1]-*/i"), 'idproduit' => ['$in' => $arr_p]]);
				$out[$key]['prix']         = $collection->find(['idproduit' => ['$in' => $ct]], ['prixProduit' => 1])->sort(['prixProduit' => 1])->getNext()['prixProduit'];
				$out[$key]['count']        = sizeof($ct);
				$out[$key]['mois_fr']      = fonctionsProduction::mois_short_Date_fr($arrP);
				$out[$key]['link_produit'] = Act::lienListeProduit($vars + ['dateDebutProduit_tarif' => $extract[0] . $extract[1]]);
			endforeach;

			return array_chunk($out, 12);
		}

		public function get_produit($vars = [], $sort = '', $limit = 10) {

			$key = 'produit' . http_build_query($vars) . $sort;
			if (!empty($this->query_cache[$key])) {
				return $this->query_cache[$key];
			}
			$vars             = array_merge($this->filter_produit, $vars);
			$this->RS_PRODUIT = $this->APP_PRODUIT->find($vars);

			if (!empty($sort)) $this->RS_PRODUIT->sort(['nomAppscheme' => 1]) ;
			$this->query_cache[$key] = $this->RS_PRODUIT->limit($limit);

			return $this->RS_PRODUIT;
		}

		static function get_tpl_data_produit($data = []) {

			$APP = new App('produit');

			$ARRREF = Act::getTableKeyOrder();
			$id     = 'idproduit';

			$out = [];
			//$out['href']             = Act::lienProduit($data[$id]);
			$out['dureeNuit']    = $data['dureeJour'] - 1;
			$out['nomTransport'] = $data['nomTransport_type'] . ' ' . $data['nomTransport'];
			//
			$arraddtpl = ['fournisseur', 'transport', 'destination', 'pays', 'ville'];

			$arr_date = $APP->plug('sitebase_production', 'produit_tarif')->distinct('dateDebutProduit_tarif', ['idproduit' => (int)$data[$id]]);

			foreach ($arr_date as $key => $arrDate):
				if (!empty($arrDate)):
					$dateArr = explode('-', $arrDate);
					$mois_fr = fonctionsSite::mois_fr($dateArr[1]);
					$jour    = (int)$dateArr[2];
					$annee   = (int)$dateArr[0];
					if (sizeof($arr_date) < 5):
						$outD[$arrDate][] = $jour . ' ' . $mois_fr . ' ' . $annee . ' ';
					else:
						$outD[$annee][(int)$dateArr[1]] = $mois_fr . ' ';
					endif;
				endif;
			endforeach;
			if (!empty($outD)):
				foreach ($outD as $key3 => $value3):
					$more = (sizeof($arr_date) < 5) ? ' ' : ' ' . $key3;
					$dateDepart .= fonctionsProduction::andLast($value3) . $more;
				endforeach;
				$out['dateDepartProduit'] = $dateDepart;
			endif;

			if (!empty($data['idtransport'])) :
				$out['nomTransport'] = ucfirst($out['nomTransport_type']) . ' ' . ucfirst($out['nomTransport']) . '<br>';
			endif;
			if (!empty($data['grilleHotelProduit'])) :
				$th = (sizeof($data['grilleHotelProduit']) > 1) ? ' HOTELS ' : $data['grilleHotelProduit'][0]['nomHotel'];
				$out['hotel'] .= '<div class="bordert"><span class="textegris"><i class="fa fa-home"></i>&nbsp;' . $th . '</span> ';
				foreach ($data['grilleHotelProduit'] as $value):
					$desc   = (empty($value['descriptionHotel'])) ? $value['grilleClauseHotel']['SITU']['descriptionHotel_clause'] : $value['descriptionHotel'];
					$tmph[] = (sizeof($data['grilleHotelProduit']) == 1) ? ' ' . coupeChaine($desc, 240) . ' ' : $value['nomHotel'];
				endforeach;
				$out['hotel'] .= fonctionsProduction::andLast($tmph);
				$out['hotel'] .= '</div>';
			endif;
			if ($data['dureeProduit'] > 1) :
				$dis_pays = $APP->plug('sitebase_production', 'produit_etape')->distinct('nomPays', ['idproduit' => (int)$id]);
				// echo $dis_pays;
				$out['itinerairePaysProduit'] .= coupeChaine(implode('', $dis_pays), 220);
				//$out['itinerairePaysProduit'] .= coupeChaine(fonctionsSite::itinerairePaysProduit($data[$id]) , 220);

				$out['itineraireProduit'] .= '<div class="bordert"><span class="textegris"><i class="fa fa-random"></i>&nbsp;ITINERAIRE</span> ';
				$out['itineraireProduit'] .= coupeChaine(fonctionsSite::itineraireProduit($data[$id]), 330);
				$out['itineraireProduit'] .= '</div>';
				$out['smallItineraireProduit'] .= 'Départ de ' . $data['villeDepartProduit'] . ' arrivée à ' . $data['villeArriveeProduit'];
				$out['trajetProduit'] = ((!empty($data['villeArriveeProduit'])) ? 'De ' : '') . $data['villeDepartProduit'] . ((!empty($data['villeArriveeProduit'])) ? ' à ' : '') . $data['villeArriveeProduit'];
			else:
				$out['smallItineraireProduit'] .= $data['grilleEtapeProduit'][0]['nomVille'] . ' ' . $data['grilleEtapeProduit'][0]['nomPays'];
				$out['trajetProduit'] .= $data['grilleEtapeProduit'][0]['nomVille'] . ' ' . $data['grilleEtapeProduit'][0]['nomPays'];
			endif;
			//

			if ($data['idpaysDepartProduit'] == 357) {
				$out['dep_fr'] = '<div title="Départs de France">&nbsp;&nbsp;</div>';
			}
			//

			//
			$out['prix']     = !empty($data['prixPromoProduit']) ? $data['prixPromoProduit'] . '&nbsp;€' : $data['prixProduit'] . '&nbsp;€';
			$out['old_prix'] = !empty($data['prixPromoProduit']) ? $data['prixProduit'] . '&nbsp;€' : '';
			//
			$out['oldPrixProduit'] = !empty($data['oldPrixProduit']) ? '<span  style="text-decoration: line-through">' . $data['oldPrixProduit'] . '&nbsp;€</span>' : '';
			$out['promotion']      = !empty($data['oldPrixProduit']) ? '<span  style="text-decoration: line-through">' . $data['oldPrixProduit'] . '&nbsp;€</span>' : '';
			$out['pct']            = empty($data["oldPrixProduit"]) ? '' : ' ' . (100 - (int)pourcentage($data["oldPrixProduit"], $data["prixProduit"])) . '&nbsp;%';
			$out['diff']           = empty($data["oldPrixProduit"]) ? '' : ($data["prixProduit"] - $data["oldPrixProduit"]) . '&nbsp;€';

			return $out;
		}

		/**
		 * @vars data_vars = [table,table_value,vars]
		 * @vars data_vars.table_vars : requete sur la table
		 * @vars data_vars.vars variable sur produit ?
		 */
		private function filter_table_data($table, $data) {
			$out   = [];
			$Table = ucfirst($table);
			foreach ($data as $key => $value) {
				$new_key       = str_replace($Table, '', $key);
				$out[$new_key] = $value;
			}

			return $out;
		}

		public function  get_fiche_rfk($data_table, $data_vars, $type_liste = '') { // sauf produit
			$parent    = ["id$data_table" => (int)$data_vars['id' . $data_table]];
			$page_rfk  = [];
			$rfk_liste = $this->get_reverse_grille_fk($data_table, $data_vars['id' . $data_table]);
			foreach ($rfk_liste as $key_fk => $arr_rfk) {
				if ($arr_rfk['table'] == 'produit') continue;
				$table_fk                         = $arr_rfk['table'];
				$data_vars['table']               = $arr_rfk['table'];
				$data_vars['table_value']         = (int)$arr_rfk['table_value'];
				$page_rfk[$key_fk]                = $arr_rfk;
				$page_rfk[$key_fk]['table']       = $arr_rfk['table'];
				$page_rfk[$key_fk]['table_value'] = $arr_rfk['table_value'];
				$page_rfk[$key_fk]['link']        = $this->set_lien_liste($data_vars, $arr_rfk, ['liste']); // que lien de type liste
				$page_rfk[$key_fk]['img']         = $this->get_image($data_vars['table'], $data_vars['table_value']);
				if (!empty($type_liste)) {

					$APP_TMP = $this->get_scheme($data_vars['table']);
					$RS_TMP  = $APP_TMP->find($parent)->limit(10); // sort top ! ... prix !!!
					// echo "cherche $data_table dans  ".$data_vars['table'].' '. $RS_TMP->count();
					$ARR_TMP = iterator_to_array($RS_TMP);
					foreach ($ARR_TMP as $key => $value) {
						foreach ($value as $key_tmp => $value_tmp) {
							if (str_find($key_tmp, ucfirst($data_vars['table']))) {
								$new_key                 = str_replace(ucfirst($data_vars['table']), '', $key_tmp);
								$ARR_TMP[$key][$new_key] = $value_tmp;
							}
						}
						$ARR_TMP[$key]['img'] = $this->get_image($table_fk, $value["id$table_fk"]);
					}
					$page_rfk[$key_fk][$type_liste] = $ARR_TMP;

				}
			}

			return $page_rfk;
		}

		public function get_indirect_table($table = '', $data_vars = [], $data_table_vars = [], $pivot = "") {
			if (!$pivot) $pivot = $this->product_table;
			$idtable          = "id$table";
			$Table            = ucfirst($table);
			$table_main       = $data_vars['table'];
			$table_main_id    = "id$table_main";
			$table_main_value = (int)$data_vars['table_value'];
			$vars_main        = [$table_main_id => $table_main_value];
			$key              = 'pivot' . $table . $table_main . $table_main_value . $pivot;
			$data_out         = [];
			//
			if (!empty($this->query_cache[$key])) {
				return $this->query_cache[$key];
			}
			$vars = array_merge($this->filter_produit, $vars_main);
			// PIVOT

			$RS_PRODUIT = $this->APP_PRODUIT->distinct_all("id$table", $vars);
			//
			$APP                  = $this->get_scheme($table);
			$rs                   = $APP->find(["id$table" => ['$in' => $RS_PRODUIT]])->limit(10); // sort !!!
			$out_data             = $APP->app_table_one;
			$out_data['count']    = $rs->count();
			$out_data['maxcount'] = $rs->count(true);//
			$out_data['link']     = $this->set_lien_liste($vars_main + $data_vars['vars'] + ['table' => $table]);
			//
			$get_field_list_raw = $APP->get_field_list_raw(); // codeAppscheme_field => field_name_raw ?
			while ($arr = $rs->getNext()) {
				foreach ($get_field_list_raw as $kk => $vv) {
					if (!empty($arr[$kk . $Table])) {
						$vv['field_value']             = $arr[$kk . $Table];
						$data_out[$arr[$idtable]][$kk] = $APP->cast_field($vv);
					} else if (!empty($arr[$kk])) {
						$vv['field_value']             = $arr[$kk];
						$data_out[$arr[$idtable]][$kk] = $APP->cast_field($vv); // pas touche
					}
					$data_out[$arr[$idtable]]['img'] = $this->get_image($table, $arr[$idtable]);

					$data_out[$arr[$idtable]]['link'] = $this->set_lien_liste($vars_main + $data_vars['vars'] + ['table' => $table, 'table_value' => $arr[$idtable]], $arr);
				}
			}

			$this->query_cache[$key] = $data_out;

			return ['data' => $out_data, 'liste' => $data_out];
		}

		/*public function get_liste_fk($data_table, $ARR_TABlE, $data_vars) {
			$tpl_out            = '';
			$dsp_out['page_fk'] = $this->get_fiche_fk($data_table, $ARR_TABlE, $data_vars);
			foreach ($dsp_out['page_fk'] as $key => $val) {
				$tpl_out  .=  AppTemplate::cf_template('appsite/item', ['data' => array_merge($val['data_vars'], $val['page_data_html'])], 'fiche_micro');
			}

			return $tpl_out ;
		}*/

		function get_field_list_raw($in = []) {
			$out = [];
			if (!empty($in)) $DIST = $this->appscheme_field->distinct('idappscheme_field', $in);
			$DIST = (!empty($DIST)) ? ['idappscheme_field' => ['$in' => $DIST]] : [];

			$rsG = $this->appscheme_has_field->find($DIST + ['idappscheme' => (int)$this->idappscheme]);
			// $rsG = $this->appscheme_field->find( ['idappscheme_field' => ['$in' => $arr_field]]);
			$a = [];
			while ($arrg = $rsG->getNext()) :
				$test                           = $this->appscheme_field->findOne(['idappscheme_field' => (int)$arrg['idappscheme_field']]);
				$a['nomAppscheme_field_type']   = $test['nomAppscheme_field_type'];
				$a['codeAppscheme_field_type']  = $test['codeAppscheme_field_type'];
				$a['nomAppscheme_field_group']  = $test['nomAppscheme_field_group'];
				$a['codeAppscheme_field_group'] = $test['codeAppscheme_field_group'];
				$a['codeAppscheme_field']       = $test['codeAppscheme_field'] . ucfirst($arrg['codeAppscheme']);
				$a['field_name']                = $test['codeAppscheme_field'] . ucfirst($arrg['codeAppscheme']);
				$a['field_name_raw']            = $test['codeAppscheme_field'];
				$out[$a['field_name']]          = $a;
			endwhile;

			$rsG = $this->appscheme_has_table_field->find($DIST + ['idappscheme' => (int)$this->idappscheme]);
			// idappscheme_link
			$a = [];
			while ($arrg = $rsG->getNext()) :
				$test                           = $this->appscheme->findOne(['idappscheme' => (int)$arrg['idappscheme_link']]);
				$a['nomAppscheme']              = $test['nomAppscheme'];
				$a['codeAppscheme']             = $test['codeAppscheme'];
				$test                           = $this->appscheme_field->findOne(['idappscheme_field' => (int)$arrg['idappscheme_field']]);
				$a['nomAppscheme_field_type']   = $test['nomAppscheme_field_type'];
				$a['codeAppscheme_field_type']  = $test['codeAppscheme_field_type'];
				$a['nomAppscheme_field_group']  = $test['nomAppscheme_field_group'];
				$a['codeAppscheme_field_group'] = $test['codeAppscheme_field_group'];

				$a['field_name']       = $test['codeAppscheme_field'] . ucfirst($a['codeAppscheme']);
				$a['field_name_raw']   = $test['codeAppscheme_field'];
				$out[$a['field_name']] = $a;
			endwhile;

			return $out;
		}
	}