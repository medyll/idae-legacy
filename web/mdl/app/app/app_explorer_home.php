<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	$vars         = empty($_POST['vars']) ? [] : $_POST['vars'];
	$table        = empty($_POST['table']) ? 'client' : $_POST['table'];
	$name_id      = 'id' . $table;
	$Table        = ucfirst($table);
	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);

	$APP               = new App($table);
	$APP_TABLE         = $APP->app_table_one;
	$HTTP_VARS_NOAGENT = $APP->translate_vars($vars_noagent);
	$TEST_AGENT        = $APP->has_agent();
	//
	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$DADATE = $dateStart->format('Y-m-d');

	$APP_HISTORY = new App('agent_history');

	if ($TEST_AGENT):
		$vars['idagent'] = (int)$_SESSION['idagent'];
	endif;
	$HTTP_VARS = $APP->translate_vars($vars);
	$zone      = uniqid($table);
	//
	$RS_LAST = $APP_HISTORY->find(['codeAgent_history' => $table, 'idagent' => (int)$_SESSION['idagent']])->sort(['quantiteAgent_history' => -1])->limit(5);

	$addvarsagent = '';
	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		$addvarsagent = 'vars[idagent]=' . $_SESSION['idagent'];
	endif;

	$ARR_LAST = ['dateCreation' => 'créés', 'dateModification' => 'modifiés'];

	//
	$scope = "explorer_home_scope_$table";

	$color          = empty($APP->colorAppscheme) ? '#c4c4c4' : $APP->colorAppscheme;
	$color_contrast = color_contrast($APP->colorAppscheme);
	$color_inverse  = color_inverse($APP->colorAppscheme);
?>
<div id="<?= $zone ?>" style="width:100%;height: 100%;overflow:hidden;" class="flex flex_v">
	<div class="borderb relative ededed">
		<div class="flex_h  flex_align_top flex_wrap relative  " style="height:100%;">
			<div class="flex_v flex_align_bottom " style="background-color :<?= $color ?>;height:100%;width:60px;border-right:2px solid <?=$color_inverse?>">
				<div class="padding_more aligncenter" style="color:<?= $color_contrast ?>;width:100%;"><i class="fa fa-<?= $APP->iconAppscheme ?>   fa-3x"></i></div>
			</div>
			<style>
				/*#fafa {
					background              : -webkit-linear-gradient(bottom,
				<?=$color?>
				, #ccc);
									background              : linear-gradient(bottom,
				<?=$color?>
				, #ccc);
									background-clip         : text;
									-webkit-text-fill-color : transparent;
								}*/
			</style>
			<div class="flex_main padding     ">
				<div class="padding_more  "><span style="font-size:1.5rem"><?= idioma('Gestion ') ?><?= $APP_TABLE['nomAppscheme'] ?> </span></div>
				<div class="flex_h     applink applinkbutton     padding">
					<? if (droit_table($_SESSION['idagent'], 'C', $table)) { ?>
					<a class="appbutton" onclick="<?= fonctionsJs::app_create($table, $vars) ?>">
						<i class="fa fa-copy textbleu"></i> <?= idioma('Créer') . ' ' . $APP_TABLE['nomAppscheme'] ?>
						</a><? } ?>
					<? if ($table == 'conge') { ?>
						<a class="appbutton" onclick="ajaxInMdl('app/app_conge/app_conge','time_cong','',{onglet:'Absences et congés'})">
							<i class="fa fa-calendar-o textvert"></i> <?= idioma('Grille des congés') ?>
						</a>
					<? } ?>
					<? if ($table == 'promo_zone') { ?>
						<a class="appbutton" onclick="ajaxInMdl('app/app_promo_zone/app_promo_zone_build','time_promo_zone','',{onglet:'promo_zone'})">
							<i class="fa fa-calendar-o textvert"></i> <?= idioma('promo_zone') ?>
						</a>
					<? } ?>
					<? if ($table == 'appscheme') { ?>
						<a class=" " onclick="<?= fonctionsJs::app_mdl('app/app_scheme/app_scheme',[],['title'=>'modèle de données']) ?>">
							<i class="fa fa-calendar-o textvert"></i> <?= idioma('modèle de données') ?>
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
			<? if($APP->has_field(['dateDebut','date'])){ ?>
				<div class="flex_main" style="max-width: 33%;">
					<div class=" " style="overflow: hidden;" act_defer mdl="app/app/app_explorer_count_date" vars="table=<?= $table ?>" value="<?= $table ?>">
					</div>
				</div>

			<? } ?>
		</div>
	</div>
	<div class="flex_main relative" style="overflow:hidden;">
		<div class="flex_h" style="height: 100%">
			<div class="flex_main relative" style="overflow:auto;" act_defer mdl="app/app_liste/app_liste" vars="table=<?= $table ?>&hide_menu=1"></div>
			<div class="borderl frmCol1" act_defer mdl="app/app/app_explorer_home_entete_rfk" vars="<?= http_build_query($_POST) ?>" scope="<?= $scope ?>"></div>
		</div>
	</div>
	<div class="flex_main relative" style="overflow:auto;display:none;">
		<? if (droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()): ?>
			<!--LIEN AGENT / ALL-->
			<div class="app_onglet toggler sticky blanc" style="position: sticky;top:0;z-index:1;">
				<a class="autoToggle active" onclick="reloadScope('<?= $scope ?>','*','vars[idagent]=<?= $_SESSION['idagent'] ?>')" title="<?= $table ?> <?= $APP->get_titre_vars($vars); ?>">
					<i class="fa fa-user textvert"></i><?= $nomAppscheme ?> <?= idioma('Agent') ?> <?= nomAgent($_SESSION['idagent']) ?>
				</a>
				<a class="autoToggle" onclick="reloadScope('<?= $scope ?>','*','<?= $HTTP_VARS_NOAGENT ?>',null,{unset_key:['vars[idagent]']})" title="<?= $table ?> <?= $APP->get_titre_vars($vars); ?>">
					<i class="fa fa-globe textorange"></i><?= idioma('tous les agents') ?> <?= $nomAppscheme ?>
				</a>
			</div>
		<? endif; ?>
		<div class="flex_h" style="position:relative;z-index:0;width:100%;">
			<div class="flex_main relative" style="width:70%;top:0;">
				<!--ENTETE-->
				<div style="overflow: hidden;" data-cache="true" act_defer mdl="app/app/app_explorer_home_entete" vars="table=<?= $table ?>&<?= $HTTP_VARS ?>" scope="<?= $scope ?>">
				</div>
				<div class="bordertb   ccc padding_more">
					<div class="margin_more border4 blanc" data-dsp_liste="data-liste" data-classname="what" data-dsp="table_icon" data-data_model="default" data-vars="table=<?= $table ?>&nbRows=10&sortBy=dateCreation<?= $Table ?>&sortDir=-1"></div>
				</div>
			</div>
			<div class="" style="display:none;width:33%;top:0;">
				<div class="border4 padding margin ededed">
					<div class="borderb padding">
						<div><i class="fa fa-<?= $APP->iconAppscheme ?> textgrisfonce"></i>
							<span class="ms-font-m"><?= idioma('Derniers éléments') ?></span></div>
						<div class="retrait padding">
							<?= idioma('Voir') . ' ' . $table ?>
							<i class="fa fa-caret-right"></i>
							<? foreach ($ARR_LAST as $k_last => $value_last) {
								$on_click = "load_table_in_zone('table=$table&$HTTP_VARS&nbRows=6&sortBy=$k_last.$Table&sortDir=-1', 'home_last_created_$zone');";
								?>
								<a onclick="<?= $on_click ?>"><?= idioma($value_last) ?></a>
							<? } ?>
						</div>
					</div>
					<div id="home_last_created_<?= $zone ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_thumb">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>