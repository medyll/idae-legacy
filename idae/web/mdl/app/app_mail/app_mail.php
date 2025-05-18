<?
	include_once($_SERVER['CONF_INC']);

	$APP_AGENT     = new App('agent');
	$APP_EMAIL     = new App('email');
	$APP_EMAILBOX  = new App('emailbox');
	$APP_EMAILMIME = new App('email_mime');

	$uniqid = uniqid();

	$or      = ['$or' => [['groupeAgent.AGENT' => 1], ['idagent' => (int)$_SESSION['idagent']]]];
	$rsA     = $APP_AGENT->find(['groupeAgent.AGENT' => 1, 'groupeAgent.DIR' => ['$ne' => 1]])->sort(['prenomAgent' => 1]);
	$selectA = fonctionsProduction::getSelectMongo('idagent', $rsA, 'idagent', 'prenomAgent', (int)$_SESSION['idagent']);

?>
<div class="flex_v blanc" style="height: 100%;">
	<div class="padding ededed applink">
		<form onchange="$('liste_mail_<?= $uniqid ?>').loadModule('app/app_mail/app_mail_liste','idagent='+this.idagent.value+'&uniqid=<?= $uniqid ?>',{value:this.idagent.value})">
			<a onclick="$('liste_mail_<?= $uniqid ?>').socketModule('app/app_mail/app_mail_liste','uniqid=<?= $uniqid ?>'); ">Recharger</a>
			<a onclick="runModule('mdl/dyn/dyn_mail_check','vars[idagent]=<?= $_SESSION['idagent'] ?>'); ">check</a>
			<label>Profil</label>
			<?= $selectA ?>
		</form>
	</div>
	<div class="flex_h flex_main relative" style="width:100%;overflow:hidden;">
		<div class="frmCol1" style=" ">
			<?= skelMdl::cf_module('app/app_mail/app_mail_boxes', ['defer' => true]) ?>
		</div>
		<div class="frmCol1" style="overflow:auto;">
			<div data-dsp_liste="" data-vars="table=email&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_micro" data-data_model="defaultModel">
			</div>
		</div>
		<div class="flex_main flex_h blanc" id="panecneter">
			<div class="flex_main" id="liste_mail_<?= $uniqid ?>">

			</div>
		</div>
	</div>
</div>
<script>
	/*$('liste_mail_<?=$uniqid?>').on('click', 'tr.idmail', function (event, element) {
	 uniqid = element.readAttribute('uniqid');
	 $('preview_mail_<?=$uniqid?>').show().socketModule('app/app_mail/app_mail_detail', 'uniqid=' + uniqid, {});

	 }.bind(this));*/
</script>