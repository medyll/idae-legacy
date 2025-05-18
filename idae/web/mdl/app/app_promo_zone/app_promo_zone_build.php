<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App('promo_zone');

	ini_set('display_errors', 55);
	$uniqid       = uniqid();
	$dropzone     = 'drop' . $uniqid;
	$dragzone     = 'dragzone' . $uniqid;
	$idpromo_zone = (int)$_POST['idpromo_zone'];
	$arr          = $APP->query_one(['idpromo_zone' => (int)$idpromo_zone]);
?>
<div class="flex_v relative blanc" id="explorer<?= $uniqid ?>">
	<div class="relative borderb">
		<div class="titre_entete ededed uppercase bold applink">
			<a expl_html_title onclick="reloadModule('app/app_promo_zone/app_promo_zone_build','*')">
				<i class="fa fa-refresh"></i>
				Zone promotion
				<?= $arr['nomPromo_zone'] ?>
			</a>
		</div>
	</div>
	<div class="none">
		<? //=skelMdl::cf_module('document/document_nav',array('scope'=>'document','document'=>$_SESSION['idagent']),$_SESSION['idagent'])?>
	</div>
	<div id="fullzone<?= $uniqid ?>" class="flex_main flex_h relative">
		<div id="<?= $dropzone ?>" class="frmCol11 ededed">
			<div data-dsp_liste="dsp_list" data-dsp-css="grid" data-vars="table=promo_zone_item&vars[idpromo_zone]=<?= $idpromo_zone ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_thumb"
			     style="max-width:700px;max-height:100%;resize: horizontal;">
			</div>
		</div>
		<div expl_file_zone class="frmCol2 flex_v">
			<div class="flex_main" id="<?= $dragzone ?>" style="overflow:hidden">
				<div class="" style="overflow:auto;background-size: auto;">

				</div>
			</div>
		</div>
	</div>
</div>
<script>
	new myddeExplorer ($ ('explorer<?=$uniqid?>'));

	$ ('<?=$dropzone?>').on ('click', '[data-table][data-table_value]', function (event, node) {
		var table       = node.readAttribute ('data-table');
		var table_value = node.readAttribute ('data-table_value');
		$ ('<?=$dragzone?>').loadModule ('app/app/app_update', 'table=' + table + '&table_value=' + table_value);
	})
</script>
<style>
	#django {
		height       : 5px;
		margin-right : 5px;
		margin-left  : 5px;
		position     : relative; background-color : #333;
	}
	.info_entete { margin : 0.5em; padding : 0.5em; }
</style>
