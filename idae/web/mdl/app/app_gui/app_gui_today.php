<?
	include_once($_SERVER['CONF_INC']);
?>
<div class="relative flex_v applink" style="height: 100%;overflow:hidden;">
	<div class="flex_h" style="width:100%;">
		<div class=" demi">
			<div class="hide_gui_pane  applink ">
				<div class="appmetro aligncenter">
					<a onclick="ajaxInMdl('app/app_planning/app_planning','time_plan','',{onglet:'Mes taches'})">
						<i class="fa fa-calendar fa-3x"></i>

						<br>
						Planning taches
					</a>
				</div>
			</div>
		</div>
		<? if (droit('DEV')) { ?>
			<div class=" demi">
				<div class="hide_gui_pane  applink ">
					<div class="appmetro aligncenter">
						<a onclick="ajaxInMdl('app/app_stat/app_stat','time_plan','',{onglet:'app_stat'})">
							<i class="fa fa-stumbleupon fa-3x"></i>

							<br>
							app_stat
						</a>
					</div>
				</div>
			</div>
		<? } ?>
	</div>
	<div class="flex_main" style="overflow:auto;">
		<div style="margin-top:2em;">
			<?= skelMdl::cf_module('app/app_gui/app_gui_today_create', ['scope' => 'app_menu_create']) ?>
		</div>
		<div style="margin-top:2em;">
			<?= skelMdl::cf_module('app/app_gui/app_gui_today_link', ['scope' => 'app_menu_create']) ?>
		</div>
		<div class="flex_h none" style="width:100%;">
			<div class=" demi">
				<div class="hide_gui_pane  applink">
					<div class="appmetro aligncenter"><i class="fa fa-envelope-o fa-3x"></i>
						<a href="https://<?= DOCUMENTDOMAIN ?>:8080/webmail/" target="_blank">
							<?= idioma('Mails') ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="none" style="width:100%;">
			<div class="appmetro" act_defer mdl="app/app_stat/app_stat_draw_mini" vars="table=tache&typeDate=dateCreation&dateDebut=2014-11-01&dateFin=2014-11-07">
			</div>
			<div class="appmetro" act_defer mdl="app/app_stat/app_stat_draw_mini" vars="table=contrat&typeDate=dateCreation&dateDebut=2014-11-01&dateFin=2014-11-07">
			</div>
		</div>
		<div class=" " style="margin_top:2em;overflow-y: hidden " act_defer mdl="app/app_gui/app_gui_today_echeancier" vars="vars[idagent]=<?= $_SESSION['idagent'] ?>"></div>
	</div>
</div>
