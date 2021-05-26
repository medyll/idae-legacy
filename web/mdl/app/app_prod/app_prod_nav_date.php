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

	$scope = 'scope' . $uniqid;
	$deb   = 'deb' . $uniqid;
	$fin   = 'fin' . $uniqid;
	$APP_SYNC        = new App('sync_log');
?>
<!--<input type="hidden" name="table" value="<? /*= $table */ ?>">-->
<div id="form<?= $uniqid ?>" class="parent_form flex_h flex_align_middle   boxshadow border4" main_auto_tree>
	<div class="flex_v  flex_main padding">
		<div class="flex_h  flex_align_middle borderb">
			<div class="padding margin borderr aligncenter"><i class="fa fa-calendar-o fa-2x textbold"></i></div>
			<div class="applink applinkblock flex_main borderr" style="overflow:hidden">
				<a data-menu="data-menu" data-clone="true" class="ellipsis"><i class="fa fa-angle-right"></i><span id="type_date_<?= $uniqid ?>">Type de date</span></a>
				<div class="contextmenu" style="display:none;">
					<div class="applink applinkblock">
						<? foreach ($APP_DATE_FIELD as $valdate => $valf) {
							$nomdate  = $valf['nomAppscheme_field'];
							$codedate = $valf['codeAppscheme_field'] . $Table;
							?>
							<a class="autoToggle ellipsis"
							   onclick="$('type_date_<?= $uniqid ?>').update('<?= $nomdate . ' ' . $table ?>');$($('form<?= $uniqid ?>').querySelector('#<?= $deb ?>')).setAttribute('name','vars_date[<?= $codedate ?>]'+'[$gte]');$($('form<?= $uniqid ?>').querySelector('#<?= $fin ?>')).setAttribute('name','vars_date[<?= $codedate ?>]'+'[$lte]')">
								<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($nomdate)) . ' ' . $table; ?></a>                                <? } ?>
					</div>
				</div>
				<div data-menu="data-menu" data-clone="true" class="applink  applinkblock   ">
					<a><i class="fa fa-angle-right"></i><span id="type_periode_<?= $uniqid ?>"> <?= idioma('Période') ?></span></a>
				</div>
				<div class="contextmenu applinkblock" style="display:none;">
					<div>
						<div act_defer mdl="app/app_calendrier/app_calendrier_select" id="select_periode<?= $uniqid ?>"></div>
					</div>
				</div>
			</div>
			<div id="refresh_nav" class="padding margin ededed aligncenter">
				<a app_button id="refresh_nav_btn"><i class="fa fa-refresh"></i></a>
			</div>
		</div>
		<div class="blanc borderb" style="display: none;">
			<?= skelMdl::cf_module('app/app_calendrier/app_calendrier', ['table' => $table]) ?>
		</div>
		<div auto_tree right="right">
			<div class="flex_h flex_align_middle borderb">
				<div class="padding flex_main" style="width:30px;">Du</div>
				<div class="padding">
					<input class="noborder validate-date-au" id="<?= $deb ?>" type="text" name="vars_date[dateCreation<?= ucfirst($table) ?>][$gte]" value=""/>
				</div>
			</div>
		</div>
		<div class="ededed border4 padding_more" style="display: none;">
			<div class="blanc"><?= skelMdl::cf_module('app/app_calendrier/app_calendrier', [ 'table' => $table, 'calendar_target' => '#' . $deb, 'date_field' => 'dateCreation' . $Table]) ?></div>
		</div>
		<div auto_tree right="right">
			<div class="flex_h flex_align_middle">
				<div class="padding flex_main" style="width:30px;">Au</div>
				<div class="padding">
					<input class="noborder validate-date-au" id="<?= $fin ?>" type="text" name="vars_date[dateCreation<?= ucfirst($table) ?>][$lte]" value=""/>
				</div>
			</div>
		</div>
		<div class="ededed border4 padding_more" style="display: none;">
			<div class="blanc"><?= skelMdl::cf_module('app/app_calendrier/app_calendrier', [ 'table' => $table, 'calendar_target' => '#' . $fin, 'date_field' => 'dateCreation' . $Table]) ?></div>
		</div>
	</div>
</div>
<script>
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