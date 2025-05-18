<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors',55);

	$uniqid = uniqid();
	$table = 'devis';
	$path_to_devis = 'business/'.BUSINESS.'/app/' . $table . '/' ;
	$APP = new App();
	$APP->init_scheme('sitebase_devis', 'devis_prestation');
	$APP->init_scheme('sitebase_devis', 'devis_acompte');
	$APP->init_scheme('sitebase_devis', 'devis_passager');
	$APP->init_scheme('sitebase_devis', 'devis_annotation');
	//

if(empty($_POST['BIG_SCREEN'])){
	?>
<div id="dev<?=$uniqid?>"  ></div>
	<script>
		 $('dev<?=$uniqid?>').fire('dom:close')
		 ajaxInMdl('<?=$path_to_devis?>devis_create','nouveau_devis','BIG_SCREEN=1&<?=http_build_query($_POST)?>',{onglet:'Nouveau devis'});
	</script>
	<?
	return;
}
?>
<div id="dev<?=$uniqid?>" style="width:100%;position:relative;height:100%;" class="blanc">
	<div class="flex_v" style="height:100%;">
		<div class="titre_entete">
			<img src="<?= ICONPATH ?>tarif16.png"/> <?= idioma('Nouveau devis') ?>
		</div>
		<div class="flex_h flex_main">
			<div style="overflow:hidden;right:0;top:0;bottom:0;width:50%;z-index:300;display:none;" id="div_devis_create_wait" class="blanc absolute borderl boxshadow"></div>
			<div class="frmCol1">
				<div class="autoNext avoid"><?= idioma('Client') ?></div>
				<div class="retrait">
					<input id="cho_cli<?= $uniqid ?>" datalist_input_name="vars[idclient]" datalist_input_value="" datalist="app/app_select" populate name="vars[nomClient]" paramName="search" vars="table=client" value=""/>
				</div>
				<div class="autoNext avoid"><?= idioma('Voyage') ?></div>
				<div class="retrait">
					<input id="devis_create_zone" datalist_input_name="vars[idproduit]" datalist_input_value="" datalist="app/app_select" populate name="vars[nomProduit]" paramName="search" vars="table=produit" value=""/>
				</div>
				<div id="div_create_devis_wizard">
					<?= skelMdl::cf_module($path_to_devis.'devis_create_wizard', $_POST + array('uniqid' => $uniqid), 'wizard_' . $uniqid) ?>
				</div>
			</div>
			<div style="overflow:auto;width:100%;z-index:2000;height:150px;display:none" id="div_devis_app_select" class="blanc absolute applink applinkblock toggler boxshadow"></div>
			<div class="flex_v flex_main" style="overflow:auto;">
				<div>
					<form id="devis_form" name="devis_form" onsubmit="$('div_devis_create_wait').show().loadModule('<?=$path_to_devis?>devis_create_wait',$(this).serialize());return false" action="">
						<input type="hidden" name="vars[idclient]" id="tmp_idclient">
						<input type="hidden" name="vars[iddevis_type]" value="2">
						<input type="hidden" name="vars[idagent]" value="<?= $_SESSION['idagent'] ?>">
						<div id="div_devis_create_make"></div>
					</form>
				</div>
				<div id="div_produit_liste_devis"  class="borderb flex_main flex_v relative" app_gui_explorer>
					<div class="titre_entete" id="div_devis_create_produit">

					</div>
					<div expl_view_button="true" ></div>
					<div><?= skelMdl::cf_module('app/app_prod/app_prod_liste_menu_search',   array('table'=>'produit','uniqid' => $uniqid), 'wizard_' . $uniqid) ?></div>
					<div class="flex_main" id="table_produit_devis_make"   expl_file_zone  expl_file_list   data-data_model="defaultModel" >
					</div>
					<div class="absolute" expl_preview_zone="true" style="display:none;z-index:100;height:100%;width:30%;right:0;top:0;"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	<?
	if(!empty($_POST['idclient']) || !empty($_POST['idproduit']) ){?>
	$('div_produit_liste_devis').unToggleContent();
	$('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', '<?=http_build_query($_POST)?>');
	<? }?>

	$('devis_create_zone').observe('dom:act_change', function (event) {
		idproduit = event.memo.id
		$('div_produit_liste_devis').unToggleContent();
		$('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', 'idproduit=' + idproduit)
		Event.stop(event)
		reloadModule('<?=$path_to_devis?>devis_create_wizard', 'wizard_<?=$uniqid?>', 'idproduit=' + idproduit)
	}.bind(this))

	/*$('div_devis_search').observe('dom:act_change', function (event) {
		var form = Event.element(event);
		vars = form.serialize();

		$('div_devis_app_select').show().loadModule('app/app_liste/app_liste', 'table=produit&' + vars)

	}.bind(this))*/

	  $('cho_cli<?=$uniqid?>').observe('dom:act_change', function (event) {
		idclient = event.memo.id;
		$('tmp_idclient').value = idclient;
		reloadModule('<?=$path_to_devis?>devis_create_wizard', 'wizard_<?=$uniqid?>', 'idclient=' + idclient);

	}.bind(this))

	$('div_produit_liste_devis').on('click','tr', function (event,node) {
		console.log('click')
		idproduit = $(node).readAttribute('data-table_value');
		$('div_devis_create_produit').show().loadModule('<?=$path_to_devis?>devis_create_produit', 'idproduit=' + idproduit)

	}.bind(this))

	/* $('div_devis_app_select').observe('dom:act_click', function (event) {
		idproduit = event.memo.id;
		$('div_produit_liste_devis').unToggleContent();
		$('div_devis_create_make').loadModule('<?=$path_to_devis?>devis_create_make', 'idproduit=' + idproduit)
		Event.stop(event)
	}.bind(this))*/
</script>
