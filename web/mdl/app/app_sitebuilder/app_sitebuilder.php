<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();

	$APP->init_scheme('sitebase_base', 'appsitebuilder', ['fields' => ['nom', 'code', 'url']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_skin', ['fields' => ['nom', 'code']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page', ['fields' => ['nom', 'code', 'url', 'ordre'], 'has' => ['type']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_module', ['fields' => ['nom', 'code', 'url', 'ordre'], 'has' => ['type'], 'grilleFK' => ['appsitebuilder_page']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_menu', ['fields' => ['nom', 'code', 'html'], 'has' => ['type'], 'grilleFK' => ['appsitebuilder_page']]);
	$APP->init_scheme('sitebase_base', 'appsitebuilder_page_template', ['fields' => ['nom', 'code', 'html']]);

?>
<div style="height:100%;overflow:hidden;" class="flex_v blanc" >
	<div class="titre_entete">
		<?= idioma('..') ?>
	</div>
	<div class="flex_h flex_main">
		<div class="frmCol1 flex_v">
			<div class="titre_entete">
				<a onclick="<?= fonctionsJs::app_create('appsitebuilder_page'); ?>"><?= idioma('Ajouter une page') ?></a>
			</div>
			<div class="flex_main ededed" id="zone_agent_tuile" data-dsp_liste="dsp_lists" data-vars="table=agent_tuile&vars[idagent]=<?= $_SESSION['idagent'] ?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini"
			     style=" resize: horizontal;"></div>
		</div>
		<div class="frmCol2" app_gui_explorer>
			<div class="flex_h" style="height:100%;" expl_left_zone>
				<div class="frmCol2" id="dropzone_site">
					<div class="applink applinkblock  " dropzone data-vars="vars[dsds]=fdsfds" style="height:100%;">
					</div>
				</div>
				<div class="frmCol1 ededed applink applinkblock">
					<a draggable="true" data-vars="table=&table_value=" data-type="element" data-className="border4  flex_main" >element</a>
					<a draggable="true" data-vars="table=&table_value=" data-className="flex_h flex_main margin_more">container ligne</a>
					<a draggable="true" data-vars="table=&table_value=" data-className="flex_v flex_main margin_more">container colonne</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$ ('dropzone_site').on ('dom:act_drop', function (event, node) {
		var node      = $ (node)
		var drop_node = $ (event.memo.drop_node);
		// if ( !node.readAttribute ('dropzone') ) node = node.up ('[dropzone]')
		Event.stop (event);
		var className = drop_node.readAttribute ('data-className') || '';
		var type      = drop_node.readAttribute ('data-type') || 'normal';
		switch (type) {
			case 'normal' :
				var  inserted_node = create_element_of('<div class="padding ededed"><div class="padding borderb">'+className+'</div> <div dropzone="dropzone" class="blanc padding_more   border4"></div></div>');
				inserted_node.select('[dropzone]').first().addClassName(className);
				node.insert (inserted_node);
				break;
			case 'element' :
				var  inserted_node = create_element_of('<div class="padding flex_main"><div act_defer mdl="app/app_sitebuilder/app_sitebuilder_element"></div></div>');

				// inserted_node.select('[dropzone]').first().addClassName(className);
				node.insert (inserted_node);
				break;
		}
		register_site_module (node);
	})
	function register_site_module(node) {
		// on post id , et au retour on inscrit id
		// ajaxValidation();
	}
</script>
<style>
	#dropzone_site .flex_v { height : auto; }
</style>