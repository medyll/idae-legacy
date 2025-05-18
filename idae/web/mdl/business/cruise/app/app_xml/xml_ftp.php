<?
	include_once($_SERVER['CONF_INC']);

	$session_id = session_id();

	ini_set('display_errors', 55);
	ini_set("default_socket_timeout", 360);
	//
	$query_vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	$page = empty($_POST['page']) ? 0 : $_POST['page'];
	//
	$APP = new App('feed_header');
	$APP_XML_JOB = new App('xml_job');

	$col_price     = $APP->plug('sitebase_xml', 'xml_price');
	$col_cruise    = $APP->plug('sitebase_xml', 'xml_cruise');
	$col_itinerary = $APP->plug('sitebase_xml', 'xml_itinerary');

	$col_F     = $APP->plug('sitebase_production', 'fournisseur');
	$col_T     = $APP->plug('sitebase_production', 'transport');
	$col_TG    = $APP->plug('sitebase_production', 'transport_gamme');
	$col_Gamme = $APP->plug('sitebase_production', 'gamme');

	$col_debug       = $APP->plug('sitebase_xml', 'xml_cruise_debug');
	$col_debug_2     = $APP->plug('sitebase_xml', 'xml_price_debug_2');
	$col_debug_3     = $APP->plug('sitebase_xml', 'xml_price_debug_3');
	$col_debug_error = $APP->plug('sitebase_xml', 'xml_price_debug_error');
	//
	$col_XD = $APP->plug('sitebase_xml', 'xml_destination');
	$col_XV = $APP->plug('sitebase_xml', 'xml_ville');

	//
	$rsApp = $APP->query($query_vars + array('estActifFeed_header' => 1))->sort(['codeFeed_header'=>1]);
	//
	$PROG_MSG = '';
	$zi       = 0;
	while ($arrApp = $rsApp->getNext()):
		$zi++;
		$PROG_MSG         = 'Feed ' . $arrApp['nomFeed_header'];
		$CODE_FOURNISSEUR = $arrApp['codeFeed_header'];

		$idfournisseur = (int)$arrApp['idfournisseur'];
		$header        = $arrApp['descriptionFeed_header'];
		$CRUISELINE    = $arrApp['codeFeed_header'];
		$WSDL          = $arrApp['urlFeed_header'] . '?WSDL';

		$arr_F          = $col_F->findOne(array('idfournisseur' => $idfournisseur));
		$nomFournisseur = $arr_F['nomFournisseur'];
		$rs_XD          = $col_XD->find(array('idfournisseur' => $idfournisseur));


		// pour chaque destination de fournisseur GeoCode
		$i = 0;
		while ($arr_XD = $rs_XD->getNext()):
			//

			//
			$i++;
			$XD            = $arr_XD['codeXml_destination'];
			$PROGRESS_NAME = str_replace(' ', '', $CRUISELINE . $arr_XD['codeXml_destination']);
			$PROGRESS_NAME = str_replace('-', '_', $PROGRESS_NAME);
			//
			/** parametres lancement xml ftp in
			 *  GeoCode
			 * CruiseLine */
			skelMdl::runModule('business/'.BUSINESS.'/app/app_xml/xml_ftp_in', array( 'session_id'    => $session_id,
				                                                                          'PROGRESS_NAME' => $PROGRESS_NAME,
				                                                                          'vars' => array('GeoCode'    => $XD,
				                                                                                          'CruiseLine' => $CODE_FOURNISSEUR)));
			//
		endwhile;
		sleep(10);
	endwhile; // fin fournisseur header

