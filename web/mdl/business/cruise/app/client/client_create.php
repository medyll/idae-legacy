<?
	include_once($_SERVER['CONF_INC']);
?>
  
  <div style="overflow:hidden;max-height:500px;"  >
  <div class="titre_entete fond_noir color_fond_noir">
  <?=idioma('Nouveau client')?>
  </div>
    <form action="mdl/app/actions.php" onSubmit="ajaxFormValidation(this);return false" auto_close="true" >
      <input type="hidden" value="app_create" name="F_action">
      <input type="hidden" value="client" name="table">
      <input type="hidden" value="vars[idagent]" name="<?=$_SESSION['idagent']?>">
      <table class="table_form" cellspacing="0">
        <tr>
          <td class="label">  </td>
          <td colspan="3"><select name="vars[sexeClient]">
              <option value="monsieur">M</option>
              <option value="madame">Mme</option>
              <option value="mademoiselle">Mlle</option>
            </select></td>
        </tr>
        <tr>
          <td class="label">Nom </td>
          <td colspan="3"><input type="text" name="vars[nomClient]" class="inputLarge required" value="" autofocus></td>
        </tr>
        <tr class="borderb">
          <td class="label">Prenom </td>
          <td colspan="3"><input type="text" name="vars[prenomClient]" class="inputLarge required" value=""></td>
        </tr>
        <tr>
          <td class="label"><li class="fa fa-envelope"></li>E-mail</td>
          <td colspan="3"><input type="text" class="inputLarge"  name="vars[emailClient]" value=""></td>
        </tr>
        <tr class="border">
          <td class="label"><li class="fa fa-phone"></li>Téléphone </td>
          <td colspan="2"><input type="text"  name="vars[telephoneClient]" value=""></td>
          <td></td>
        </tr>
        <tr class="border">
          <td class="label"><li class="fa fa-phone"></li>Téléphone 2</td>
          <td colspan="2"><input type="text"  name="vars[telephone2Client]" value=""></td>
          <td></td>
        </tr>
        <tr class="ededed bordert">
          <td class="label">Adresse </td>
          <td colspan="3"><input type="text"  name="vars[adresseClient]" value="" class="inputLarge"></td>
        </tr>
        <tr class="ededed">
          <td></td>
          <td colspan="3"><input type="text"  name="vars[adressePlusClient]" value="" class="inputLarge"></td>
        </tr>
        <tr class="ededed">
          <td class="label">Code postal </td>
          <td ><input type="text"  name="vars[codePostalClient]" value=""></td>
          <td class="label">Ville</td>
          <td><input type="text"  name="vars[villeClient]" value=""></td>
        </tr>
      </table>
      <div class="buttonZone">
        <input type="button" value="Annuler" class="cancelClose" />
        <input type="submit" value="Valider" />
      </div>
    </form>
  </div> 
