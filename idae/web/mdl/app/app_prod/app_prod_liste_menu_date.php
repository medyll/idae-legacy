<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 0);
	if (empty($_POST['table'])) return;
	$uniqid          = uniqid($_POST['table']);
	$table           = $_POST['table'];
	$vars            = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP             = new App($table);
	$ARR_GROUP_FIELD = $APP->get_field_group_list('date');
	$HTTP_VARS       = $APP->translate_vars($vars);
	$HTTP_BASE_VARS  = http_build_query(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);

	// 7 derniers jours
	$startTime       = date('d/m/Y', mktime() - 7 * 3600 * 24);
	$endTime         = date('d/m/Y', mktime());
	$APP_SORT_FIELDS = $APP->get_date_fields($table);

?>
<!--<input type="hidden" name="table" value="<? /*= $table */ ?>">-->
<div id="form<?= $uniqid ?>" class="parent_form flex_h flex_align_middle flex_wrap">
	<div class="flex_v flex_margin flex_main">
		<div class="flex_h">
			<div class="padding flex_main">
				<div data-menu="data-menu" class="applink ">
					<a class="ellipsis">
						<i class="fa fa-calendar-times-o"></i><span   id="type_periode_<?= $uniqid ?>"> <?= idioma('Période') ?></span>
					</a>
				</div>
				<div class="contextmenu applinkblock" style="display:none;" id="oi<?= $uniqid ?>">
					<div act_defer mdl="app/app_calendrier/app_calendrier_select" id="oi<?= $uniqid ?>"></div>
				</div>
			</div>
			<div class="padding">
				<a data-menu="data-menu" class="ellipsis"><i class="fa fa-angle-right"></i><span id="type_date_<?= $uniqid ?>">Type de date</span></a>
				<div class="contextmenu" style="display:none;">
					<? foreach ($ARR_GROUP_FIELD as $key => $val) {
						$arrg = $val['group'];
						$arrf = $val['field'];
						?>
						<div class="applinkblock applink">
							<? foreach ($arrf as $keyf => $valf) {
								$valdate = $valf['codeAppscheme_field'] . ucfirst($table);
								$nomdate = $valf['nomAppscheme_field'];
								?>
								<a class="autoToggle"
								   onclick="$('type_date_<?= $uniqid ?>').update('<?= $nomdate ?>');$(this.up('.parent_form').querySelector('#deb')).setAttribute('name','vars_date[<?= $valdate ?>]'+'[$gte]');$(this.up('.parent_form').querySelector('#fin')).setAttribute('name','vars_date[<?= $valdate ?>]'+'[$lte]')">
									<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($valf['nomAppscheme_field'])) . ' ' . $table; ?></a>                                <? } ?>
						</div>                        <? } ?>
				</div>
			</div>
		</div>
		<div class="flex_h padding flex_wrap flex_align_middle">
			<div class="flex_h flex_align_middle">
				<div class="padding" style="width:20px;">du</div>
				<div>
					<input class="noborder validate-date-au" placeholder="Date de début" id="deb" type="text" name="vars_date[dateCreation<?= ucfirst($table) ?>][$gte]" value=""/>
				</div>
			</div>
			<div class="flex_h flex_align_middle">
				<div class="padding" style="width:20px;">au</div>
				<div>
					<input class="noborder validate-date-au" placeholder="Date de fin" id="fin" type="text" name="vars_date[dateCreation<?= ucfirst($table) ?>][$lte]" value=""/>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$ ('oi<?=$uniqid?>').observe ('dom:act_click', function (event) {
		$ ('type_periode_<?=$uniqid?>').update (event.memo.value);
		$ ($ ('form<?=$uniqid?>').querySelector ('#deb')).value = event.memo.dateDebut
		$ ($ ('form<?=$uniqid?>').querySelector ('#fin')).value = event.memo.dateFin
	})
</script>