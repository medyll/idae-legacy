<?
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	//
	$uniqid        = uniqid();
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];
	$namespace_app = empty($_POST['vars']['namespace_app']) ? 'prod' : $_POST['vars']['namespace_app'];
	$expl_file     = empty($_POST['expl_file']) ? 'app/app_prod/app_prod_liste' : $_POST['expl_file'];

	$arr_sc = $APP->get_one_scheme($_POST['vars']);
	$table = $_POST['table'];

	$dad_foradzone = uniqid($table);
	$for_patolon_bismuth= uniqid($table);
	$patolon_bismuth= uniqid($table);
	$forward_zone= uniqid($table);
	$forward_zone_entete= uniqid($table);
?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;"  >
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="titre_entete " syle="z-index:10">
			<h3><i class="fa fa-<?=$arr_sc['icon']?>"></i> <?=idioma('Recherche')?> <?=ucfirst($table)?></h3>
		</div>
		<div class="flex_main " style="position:relative;overflow:hidden;">
			<div style="position: absolute;height: 100%;width: 100%;">
				<div class="flex_h" style="height:100%;overflow: hidden;">
					<div class="frmCol1 ededed">
						<div class="padding ededed borderb aligncenter">
							<form onsubmit="load_table_in_zone($(this).serialize(),'<?=$patolon_bismuth?>');$('<?=$patolon_bismuth?>').show();return false;">
								<input type="hidden" name="table" value="<?=$table?>">
								<button type="submit" style="position:absolute;right: 0.5em; z-index: 10;border: none;background-color: transparent;"><i class="fa fa-search"></i></button>
								<input placeholder="Recherche" name="search"
								       style="position: relative;margin-right:0px;z-index:1;width:100%;line-height:2" value=""
								       type="text" class=""/>
								<br>
								<br>
							</form>
						</div>
						<div class="flex_h toggler applink applinkblock">
							<div class="flex_main aligncenter"><a class="autoToggle active" onclick="load_table_in_zone('table=agent_tuile&vars[codeAgent_tuile]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-desktop"></i><br> text</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle" onclick="load_table_in_zone('sortBy=quantiteAgent_history&sortDir=-1&table=agent_history&vars[codeAgent_history]=<?=$table?>','<?=$patolon_bismuth?>');"><i class="fa fa-history"></i><br> historique</a></div>
							<div class="flex_main aligncenter"><a class="autoToggle"><i class="fa fa-folder-o"></i><br> tout</a></div>
						</div>
						<div id="<?=$for_patolon_bismuth?>" class="flex_v frmCol1 blanc shadowbox " style="overflow:hidden;">
							<div class="flex_main ededed applink applinkblock" id="<?=$patolon_bismuth?>"  data-dsp="line" ></div>
						</div>
					</div>
					<div id="<?=$dad_foradzone?>" class="flex_main " style="overflow:hidden;">
						<div id="forward_zone_entete" ></div>
						 <div id="forward_zone" class="borderr   relative flex_h" style="height:100%;overflow: hidden">
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
		$('forward_zone').loadModule('app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
		// $('forward_zone_entete').loadModule('app/app/app_fiche_maxi_entete','table='+table+'&table_value='+table_value);
	})
	$('<?=$dad_foradzone?>').on('click','[data-link][data-table][data-table_value]',function(event,node){
		var table =node.readAttribute('data-table');
		var table_value =node.readAttribute('data-table_value');
		nav_forward($(node),'app/app/app_fiche_forward','table='+table+'&table_value='+table_value);
	})
	$('<?=$dad_foradzone?>').on('click','[data-link][data-table][data-vars]',function(event,node){
		var table =node.readAttribute('data-table');
		var vars =node.readAttribute('data-vars');
		nav_forward($(node),'app/app/app_fiche_forward_liste','table='+table+'&'+vars);
	})
</script>
<script>
	nav_forward = function(node,mdl,vars){
		// var daid = $(node).identify();
		var daid = node.up('.forwarder').next();
		$(daid).loadModule(mdl,vars);
	}
</script>