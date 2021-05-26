<?

include_once($_SERVER['CONF_INC']);


$APP = new App();

ini_set('display_errors',55);

$time = time();



?>
<?
if(empty($_POST['run'])):
	?>
	<div style="width:950px;">
		<table class="tabletop">
			<tr>
				<td style="width:90px;text-align:center"><br>
					<i class="fa fa-database fa-2x"></i></td>
				<td class="texterouge"><br>
					APP migration 1<br>
				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_first_job"></progress><progress value="0" id="auto_first_injob"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('app/app_admin/app_migration_first','run=1')"  >
			<input type="button" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
	</div>
	<?
	return;
endif;

$time = time();
// 
$arr_tables = get_artis_table();

$arr_tables = ['t_lgnfact'];  // ,'t_dmdeinter' , 't_inter','t_interinfoprincipales','t_interplanifiee'
// t_dmdeinter t_inter t_interinfoprincipales t_interplanifiee
//
$conn = &ADONewConnection("mysql");
$conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,'artisdb_leasys_prod');
//
foreach ($arr_tables as $key => $name_table) {
	//
	do_artis_index($name_table,'N_ID');
	do_artis_index($name_table,'T_UPDATE_DATE');

	$WHERE = '';
	switch($name_table):
		case "t_couvbienparserv":
			$WHERE = " where B_ACTIF_COUVBIENPARSERV_IDX <> 'Non' ";
			break;
		case "t_solservclt":
			$WHERE = " where DATE(D_DATEDEFININITIALE_SOLSERVCLT) > DATE(NOW()) ";
			break;
		case "t_cptcpta":

			break;
	endswitch;

	$i++;
	$id = 'id' . $name_table;
	$app_cli = $APP -> plug('sitebase_sync' , $name_table  );
	
	$sql = "SELECT *, DATE_FORMAT(T_UPDATE_DATE,'%d %b %Y %T:%f') as DADATE  FROM  ".$name_table." $WHERE order by T_COMPUTED_UPDATE_DATE desc ,T_UPDATE_DATE desc limit 0,180000";
	$res = $conn->Execute($sql) ;

	if(!$res) continue;
	//
	skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'first_job' ,
		'progress_value'   => $i ,
		'progress_max'     => sizeof($arr_tables) ,
		'progress_message' => 'Traitement '.$name_table.' en cours' ) , session_id());

	$z=0;
	while($ROW = $res->fetchRow()){
		foreach($ROW as $key_r => $value_r){
			if(is_numeric($key_r)) unset($ROW[$key_r]);
		}
		//
		$z++;
		// exceptions
		switch($name_table):
			case "t_couvbienparserv":
				if($ROW['B_ACTIF_COUVBIENPARSERV_IDX']=='Non') continue;
			break;
			case "t_solservclt":
				if(strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']) < $time) continue;
			break;
			case "t_cptcpta":
				if(empty($ROW['C_CODEENCOMPTABILITE_CPTCPTA']) ) continue;
			break;
		endswitch;
		// $ROW  = fonctionsProduction::cleanPostMongo($ROW,false);
		array_walk_recursive($ROW,'encode_items');
		// si N_ID
		if($ROW['N_ID']){
			// $app_cli->update(array('N_ID'=>$ROW['N_ID']), array('$set'=> $ROW + array('table'=>$name_table, 'date'=>date('d/M/Y'),'time'=>date('H:i:s'))), array('upsert'=>true));
		}
		//
		if($z % 200 == 0 || $z == $res->recordCount() ){
			skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'first_injob' ,
				'progress_value'   => $z ,
				'progress_max'     => $res->recordCount()  ,
				'progress_message' => 'en cours '.$z ) );// , session_id()
		}
		// pour do_artis_rows
		do_artis_rows($name_table,$ROW);
	  	// skelMdl::runModule('app/app_admin/app_migration_first_wrk' , array( 'vars' =>['ROW'=>$ROW,'name_table'=>$name_table] ));

	}
}
	$conn->Close();

	skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'first_injob' ,
	                                          'progress_value'   => 100 ,
	                                          'progress_max'     => 100  ,
	                                          'progress_message' => 'termine 100%' ) , session_id());
//
skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'first_job' ,
	'progress_value'   => 100 ,
	'progress_max'     => 100 ,
	'progress_message' => 'Traitemen termine' ) , session_id());

function encode_items(&$item, $key){
    $item = utf8_encode($item);
}
?>