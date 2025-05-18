<?
	include_once($_SERVER['CONF_INC']);
	$time = 'app_conge_time';
	$_POST['idagent'] = $_SESSION['idagent'];
	$APP_TY = new App('conge_type');
	$APP_ST = new App('conge_statut');
	$rs_ty = $APP_TY->find()->sort(['ordreConge_type'=> 1]);
	$rs_st = $APP_ST->find()->sort(['ordreConge_statut'=> 1]);
	$zone_liste = 're54d';
	$zone_grille = 'azde45';
	// liste des agents sur la droite => defiler jours semaines / mois /trimestres /
?>


<div class="flex_h relative" style="width:100%;height:100%;overflow:hidden;" >
	<div class="frmCol1  ededed flex_v" style="overflow:hidden;" >
		<div class="titre_entete alignright borderb" >
			<a onclick="reloadModule('app/app_conge/app_conge','*')" ><i class="fa fa-refresh" ></i >
				&nbsp;<?= idioma('Gestion des congés et absences') ?>
				&nbsp;</a >
		</div >
		<div id='cal_planning_<?= $time ?>' >
			<?= skelMdl::cf_module('app/app_calendrier/app_calendrier',  $_POST, 'calPLan') ?>
		</div >
		<div main_auto_tree class="flex_main blanc" style="overflow: auto;" >
			<div >
				<div auto_tree class="margin padding borderb" >
					<div class="bold" >Demandes en attentes</div >
				</div >
				<div data-dsp_liste=""  data-vars="table=conge&vars[codeConge_statut]=WAIT" data-dsp="flex_line" data-data_model="defaultModel" data-dsp_fields="dateDebutConge" >

				</div >
				<div auto_tree  class="margin padding borderb" >
					<div class="bold" > Prochains congés</div >
				</div >
				<div  data-dsp_liste="" data-vars="table=conge&vars[codeConge_statut]=END" data-dsp="flex_line" data-data_model="defaultModel" data-dsp_fields="dateDebutConge" >

				</div >
			</div >
		</div >
	</div >
	<div class="flex_main flex_v relative blanc" style="overflow:hidden;position:relative;width:100%;height:100%;" >
		<div class="app_onglet toggler" >
			<a class="avoid boredr4 ededed" onclick="<?= fonctionsJs::app_create('conge') ?>" >
				&nbsp;<i class="fa fa-plus" ></i >&nbsp;
				<?= idioma('Faire une demande') ?>&nbsp;&nbsp;
			</a > <a onclick="$('<?= $zone_grille ?>').toggleContent()" class="autoToggle active" >Calendrier</a >
			<a onclick="$('<?= $zone_liste ?>').toggleContent().loadModule('app/app_conge/app_conge_liste')" class="none autoToggle" >Liste </a >
		</div >
		<div class="flex_main relative" style="height:100%;overflow:hidden;" >
			<div style="height:100%;overflow:hidden;" id="<?= $zone_grille ?>" data-cache="true" act_defer mdl="app/app_conge/app_conge_grille"  ></div >
			<div style="height:100%;overflow:hidden;display:none;" id="<?= $zone_liste ?>" ></div >
		</div >
		<div class="relative padding_more ededed flex_h" >
			<div class="margin padding bold ucfirst" ><?=idioma('types')?></div >
			<? while ($arr_ty = $rs_ty->getNext()) {
				?>
				<div class="margin padding border4 blanc" ><i style="color:<?= $arr_ty['colorConge_type'] ?>;" class="fa fa-fw fa-square"></i> <?= $arr_ty['nomConge_type'] ?></div >
				<? 	} ?>
			<div class="margin padding bold ucfirst" ><?=idioma('statuts')?></div >

			<? while ($arr_st = $rs_st->getNext()) {
				?>
				<div class="margin padding border4 blanc" ><i style="color:<?= $arr_st['colorConge_statut'] ?>;" class="fa fa-fw fa-<?= $arr_st['iconConge_statut'] ?>"></i> <?= $arr_st['nomConge_statut'] ?></div >
				<? 	} ?>
		</div >
	</div >
</div >
<script >

	$('cal_planning_<?=$time?>').observe('dom:act_click', function (event) {
		navCal(event.memo.value)
	})

	navCal = function (vars) {
		reloadModule('app/app_conge/app_conge_grille', '*', 'date=' + vars);
	}

</script >
<style >
	[data-dragconge] {
		display: inline-block;
		position: absolute;
		background-color: #ededed;
		height: 100%;
		z-index: 1000;
		box-shadow: 0 0 3px #666;
		border: 1px solid white;
	}
</style >
