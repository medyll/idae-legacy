<?php
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
<!--<input type="hidden" name="table" value="<?php /*= $table */ ?>">-->
<div id="form<?= $uniqid ?>" class="parent_form flex_h flex_align_middle   boxshadow border4" main_auto_tree>
	<div class="flex_v  flex_main padding">
		<div class="flex_h  flex_align_middle borderb">
			<div class="padding margin borderr aligncenter"><i class="fa fa-calendar-o fa-2x textbold"></i></div>
			<div class="applink applinkblock flex_main borderr" style="overflow:hidden">
				<a data-menu="data-menu" data-clone="true" class="ellipsis"><i class="fa fa-angle-right"></i><span id="type_date_<?= $uniqid ?>">Type de date</span></a>
				<div class="contextmenu" style="display:none;">
					<div class="applink applinkblock">
						<?php foreach ($APP_DATE_FIELD as $valdate => $valf) {
							$nomdate  = $valf['nomAppscheme_field'];
							$codedate = $valf['codeAppscheme_field'] . $Table;
							?>
							<a class="autoToggle ellipsis"
							   onclick="nd_el('type_date_<?= $uniqid ?>').innerHTML = '<?= $nomdate . ' ' . $table ?>';nd_el('form<?= $uniqid ?>').querySelector('#<?= $deb ?>').setAttribute('name','vars_date[<?= $codedate ?>]'+'[$gte]');nd_el('form<?= $uniqid ?>').querySelector('#<?= $fin ?>').setAttribute('name','vars_date[<?= $codedate ?>]'+'[$lte]')">
								<i class="fa fa-<?= $valf['iconAppscheme_field'] ?>"></i> <?= ucfirst(idioma($nomdate)) . ' ' . $table; ?></a>                                <?php } ?>
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
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .select, .first, .update, .add/removeClassName) to native DOM,
	 * with file-local `nd_` helpers and serializeFields, covered by
	 * playwright/tests/form-serialize.spec.ts.
	 */
	function nd_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener — a
	 * two-argument .on() was never delegation, the shim fell through to
	 * Event.observe.
	 */
	function nd_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (!root) return;
		if (maybeHandler === undefined) {
			root.addEventListener(eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	/**
	 * `#refresh_nav` / `#refresh_nav_btn` are literal ids, not keyed off
	 * $uniqid, so several instances of this module share them. Every lookup
	 * below is scoped to this instance's form — which is what
	 * `$(form).select('#x').first()` did, and why replacing it with a bare
	 * document.getElementById would silently drive another instance's button.
	 */
	function nd_in(scopeId, selector) {
		var scope = nd_el(scopeId);
		return scope ? scope.querySelector(selector) : null;
	}

	nd_on(document.body, 'dom:act_click', '#select_periode<?=$uniqid?>', function (event) {

		var nav = nd_in('form<?= $uniqid ?>', '#refresh_nav');
		nav.classList.remove('bounce');
		nav.classList.remove('animated');

		nd_el('type_periode_<?=$uniqid?>').innerHTML = event.memo.value;
		nd_in('form<?=$uniqid?>', '#<?=$deb?>').value = event.memo.dateDebut
		nd_in('form<?=$uniqid?>', '#<?=$fin?>').value = event.memo.dateFin

		nd_in('form<?= $uniqid ?>', '#refresh_nav_btn').setAttribute('vars', serializeFields(nd_el('form<?=$uniqid?>')))
		// Prototype's addClassName appended the raw string, which the browser
		// then read as two class tokens; classList.add takes them separately.
		nav.classList.add('animated', 'bounce');
	})
	nd_on(nd_el('form<?= $uniqid ?>'), 'dom:act_change', function () {

		var nav = nd_in('form<?= $uniqid ?>', '#refresh_nav');
		nav.classList.remove('bounce');
		nav.classList.remove('animated');

		var fin = nd_in('form<?=$uniqid?>', '#<?=$fin?>');
		if (fin.value == '') fin.value = nd_in('form<?=$uniqid?>', '#<?=$deb?>').value;
		nd_in('form<?= $uniqid ?>', '#refresh_nav_btn').setAttribute('vars', serializeFields(nd_el('form<?=$uniqid?>')));

		nav.classList.add('animated', 'bounce');
	})
</script>
