<?php
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
// generalement , le nom du container
$calendarId= $_POST['calendarId'];  
$iddevis = $_POST['iddevis'];
$time = time();
$ClassPrestation = new Prestation();
$ClassDateProduit = new DateProduit();
$rsPrestation = $ClassPrestation->getOnePrestation(array('iddevis'=>$iddevis,'codeType_prestation'=>CODETYPE_DEVIS));
$idproduit = $rsPrestation->fields['idproduit'];
$rsDateProduit = $ClassDateProduit->getOneDateProduit(array('idproduit'=>$idproduit));
$tabmonth  = array(1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet","Août", "Septembre", "Octobre", "Novembre", "Décembre");
$tabjour  = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
$tabjour  = array("L","M","M","J","V","S","D");

// Modified: 2026-08-11 — $inputString is interpolated straight into the
// onClick attribute below as fillInput's first argument. It used to emit
// `$('someform').somefield` / `$('someid')`, a call through the Prototype $()
// shim; fillInput (mdlCalendrierHeure.php's twin) already accepts either a
// plain id string or an element, which is exactly what $() absorbed. Emits
// the id/property-access expression as plain JS now, unshimmed.
if(!empty($_POST['form'])){ $inputString = "document.getElementById('".$_POST['form']."').".$_POST['input']; }
if(empty($_POST['form']) && !empty($_POST['input'] )){ $inputString = "'".$_POST['input']."'"; }
if(!empty($_POST['function'])){ $inputString = $_POST['function']."()"; }
$input = time();

if(!empty($_POST['date'])){
	$tmpdate = explode( "/", $_POST['date']);
	$jour = $tmpdate[0];
	$mois = !empty($tmpdate[1])? $tmpdate[1] : date("m");
	$annee = !empty($tmpdate[2])? $tmpdate[2] : date("Y"); 

	$_POST['sd'] = mktime(12,0,0,$mois, $jour, $annee);
}
unset($_POST['date']);
if (empty($_POST['sd'])) {
	if (!isset($jour))  $jour = ((!empty($mois)) ? 1 : date("j"));
	if (!isset($mois))  $mois = date("m");
	if (!isset($annee)) $annee = date("Y");
	$sd = $_POST['sd'] = mktime(12,0,0,$mois, $jour, $annee);
}
else{
	$sd = $_POST['sd'];
}

$jourEnCours  = date("d", $sd);
$moisEnCours  = date("m", $sd);
$anneeEnCours = date("Y", $sd);
$indexJourCrt = date("w",$sd);
if ($indexJourCrt == 0)
$indexJourCrt = 7;

$lienCalAvant = gmmktime(12,0,0,$moisEnCours-1,$jourEnCours,$anneeEnCours);
$lienCalApres = gmmktime(12,0,0,$moisEnCours+1,$jourEnCours,$anneeEnCours);

$anneeAvant = $moisEnCours."','".($anneeEnCours-1);
$anneeApres = $moisEnCours."','".($anneeEnCours+1);
$moyear = $tabmonth[intval($moisEnCours)]."&nbsp;&nbsp;".$anneeEnCours;
$now=date("Y/m/d",$sd);

$moisPrec = mktime(12,0,0,$moisEnCours-1,$jourEnCours,$anneeEnCours);
$moisSuiv = mktime(12,0,0,$moisEnCours+1,$jourEnCours,$anneeEnCours);

?>

<div style="width:200px;"></div>
<div style="width:200px;margin:auto;height:170px;" class="blanc" >
  <div class="blanc" style="width:100%;display:block;" id="outTblCalTache<?=$time?>">
 <span class="titre3"> <?=idioma('Dates disponibles')?></span>
 <div class="flowDown" style="overflow:auto;">
 <?php
 while($arr = $rsDateProduit->fetchRow()){
 ?>
 <br />
 <a onclick="fillInput(<?=$inputString?>,'<?=date_fr($arr['dateDebutDate_produit'])?>');"><?=date_fr($arr['dateDebutDate_produit'])?></a>
 <?php
 }?></div>
  </div>
</div>
<script>
 

 
/*
 * Modified: 2026-08-11 — migrated off the PrototypeJS `$` shim. `input`
 * arrives here both as an id and as an element, which is what $() absorbed.
 */
fillInput = function(input,vars){
	<?php if(!empty($_POST['function'])){ ?> 
		<?=$_POST['function']?>(vars) 
	<?php }else{ ?>
	(typeof input === 'string' ? document.getElementById(input) : input).value = vars;
	<?php }?> 
	return false;
}
</script>
