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
	$APP = new App($table);
	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];


	$dad_foradzone = uniqid($table);
	$for_patolon_bismuth= uniqid($table);
	$patolon_bismuth= uniqid($table);
	$forward_zone= uniqid($table);
	$forward_zone_entete= uniqid($table);
?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;"  >
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="titre_entete" syle="z-index:10">
			<i class="fa fa-<?= $APP->iconAppscheme?>"></i> <?=idioma('Comparer')?> <?=ucfirst($table)?>s
		</div>
		<div class="flex_main " style="position:relative;overflow:hidden;">
			<div style="position: absolute;height: 100%;width: 100%;">
				<div class="flex_h" style="height:100%;overflow: hidden;">
					<div class="frmCol1 ededed">
						<div class="padding ededed borderb aligncenter">
							<script>
								// main_item_search_finder = new BuildSearch('<?=$patolon_bismuth?>');
							</script>
							<form
								onsubmit="load_table_in_zone($(this).serialize(),'<?=$patolon_bismuth?>');$('<?=$patolon_bismuth?>').show();return false;">
								<input type="hidden" name="table" value="<?=$table?>">
								<button type="submit" style="position:absolute;right: 0.5em; z-index: 10;border: none;background-color: transparent;"><i class="fa fa-search"></i></button>
								<input placeholder="Recherche" name="search" style="position: relative;margin-right:0px;z-index:1;width:100%;line-height:2" value="" type="text" class="border4"/>
								<br>
							</form>
						</div>
						<div class="flex_h toggler applink applinkblock">
							<div class="flex_main aligncenter"><a class="autoToggle active" onclick="load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-desktop"></i><br> tuiles</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle" onclick="load_table_in_zone('sortBy=quantiteAgent_history&sortDir=-1&table=agent_history&vars[codeAgent_history]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-history"></i><br> historique</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle"><i class="fa fa-folder-o"></i><br> tout</a></div>
						</div>
						<div id="<?=$for_patolon_bismuth?>" class="flex_v frmCol1 blanc shadowbox " style="overflow:hidden;">
							<div class="flex_main ededed applink applinkblock" id="<?=$patolon_bismuth?>"  data-dsp="line" ></div>
						</div>
					</div>
					<div id="<?=$dad_foradzone?>" class="flex_main " style="overflow:hidden;">
						<div id="<?=$forward_zone?>" class="flex_h" style="height:100%;overflow-y: hidden;overflow-x: auto;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');
	$('<?=$patolon_bismuth?>').show();

	$('<?=$for_patolon_bismuth?>').on('click','[data-table][data-table_value]',function(event,node){
		var table =node.readAttribute('data-table');
		var table_value =node.readAttribute('data-table_value');
		var div = new Element('div');
		$('<?=$forward_zone?>').appendChild(div);
		div.loadModule('app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	$('<?=$dad_foradzone?>').on('click','[data-link][data-table][data-table_value]',function(event,node){

		var table =node.readAttribute('data-table');
		var table_value =node.readAttribute('data-table_value');

		$$('#<?=$dad_foradzone?> [data-link][data-table='+table+']').each(function(renode){
			var value = renode.readAttribute('data-table_value')
			renode.up('.forwarder').select('.forwarder_zone_fiche').first().show();
			renode.up('.forwarder').select('.forwarder_zone_fiche').first().loadModule('app/app/app_fiche_mini','table='+table+'&table_value='+value);
		}.bind(this))

	//	nav_forward($(node),'app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	$('<?=$dad_foradzone?>').on('click','[data-link][data-table][data-vars]',function(event,node){

		var table =node.readAttribute('data-table');
		var vars =node.readAttribute('data-vars');
		var value = node.up('[data-table_value]').readAttribute('data-table_value')

		$$('#<?=$dad_foradzone?> [data-link][data-table='+table+'][data-vars]').each(function(renode){
			var f_node = renode;
			var retable =renode.readAttribute('data-table');
			var revars =renode.readAttribute('data-vars');

			f_node.up('.forwarder').select('.forwarder_zone_fiche').first().show();
			f_node.up('.forwarder').select('.forwarder_zone_fiche').first().loadModule('app/app/app_fiche_forward_liste','table='+retable+'&'+revars);
			console.log(retable,revars);
		}.bind(this))

	})
</script>