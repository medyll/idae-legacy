<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/09/2015
	 * Time: 10:59
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$APP   = new App();
	$APPG  = new App('agent_groupe');
	$APPGD = new App('agent_groupe_droit');
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars'], "my_array_filter_fn"), 1);

	// droit par groupe
	$ARR_GA = $APPG->findOne($vars);
	$RSGD   = $APPGD->find();

	$rs = $APP->plug('sitebase_app', 'appscheme')->find()->sort(['codeAppscheme' => 1, 'codeAppscheme_base' => 1]);

	$ARR_DR         = ['C' => 'Créer', 'R' => 'Consulter', 'L' => 'Lister', 'U' => 'Modifier', 'D' => 'Supprimer', 'CONF' => 'Confidentiel'];
	$idagent_groupe = (int)$vars['idagent_groupe'];

	$warp = "daidted";
	//
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');

	$ARR_TYPE = $APP_SCH->distinct_all('idappscheme_type');
	$ARR_TYPE = array_values(array_filter($ARR_TYPE));

	$base = 'sitebase_base';
	$RSTYPE     = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $ARR_TYPE]])->sort(['ordreAppscheme_type' => 1,'nomAppscheme_type' => 1]);
	$RSNOSCHEME = $APP_SCH->find(['idappscheme_type' => ['$nin' => $ARR_TYPE]])->sort(['nomAppscheme' => 1]); // , 'codeAppscheme_base' => $base

?>
<div id="<?= $warp ?>">
	<div class="ms-Table" style="position:sticky;top:0px;z-index:1000;">
		<div class="ms-Table-row blanc">
			<div class="ms-Table-cell" style="width:30px;"><i class="fa fa-code"></i></div>
			<div class="ms-Table-cell uppercase bold"><?= idioma('table') ?>
			<br>
			<div><input type="text" onkeyup="quickFind(this.value,'for_<?= $warp ?>','.ms-Table-row')">&nbsp;<i class="fa fa-search"></i> </div>
			</div>
			<?php foreach ($ARR_DR as $k => $v) {
				// $idagent_groupe_droit = $APPGD->create_update(['idagent_groupe' => $idagent_groupe, 'idappscheme' => $idappscheme], ['init' => true]);
				?>
				<div class="ms-Table-cell aligncenter" style="width: 80px;overflow:hidden;">
					<div class="inline borderb ellipsis"><?= $v ?></div>
					<br>
					<input type="checkbox" data-main-ch="<?= $k ?>">
				</div>
			<?php } ?>
		</div>
	</div>
	<div id="for_<?= $warp ?>">
		<div class="ms-Table" style="position:static;">
		<?php while ($arr = $RSTYPE->getNext()) {
			$RSSCHEME = $APP_SCH->get_schemes(['idappscheme_type' => (int)$arr['idappscheme_type']])->sort(['nomAppscheme' => 1]); // , 'codeAppscheme_base' => $base
			?>
				<div class=" ms-Table-row ededed">
					<div class="ms-Table-cell aligncenter" style="width:30px;"></div>
					<div class="ms-Table-cell aligncenter" style="width: 30px;"><i class="fa fa-caret-down"></i></div>
					<div class="ms-Table-cell alignright"><?= $arr['nomAppscheme_type'] ?></div>
					<?php foreach ($ARR_DR as $k => $v) { ?>
						<div class="ms-Table-cell aligncenter" style="width: 80px;">
							<input type="checkbox" data-maingroup-ch="<?= $k ?>" data-type-ch="<?= $arr['codeAppscheme_type'] ?>">
						</div>
					<?php } ?>
				</div>
				<?php foreach ($RSSCHEME as $arr_sch):
					$idappscheme          = (int)$arr_sch['idappscheme'];
					$table    = $arr_sch['codeAppscheme'];
					$APP_TMP = new App($table);
					$test_ins = $APPGD->findOne(['idagent_groupe' => $idagent_groupe, 'idappscheme' => $idappscheme]);

					// entrée dans agent_groupe_droit
					$idagent_groupe_droit = $APPGD->create_update(['idagent_groupe' => $idagent_groupe, 'idappscheme' => $idappscheme], ['init' => true]);

					?>
					<div class="ms-Table-row">
						<div class="ms-Table-cell aligncenter borderr" style="width:30px;">
							<i class="fa fa-<?= $arr_sch['icon'] ?>"></i>
						</div>
						<div class="ms-Table-cell aligncenter" style="width: 30px;">
							<input type="checkbox" data-group-line-ch="<?= $idappscheme ?>">
						</div>
						<div class="ms-Table-cell  borderr">
							<?= $arr_sch['nomAppscheme'] ?>
						</div>
						<?php foreach ($ARR_DR as $k => $v) {
							$key_dr = $k;
							?>
							<div class="ms-Table-cell aligncenter" style="width: 80px;">
								<?php if($key_dr!='CONF'){ ?>
								<input data-droit-value="<?= $idagent_groupe_droit ?>" data-collection="<?= $table ?>" data-collection-value="<?= $idappscheme ?>" data-ch="<?= $k ?>"
								       data-type-ch="<?= $arr['codeAppscheme_type'] ?>" <?= checked($test_ins[$k]) ?> name="<?= $key_dr ?>"
								       type="checkbox" onchange="node_validate(this)">
								<?php }elseif($APP_TMP->has_agent() && $key_dr=='CONF' ) { ?>
									<input data-droit-value="<?= $idagent_groupe_droit ?>" data-collection="<?= $table ?>" data-collection-value="<?= $idappscheme ?>" data-ch="<?= $k ?>"
									       data-type-ch="<?= $arr['codeAppscheme_type'] ?>" <?= checked($test_ins[$k]) ?> name="<?= $key_dr ?>"
									       type="checkbox" onchange="node_validate(this)">
									<?php
								}?>
							</div>
						<?php } ?>
					</div>
				<?php endforeach; ?>
		<?php } ?>
			<div class=" ms-Table-row ededed">
				<div class="ms-Table-cell aligncenter" style="width:30px;"></div>
				<div class="ms-Table-cell aligncenter" style="width: 30px;"><i class="fa fa-caret-down"></i></div>
				<div class="ms-Table-cell alignright"><?=idioma('Sans type') ?></div>
				<?php foreach ($ARR_DR as $k => $v) { ?>
					<div class="ms-Table-cell aligncenter" style="width: 80px;">
						<input type="checkbox" data-maingroup-ch="<?= $k ?>" data-type-ch="NOTYPE">
					</div>
				<?php } ?>
			</div>
			<?php foreach ($RSNOSCHEME as $arr_sch):
				$idappscheme          = (int)$arr_sch['idappscheme'];
				$table    = $arr_sch['codeAppscheme'];
				$APP_TMP = new App($table);
				$test_ins = $APPGD->findOne(['idagent_groupe' => $idagent_groupe, 'idappscheme' => $idappscheme]);

				// entrée dans agent_groupe_droit
				$idagent_groupe_droit = $APPGD->create_update(['idagent_groupe' => $idagent_groupe, 'idappscheme' => $idappscheme], ['init' => true]);
				?>
				<div class="ms-Table-row">
					<div class="ms-Table-cell aligncenter borderr" style="width:30px;">
						<i class="fa fa-<?= $arr_sch['icon'] ?>"></i>
					</div>
					<div class="ms-Table-cell aligncenter" style="width: 30px;">
						<input type="checkbox" data-group-line-ch="<?= $idappscheme ?>">
					</div>
					<div class="ms-Table-cell  borderr">
						<?= $arr_sch['nomAppscheme'] ?>
					</div>
					<?php foreach ($ARR_DR as $k => $v) {
						$key_dr = $k;
						?>
						<div class="ms-Table-cell aligncenter" style="width: 80px;">
							<?php if($key_dr!='CONF'){ ?>
								<input data-droit-value="<?= $idagent_groupe_droit ?>" data-collection="<?= $table ?>" data-collection-value="<?= $idappscheme ?>" data-ch="<?= $k ?>"
								       data-type-ch="NOTYPE" <?= checked($test_ins[$k]) ?> name="<?= $key_dr ?>"
								       type="checkbox" onchange="node_validate(this)">
							<?php }elseif($APP_TMP->has_agent() && $key_dr=='CONF' ) { ?>
								<input data-droit-value="<?= $idagent_groupe_droit ?>" data-collection="<?= $table ?>" data-collection-value="<?= $idappscheme ?>" data-ch="<?= $k ?>"
								       data-type-ch="NOTYPE" <?= checked($test_ins[$k]) ?> name="<?= $key_dr ?>"
								       type="checkbox" onchange="node_validate(this)">
								<?php
							}?>
						</div>
					<?php } ?>
				</div>

			<?php endforeach; ?>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, $A, .on, .select, .invoke, .each, .readAttribute) to native DOM,
	 * with file-local `dr_` helpers.
	 *
	 * doCheck / doUnCheck are NOT shim calls and stay as-is: engine/methods.js
	 * installs both directly on HTMLElement.prototype.
	 */
	function dr_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#select, as a real Array. */
	function dr_select(ref, selector) {
		var node = dr_el(ref);
		if (!node) return [];
		return Array.prototype.slice.call(node.querySelectorAll(selector));
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function dr_on(root, eventName, selectorOrHandler, maybeHandler) {
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
	 * `.invoke(name)` over a node list. The interpolated attribute values in
	 * every selector below are quoted: these are ids and permission codes, so
	 * one starting with a digit makes the selector invalid CSS and native
	 * querySelectorAll throws SyntaxError. The shim's $$/select retried with
	 * quotes added; nothing does that here.
	 */
	function dr_invoke(nodes, methodName) {
		nodes.forEach(function (node) { node[methodName](); });
		return nodes;
	}

	dr_on(dr_el('<?=$warp?>'), 'click', 'input[type=checkbox][data-group-line-ch]', function (event, node) {

		var ch = node.checked ? 'doCheck' : 'doUnCheck';
		var datach = node.getAttribute('data-group-line-ch');

		var targets = dr_select('<?=$warp?>', 'input[type=checkbox][data-collection-value="' + datach + '"]');
		dr_invoke(targets, ch);
		targets.forEach(function (danode) {
			node_validate(danode)
		});

	});
	dr_on(dr_el('<?=$warp?>'), 'click', 'input[type=checkbox][data-main-ch]', function (event, node) {

		var ch = node.checked ? 'doCheck' : 'doUnCheck';
		var datach = node.getAttribute('data-main-ch');

		var targets = dr_select('for_<?=$warp?>', 'input[type=checkbox][data-ch="' + datach + '"]');
		dr_invoke(targets, ch);
		dr_invoke(dr_select('for_<?=$warp?>', 'input[type=checkbox][data-maingroup-ch="' + datach + '"]'), ch);
		targets.forEach(function (danode) {
			node_validate(danode)
		});

	});
	dr_on(dr_el('<?=$warp?>'), 'click', 'input[type=checkbox][data-maingroup-ch]', function (event, node) {

		var ch = node.checked ? 'doCheck' : 'doUnCheck';
		var datach = node.getAttribute('data-maingroup-ch');
		var datype = node.getAttribute('data-type-ch');

		var targets = dr_select('for_<?=$warp?>', 'input[type=checkbox][data-ch="' + datach + '"][data-type-ch="' + datype + '"]');
		dr_invoke(targets, ch);
		targets.forEach(function (danode) {
			node_validate(danode)
		});

	});
	node_validate = function (node) {
		var table = node.getAttribute('data-collection');
		var table_value = node.getAttribute('data-collection-value');
		var table_droit_value = node.getAttribute('data-droit-value');
		var data_ch = node.getAttribute('data-ch');
		console.log(table, table_value, table_droit_value, data_ch);
		// '&vars[idappscheme]=' + table_value +
		ajaxValidation('app_update', 'mdl/app/', 'table=agent_groupe_droit&table_value=' + table_droit_value + '&vars[idappscheme]=' + table_value + '&vars[' + data_ch + ']=' + (node.checked == true));
	}
</script>