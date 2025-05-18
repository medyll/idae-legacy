<?
include_once ('conf.inc.php');
$APP = new App();
set_time_limit(0);
ini_set('display_errors',55);

// 
$arr_tables = get_artis_table();
 

$DAVALUE = empty($_GET['DAVALUE'])? 'CCBO': $_GET['DAVALUE'] ; 
$DAKEY = empty($_GET['DAKEY'])? '': $_GET['DAKEY'] ; 

foreach ($arr_tables as $key => $name_table) {
	
	$id = 'id' . $name_table;
	$app_cli = $APP -> plug('sitebase_sync' , $name_table  ); 
	 
		
	$rs = $app_cli->find();
		
	while ($arr = $rs -> getNext()) { 
		 foreach ($arr as $key => $value) {
			 if($value==$DAVALUE):
				 if (str_find( $key,$DAKEY,true)) {
					echo " => ";
				}
				 echo $name_table.' : N_ID:'.$arr['N_ID'].'  KEY : '.$key.' VALUE : '.$value.'<br>';
				 // vardump($arr);
				 echo "<hr>"; 
			 endif;
		 }
	}
}
 
function encode_items(&$item, $key){
    $item = utf8_encode($item);
}
 
?>
 