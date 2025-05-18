<?
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
$('loader <?=$uniqid?>').up().observe('dom:appreload',function(){
		             //  reloadModule('app_document/app_document'_tag_queue','<?= $idagent ?>');
		              });
		              pleaseTag=function(tag){
		              vars = Form.serialize($('skel<?= $uniqid ?>'));
		              ajaxValidation('tagDocument','mdl/document/','<?= http_build_query($_POST) ?>&'+vars+'&tag='+tag);
		              }
		</script>