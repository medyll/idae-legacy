<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	ignore_user_abort(true);
	set_time_limit(0);

	echo 	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	$four = empty($_GET['fourn']) ? $_POST['fourn'] : $_GET['fourn'];

	$vars = array('notify'=>'HTTP XML MSC ITI');
	skelMdl::reloadModule('activity/appActivity','*',$four);

	/*skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CATEGORY'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CATEGORY_CD'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CRUISE'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CRUISE-ID'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CRUISE_ID'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('ITIN-CD'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('SHIP'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('SHIP-CD'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('UNIQUE-ID'=>1));
	skelMongo::connect($four.'_csv','sitebase_csv')->ensureIndex(array('CRUISE_CODE'=>1));
	//
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('CATEGORY'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('CATEGORY_CD'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('CRUISE'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('CRUISE_ID'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('ITIN-CD'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('SHIP'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('SHIP-CD'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('UNIQUE-ID'=>1));
	skelMongo::connect($four.'_tmp','sitebase_csv')->ensureIndex(array('CRUISE_CODE'=>1));
	//
	skelMongo::connect($four.'_csv_iata','sitebase_csv')->ensureIndex(array('PORT_CD'=>1));
	skelMongo::connect($four.'_csv_iata','sitebase_csv')->ensureIndex(array('PORT_NAME'=>1));
	skelMongo::connect($four.'_csv_iata','sitebase_csv')->ensureIndex(array('codeVille'=>1));
	skelMongo::connect($four.'_csv_iata','sitebase_csv')->ensureIndex(array('idville'=>1));

	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('ARRIVAL_TIM'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('DATE-US'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('PORT_CD'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('RUN_DAT'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('SAIL_DAT'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('SAIL_ID'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('UNIQUE-ID'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('VOYAGE_CD'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('CRUISE'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('CRUISE_ID'=>1));
	skelMongo::connect($four.'_csv_iti','sitebase_csv')->ensureIndex(array('CRUISE_CODE'=>1));*/


?>
<div class="flowDown" style="overflow:auto;">
	<?
		// FTP
		$vars = array('count'=>0,'total'=>4);
		skelMdl::reloadModule('xml/xml_bar','*',$vars);
		echo $PATH.'ftp/'.$four;
		// echo skelMdl::cf_module($PATH.'ftp/'.$four,array('moduleTag'=>'none'));
		echo skelMdl::cf_module($PATH.'read/read'.$four,array('moduleTag'=>'none'));

		/*// READ
		$vars = array('count'=>1,'total'=>4);
		skelMdl::reloadModule('xml/xml_bar','*',$vars);
		echo skelMdl::cf_module('xml/read'.$four,array('moduleTag'=>'none'));

		// READ ITI
		$vars = array('count'=>2,'total'=>4);
		skelMdl::reloadModule('xml/xml_bar','*',$vars);
		echo skelMdl::cf_module('xml/read'.$four.'_iti',array('moduleTag'=>'none'));



		// PARSE
		$vars = array('count'=>3,'total'=>4);
		skelMdl::reloadModule('xml/xml_bar','*',$vars);
		echo skelMdl::cf_module('xml/xml'.$four,array('moduleTag'=>'none'));

		// END
		$vars = array('count'=>4,'total'=>4);
		skelMdl::reloadModule('xml/xml_bar','*',$vars);*/
		//
	?>
</div>