<?php
include_once($_SERVER['CONF_INC']);

$uniqid = $_POST['uniqid'];
$page = (empty($_POST['page']))? 0 : $_POST['page'] ; 
$rppage = (empty($_POST['rppage']))? 30 : $_POST['rppage'] ;
$idagent = (empty($_POST['idagent']))? (int)$_SESSION['idagent'] : (int)$_POST['idagent'] ; 
//
$rs=skelMongo::connect('email','sitebase_email')->find(array('idagent'=>(int)$idagent,'from'=>array('$ne'=>'caroline.c@quizzboutique.com')))->sort(array('date'=>-1,'heure'=>-1))->skip($page*$rppage)->limit($rppage);
$time = time(); 
?>
<tr   id="autoLoad-recordcount" style="display:none" class="autoLoad-recordcount" recordcount="<?=$rs->count()?>"></tr>
<?
$i=0;
$out = '';
while($msg=$rs->getNext()){
		$i++;
	$arrClient = skelMongo::connect('client','sitebase_devis')->findOne(array('emailClient'=>trim($msg['from'])));
	$className = "autoToggle idmail";
	$className .= empty($msg['seen'])? ' bold ' : '' ;
	echo   skelMdl::cf_module('app/app_mail/app_mail_liste_tr',array( 'className'=>$className,'moduleTag'=>'tr','uniqid'=>$msg['uniqid']),$msg['uniqid'],'uniqid="'.$msg['uniqid'].'"' );
 
	}
?>