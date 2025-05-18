<?
	include_once($_SERVER['CONF_INC']);
	$time = time();
?>
<div style="width:350px;">
	<div class="enteteFor">
		<div class="titre_entete"><span class="titre">
      <?= idioma('Suppression') ?>
				Image</span></div>
	</div>
	<form action="mdl/app/app_img/actions.php" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden" name="reloadScope[app_img]" value="<?= $_POST['src'] ?>"/>
		<input type="hidden" name="deleteModule[production/image/imageInner]" value="<?= $_POST['src'] ?>"/>
		<input type="hidden" name="F_action" value="deleteImageMongo"/>
		<input type="hidden" name="src" value="<?= $_POST['src'] ?>.jpg"/>
		<table>
			<tr>
				<td class="padding_more ededed borderr" style="width:90px;text-align:center">
					<img class="boxshadow" src="<?= Act::imgSrc($_POST['src']) ?>?<?= $time ?>" width="120"/>
				</td>
				<td class="texterouge padding">
					<br>
					Voulez vous supprimer
					<br>
					cette image ?
					<br>
					<?= $_POST['src'] ?>
					<br>
				</td>
			</tr>
		</table>
		<div class="buttonZone">
			<input type="submit" value="Supprimer">
			<input type="button" value="Annuler" class="cancelClose">
		</div>
	</form>
</div>
