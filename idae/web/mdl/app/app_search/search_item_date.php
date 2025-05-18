<?
	include_once($_SERVER['CONF_INC']);
return;
	$APP = new App();

	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
	$DBVARS = Act::decodeVars($vars);
	$emptyArr = array();
	$collection = $APP->plug('sitebase_production','produit');
	//
	$rs = $collection->find($DBVARS);
	//
	foreach ($rs as $key => $value) {
		foreach ($value['grilleDateProduit'] as $keydate => $valuedate) {
			if (strtotime($valuedate['dateDebutProduit_tarif']) > time()):
				$ext                         = explode('-', $valuedate['dateDebutProduit_tarif']);
				$emptyArr[$ext[0] . $ext[1]] = $ext[0] . '-' . $ext[1];
			endif;
		}
	}
	ksort($emptyArr);
?>

<div class = "searchMdl input-control select size4" >
	<select name = "vars[dateDebutProduit_tarif]" >
		<option class = "textegris searchline" value = "" >Date</option >
		<? foreach ($emptyArr as $key => $arrP):
			$extract = explode('-', $arrP);
			?>
			<option value = "<?= $extract[0] . $extract[1] ?>" class = "searchline"  <?= selected($vars["date"] == (int)($extract[0] . $extract[1])) ?> >
				<?= fonctionsSite::mois_fr($extract[1]) . ' ' . $extract[0] ?>
			</option >
		<?
		endforeach;
		?>
	</select >
</div >

