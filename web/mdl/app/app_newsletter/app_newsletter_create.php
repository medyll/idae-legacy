<?
include_once ($_SERVER['CONF_INC']);
?>

<div >
<div class="titre_entete fond_noir color_fond_noir">
<?=idioma('Nouvelle newsletter')?>
</div>
  <form  method="post" action="<?=ACTIONMDL?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
    <input type="hidden" name="F_action" value="app_create" />
    <input type="hidden" name="table" value="newsletter" />
    <table class="table_form">
      <tr>
        <td class="label">Titre</td>
        <td><input type="text" name="nomNewsletter"  class="required inputLarge" value="" /></td>
      </tr>
      <tr>
        <td class="label">Date d'envoi</td>
        <td><input type="text" name="dateDebutNewsletter" class="validate-date-au required" value="" /></td>
      </tr>
      <tr>
        <td class="label">Texte ouverture</td>
        <td><textarea style="width:100%;" name="descriptionNewsletter"> </textarea></td>
      </tr>
      <tr>
    </table>
    <div class="buttonZone">
      <input type="submit" value="valider" class="validButton" />
      <input type="button" value="Fermer" class="cancelClose" />
    </div>
  </form>
</div>
<div class = "titreFor" >
	<?= idioma('Création') ?>  <?= idioma('newsletter') ?>
</div >
