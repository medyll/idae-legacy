<?php
	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	$APP_AGENT = new App('agent');
	$APP_MAIL = new App('email');

	$uniqid = uniqid();
	$mailbox = (empty($_POST['mailbox'])) ? 'INBOX' : $_POST['mailbox'];
	//
	$rs = $APP_MAIL->find(array('idagent' => (int)$_SESSION['idagent'], 'from' => array('$ne' => 'caroline.c@quizzboutique.com')))->sort(array('mongoDate' => -1));
	$time = time();
?>
<div class="flex_v" style="height: 100%;overflow:hidden;">
	<div class="padding blanc">
		<input type="button" onclick="ajaxMdl('app/app_mail/app_mail_send','Nouveau message')" value="Nouveau message"/>

	</div>
	<div class="flex_main blanc" style="overflow:auto;" id="zone_mailer_liste">

	</div>
</div>
<script>
	new BuildTbl($('zone_mailer_liste'), {
		table_name: 'email',
		url_data  : 'table=email&vars[idagent]=<?=$_SESSION['idagent']?>',
		nbRows    : 500
	});
</script>
<style>
	.selected, td.selected {
		background-color: #333 !important;
		color: #fff !important
	}

	.vResizeArrow {
		position: absolute !important;
		background-color: rgba(237, 237, 237, 0.1);
		top: 0;
		z-index: 3000;
		width: 10px !important;
		height: 100%
	}

	.vResizeArrow:hover {
		background-color: rgba(237, 237, 237, 0.8);
	}

	.vResizeArrow.active {
		background-color: rgba(0, 0, 0, 0.2);
		width: 200px !important;
		margin-right: -100px;
	}
</style>