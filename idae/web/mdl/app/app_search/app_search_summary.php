<?php
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	$vars   = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$vars   = array_filter($vars);
	$table  = $_POST['table'];
	$id     = 'id' . $_POST['table'];
	$Table  = ucfirst($table);
	$nom    = 'nom' . ucfirst($table);
	$prenom = 'prenom' . ucfirst($table);
	$code   = 'code' . ucfirst($table);
	//
	//
	$APP = new App($table);
	if (!empty($_POST['vars_in'])) {
		foreach ($_POST['vars_in'] as $key_vars => $value_vars):
			$value_vars['$in'] = json_decode($value_vars['$in']);
			$vars[$key_vars]   = $value_vars;
		endforeach;
	}
	//  vardump($vars);
	$HTTP_VARS  = $APP->translate_vars($vars);
	$APP_TABLE  = $APP->app_table_one;
	$ARR_FIELDS = $APP->get_field_list();

	$add = [];

	//
	if (!empty($_POST['search'])):
		$search    = trim($_POST['search']);
		$arrSearch = explode(' ', trim($search));
		foreach ($arrSearch as $key => $value) {
			// $out[] = MongoCompat::toRegex(".*" . (string)$arrSearch[$key] . "*.", 'i');
		}
		$search_escaped = MongoCompat::escapeRegex((string)$search);
		$out[] = MongoCompat::toRegex($search_escaped, 'i');
		if (sizeof($out) == 1) {
			$add = ['$or' => [[$nom => ['$all' => $out]], [$id => (int)$_POST['search']], ['code' . $Table => ['$in' => $out]], [$prenom => ['$in' => $out]]]];
		}
		if (is_int($_POST['search'])):
			$add['$or'][] = [$id => (int)$_POST['search']];
		endif;
		$rs = $APP->find($vars + $add, ['sort' => ['nom' . $Table => 1], 'limit' => 5]);
	// vardump_async(array_merge( $vars , $add),true);
	else:
		$rs = $APP->find($vars, ['sort' => ['nom' . $Table => 1], 'limit' => 5]);
	endif;

	if(empty($vars) && empty($add)) return;
	$count = $rs->count(false);

?>
<div class="boxshadow    ">
	<div class="flex_h flex_align_middle" style="background-color: <?=$APP->colorAppscheme?>">
		<div class="padding_more     flex_main"><span class="bold">Résultats <?= $count ?></span></div>
		<div class="padding_more cursor edededhover"><i class="fa fa-times-circle-o fa-2x textrouge"></i></div>
	</div>
	<div class="applink   applinkblock ededed borderb">
		<? while ($arr = $rs->getNext()) {

			?>
			<a title="<?= $arr[$id] ?>" class="autoToggle app_select borderb"
			   onclick="$(this).fire('dom:act_click',{value:'<?= niceUrlSpace($arr[$nom]) ?>',code:'<?= $arr[$code] ?>',id:'<?= $arr[$id] ?>',table:'<?= $table ?>'})">
				<div data-menu="data-appmenu" data-clone="true" class="    flex_h flex_align_middle">
					<div style="width:20px;"><i class="fa fa-<?= $APP->iconAppscheme ?>" style="color:<?= $APP->colorAppscheme ?>"></i></div>
					<div class="flex_main"><span class="ellipsis"><?= $arr[$nom] ?> <?= htmlspecialchars(empty($arr[$prenom]) ? '' : ' ' . $arr[$prenom]); ?></span></div>
					<? if (array_key_exists('color' . $Table, $ARR_FIELDS) && $arr['color' . $Table]) { ?>
						<div style="width:15px;text-align:center;"><span class="border4" style="background-color: <?= $arr['color' . $Table] ?>">&nbsp;</span></div>
					<? } ?>
				</div>
				<div class="contextmenu" style="display:none">
					<div   class= "boxshadow   " act_defer mdl="app/app/app_fiche_thumb"
					       vars="table=<?= $table ?>&table_value=<?= $arr[$id] ?>"
					       value="<?= $arr[$id] ?>"></div>
				</div>
			</a>
		<? } ?>
	</div>
</div>
