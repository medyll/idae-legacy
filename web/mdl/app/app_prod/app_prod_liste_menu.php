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

	$nom      = 'nom' . ucfirst($table);

	$vars    = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? $nom : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? 1 : (int)$_POST['sortDir'];

	//
	$APP = new App($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$ARR_GROUP_FIELD = $APP->get_field_group_list();
	$GRILLE_FK       = $APP->get_grille_fk();
	$HTTP_VARS       = $APP->translate_vars($vars);
	$arrFieldsBool   = $APP->get_array_field_bool();
	//
	$arr_sort       = $APP->get_sort_fields($table);
	$HTTP_BASE_VARS = http_build_query(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);
?>
<div class="padding relative toggler applink">
	<div class="flex_h flex_wrap flex_margin padding" style="width:100%;">
		<? if (!empty($table)): ?>
			<div style="min-width:90px;" class="aligncenter blanc">
				<a onclick="<?= fonctionsJs::app_create($table) ?>" class="ellipsis">
					<i class="fa fa-<?= $APP->iconAppscheme ?> fa-2x"></i>

					<br>
					<i class="fa fa-copy textbleu"></i><?= idioma('Nouveau') ?>
				</a>
			</div>        <? endif; ?>
		<div>
			<? if (!empty($APP_TABLE['hasTypeScheme']) || sizeof($GRILLE_FK) != 0): ?>
				<a data-menu="data-menu" class="aligncenter ellipsis">
					<i class="fa fa-database" style="color: #BEAC8B"></i>

					<br>
					Grouper par <?= $groupBy ?>
				</a>
				<div class="toggler boxshadow   contextmenu applinkblock hide_on_click" style="display:none;z-index:1000;">
					<? if (!empty($APP_TABLE['hasTypeScheme'])): ?>
						<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&groupBy=<?= $table . '_type' ?>&<?= $HTTP_VARS ?>">
							<i class="fa fa-list"></i> <?= $table . '_type' ?></a>                    <? endif; ?>
					<? if (sizeof($GRILLE_FK) != 0): ?>
						<? foreach ($GRILLE_FK as $fk):
							?>
							<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&groupBy=<?= $fk['table_fk'] ?>&<?= $HTTP_VARS ?>">
								<?= $fk['table_fk'] ?> </a>                        <? endforeach; ?><? endif; ?>
					<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&groupBy=&<?= $HTTP_VARS ?>">
						ne plus grouper
					</a>
				</div>            <? endif; ?>
		</div>
		<div class="autoToggle flex_h">
			<a class=" aligncenter ellipsis" data-menu="data-menu">
				<i class="fa fa-unsorted" style="color: #BEAC8B"></i>

				<br>
				Trier par <?= $arr_sort[$sortBy] ?>
			</a>
			<div class="absolute   contextmenu applinkblock hide_on_click" style="display:none;z-index:1000;">
				<? foreach ($ARR_GROUP_FIELD as $key => $val) {
					$arrg = $val['group'];
					$arrf = $val['field'];
					?>
					<div class="borderb">
						<? foreach ($arrf as $keyf => $valf) {
							?>
							<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= $valf['codeAppscheme_field'] . $Table ?>&<?= $HTTP_VARS ?>">
								<?= ucfirst($valf['nomAppscheme_field']) ?>  </a>
						<? } ?>
					</div>
				<? } ?>


				<? if (sizeof($GRILLE_FK) != 0): ?>
					<? foreach ($GRILLE_FK as $fk):
						?>
						<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= 'nom' . ucfirst($fk['table_fk']) ?>&<?= $HTTP_VARS ?>">
							<?= $fk['table_fk'] ?> </a>                    <? endforeach; ?>
					<hr>                <? endif; ?>
				<? foreach ($arr_sort as $key => $value):
					?>
					<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= $key ?>&<?= $HTTP_VARS ?>">
						<?= $value ?></a>                <? endforeach; ?>
			</div>
			<div class="toggler">
				<a class="autoToggle borderb" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortDir=<?= (($sortDir == 1) ? '1' : '-1'); ?>&<?= $HTTP_VARS ?>">
					<i class="fa fa-sort-alpha-<?= (($sortDir == 1) ? 'asc' : 'desc'); ?>"></i>
				</a>
				<a class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortDir=<?= (($sortDir == 1) ? '-1' : '1'); ?>&<?= $HTTP_VARS ?>">
					<i class="fa fa-sort-alpha-<?= (($sortDir == 1) ? 'desc' : 'asc'); ?>"></i>
				</a>
			</div>
		</div>
		<div class="border4 flex_h">
			<? foreach ($arrFieldsBool as $bool => $arr_ico):
				$var = $bool . ucfirst($table);
				(empty($_POST['vars'][$var]));
				?>
				<div class="mastershow relative">
					<a>
						<i class="fa fa-<?= $arr_ico[(empty($_POST['vars'][$var]))] ?>"></i>
					</a>
					<div class="slaveshow absolute blanc applink applinkblock border4" style="s-index:1000;right:80%;top:0">
						<a onclick="return false;" class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&vars[<?= $var ?>]=1&<?= $HTTP_VARS ?>"><i class="fa fa-<?= $arr_ico[0] ?>"></i> <?= $bool ?></a>
						<a onclick="return false;" class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&vars[<?= $var ?>]=0&<?= $HTTP_VARS ?>"><i class="fa fa-<?= $arr_ico[1] ?>"></i> <?= $bool ?></a>
					</div>
				</div>            <? endforeach; ?>
		</div>
		<div class="disinput border4 blanc" style="overflow:hidden;">
			<a expl_multi_button="expl_multi_button">
				<i class="fa fa-tencent-weibo"></i> <?= idioma('Modifier sélection') ?></a>
			<br>
			<a expl_multi_delete_button="expl_multi_delete_button">
				<i class="fa fa-times-circle textrouge"></i> <?= idioma('Supprimer sélection') ?></a>
		</div>
		<div class="disinput border4 blanc" style="overflow:hidden;">
			<a class="ellipsis" expl_save_liste_button="expl_save_liste_button">
				<i class="fa fa-save"></i> <?= idioma('Enregistrer selection') ?></a>
		</div>
		<div>
			<a class="ellipsis" expl_view_button="expl_view_button">
				<i class="fa fa-eye"></i>
				&nbsp; <?= idioma('Visualiser') ?>
			</a>
		</div>
		<div><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', $_POST) ?></div>
	</div>
</div>