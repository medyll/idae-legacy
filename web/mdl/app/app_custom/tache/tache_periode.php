<? 
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$time = time(); 
// ?
?>

<table class="tablemiddle"  style="width:100%;table-layout:auto">
  <tr>
    <td style="width:80px;"><label>Répétitions</label></td>
    <td><div class="toggler applink retrait">
        <label class="autoToggle nolabel padding inline">
          <input type="radio" name="typePeriodiciteTache" value="ONE" checked="checked">
          &nbsp;Unique</label>
        <label class="autoToggle nolabel padding inline">
          <input type="radio" name="typePeriodiciteTache" value="DAY">
          &nbsp;Tout les jours</label>
        <label class="autoToggle nolabel padding inline">
          <input type="radio" name="typePeriodiciteTache" value="WORK">
          &nbsp;Tout les jours ouvrables</label>
        <label class="autoToggle nolabel padding inline">
          <input type="radio" name="typePeriodiciteTache" value="WEEK">
          &nbsp;Toutes les semaines</label>
        <label class="autoToggle nolabel padding inline">
          <input type="radio" name="typePeriodiciteTache" value="MONTH">
          &nbsp;Tout les mois</label>
      </div></td>
  </tr>
  <tr>
    <td><label>Pas</label></td>
    <td><div class="toggler applink retrait">
        &nbsp;Toutes les&nbsp;
        <input type="text" name="pasPeriodiciteTache" class="inputTiny" value="1" />
        fois </div></td>
  </tr>
  <tr>
    <td><label>Plage</label></td>
    <td><div class="toggler applink retrait">
        <div class="autoToggle nolabel padding inline">
          <input type="radio" name="plagePeriodiciteTache" value="ROLL">
          &nbsp;Infini</div>
        <div class="autoToggle nolabel padding inline">
          <input type="radio" name="plagePeriodiciteTache" value="MAX" checked="checked">
          &nbsp;
          <input type="text" name="maxPlagePeriodiciteTache" value="10" class="inputTiny required validate-numeric" />
          &nbsp;fois maximum</div>
        <div class="autoToggle nolabel padding inline">
          <input type="radio" name="plagePeriodiciteTache" value="ONDATE">
          &nbsp;Date de fin
          <input type="text" name="dateFinPeriodiciteTache" class="validate-date-au"  />
        </div>
      </div></td>
  </tr>
</table>
<script>
/*$$('#reou input').each(function(node){
	$('reou').insert(node.readAttribute('name')+'<br>')
	})*/
</script>