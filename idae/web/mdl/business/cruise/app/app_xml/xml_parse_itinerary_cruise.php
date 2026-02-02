<?

	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	$APP = new App('feed_header');

	ini_set('display_errors', 55);
	ini_set("default_socket_timeout", 3460);
	// on reÃ§oit :

	if (empty($_POST['vars']["idxml_cruise"]) || empty($_POST['vars']["CruiseLine"]) || empty($_POST['vars']['idproduit']) || empty($_POST['vars']['ShipCode']) || empty($_POST['vars']['PackageId'])) {
		echo " du vide ";

		return;
	}

	//
	/** @var  $CruiseLine */
	/** @var  $idproduit */
	/** @var  $ShipCode */
	/** @var  $PackageId */
	/** @var  $idxml_cruise */

	$CruiseLine   = $_POST['vars']["CruiseLine"];
	$ShipCode     = $_POST['vars']['ShipCode'];
	$PackageId    = $_POST['vars']['PackageId'];
	$idproduit    = (int)$_POST['vars']['idproduit'];
	$idxml_cruise = (int)$_POST['vars']['idxml_cruise'];

	/** @var  $idtransport */
	/** @var  $idfournisseur */

	//
	$PROGRESS_NAME = $CruiseLine;
	//
	$APP_XML_ITI         = $APP->plug('sitebase_xml', 'xml_itinerary');
	$APP_XML_CRUISE      = $APP->plug('sitebase_xml', 'xml_cruise');
	$APP_XML_PRICE       = $APP->plug('sitebase_xml', 'xml_price');
	$APP_XML_DESTINATION = $APP->plug('sitebase_xml', 'xml_destination');
	$APP_XML_VILLE       = $APP->plug('sitebase_xml', 'xml_ville');
	$col_debug_itinerary = $APP->plug('sitebase_xml', 'xml_itinerary_debug');
	$col_debug_error     = $APP->plug('sitebase_xml', 'xml_itinerary_debug_error');

	//
	$col_F       = $APP->plug('sitebase_production', 'fournisseur');
	$col_T       = $APP->plug('sitebase_production', 'transport');
	$col_etape   = $APP->plug('sitebase_production', 'produit_etape');
	$APP_VILLE   = new App('ville');
	$col_Pays    = $APP->plug('sitebase_production', 'pays');
	$APP_PRODUIT = new App('produit');

	/** @var  $arr_produit */
	$arr_produit = $APP_PRODUIT->findOne(array('idproduit' => $idproduit));
	$nomProduit  = $arr_produit['nomProduit'];
	//
	$arrApp = $APP->query_one(array('codeFeed_header' => $CruiseLine, 'estActifFeed_header' => 1)); // $query_vars

	if (empty($arrApp['idfournisseur'])) {
		return;
	}
	/** @var  $idfournisseur */
	$idfournisseur = (int)$arrApp['idfournisseur'];

	// echo "running ship " . $PackageId . '-' . $ShipCode . '-' . $CruiseLine;

	/** @var  $RS_ITI */
	$RS_ITI = $APP_XML_ITI->find(array('PackageId' => $PackageId, 'idxml_cruise' => $idxml_cruise))->sort(array('dateDebutXml_itinerary' => 1));

	$idxml_cruise . ' ' . $RS_ITI->count();

	$ARR_CRUISE = $APP_XML_CRUISE->findOne(array('idxml_cruise' => $idxml_cruise));
	//
	//
	$APP = new App();

	// skelMdl::send_cmd('act_notify', array('msg' => 'iti pour ok ' . $PROGRESS_NAME . ' ' . $ARR_CRUISE['DepartureDate']), session_id());

	/** @var  $first_DepartureDate */
	$first_DepartureDate = new DateTime($ARR_CRUISE['DepartureDate']);

	while ($iti = $RS_ITI->getNext()):
		//
		$a++;
		//
		$iti['idproduit']    = $idproduit;
		$iti['idxml_cruise'] = $idxml_cruise;
		$codeV              = empty($iti['PortCode'])? preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $iti['PortName']) : $iti['PortCode'] ;
		//
		$datetime2 = new DateTime($iti['DepartureDate']);
		$interval  = $first_DepartureDate->diff($datetime2);
		$ordre     = (int)$interval->days + 1;

		// skelMdl::send_cmd('act_notify', array('msg' => $iti['PortCode'].'             '.$codeV), session_id());

		// VILLE
		$test_XV = $APP_XML_VILLE->find(array('codeFournisseur' => $CruiseLine, 'codeXml_ville' => $codeV));
		if ($test_XV->count() == 0):

			if ($CruiseLine == 'MSC' && !empty($codeV)):
				$test_V = $APP_VILLE->findOne(array('codeVille' =>$codeV));
				if (!(empty($test_V['idville']))):
					$idville  = (int)$test_V['idville'];
					$nomVille = $test_V['nomVille'];
				else: // on cree la ville dans app
					$idville = (int)$APP->getNext('idville');
					// le pays, aprÃ©s la virgule
					$nomVille = $iti['PortName'];
					$arr_tmp  = explode(',', $nomVille);
					$nomVille = $arr_tmp[0];
					$nomPays  = $arr_tmp[1];
					//
					/*$test_Pays = $col_Pays->findOne(array( 'codePays' => $nomPays ));
					if ( $test_Pays['idpays'] ):

					endif;*/
					// $APP_VILLE->insert(array('idville' => $idville, 'nomVille' => $nomVille, 'codeVille' => $iti['PortCode']));
				endif;
			else:
				$test_V   = $APP_VILLE->findOne(array('codeVille' => $codeV));
				$idville  = (int)$test_V['idville'];
				$nomVille = $test_V['nomVille'];
				if(empty($idville)){
					$arr_c = explode(',',$iti['PortName']);
					$PortName = $arr_c[0];
					// skelMdl::send_cmd('act_notify', array('msg' => ' $PortName ' . $PortName . ' ' ), session_id());
					//$PortName = $iti['PortName'];
					$test_V   = $APP_VILLE->findOne(array('nomVille' =>  new MongoRegex("/^$PortName^/i")));
					$idville  = (int)$test_V['idville'];
					$nomVille = $test_V['nomVille'];
				}
			endif;
			if (!empty($codeV)):
				$id = (int)$APP->getNext('idxml_ville');
				$APP_XML_VILLE->insert(array('idxml_ville'     => $id,
				                             'idfournisseur'   => (int)$idfournisseur,
				                             'idville'         => (int)$idville,
				                             'codeFournisseur' => $CruiseLine,
				                             'nomVille'        => $nomVille,
				                             'codeXml_ville'   => $codeV,
				                             'nomXml_ville'    => $iti['PortName']));
			endif;
		else:
			$arr_idville = $test_XV->getNext();
			$idville     = (int)$arr_idville['idville'];
			$nomVille    = (empty($arr_idville['nomVille'])) ? strtolower($iti['PortName']) : $arr_idville['nomVille'];
			if (empty($arr_idville['codeVille'])) {
			 $APP_VILLE->update(['idville'=>$idville],['codeVille'=>$codeV]);
			}
		endif;

		/*$vars = array( 'progress_name'    => $PROGRESS_NAME ,
		               'progress_value'   => $ordre ,
		               'progress_max'     => sizeof($RS_ITI) ,
		               'progress_message' => 'iti ' . $CruiseLine . ' ' . $ShipCode );
		skelMdl::send_cmd('act_progress' , $vars , session_id());*/

		// EN MER
		if ($codeV == 'ASE'):
			$idville  = $nomVille = '';
			$nomEtape = strtolower($iti['PortName']);
		else :
			$nomEtape = $nomVille;
		endif;
		// Etape
		$test_etape = $col_etape->find(array('idproduit'          => $idproduit,
		                                     'idville'            => $idville,
		                                     'ordreProduit_etape' => $ordre));

		//
		if ($test_etape->count() == 0):
			//

			$arr_XV          = $APP_XML_VILLE->findOne(array('codeFournisseur' => $CruiseLine, 'codeXml_ville' => $codeV));
			$idproduit_etape = (int)$APP->getNext('idproduit_etape');
			$col_etape->insert(array('ordreProduit_etape'      => $ordre,
			                         'dateDebutProduit_etape'  => date_mysql($iti['DepartureDate']),
			                         'heureDebutProduit_etape' => $iti['ArrivalTime'],
			                         'heureFinProduit_etape'   => $iti['DepartureTime'],
			                         'idproduit_etape'         => $idproduit_etape,
			                         'idproduit'               => $idproduit,
			                         'codeXml_ville'           => $codeV,
			                         'idville'                 => $idville,
			                         'nomVille'                => ucfirst(strtolower($nomVille)),
			                         'nomProduit_etape'        => ucfirst(strtolower($nomEtape)),
			                         'nomXml_ville'            => $iti['PortName']));
		endif;
	endwhile;

	if (empty($arr_produit['m_mode'])):
		$nomProduit = fonctionsSite::buildTitreProduit($idproduit);
		//	$APP_PRODUIT->update(array( 'idproduit' => $idproduit ),array('$set'=>array('nomProduit'=>$nomProduit)));
		if (!empty($nomProduit)) {
			$APP_PRODUIT->update(array('idproduit' => $idproduit), array('nomProduit' => $nomProduit));
		} else {
			$APP_PRODUIT->update(array('idproduit' => $idproduit),array('nomProduit' => $ARR_CRUISE['nomXml_cruise']));
		}
	endif;
	$vars = array('progress_name' => $PROGRESS_NAME, 'progress_message' => $CruiseLine . ' ' . $ShipCode . ' ' . $PackageId . ' ' . 'Itinerary End ', 'progress_value' => 100, 'progress_max' => 100);
	skelMdl::send_cmd('act_progress', $vars, session_id());