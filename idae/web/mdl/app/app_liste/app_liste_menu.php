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

	$id  = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	$vars         = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy      = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy       = empty($_POST['sortBy']) ? $nom : $_POST['sortBy'];
	$sortDir      = empty($_POST['sortDir']) ? 1 : (int)$_POST['sortDir'];
	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	//
	$APP = new App($table);
	//
	$APP_TABLE         = $APP->app_table_one;
	$ARR_GROUP_FIELD   = $APP->get_field_group_list();
	$GRILLE_FK         = $APP->get_grille_fk();
	$ARR_VARS         =  ['vars'=>$vars];
	$HTTP_VARS         = $APP->translate_vars($vars);
	$HTTP_VARS_NOAGENT = $APP->translate_vars($vars_noagent);
	//
	$arr_sort       = $APP->get_field_list_raw();
	$ARR_BASE_VARS  = array_filter(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]);
	$HTTP_BASE_VARS = http_build_query(array_filter(['table' => $table, 'sortBy' => $sortBy, 'sortDir' => $sortDir, 'groupBy' => $groupBy]));
	//
	$settings_button_group = empty($groupBy) ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_group', $table) : $groupBy;
	$settings_button_sort  = empty($sortBy) ? $APP->get_settings($_SESSION['idagent'], 'list_data_button_sort', $table) : $sortBy;

	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));

?>
<div class="boxshadow padding relative toggler applink">
	<div class="flex_h   flex_margin padding flex_align_middle">
		<? if (!empty($table) && droit_table($_SESSION['idagent'], 'C', $table)): ?>
			<div class="aligncenter blanc borderr">
				<a onclick="<?= fonctionsJs::app_create($table, $_POST) ?>" class="ellipsis appbutton bold">
					<i class="fa fa-copy textbleu"></i> <?= idioma('Créer') . ' ' . $APP->nomAppscheme ?>
				</a>
			</div>
		<? endif; ?>
		<div class="">
			<a class="autoToggle none" app_button="app_button" onclick="save_settings('list_data_button_nbRows_<?= $table ?>','<?= $_table ?>');" vars="<?= $HTTP_BASE_VARS ?>&nbRows=5000&<?= $HTTP_VARS ?>">
				5000
			</a>
			<? if (!empty($APP_TABLE['hasTypeScheme']) || sizeof($GRILLE_FK) != 0):
				$css = (!empty($settings_button_group)) ? 'active' : '';
				?>
				<a data-menu="data-menu" class="<?= $css ?> aligncenter ellipsis">
					<i class="fa fa-database" style="color: #BEAC8B"></i>
					Grouper <?= $settings_button_group ?>
				</a>
				<div class="toggler boxshadow   contextmenu applinkblock hide_on_click" style="display:none;z-index:1000;">
					<? foreach ($arr_sort as $key => $value):
						$url_vars  = http_build_query(array_merge($ARR_BASE_VARS,$ARR_VARS,['groupBy'=> $value['codeAppscheme_field']]));
						?>
						<a data-button_group="<?=$key ?>" onclick="save_settings('list_data_button_group_<?= $table ?>','<?= $value['codeAppscheme_field'] ?>');" class="autoToggle" app_button="app_button" vars="<?= $url_vars ?>">
							<?= $value['nomAppscheme_field'] ?></a>                <? endforeach; ?>
					<hr>
					<?
						$arr_has = ['statut', 'type', 'categorie', 'groupe', 'group'];
						foreach ($arr_has as $key => $value):
							$Value  = ucfirst($value);
							$_table = $table . '_' . $value;
							if (!empty($APP_TABLE['has' . $Value . 'Scheme'])):
								$css = ($settings_button_group == $value) ? 'active' : '';
								$url_vars  = http_build_query(array_merge($ARR_BASE_VARS,$ARR_VARS,['groupBy'=>$_table]));
								?>
								<a data-button_group="<?=$_table?>" class="autoToggle <?= $css ?>" app_button="app_button" onclick="save_settings('list_data_button_group_<?= $table ?>','<?= $_table ?>');"
								   vars="<?=$url_vars?>">
									<?= $value ?>
								</a>
							<? endif; ?>
						<? endforeach; ?>
					<? if (sizeof($GRILLE_FK) != 0):
						foreach ($GRILLE_FK as $fk):
							$css = ($settings_button_group == $fk['table_fk']) ? 'active' : '';
							$url_vars  = http_build_query(array_merge($ARR_BASE_VARS,$ARR_VARS,['groupBy'=>$fk['table_fk']]));
							?>
							<a data-button_group="<?=$fk['table_fk']?>" onclick="save_settings('list_data_button_group_<?= $table ?>','<?= $fk['table_fk'] ?>');" class="autoToggle <?= $css ?>" app_button="app_button"
							   vars="<?= $url_vars ?>">
								<?= $fk['table_fk'] ?> </a>
						<? endforeach;
					endif;
						$url_vars  = http_build_query(array_merge($ARR_BASE_VARS,$ARR_VARS,['groupBy'=>null]));
					?>
					<a data-button_group="" onclick="save_settings('list_data_button_group_<?= $table ?>','');" class="autoToggle" app_button="app_button" vars="<?=$url_vars?>">
						ne plus grouper
					</a>
				</div>            <? endif; ?>
		</div>
		<div class="borderl flex_h flex_align_middle">
			<a class=" aligncenter ellipsis" data-menu="data-menu">
				<i class="fa fa-unsorted" style="color: #BEAC8B"></i>
				Trier par <?= $arr_sort[$sortBy]['nomAppscheme_field'] ?>
			</a>
			<div class="absolute   contextmenu applinkblock hide_on_click" style="display:none;z-index:1000;">
				<? foreach ($ARR_GROUP_FIELD as $key => $val) {
					$arrg = $val['group'];
					$arrf = $val['field'];
					?>
					<div class="borderb">
						<? foreach ($arrf as $keyf => $valf) {
							?>
							<a data-button_sort="<?= $valf['codeAppscheme_field'] . $Table ?>" class="autoToggle" onclick="save_settings('list_data_button_sort_<?= $table ?>','<?= $valf['codeAppscheme_field'] . $Table ?>');" app_button="app_button"
							   vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= $valf['codeAppscheme_field'] . $Table ?>&<?= $HTTP_VARS ?>">
								<?= ucfirst($valf['nomAppscheme_field']) ?>  </a>                        <? } ?>
					</div>                <? } ?>

				<? if (sizeof($GRILLE_FK) != 0): ?><? foreach ($GRILLE_FK as $fk):
					?>
					<a data-button_sort="<?=$fk['table_fk'] ?>" onclick="save_settings('list_data_button_sort_<?= $table ?>','<?= 'nom' . ucfirst($fk['table_fk']) ?>');" class="autoToggle" app_button="app_button"
					   vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= 'nom' . ucfirst($fk['table_fk']) ?>&<?= $HTTP_VARS ?>">
						<?= $fk['table_fk'] ?> </a>                    <? endforeach; ?>
					<hr>                <? endif; ?>
				<? foreach ($arr_sort as $key => $value):
					?>
					<a data-button_sort="<?=$key ?>" onclick="save_settings('list_data_button_sort_<?= $table ?>','<?= $key ?>');" class="autoToggle" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortBy=<?= $key ?>&<?= $HTTP_VARS ?>">
						<?= $value['nomAppscheme_field'] ?></a>                <? endforeach; ?>
			</div>
			<div class="flex_h toggler">
				<a data-button_sort_order="1" class="autoToggle bordert" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortDir=<?= (($sortDir == 1) ? '1' : '-1'); ?>&<?= $HTTP_VARS ?>">
					<i class="fa fa-sort-alpha-<?= (($sortDir == 1) ? 'asc' : 'desc'); ?>"></i>
				</a>
				<a data-button_sort_order="-1" class="autoToggle borderb" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&sortDir=<?= (($sortDir == 1) ? '-1' : '1'); ?>&<?= $HTTP_VARS ?>">
					<i class="fa fa-sort-alpha-<?= (($sortDir == 1) ? 'desc' : 'asc'); ?>"></i>
				</a>
			</div>
		</div>
		<div class="borderl flex_h flex_margin flex_align_top">
			<div onclick="this.next().toggle();" class="toggler toggler_visible">
				<a class="autoToggle"><i class="fa fa-check textgrisfonce"></i></a>
				<a class="autoToggle" style="display: none;"><i class="fa fa-check-square"></i></a>
			</div>
			<div style="display:none" class="alignleft flex_h flex_margin flex_align_middle">
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
		<div class="borderl none">
			<a class="ellipsis" expl_view_button="expl_view_button">
				<i class="fa fa-eye"></i>
			</a>
		</div>
		<div>
			<a class="autoToggle none" data-button-className="sortable" onclick="save_settings('list_data_button_className_<?= $table ?>','sortable');">
				sortable
			</a>
		</div>
		<div class="toggler borderl">
			<a class="autoToggle none" data-button_chk="showchk">chk</a>
			<a class="ellipsis" data-menu="data-menu"><i class="fa fa-desktop textbold" style="color: #BEAC8B"></i></a>
			<div class="contextmenu applinkblock" style="display:none;">
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','table');save_settings('list_data_button_dsp_mdl_<?= $table ?>','');" class="autoToggle" data-button-dsp="table">
					<i class="fa fa-table"></i> <?= idioma('Table') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','table_icon');" class="autoToggle" data-button-dsp="table_icon">
					<i class="fa fa-list "></i> <?= idioma('table icone') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','thumb');save_settings('list_data_button_dsp_mdl_<?= $table ?>','');" class="autoToggle" data-button-dsp="thumb">
					<i class="fa fa-list "></i> <?= idioma('thumb') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','table');save_settings('list_data_button_dsp_mdl_<?= $table ?>','');" class="autoToggle none" data-button-dsp="table_line">
					<i class="fa fa-list "></i> <?= idioma('Ligne detail') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','line_fk');save_settings('list_data_button_dsp_mdl_<?= $table ?>','');" class="autoToggle" data-button-dsp="line_fk">
					<i class="fa fa-list "></i> <?= idioma('line_fk') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','flex_line');save_settings('list_data_button_dsp_mdl_<?= $table ?>','');" class="autoToggle" data-button-dsp="flex_line">
					<i class="fa fa-columns"></i> <?= idioma('Colonnes') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_forward');" class="autoToggle none" data-button-dsp="mdl"
				   data-dsp-mdl="app/app/app_fiche_forward">
					<i class="fa fa-columns"></i> <?= idioma('fiche verticale') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_icone');" class="autoToggle" data-button-dsp="mdl"
				   data-dsp-mdl="app/app/app_fiche_icone">
					<i class="fa fa-desktop"></i> <?= idioma('icone') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','mdl');save_settings('list_data_button_dsp_mdl_<?= $table ?>','app/app/app_fiche_entete');" class="autoToggle" data-button-dsp="mdl"
				   data-dsp-mdl="app/app/app_fiche_entete_arbo">
					<i class="fa fa-list-alt "></i> <?= idioma('fiche') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','line');" class="autoToggle none" data-button-dsp="line">
					<i class="fa fa-list "></i> <?= idioma('Simple') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','icon');" class="autoToggle" data-button-dsp="icon">
					<i class="fa fa-list "></i> <?= idioma('Icone') ?>
				</a>
				<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','fields');" class="autoToggle" data-button-dsp="fields">
					<i class="fa fa-align-left "></i> <?= idioma('fields') ?>
				</a>
				<? if ($APP->hasImageScheme) { ?>
					<hr>
					<a onclick="save_settings('list_data_button_dsp_<?= $table ?>','image');" class="autoToggle" data-button-dsp="image">
						<i class="fa fa-align-left "></i> <?= idioma('Images') ?>
					</a>
				<? } ?>
			</div>
		</div>
		<? if (droit('DEV')) { ?>
			<div>
				<?= skelMdl::cf_module('app/app_prod/app_prod_liste_menu_export', $_POST) ?>
			</div>
		<? } ?>
		<? if (!empty($TEST_AGENT)): ?>
			<div class="toggler toggler_visible ededed border4 flex_h">
				<a class="autoToggle textvert" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&<?= $HTTP_VARS ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>"><i class="fa fa-user"></i></a>
				<a class="autoToggle textorange" app_button="app_button" vars="<?= $HTTP_BASE_VARS ?>&<?= $HTTP_VARS_NOAGENT ?>"><i class="fa fa-globe"></i></a>
			</div>
		<? endif; ?>
		<div class="flex_main"></div>
		<div class="flex_h flex_align_middle borderl borderr">
			<div class="border4 margin flex_h flex_align_middle ededed"><i class="fa fa-search textbold"></i>
				<input class="noborder" placeholder="Rechercher" expl_search_button="expl_search_button" style="width:120px;" type="text">
			</div>
			<div class="ellipsis">
				<a class="flex_h flex_align_middle" onclick="$('search_more_<?= $table ?>').toggleContent();"><i class="fa fa-search-plus textvert"></i> plus ..</a>
			</div>
		</div>
		<div><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', $_POST) ?></div>
	</div>
	<div class="flex_h" id="search_more_<?= $table ?>" style="display:none;">
		<div class="flex_main" style="z-index:500;" data-cache="true" act_defer mdl="app/app_prod/app_prod_liste_menu_search" vars="<?= http_build_query($_POST) ?>"></div>
		<div class="border4 ededed aligncenter">
			<a onclick="$('search_more_<?= $table ?>').unToggleContent();"><i class="fa fa-times textrouge"></i></a>
		</div>
	</div>
</div>