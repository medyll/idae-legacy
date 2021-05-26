<? 
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 

array_walk_recursive($_POST, 'CleanStr',$_POST);
ini_set('display_errors',55);
if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;}

$base = skelMongo::connectBase('sitebase_sockets'); 
$baseLive  = skelMongo::connect('onlive','sitebase_base');
 
switch ($F_action){	 
	case "write":
		$arr = fonctionsProduction::cleanPostMongo($_POST);
		$insert = array();
		$ONLINE_KEY = $_POST['ONLINE_KEY']; 
		$arrV = $baseLive->findOne(array('ONLINE_KEY'=>$ONLINE_KEY));
		$out = array('texte'=>$arr['texte'],'idagent'=>$_SESSION['idagent'],'dateCreation'=>date('Y-m-d'),'time'=>time(),'heureCreation'=>date('H:i:s')); 
		$insert['$set'] = array('onlive_thread.'.time()=>$out);
		$baseLive->update(array('ONLINE_KEY'=>$ONLINE_KEY),$insert ,array('upsert'=>true));
		$talkto = explode('_',$ONLINE_KEY);
		unset($talkto[0]);
			foreach($talkto as $key => $value):
			if ($value == $_SESSION['idagent'] ) unset($talkto[$key]);
			endforeach;
		//$talkto = $talkto - array($_SESSION['idagent']);
		$talkto = array_values($talkto); 
		//
		$needReload = $base->module->find(array('idagent'=>(int)$talkto[0],"value.liveidle/discussion_".$ONLINE_KEY=>array('$exists'=>1)));  
		$tot = $needReload->count();
		// 
	//	if($tot==0):
			$vars = array('notify'=>'loadModule','loadModule'=>'liveidle/discussion','titre'=>'appPop','width'=>350,'height'=>462,'vars'=>array('idagent'=>$talkto[0],'ONLINE_KEY'=>$ONLINE_KEY,'value'=>$ONLINE_KEY));
			skelMdl::reloadModule('activity/appActivity',$talkto[0],$vars);
			//skelMdl::doCurl('http://'.DOCUMENTDOMAIN.':3000/postReload',array('module'=>'activity/appActivity','value'=>'*','vars'=>$vars));
	//	endif;
 
		?>
		<script> 
		// $('writer_text_<?=$ONLINE_KEY?>').value='';
		</script>
		<?
		//exit;
	break;
	case "setIdle":
		if(!empty($_SESSION['idagent'])):
		$base->onLine->update(array('idagent'=>(int)$_SESSION['idagent']),array('$set'=>array('online'=>1,'idle'=>1)),array('upsert'=>true));
		skelMdl::reloadModule('gadget/onliveGadget','*');
		endif;
		exit;
	break;
	case "removeIdle":
		if(!empty($_SESSION['idagent'])):
		skelMdl::reloadModule('gadget/onliveGadget','*');
		$base->onLine->update(array('idagent'=>(int)$_SESSION['idagent']),array('$set'=>array('online'=>1,'idle'=>0)),array('upsert'=>true));
		endif;
		exit;
	break;
} 

include_once(DOCUMENTROOT.'/postAction.php');
?>  