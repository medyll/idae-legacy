<?
	include_once($_SERVER['CONF_INC']);
	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';
?>
<div style="width:750px;">
	<form>
		<input type="hidden" name="F_action" value="goProduction"/>
		<table class="tabletop">
			<tr>
				<td style="width:90px;text-align:center">
					<br>
					<img src="<?= ICONPATH ?>alert32.png"/>
				</td>
				<td class="texterouge">
					<br>
					Voulez vous lancer une synchro xml pour <?= $_POST['fourn'] ?> ?
					<br>
					<br>
					<div><?= skelMdl::cf_module($PATH.'xml_bar', ['emptyModule' => true], $_SESSION['idagent']); ?></div>
					<div><?= skelMdl::cf_module($PATH.'xml_bar_info', ['emptyModule' => true], $_SESSION['idagent']); ?></div>
				</td>
			</tr>
		</table>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlt').show().loadModule('<?=$PATH?>xml_thread','fourn=<?= $_POST['fourn'] ?>');">
			<input type="reset" value="Fermer" class="cancelClose">
		</div>
	</form>
	<div style="width:100%;min-height:350px;border:none;overflow:auto;" id="frame_xmlt" scrolling="auto"></div>
	<!--<iframe style="width:100%;height:350px;border:none;display:none;overflow:auto;" id="frame_xmlt" scrolling="auto"></iframe>-->
</div>
<!--$('frame_xmlt').show().src='http://<?= DOCUMENTDOMAIN ?>/mdl/xml/xml_thread.php?fourn=<?= $_GET['fourn'] ?>';-->