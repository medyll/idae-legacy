<?php
	require_once __DIR__ . '/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;

	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 11/12/14
	 * Time: 00:35
	 */
	class Act extends App {
		public
		function __construct() {
			parent::__construct();
		}

		static function makeTplDataProduit($table, $data = [], $soid = '') {

			$APP = new App('produit');

			$ARRREF = Act::getTableKeyOrder();
			$id     = (empty($soid)) ? 'id' . $table : $soid;

			$out                     = Act::makeTplData($table, $data, $soid);
			$out['href']             = Act::lienProduit($data[$id]);
			$out['iddestination']    = $data['iddestination'];
			$out['idpays']           = $data['idpays'];
			$out['idville']          = $data['idville'];
			$out['idhotel']          = $data['grilleHotelProduit'][0]['idhotel'];
			$out['idtransport']      = $data['idtransport'];
			$out['nomProduit']       = $data['nomProduit'];
			$out['atoutProduit']     = $data['atoutProduit'];
			$out['idfournisseur']    = $data['idfournisseur'];
			$out['dureeJourProduit'] = $data['dureeJourProduit'];
			$out['prixProduit']      = maskNbre($data['prixProduit'], 2);
			$out['dureeProduit']     = $data['dureeProduit'];
			$out['dureeNuitProduit'] = $data['dureeJourProduit'] - 1;
			$out['nomTransport']     = $data['nomTransport_type'] . ' ' . $data['nomTransport'];
			//
			$arraddtpl = ['fournisseur', 'transport', 'destination', 'pays', 'ville'];
			foreach ($arraddtpl as $value):
				$out[$value . 'Src_mini']   = Act::imageSite($value, $out['id' . $value], 'mini');
				$out[$value . 'Src_tiny']   = Act::imageSite($value, $out['id' . $value], 'tiny');
				$out[$value . 'Src_small']  = Act::imageSite($value, $out['id' . $value], 'small');
				$out[$value . 'Src_square'] = Act::imageSite($value, $out['id' . $value], 'square');
				$out[$value . 'Src_squary'] = Act::imageSite($value, $out['id' . $value], 'squary');
				$out[$value . 'Src_long']   = Act::imageSite($value, $out['id' . $value], 'long');
				$out[$value . 'Src_large']  = Act::imageSite($value, $out['id' . $value], 'large');
			endforeach;

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
			$out['prod_coeurProduit']     = !empty($data['estCoeurProduit']) ? '&nbsp;<i class="fa fa-heart cursor textevert" title="Coup de coeur"></i>' : '';
			$out['prod_homePageProduit']  = !empty($data['homePageProduit']) ? '&nbsp;<i class="fa fa-star cursor" title="Focus"></i>' : '';
			$out['prod_promoProduit']     = !empty($data['estPromoProduit']) ? '&nbsp;<i class="fa fa-tags cursor textebleu" title="Promotion et baisse de prix"></i>' : '';
			$out['prod_toutIncluProduit'] = !empty($data['toutIncluProduit']) ? '&nbsp;<i class="fa fa-credit-card cursor" title="Tout inclu"></i>' : '';
			// $out['prod_volProduit']       = ! empty($data['volProduit']) ? '&nbsp;<div class="padding ededed"> <i class="fa fa-plane cursor fa-2x" title="Vol inclu"></i> Vol inclu</div>' : '';
			//
			if (!empty($data['atoutProduit'])) {
				$out['atoutProduit'] = '<div><i class="fa fa-thumbs-o-up fg-mauve padding5">&nbsp;&nbsp;' . $data['atoutProduit'] . '</i></div>';
			}
			if ($data['idpaysDepartProduit'] == 357) {
				$out['dep_fr'] = '<div title="Départs de France">&nbsp;&nbsp;</div>';
			}
			//
			if ($data['codeProduit_type'] == 'TR') {
				$out['linkSrc_main'] = $out['destinationSrc_small'];
				$out['linkSrc_more'] = $out['transportSrc_tiny'];
			}
			if ($data['codeProduit_type'] == 'CR') {
				$out['linkSrc_main'] = $out['destinationSrc_small'];
				$out['linkSrc_more'] = $out['transportSrc_tiny'];
			}
			if ($data['codeProduit_type'] == 'CIRC') {
				$out['linkSrc_main'] = $out['paysSrc_small'];
				$out['linkSrc_more'] = $out['destinationSrc_tiny'];
			}
			if ($data['codeProduit_type'] == 'SEJ') {
				$out['linkSrc_main'] = $out['hotelSrc_small'];
				$out['linkSrc_more'] = $out['paysSrc_tiny'];
			}
			if ($data['codeProduit_type'] == 'CF') {
				$out['linkSrc_main'] = $out['destinationSrc_small'];
				$out['linkSrc_more'] = $out['transportSrc_tiny'];
			}
			if ($data['codeProduit_type'] == 'CIRC' || $data['codeProduit_type'] == 'SEJ') {
				$out['fournisseurSrc_tiny'] = '';
				$out['fournisseurSrc_mini'] = '';
			}

			//
			$data['prixPromoProduit'] = (int)$data['prixPromoProduit'];
			$data['prixProduit']      = (int)$data['prixProduit'];
			$out['prix']              = !empty($data['prixPromoProduit']) ? $data['prixPromoProduit'] . '&nbsp;€' : $data['prixProduit'] . '&nbsp;€';
			$out['old_prix']          = !empty($data['prixPromoProduit']) ? $data['prixProduit'] . '&nbsp;€' : '';
			//
			$out['oldPrixProduit'] = !empty($data['oldPrixProduit']) ? '<span  style="text-decoration: line-through">' . $data['oldPrixProduit'] . '&nbsp;€</span>' : '';
			$out['promotion']      = !empty($data['oldPrixProduit']) ? '<span  style="text-decoration: line-through">' . $data['oldPrixProduit'] . '&nbsp;€</span>' : '';
			$out['pct']            = empty($data["oldPrixProduit"]) ? '' : ' ' . (100 - (int)pourcentage($data["oldPrixProduit"], $data["prixProduit"])) . '&nbsp;%';
			$out['diff']           = empty($data["oldPrixProduit"]) ? '' : ($data["prixProduit"] - $data["oldPrixProduit"]) . '&nbsp;€';

			return $out;
		}

		function getTableKeyOrder() {
			$dakeys = ['fleuve'             => 'fleuve',
			           'hotel'              => 'hotel',
			           'produit_selection'  => 'produit_selection',
			           'theme'              => 'theme',
			           'ville'              => 'ville',
			           'villeDepartProduit' => 'ville',
			           'pays'               => 'pays',
			           'paysDepartProduit'  => 'pays',
			           'destination'        => 'destination',
			           'continent'          => 'continent',
			           'transport'          => 'transport',
			           'fournisseur'        => 'fournisseur',
			           'produit_type'       => 'produit_type',
			           'vacance'            => 'vacance'];

			return $dakeys;
		}

		static function makeTplData($table, $value_id, $soid = '') {
			$APP                      = new App($table);
			$data                     = [];
			$basic_fields_query       = $APP->get_basic_fields_query();
			$basic_fields_query_table = $APP->get_basic_fields_query($table);
			$arr                      = $APP->query_one(['id' . $table => (int)$value_id], $basic_fields_query_table);

			foreach ($basic_fields_query as $field => $key):
				$out[$field] = nl2br($arr[$field . ucfirst($table)]);
			endforeach;

			$upc = ucfirst($table);

			$out['nom']               = $arr['nom' . $upc];
			$out['className']         = empty($arr['estTop' . $upc]) ? '' : 'bold';
			$out['preText']           = '';
			$out['linkText']          = $arr['nom' . $upc];
			$out['descText']          = nl2br($arr['description' . $upc]);
			$out['description']       = nl2br($arr['description' . $upc]);
			$out['shortDescText']     = coupeChaine($arr['description' . $upc], 200, ' ..');
			$out['descriptionCourte'] = coupeChaine($arr['description' . $upc], 200, ' ..');
			//
			$arr_size = ['mini', 'tiny', 'squary', 'square', 'small', 'long', 'large'];
			foreach ($arr_size as $k_size => $size):
				$out['linkSrc_' . $size] = Act::imgSrc($table . '-' . $size . '-' . $value_id);
			endforeach;
			$out['linkSrc_mini']   = Act::imgSrc($table . '-' . $value_id . '-mini');
			$out['linkSrc_tiny']   = Act::imgSrc($table . '-' . $value_id . '-tiny');
			$out['linkSrc_squary'] = Act::imgSrc($table . '-' . $value_id . '-squary');
			$out['linkSrc_square'] = Act::imgSrc($table . '-' . $value_id . '-square');
			$out['linkSrc_small']  = Act::imgSrc($table . '-small-' . $value_id);
			$out['linkSrc_long']   = Act::imgSrc($table . '-' . $value_id . '-long');
			$out['linkSrc_large']  = Act::imgSrc($table . '-' . $value_id . '-large');
			//$out['linkSrc_reflect'] = Act::imgApp($table , $value_id , 'large' , 'reflect');

			$out['href']               = Act::lienListeProduit(['id' . $table => (int)$value_id]);
			$out['href_fiche']         = Act::lienFiche(['id' . $table => (int)$value_id], $table);
			$out['href_intermediaire'] = Act::lienIntermediaire(['id' . $table => (int)$value_id], 'hub/' . $table);
			// $out['href_intermediaire'] = Act::lienIntermediaire(['id' . $table => (int)$value_id], 'intermediaire/' . $table);
			// vardump($out);
			return $out;
		}

		static function imgSrc($image_name) {

			$APP            = new App();
			$raw_image_name = $image_name;
			$file_extension = strtolower(substr(strrchr($image_name, '.'), 1));
			if (empty($file_extension)) {
				$image_name .= '.jpg';
			};
			$reflect = isset($reflect) ? $reflect : ''; // Fix undefined variable
			$type = empty($reflect) ? 'jpg' : 'png';

			$con  = $APP->plug_base('sitebase_image');
			$grid = $con->getGridFs();
			// Modern MongoDB driver: create index on files collection directly
			try {
				$filesCollection = $con->getDatabase()->selectCollection($con->getGridFsBucketName() . ".files");
				$filesCollection->createIndex(['filename' => 1]);
			} catch (\Exception $e) {
				error_log("GridFS index creation failed: " . $e->getMessage());
			}
			//
			$image = $grid->findOne(['filename' => $image_name]);
			if (empty($image)) {
				$image = $grid->findOne(['filename' => $raw_image_name]);
			}
			$dir  = $image->file['metadata']['tag'] . '/'; // tag = table
			$file = $image->file;

			switch ($file['metadata']['contentType']) {
				case "image/jpeg":
					$ext = 'jpg';
					break;
				case "image/jpg":
					$ext = 'jpg';
					break;
				case "image/gif":
					$ext = 'gif';
					break;
				case "image/png":
					$ext = 'png';
					break;
				default:
					$ext = 'jpg';
					break;
			}

			// echo FLATTENIMGDIR . $dir;

			$image_file      = FLATTENIMGDIR . $dir . $image_name;
			$image_http_file = FLATTENIMGHTTP . $dir . $image_name;

			if (!file_exists(FLATTENIMGDIR)) {
				mkdir(FLATTENIMGDIR);
			}
			if (!file_exists(FLATTENIMGDIR . $dir)) {
				mkdir(FLATTENIMGDIR . $dir);
			}
			if (empty($image) && !file_exists($image_file)) {
				// echo "ok 3 ";
				return '';

				return empty($image) . " but not found $image_file";// HTTPIMAGES."blank.png?f=".$image_http_file;

			}
			if (!empty($image) || !file_exists($image_file)) {
				// on écrit image
				$file = $image->file;
				$sdir = $image->file['metadata']['tag'];
				if (is_array($sdir)) {
					$sdir = $sdir[0];
				}

				$dir = FLATTENIMGDIR . $sdir . '/';
				if (!file_exists($dir) && !empty($file['length'])) {
					mkdir($dir, 0777);
				}
				if (file_exists($dir) && !empty($file['length']) && !empty($file['chunkSize'])):

					if (file_exists($dir . $image_name)) {
						$length   = $image->file['length'];
						$filesize = filesize($dir . $image_name);

						if ($length == $filesize):
							return $image_http_file;
						else:
							//@chmod($dir , 777);
							$image->write($dir . $image_name);

							return $image_http_file;
						endif;
					}
					if (!file_exists($dir . $image_name)) {

						$image->write($dir . $image_name);

						return $image_http_file;
					}
				endif;

			}
			if (file_exists($image_file)) {
				return $image_http_file;

			}

			return $image_name;
		}

		static function lienListeProduit($vars) {
			return Act::lienIntermediaire($vars, 'catalogue_produit');
		}

		function lienIntermediaire($arrIn, $type_page = 'lien_liste', $pfxpage = '') {
			$APP = new App();

			$vars_out = [];
			$arrIn    = fonctionsProduction::cleanPostMongo($arrIn, 1);
			$base     = $APP->plug_base('sitebase_production');

			if (!empty($arrIn['idproduit'])) {
				$vars_out[]   = 'iprod' . $arrIn['idproduit'];
				$arr          = $base->produit->findOne(['idproduit' => (int)$arrIn['idproduit']]);
				$vars_titre[] = $arr['nomProduit'];
			}
			if (!empty($arrIn['idproduit_type'])) {
				$vars_out[]               = 'prt' . $arrIn['idproduit_type'];
				$arrpfx['idproduit_type'] = $arrIn['idproduit_type'];
				$arr                      = $base->produit_type->findOne(['idproduit_type' => (int)$arrIn['idproduit_type']]);
				$vars_titre[]             = $arr['plurielProduit_type'];
			}
			if (!empty($arrIn['idcontinent'])):
				$vars_out[]   = 'cont' . $arrIn['idcontinent'];
				$arr          = $base->continent->findOne(['idcontinent' => (int)$arrIn['idcontinent']]);
				$vars_titre[] = $arr['nomContinent'];
			endif;
			if (!empty($arrIn['idpaysDepartProduit'])) {
				$vars_out[]   = 'pyd' . $arrIn['idpaysDepartProduit'];
				$arr          = $base->pays->findOne(['idpays' => (int)$arrIn['idpaysDepartProduit']]);
				$vars_titre[] = 'partant ' . $arr['nomPays'];
			}
			if (!empty($arrIn['iddestination'])):
				$vars_out[]   = 'des' . $arrIn['iddestination'];
				$arr          = $base->destination->findOne(['iddestination' => (int)$arrIn['iddestination']]);
				$vars_titre[] = $arr['nomDestination'];
			endif;
			if (!empty($arrIn['dureeJourProduit'])) {
				$vars_out[]   = 'nbj' . $arrIn['dureeJourProduit'] . '';
				$vars_titre[] = $arrIn['dureeJourProduit'] . ' jours';
			}
			if (!empty($arrIn['idpays'])) {
				$vars_out[]   = 'py' . $arrIn['idpays'];
				$arr          = $base->pays->findOne(['idpays' => (int)$arrIn['idpays']]);
				$vars_titre[] = $arr['nomPays'];
			}
			if (!empty($arrIn['dateDebutProduit_tarif']) && empty($arrIn['idvacance'])) {
				$vars_out[]   = 'date' . $arrIn['dateDebutProduit_tarif'] . '';
				$mois         = substr($arrIn['dateDebutProduit_tarif'], 4, 2);
				$annee        = substr($arrIn['dateDebutProduit_tarif'], 0, 4);
				$mois_fr      = fonctionsSite::mois_fr($mois);
				$vars_titre[] = $mois_fr . '-' . $annee;
			}
			if (!empty($arrIn['idvilleDepartProduit'])) {
				$vars_out[]   = 'vd' . $arrIn['idvilleDepartProduit'] . '';
				$arr          = $base->ville->findOne(['idville' => (int)$arrIn['idvilleDepartProduit']]);
				$vars_titre[] = 'departs ' . $arr['nomVille'];
			}
			if (!empty($arrIn['idville'])) {
				$vars_out[]   = 'vi' . $arrIn['idville'];
				$arr          = $base->ville->findOne(['idville' => (int)$arrIn['idville']]);
				$vars_titre[] = 'escales ' . $arr['nomVille'];
			}
			if (!empty($arrIn['idfournisseur'])) {
				$vars_out[]               = 'fo' . $arrIn['idfournisseur'];
				$arr                      = $base->fournisseur->findOne(['idfournisseur' => (int)$arrIn['idfournisseur']]);
				$arrpfx['idproduit_type'] = $arr['idproduit_type'];
				$vars_titre[]             = $arr['nomProduit_type'] . '-' . $arr['petitNomFournisseur'];
			}
			if (!empty($arrIn['idtransport'])) {
				$vars_out[]               = 'idt' . $arrIn['idtransport'];
				$arr                      = $base->transport->findOne(['idtransport' => (int)$arrIn['idtransport']]);
				$arrpfx['idproduit_type'] = $arr['idproduit_type'];
				$vars_titre[]             = $arr['nomTransport_type'] . ' ' . $arr['nomTransport'];
			}
			if (!empty($arrIn['idtransport_cabine'])) {
				$vars_out[]            = 'idtc' . $arrIn['idtransport_cabine'];
				$arr                   = $base->transport_cabine->findOne(['idtransport_cabine' => (int)$arrIn['idtransport_cabine']]);
				$arrpfx['idtransport'] = $arr['idtransport'];
				$vars_titre[]          = $arr['nomTransport'] . ' ' . $arr['nomTransport_cabine'];
			}
			if (!empty($arrIn['idvacance'])):
				$vars_out[]   = 'vac' . $arrIn['idvacance'];
				$arr          = $APP->plug('sitebase_production', 'vacance')->findOne(['idvacance' => (int)$arrIn['idvacance']]);
				$vars_titre[] = $arr['nomVacance'];
			endif;
			if (!empty($arrIn['homePageProduit'])):
				$vars_out[]   = 'star1';
				$vars_titre[] = 'focus';
			endif;
			if (!empty($arrIn['coeurProduit'])):
				$vars_out[]   = 'coupdecoeur1';
				$vars_titre[] = 'coup de coeur';
			endif;
			if (!empty($arrIn['promoProduit'])):
				$vars_out[]   = 'promo1';
				$vars_titre[] = 'prix doux';
			endif;
			if (!empty($arrIn['volProduit'])):
				$vars_out[] = 'volinclu';
			endif;
			if (!empty($arrIn['toutIncluProduit'])):
				$vars_out[] = 'toutinclu';
			endif;
			if (!empty($arrIn['idtheme'])):
				$vars_out[]   = 'th' . $arrIn['idtheme'];
				$arr          = $base->theme->findOne(['idtheme' => (int)$arrIn['idtheme']]);
				$vars_titre[] = $arr['nomTheme'];
			endif;
			if (!empty($arrIn['idproduit_selection'])):
				$vars_out[]   = 'ps' . $arrIn['idproduit_selection'];
				$arr          = $base->produit_selection->findOne(['idproduit_selection' => (int)$arrIn['idproduit_selection']]);
				$vars_titre[] = $arr['nomProduit_selection'];
			endif;
			if (!empty($arrIn['idhotel'])):
				$vars_out[]   = 'idh' . $arrIn['idhotel'];
				$arr          = $base->hotel->findOne(['idhotel' => (int)$arrIn['idhotel']]);
				$vars_titre[] = $arr['nomHotel'];
			endif;
			if (!empty($arrIn['idmer'])):
				$vars_out[]   = 'mer' . $arrIn['idmer'];
				$arr          = $base->mer->findOne(['idmer' => (int)$arrIn['idmer']]);
				$vars_titre[] = $arr['nomMer'];
			endif;
			if (!empty($arrIn['idfleuve'])):
				$vars_out[]   = 'flv' . $arrIn['idfleuve'];
				$arr          = $base->fleuve->findOne(['idfleuve' => (int)$arrIn['idfleuve']]);
				$vars_titre[] = $arr['nomFleuve'];
			endif;
			sort($vars_out);
			$vars = implode('-', $vars_out);
			if (sizeof($vars_titre) != 0) {
				$page = implode('-', (array)$vars_titre);

				return HTTPCUSTOMERSITE . $type_page . '/' . $vars . '/' . $pfxpage . niceUrl($page) . '.html';
			}

			return $vars;
		}

		static function lienFiche($vars, $table = 'destination') {
			return Act::lienIntermediaire($vars, 'fiche_presentation/' . $table);
		}

		function lienProduit($arr) {
			$APP = new App('produit');

			if (is_numeric($arr)) {
				$arr = $APP->query_one(['idproduit' => (int)$arr]);
			}

			$titre = niceUrl($arr['nomDestination']) . '/' . niceUrl($arr['nomFournisseur']) . '/' . niceUrl($arr['nomTransport']);

			return HTTPCUSTOMERSITE . $titre . '/fiche-' . $arr['idproduit'] . '.html';

		}

		static function imageSite($famille, $id, $size = 'small', $reflect = false) {
			if ($famille == 'produit') {
				return fonctionsSite::imageProduit($id, $size);
			}

			return Act::imgApp($famille, $id, $size, $reflect);

		}

		static function imgApp($famille, $id, $size = 'small', $reflect = '') {
			error_log("DEBUG: ClassAct::imgApp START famille=$famille id=$id size=$size reflect=$reflect");
			ini_set('display_errors', 0);

			$APP = new App();

			if ($famille == 'produit') {
				return fonctionsSite::imageProduit($id, $size);
			}
			$con  = $APP->plug_base('sitebase_image');
			$grid = $con->getGridFs();
			
			if ($size == 'smallest') {
				global $IMG_SIZE_ARR, $buildArr;
				$arr_size = array_merge(array_keys($buildArr), array_keys($IMG_SIZE_ARR));
				$str      = implode("|", $arr_size);
				//echo $str = "$famille-($str)-$id";
				//$dist = $grid->distinct('filename', ['filename' => ['$regex' => "$str"]]);
				foreach ($arr_size as $key => $valimg) {
					//echo $valimg . ' => ';
					//echo "<br>";
					foreach ($dist as $key_dist => $val_dist) {
						echo $val_dist . '||';
						if (preg_match("/$famille-$valimg-$id/i", $val_dist)) {
							//echo "found  ";
							$size = $valimg;
							break;
						}
						//	echo "<br>";

					}
					if ($size != 'smallest') break;
				}

			}
			//	echo " => $size \\ ";

			$zen   = empty($reflect) ? '' : '_reflect';
			$type  = empty($reflect) ? 'jpg' : 'png';
			$image = strtolower($famille) . '-' . $size . '-' . $id . $zen . '.' . $type;
			//
			$arrImage  = explode('/', $image);
			$nameImage = $arrImage[0];
			//
			$nude_name = strtolower($famille) . '-' . $size . '-' . $id . $zen;

			$fullNameImg = $nameImage . '.' . $type;
			error_log("DEBUG: ClassAct::imgApp query nude_name=$nude_name type=$type");
			try {
				$image = $grid->findOne($nude_name . '.' . $type);
				if (empty($image)) {
					error_log("DEBUG: ClassAct::imgApp not found with ext, trying bare");
					$image = $grid->findOne($nude_name);
				}
			} catch (Exception $e) {
				error_log("ERROR: ClassAct::imgApp EXCEPTION: " . $e->getMessage());
				return "http://www.notfound.com/images/error.png";
			}
			
			if (empty($image)) {
				error_log("DEBUG: ClassAct::imgApp image empty, returning 404 placeholder");
				return "http://www.notfound.com/images/blank.png?f=" . $nude_name;
			}
			
			//	vardump($image);
			// echo " $nude_name. $type , $nude_name  ";
			// $dir  = $image->file['metadata']['tag'] . '/';
			$dir  = $famille . '/';
			
			error_log("DEBUG: ClassAct::imgApp image found, processing file data");
			$file = isset($image->file) ? $image->file : []; // Safety check

			$contentType = isset($file['metadata']['contentType']) ? $file['metadata']['contentType'] : '';
			switch ($contentType) {
				case "image/jpeg":
					$ext = 'jpg';
					break;
				case "image/jpg":
					$ext = 'jpg';
					break;
				case "image/gif":
					$ext = 'gif';
					break;
				case "image/png":
					$ext = 'png';
					break;
				default:
					$ext = 'jpg';
					break;
			}

			$image_file      = FLATTENIMGDIR . $dir . $file['filename']; // . '.' . $ext
			$image_http_file = FLATTENIMGHTTP . $dir . $file['filename']; // . '.' . $ext

			if (empty($image) && !file_exists($image_file)) {
				return "http://www.notfound.com/images/blank.png?f=" . $nude_name;
			}
			//echo " what 00 ";
			if (!empty($image) || !file_exists($image_file)) { // && 3 == 8
				//	echo " what 11 ";
				// on écrit image ?? null;
				$file = isset($image->file) ? $image->file : [];
				$sdir = isset($file['metadata']['tag']) ? $file['metadata']['tag'] : 'default';
				
				if (empty($sdir)) $sdir = 'default';
				
				if (is_array($sdir)) {
					$sdir = isset($sdir[0]) ? $sdir[0] : 'default';
				}
				//	echo " what 22 ";
				$dir = FLATTENIMGDIR . $sdir . '/';
				if (!file_exists($dir) && !empty($file['length'])) {
					mkdir($dir, 0777);
				}
				if (file_exists($dir) && !empty($file['length']) && !empty($file['chunkSize'])):
					// echo " what 33 ".$dir . $file['filename'] . '.' . $ext . '<br/>';
					if (!file_exists($dir . $file['filename'])) {
						//		echo " what 44 ";
						// echo $dir . $file['filename'] . '.' . $ext . '<br/>';

						$image->write($dir . $file['filename']);

						return $image_http_file;
						// $grid->update(array("_id" => $id), array('$inc' => array("downloads" => 1)));
					} else {
						return $image_http_file;
					}
				endif;

			}
			if (file_exists($image_file)) {
				return $image_http_file;

			}

		}

		function consolidate_image($table, $table_value, $codeTailleImage = 'small') {
			global $buildArr;
			global $IMG_SIZE_ARR;

			$base       = empty($_POST['base']) ? 'sitebase_image' : $_POST['base'];
			$collection = empty($_POST['collection']) ? 'fs' : $_POST['collection'];
			$codeImage  = empty($_POST['codeImage']) ? $table . '-' . strtolower($codeTailleImage) . '-' . $table_value : $_POST['codeImage'];
			$file_name  = empty($_POST['keep_file_name']) ? $codeImage . '.' . strtolower('jpg') : $_POST['filename'];
			$db         = $this->plug_base($base);
			$grid       = empty($collection) ? $db->getGridFs() : $db->getGridFs($collection);

			// ecriture sur disk // si keep_file_name sinon
			$dir        = FLATTENIMGDIR . $table . '/';
			$image_file = $dir . $file_name;

			if (!file_exists($dir)) {
				mkdir($dir, 0777);
				exec("chmod $dir 0777");
			}

			switch ($codeTailleImage):
				case  'square':
					$vars['build'] = ['small'  => ['from' => $codeTailleImage, 'width' => $buildArr['small'][0], 'height' => $buildArr['small'][1]],
					                  'squary' => ['from' => $codeTailleImage, 'width' => $buildArr['squary'][0], 'height' => $buildArr['squary'][1]]];
					break;
				case  'small' :
					$vars['build'] = ['smally' => ['from' => $codeTailleImage, 'width' => $buildArr['smally'][0], 'height' => $buildArr['smally'][1]],
					                  'tiny'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['tiny'][0], 'height' => $IMG_SIZE_ARR['tiny'][1]],
					                  'square' => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]]
					];
					break;
				case  'large':
					$vars['build'] = ['long'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['long'][0], 'height' => $IMG_SIZE_ARR['long'][1]],
					                  'small'  => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['small'][0], 'height' => $IMG_SIZE_ARR['small'][1]],
					                  'tiny'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['tiny'][0], 'height' => $IMG_SIZE_ARR['tiny'][1]],
					                  'square' => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]]];
					break;
				case  'wallpaper':
					$vars['build'] = ['small'  => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['small'][0], 'height' => $IMG_SIZE_ARR['small'][1]],
					                  'smally' => ['from' => $codeTailleImage, 'width' => $buildArr['smally'][0], 'height' => $buildArr['smally'][1]],
					                  'square' => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]],
					                  'tiny'   => ['from' => $codeTailleImage, 'width' => $buildArr['tiny'][0], 'height' => $buildArr['tiny'][1]]];
					break;

			endswitch;
			//
			$ins_size = $ins;
			unset($ins_size['filename']);
			foreach ($vars['build'] as $key => $build):
				if (empty($build['width']) || empty($build['height'])) continue;
				$new_src = str_replace($codeTailleImage, $key, $file_name);
				// test _exist //
				$image_sized = $grid->findOne(["filename" => $new_src]);

				if (!empty($image_sized->getFilename())) continue;
				// GRIDFS
				$thumbed          = fonctionsSite::makeGdThumb($file_name, $build['width'], $build['height'], $key, $_POST['mongoTag'], $build['from'], ['real_filename' => $ins['real_filename']]);
				$image_file_sized = $dir . $new_src;
				//
				skelMdl::send_cmd('act_notify', ['msg' => ' Génération => ' . $new_src], session_id());
				//
				skelMdl::reloadModule('app/app_img/image_dyn', $image_file_sized);

			endforeach;

			// THUMB
			$ins_thumb = $ins;
			unset($ins_thumb['filename']);
			$new_file_name         = str_replace($codeTailleImage, 'thumb', $file_name);
			$smallbytes            = fonctionsSite::gridImage($_id, $collection, $base, 50, 50);
			$ins_thumb['thumb']    = 1;
			$ins_thumb['filename'] = $new_file_name;
			// écriture mongodb GRIFS
			$grid->remove(["filename" => $new_file_name]);
			$grid->storeBytes($smallbytes, ["filename" => $new_file_name, "metadata" => $ins_thumb]);
		}

		static function decodeUri($vars) {
			if (empty($vars)) {
				return [];
			}
			if (is_array($vars)) {
				return $vars;
			}
			$outvars = [];
			$dakeys  = Act::getUriKeys();
			$arrVars = explode('-', $vars);
			foreach ($arrVars as $key => $value):
				foreach ($dakeys as $keyname => $keycode):
					$val = (str_replace($keycode, '', strstr($value, $keycode)));
					if (!empty($val)):
						$outvars[$keyname] = $val;
					endif;
				endforeach;
			endforeach;

			return fonctionsProduction::cleanPostMongo($outvars, 1);
		}

		static function getUriKeys() {
			$dakeys = ['idproduit'              => 'iprod',
			           'idproduit_type'         => 'prt',
			           'dureeJourProduit'       => 'nbj',
			           'dateDebutProduit_tarif' => 'date',
			           'idvilleDepartProduit'   => 'vd',
			           'idville'                => 'vi',
			           'idpays'                 => 'py',
			           'idpaysDepartProduit'    => 'pyd',
			           'idtransport'            => 'idt',
			           'idtransport_cabine'     => 'idtc',
			           'idfournisseur'          => 'fo',
			           'iddestination'          => 'des',
			           'idcontinent'            => 'cont',
			           'idmer'                  => 'mer',
			           'idvacance'              => 'vac',
			           'homePageProduit'        => 'star',
			           'coeurProduit'           => 'coupdecoeur',
			           'promoProduit'           => 'promo',
			           'volProduit'             => 'volinclu',
			           'toutIncluProduit'       => 'toutinclu',
			           'idtheme'                => 'th',
			           'idproduit_selection'    => 'ps',
			           'idhotel'                => 'idh',
			           'idfleuve'               => 'flv'];

			return $dakeys;
		}

		function dynamicScheme($tot) {
			if ($tot == 1) {
				$scheme = 'scheme_long';
			}
			if ($tot >= 2 && $tot <= 5) {
				$scheme = 'scheme_smallest';
			}
			if ($tot >= 6 && $tot <= 11) {
				$scheme = 'scheme_small';
			}
			if ($tot >= 12) {
				$scheme = 'scheme_tiny';
			}

			return $scheme;
		}

		function buildDevis($vars) {

			$APP = new App('devis');
			if (empty($vars['idclient']) && !empty($vars['emailDevis'])):
				$emailClient = strtolower(trim($vars['emailDevis']));
				$testCli     = $APP->plug('sitebase_devis', 'client')->find(['emailClient' => $emailClient]);

				if ($testCli->count() == 0) {
					//
					$arrCL                    = ['nomClient' => strtolower($vars['nomDevis']), 'prenomClient' => strtolower($vars['prenomDevis']), 'emailClient' => $emailClient];
					$arrCL['telephoneClient'] = (string)$vars['telephoneDevis'];
					$arrCL['sexeClient']      = $vars['sexeDevis'];
					$arrCL['idclient']        = (int)$APP->getNext('idclient');
					//
					$arrCL['ipClient'] = $_SERVER['REMOTE_ADDR'];
					//
					$arrCL['nomClient']       = $vars['nomDevis'];
					$arrCL['prenomClient']    = $vars['prenomDevis'];
					$arrCL['telephoneClient'] = $vars['telephoneDevis'];
					//
					$APP->plug('sitebase_devis', 'client')->insert($arrCL);
					//
					$vars['idclient'] = (int)$arrCL['idclient'];
					//
				} else {
					$arr                  = $testCli->getNext();
					$arrClean['idclient'] = (int)$arr['idclient'];
					// Le dernier agent du dernier devis de ce client
					$arrAg = $APP->plug('sitebase_devis', 'devis')->findOne(['idclient' => (int)$vars['idclient']]);
					if (!empty($arrAg['idagent'])) {
						$arrClean['idagent'] = (int)$arrAg['idagent'];
					}
				}
			endif;
			//
			$idproduit             = (int)$vars['idproduit'];
			$idproduit_tarif       = (int)$vars['idproduit_tarif'];
			$idproduit_tarif_gamme = (int)$vars['idproduit_tarif_gamme'];
			$idtransport_gamme     = (int)$vars['idtransport_gamme'];
			//
			$arrProduit     = $APP->plug('sitebase_production', 'produit')->findOne(['idproduit' => (int)$idproduit]);
			$arrFournisseur = $APP->plug('sitebase_production', 'fournisseur')->findOne(['idfournisseur' => (int)$arrProduit['idfournisseur']]);
			$arrTransport   = $APP->plug('sitebase_production', 'transport')->findOne(['idtransport' => (int)$arrProduit['idtransport']]);
			//
			$arrPx = $APP->plug('sitebase_production', 'produit_tarif_gamme')->findOne(['idproduit_tarif_gamme' => $idproduit_tarif_gamme]);
			$count = $APP->plug('sitebase_devis', 'devis')->find(['dateCreationDevis' => date('Y-m-d')])->count();
			//
			$vars['iddevis_type']       = 1;
			$vars['dateCreationDevis']  = date('Y-m-d');
			$vars['telephoneDevis']     = (string)maskTel($vars['telephoneDevis']);
			$vars['timeCreationDevis']  = time();
			$vars['heureCreationDevis'] = date('H:i:s');
			/** @var  $iddevis */
			$iddevis             = $vars['iddevis'] = (int)$APP->getNext('iddevis');
			$vars['nomDevis']    = $vars['nomDevis'] . ' ' . $vars['prenomDevis'] . ' ' . $arrFournisseur['nomFournisseur'] . ' ' . $arrTransport['nomTransport'];
			$vars['numeroDevis'] = $iddevis . $arrFournisseur['codeFournisseur'] . '-' . $arrTransport['codeTransport'];
			$vars['codeDevis']   = $iddevis . $arrFournisseur['codeFournisseur'] . '-' . $arrTransport['codeTransport'];
			$vars['md5Devis']    = md5($vars['numeroDevis']);
			//
			$arrClean = $vars;

			$arrClean['idfournisseur']  = (int)$arrProduit['idfournisseur'];
			$arrClean['nomFournisseur'] = $arrProduit['nomFournisseur'];
			$arrClean['idtransport']    = (int)$arrProduit['idtransport'];
			$arrClean['nomTransport']   = $arrProduit['nomTransport'];
			//
			$arrClean['dateDebutDevis']  = date_fr($arrPx['dateDebutProduit_tarif']);
			$arrClean['dateRetourDevis'] = date('Y-m-d', strtotime($arrPx['dateDebutProduit_tarif']) + ($arrProduit['dureeProduit'] * 86400));
			$arrClean['dateFinDevis']    = date('Y-m-d', strtotime($arrPx['dateDebutProduit_tarif']) + ($arrProduit['dureeProduit'] * 86400));
			//
			$arrTarif = $APP->plug('sitebase_production', 'produit_tarif_gamme')->findOne(['idproduit_tarif_gamme' => $idproduit_tarif_gamme]);
			//
			$prix      = empty($arrPx["prixPromoProduit_tarif_gamme"]) ? $arrPx["prixProduit_tarif_gamme"] : $arrPx["prixPromoProduit_tarif_gamme"];
			$prixPromo = empty($arrPx["prixPromoProduit_tarif_gamme"]) ? '' : $arrPx["prixPromoProduit_tarif_gamme"];
			//
			if ($vars["nbreAdulteDevis"] == 1) {
				$prixSiteDevis = $prix + ($prix / 2);
				$toteco        = $prixPromo + ($prixPromo / 2);
			} else {
				$prixSiteDevis = $prix * $vars["nbreAdulteDevis"];
				$toteco        = ($prixPromo * $vars["nbreAdulteDevis"]);
			}
			if (!empty($vars['nbreEnfantDevis'])) {
				$prixSiteDevis += $arrPx['prixProduit_tarif_gamme'] * $vars['nbreEnfantDevis'];
			}

			$arrClean['prixSiteDevis'] = (int)$prixSiteDevis;

			//
			$arrClean['ipDevis'] = $_SERVER['REMOTE_ADDR'];

			$APP->plug('sitebase_devis', 'devis')->insert($arrClean, ['safe' => true]);

			return (int)$iddevis;
		}

		function lienNewsletter($id) {
			$APP = new App('newsletter');

			$arr = $APP->query_one(['idnewsletter' => (int)$id]);

			$titre = HTTPCUSTOMERSITE . 'newsletter/' . $id . '/' . niceUrl($arr['nomNewsletter']) . '.html';

			return $titre;

		}

		function getPrefixeKeys() {
			$dakeys = ['idtheme_type'         => '',
			           'idtheme'              => 'theme',
			           'idhotel'              => 'hotel',
			           'idville'              => 'escale',
			           'idvilleDepartProduit' => 'départ',
			           'idpays'               => '',
			           'idpaysDepartProduit'  => '',
			           'idfleuve'             => 'fleuve',
			           'iddestination'        => '',
			           'idcontinent'          => '',
			           'idtransport'          => '',
			           'idfournisseur'        => '',
			           'idproduit_type'       => ''];

			return $dakeys;
		}

		function Cdf($vars = []) {
			$ARRKEYS  = Act::getInfoKeys();
			$ARRTABLE = Act::getInfoTableKeys();
			$CDF      = [];
			$ARROUT   = [];
			$ARRKEYS  = array_reverse($ARRKEYS);
			foreach ($vars as $key => $value) {
				$tmp_table       = substr($key, 2);
				$CDF[$tmp_table] = $value;
				$APP_TMP         = new App($tmp_table);
				$arr_grille      = array_keys($APP_TMP->get_grille_fk($tmp_table));
				foreach ($arr_grille as $key_f => $value_f) {
					$CDF[$value_f] = $key_f;
				}

			}
			//vardump($CDF);
			foreach ($CDF as $id => $valueid):
				$COLLECT['id' . $id] = $CDF[$id];
				//echo $tmp_table = $id;
				//echo '<br>';
				$APP_TMP = new App($tmp_table);
				//
				$name = 'nom' . ucfirst($tmp_table);
				$arr  = $APP_TMP->findOne(['id' . $id => (int)$CDF[$id]]);
				//
				if (!empty($arr[$name])):
					$ARROUT[$id]['valueid'] = (int)$CDF[$id];
					$ARROUT[$id]['title']   = $arr[$name];
					$ARROUT[$id]['vars']    = $COLLECT;
					$ARROUT[$id]['nameid']  = $id;
					$ARROUT[$id]['image']   = $ARRKEYS[$id];
				endif;
			endforeach;

			return $ARROUT;
			if (!empty($vars['idproduit_type'])):
				$idproduit_type        = $vars['idproduit_type'];
				$CDF['idproduit_type'] = $idproduit_type;
			endif;
			if (!empty($vars['idfournisseur'])):
				$idfournisseur  = $vars['idfournisseur'];
				$fourn          = skelMongo::connect('fournisseur')->findOne(['idfournisseur' => (int)$idfournisseur]);
				$idproduit_type = $fourn['idproduit_type'];
				if (empty($idproduit_type)) {
					$tmp            = skelMongo::connect('produits')->distinct('idproduit_type', $DBVARS);
					$idproduit_type = $tmp[0];
				}
				$CDF['idproduit_type'] = $idproduit_type;
				$CDF['idfournisseur']  = $idfournisseur;

			endif;
			if (!empty($vars['idtransport'])):
				$idtransport    = $vars['idtransport'];
				$transp         = skelMongo::connect('transports')->findOne(['idtransport' => (int)$idtransport]);
				$idfournisseur  = $transp['idfournisseur'];
				$ouTY           = skelMongo::connect('produits')->findOne(['idtransport' => (int)$idtransport]);
				$idproduit_type = $ouTY['idproduit_type'];
				if (empty($idproduit_type)) {
					$tmp            = skelMongo::connect('produits')->distinct('idproduit_type', $DBVARS);
					$idproduit_type = $tmp[0];
				}
				$CDF['idproduit_type'] = $idproduit_type;
				$CDF['idfournisseur']  = $idfournisseur;
				$CDF['idtransport']    = $idtransport;
			endif;
			if (!empty($vars['idvilleDepartProduit'])):
				$idville              = (int)$vars['idvilleDepartProduit'];
				$ville                = skelMongo::connect('ville')->findOne(['idville' => (int)$idville]);
				$CDF['idcontinent']   = $ville['idcontinent'];
				$CDF['iddestination'] = $ville['iddestination'];
				$CDF['idpays']        = $ville['idpays'];
				$CDF['idville']       = $ville['idville'];
			endif;
			if (!empty($vars['idville'])):
				$idville              = (int)$vars['idville'];
				$ville                = skelMongo::connect('ville')->findOne(['idville' => (int)$idville]);
				$CDF['idcontinent']   = $destination['idcontinent'];
				$CDF['iddestination'] = $destination['iddestination'];
				$CDF['idpays']        = $ville['idpays'];
				$CDF['idville']       = $ville['idville'];
			endif;
			if (!empty($vars['idpays'])):
				$idpays               = $vars['idpays'];
				$pays                 = skelMongo::connect('pays')->findOne(['idpays' => (int)$idpays]);
				$destination          = skelMongo::connect('destination')->findOne(['iddestination' => (int)$pays['iddestination']]);
				$CDF['idcontinent']   = $destination['idcontinent'];
				$CDF['iddestination'] = $destination['iddestination'];
				$CDF['idpays']        = $pays['idpays'];
			endif;
			if (!empty($vars['iddestination'])):
				$iddestination        = $vars['iddestination'];
				$destination          = skelMongo::connect('destination')->findOne(['iddestination' => (int)$iddestination]);
				$CDF['idcontinent']   = $destination['idcontinent'];
				$CDF['iddestination'] = $destination['iddestination'];
			endif;
			if (!empty($vars['idcontinent'])):
				$idcontinent        = $vars['idcontinent'];
				$destination        = skelMongo::connect('continent')->findOne(['idcontinent' => (int)$idcontinent]);
				$CDF['idcontinent'] = $destination['idcontinent'];
			endif;
			if (!empty($vars['idtheme'])):
				$idtheme             = $vars['idtheme'];
				$the                 = skelMongo::connect('theme')->findOne(['idtheme' => (int)$idtheme]);
				$idtheme_type        = $the['idtheme_type'];
				$ouTY                = skelMongo::connect('theme_type')->findOne(['idtheme_type' => (int)$idtheme_type]);
				$CDF['idtheme_type'] = $idtheme_type;
				$CDF['idtheme']      = $idtheme;
			endif;
			foreach ($CDF as $id => $valueid):
				$COLLECT[$id] = $CDF[$id];
				$mongoTable   = $ARRTABLE[$id];
				//
				$name = 'nom' . ucfirst($ARRKEYS[$id]);
				$arr  = skelMongo::connect($mongoTable)->findOne([$id => (int)$CDF[$id]], [$name => 1]);
				//
				if (!empty($arr[$name])):
					$ARROUT[$id]['valueid'] = (int)$CDF[$id];
					$ARROUT[$id]['title']   = $arr[$name];
					$ARROUT[$id]['vars']    = $COLLECT;
					$ARROUT[$id]['nameid']  = $id;
					$ARROUT[$id]['image']   = $ARRKEYS[$id];
				endif;
			endforeach;

			return $ARROUT;
		}

		static function getInfoKeys() {
			$dakeys = ['idproduit_selection'  => 'produit_selection',
			           'idtheme'              => 'theme',
			           'idtheme_type'         => 'theme_type',
			           'idhotel'              => 'hotel',
			           'idville'              => 'ville',
			           'idvilleDepartProduit' => 'ville',
			           'idpays'               => 'pays',
			           'idpaysDepartProduit'  => 'pays',
			           'idfleuve'             => 'fleuve',
			           'iddestination'        => 'destination',
			           'idcontinent'          => 'continent',
			           'idtransport'          => 'transport',
			           'idfournisseur'        => 'fournisseur',
			           'idproduit_type'       => 'produit_type'];

			return $dakeys;
		}

		function getInfoTableKeys() {
			$dakeys = ['idproduit_selection'  => 'produit_selection',
			           'idtheme_type'         => 'theme_type',
			           'idtheme'              => 'theme',
			           'idhotel'              => 'hotel',
			           'idvilleDepartProduit' => 'ville',
			           'idville'              => 'ville',
			           'idpays'               => 'pays',
			           'idpaysDepartProduit'  => 'pays',
			           'idfleuve'             => 'fleuve',
			           'iddestination'        => 'destination',
			           'idcontinent'          => 'continent',
			           'idtransport'          => 'transport',
			           'idfournisseur'        => 'fournisseur',
			           'idproduit_type'       => 'produit_type',
			           'idtransport_gamme'    => 'transport_gamme'];

			return $dakeys;
		}

		function oneNom() {
			$AAK = Act::getInfoKeys();
		}

		function descVars($vars, $mode = 'desc') {
			$APP = new App();
			//
			$DBVARS = Act::decodeVars($vars);
			// si fournisseur => devient le préfixe
			if (!empty($vars['idtheme'])) {
				$prefixe = Act::titreVars(['idtheme' => (int)$vars['idtheme']]);
			} elseif (!empty($vars['idville'])) {
				$prefixe = Act::titreVars(['idville' => (int)$vars['idville']]);
			} elseif (!empty($vars['idvilleDepartProduit'])) {
				$prefixe = Act::titreVars(['idvilleDepartProduit' => (int)$vars['idvilleDepartProduit']]);
			} elseif (!empty($vars['idpays'])) {
				$prefixe = Act::titreVars(['idpays' => (int)$vars['idpays']]);
			} elseif (!empty($vars['idpaysDepartProduit'])) {
				$prefixe = Act::titreVars(['idpays' => (int)$vars['idpaysDepartProduit']]);
			} elseif (!empty($vars['iddestination'])) {
				$prefixe = Act::titreVars(['iddestination' => (int)$vars['iddestination']]);
			} elseif (!empty($vars['idcontinent'])) {
				$prefixe = Act::titreVars(['idcontinent' => (int)$vars['idcontinent']]);
			} elseif (!empty($vars['idfournisseur'])) {
				$prefixe = Act::titreVars(['idfournisseur' => (int)$vars['idfournisseur']]);
			} elseif (!empty($vars['idtransport'])) {
				$prefixe = Act::titreVars(['idtransport' => (int)$vars['idtransport']]);
			} elseif (!empty($vars['idproduit_type'])) {
				$prefixe = Act::titreVars(['idproduit_type' => (int)$vars['idproduit_type']]);
			}

			$base = $APP->plug_base('sitebase_production');

			if (empty($vars['idproduit_type'])) {
				$arrout[] = $base->produit->distinct('nomProduit_type', $DBVARS);
			}
			if (empty($vars['idfournisseur'])) {
				$arrout[] = $base->produit->distinct('nomFournisseur', $DBVARS);
			}
			if (empty($vars['idtransport'])) {
				$arrout[] = $base->produit->distinct('nomTransport', $DBVARS);
			}
			$arrout[] = $base->produit->distinct('grilleDestinationProduit.nomDestination', $DBVARS);
			$arrout[] = $base->produit->distinct('grilleEtapeProduit.nomPays', $DBVARS);
			$arrout[] = $base->produit->distinct('grilleEtapeProduit.nomVille', $DBVARS);

			foreach ($arrout as $last):
				$arrfinal = ($mode == 'desc') ? array_slice($last, 0, 3) : array_slice($last, 0, 8);
				$sep      = ($mode == 'desc') ? ', ' : ' ';
				$out .= $prefixe . ' ' . fonctionsProduction::andLast($arrfinal, $sep, 'et ' . strtolower($prefixe) . ' ') . '.  ';
			endforeach;
			if ($mode == 'kw') {
				$arro = explode(' ', $out);
				$arro = array_unique($arro);
				$out  = implode(' ', $arro);
			}

			return $out;
		}

		static function decodeVars($arrIn) {
			$APP = new App();

			$vars_out = [];
			$URIK     = Act::getUriKeys();
			$arrIn    = fonctionsProduction::cleanPostMongo($arrIn, 1);

			$base = $APP->plug_base('sitebase_production');

			foreach ($URIK as $var => $px):

			endforeach;
			if (!empty($arrIn['idproduit'])) {
				$arrIn[] = (int)$arrIn['idproduit'];
			}
			if (!empty($arrIn['dureeJourProduit'])) {
				switch ($arrIn['dureeJourProduit']):
					case '0S':
						$arrIn['dureeJourProduit'] = ['$lt' => 7];
						break;
					case '1S':
						$arrIn['dureeJourProduit'] = ['$gte' => 6, '$lte' => 8];
						break;
					case '2S':
						$arrIn['dureeJourProduit'] = ['$gte' => 8, '$lte' => 14];
						break;
					case '34S':
						$arrIn['dureeJourProduit'] = ['$gte' => 14, '$lte' => 21];
						break;
					case '5S':
						$arrIn['dureeJourProduit'] = ['$gte' => 21];
						break;
				endswitch;
			}
			if (!empty($arrIn['dateDebutProduit_tarif'])) {
				$mois  = substr($arrIn['dateDebutProduit_tarif'], 4, 2);
				$annee = substr($arrIn['dateDebutProduit_tarif'], 0, 4);

				$arrIn['grilleDateProduit'] = MongoCompat::toRegex('^' . preg_quote($annee . '-' . $mois, '/'), 'i'); // array('$gte' => $annee . '-' . $mois . '-01', '$lte' => $annee . '-' . $mois . '-31');
				//print_r($arrIn['grilleDateProduit']);
				unset($arrIn['dateDebutProduit_tarif']);

			}
			/*if (!empty($arrIn['idville'])) {
				$arrIn['idvilleDepartProduit'] = $arrIn['idville'];
				unset($arrIn['idville']);
			}*/
			if (!empty($arrIn['idpays'])) {
				$arrIn['grilleEtapeProduit.idpays'] = $arrIn['idpays'];
				unset($arrIn['idpays']);
			}
			/*if (!empty($arrIn['iddestination'])):
				$arrIn['grilleDestinationProduit.iddestination'] = $arrIn['iddestination'];
				unset($arrIn['iddestination']);
			endif;*/
			if (!empty($arrIn['idcontinent'])):
				$arrIn['grilleDestinationProduit.idcontinent'] = $arrIn['idcontinent'];
				unset($arrIn['idcontinent']);
			endif;
			if (!empty($arrIn['idvacance'])):
				$arrV             = $base->vacances->findOne(['idvacance' => (int)$arrIn['idvacance']]);
				$dateDebutVacance = $arrV['dateDebutVacance'];
				$dateFinVacance   = $arrV['dateFinVacance'];
				//
				$mois    = substr($arrV['dateDebutVacance'], 5, 2);
				$annee   = substr($arrV['dateDebutVacance'], 0, 4);
				$out_d[] = MongoCompat::toRegex(preg_quote($annee . '-' . $mois, '/') . '-*', 'i');

				//$mois                                         = substr($arrV['dateFinVacance'], 5, 2);
				//$annee                                        = substr($arrV['dateFinVacance'], 0, 4);
				//$out_d[] = new MongoRegex("/$annee-$mois-*/i");

				// myddeDebug($out_d);
				//
				// $arrIn['grilleDateProduit.dateProduit_tarif'] = array('$gte' => $dateDebutVacance, '$lte' => $dateFinVacance);
				$arrIn['grilleDateProduit.dateDebutProduit_tarif'] = MongoCompat::toRegex(preg_quote($annee . '-' . $mois, '/') . '-*', 'i');
				unset($arrIn['idvacance']);
			endif;
			if (!empty($arrIn['idtransport_gamme'])):
				$arrV                 = $base->transport_gamme->findOne(['idtransport_gamme' => (int)$arrIn['idtransport_gamme']]);
				$arrIn['idtransport'] = (int)$arrV['idtransport'];
				unset($arrIn['idtransport_gamme']);
			endif;
			if (!empty($arrIn['homePageProduit'])):
				$arrIn['homePageProduit'] = 1;
			endif;
			if (!empty($arrIn['coeurProduit'])):
				$arrIn['coeurProduit'] = 1;
			endif;
			if (!empty($arrIn['promoProduit'])):
				$arrIn['promoProduit'] = 1;
			endif;
			if (!empty($arrIn['volProduit'])):
				$arrIn['volProduit'] = 1;
			endif;
			if (!empty($arrIn['toutIncluProduit'])):
				$arrIn['toutIncluProduit'] = 1;
			endif;
			if (!empty($arrIn['idtheme'])):
				$arrIn['grilleThemeProduit.idtheme'] = (int)$arrIn['idtheme'];
				unset($arrIn['idtheme']);
			endif;
			if (!empty($arrIn['idproduit_selection'])):
				$arrIn['idproduit_selection'] = (int)$arrIn['idproduit_selection'];
			endif;
			if (!empty($arrIn['idhotel'])):
				$arrIn['grilleHotelProduit.idhotel'] = (int)$arrIn['idhotel'];
				unset($arrIn['idhotel']);
			endif;
			if (!empty($arrIn['idfleuve'])):
				$arrIn['grilleFleuveProduit.idfleuve'] = (int)$arrIn['idfleuve'];
				unset($arrIn['idfleuve']);
			endif;

			return $arrIn;
		}

		function titreVars($arrIn = []) {
			$APP = new App();
			// mongo field name
			$vars_out = $vars_titre = [];
			$arrIn    = fonctionsProduction::cleanPostMongo($arrIn, 1);
			$base     = $APP->plug_base('sitebase_production');

			if (!empty($arrIn['homePageProduit'])):
				$vars_titre[] = 'focus';
			endif;
			if (!empty($arrIn['coeurProduit'])):
				$vars_titre[] = 'coup de coeur';
			endif;
			if (!empty($arrIn['promoProduit'])):
				$vars_titre[] = 'promotions';
			endif;
			if (!empty($arrIn['idproduit_type'])) {
				$arr          = $base->produit_type->findOne(['idproduit_type' => (int)$arrIn['idproduit_type']]);
				$vars_titre[] = $arr['plurielProduit_type'];
			}
			if (!empty($arrIn['idfournisseur'])) {
				$arr          = $base->fournisseur->findOne(['idfournisseur' => (int)$arrIn['idfournisseur']]);
				$vars_titre[] = $arr['nomFournisseur'];
			}
			if (!empty($arrIn['idcontinent'])):
				$arr          = $base->continent->findOne(['idcontinent' => (int)$arrIn['idcontinent']]);
				$vars_titre[] = $arr['nomContinent'];
			endif;
			if (!empty($arrIn['iddestination'])):
				$arr          = $base->destination->findOne(['iddestination' => (int)$arrIn['iddestination']]);
				$vars_titre[] = ((sizeof($arrIn) == 1) ? 'Destination ' : $arr['prefixeDestination']) . ' ' . $arr['nomDestination'];
			endif;
			if (!empty($arrIn['idvilleDepartProduit'])) {
				$arr          = $base->ville->findOne(['idville' => (int)$arrIn['idvilleDepartProduit']]);
				$vars_titre[] = 'départs de ' . $arr['nomVille'];
			}
			if (!empty($arrIn['idpays'])) {
				$arr          = $base->pays->findOne(['idpays' => (int)$arrIn['idpays']]);
				$vars_titre[] = 'en ' . $arr['nomPays'];
			}
			if (!empty($arrIn['idpaysDepartProduit'])) {
				$arr          = $base->pays->findOne(['idpays' => (int)$arrIn['idpaysDepartProduit']]);
				$vars_titre[] = 'partant de ' . $arr['nomPays'];
			}
			if (!empty($arrIn['idville'])) {
				$arr          = $base->ville->findOne(['idville' => (int)$arrIn['idville']]);
				$vars_titre[] = 'escales ' . $arr['nomVille'];
			}
			if (!empty($arrIn['idtheme'])):
				$arr          = $base->theme->findOne(['idtheme' => (int)$arrIn['idtheme']]);
				$vars_titre[] = ((sizeof($arrIn) == 1) ? 'Thematique ' : '') . ' ' . $arr['nomTheme'];
			endif;
			if (!empty($arrIn['idtransport'])) {
				$arr          = $base->transport->findOne(['idtransport' => (int)$arrIn['idtransport']]);
				$vars_titre[] = $arr['nomTransport'];
			}
			if (!empty($arrIn['idfournisseur_type'])) {
				$arr          = $base->fournisseurtype->findOne(['idfournisseur_type' => (int)$arrIn['idfournisseur_type']]);
				$vars_titre[] = $arr['nomFournisseur_type'];
			}
			if (!empty($arrIn['dureeJourProduit'])) {
				$vars_titre[] = $arrIn['dureeJourProduit'] . ' jours';
			}
			if (!empty($arrIn['dateDebutProduit_tarif'])) {
				$mois         = substr($arrIn['dateDebutProduit_tarif'], 4, 2);
				$annee        = substr($arrIn['dateDebutProduit_tarif'], 0, 4);
				$mois_fr      = fonctionsSite::mois_fr($mois);
				$vars_titre[] = $mois_fr . ' ' . $annee;
			}
			if (!empty($arrIn['idvacance'])):
				$arr          = $APP->plug('sitebase_production', 'vacance')->findOne(['idvacance' => (int)$arrIn['idvacance']]);
				$vars_titre[] = $arr['nomVacance'];
			endif;
			if (!empty($arrIn['volProduit'])):
				$vars_titre[] = 'volinclu';
			endif;
			if (!empty($arrIn['toutIncluProduit'])):
				$vars_titre[] = 'toutinclu';
			endif;
			if (!empty($arrIn['idproduit_selection'])):
				$arr          = $base->produit_selection->findOne(['idproduit_selection' => (int)$arrIn['idproduit_selection']]);
				$vars_titre[] = $arr['nomProduit_selection'];
			endif;
			if (!empty($arrIn['idhotel'])):
				$arr          = $base->hotel->findOne(['idhotel' => (int)$arrIn['idhotel']]);
				$vars_titre[] = $arr['nomHotel'];
			endif;
			if (!empty($arrIn['idfleuve'])):
				$arr          = $base->fleuve->findOne(['idfleuve' => (int)$arrIn['idfleuve']]);
				$vars_titre[] = $arr['nomFleuve'];
			endif;

			$titre = implode(' ', $vars_titre);

			// if(empty($arrIn['idproduit_type']) && empty($arrIn['idfournisseur']) && empty($arrIn['idtransport'])) $titre = 'voyages '.$titre;
			return ucfirst(trim($titre));
		}

		function lienMobile($vars) {
			return Act::lienIntermediaire($vars, 'mobile', $pfxpage = 'mobile-');
		}

		function lienMobileTable($vars, $table) {
			return Act::lienIntermediaire($vars, 'mobile_table/' . $table, $pfxpage = 'mobile-');
		}

		function lienMobileTableListe($vars, $table) {
			return Act::lienIntermediaire($vars, 'mobile_table_liste/' . $table, $pfxpage = 'mobile-');
		}

		function lienMobileFiche($vars, $table) {
			return Act::lienIntermediaire($vars, 'mobile_fiche/' . $table, $pfxpage = 'mobile-');
		}

		function lienMobileDevis($vars) {
			return Act::lienIntermediaire($vars, 'mobile_devis', $pfxpage = 'mobile-');
		}

		function lienMobileProduit($vars) {
			return Act::lienIntermediaire($vars, 'mobile_produit', $pfxpage = 'mobile-');
		}

		function lienAll($vars) {
			return Act::lienIntermediaire($vars, 'lien_fiche');
		}

		function lienHowto($item, $vars = []) {
			$tr_vars = App::translate_vars($vars);
			$lk      = "index.php?mdl=page/item_howto&item=" . $item . (!empty($tr_vars) ? '&' . $tr_vars : '');
			$lk      = "espace_information/" . (!empty($tr_vars) ? $tr_vars . '/' : '') . $item . ".html";

			return HTTPCUSTOMERSITE . $lk;
		}

		function lienCart($vars) {
			return HTTPCUSTOMERSITE . 'lien_cart/' . $vars . '/liste-personnalisee.html';
		}

	}