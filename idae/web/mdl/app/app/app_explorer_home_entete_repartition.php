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
	$name_id           = 'id' . $table;
	$Table             = ucfirst($table);
	$APP               = new App($table);
	$APP_TABLE         = $APP->app_table_one;
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
<div style="width:100%; " class="flex_h flex_wrap">
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
				$rs_tmp      = $APP_TMP->find()->sort(['ordre' . $_Table => 1])->limit(8);
				$has_some    = $APP->find($vars + [$_id => ['$exists' => 1]])->count(true);
				$arr_tmp_end = $APP_TMP->findOne(['code' . $_Table => 'END']);
				$id_end      = (int)$arr_tmp_end[$_id];
				$max_end     = $APP->find($vars + [$_id => $id_end])->count();

				if ($has_some == 0) continue;
				if ($rs_tmp->count() == 0) continue;
				?>
				<div style="margin:0;border-left:5px solid #ededed;border-left-color: <?=$APP_TMP->colorAppscheme?>" class="flex_v flex_main ">
					<div class="  flex_align_middle  padding uppercase flex_h">
						<div class="flex_main bold">
							<span class="padding borderb boxshadowb"><?=$APP->nomAppscheme?> : <?=$APP_TMP->nomAppscheme?></span>
						</div>
					</div>
					<div style="width:100%">
						<div style="padding-right:6em;z-index:10;" class=" retrait   alignright">
							<a onclick="$(this).up('[main_auto_tree]').toggleClassName('please_show')"><i class="fa fa-fw fa-eye"></i> <?= idioma('Détails') ?></a>
						</div>
						<div class="flex_h flex_wrap margin flex_margin flex_border">
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
								<div style="min-width:200px;padding:0 1.5em 0.5em 0  ;width:200px;max-width:200px" class="edededhover flex_main  cursor flex_grow_0 flex_align_middle">
									<div class="flex_h flex_align_middle  ">
										<div style="width: 40px;" class=" ">
											<div class="padding_more aligncenter  " style="border-color:<?= $arr_tmp[$_color] ?>">
												<i class="relative textgrisfonce fa fa-<?= empty($arr_tmp[$_icon]) ? 'folder-o' : $arr_tmp[$_icon]; ?> "></i>
											</div>
										</div>
										<div style="overflow:hidden;" class="padding flex_align_middle ms-font-m">
											<div class="ms-fontWeight-light flex_main cursor bold" onclick="<?= fonctionsJs::app_liste($table, $table_value, ['vars' => $vars, $_id => $arr_tmp[$_id]]) ?>">
												<?= ucfirst(strtolower($APP->get_titre_vars([$_id => $arr_tmp[$_id]]))) ?>
											</div>
											<div class="ms-font-s">
												<span class="textgrisfonce"><?= $count . ' sur ' . ($max - $delta); ?> -<//?=$max_no_status?></span>
												<? if ($value == 'statut') { ?><span class=""><?= (int)(($count / ($max - $delta)) * 100) . '% ' ?>  </span><? } ?>
											</div>
										</div>
									</div>
									<div class="flex_h flex_align_middle   ">
										<div style="width: 40px;" class="  ">&nbsp;</div>
										<div class="padding flex_main">
											<div>
												<div class="border4" style="margin-right:3em;color:<?= $color ?>;">
													<div class="padding borderr" style="height:12px;background-color: <?= $color ?>;width:<?= ($count / $max) * 100 ?>%;min-width:1px;"></div>
												</div>
											</div>
											<? if ($value == 'statut') { ?>
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
