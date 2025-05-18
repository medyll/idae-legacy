<?
include_once($_SERVER['CONF_INC']);
	$APP = new App('promo-zone');

ini_set('display_errors',55);  
$uniqid 	= uniqid(); 
if(empty($_POST['idpromo_zone'])) return;
$idpromo_zone 		= (int)$_POST['idpromo_zone'] ;
?>

<div style="width:350px;"> 
  <div class="titre_entete fond_noir color_fond_noir">
  <?=idioma('Supression block')?>
  </div>
  <div class="barre_entete">
  <?=$uid_grille_block?>
  </div>
  <form action="<?=ACTIONMDL?>app/app_promo_zone/actions.php"  onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
  <input type="hidden" name="deleteModule[app/app_promo_zone/app_promo_zone_build_module_block]" value="<?=$_POST['uid_grille_block']?>" />
  <input type="hidden" name="F_action" value="delete_block_enews" />
  <input type="hidden" name="idpromo_zone" value="<?=$idpromo_zone?>" />
  <input type="hidden" name="uid_grille_block" value="<?=$_POST['uid_grille_block']?>" /> 
  <input type="hidden" name="deleteModule[uidblock]" value="<?=$_POST['uid_grille_block']?>" />
  <table>
    <tr>
      <td style="width:90px;text-align:center"><br>
        <img src="<?=ICONPATH?>alert32.png" /></td>
      <td class="texterouge"><br>
        Voulez vous supprimer <br>
        ce block ?<br>
        <strong><?=$uid_grille_block?></strong> 
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
