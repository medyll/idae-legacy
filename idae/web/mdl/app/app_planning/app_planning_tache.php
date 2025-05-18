<?
	include_once($_SERVER['CONF_INC']);
	$APP         = new App('tache');

	$time        = time();
	$table       = 'tache';
	$table_value = (int)$_POST['idtache'];
	$idtache     = (int)$_POST['idtache'];
	$arrtache    = $APP->findOne(array('idtache' => $idtache));
	// $arrstatut = skelMongo::connect('statut_tache','sitebase_tache')->findOne(array('idstatut_tache'=>(int)$arrtache['idstatut_tache'])) ;
	// $arrtype = skelMongo::connect('type_tache','sitebase_tache')->findOne(array('idtype_tache'=>(int)$arrtache['idtype_tache'])) ;
	$tache_id = rand() . time();
	//
	if (!empty($arrtache['heureDebutTache']) && !empty($arrtache['heureFinTache'])) {
		//Définition des heures
		$heure1 = $arrtache['heureDebutTache'];
		$heure2 = $arrtache['heureFinTache'];
		//Extractions des différents paramètres
		list($h1, $m1, $sec1) = explode(':', $heure1);
		list($h2, $m2, $sec2) = explode(':', $heure2);
		//Calcul des timestamps
		$timestamp1 = @mktime($h1, $m1, $sec1, 7, 9, 2006);
		$timestamp2 = @mktime($h2, $m2, $sec2, 7, 9, 2006);
		$diff       = floor(abs($timestamp2 - $timestamp1) / 60);
		$diff       = (!empty($heure2) && $heure1 != $heure2) ? ($diff / 15) * 20 : "16";//
	} else {
		$diff = 16;
	}
	if ($diff > 400) {
		$diff = 16;
	}

	$icone_statut = (empty($arrtache['iconTache_statut'])) ? '' : '<i class="fa fa-' . $arrtache['iconTache_statut'] . '"></i>';
	$icone = (empty($arrtache['iconTache_type'])) ? $arrtache['codeTache_type'] : '<i class="fa fa-' . $arrtache['iconTache_type'] . '"></i>';
	$titre_tache = (empty($arrtache['nomClient'].$arrtache['nomProspect']))? $arrtache['nomTache'] : $arrtache['nomClient'].$arrtache['nomProspect']  ;
?>
<div data-table="tache" data-table_value="<?=$idtache?>" data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>" ondblclick="Event.stop(event);<?= fonctionsJs::app_update('tache', $idtache) ?>" resizeable="true" class="flex_h flex flex_align_middle" id="<?= $tache_id ?>"
     title="<?= $arrtache['objetTache'] ?>">
	<div class=""><?=$APP->draw_field(['field_name_raw'=>'icon','table'=>'tache_type','field_value'=>$arrtache['iconTache_type']])?></div>
	<div class="flex_main ellipsis" title="<?= $arrtache['nomTache'] ?>">
		<? if(empty($arrtache['nomClient'].$arrtache['nomProspect'])){
			echo $APP->draw_field(['field_name_raw'=>'nom','table'=>'tache','field_value'=>$arrtache['nomTache']]);
		} else{
			echo $APP->draw_field(['field_name_raw'=>'nom','table'=>'client','field_value'=>$arrtache['nomClient']]);
			echo $APP->draw_field(['field_name_raw'=>'nom','table'=>'prospect','field_value'=>$arrtache['nomProspect']]);
		}  ?>
	</div>
	<div class="padding" title="<?= $arrtache['nomTache_statut'] ?>" >
		<?=$APP->draw_field(['field_name_raw'=>'color','table'=>'tache_statut','field_value'=>$arrtache['colorTache_statut']])?>
	</div>
	<div class="none absolute boxshadow" style="left:0%;min-width:250px;bottom:100%;z-index:20000">
		<div class="blanc" act_defer mdl="app/app/app_fiche_mini" vars="table=tache&table_value=<?= $idtache ?>"></div>
	</div>
</div>
<script>
	dateAdd = function (date, interval, units) {
		var ret = new Date(date); //don't change original date
		switch (interval.toLowerCase()) {
			case 'year'   :
				ret.setFullYear(ret.getFullYear() + units);
				break;
			case 'quarter':
				ret.setMonth(ret.getMonth() + 3 * units);
				break;
			case 'month'  :
				ret.setMonth(ret.getMonth() + units);
				break;
			case 'week'   :
				ret.setDate(ret.getDate() + 7 * units);
				break;
			case 'day'    :
				ret.setDate(ret.getDate() + units);
				break;
			case 'hour'   :
				ret.setTime(ret.getTime() + units * 3600000);
				break;
			case 'minute' :
				ret.setTime(ret.getTime() + units * 60000);
				break;
			case 'second' :
				ret.setTime(ret.getTime() + units * 1000);
				break;
			default       :
				ret = undefined;
				break;
		}
		return ret;
	}

	/*new Resizeable($('<?=$tache_id?>'),{
	 top: 0,
	 left: 0,
	 right: 0,
	 parent: $('<?=$tache_id?>').up(),
	 resize: function(el) {

	 height = eval($('<?=$tache_id?>').getHeight()) / 20 ;
	 height = Math.round(height) * 20;


	 $('<?=$tache_id?>').setStyle({'height':height+'px'});

	 ajaxValidation('app_update','mdl/app/','table=tache&table_value=<?=$idtache?>&vars[heureFinTache]='+(height/20))
	 }
	 });*/
</script>
