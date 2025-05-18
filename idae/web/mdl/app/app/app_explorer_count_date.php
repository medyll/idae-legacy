<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$vars           = empty($_POST['vars']) ? [] : $_POST['vars'];
	$table          = empty($_POST['table']) ? 'client' : $_POST['table'];
	$typeDate       = empty($_POST['typeDate']) ? 'dateDebut' : $_POST['typeDate'];
	$nomDate       = empty($_POST['nomDate']) ? 'date de début' : $_POST['nomDate'];
	$name_id        = 'id' . $table;
	$Table          = ucfirst($table);
	$APP            = new App($table);
	$APP_TABLE      = $APP->app_table_one;
	$APP_FIELDS     = $APP->get_field_list();
	$GRILLE_FK      = $APP->get_grille_fk();
	$ARR_DATE_FIELD = $APP->get_field_group_list('date');

	$dateStart = new DateTime('', new DateTimeZone('Europe/Paris'));

	$default = 'semaine';

	$DADATE               = $dateStart->format('Y-m-d');
	$jourEnCours          = $dateStart->format('d');
	$moisEnCours          = $dateStart->format('m');
	$anneeEnCours         = $dateStart->format('Y');
	$numeroSemaineEnCours = $dateStart->format('W');

	$premier_jour_mois          = clone $dateStart->modify('first day of this month');
	$dateStart                  = new DateTime('', new DateTimeZone('Europe/Paris'));
	$premier_jour_mois_dernier  = clone $dateStart->modify('first day of last month');
	$dateStart                  = new DateTime('', new DateTimeZone('Europe/Paris'));
	$dernier_jour_mois_dernier  = clone $dateStart->modify('last day of last month');
	$dateStart                  = new DateTime('', new DateTimeZone('Europe/Paris'));
	$premier_jour_prochain_mois = clone $dateStart->modify('first day of next month');
	$dateStart                  = new DateTime('', new DateTimeZone('Europe/Paris'));
	$dernier_jour_prochain_mois = clone $dateStart->modify('last day of next month');
	$dateStart                  = new DateTime('', new DateTimeZone('Europe/Paris'));

	$ce_lundi  = clone $dateStart->modify('monday this week');
	$dateStart = new DateTime('', new DateTimeZone('Europe/Paris'));

	$fin_semaine = clone $dateStart->modify('last day next week');
	$dateStart   = new DateTime('', new DateTimeZone('Europe/Paris'));

	$debut_semaine_derniere = clone $dateStart->modify('monday last week');
	$dateStart              = new DateTime('', new DateTimeZone('Europe/Paris'));

	$fin_semaine_derniere = clone $dateStart->modify('sunday last week');
	$dateStart            = new DateTime('', new DateTimeZone('Europe/Paris'));

	$prochain_lundi    = clone $dateStart->modify('next monday this month');
	$dateStart         = new DateTime('', new DateTimeZone('Europe/Paris'));
	$dernier_jour_mois = clone $dateStart->modify('last day of this month');
	$dateStart         = new DateTime('', new DateTimeZone('Europe/Paris'));

	// 	cette semaine
	// 	le reste du mois
	//	le mois prochain
	//  on prend les timestamp. puis boucle :
	$periode['mois_dernier']     = ['titre' => 'Mois dernier', 'dates' => [$premier_jour_mois_dernier->format("Y-m-d"), $dernier_jour_mois_dernier->format("Y-m-d")]];
	$periode['ce_mois']          = ['titre' => 'Ce mois çi', 'dates' => [$premier_jour_mois->format("Y-m-d"), $dernier_jour_mois->format("Y-m-d")]];
	$periode['semaine_derniere'] = ['titre' => 'Semaine derniere', 'dates' => [$debut_semaine_derniere->format("Y-m-d"), $fin_semaine_derniere->format("Y-m-d")]];
	$periode['semaine']          = ['titre' => 'Cette semaine', 'dates' => [$ce_lundi->format("Y-m-d"), $fin_semaine->format("Y-m-d")]];

	$zone = uniqid($table);
	//
	$periode['mois_dernier']['color']     = 'red';
	$periode['ce_mois']  ['color']        = 'blue';
	$periode['semaine_derniere']['color'] = 'green';
	$periode['semaine']  ['color']        = 'yellow';
	//	$rs = $APP->find([''=>['$gte'=>,'$lte'=>]]);
?>
<div id="<?= $zone ?>"  style="margin:20px auto 5px auto;">
	<div class="margin applink applinkblock relative">
		<a data-menu="data-menu" class="alignright"><i class="fa fa-angle-right"></i><span class="inline padding bordert" id="type_date_<?= $uniqid ?>">Type de date : <?=$nomDate?></span></a>
		<div class="contextmenu inline" style="display:none;right:0;">
			<? foreach ($ARR_DATE_FIELD as $key => $val) {
				$arrg = $val['group'];
				$arrf = $val['field'];
				?>
				<div class=" applink">
					<? foreach ($arrf as $keyf => $valf) {
						$valdate = $valf['codeAppscheme_field'] . ucfirst($table);
						$nomdate = $valf['nomAppscheme_field'];
						?>
						<a class="autoToggle"
						   onclick="$('type_date_<?= $uniqid ?>').update('<?= $nomdate .' ' . $table ?>');reloadModule('app/app/app_explorer_count_date','<?=$table?>','nomDate=<?=$nomdate?>&typeDate=<?=$valf['codeAppscheme_field']?>&table=<?=$table?>')">
							<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($valf['nomAppscheme_field'])) . ' ' . $table; ?></a>                                <? } ?>
				</div>                        <? } ?>
		</div>
	</div>
	<div class="flex_h flex_margin margin flex_wrap">
		<? foreach ($periode as $kay => $val):
			$titre   = $val['titre'];
			$arrdate = $val['dates'];
			$color   = $val['color'];
			$begin   = $arrdate[0];
			$end     = $arrdate[1];
			$rs      = $APP->find([$typeDate . $Table => ['$gte' => $begin, '$lte' => $end]]);
			if ($APP->has_agent()) {
				$rs_agent = $APP->find(['idagent' => (int)$_SESSION['idagent'], $typeDate . $Table => ['$gte' => $begin, '$lte' => $end]]);
			}
			?>
			<div class=" flex_main padding" title="<?= fonctionsProduction::jourMoisDate_fr($begin) . ' ' . fonctionsProduction::jourMoisDate_fr($end) ?>">
				<div class="border4 blanc" style="border-top:1px solid <?= $color ?>;">
					<div class="padding ellipsîs"><?= $titre ?></div>
					<? if ($APP->has_agent()) { ?>
						<div class="padding"><span class="ms-font-l "><?= $rs_agent->count() ?></span> <span class="textgrisfonce">(<?= $rs->count() ?>)</span></div>
					<? } else { ?>
						<div class="padding ms-font-l "><?= $rs->count() ?> </div>
					<? } ?></div>
			</div>
		<? endforeach; ?>
	</div>
</div>