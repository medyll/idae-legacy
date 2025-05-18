<?
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];
	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];

	$dad_foradzone       = uniqid($table);
	$for_patolon_bismuth = uniqid($table);
	$patolon_bismuth     = uniqid($table);
	$forward_zone        = uniqid($table);
	$forward_zone_entete = uniqid($table);

	$TEST_AGENT = $APP->has_agent();

?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;">
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="titre_entete flex_h">
			<div class="flex_main"><i class="fa fa-<?= $APP->iconAppscheme ?>"></i> <?= idioma('tri') ?> <?= ucfirst($table) ?>s</div>
			<div class="applink">
				<a onclick="reloadModule('<?= $_POST['mdl'] ?>','*')"><i class="fa fa-refresh"></i></a>
			</div>
		</div>
		<div class="flex_main " style=" overflow:hidden;">
			<div class="flex_v" style=" overflow:hidden;width:100%;">
				<div app_gui_explorer id="<?= $dad_foradzone ?>" class="flex_main flex_v" style="overflow:hidden;">
								<div class="flex_h flex_align_bottom">
						<? if (!empty($TEST_AGENT)): ?>
							<div class="app_onglet toggler  applink flex_h">
								<a class="autoToggle textvert" app_button="app_button" vars="<?= $HTTP_VARS ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>"><i class="fa fa-user"></i></a>
								<a class="autoToggle textorange" app_button="app_button" vars="<?= $HTTP_VARS_NOAGENT ?>"><i class="fa fa-globe"></i></a>
							</div>
						<? endif; ?>
						<div class="app_onglet toggler flex_main">
							<?
								$arr_has = ['statut', 'type', 'categorie', 'group'];
								foreach ($arr_has as $key => $value):
									$Value = ucfirst($value);
									if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
										<a class="autoToggle" act_target="<?= $forward_zone ?>" mdl="app/app/app_dispatch_inner" vars="table=<?= $table ?>&type=<?= $value ?>">
											<?= ucfirst(idioma($Value)) ?>
										</a>
									<? endif; ?>
								<? endforeach; ?>
						</div>
					</div>
					<div expl_left_zone id="<?= $forward_zone ?>" class="flex_h flex_margin" style="height:100%;overflow-y: hidden;overflow-x: auto;">
					</div>
				</div>
			</div>
		</div>
		<div class="padding ededed bordert">
			<br>
		</div>
	</div>
</div>
<script>
	load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>', '<?=$patolon_bismuth?>');
	$('<?=$patolon_bismuth?>').show();

	$('<?=$for_patolon_bismuth?>').on('click', '[data-table][data-table_value]', function (event, node) {
		var table = node.readAttribute('data-table');
		var table_value = node.readAttribute('data-table_value');
		var div = new Element('div');
		$('<?=$forward_zone?>').appendChild(div);
		div.loadModule('app/app/app_fiche_forward', 'table=' + table + '&table_value=' + table_value);
	})
	$('<?=$dad_foradzone?>').on('click', '[data-link][data-table][data-table_value]', function (event, node) {

		var table = node.readAttribute('data-table');
		var table_value = node.readAttribute('data-table_value');

		$$('#<?=$dad_foradzone?> [data-link][data-table=' + table + ']').each(function (renode) {
			var value = renode.readAttribute('data-table_value')
			renode.up('.forwarder').select('.forwarder_zone_fiche').first().show();
			renode.up('.forwarder').select('.forwarder_zone_fiche').first().loadModule('app/app/app_fiche_mini', 'table=' + table + '&table_value=' + value);
		}.bind(this))

		//	nav_forward($(node),'app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	$('<?=$dad_foradzone?>').on('click', '[data-link][data-table][data-vars]', function (event, node) {

		var table = node.readAttribute('data-table');
		var vars = node.readAttribute('data-vars');
		var value = node.up('[data-table_value]').readAttribute('data-table_value')

		$$('#<?=$dad_foradzone?> [data-link][data-table=' + table + '][data-vars]').each(function (renode) {
			var f_node = renode;
			var retable = renode.readAttribute('data-table');
			var revars = renode.readAttribute('data-vars');

			f_node.up('.forwarder').select('.forwarder_zone_fiche').first().show();
			f_node.up('.forwarder').select('.forwarder_zone_fiche').first().loadModule('app/app/app_fiche_forward_liste', 'table=' + retable + '&' + revars);
			console.log(retable, revars);
		}.bind(this))

	})
</script>