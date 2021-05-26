<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 03:24
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];
	$Table = ucfirst($table);

	$id       = 'id' . $table;
	$nom      = 'nom' . ucfirst($table);
	$id_type  = 'id' . $table . '_type';
	$nom_type = 'nom' . ucfirst($table) . '_type';
	$top      = 'estTop' . ucfirst($table);
	$actif    = 'estActif' . ucfirst($table);

	$vars         = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy      = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy       = empty($_POST['sortBy']) ? $nom : $_POST['sortBy'];
	$sortDir      = empty($_POST['sortDir']) ? 1 : (int)$_POST['sortDir'];
	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	//
	$APP = new App($table);
	//
	$GRILLE_FK         = $APP->get_grille_fk();
	$HTTP_VARS         = http_build_query($_POST) + '&' + $APP->translate_vars($vars);
	$HTTP_VARS_NOAGENT = $APP->translate_vars($vars_noagent);
	//
	$arr_sort       = $APP->get_sort_fields($table);
	$HTTP_BASE_VARS = http_build_query(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);
	//

	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));

?>
<div class="boxshadow padding relative toggler applink">
	<div class="flex_h   flex_margin padding flex_align_middle">
		<div class="aligncenter blanc   ms-fontweight-semibold ms-font-s">
			<?= idioma('liste') ?> <?= $APP->nomAppscheme ?>
		</div>
		<div class="  flex_h flex_margin flex_align_top">
			<div onclick="this.next().toggle();" class="toggler toggler_visible">
				<a class="autoToggle"><i class="fa fa-check textgrisfonce"></i></a>
				<a class="autoToggle" style="display: none;"><i class="fa fa-check-square"></i></a>
			</div>
			<div style="display:none" class="flex_h flex_margin flex_align_middle">
				<div class="disinput border4 blanc" style="overflow:hidden;">
					<a expl_multi_button="expl_multi_button">
						<i class="fa fa-tencent-weibo"></i> <?= idioma('Modifier') ?></a>
					<a expl_multi_delete_button="expl_multi_delete_button">
						<i class="fa fa-times-circle textrouge"></i> <?= idioma('Supprimer') ?></a>
				</div>
				<div class="disinput border4 blanc" style="overflow:hidden;">
					<a class="ellipsis" expl_save_liste_button="expl_save_liste_button">
						<i class="fa fa-save"></i> <?= idioma('Enregistrer') ?></a>
				</div>
			</div>
		</div>
		<? if (!empty($TEST_AGENT)): ?>
			<div class="toggler toggler_visible ededed">
				<a class="autoToggle textvert" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&<?= $HTTP_VARS ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>"><i class="fa fa-user"></i></a>
				<a class="autoToggle textorange" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&<?= $HTTP_VARS_NOAGENT ?>"><i class="fa fa-user-secret"></i></a>
			</div>
		<? endif; ?>
		<div class="flex_main"></div>
		<div class="toggler borderr">
			<a class="autoToggle none" data-button_chk="showchk">chk</a>
		</div>
		<? if (!empty($table)): ?>
			<div class="aligncenter blanc borderr">
				<a onclick="<?= fonctionsJs::app_create($table, $_POST) ?>" class="ellipsis">
					<i class="fa fa-copy textbleu bold"></i> <?= idioma('Créer') . ' ' . $APP->nomAppscheme ?>
				</a>
			</div>
		<? endif; ?>
		<div class="flex_h flex_align_middle   borderr">
			<div class="border4 margin flex_h flex_align_middle ededed"><i class="fa fa-search textbold"></i>
				<input class="noborder" placeholder="Rechercher" expl_search_button="expl_search_button" style="width:120px;" type="text">
			</div>
		</div>
		<div><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', $_POST) ?></div>
	</div>
</div>