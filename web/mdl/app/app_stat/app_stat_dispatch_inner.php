<?
	include_once($_SERVER['CONF_INC']);
	// nbre par date
	ini_set('display_errors', 55);
	$table          = $_POST['table'];
	$app_stat_scope = empty($_POST['app_stat_scope']) ? 'app_stat_scope' : $_POST['app_stat_scope'];
	$scope_str      = "scope='$app_stat_scope'";
?>
<div class="flex_h flex_align_stretch" style="height:100%;">
	<div class="flex_main ededed" style="overflow-y:auto;overflow-x:hidden;">
		<div>
			<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_dispatch_sum" vars="<?= http_build_query($_POST) ?>&type_stat=categorie"></div>
		</div>
		<div class="margin_more blanc border4 boxshadow">
			<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_dispatch_val" vars="<?= http_build_query($_POST) ?>"></div>
		</div>
		<div class="margin_more blanc border4 boxshadow">
			<div>
				<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_draw" vars="<?= http_build_query($_POST) ?>&type_stat=general"></div>
			</div>
		</div>
	</div>
	<div style="width:25%;overflow-y:auto;overflow-x:hidden;" class="ededed  flex_v flex_align_stretch">
		<div class="margin_more blanc border4 boxshadow">
			<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_draw" vars="<?= http_build_query($_POST) ?>&type_stat=type"></div>
		</div>
		<div class="margin_more blanc border4 boxshadow">
			<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_draw" vars="<?= http_build_query($_POST) ?>&type_stat=categorie"></div>
		</div>
		<div class="margin_more blanc border4 boxshadow">
			<div <?= $scope_str ?> act_defer mdl="app/app_stat/app_stat_draw" vars="<?= http_build_query($_POST) ?>&type_stat=statut"></div>
		</div>
	</div>
</div>



