<?
	include_once($_SERVER['CONF_INC']);
	$time = uniqid();

	// generalement , le nom du container
	$calendarId = $_POST['calendarId'];
	$sd         = $_POST['sd'];

	$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
	$tabjour  = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
	$tabjour  = ["L", "M", "M", "J", "V", "S", "D"];

	$jourEnCours  = date("d", $sd);
	$moisEnCours  = date("m", $sd);
	$anneeEnCours = date("Y", $sd);
	$indexJourCrt = date("w", $sd);
	if ($indexJourCrt == 0)
		$indexJourCrt = 7;

	$lienCalAvant = gmmktime(12, 0, 0, $moisEnCours - 1, $jourEnCours, $anneeEnCours);
	$lienCalApres = gmmktime(12, 0, 0, $moisEnCours + 1, $jourEnCours, $anneeEnCours);

	$anneeAvant = $moisEnCours . "','" . ($anneeEnCours - 1);
	$anneeApres = $moisEnCours . "','" . ($anneeEnCours + 1);

	$moyear = $tabmonth[intval($moisEnCours)] . "&nbsp;&nbsp;" . $anneeEnCours;
	$now    = date("Y/m/d", $sd);

	$moisPrec = mktime(12, 0, 0, $moisEnCours - 1, $jourEnCours, $anneeEnCours);
	$moisSuiv = mktime(12, 0, 0, $moisEnCours + 1, $jourEnCours, $anneeEnCours);
	$today    = date("d/m/Y", $sd);


?>
<div class="applink flex_h flex_align_middle borderb" style="overflow: hidden">
	<div class="flex_main" style="overflow: hidden">
		<div class="flex_h">
			<div class="flex_main">
				<a class="avoid bold change_month ellipsis    " vars="<?= http_build_query(['sd' => $sd]) ?>">
					<?= $tabmonth[intval($moisEnCours)] ?>&nbsp;<i class="fa fa-caret-down"></i>
				</a>
			</div>
			<div>
				<a class="avoid bold change_year ellipsis aligncenter" vars="<?= http_build_query(['sd' => $sd]) ?>">
					<?= $anneeEnCours ?>&nbsp;<i class="fa fa-caret-down"></i>
				</a>
			</div>
		</div>
	</div>
	<div style="min-width:30px;width:30px;" class="aligncenter previous_month padding"
	     vars="<?= http_build_query(['sd' => $moisPrec]) ?>">
		<a>
			<i class="fa fa-chevron-left"></i>
		</a>
	</div>
	<div style="min-width:30px;width:30px;" class="avoid aligncenter next_month borderl padding"
	     vars="<?= http_build_query(['sd' => $moisSuiv]) ?>">
		<a><i class="fa fa-chevron-right"></i></a>
	</div>
</div>
