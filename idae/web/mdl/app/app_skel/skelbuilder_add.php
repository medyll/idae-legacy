<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	$APP = new App();

$_POST['_id']= MongoCompat::toObjectId($_POST['_id']); 
$arr = $APP->plug('sitebase_skelbuilder','skel_builder')->findOne(array('_id'=>$_POST['_id']));
?>

<div ><div class="entete">
<div class="titre"><?=$arr['dbForm'].'.'.$arr['colForm']?></div>
</div>
<div class="padding borderb"> 
  <div style="overflow:hidden;max-height:500px;" id=" " >
    <form action="mdl/app/app_skel/actions.php" onSubmit="ajaxFormValidation(this);return false" >
      <input type="hidden" value="addInput" name="F_action"> 
      <input type="hidden" value="close" name="afterAction[app/app_skel/skelbuilder_add]">
      <input type="hidden" value="*" name="reloadModule[app/app_skel/skelbuilder_liste]">
  		<input type="hidden" name="_id" value="<?=$_POST['_id']?>" /> 
      <table>
        <tr>
          <td><label >Nom du champ</label></td>
          <td ><input type="text" name="vars[nomInput]" class="required inputLarge" value=""  onkeyup="$('inputer').value= $(this).value.gsub('-','')+'<?=ucfirst($arr['colForm'])?>';"></td> 
        </tr>  
        <tr>
          <td><label >Nom du champ</label></td>
          <td ><input type="text" name="vars[nomInput]" class="required inputLarge" value=""  id="inputer"></td> 
        </tr> 
        <tr>
          <td><label >Type</label></td>
          <td ><select name="vars[typeInput]" class="inputLarge" >
                            <option value="input">input</option>
                            <option value="input">hidden</option>
                            <option value="checkbox">checkbox</option>
                            <option value="textarea">textarea</option>
                            <option value="date">date</option>
                            <option value="radio">radio</option>
                            <option value="checkbox">checkbox</option>
                            <option value="select">select</option>
                            <option value="mdl_select">mdl_select</option>
                    </select></td>
        </tr> 
        <tr>
          <td><label >Taille</label></td>
          <td><select name="vars[sizeInput]" class="inputLarge" >
                            <option value="inputTiny">inputTiny</option>
                            <option value="inputSmall">inputSmall</option>
                            <option value="inputMedium">inputMedium</option>
                            <option value="inputLarge">inputLarge</option>
                            <option value="inputFull">inputFull</option>
                            <option value="inputFree">inputFree</option> 
                    </select></td>
        </tr> 
        <tr>
          <td><label >Requis</label></td>
          <td><input type="checkbox" name="vars[requiredInput]" > </td>
        </tr> 
      </table>
      <div class="buttonZone">
        <input type="button" value="Annuler" class="cancelClose" />
        <input type="submit" value="Valider" />
      </div>
    </form>
  </div>
</div>
