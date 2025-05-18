<?
include_once($_SERVER['CONF_INC']);


	$MDL = empty($_POST['MDL']) ? 'xml_ftp' : $_POST['MDL'];

	$APP = new App('feed_header');
	//
	$col_price  = $APP->plug('sitebase_xml', 'xml_price');
	$col_cruise = $APP->plug('sitebase_xml', 'xml_cruise');
	//
	/* $col_price->drop();
	 $col_cruise->drop();*/
	//

	$rsApp = $APP->query(array('estActifFeed_header'=>1));

	while ($arrApp = $rsApp->getNext()):
		$idfournisseur    = (int)$arrApp['idfournisseur'];
		echo $CODE_FOURNISSEUR = $arrApp['codeFeed_header'];
		//
		// skelMdl::runModule('app/app_xml/'.$MDL,array('vars'=>array('idfournisseur'=>$idfournisseur)));
	endwhile;