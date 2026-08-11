<?php
	include_once($_SERVER['CONF_INC']);
	$APP = new App('document');
	$RF_K = $APP->get_grille_fk('document');
	$RF_K_KEYS = array_keys($RF_K);
	$collection = 'ged_bin';
	$base       = 'sitebase_ged';

	$base = $APP->plug_base('sitebase_ged');
	$GED  = $base->ged;
	$grid = $base->getGridFs('ged_bin');

	$ct                 = $grid->find(['metadata.table'=> ['$nin'=>$RF_K_KEYS] ] );
	$dist                 = $grid->distinct('metadata.iddocument',['metadata.table'=> ['$nin'=>$RF_K_KEYS] ] );


	$uniqid  = uniqid();
	$_POST   = fonctionsProduction::cleanPostMongo($_POST);
	$idagent = (int)$_SESSION['idagent'];

	$arrTag               = array('FACTFOURN' => 'facture fournisseur', 'FACTPREST' => 'facture prestataire', 'CNICLI' => 'piece client', 'CONFIRMF' => 'confirm fournisseur', 'DOC' => 'document');

	$rs = $APP->find(['iddocument'=>['$in'=>$dist]]);
?>
<div id="loader<?= $uniqid ?>" style="width:750px;">
	<div class="titre_entete">Tag</div>
	<div class="titre_entete bordert relative" >
		<?=skelMdl::cf_module('app/app_field_add', array('display_mode' => 'vert', 'module_value' => 1235, 'field' => $RF_K_KEYS), 1235);?>
	</div>
	<div class="relative" id="skel<?= $uniqid ?>">
		 // list documents sans tag
  </div>
</div>
<script>
/*
 * Modified: 2026-08-11 — `$('loader <?=$uniqid?>')` has a stray space; the
 * real id is `loader<?=$uniqid?>` (no space), set two lines above. The lookup
 * has therefore always returned null, and `.up()` on null threw immediately —
 * this handler has never registered. Fixed the id and migrated off $/.up/
 * .observe to native DOM. The commented-out reloadModule call one line below
 * carries the same misplaced-quote bug fixed elsewhere in this migration
 * ('app_document/app_document'_tag_queue); left as a comment, not revived.
 */
document.getElementById('loader<?=$uniqid?>').parentNode.addEventListener('dom:appreload',function(){
		             //  reloadModule('app_document/app_document_tag_queue','<?= $idagent ?>');
		              });
		              pleaseTag=function(tag){
		              vars = Form.serialize(document.getElementById('skel<?= $uniqid ?>'));
		              ajaxValidation('tagDocument','mdl/document/','<?= http_build_query($_POST) ?>&'+vars+'&tag='+tag);
		              }
		</script>