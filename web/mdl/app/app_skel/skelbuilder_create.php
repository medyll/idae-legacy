<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php');
if(file_exists('../../../conf.inc.php')) include_once('../../../conf.inc.php');

?>

<div >
<div class="padding borderb"> 
  <div style="overflow:hidden;max-height:500px;" id=" " >
    <form action="mdl/app/app_skel/actions.php" onSubmit="ajaxFormValidation(this);return false" >
      <input type="hidden" value="createForm" name="F_action">
      <input type="hidden" value="<?=date('Y-m-d')?>" name="vars[dateCreationForm]">
      <input type="hidden" value="close" name="afterAction[app/app_skel/skelbuilder_create]">
      <input type="hidden" value="*" name="reloadModule[app/app_skel/skelbuilder_liste]">
      <input type="hidden" value="<?=$_SESSION['idagent']?>" name="vars[idagent]">
      <table class="table_form">
        <tr>
			<td><label >base</label></td>
			<td ><input type="text" name="vars[base]" class="required" value=""></td></tr><tr>
          <td><label >Collection données </label></td>
          <td ><input type="text" name="vars[collection]" class="required" value=""></td>
        </tr>
      </table>
      <div class="buttonZone">
        <input type="button" value="Annuler" class="cancelClose" />
        <input type="submit" value="Valider" />
      </div>
    </form>
  </div>
</div>
