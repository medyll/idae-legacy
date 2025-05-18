<?
include_once($_SERVER['CONF_INC']);
	$APP = new App();
	$APP_CONTACT = new App('contact');
$time = time();
ini_set('display_errors',55); 
$vars = (empty($_POST['vars']))? array():$_POST['vars'];  
$add= array();	 

$col_contact = $APP->plug('sitebase_email','email_sent');
//
$remember 	= 	trim($_POST['email']);
$arrEmail 	= 	explode(';',$_POST['email']);
$DAMAIL 	=  	trim(end($arrEmail));
$NODAMAIL 	= 	str_replace($DAMAIL,'',$remember);
$arrSearch 	= 	explode(' ',trim($DAMAIL));
foreach($arrSearch as $key=>$value){   
	  $out[] = new MongoRegex("/.*".(string)$arrSearch[$key]."*./i");  
} 
if(sizeof($out)==1){
	$add = array('$or'=>array(array('nomClient'=>array('$in'=>$out)),array('prenomClient'=>array('$in'=>$out))));
}
if(sizeof($out)>1){
	$add = array('$and'=>array(array('nomClient'=>array('$in'=>$out)),array('prenomClient'=>array('$in'=>$out))));
} 
//
if(sizeof($out)==1){
	$add2 = array('$or'=>array(array('from'=>array('$in'=>$out)),array('from_name'=>array('$in'=>$out))));
}
if(sizeof($out)>1){
	$add2 = array('$and'=>array(array('from'=>array('$in'=>$out)),array('from_name'=>array('$in'=>$out))));
} 
//

$rsOri 		= 	$APP->plug('sitebase_email','email_contact')->find()->sort(array('last_email_time'=>-1,'email_sent'=>1))->limit(10);
$rs 		= 	$APP_CONTACT->find($add)->limit(10);
 

?>
<?
if(empty($_POST['email'])){
while($arr =  $rsOri->getNext()){
	$value = strtolower($arr["email"]);
	$name  = empty($arr["nom"])? $value : $arr["nom"].' '.$value;
	$meta = 'meta[nom]='.$name;
	$meta .= '&meta[email]='.$value;
	?>
	<a class="autoToggle" onclick="$(this).fire('dom:act_click',{value:'<?=$value?>',meta:'<?=$meta?>'})" ><?=$name?></a>
	<?  }
while($arr =  $rs->getNext()){
	$value = strtolower($arr["email"]);
	$name  = empty($arr["nom"])? $value : $arr["nom"].' '.$value;
	$meta = 'meta[nom]='.$name;
	$meta .= '&meta[email]='.$value;
	?>
	<a class="autoToggle" onclick="$(this).fire('dom:act_click',{value:'<?=$value?>',meta:'<?=$meta?>'})" ><?=$name?></a>
	<?  }
}?>