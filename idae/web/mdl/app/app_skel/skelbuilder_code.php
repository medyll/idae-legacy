<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php');
if(file_exists('../../../conf.inc.php')) include_once('../../../conf.inc.php');
require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
use AppCommon\MongoCompat;  
ini_set('display_errors',55); 
$_POST['_id']= MongoCompat::toObjectId($_POST['_id']); 
$arr = skelMongo::connect('skel_builder','sitebase_skelbuilder')->findOne(array('_id'=>$_POST['_id']));
?>
 <div >
<div class="entete">
<div class="titre"><?=$arr['dbForm'].'.'.$arr['colForm']?></div>
</div>
<div class="padding borderb">  
  <div style="overflow:hidden;max-height:500px;width:750px;" id=" " > 
     <? 
		echo highlight_string(skelForm::getForm($_POST['_id'])); //  
	 ?> 
      <div class="buttonZone">
        <input type="button" value="Annuler" class="cancelClose" /> 
      </div> 
  </div>
</div>