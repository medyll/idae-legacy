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
	$Table             = ucfirst($table);
	$APP               = new App($table);
	$GRILLE_FK         = $APP->get_grille_fk();

	$dateStart         = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$DADATE       = $dateStart->format('Y-m-d');
	$nomAppscheme = $APP->nomAppscheme;
	$COL          = [];

	$zone = uniqid($table);

	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		// $vars['idagent']=(int)$_SESSION['idagent'];
	endif;

	$arr_rfk = $vars;
	$sort_fk = empty($_POST['sort_fk']) ? 'dateCreation' : $_POST['sort_fk'];
	// vardump($vars);
?>
<div   style="margin:10px auto auto auto">
	<div   class="padding margin">
		<div class="border4  flex_align_middle  padding uppercase flex_h">
			<div class="flex_main">
				<?= idioma('REPARTITIONS') ?>
			</div>
		</div>
	</div>
	<div style="width:100%; " class="  ">
		<div class="flex_h flex_align_middle ">
			<div style="display:none;" class="margin_more applink toggler alignright">
				<a class="autoToggle <?= ($sort_fk == 'count') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=count')">date de création <?= $nomAppscheme ?></a>
				<a class="autoToggle <?= ($sort_fk == 'dateCreation') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=dateCreation')">date de création <?= $nomAppscheme ?></a>
				<a class="autoToggle <?= ($sort_fk == 'dateModification') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=dateModification')">date de modification <?= $nomAppscheme ?></a>
			</div>
		</div>
		<div class="flex_h flex_wrap  " data-app_fragment="remenber_search">
			<?

				$sort_fk = empty($_POST['sort_fk']) ? 'count' : $_POST['sort_fk'];
				foreach ($GRILLE_FK as $key => $value) {
					$table_fk = $key;
					$Table_fk = ucfirst($table_fk);
					$G        = $APP->distinct_all('id' . $key);

					$vars_groupBy_count = ['groupBy_table' => $key, 'vars' => [], 'limit' => 8, 'mode' => 'full', 'field' => '', 'sort_field' => [$sort_fk, -1]];
					$G2 = $APP->distinct_rs($vars_groupBy_count);

					$vars_groupBy_crea = ['groupBy_table' => $key, 'vars' => [], 'limit' => 8, 'mode' => 'full', 'field' => '', 'sort_field' => ['dateCreation'.$Table, 1]];
					$G_groupBy_crea = $APP->distinct_rs($vars_groupBy_crea);

					$vars_groupBy_modif = ['groupBy_table' => $key, 'vars' => [], 'limit' => 8, 'mode' => 'full', 'field' => '', 'sort_field' => ['dateModification'.$Table, 1]];
					$G_groupBy_modif = $APP->distinct_rs($vars_groupBy_modif);

					if (sizeof($G) == 0) continue;
					//
					$_id    = 'id' . $table_fk;
					$_nom   = 'nom' . $Table_fk;
					$_code  = 'code' . $Table_fk;
					$_icon  = 'icon' . $Table_fk;
					$_color = 'color' . $Table_fk;

					$APP_TMP = new App($table_fk);

					if (!droit_table($_SESSION['idagent'], 'CONF', $table_fk) && $APP_TMP->has_agent()):
						// $vars['idagent']=(int)$_SESSION['idagent'];
						// echo "waa";
					endif;

					// NULL VARS
					$orphans_vars          = [];
					$orphans_vars['$or'][] = [$_id => ['$exists' => false]];
					$orphans_vars[$_id]    = ['$in' => [null, '']];

					$rs_orphans = $APP->find($orphans_vars); // ,'$or'=>[$name_id=>['$exists'=>false]]
					$rs_tmp     = $APP_TMP->find([$_id => ['$in' => $G]])->sort(['dateCreation' . ucfirst($table_fk) => -1])->limit(6);
					$count      = $rs_tmp->count();

					$orphan_link = fonctionsJs::app_liste($table, '', ['vars' => [$_id => 'NULL']]);
					?>
					<div class="flex_h   flex_main margin_more boxshadow borderr cursor">
						<div class=" flex_v  padding borderr applink" style="width:45px;background-color: <?= $APP_TMP->colorAppscheme ?>;border-width: 3px;">
							<div class="flex_main aligncenter ">
								<i style="color:#fff;text-shadow: #000 0 0 1px, #fff 0 0 2px" class="fa fa-<?= $APP_TMP->iconAppscheme ?> fa-2x"></i>
							</div>
							<?php if ($rs_orphans->count() != 0) { ?>
								<div class="bordert aligncenter  color_fond_noir  " title="<?= idioma("Elements sans $_nom ") ?>">
									<a onclick="<?= $orphan_link ?>" class=""><i class="fa fa-chain-broken textrouge"></i><?= $rs_orphans->count(); ?></a>
								</div>
							<? } ?>
						</div>
						<div data-menu="data-menu" data-menu_free="red" data-clone="treu" class="flex_main">
							<div class="  flex_main   padding_more    ">
								<div class="bold uppercase  ">
									<?= ucfirst($value['nomAppscheme']) ?>
								</div>
								<div class="paddingl small  flex_main  ">
									<?= $count . " élément(s) " ?>
								</div>
								<div class="none"><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', ['table' => $APP_TMP->codeAppscheme]) ?></div>
							</div>
						</div>
						<div class=" flex_h  flex_margin flex_border  contextmenu" style="display: none;position:absolute;">
							<div class="boxshadow">
								<div class="padding_more borderb ededed">
									<i class="fa fa-star"></i><?=$APP_TMP->nomAppscheme?> <?=idioma('ayant le plus de')?> <?=$APP->nomAppscheme?>
								</div>
								<?
									foreach ($G2 as $value_idfk => $arr_groupBy) {
										// echo $_id;
										$afk_tmp = $arr_rfk;

										$count_in      = $arr_groupBy['count'];
										$afk_tmp[$_id] = $arr_groupBy[$_id];
										$dsp           = $arr_groupBy[$_nom];
										$link          = fonctionsJs::app_fiche_maxi($table_fk, $arr_groupBy[$_id], ['mdl_load' => $table]);
										?>
										<div class="flex_h flex_align_middle" style=" overflow:hidden;order:-<?= $count_in ?>">
											<div class="    applink  applinkblock " style="">
												<a onclick="<?= $link ?>"><?= $dsp ?> (<?= $count_in ?>)</a>
											</div>
										</div>
									<? } ?>
							</div>
							<div class="boxshadow">
								<div class="padding_more borderb no_wrap ededed">
									<i class="fa fa-star"></i> <?=$APP_TMP->nomAppscheme?> <?=idioma('avec')?> <?=$APP->nomAppscheme?> <?=idioma('dernierement créés')?>
								</div>
								<div class="    applink  applinkblock ">
								<?
									foreach ($G_groupBy_crea as $value_idfk => $arr_groupBy) {
										// echo $_id;
										$afk_tmp = $arr_rfk;

										$count_in      = $arr_groupBy['count'];
										$afk_tmp[$_id] = $arr_groupBy[$_id];
										$dsp           = $arr_groupBy[$_nom];
										$link          = fonctionsJs::app_fiche_maxi($table_fk, $arr_groupBy[$_id], ['mdl_load' => $table]);
										?>
											<div class=" " style="overflow:hidden;">
												<a onclick="<?= $link ?>"><?= $dsp ?> (<?= $count_in ?>)</a>
											</div>
									<? } ?>
								</div>
							</div>
							<div class="boxshadow">
								<div class="padding_more borderb no_wrap ededed">
									<i class="fa fa-star"></i> <?=$APP_TMP->nomAppscheme?> <?=idioma('avec')?> <?=$APP->nomAppscheme?> <?=idioma('dernierement modifiés')?>
								</div>
								<div class="    applink  applinkblock ">
								<?
									foreach ($G_groupBy_modif as $value_idfk => $arr_groupBy) {
										// echo $_id;
										$afk_tmp = $arr_rfk;

										$count_in      = $arr_groupBy['count'];
										$afk_tmp[$_id] = $arr_groupBy[$_id];
										$dsp           = $arr_groupBy[$_nom];
										$link          = fonctionsJs::app_fiche_maxi($table_fk, $arr_groupBy[$_id], ['mdl_load' => $table]);
										?>
											<div  style="overflow:hidden;">
												<a onclick="<?= $link ?>"><?= $dsp ?> (<?= $count_in ?>)</a>
											</div>
									<? } ?>
								</div>
							</div>
						</div>
						<div class="ededed"><i class="fa fa-arrow-right"></i></div>
					</div>
				<? } ?></div>
	</div>
</div>
