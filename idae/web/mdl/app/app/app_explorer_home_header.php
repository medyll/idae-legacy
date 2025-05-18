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
	$name_id = 'id' . $table;
	$Table   = ucfirst($table);

	$APP         = new App($table);
	$APP_HISTORY = new App('agent_history');

	$APP_TABLE = $APP->app_table_one;

	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');

	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		// $vars['idagent']=(int)$_SESSION['idagent'];
	endif;

	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$DADATE = $dateStart->format('Y-m-d');

	$arr_rfk = $vars;
	$sort_fk = empty($_POST['sort_fk']) ? 'dateCreation' : $_POST['sort_fk'];

	$RS_LAST = $APP_HISTORY->find(['codeAgent_history' => $table, 'idagent' => (int)$_SESSION['idagent']])->sort(['quantiteAgent_history' => -1])->limit(5);

	$ARR_LAST = ['dateCreation' => 'créés', 'dateModification' => 'modifiés'];
?>
<div class="flex_h  flex_align_top flex_wrap relative  " style="height:100%;">
	<div class="flex_v flex_align_bottom  " style="height:100%;width:60px;">
		<div class="none padding "><i class="fa fa-<?= $APP->iconAppscheme ?>   fa-4x" style="color:#fff;text-shadow: 0px 0px 2px #333,1px 1px 3px <?= $APP->colorAppscheme ?>"></i></div>
		<div id="fafa" class="padding aligncenter "><i class="fa fa-<?= $APP->iconAppscheme ?>   fa-3x"></i></div>
	</div>
	<style>
		#fafa {
			background              : -webkit-linear-gradient(bottom, <?=$color?>, #ccc);
			background              : linear-gradient(bottom, <?=$color?>, #ccc);
			background-clip         : text;
			-webkit-text-fill-color : transparent;
		}
	</style>
	<div class="flex_main padding     ">
		<div class="padding_more  "><span style="font-size:1.5rem"><?= idioma('Gestion ') ?><?= $APP_TABLE['nomAppscheme'] ?> </span></div>
		<div class="flex_h applinkblock     padding">
			<? if (droit_table($_SESSION['idagent'], 'C', $table)) { ?>
			<a class="appbutton" onclick="<?= fonctionsJs::app_create($table, $vars) ?>">
				<i class="fa fa-copy textbleu"></i> <?= idioma('Créer') . ' ' . $APP_TABLE['nomAppscheme'] ?>
				</a><? } ?>
			<? if ($table == 'conge') { ?>
				<a onclick="ajaxInMdl('app/app_conge/app_conge','time_cong','',{onglet:'Absences et congés'})">
					<i class="fa fa-calendar-o textvert"></i> <?= idioma('Grille des congés') ?>
				</a>
			<? } ?>
			<? if ($table == 'promo_zone') { ?>
				<a onclick="ajaxInMdl('app/app_promo_zone/app_promo_zone_build','time_promo_zone','',{onglet:'promo_zone'})">
					<i class="fa fa-calendar-o textvert"></i> <?= idioma('promo_zone') ?>
				</a>
			<? } ?>
			<? if ($APP->has_field('dateDebut') && !empty($APP->app_table_one['hasStatutScheme'])) { ?>
				<a onclick="<?= fonctionsJs::app_console($table) ?>"><i class="fa fa-dashboard textorange"></i> <?= idioma('Console') ?></a>
			<? } ?>
		</div>
		<div class="padding"><i class="fa fa-history"></i>
			<?= idioma('Historique') ?> <i class="fa fa-caret-right"></i>
			<? while ($arr_last = $RS_LAST->getNext()):
				if (empty($arr_last['nomAgent_history'])) continue;
				?><span data-contextual="table=<?= $table ?>&table_value=<?= $arr_last['valeurAgent_history'] ?>" class="inline margin">
				<a act_chrome_gui="app/app/app_fiche" vars="table=<?= $table ?>&table_value=<?= $arr_last['valeurAgent_history'] ?>">
					<?= strtolower($arr_last['nomAgent_history']) ?></a>
				</span><?
			endwhile; ?>
		</div>
	</div>
	<div class="flex_main" style="max-width: 33%;">
		<div class=" " style="overflow: hidden;" act_defer mdl="app/app/app_explorer_count_date" vars="table=<?= $table ?>" value="<?= $table ?>">
		</div>
	</div>
</div>
