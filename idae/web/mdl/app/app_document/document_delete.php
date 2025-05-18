<?
include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55);  
$uniqid 	= uniqid(); 
if(empty($_POST['_id'])) return; 
$vars 		= empty($_POST['vars'])? array() : $_POST['vars'] ;
$base 		= empty($_POST['base'])? 'sitebase_ged' : $_POST['base']; 
$collection = empty($_POST['collection'])? 'ged_client' : $_POST['collection'];  
$baseFile	= skelMongo::connectBase($base);
$fs 	 	= $baseFile->getGridFs($collection);
$rs 	 	= $fs->findOne($vars); 
$arr 		= $rs->file 
?>

<div style="width:350px;"> 
  <div class="titre_entete fond_noir color_fond_noir">
  <?=idioma('Supression document')?>
  </div>
  <div class="barre_entete">
  <?=$arr['filename']?>
  </div>
  <form action="<?=ACTIONMDL?>document/actions.php"  onsubmit="ajaxFormValidation(this);return false;" />
  <input type="hidden" name="afterAction[document/document_delete]" value="close" /> 
  <input type="hidden" name="deleteModule[trfilename]" value="<?=$_POST['_id']?>" />
  <input type="hidden" name="F_action" value="deleteDoc" />
  <input type="hidden" name="_id" value="<?=$_POST['_id']?>" /> 
  <input type="hidden" name="collection" value="<?=$collection?>" /> 
  <input type="hidden" name="base" value="<?=$base?>" /> 
  <table>
    <tr>
      <td style="width:90px;text-align:center"><br>
        <img src="<?=ICONPATH?>alert32.png" /></td>
      <td class="texterouge"><br>
        Voulez vous supprimer <br>
        ce document ?<br>
        <strong><?=$arr['filename']?></strong><br>
		<?=date('d/m/Y H:i',$arr['uploadDate']->sec)?><br> 
        <?=strtolower(implode(' ',(array)$arr['metatag']));?>
         </td>
    </tr>
  </table>
  <br>
  <div class="buttonZone">
    <input type="submit" value="Supprimer"  >
    <input type="reset" value="Annuler" class="cancelClose" >
  </div>
  </form>
</div>
