<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$uniqid = uniqid();
?>
<div class="flex_v blanc" id="explorer<?= $uniqid ?>">
	<div class="relative borderb">
		<div class="titre_entete ededed uppercase bold">
			<a expl_html_title onclick="reloadModule('app_document/app_document','*')">Documents</a>
		</div>
	</div>
	<div class="borderb">
		<?= skelMdl::cf_module('app/app_document/app_document_nav', array('defer'=>true,'scope' => 'document', 'document' => $_SESSION['idagent']), $_SESSION['idagent']) ?>
	</div>
	<div id="fullzone<?= $uniqid ?>" class="flex_main flex_h">
		<div expl_left_zone="expl_left_zone" class="flex_main frmCol1 ededed" style="overflow:auto;">
			<?= skelMdl::cf_module('app/app_document/app_document_tag_liste', array()) ?>
		</div>
		<div expl_file_zone id="dragzone<?= $uniqid ?>" class="flex_main">
			<?= skelMdl::cf_module('app/app_document/document_liste', array(), $_SESSION['idagent']) ?>
		</div>
	</div>
</div>
<div style="display:none"><?//= skelMdl::cf_module('app_document/document_spy', array('document' => $_SESSION['idagent']), $_SESSION['idagent']) ?></div>
<script>
	new myddeExplorer($('explorer<?=$uniqid?>'));
</script>
