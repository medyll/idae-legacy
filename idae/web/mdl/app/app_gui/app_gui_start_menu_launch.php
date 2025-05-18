<?
	include_once($_SERVER['CONF_INC']);

	$table = $_POST['table'];
	$Table = ucfirst($table);;
	$APP_TMP     = new App($table);
	$idappscheme = $table_value = (int)$APP_TMP->idappscheme;

	//   VAR BANG :$NAME_APP ,  $ARR_GROUP_FIELD ,  $APP_TABLE, $GRILLE_FK, $R_FK, $HTTP_VARS;
	$EXTRACTS_VARS = $APP_TMP->extract_vars($table_value);
	extract($EXTRACTS_VARS, EXTR_OVERWRITE);

	$rgba_table = implode(',', hex2rgb($APP_TMP->colorAppscheme, 0.5));

?>
<div class=" flex_v   toggler applink" style="background: -moz-linear-gradient(top, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.9) 33%, rgba(<?= $rgba_table ?>) 100%);height:100%;">
	<div>
		<div style="width:100%;">
			<br>
			<? if (droit_table($_SESSION['idagent'], 'C', $table)) : ?>
				<div class="alignright padding uppercase  " style="border-color:<?= $ICON_COLOR ?>">
					<a class="autoToggle hide_gui_pane margin border4" onclick="<?= fonctionsJs::app_create($table, ['idagent' => $_SESSION['idagent']]); ?>">
						<i class="fa fa-save textbold" style="color:<?= $APP_TMP->colorAppscheme ?>"></i> <?= idioma('Créer ' . $table) ?></a>
				</div>
			<? endif; ?>
			<div class="flex_h">
				<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
					<div class=" demi">
						<div  class="applinkblock">
							<div class="appmetro aligncenter flex_align_bottom">
								<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_explorer','app_explorer_<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Espace ' . $table) ?>'});"><i
										class="fa fa-<?= $APP_TMP->iconAppscheme ?> fa-3x"></i>

									<br>
									Espace <?= idioma($APP_TMP->nomAppscheme) ?> </a>
							</div>
						</div>
					</div>
				<? endif; ?>
				<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
					<div class=" demi">
						<div  class="applinkblock">
							<div class="appmetro aligncenter">
								<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod_search','tmp_exp_prod_search','table=<?= $table ?>&vars[collection]=<?= $table ?>',{onglet:'Recherche rapide <?= $table ?>'});"><i
										class="fa fa-search fa-3x"></i>

									<br>
									Recherche rapide
								</a>
							</div>
						</div>
					</div>
				<? endif; ?>
			</div>
			<br>
			<div class="appmetro ">
				<div class="flex_h flex_wrap flex_align_top">
					<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
						<div class="demi">
							<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_prod','table=<?= $table ?>',{onglet:'Production <?= $table ?>'});"><i class="fa fa-folder-open"></i> Parcourir
							</a>
						</div>
					<? endif; ?>
					<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
						<div class=" ">
							<a class=" autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_compare','app_compare<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Comparaison ' . $table) ?>'});"><i
									class="fa fa-list-alt"></i> Comparer
							</a>
						</div>
					<? endif; ?>
					<? if (droit('ADMIN')) : ?>
						<div class="demi">
							<a class="   autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_dispatch','app_dispatch<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Tri  ' . $table) ?>'});"><i
									class="fa fa-list-alt"></i> Trier
							</a>
						</div>
					<? endif; ?>
					<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
						<? if ($APP_TMP->has_field('dateDebut') && !empty($APP_TMP->app_table_one['hasStatutScheme'])) { ?>
							<div class="demi mce-i-align none">
							<a class="  autoToggle hide_gui_pane" onclick="<?= fonctionsJs::app_console($table) ?>"><i class="fa fa-dashboard"></i><?= idioma('console') . ' ' . $table ?></a>
							</div><? } ?>
					<? endif; ?>
					<a class="demi flex_main autoToggle hide_gui_pane  none" onclick="<?= fonctionsJs::app_mdl('app/app_img/app_img', ['table' => $table]) ?>"><i class="fa fa-dashboard"></i><?= idioma('images') . ' ' . $table ?></a>
				</div>
			</div>
			<br>
			<? if (droit('DEV') && !empty($APP_TMP->app_table_one['hasStatutScheme'])) {
				$table_statut = $table . '_statut';
				$Table_statut = $Table . '_statut';
				$APP_STATUT   = new App($table_statut);
				$rs_statut    = $APP_STATUT->find()->sort(['ordre' . $Table_statut => 1]);
				?>
				<div class="appmetro applinkblock">
					<?
						$addvarsagent     = '';
						$arr_addvarsagent = [];
						if ($APP_TMP->has_agent()):
							$addvarsagent                = 'vars[idagent]=' . $_SESSION['idagent'] . '&';
							$arr_addvarsagent['idagent'] = (int)$_SESSION['idagent'];
						endif;
						while ($arr_statut = $rs_statut->getNext()) {
							$rs_tot = $APP_TMP->find($arr_addvarsagent + ['id' . $table_statut => (int)$arr_statut['id' . $table_statut]]);
							?>
							<a class="autoToggle flex hide_gui_pane flex_h"
							   onclick="ajaxInMdl('app/app_liste/app_liste','app_explorer_<?= $table ?>','<?= $addvarsagent ?>table=<?= $table ?>&vars[id<?= $table_statut ?>]=<?= $arr_statut['id' . $table_statut] ?>',{onglet:'<?= idioma('Liste de ' . $table . ' ' . $arr_statut['nom' . $Table_statut]) ?>'});">
								<span class=""><i class="fa fa-<?= $arr_statut['icon' . $Table_statut] ?>"></i> <?= $arr_statut['nom' . $Table_statut] ?></span>
								<span class="flex_main alignright"><?= $rs_tot->count(); ?></span>
							</a>
						<? } ?>
				</div>
				<br>
			<? } ?>
		</div>
	</div>
	<div class="padding_more" >
		<div act_defer mdl="app/app_gui/app_gui_panel" vars="table=<?= $table ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>" value="<?= $table ?>"></div>
	</div>
</div>
