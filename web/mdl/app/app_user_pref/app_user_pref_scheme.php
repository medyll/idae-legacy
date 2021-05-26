<?
	include_once($_SERVER['CONF_INC']);


	$vars       = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$code       = (empty($_POST['code'])) ? 'app_search' : $_POST['code'];
	$page       = (empty($_POST['page'])) ? 0 : $_POST['page'];
	$nbRows     = (empty($_POST['nbRows'])) ? 5 : $_POST['nbRows'];
	$APP        = new App();
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');

	$ARR_TYPE = $APP_SCH->distinct_all('idappscheme_type');
	$ARR_TYPE = array_values(array_filter($ARR_TYPE));

	$RSTYPE     = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $ARR_TYPE]])->sort(['nomAppscheme_type' => 1]);
	$RSNOSCHEME = $APP_SCH->find(['idappscheme_type' => ['$nin' => $ARR_TYPE]])->sort(['nomAppscheme' => 1]);

	switch($code):

		case 'app_search':
			$titre_zone = 'Recherche rapide';
			break;
		default:
		case 'app_panel':
			$titre_zone = 'Panneau latéral droit';
			break;
		default:
		case 'app_menu':
			$titre_zone = 'Panneau latéral gauche';
			break;
		default:
		case 'app_menu_start':
			$titre_zone = 'Menu démarrer, accés aux espaces';
			break;
		default:
			$titre_zone = $code;
			break;
	endswitch;
?>
<div class="flex_v" style="height: 100%;">
	<div class="titre_entete bold">
		<?= idioma('Préférences') ?> : <?=idioma($titre_zone) ?>
	</div>
	<div class="padding_more  ">
		<input data-quickFind=""   data-quickFind-tag=".autoBlock" data-quickFind-parent=".sparent" type="text"/>
	</div>
	<div id="user_pref_ch_<?=$table?>" class="flex_main applink applinkblock blanc" style="overflow-y:auto;overflow-x:hidden;">
		<div main_auto_tree>
			<? while ($arr = $RSTYPE->getNext()) {
				$RSSCHEME = $APP_SCH->get_schemes(['idappscheme_type' => (int)$arr['idappscheme_type']])->sort(['nomAppscheme' => 1]);
				?>
				<div class="sparent">
				<div auto_tree class="padding margin borderb">
					<label class="bold flex flex_h">
						<span class="flex_main"><i class="fa fa-<?= $arr['iconAppscheme_type'] ?>"></i> <?= $arr['nomAppscheme_type'] ?></span>
						<span>
							<input type="checkbox" onclick="save_setting_mdl_search (this,'<?= $code ?>_<?= $arr['codeAppscheme_type'] ?>','<?= $arr['codeAppscheme_type'] ?>');"
						             value="1" <?= checked($APP->get_settings($_SESSION['idagent'], $code . '_' . $arr['codeAppscheme_type'])); ?>></span>
					</label>
				</div>
				<div class="autoBlock flex_h flex_wrap flex_align_top">
					<? foreach ($RSSCHEME as $sch):
						$table = $sch['codeAppscheme'];
						if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;
						if (!droit_table($_SESSION['idagent'], 'C', $table) && $code == 'app_menu_create') continue;
						/*if (str_find($table, '_statut')) continue;
						if (str_find($table, '_type')) continue;
						if (str_find($table, '_ligne')) continue;
						if (str_find($table, '_categorie')) continue;*/
						?>
						<label style="width:33%;max-width:33%;">
							<input type="checkbox" onclick="save_setting_mdl_search (this,'<?= $code ?>_<?= $table ?>','<?= $table ?>');"
							       value="1" <?= checked($APP->get_settings($_SESSION['idagent'], $code . '_' . $table)); ?>>
							&nbsp;<?= ucfirst($sch['nomAppscheme']) ?>
						</label>
					<? endforeach; ?>
				</div>
				</div>
			<? } ?>
			<div auto_tree class="padding margin borderb">
				<label class="bold"><?= idioma('Non classés') ?></label>
			</div>
			<div class="autoBlock flex_h flex_wrap flex_align_top">
				<? foreach ($RSNOSCHEME as $sch):
					$table = $sch['codeAppscheme'];
					if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;
					if (!droit_table($_SESSION['idagent'], 'C', $table) && $code == 'app_menu_create') continue;
					/*if (str_find($table, '_statut')) continue;
					if (str_find($table, '_type')) continue;
					if (str_find($table, '_ligne')) continue;
					if (str_find($table, '_categorie')) continue;*/
					?>
					<label style="width:33%;max-width:33%;">
						<input type="checkbox" onclick="save_setting_mdl_search (this,'<?= $code ?>_<?= $table ?>','<?= $table ?>');"
						       value="1" <?= checked($APP->get_settings($_SESSION['idagent'], $code . '_' . $table)); ?>>
						&nbsp;<?= ucfirst($sch['nomAppscheme']) ?>
					</label>
				<? endforeach; ?>
			</div>
		</div>
	</div>
</div>
<script>
	save_setting_mdl_search = function (node, key) {

		setTimeout(function () {
			dsp = $(node).checked;
			ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + dsp);
			setTimeout(function () {
				reloadScope('<?=$code?>', '*');
			}, 1500)
		}.bind(this), 100)
	}
</script>