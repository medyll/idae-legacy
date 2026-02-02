<?
	require_once __DIR__ . '/../appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;

	class fonctionsSite {
		function fonctionsSite () {

		}

		function testCache ($id) {
			echo __FUNCTION__;
		}

		function meta_title ($GET) {
			switch ($GET['mdl']):
				/*case "page/intermediaire":
					if ( ! empty($GET['vars']) ):
						if ( is_string($GET['vars']) ):
							$vars = Act::decodeUri($GET['vars']);
						endif;
					endif;

					break;*/
				case 'page/item_howto':
					switch ($GET['item']):
						case 'fournisseur':
							$titre = 'Nos armateurs ';
							break;
						case 'promotion':
							$titre = 'Promotions ';
							break;
						case 'contact':
							$titre = 'Contact croisières MAW ';
							break;
						case 'assurance':
							$titre = 'Assurances croisières ';
							break;
						case 'groupe':
							$titre = 'Croisieres spéciales Groupes MAW ';
							break;
						case 'luxe':
							$titre = 'Croisieres Luxe MAW ';
							break;
						case 'reserver':
							$titre = 'Réservation de croisière MAW ';
							break;
					endswitch;
					break;
				case 'page/item_list':
					$titre = 'Liste ';
					switch ($GET['item']):
						case 'fournisseur':
							$titre = 'Nos armateurs Croisière';
							break;
						case 'promotion':
							$titre = 'Promotions ';
							break;
						case 'destination':
							$titre = 'Top destinations de croisières';
							break;
					endswitch;
					break;
				case 'page/item_fiche':
					$titre = 'Information ';
					break;
				case 'page/product_list':
					$titre = 'Catalogue ';
					break;
			case 'page/product':
				if(!empty($GET['idproduit'])):
					              $APP = new App('produit');
				$arrP = $APP->query_one(['idproduit'=>(int)$GET['idproduit']]) ;
					$titre=$arrP['nomFournisseur'].' '.$arrP['nomTransport'].' '.$arrP['nomProduit'];
					endif;

				break;
				default:
					$titre = 'Croisieres maw, Agence de croisieres personnalisées';
					break;
			endswitch;

			//
			if ( ! empty($GET['vars']) ):
				if ( is_string($GET['vars']) ):
					$vars = Act::decodeUri($GET['vars']);
				endif;
			endif;
			//
			if ( sizeof($vars) != 0 ) :
				return $titre . ' ' . Act::titreVars($vars);
			else:
				return $titre;
			endif;
			//
			$table = empty($vars['item']) ? 'produit_type' : $vars['item'];
			$Table = ucfirst($table);
			$APP   = new App($table);


			$arr = $APP->query_one(array( 'id' . $table => (int)$vars['id' . $table] ));

			return http_build_query($vars);

			return ucfirst($arr['nom' . $Table . '_type']) . ' ' . $arr['nom' . $Table];
		}

		function itineraireProduit ($id) {
			$APP = new App();

			$arrSort       = array();
			$arrListeEtape = array();

			$rs_iti = $APP->plug('sitebase_production' , 'produit_etape')->find(array( 'idproduit' => (int)$id ))->sort(array( 'ordreProduit_etape' => 1 ));
			// echo $rs_iti->count();
			while ($arrVille = $rs_iti->getNext()) {
				$arrSort[(int)$arrVille['ordreProduit_etape']] = $arrVille;
				if ( ! empty($arrVille['nomProduit_etape']) && $arrVille['nomProduit_etape'] != $arrVille['nomVille'] ) {
					$arrVille['nomVille'] = empty($arrVille['nomVille']) ? '' : ' (' . $arrVille['nomVille'] . ')';
					$arrListeEtape[]      = '&nbsp;' . $arrVille['nomProduit_etape'] . $arrVille['nomVille'];
				} else {
					$arrListeEtape[] = '&nbsp;' . $arrVille['nomVille'];
				}
			}
			ksort($arrSort);
			ksort($arrListeEtape);
			$i     = 0;
			$count = 0;
			foreach ($arrListeEtape as $key => $arr) {
				$i ++;
				if ( ! empty($arrListeEtape[$key + 1]) ) {
					if ( $arrListeEtape[$key + 1] == $arrListeEtape[$key] ) {
						//  echo 'same:';
						unset($arrListeEtape[$key]);
						$count ++;
					} else {
						if ( $count != 0 ) {
							$count ++;
							if ( ! empty($arrSort[$key]['nomVille']) ) {
								$arrListeEtape[$key] = '&nbsp;' . $count . ' jours à&nbsp;' . $arrSort[$key]['nomVille'];
							} else {
								if ( ! empty($arrSort[$key]['nomProduit_etape']) ) {
									$arrListeEtape[$key] = $arrSort[$key]['nomProduit_etape'] . '&nbsp;(' . $count . ' jours)&nbsp;';
								}
							}
						}
						$count = 0;
					}
				}
			}

			return $listeEtape = fonctionsProduction::andLast($arrListeEtape);
		}

		function buildTitreProduit ($id) {
			$APP   = new App('produit');
			$APP_ETAPE   = new App('produit_etape');
			$APP_V = new App('ville');
			$APP_P = new App('pays');

			$arrSort       = array();
			$arrListeEtape = array();

			$arrP = $APP->query_one(array( 'idproduit' => (int)$id ));
			if ( $arrP['dureeProduit'] < 21 ):
				$rs_iti = $APP_ETAPE->find(array( 'idproduit' => (int)$id ))->sort(array( 'ordreProduit_etape' => 1 ));
				// echo $rs_iti->count();
				if($rs_iti->count()==0) return '';
				$arrListeVille = [];
				while ($arrVille = $rs_iti->getNext()) {
				//	vardump($arrVille);
					$arrSort[(int)$arrVille['ordreProduit_etape']] = $arrVille;
					if ( empty($arrVille['idville']) && !empty($arrVille['nomVille']) ) {
						$nomVille = $arrVille['nomVille'];
						$testV = $APP_V->findOne(['nomVille' => MongoCompat::toRegex('^' . preg_quote($nomVille, '/') . '^', 'i')]);
						if(!empty($testV['idville'])){
							$arrListeVille[] = ucfirst(strtolower($testV['nomVille'])) . '&nbsp;';
							$arrListeEtape[] = ucfirst(strtolower($testV['nomPays'])) . '&nbsp;';
						}
					} elseif( empty($arrVille['idville']) &&  empty($arrVille['nomVille']) ){
						if(strtolower($arrVille['nomProduit_etape'])!='en mer'):
							// $arrListeVille[] = ucfirst(strtolower($arrVille['nomProduit_etape'])) . '&nbsp;';
						endif;
					}else {
						$arrV  = $APP_V->query_one(array( 'idville' => (int)$arrVille['idville'] ));
						$arr_p = $APP_P->query_one(array( 'idpays' => (int)$arrV['idpays'] ));
						if ( ! empty($arr_p['nomPays']) ):
							$arrListeVille[] = ucfirst(strtolower($arrV['nomVille'])) . '&nbsp;';
							$arrListeEtape[] = ucfirst(strtolower($arr_p['nomPays'])) . '&nbsp;';
						endif;
					}
				}
				$testV = array_count_values($arrListeVille);
				$test  = array_count_values($arrListeEtape);

				if ( sizeof($test) > 1 ):
					$add = '';
					foreach ($test as $key => $value) {
						$tmplast[] = $key . ' ' . (($value == 1) ? '' : '');
					}
					if ( sizeof($test) < 4 && !empty($arrP['idville']) ):
						$arrVd  = $APP_V->query_one(array( 'idville' => (int)$arrP['idville'] ));
						$arrVa  = $APP_V->query_one(array( 'idville' => (int)$arrP['idvilleArrivee'] ));
						$add = ', de  '.ucfirst(strtolower($arrVd['nomVille'])) . ' à ' . ucfirst(strtolower($arrVa['nomVille'])) ;
					endif;

					return fonctionsProduction::andLast($tmplast , ',').$add;
				else :
					foreach ($testV as $key => $value) {
						$tmplast[] = $key . ' ' . (($value == 1) ? '' : '');
					}

					return fonctionsProduction::andLast($tmplast , '');
				endif;

			else:
				return ucfirst(strtolower($arrP['nomVille'])) . ' ' . ucfirst(strtolower($arrP['nomVilleArrivee'])) . ' en ' . $arrP['dureeProduit'] . ' jours';
			endif;
		}

		function itinerairePaysProduit ($id) {
			$APP   = new App();
			$APP_V = new App('ville');
			$APP_P = new App('pays');

			$arrSort       = array();
			$arrListeEtape = array();


			$rs_iti = $APP->plug('sitebase_production' , 'produit_etape')->find(array( 'idproduit' => (int)$id ))->sort(array( 'ordreProduit_etape' => 1 ));

			while ($arrVille = $rs_iti->getNext()) {
				$arrSort[(int)$arrVille['ordreProduit_etape']] = $arrVille;
				if ( ! empty($arrVille['nomProduit_etape']) && empty($arrVille['idville']) ) {
					// $arrVille['nomPays'] = empty($arrVille['nomPays'])? '':' ('.$arrVille['nomPays'].')';
					// $arrListeEtape[(int)$arrVille['ordreProduit_etape']] =  $arrVille['nomProduit_etape'].$arrVille['nomPays'];
				} else {
					$arrV                                                = $APP_V->query_one(array( 'idville' => (int)$arrVille['idville'] ));
					$arrListeEtape[(int)$arrVille['ordreProduit_etape']] = $arrV['nomPays'] . '&nbsp;';
				}
			}
			ksort($arrSort);
			ksort($arrListeEtape);
			$test = array_count_values($arrListeEtape);
			foreach ($test as $key => $value) {
				$tmplast[] = $key . ' ' . (($value == 1) ? '' : $value . ' jours ');
			}

			return fonctionsProduction::andLast($tmplast);
		}

		function imageProduit ($idproduit , $size = 'small') {
			$APP = new App();

			$arr   = $APP->plug('sitebase_production' , 'produit')->findOne(array( 'idproduit' => (int)$idproduit ));
			$grid  = $APP->plug_fs('sitebase_image');
			$image = $grid->findOne('produit-' . $size . '-' . $idproduit);
			if ( ! empty($image) && ! empty($image->file['chunkSize']) ) {
				return HTTPMEDIA . 'mediatheque-produit-' . $size . '-' . $idproduit . '.jpg';
			}
			if ( ! empty($arr['idtransport']) ) {
				return Act::imgApp('transport' , $arr['idtransport'] , $size);
			}
			if ( ! empty($arr['grilleHotelProduit']) ) {
				sort($arr['grilleHotelProduit']);

				return Act::imgApp('hotel' , $arr['grilleHotelProduit'][0]['idhotel'] , $size);
			}
			if ( ! empty($arr['idvilleDepartProduit']) ) {
				return Act::imgApp('ville' , $arr['idvilleDepartProduit'] , $size);
			}

			return Act::imgApp('fournisseur' , $arr['idfournisseur'] , $size);
		}


		static function mois_fr ($num) {
			$tabmonth = array( 1 => "Janvier" , "Février" , "Mars" , "Avril" , "Mai" , "Juin" , "Juillet" , "Août" , "Septembre" , "Octobre" , "Novembre" , "Décembre" );

			return $tabmonth[(int)$num];
		}

		function date_fr ($date) {
			$arrDate  = explode('-' , $date);
			$tabmonth = array( 1 => "Janvier" , "Février" , "Mars" , "Avril" , "Mai" , "Juin" , "Juillet" , "Août" , "Septembre" , "Octobre" , "Novembre" , "Décembre" );

			return $arrDate[2] . ' ' . $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}

		function moisDate_fr ($date) {
			$arrDate  = explode('-' , $date);
			$tabmonth = array( 1 => "Janvier" , "Février" , "Mars" , "Avril" , "Mai" , "Juin" , "Juillet" , "Août" , "Septembre" , "Octobre" , "Novembre" , "Décembre" );

			return $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}

		function jourMoisDate_fr ($date) {
			$arrDate  = explode('-' , $date);
			$tabmonth = array( 1 => "Janvier" , "Février" , "Mars" , "Avril" , "Mai" , "Juin" , "Juillet" , "Août" , "Septembre" , "Octobre" , "Novembre" , "Décembre" );

			return $arrDate[2] . ' ' . $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}

		function lowImage ($name) {
			$im = new Imagick($name);
			$im->setImageCompression(imagick::COMPRESSION_JPEG);
			$im->setImageCompressionQuality(50);
			$im->thumbnailImage($im->getImageWidth() , null);
			$bytesOut = $im->getimageblob();

			return $bytesOut;
		}

		function imageBytesAnnotate ($im , $vars = array( 'fillColor' => 'ffffff88' , 'opacity' => 60 )) {
			$draw = new ImagickDraw();
			$draw->setFillColor('#ffffff88');
			$draw->setFontSize(10);
			//$im->annotateImage($draw , 40 , $height - 10 , 0 , "croisieres-maw.com");

			$draw->setFillColor('#00000088');
			$draw->setFontSize(10);
			//$im->annotateImage($draw , 40 , $height - 9 , 0 , "croisieres-maw.com");

			return $im;
		}

		function imageBytesResize ($bytes , $vars = array( 'width' => 120 , 'height' => 60 )) {
			if ( empty($bytes) ) {
				return;
			}
			$im = new Imagick();
			$im->readImageBlob($bytes);
			$im->setImageCompression(imagick::COMPRESSION_JPEG);
			$im->setImageCompressionQuality(80);
			$geo    = $im->getImageGeometry();
			$width  = $vars['width'];
			$height = $vars['height'];
			if ( ($geo['width'] / $width) < ($geo['height'] / $height) ) {
				$im->cropImage($geo['width'] , floor($height * $geo['width'] / $width) , 0 , (($geo['height'] - ($height * $geo['width'] / $width)) / 2));
			} else {
				$im->cropImage(ceil($width * $geo['height'] / $height) , $geo['height'] , (($geo['width'] - ($width * $geo['height'] / $height)) / 2) , 0);
			}
			//$im->charcoalImage (2,1);
			$im->thumbnailImage($width , $height , true);

			$draw = new ImagickDraw();
			$draw->setFillColor('#ffffff88');
			$draw->setFontSize(10);
			//$im->annotateImage($draw , 10 , $height - 10 , 0 , "croisieres-maw.com");

			$bytesOut = $im->getimageblob();

			return $bytesOut;
		}

		static function gridImage ($id , $col = 'appimg' , $base = 'sitebase_base' , $width = 120 , $height = 60) {
			$APP   = new App();
			$grid  = empty($col) ? $APP->plug_base($base)->getGridFs() : $APP->plug_base($base)->getGridFs($col);
			$data  = $grid->findOne(array( '_id' => MongoCompat::toObjectId($id) ));
			$im    = new Imagick();
			$bytes = $data->getBytes();
			$im->readImageBlob($bytes);
			$im->setImageCompression(imagick::COMPRESSION_JPEG);
			$im->setImageCompressionQuality(80);
			$im->thumbnailImage($width , $height , true);
			$bytesOut = $im->getimageblob();

			return $bytesOut;
		}

		static function cropImage ($id , $col = 'fs' , $base = 'sitebase_image' , $vars = array()) {
			$APP  = new App();
			$grid = empty($col) ? $APP->plug_base($base)->getGridFs() : $APP->plug_base($base)->getGridFs($col);
			$data = $grid->findOne(array( '_id' => MongoCompat::toObjectId($id) ));

			$im    = new Imagick();
			$bytes = $data->getBytes();
			$im->readImageBlob($bytes);
			$im->setImagePage(0 , 0 , 0 , 0);

			$geo = $im->getImageGeometry();
			// <=> // taille ecran =>
			$im->thumbnailImage($vars['display_width'] , $vars['display_height'] , true);
			// on crop now
			$im->cropImage($vars['width'] , $vars['height'] , $vars['x1'] , $vars['y1']);
			$im->thumbnailImage($vars['final_width'] , $vars['final_height'] , true);
			// taiile finale demandé (ratio )
			/*		$im->cropImage($width,$height,$x,$y);
					$im->thumbnailImage($fw,$fh,true);*/
			//
			$im->adaptiveSharpenImage(2 , 1);
			//
			$bytesOut = $im->getimageblob();

			return $bytesOut;
		}

		function thumbImage ($name , $width = 120 , $height = null) {
			$im = new Imagick();
			$im->readImageBlob($name);
			$im->setImageCompression(imagick::COMPRESSION_JPEG);
			$im->setImageCompressionQuality(90);
			$im->thumbnailImage($width , $height , true);
			$bytesOut = $im->getimageblob();

			return $bytesOut;
		}

		function reflectImage ($name) {
			$outname = str_replace('.jpg' , '_reflect.png' , $name);
			/* Lecture de l'image */
			clearstatcache();
			//if(!file_exists($name)) return false;
			$AgetHeaders = @get_headers($name);
			if ( preg_match("|200|" , $AgetHeaders[0]) ) {
				// file exists
			} else {
				// file doesn't exists
				return false;
			}
			$im = new Imagick($name);
			// profile icc
			//$icc_rgb = file_get_contents(PATHICC.'ISOcoated_v2_eci.icc');
			//$im->profileImage('icc', $icc_rgb);
			//$im->setImageColorSpace(Imagick::COLORSPACE_RGB);
			//
			/* Thumbnail the image */
			$im->thumbnailImage($im->getImageWidth() , null);
			$reflection = $im->clone();
			$reflection->flipImage();
			$gradient = new Imagick();
			$gradient->newPseudoImage($reflection->getImageWidth() , $reflection->getImageHeight() * 0.5 , "gradient:transparent-black");
			$reflection->compositeImage($gradient , imagick::COMPOSITE_DSTOUT , 0 , 0);
			$gradient->newPseudoImage($reflection->getImageWidth() , $reflection->getImageHeight() * 0.5 , "gradient:black");
			$reflection->compositeImage($gradient , imagick::COMPOSITE_DSTOUT , 0 , $reflection->getImageHeight() * 0.5);
			$opacity = new Imagick();
			$opacity->newImage($reflection->getImageWidth() , $reflection->getImageHeight() , new ImagickPixel('black'));
			$opacity->setImageOpacity(0.4);
			$reflection->compositeImage($opacity , imagick::COMPOSITE_DSTOUT , 0 , 0);

			$canvas = new Imagick();

			$width  = $im->getImageWidth();
			$height = ($im->getImageHeight() * 1.5);

			$canvas->newImage($width , $height , 'none' , "png");
			$canvas->compositeImage($im , imagick::COMPOSITE_SRCOVER , 0 , 0);
			$canvas->compositeImage($reflection , imagick::COMPOSITE_SRCOVER , 0 , $im->getImageHeight());
			$bytesOut = $canvas->getimageblob();
			//$canvas->writeImage(PATHTMP.'destination.jpg');
			//echo $outname;
			return $bytesOut; //$bytesOut;
		}

		function makeThumb ($file , $idd , $width = 250 , $height = 120 , $sizeName , $tag , $nameSizeFrom = 'large') {
			fonctionsSite::makeGdThumb($file , $width , $height , $sizeName , $tag , $nameSizeFrom);

			return;
			$db    = skelMongo::connectBase('sitebase_image'); //$con->sitebase_image;
			$grid  = $db->getGridFS();
			$thumb = str_replace($nameSizeFrom , $sizeName , $file);
			$test  = $grid->findOne($file);
			if ( ! empty($test) && ! empty($test->file['chunkSize']) ) {
				$image = $test->getBytes();
				$image = imagecreatefromstring($image);
				$grid->remove(array( 'filename' => $thumb ));
				//
				$im = new Imagick();
				//echo $im->identifyImage($image);
				//var_dump($im->getImageType($file)); exit;
				//
				//$im->readimageblob($image);
				$geo = $im->getImageGeometry();
				if ( ($geo['width'] / $width) < ($geo['height'] / $height) ) {
					$im->cropImage($geo['width'] , floor($height * $geo['width'] / $width) , 0 , (($geo['height'] - ($height * $geo['width'] / $width)) / 2));
				} else {
					$im->cropImage(ceil($width * $geo['height'] / $height) , $geo['height'] , (($geo['width'] - ($width * $geo['height'] / $height)) / 2) , 0);
				}
				//$im->charcoalImage (2,1);
				$im->thumbnailImage($width , $height , true);
				$bytesOut = $im->getimageblob();
				$myMeta   = array( 'filename' => $thumb ,
				                   'metadata' => array( 'width'       => $width ,
				                                        'heigh'       => $height ,
				                                        'tag'         => $tag ,
				                                        'name'        => $thumb ,
				                                        'size'        => $sizeName ,
				                                        'mysqlid'     => $idd ,
				                                        'contentType' => 'image/jpeg' ) );
				$grid->storeBytes($bytesOut , $myMeta);
				// echo "done ".$thumb;
			}
		}
		//
		//
		// $file,$idd,$width=250,$height=120,$sizeName,$tag,$nameSizeFrom='large'
		static function makeGdThumb ($file , $thumb_width = 250 , $thumb_height = 120 , $sizeName , $tag , $nameSizeFrom = 'large',$metadata=[]) {
			//
			$APP = new App();
			ob_start();
			$time = time();
			//
			$db      = $APP->plug_base('sitebase_image'); //$con->sitebase_image;
			$grid    = $db->getGridFS();
			$thumb   = str_replace($nameSizeFrom , $sizeName , $file);
			$jpgName = str_replace('.jpg' , '' , $thumb) . '.jpg';
			$test    = $grid->findOne($file);
			if ( ! empty($test) && ! empty($test->file['chunkSize']) ):
				$image      = $test->getBytes();
				$nametmp    = PATHTMP . 'GDTMP/' . $time . '-' . $file . '-' . $sizeName ;
				$nameNewtmp = PATHTMP . 'GDTMP/' . $thumb ;
				file_put_contents($nametmp , $image);
				$thumbNail = PhpThumbFactory::create($nametmp);
				$thumbNail->adaptiveResize($thumb_width , $thumb_height)->save($nameNewtmp , 'jpg');
				$bytesOut = $thumbNail->getImageAsString();
				$grid->remove(array( 'filename' => $thumb ));

				$finalOut = file_get_contents($nameNewtmp);
				$myMeta   = array( 'filename'   => $thumb ,
				                   'uploadDate' => MongoCompat::toDate(time()) ,
				                   'metadata'   => array_merge(array( 'time' => time() ,
				                                                      'date'     => date('Y-m-d') ,
				                                                      'heure'     => date('H:i:s') ,
				                                                      'width'     => $thumb_width ,
				                                                      'heigh'     => $thumb_height ,
				                                                      'tag'       => $tag ,
				                                                      'table'       => $tag ,
				                                                      'mongoTag'       => $tag ,
				                                                      'filename'  => $thumb ,
				                                                      'real_filename'  => $thumb ,
				                                                      'size'      => $sizeName   ),$metadata) );
				$grid->storeBytes($finalOut , $myMeta , array( 'safe' => true ));
				unlink($nametmp);
			endif;
			ob_end_clean();
			return $thumb;
		}
	}

?>