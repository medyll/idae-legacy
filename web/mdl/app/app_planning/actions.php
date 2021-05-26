<? 
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 

array_walk_recursive($_POST, 'CleanStr',$_POST); 
if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;}
switch ($F_action)
{	 
	case "updateTache":
		if(isset($_POST['heureDebutTache'])){
			if(trim($_POST['heureDebutTache'])== ''){
				unset($_POST['heureDebutTache']);
			}
		}
		//recalcule heure de fin si besoin
		if(!empty($_POST['heureDebutTache'])){
			$rsTache = $ClassTache->getOneTache(array('idtache'=>$_POST['idtache'])); 
			$debut =  strtotime($rsTache->fields['heureDebutTache']);
			$fin =  strtotime($rsTache->fields['heureFinTache']);
			$diff =  $fin-$debut;
			$newdebut = strtotime($_POST['heureDebutTache']);
			$_POST['heureFinTache'] =  $diff = date('H:i:s',$newdebut+$diff);
			// $_POST['noticeMsg'] = $newdebut.' '.$diff;
		}
			//$diff = date('h:m:s',$diff);
			$ClassTache->updateTache(array('idtache'=>$_POST['idtache'])+$_POST);
	break; 
	
	case "dropTache": 
		if(empty($_POST['idtache'])) break; 
		$idtache = (int)$_POST['idtache'];
		$arr=fonctionsProduction::cleanPostMongo($_POST);
		unset( $arr['idtache'] );
		$_POST['reloadModule']['mdlTacheFiche'] = $idtache ;
		skelMongo::connect('tache','sitebase_tache')->update(array('idtache'=>(int)$idtache),array('$set'=>$arr),array('upsert'=>true));
	break;
	case "updateHeureTache":
		if(empty($_POST['idtache'])) break; 
		$idtache = (int)$_POST['idtache'];
		$arr = skelMongo::connect('tache','sitebase_tache')->findOne(array('idtache'=>$idtache)); 
		$heureDebutTache = $arr['heureDebutTache'];
		$val = $_POST['addTime'];
		$nbheure = intval($val/4);  
		$debut = strtotime($arr['heureDebutTache']);
		$nbsec = $val*15*60 ;
		$_POST['heureFinTache'] =  date('H:i:s',$nbsec+$debut);
		$arrOut=fonctionsProduction::cleanPostMongo($_POST);
		skelMongo::connect('tache','sitebase_tache')->update(array('idtache'=>(int)$idtache),array('$set'=>$arrOut),array('upsert'=>true)); 
	break;
}

$host  = 'http://'.$_SERVER['HTTP_HOST'].'/'.APPDIR.'/postAction.php?'.http_build_query($_POST);  
//include_once(DOCUMENTROOT.'/postAction.php'); 
include_once(DOCUMENTROOT.'/postAction.php');
?> 