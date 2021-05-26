<?
	include_once($_SERVER['CONF_INC']);
	$APP     = new App('appscheme');
	$arr_tbl = ['client', 'prospect', 'contact', 'tache', 'affaire', 'financement', 'contrat', 'opportunite', 'intervention', 'materiel'];

	$RSSCHEME = $APP->find([])->sort(['nomAppscheme' => 1]); // 'codeAppscheme_base'=>'sitebase_base'
	//
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');
	$APP_GD     = new App('agent_groupe_droit'); // verification des droits utilisateur // code =  $code.'_'.$table

	$arr_table_R = $APP_GD->distinct_all('codeAppscheme', ['R' => true, 'idagent_groupe' => (int)$_SESSION['idagent_groupe']]);

	$ARR_TYPE = $APP_SCH->distinct_all('idappscheme_type', ['codeAppscheme' => ['$in' => $arr_table_R]]);
	$ARR_TYPE = array_values(array_filter($ARR_TYPE));

	$RSTYPE     = $APP_SCH_TY->find(['idappscheme_type' => ['$nin' => [null, 0, '']]])->sort(['nomAppscheme_type' => 1]);
	$RSNOSCHEME = $APP_SCH->find(['idappscheme_type' => ['$in' => [null, 0, '']]])->sort(['nomAppscheme' => 1]);


	$arr_sch      = $APP_GD->distinct_all('idappscheme', ['idagent_groupe' => (int)$_SESSION['idagent_groupe'],  'R' => true]);
	$arr_sch_type = $APP_SCH->distinct_all('idappscheme_type', ['idappscheme' => ['$in' => $arr_sch]]);
	$RS_TY        = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $arr_sch_type]])->sort(['nomAppscheme_type' => 1]);

?>
<div class="blanc" style="height: 100%;width:100%;">
	<div class="ms-font-l padding alignright">
		Production
	</div>
	<div main_auto_tree auto_tree_accordeon="true" class="applinkblock applink panel_entete flex_main flex_v  " style="overflow:auto;">
		<? while ($arr = $RS_TY->getNext()) {
			$has      = 0;
			$RSSCHEME = $APP_SCH->get_schemes(['idappscheme_type' => (int)$arr['idappscheme_type']])->sort(['nomAppscheme' => 1]); // , 'codeAppscheme_base' => $sitebase_base

			?>
			<div auto_tree right="right" class="borderb blanc" style="line-height:3;z-index:10">
				<div auto_tree_click="true" class="flex_h flex_align_middle cursor" >
					<span style="width:25px;" class="aligncenter padding margin borderr"><i class="fa fa-<?= $arr['iconAppscheme_type'] ?> textenoir"></i></span>
					<span class="flex_main      "><?= $arr['nomAppscheme_type'] ?></span>
				</div>
			</div>
			<div class="flex_h borderb animated slideInDown    speed" style="display:none;z-index:0;margin-bottom:1.5em;">
				<div class="retrait" style="order:1;">
					<div class="flex_h flex_wrap">
						<? foreach ($RSSCHEME as $sch):
							$table   = $sch['codeAppscheme'];
							$table_name = $sch['nomAppscheme'];
							if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;
							$APP_TMP = new App($table);
							$has     = 1;
							?>
							<div class="demi">
								<a class="flex_h flex autoToggle  " vars="table=<?= $table ?>" mdl="app/app_gui/app_gui_start_menu_launch"
								   act_target="loader_gui_pane">
									<span style="width:25px;color:<?= $APP_TMP->colorAppscheme ?>" class="aligncenter textbold"><i class="fa fa-<?= $APP_TMP->iconAppscheme ?>"></i></span>
									<span class="flex_main ellipsis"><?= ucfirst(idioma($table_name)) ?></span>
								</a>
							</div>
						<? endforeach; ?></div>
				</div>
			</div>
		<? } ?>
	</div>
</div>
