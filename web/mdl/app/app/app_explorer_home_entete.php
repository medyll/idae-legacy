<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$table = empty($_POST['table']) ? 'client' : $_POST['table'];

	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	$APP               = new App($table);
	$APP_TABLE         = $APP->app_table_one;
	$HTTP_VARS         = $APP->translate_vars($vars);
	$dateStart         = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');

	$zone = uniqid($table);

	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		// $vars['idagent']=(int)$_SESSION['idagent'];
	endif;

	$arr_rfk = $vars;
	$sort_fk = empty($_POST['sort_fk']) ? 'dateCreation' : $_POST['sort_fk'];
	// vardump($vars);
?>
<div id="<?= $zone ?>" style="width:100%;height:100%;" class=" ">
	<div class="flex_h flex_align_top flex_margin">
		<div class="  padding" style="width:85%;overflow: hidden;">
			<div class="    flex_h ">
				<div class="dashbox " style="min-width:15%;" act_defer mdl="app/app/app_explorer_home_entete_count" vars="<?= http_build_query($_POST) ?>" scope="<?= $scope ?>"></div>
				<div class="dashbox  flex_main" style="overflow: hidden">
					<div class="borderb margin_moreb padding_more  flex_align_middle  padding   flex_h" style="border-color: <?= $APP->colorAppscheme ?>;">
						<div class="border4 boxshadow" style="background-color: <?= $APP->colorAppscheme ?>;width:40px;">&nbsp;</div>
						<div class="padding_morel bold   ms-font-m   ">
							Espace "<?= $APP->nomAppscheme ?>" vues récentes et activité
						</div>
					</div>
					<div style="overflow: hidden;width:100%;" act_defer mdl="app/app/app_explorer_home_entete_last" vars="table=<?= $table ?>&<?= $HTTP_VARS ?>" scope="<?= $scope ?>"></div>
				</div>
			</div>
			<div class="dashbox " main_auto_tree style="">
				<div class="borderb margin_moreb padding_more  flex_align_middle  padding   flex_h" style="border-color: <?= $APP->colorAppscheme ?>;">
					<div class="border4 boxshadow" style="background-color: <?= $APP->colorAppscheme ?>;width:40px;">&nbsp;</div>
					<div class="padding_morel bold   ms-font-m   ">
						<?= idioma('ELEMENTS LES PLUS CONSULTES') ?>
					</div>
				</div>
				<div class="margin padding flex_main">
					<div class="" id="home__more<?= $zone ?>" data-dsp_liste="dsp_lists"
					     data-dsp-css="grid"
					     data-vars="table=agent_history&vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[codeAgent_history]=<?= $table ?>&nbRows=5&sortBy=quantiteAgent_history&sortDir=-1"
					     data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_entete_arbo">
					</div>
				</div>
			</div>
			<div class="dashbox " act_defer mdl="app/app/app_explorer_home_entete_fk" vars="table=<?= $table ?>&<?= $HTTP_VARS ?>" scope="<?= $scope ?>"></div>
		</div>
		<div class="dashbox" style="position:sticky; overflow: hidden;width:15%;">
			<div class="  " style="width:auto;overflow: hidden;" act_defer mdl="app/app_echeance/app_echeance_summary" vars="table=<?= $table ?>&<?= $HTTP_VARS ?>" value="<?= $table ?>" scope="<?= $scope ?>">
			</div>
			<div class=" " act_defer mdl="app/app/app_explorer_home_entete_rfk" vars="<?= http_build_query($_POST) ?>" scope="<?= $scope ?>"></div>
		</div>
	</div>
	<?
		$arr_has = ['statut', 'type', 'categorie', 'groupe', 'group'];
		foreach ($arr_has as $key => $value):
			$Value  = ucfirst($value);
			$_table = $table . '_' . $value;
			$_Table = ucfirst($_table);
			$_id    = 'id' . $_table;
			$_nom   = 'nom' . $_Table;
			$_nom   = 'code' . $_Table;
			$_icon  = 'icon' . $_Table;
			$_color = 'color' . $_Table;
			// echo $APP->has_field_fk($value).' '.$value; || !empty($APP->has_field_fk($value))
			// echo $without = $APP->find($vars + [$_id => ['$exists' => 0]])->count(true);
			if (!empty($APP_TABLE['has' . $Value . 'Scheme'])):
				$APP_TMP     = new App($_table);
				$rs_tmp      = $APP_TMP->find()->sort(['ordre' . $_Table => 1]);
				$has_some    = $APP->find($vars + [$_id => ['$exists' => 1]])->count(true);
				$arr_tmp_end = $APP_TMP->findOne(['code' . $_Table => 'END']);
				$id_end      = (int)$arr_tmp_end[$_id];
				$max_end     = $APP->find($vars + [$_id => $id_end])->count();

				if ($has_some == 0) continue;
				if ($rs_tmp->count() == 0) continue;
				?>
				<div style="margin:0.5em; " class="flex_v" main_auto_tree>
					<div auto_tree data-auto_tree_setting="" class="  padding   ">
						<div class="trait  flex_align_middle  padding uppercase flex_h">
							<div class="flex_main">
								<?= $value ?>
							</div>
						</div>
					</div>
					<div style="width:100%">
						<div style="padding-right:6em;z-index:10;" class="retrait   alignright">
							<a onclick="$(this).up('[main_auto_tree]').toggleClassName('please_show')"><i class="fa fa-fw fa-eye"></i> <?= idioma('Détails') ?></a>
						</div>
						<div class="flex_h flex_wrap margin">
							<? while ($arr_tmp = $rs_tmp->getNext()):
								$rs_tmp_ct = $APP->find($vars + [$_id => $arr_tmp[$_id]])->sort(['ordre' . $_Table => 1]);
								$count = $rs_tmp_ct->count();
								$max   = $APP->find($vars + [$_id => ['$exists' => 1]])->count(true);

								$max_no_status = $APP->find($vars + [$_id => ['$exists' => 0]])->count(true);
								$color         = empty($arr_tmp[$_color]) ? '#ededed' : $arr_tmp[$_color];
								$arr_rfk[$_id] = $arr_tmp[$_id];
								$http_rfk      = $APP->translate_vars($arr_rfk);
								if ($count == 0) continue;
								$delta     = ($id_end != $arr_tmp[$_id]) ? $max_end : 0;
								?>
								<div style="min-width:25%;padding:0 1.5em 0.5em 0  ;width:350px;max-width:350px" class=" flex_main  cursor flex_grow_0 flex_align_middle">
									<div class="flex_h flex_align_middle padding">
										<div style="width: 40px;" class="ededed boxshadow">
											<div class="padding_more aligncenter border4" style="border-color:<?= $arr_tmp[$_color] ?>">
												<i style="z-index:0" class="none textgris  fa fa-file-o fa-3x absolute"></i>
												<i class="relative textgrisfonce fa fa-<?= empty($arr_tmp[$_icon]) ? 'folder-o' : $arr_tmp[$_icon]; ?> fa-2x "></i>
											</div>
										</div>
										<div style=";overflow:hidden;" class="padding flex_align_middle ms-font-m">
											<div class="ms-fontWeight-light flex_main cursor bold" onclick="<?= fonctionsJs::app_liste($table, $table_value, ['vars' => $vars, $_id => $arr_tmp[$_id]]) ?>">
												<?= ucfirst(strtolower($APP->get_titre_vars([$_id => $arr_tmp[$_id]]))) ?>
											</div>
											<div class="ms-font-s">
												<span class="textgrisfonce"><?= $count . ' sur ' . ($max - $delta); ?> -<//?=$max_no_status?></span>
												<? if ($value == 'statut') { ?><span class=""><?= (int)(($count / ($max - $delta)) * 100) . '% ' ?>  </span><? } ?>
											</div>
										</div>
									</div>
									<div class="flex_h flex_align_middle ">
										<div style="width: 40px;" class="  ">&nbsp;</div>
										<div class="padding flex_main">
											<? if ($value == 'statut') { ?>
												<div>
													<div class="border4" style="margin-right:3em;color:<?= $color ?>;">
														<div class="padding borderr" style="height:12px;background-color: <?= $color ?>;width:<?= ($count / $max) * 100 ?>%;min-width:1px;"></div>
													</div>
												</div>
											<? } ?>
											<div style="display:none;" class="bordert ok_show" act_defer mdl="app/app/app_explorer_entete_rfk" vars="table=<?= $table ?>&<?= $http_rfk ?>"></div>
										</div>
									</div>
								</div>
							<? endwhile; ?>
						</div>
					</div>
				</div>
			<? endif; ?>
		<? endforeach; ?>
</div>
