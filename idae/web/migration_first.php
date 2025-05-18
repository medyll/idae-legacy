<?

include_once ('conf.inc.php');
$APP = new App();
set_time_limit(0);
ini_set('display_errors',55);

// 
$arr_tables = get_artis_table();
 //$arr_tables = ['t_volumemoyenmensuel'];
	//$arr_tables = ['t_relationclientfournisseur','t_categprofilclt'];
	$conn = &ADONewConnection("mysql");
$conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,'artisdb_leasys_prod');

foreach ($arr_tables as $key => $name_table) {
	
	$id = 'id' . $name_table;
	$app_cli = $APP -> plug('sitebase_sync' , $name_table  );
	$app_cli->createIndex(array('N_ID'=>1));
	$app_cli->createIndex(array('N_ID'=>-1));
	$app_cli->createIndex(array('T_UPDATE_DATE'=>1)); 
	$app_cli->createIndex(array('T_UPDATE_DATE'=>-1));
	$app_cli->createIndex(array('T_COMPUTED_UPDATE_TIME'=>1));
	$app_cli->createIndex(array('T_COMPUTED_UPDATE_TIME'=>-1));

	$sql = "SELECT *, DATE_FORMAT(T_UPDATE_DATE,'%d %b %Y %T:%f') as DADATE  FROM  ".$name_table." order by T_UPDATE_DATE desc";
	$res = $conn->Execute($sql) ;	
	
	if(!$res) continue;
 
	echo "\r\n";
	echo $name_table.' '.$res->recordCount();
		
	while ($ROW = $res -> fetchRow()) {
		foreach ($ROW as $kk => $vv):
			if ( is_numeric($kk) ) {
				unset($ROW[$kk]);
			}
		endforeach;

		array_walk_recursive($ROW,'encode_items');
		switch ($name_table):
			case "t_couvbienparserv":

				break;
			case "t_solservclt":
				$ROW['D_DATEDEFININITIALE_SOLSERVCLT'] = date('Y-m-d',strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']));
				$ROW['D_DATEDEDEBUT_SOLSERVCLT'] = date('Y-m-d',strtotime($ROW['D_DATEDEDEBUT_SOLSERVCLT']));
				break;
			case "t_volumemoyenmensuel":
				$ROW['D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL'] = date('Y-m-d',strtotime($ROW['D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL']));

				break;
			case "t_cptcpta":

				break;
		endswitch;
echo ' .' ;
		flush();
		ob_flush();
		// si N_ID
		if($ROW['N_ID']){
			if(!empty($ROW['T_UPDATE_DATE'])) $ROW['T_UPDATE_TIME'] = strtotime($ROW['T_UPDATE_DATE']);
			if(!empty($ROW['T_CREATE_DATE'])) $ROW['T_CREATE_DATE'] = date('Y-m-d',strtotime($ROW['T_CREATE_DATE']));
			if(!empty($ROW['T_COMPUTED_UPDATE_DATE'])) $ROW['T_COMPUTED_UPDATE_TIME'] = strtotime($ROW['T_COMPUTED_UPDATE_DATE']);
			$app_cli->update(array('N_ID'=>$ROW['N_ID']), array('$set'=> $ROW+ array('table'=>$name_table)), array('upsert'=>true));
		}  
	}
}


function encode_items(&$item, $key){
   $item = utf8_encode($item);
	$item = iconv('', 'UTF-8', $item);
}
 