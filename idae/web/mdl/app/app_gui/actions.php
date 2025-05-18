<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();

array_walk_recursive($_POST, 'CleanStr',$_POST);
ini_set('display_errors',55);
if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;}

 
switch ($F_action){	
	case "createTile":
		// 
		$_POST['idagent'] = $_SESSION['idagent']; 
		$arr  = fonctionsProduction::cleanPostMongo($_POST);
		$arr[$arr['table']] = $arr['table_value'];
		$APP->plug('sitebase_base','agent')->update(array('idagent'=>(int)$_SESSION['idagent']),array('$push'=>array('tile'=>$arr)));
		skelMdl::reloadModule('gui/gui_tile','*');
		skelMdl::reloadModule('gui/gui_tile_click','*');
	break; 
	case "deleteTile":
		$arr  = fonctionsProduction::cleanPostMongo($_POST);
		$arr[$arr['table']] = $arr['table_value'];
		$APP->plug('sitebase_base','agent')->update(array('idagent'=>(int)$_SESSION['idagent']),array('$pull'=>array('tile'=>$arr)));
		skelMdl::reloadModule('gui/gui_tile','*');
		skelMdl::reloadModule('gui/gui_tile_click','*');
	break; 
} 
include_once(DOCUMENTROOT.'/postAction.php');
?>  