<?
	include_once($_SERVER['CONF_INC']);
?>

<div style="height:350px;" class="flex_v relative" >
  <div class="titre_entete uppercase relative"  >
    <?=idioma('Personnalisation')?>
  </div>
	<br><br>
  <div class="relative flex_main" style="width:100%;overflow:hidden;" id="drag<?=$uniqid?>">
    <?=skelMdl::cf_module('app/app_user_pref/app_wallpaper','',$_SESSION['idagent']) ?>
  </div>

</div>
