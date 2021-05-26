<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$table = $_POST['table'];
	$Table = ucfirst($table);
	$uniqid = uniqid($table);

	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	$APP = new App($table);
	$HTTP_VARS = $APP->translate_vars($vars);
	$rs = $APP->query($vars);
	$count = $rs->count();
	if($count==0) return;
	if($count<= 8):
		$str = 'data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_micro"';
	else:
		$str = 'data-dsp="line"';
	endif;
?>
<div style="height:100%;width:230px;overflow:hidden;" class="flex_v relative borderr blanc forwarder boxshadow" app_gui_explorer>
	<div class="titre_entete flex_h applink">
		<span class="flex_main ellipsis"><?=$rs->count()?><i class="fa fa-<?= $APP_TABLE['icon'] ?> "></i><?= $Table ?> <?=$APP->get_titre_vars($vars)?></span>

	</div>
	<div class="none padding ededed applink borderb alignright toggler">

		<a class="autoToggle" data-button-dsp="mdl" data-dsp-mdl="app/app/app_fiche_micro" >
			<i class="fa fa-list-alt "></i>
		</a>
		<a class="autoToggle" data-button-dsp="fields" >
			<i class="fa fa-align-left "></i>
		</a>
		<a class="autoToggle" data-button-dsp="line" >
			<i class="fa fa-list "></i>
		</a>
		<a onclick="<?=fonctionsJs::app_liste($table,'',$HTTP_VARS)?>">
			<i class="fa fa-external-link"></i>
		</a>
	</div>
	<div style="overflow:hidden" class="flex_main" id="forwarder_zone_liste_<?= $uniqid ?>" data-data_model="defaultModel"  <?=$str?> expl_file_list >

	</div>
</div>
<div class="forwarder_zone flex_h" style="overflow:hidden;height:100%;"></div>
<script>
	load_table_in_zone('table=<?=$table?>&<?=$HTTP_VARS?>', 'forwarder_zone_liste_<?=$uniqid?>');
</script>
