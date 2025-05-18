<?
	include_once($_SERVER['CONF_INC']);

array_walk_recursive($_POST, 'CleanStr',$_POST); 
ini_set('display_errors',55);
if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;}
	$APP = new App();
switch ($F_action){	
	case 'uploadWallpaper': 
			// dossier de destination
			umask(0777); 
			// chmod($_POST['rep'], 0777);
			umask(0777);
			
			$filename = str_replace(' ','-',$_FILES['wallPaper']['name']); // file name  
			$result = 'OK'; 
			move_uploaded_file($_FILES['wallPaper']['tmp_name'], $_POST['rep'].$filename); 
			chmod($_POST['rep'].$filename, 0777); 
			 
	break;

	case "setWallPaper":
		$wallpaper = $_POST['wallpaper'];

		$APP->plug('sitebase_base','agent')->update(array('idagent'=>(int)$_SESSION['idagent']),array('$set'=>array('settings.wallpaper'=>$wallpaper)));
		$base 	=   $APP->plug_base('sitebase_image')->getGridFs('wallpaper');
		$dsp 	=   $base->findOne(array('filename'=>$wallpaper,'metadata.thumb'=>array('$ne'=>1)));
		$imgsrc =   $dsp->getBytes();
		$_id 	=   $dsp->file['_id'];
		?>
		<script> 
		url_w = "url(http://<?=DOCUMENTDOMAIN?>/images/appimg-<?=$_id?>.jpg)";
		localStorage.setItem('wallpaper',url_w);
		$('body').setStyle({backgroundImage:url_w}) ;
		// $('decobar').setStyle({backgroundImage:url_w});
		//$('ssupbar').setStyle({backgroundImage:url_w})  ;
		</script>
		<? 
	break;
	case "setColor":
		$color = $_POST['color'];//PATH."/images/background/"; 

		$APP->plug('sitebase_base','agent')->update(array('idagent'=>(int)$_SESSION['idagent']),array('$set'=>array('settings.wallpaper'=>'')));
		$APP->plug('sitebase_base','agent')->update(array('idagent'=>(int)$_SESSION['idagent']),array('$set'=>array('settings.backgroundcolor'=>$color)));
	break;
	case "delWallPaper":
		$rep = $_POST['rep'];//PATH."/images/background/";
		$legalRep = DOCUMENTROOT."/images/background/";
		$size = $_POST['size'];
		$wallpaper = $_POST['wallpaper'];
		unlink($rep.$wallpaper); 
	break;
}

include_once(DOCUMENTROOT.'/postAction.php');
?>
