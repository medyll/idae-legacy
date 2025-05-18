<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();

	$APP->init_scheme('sitebase_base','tache_type',['fields'=>['color','icon','code','nom']]);
	$APP->init_scheme('sitebase_base','tache_statut',['fields'=>['color','icon','code','nom']]);
	$APP->init_scheme('sitebase_base','tache',['hasTypeScheme'=>1,'hasStatutScheme'=>1,'fields'=>['dateDebut','dateFin','heureDebut','heureFin','description']]);
	$time = time();
	$table = 'tache';
	$Table = ucfirst($table);
	$_POST['idagent'] = $_SESSION['idagent'];
	if (empty($_POST['typeCalendar'])) {
		$_POST['typeCalendar'] = 'quotidien';
	}
?> 
<div class="flex_h relative" style="width:100%;height:100%;max-height:100%;overflow:hidden;">
	<div class="frmCol1    flex_v" style="overflow:hidden;height:100%;">
		<div class="titre_entete alignright borderb">
			<a onclick="reloadModule('app/app_planning/app_planning','*')"><i class="fa fa-refresh"></i> &nbsp;<?= idioma('Gestion des taches') ?>
			&nbsp;</a>
		</div>
		<div class="applink applinkblock applinkbig alignright fond_noir color_fond_noir">

			<a class=" " onclick="<?= fonctionsJs::app_create('tache') ?>">
				<img src="<?= ICONPATH ?>tache16.png"/>&nbsp;
				<?= idioma('Créer une tache') ?>
			</a>
		</div>
		<div id="cal_planning_<?= $time ?>" class="borderb ededed">
			<?= skelMdl::cf_module('app/app_calendrier/app_calendrier', array('calendarId' => 'calPLan') + $_POST, 'calPLan') ?>
		</div>
		<div class="flex_main" >
			<div class="applink applinkblock toggler applinkbig" id="app_planning_menu">
				<a class="autoToggle active flex flex_align_middle flex_h" onclick="$('loaderPlanning').loadModule('app/app_planning/app_planning_hebdo');"> <span class="flex_main"><?=idioma('Hebdomadaire')?></span><i class="fa fa-caret-right"></i></a>
				<a class="autoToggle flex flex_align_middle flex_h" onclick="$('loaderPlanning').loadModule('app/app_planning/app_planning_quoti_bi');"><span class="flex_main"><?=idioma('Bi-qotidien')?></span><i class="fa fa-caret-right"></i></a>
				<a class="autoToggle flex flex_align_middle flex_h" onclick="$('loaderPlanning').loadModule('app/app_planning/app_planning_quoti');"><span class="flex_main"><?=idioma('Quotidien')?></span><i class="fa fa-caret-right"></i></a>
				<a class="autoToggle flex flex_align_middle flex_h" onclick="$('loaderPlanning').loadModule('app/app_planning/app_planning_mens','<?= $time ?>','','');"><span class="flex_main"><?=idioma('Mensuel')?></span><i class="fa fa-caret-right"></i></a>
				<div class="padding bordertb">
				<a class="autoToggle flex flex_align_middle flex_h textvert" onclick="$('loaderPlanning').loadModule('app/app_liste/app_liste','table=tache&vars[idagent]=<?=$_SESSION['idagent']?>&vars[ne][codeTache_statut]=END',{value:'planning'});"><span class="flex_main borderr"><?=idioma('Liste taches actives')?></span><i class="fa fa-caret-right"></i></a>
				<a class="autoToggle flex flex_align_middle flex_h textrouge" onclick="$('loaderPlanning').loadModule('app/app_liste/app_liste','table=tache&vars[idagent]=<?=$_SESSION['idagent']?>&vars[codeTache_statut]=END',{value:'planning'});"><span class="flex_main borderr"><?=idioma('Liste taches inactives')?></span><i class="fa fa-caret-right"></i></a>
			</div></div>
		</div>
		<div class="titre_entete ededed bordert aligncenter">
			<i class="fa fa-recycle fa-2x" style="vertical-align:middle;"></i>
		</div>
	</div>
	<div class="flex_main flex_v relative blanc" style="overflow:hidden;position:relative;width:100%;height:100%;" id="loaderPlanning" act_defer mdl="app/app_planning/app_planning_hebdo" >
		 
	</div>
</div>
<script>
// load_table_in_zone('table_tache','loaderPlanning');
	$('cal_planning_<?=$time?>').observe('dom:act_click', function (event) {
		navCal(event.memo.value)
		navCalUs(event.memo.value_us)
	})

	navCal = function (vars) {
		reloadModule('app/app_planning/app_planning_quoti', '*', 'date=' + vars);
		reloadModule('app/app_planning/app_planning_hebdo', '*', 'date=' + vars);
		reloadModule('app/app_planning/app_planning_mens', '*', 'date=' + vars);
		reloadModule('app/app_planning/app_planning_quoti_bi', '*', 'date=' + vars);

	}
	navCalUs = function (vars) {
		reloadModule('app/app_liste/app_liste', 'planning', 'table=tache&vars[idagent]=<?=$_SESSION['idagent']?>&vars[dateDebut<?=$Table?>]=' + vars);

	}

</script>
<style>
	#app_planning_menu a .fa {
		width: 20px;
		}
	[data-dragtache]{
		/*display: inline-block;*/
		position: absolute;
		background-color: #ededed;
		box-shadow:0 0 1px #666,0 0 2px #fff;
	}
	.tablePlanningMensuel [data-dragtache]{
		/*display: inline-block;*/
		position: relative;
		background-color: #ededed;
		box-shadow:0 0 1px #666,0 0 2px #fff;
	}
	#tablePlanninHebdo [data-dragtache]{
		/*display: inline-block;*/
		position: absolute;
		background-color: #ededed;
		box-shadow:0 0 1px #666,0 0 2px #fff;
	}

	.evenement .dyntache {
		position: relative !important;
		margin: 0px;
		width: auto;
		/*height: 20px;*/
		padding: 0 !important;
	}



	.calday {
		height: 20px;
		/*line-height: 20px;*/
		max-height: 20px;
	}

</style>
