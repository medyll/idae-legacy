<?
	include_once($_SERVER['CONF_INC']);
	
	$APP = new App('conge');
	$time = time(); 
	$idconge = (int)$_POST['idconge'];
	$arr = $APP->query_one(array('idconge'=>$idconge)) ;
	 if($arr['codeConge_statut']=='BEFORE' || $arr['codeConge_statut']=='END') return;
?>

<a act_chrome_gui = "app/app_custom/conge/conge_validate" vars = "table=conge&table_value=<?= $idconge ?>" options = "{ident:'validate_conge_<?= $idconge ?>'}" >
	<i class="fa fa-check textvert"></i>&nbsp; Valider ce congé</a>
<hr>
 