<?php
	include_once($_SERVER['CONF_INC']);

	if (empty($_POST['run'])):
		$uniqid = uniqid();
		?>
		<div class="flex_h blanc" style="height:100%;overflow:hidden;">
			<div class="frmColSmall aligncenter">
				<?= date('H:i:s') ?>
			</div>
			<div class="frmCol1 aligncenter">
				<div class="applink applinkblock applinkbig">
					<a class="autoToggle" act_target="<?= $uniqid ?>" mdl="app/app_scheme/app_scheme_export_model_run">Exporter le modele</a>
				</div>
			</div>
			<div class="flex_main flex_v" style="overflow:hidden;height:100%;">
				<div class="padding  ">
					<progress value="0" id="auto_export_job"></progress>
				</div>
				<div class="flex_main" id="<?= $uniqid ?>" style="overflow:auto;">
				</div>
			</div>
		</div>
		<?
		return;
	endif;