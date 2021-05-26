<?
	include_once($_SERVER['CONF_INC']);

	$time= time();
	$table = $_POST['table'];

    $APP = new App($table);
    //
    $idtache = (int)$_POST['table_value'];
    $ARR = $APP->query_one(['id'.$table => $idtache]);

?>
<script>
$('cal_<?=$time?>').observe('dom:act_click',function(event){moveTache(event.memo.value)})
	moveTache = function(date){
	$('movespy').value = date;
	}
</script>
<div >
  <form id="formMoveTache" onsubmit="ajaxFormValidation(this);return false;" action="<?= ACTIONMDL ?>app/actions.php" auto_close>
	<input type="hidden" name="F_action" value="app_update" />
	<input type="hidden" value="<?=$table?>" name="table" >
	<input type="hidden" value="<?=$idtache?>" name="table_value" >
	  <input type="hidden" name="reloadModule[app/app_planning/app_planning_tache_reload]" value="*"/>
	  <div class="aligncenter">
	<input type="text" id="movespy"  name="vars[dateDebutTache]" class="titre2 aligncenter" readonly required >
	  </div>

    <div class="padding">
      <div class="padding" id="cal_<?=$time?>">
        <?=skelMdl::cf_module('app/app_calendrier/app_calendrier')?>
      </div>
    </div>
    <div class="buttonZone">
      <input type="submit"  value="Valider" >
      <input type="button" class="cancelButton" value="Fermer" >
    </div>
  </form>
</div>
