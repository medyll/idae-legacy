<?
	include_once($_SERVER['CONF_INC']);
$time = time(); 
$idagent= (int)$_POST['table_value'];
$APP = new App('agent');
$arr = $APP->query_one(array('idagent'=>(int)$idagent));
?>

<div class="titre_entete fond_noir color_fond_noir">
  <?=idioma('Mise à jour').' '.$arr["nomAgent"]?>
  </div>
<form action="<?=ACTIONMDL?>app/actions.php"  onsubmit="ajaxFormValidation(this);return false;" />

<input type="hidden" name="F_action" value="app_update" />
<input type="hidden" name="afterAction[agent/mdlAgentUpdate]" value="close" />
<input type="hidden" name="reloadModule[agent/mdlAgentListe]" value="*" />
<input type="hidden" name="idagent" value="<?=$arr["idagent"]?>"   >
<input type="hidden" name="table" value="agent"   >
<input type="hidden" name="table_value" value="<?=$arr["idagent"]?>"   >
<table >
  <tr>
    <td><table class="table_form">
        <tr>
          <td><label>
              <?=idioma("actif")?>
            </label></td>
          <td>
          <input type="radio" name="vars[estActifAgent]" value="1" <?=checked(($arr["estActifAgent"]==1))?> /> oui
          <input type="radio" name="vars[estActifAgent]" value="0" <?=checked(($arr["estActifAgent"]==0))?> /> non
          </td>
        </tr>
        
          <td><label>
              <?=idioma("nomAgent")?>
            </label></td>
          <td><input type="text" name="vars[nomAgent]"  value="<?=$arr["nomAgent"]?>" class="required inputMedium"  ></td>
        </tr>
        <tr>
          <td><label>
              <?=idioma("prenomAgent")?>
            </label></td>
          <td><input type="text" name="vars[prenomAgent]"  value="<?=$arr["prenomAgent"]?>" class="required inputMedium"  ></td>
        </tr>
        <tr>
          <td><label>
              <?=idioma("emailAgent")?>
            </label></td>
          <td><input type="text" name="vars[emailAgent]"  value="<?=$arr["emailAgent"]?>" class="required inputMedium"  ></td>
        </tr>
        <tr>
          <td><label>
              <?=idioma("loginAgent")?>
            </label></td>
          <td><input type="text" name="vars[loginAgent]" value="<?=$arr["loginAgent"]?>"   ></td>
        </tr>
        <tr>
          <td><label>
              <?=idioma("passwordAgent")?>
            </label></td>
          <td><input type="text" name="vars[passwordAgent]" value="<?=$arr["passwordAgent"]?>"   ></td>
        </tr>
        <tr>
          <td><label>
              <?=idioma("mailPasswordAgent")?>
            </label></td>
          <td><input type="text" name="vars[mailPasswordAgent]" value="<?=$arr["mailPasswordAgent"]?>"  class="  inputMedium"  ></td>
        </tr>
      </table></td>
    <td style="vertical-align:top"><label>Groupes</label>
      <br />
      <input type="checkbox" name="vars[groupeAgent][DIR]" value="1" <?=checked($arr["groupeAgent"]['DIR'])?>   >
      groupe DIR<br />
      <input type="checkbox" name="vars[groupeAgent][BLNA]" value="1" <?=checked($arr["groupeAgent"]['BLNA'])?>   >
      groupe BLNA<br />
      <input type="checkbox" name="vars[groupeAgent][DISPLNA]" value="1" <?=checked($arr["groupeAgent"]['DISPLNA'])?>   >
      groupe DISPLNA<br />
      <input type="checkbox" name="vars[groupeAgent][ADMIN]" value="1" <?=checked($arr["groupeAgent"]['ADMIN'])?>  >
      groupe ADMIN<br />
      <input type="checkbox" name="vars[groupeAgent][AGENT]" value="1" <?=checked($arr["groupeAgent"]['AGENT'])?>   >
      groupe AGENT<br />
      <input type="checkbox" name="vars[groupeAgent][ELENA]" value="1" <?=checked($arr["groupeAgent"]['ELENA'])?>   >
      groupe ELENA<br />
      <input type="checkbox" name="vars[groupeAgent][MARK]" value="1" <?=checked($arr["groupeAgent"]['MARK'])?>   >
      groupe MARK<br />
      <input type="checkbox" name="vars[groupeAgent][DEV]" value="1" <?=checked($arr["groupeAgent"]['DEV'])?>   >
      groupe DEV<br />
      <input type="checkbox" name="vars[groupeAgent][COMPTA]" value="1" <?=checked($arr["groupeAgent"]['COMPTA'])?>   >
      groupe COMPTA<br /></td>
  </tr>
</table>
<div class="buttonZone">
  <input type="submit" value="Valider"  >
  <input type="reset" value="Annuler" class="cancelClose" >
</div>
</form>
