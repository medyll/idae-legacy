<?

include_once($_SERVER['CONF_INC']);


include_once ('conf.inc.php');
$APP = new App();

set_time_limit(0);

ini_set('max_execution_time',0);
ini_set('display_errors',55);


$time = time();
$vars = array( 'notify' => 'Début mise en production' );


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
					Resynchro Mysql /mongodb <br>
				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_mysql_synchr_job"></progress><progress value="0" id="auto_mysql_firstinjob"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_mysql').loadModule('customer/<?=CUSTOMERNAME?>/app_admin/app_migration_mysql','run=1')"  >
			<input type="button" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_mysql" scrolling="auto"></div>
	</div>
	<?
	return;
endif;

// 
	$arr_tables = get_artis_table();
	rsort($arr_tables);
// $arr_tables = ['t_cptcpta'];
//	$arr_tables = ['t_servclt'];
// $arr_tables = ['t_art'];
//  $arr_tables = ['t_statutmoduleservclt'];
//$arr_tables = ['t_reglefactuclt'];

 // $arr_tables = ['t_lieubien'];

$conn = &ADONewConnection("mysql");  
$conn->Connect(SQL_HOST,SQL_USER,SQL_PASSWORD,'artisdb_leasys_prod');


/*$sql = "select * from mysql.information_schema";

$res = $conn->Execute($sql) ;*/


foreach ($arr_tables as $key => $name_table) {
	$i++;
	$id = 'id' . $name_table;
	$app_cli = $APP -> plug('sitebase_sync' , $name_table  );
	$app_cli->createIndex(array('N_ID'=>1));
	$app_cli->createIndex(array('N_ID'=>-1));
	$app_cli->createIndex(array('T_CREATE_DATE'=>1));
	$app_cli->createIndex(array('T_CREATE_DATE'=>-1));
	$app_cli->createIndex(array('T_UPDATE_DATE'=>1));
	$app_cli->createIndex(array('T_UPDATE_DATE'=>-1));

	$count = $app_cli->find()->count(true);
	// D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL

	$limit = 40000;
	$sort = 'T_UPDATE_DATE desc, T_COMPUTED_UPDATE_DATE desc';
	$where = 1;
	switch ($name_table):
		case "t_volumemoyenmensuel":
			$where = "D_DATEDEBUTVAL_VMMGRANDEUR_VOLUMEMOYENMENSUEL > '2016-12-07' ";
			$limit = 20000;
		break;
		case "t_valori":
			$where = "T_RELEVEELE_VALORI > '2016-12-01 00:00:00' ";
			$limit = 30000;
		break;
	endswitch;

	$sql = "SELECT *, DATE_FORMAT(T_UPDATE_DATE,'%d %b %Y %T:%f') as DADATE  FROM  ".$name_table." where $where order by $sort limit 0,$limit";
	$res = $conn->Execute($sql) ;
	
	if(!$res) continue;

	//
	skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'mysql_synchr_job' ,
		'progress_value'   => $i ,
		'progress_max'     => sizeof($arr_tables) ,
		'progress_log'     => $name_table.' '.$res->recordCount().' rows' ,
		'progress_message' => 'Traitement '.$name_table.' en cours' ) , session_id());
	$z=0;

	while($ROW = $res->fetchRow()){
		$z++;
		// $ROW  = fonctionsProduction::cleanPostMongo($ROW,false);
		foreach ($ROW as $key => $column) {
			if ((is_numeric($key))) {
				unset($ROW[$key]);
			}
		}
		array_walk_recursive($ROW,'encode_items');
// exceptions
		switch ($name_table):
			case "t_couvbienparserv":
				// if ($ROW['B_ACTIF_COUVBIENPARSERV_IDX'] == 'Non') continue;
				break;
			case "t_financement":
				$ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT'] = date('Y-m-d', strtotime($ROW['D_DATEDEFINDELALOCATIONHORSPAIEMENTVR_FINANCEMENT']));
				break;
			case "t_cmde":
				$ROW['D_DATEDECOMMANDE_CMDE'] = date('Y-m-d H:i:s', strtotime($ROW['D_DATEDECOMMANDE_CMDE']));
				break;
			case "t_interplanifiee":
				$ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE'] = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREDEBUTPREVUE_INTERPLANIFIEE']));
				$ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']   = date('Y-m-d H:i:s', strtotime($ROW['T_DATEHEUREFINPREVUE_INTERPLANIFIEE']));
				break;
			case "t_grandeur":
				$ROW['D_DERNFACTURATIONLE_GRANDEUR'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNFACTURATIONLE_GRANDEUR']));
				break;
			case "t_reglefactuclt":
				$ROW['D_DERNREELLELE_REGLEFACTUCLT']    = date('Y-m-d H:i:s', strtotime($ROW['D_DERNREELLELE_REGLEFACTUCLT']));
				$ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT'] = date('Y-m-d H:i:s', strtotime($ROW['D_DERNTHEORIQUELE_REGLEFACTUCLT']));
				break;
			case "t_lieubien":
				$ROW['D_DATEDEBUT_LIEUBIEN'] = date('Y-m-d', strtotime($ROW['D_DATEDEBUT_LIEUBIEN']));
				if(!empty($ROW['D_DATEFIN_LIEUBIEN'])){
					$ROW['D_DATEFIN_LIEUBIEN'] = date('Y-m-d', strtotime($ROW['D_DATEFIN_LIEUBIEN']));
				}else{
					$ROW['D_DATEFIN_LIEUBIEN']='';
				}
				break;
			case "t_solservclt":
				if (strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']) < $time) continue;
				$ROW['D_DATEDEFININITIALE_SOLSERVCLT'] = date('Y-m-d', strtotime($ROW['D_DATEDEFININITIALE_SOLSERVCLT']));
				$ROW['D_DATEDEDEBUT_SOLSERVCLT']       = date('Y-m-d', strtotime($ROW['D_DATEDEDEBUT_SOLSERVCLT']));
				break;
			case "t_cptcpta":
				if (empty($ROW['C_CODEENCOMPTABILITE_CPTCPTA'])) continue;
				break;
		endswitch;
		// si N_ID
		if($ROW['N_ID']){
			 $app_cli->update(array('N_ID'=>$ROW['N_ID']),  ['$set'=>$ROW + array('table'=>$name_table, 'date'=>date('d/M/Y'),'time'=>date('H:i:s'))],['upsert'=>true]);
		}
		//
		if($z % 200 == 0 || $z==$res->recordCount()){
			skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'mysql_firstinjob' ,
				'progress_value'   => $z ,
				'progress_max'     => $res->recordCount()  ,
				'progress_message' => $count.' / '.$z ) , session_id());
		}
		//
		// do_artis_rows($name_table,$ROW);
	}
}
//
skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'mysql_synchr_job' ,
	'progress_value'   => 100 ,
	'progress_max'     => 100 ,
	'progress_message' => 'Traitemen terminé' ) , session_id());

function encode_items(&$item, $key){
    $item = utf8_encode($item);
}
 