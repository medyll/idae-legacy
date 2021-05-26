<?    
ob_start();
include_once($_SERVER['CONF_INC']);

ob_end_clean();
ini_set('display_errors',55); 
if(!empty($_GET['frameLoaded'])){ 
	$uniqid = $_GET['uniqid'];
	$arr = 	skelMongo::connect('email','sitebase_email')->findOne(array('uniqid'=>$uniqid));
	vardump($uniqid);
	echo empty($arr['html'])? $arr['text'] : $arr['html'] ; 
	//
}else{
	$uniqid = $_POST['uniqid'];
	?>
	<div class="flowDown relative ededed" style="overflow:hidden;width:100%;">
  <iframe src="<?=$_SERVER['PHP_SELF']?>?frameLoaded=frameLoaded&uniqid=<?=$uniqid?>" class="flowDown" style="display:block;height:100%;overflow:auto;width:100%;margin:0 auto;" marginheight="0" marginwidth="0" frameborder="0">
  </iframe>
</div>
	<?
	}  
?>