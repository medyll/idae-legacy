<?
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	$APP    = new App();
?>
<style>
	.swatch_3d_32 {
		background: url("/images/swatch_3d_32.png") no-repeat scroll 0 0 transparent;
		display: inline-block;
		height: 32px;
		margin: 0 6px 6px 0;
		width: 32px;
	}
</style>
<div main_auto_tree class="aligncenter ededed" style="height:100%;overflow:auto">
	<div class="padding" auto_tree>
		<div><?= idioma('Général') ?></div>
	</div>
	<div style="position:relative;min-height:150px;" id="drag_system">
		<?
			$base = $APP->plug_base('sitebase_image')->getGridFs('wallpaper');
			$rs   = $base->find(array('metadata.thumb' => 1, 'metadata.idagent' => ['$exists' => 0]))->sort(array('metadata.date' => -1));

			while ($arr = $rs->getNext()) {
				$file   = $arr->file;
				$dsp    = $base->findOne(array('_id' => $file['_id']));
				$imgsrc = $dsp->getBytes();
				?>
				<div class="autoToggle inline  blanc aligncenter margin " style="width:120px;">
					<div class="applink applinkblock" onclick="saveimage('<?= $file['filename'] ?>')">
						<a>
							<img src="data:image/jpeg;base64,<?= base64_encode($imgsrc) ?>">
						</a>
					</div>
					<div class="ellipsis"><?= coupeChaineMilieu($file['filename'], 15) ?> </div>
				</div>
			<? } ?>
	</div>
	<div class="padding" auto_tree>
		<div><?= idioma('Personel') ?></div>
	</div>
	<div style="position:relative;min-height:150px;" id="drag_perso">
		<?
			$base = $APP->plug_base('sitebase_image')->getGridFs('wallpaper');
			$rs   = $base->find(array('metadata.thumb' => 1, 'metadata.idagent' => $_SESSION['idagent']))->sort(array('metadata.date' => -1));

			while ($arr = $rs->getNext()) {
				$file   = $arr->file;
				$dsp    = $base->findOne(array('_id' => $file['_id']));
				$imgsrc = $dsp->getBytes();
				?>
				<div class="autoToggle inline  blanc aligncenter margin " style="width:120px;">
					<div class="applink applinkblock" onclick="saveimage('<?= $file['filename'] ?>')">
						<a>
							<img src="data:image/jpeg;base64,<?= base64_encode($imgsrc) ?>">
						</a>
					</div>
					<div class="ellipsis"><?= coupeChaineMilieu($file['filename'], 15) ?> </div>
				</div>
			<? } ?>
	</div>
</div>
<form novalidate id="form_system" action="mdl/app/app_img/actions.php" onsubmit="ajaxFormValidation(this);return false"  >
	<input type="hidden" name="F_action" value="addDoc" />
	<input type="hidden" name="base" value="sitebase_image" />
	<input type="hidden" name="collection" value="wallpaper" />
	<input type="hidden" name="codeTailleImage" value="wallpaper" />
	<input type="hidden" name="act_thumb" value="1" />
	<input type="hidden" name="multiple" value="1" />
	<input type="hidden" name="table" value="agent" />
	<input type="hidden" name="table_value" value="<?=$_SESSION['idagent']?>" />
	<input type="hidden" name="codeImage" value="system-wallpaper" />
	<input type="hidden" name="reloadModule[app/app_user_pref/app_wallpaper]" value="*" />
</form>
<form novalidate id="form_perso" action="mdl/app/app_img/actions.php" onsubmit="ajaxFormValidation(this);return false"  >
	<input type="hidden" name="F_action" value="addDoc" />
	<input type="hidden" name="base" value="sitebase_image" />
	<input type="hidden" name="collection" value="wallpaper" />
	<input type="hidden" name="codeTailleImage" value="wallpaper" />
	<input type="hidden" name="act_thumb" value="1" />
	<input type="hidden" name="multiple" value="1" />
	<input type="hidden" name="table" value="agent" />
	<input type="hidden" name="table_value" value="<?=$_SESSION['idagent']?>" />
	<input type="hidden" name="vars[idagent]" value="<?=$_SESSION['idagent']?>" />
	<input type="hidden" name="reloadModule[app/app_user_pref/app_wallpaper]" value="<?=$_SESSION['idagent']?>" />
</form>
<div id="pref_preview" class="aligncenter ededed" style="overflow:auto"></div>
<script>
	new myddeAttach($('drag_system'), {form: 'form_system', autoSubmit: true, preview_zone: 'pref_preview'});
	new myddeAttach($('drag_perso'), {form: 'form_perso', autoSubmit: true, preview_zone: 'pref_preview'});
</script>
<style>
	#pref_preview img {
		max-width: 150px;
	}
</style>
<div class="spacer"></div>
<script>
	saveimage = function (wall) {
		ajaxValidation('setWallPaper', 'mdl/app/app_user_pref/', 'wallpaper=' + wall);
	}
</script>
<script>
	delimage = function (wall, rep) {
		ajaxValidation('delWallPaper', '', 'rep=' + rep + '&wallpaper=' + wall);
	}
</script>