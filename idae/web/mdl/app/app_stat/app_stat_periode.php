<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 0);
	if (empty($_POST['table'])) return;
	$uniqid         = uniqid($_POST['table']);
	$table          = $_POST['table'];
	$Table          = ucfirst($table);
	$vars           = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP            = new App($table);
	$HTTP_VARS      = $APP->translate_vars($vars);
	$HTTP_BASE_VARS = http_build_query(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);

	// 7 derniers jours
	$startTime      = date('d/m/Y', mktime() - 7 * 3600 * 24);
	$endTime        = date('d/m/Y', mktime());
	$APP_DATE_FIELD = $APP->get_field_group_list('date')[0]['field'];

	$scope    = 'scope' . $uniqid;
	$deb      = 'deb' . $uniqid;
	$fin      = 'fin' . $uniqid;
	$APP_SYNC = new App('sync_log');

	// 3 derniers mois par defaut
	$dateEnd   = new DateTime();
	$dateStart = new DateTime();
	$dateStart->modify('-6 month');
	$dateDebut = $dateStart->format('Y-m-d');
	$dateFin   = $dateEnd->format('Y-m-d');
?>
<!--<input type="hidden" name="table" value="<? /*= $table */ ?>">-->
<div id="form<?= $uniqid ?>" class="parent_form flex_h flex_align_middle  blanc " main_auto_tree>
	<div class="flex_v  flex_main">
		<div class="flex_h  flex_align_middle borderb">
			<div class="padding margin borderr aligncenter"><i class="fa fa-calendar-o fa-2x textbold"></i></div>
			<div id="refresh_nav" class="padding margin ededed aligncenter">
				<a app_button id="refresh_nav_btn"><i class="fa fa-refresh"></i></a>
			</div>
		</div>
		<div auto_tree right="right" class="borderb" >
			<div class="padding_more bold blanc flex_main ucfirst"><i class="fa fa-calendar"></i><?= idioma('période') ?> <span id="type_periode_<?= $uniqid ?>"></span></div>
		</div>
		<div class="applinkblock borderb marginb" style="box-shadow: -1px 1px 2px #cccccc">
			<div class="retrait_more" act_defer mdl="app/app_calendrier/app_calendrier_select" id="select_periode<?= $uniqid ?>"></div>
		</div>
		<div auto_tree right="right" class="borderb">
			<div class="flex_h flex_align_middle">
				<div class="padding bold" style="width:2em;">Du</div>
				<div class="padding flex_main flex_h flex_align_middle">
					<input class="ededed inputFull inputDate" id="<?= $deb ?>" type="text" name="dateDebut" value="<?= date_fr($dateDebut) ?>"/>
				</div>
			</div>
		</div>
		<div class="borderb marginb" style="box-shadow: -1px 1px 2px #cccccc">
			<div class="blanc"><?= skelMdl::cf_module('app/app_calendrier/app_calendrier', ['date' => $dateDebut, 'table' => $table, 'calendar_target' => '#' . $deb, 'date_field' => 'dateCreation' . $Table]) ?></div>
		</div>
		<div auto_tree right="right" class="borderb ">
			<div class="flex_h flex_align_middle">
				<div class="padding bold" style="width:2em;">Au</div>
				<div class="padding flex_main flex_h flex_align_middle"  id="up_<?= $fin ?>">
					<input class="ededed borderb inputFull inputDate" id="<?= $fin ?>" type="text" name="dateFin" value="<?= date_fr($dateFin) ?>"/>
				</div>
			</div>
		</div>
		<div class="borderb marginb" style="box-shadow: -1px 1px 2px #cccccc">
			<div class="blanc"><?= skelMdl::cf_module('app/app_calendrier/app_calendrier', ['date' => $dateFin, 'table' => $table, 'calendar_target' => '#' . $fin, 'date_field' => 'dateCreation' . $Table]) ?></div>
		</div>
	</div>
</div>
<script>
	$('<?= $fin ?>').observe('change',function(event){
		console.log($('<?= $fin ?>').value)
	});
	$('body').on('dom:act_click', '#select_periode<?=$uniqid?>', function (event) {

		$('form<?= $uniqid ?>').select('#refresh_nav').first().removeClassName('bounce');
		$('form<?= $uniqid ?>').select('#refresh_nav').first().removeClassName('animated');

		$('type_periode_<?=$uniqid?>').update(event.memo.value);
		$($('form<?=$uniqid?>').querySelector('#<?=$deb?>')).value = event.memo.dateDebut
		$($('form<?=$uniqid?>').querySelector('#<?=$fin?>')).value = event.memo.dateFin

		$('form<?= $uniqid ?>').select('#refresh_nav_btn').first().setAttribute('vars', Form.serialize($('form<?=$uniqid?>')))
		$('form<?= $uniqid ?>').select('#refresh_nav').first().addClassName('animated bounce');
	})
	$('form<?= $uniqid ?>').on('dom:act_change', function () {

		$('form<?= $uniqid ?>').select('#refresh_nav').first().removeClassName('bounce');
		$('form<?= $uniqid ?>').select('#refresh_nav').first().removeClassName('animated');

		if ($($('form<?=$uniqid?>').querySelector('#<?=$fin?>')).value == '') $($('form<?=$uniqid?>').querySelector('#<?=$fin?>')).value = $($('form<?=$uniqid?>').querySelector('#<?=$deb?>')).value;
		$('form<?= $uniqid ?>').select('#refresh_nav_btn').first().setAttribute('vars', Form.serialize($('form<?=$uniqid?>')));

		$('form<?= $uniqid ?>').select('#refresh_nav').first().addClassName('animated bounce');
	})
</script>