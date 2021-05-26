<?
	include_once($_SERVER['CONF_INC']);

	$idappscheme_type = (int)$_POST['idappscheme_type'];
	$APP_SCH          = new App('appscheme');
	$APP_SCH_TY       = new App('appscheme_type');
	$ARR_TYPE         = $APP_SCH->findOne(['idappscheme_type' => $idappscheme_type]);
	$RSSCHEME         = $APP_SCH->get_schemes(['idappscheme_type' => $idappscheme_type])->sort(['nomAppscheme' => 1]);
?>
<div class=" flex_v toggler applink" style="height:100%;overflow:hidden;">
	<div class="padding flex_h flex_align_middle alignright">
		<div class="padding flex_main alignright uppercase"><?= $ARR_TYPE['nomAppscheme_type'] ?></div>
		<div class="padding alignright"><i class="fa fa-<?= $ARR_TYPE['iconAppscheme_type'] ?> textbold fa-2x"></i></div>
	</div>
	<div class="flex_main" style="overflow:auto;">
		<div style="width:100%;">
			<div class="">
				<? foreach ($RSSCHEME as $sch):
					$table   = $sch['codeAppscheme'];
					$APP_TMP = new App($table);
					?>
					<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
					<div class="margin_more   bordert    flex_align_middle">
						<div class="ellipsis">
							<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_explorer','app_explorer_<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Espace ' . $table) ?>'});"><i
									class="fa fa-<?= $APP_TMP->iconAppscheme ?> " style="color:<?= $APP_TMP->colorAppscheme ?>"></i>
								<span class="bold"> <?= idioma($APP_TMP->app_table_one['nomAppscheme']) ?></span>
							</a>
						</div>
						<div class=" applink retrait alignright">
							<? if (droit_table($_SESSION['idagent'], 'C', $table)) : ?>
								<a class="autoToggle hide_gui_pane" onclick="<?= fonctionsJs::app_create($table, ['idagent' => $_SESSION['idagent']]); ?>"><i class="fa fa-save"></i></a>
							<? endif; ?>
							<? if (droit_table($_SESSION['idagent'], 'L', $table)) : ?>
								<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app/app_explorer','app_explorer_<?= $table ?>','table=<?= $table ?>',{onglet:'<?= idioma('Espace ' . $table) ?>'});"><i
										class="fa fa-home "></i>
								</a>
								<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_prod','table=<?= $table ?>',{onglet:'Production <?= $table ?>'});"><i class="fa fa-folder-open"></i>
								</a>
							<? endif; ?>
							<? if (droit_table($_SESSION['idagent'], 'R', $table)) : ?>
								<a class="autoToggle hide_gui_pane" onclick="ajaxInMdl('app/app_prod/app_prod_search','tmp_exp_prod_search','table=<?= $table ?>&vars[collection]=<?= $table ?>',{onglet:'Recherche rapide <?= $table ?>'});"><i
										class="fa fa-search textgris"></i>
								</a>
							<? endif; ?>
							<? if ($APP_TMP->has_field('dateDebut') && !empty($APP_TMP->app_table_one['hasStatutScheme'])) { ?>
								<a class="autoToggle hide_gui_pane" onclick="<?= fonctionsJs::app_console($table) ?>"><i class="fa fa-dashboard"></i></a>
							<? } ?>
						</div>
					</div>
				<? endif; ?><br>
				<? endforeach; ?>
			</div>
		</div>
	</div>
</div>
