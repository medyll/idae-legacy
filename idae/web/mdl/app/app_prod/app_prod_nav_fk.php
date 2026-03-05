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
	$APP       = new App($table);
	$GRILLE_FK = $APP->get_grille_fk();

	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$nomAppscheme = $APP->nomAppscheme;

	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		// $vars['idagent']=(int)$_SESSION['idagent'];
	endif;

	$arr_rfk = $vars;
	$sort_fk = empty($_POST['sort_fk']) ? 'dateCreation' : $_POST['sort_fk'];
	// vardump($vars);
?>
<div main_auto_tree class="  ">
	<div class="flex_h flex_align_middle ">
		<div style="display:none;" class="margin_more applink toggler alignright">
			<a class="autoToggle <?= ($sort_fk == 'count') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=count')">date de création <?= $nomAppscheme ?></a>
			<a class="autoToggle <?= ($sort_fk == 'dateCreation') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=dateCreation')">date de création <?= $nomAppscheme ?></a>
			<a class="autoToggle <?= ($sort_fk == 'dateModification') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','table=<?= $table ?>&sort_fk=dateModification')">date de modification <?= $nomAppscheme ?></a>
		</div>
	</div>
	<div auto_tree class="ededed border4">
		<div class="padding">Repartitions</div>
	</div>
	<div class="retrait" data-app_fragment="remenber_search">
		<?

			$sort_fk = empty($_POST['sort_fk']) ? 'count' : $_POST['sort_fk'];
			foreach ($GRILLE_FK as $key => $value) {
				$table_fk = $key;
				$Table_fk = ucfirst($table_fk);
				$G        = $APP->distinct_all('id' . $key);

				$vars_groupBy = ['groupBy_table' => $key, 'vars' => [], 'limit' => 8, 'mode' => 'full', 'field' => '', 'sort_field' => [$sort_fk, -1]];

				$G2 = $APP->distinct_rs($vars_groupBy);

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
				$rs_tmp     = $APP_TMP->find([$_id => ['$in' => $G]], ['sort' => ['dateCreation' . ucfirst($table_fk) => -1], 'limit' => 6]);
				$count      = $rs_tmp->count();

				$orphan_link = fonctionsJs::app_liste($table, '', ['vars' => [$_id => 'NULL']]);
				?>
				<div class="">
					<div class=" flex_v  padding   applink">
						<?php if ($rs_orphans->count() != 0) { ?>
							<div class="bordert aligncenter  none  " title="<?= idioma("Elements sans $_nom ") ?>">
								<a onclick="<?= $orphan_link ?>" class=""><i class="fa fa-chain-broken textrouge"></i><?= $rs_orphans->count(); ?></a>
							</div>
						<? } ?>
					</div>
					<div class="flex_main">
						<div auto_tree>
							<div class="flex_align_middle  flex_h     borderl" style="border-color:<?= $APP_TMP->colorAppscheme ?>;">
								<div class="bold  ">
									<i style="text-shadow: 0 0 2px #fff " class="textgris fa fa-<?= $APP_TMP->iconAppscheme ?>"></i> <?= ucfirst($APP_TMP->nomAppscheme) ?>
								</div>
								<div class="paddingl alignright  flex_main  ">
									<?= "[$count]" ?>
								</div>
							</div>
						</div>
						<div class="none applink applinkblock  ">
							<?
								foreach ($G2 as $value_idfk => $arr_groupBy) {
									// echo $_id;
									$afk_tmp = $arr_rfk;

									$count_in      = $arr_groupBy['count'];
									$afk_tmp[$_id] = $arr_groupBy[$_id];
									$dsp           = $arr_groupBy[$_nom];
									$link          = fonctionsJs::app_fiche_maxi($table_fk, $arr_groupBy[$_id], ['mdl_load' => $table]);
									?>
									<div title="<?= $arr_groupBy[$_id] ?>" class="retrait " style="order:-<?= $count_in ?>">
										<a onclick="<?= $link ?>">
											<span class="flex_h">

											<span class="flex_main ellipsis"><?= $dsp ?></span>
											<span><?= $count_in ?></span>
											</span>
										</a>
									</div>
									<?
								}
							?>
						</div>
					</div>
				</div>
			<? } ?></div>
</div>
