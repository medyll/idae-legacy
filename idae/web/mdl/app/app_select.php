<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../appclasses/appcommon/MongoCompat.php');
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
			// $out[] = MongoCompat::toRegex(".*" . MongoCompat::escapeRegex((string)$arrSearch[$key]) . "*.", 'i');
		}
		$out[] = MongoCompat::toRegex(MongoCompat::escapeRegex((string)$search), 'i');
		if (sizeof($out) == 1) {
			$add = ['$or' => [[$nom => ['$all' => $out]], [$id => (int)$_POST['search']], ['code' . $Table => ['$in' => $out]], [$prenom => ['$in' => $out]]]];
		}
		if (is_int($_POST['search'])):
			$add['$or'][] = [$id => (int)$_POST['search']];
		endif;
		$rs = $APP->find($vars + $add)->sort(['nom' . $Table => 1,'ordre' . $Table => -1])->limit(250);
		// vardump_async(array_merge( $vars , $add),true);
	else:
		$rs = $APP->find($vars)->sort(['nom' . $Table => 1,'ordre' . $Table => -1])->limit(250);
	endif;

?>
<? while ($arr = $rs->getNext()) { ?>
	<a class="autoToggle app_select"
	   onclick="$(this).fire('dom:act_click',{value:'<?= niceUrlSpace($arr[$nom]) ?>',code:'<?= $arr[$code] ?>',id:'<?= $arr[$id] ?>',table:'<?= $table ?>'})">
		<div class="    flex_h flex_align_middle">
			<div class="textgris" style="width:40px;"><?= $arr[$id] ?></div>
			<? if (array_key_exists('icon' . $Table, $ARR_FIELDS) && $arr['icon' . $Table]) { ?>
				<div style="width:20px;"><i class="fa fa-<?= $arr['icon' . $Table] ?>"></i></div>
			<? } ?>
			<div class="flex_main"><span class="ellipsis"><?= $arr[$nom] ?> <?= htmlspecialchars(empty($arr[$prenom]) ? '' : ' ' . $arr[$prenom]); ?></span></div>

			<? if (array_key_exists('codePostal' . $Table, $ARR_FIELDS) && $arr['codePostal' . $Table]) { ?>
				<div style=" text-align:right;"><span   >&nbsp;<?= $arr['codePostal' . $Table] ?></span></div>
			<? } ?>

			<? if (array_key_exists('color' . $Table, $ARR_FIELDS) && $arr['color' . $Table]) { ?>
				<div style="width:15px;text-align:center;"><span class="border4" style="background-color: <?= $arr['color' . $Table] ?>">&nbsp;</span></div>
			<? } ?>

		</div>
	</a>
<? } ?>
<? if (droit_table($_SESSION['idagent'], 'C', $table)) { ?>
	<div class="padding_more bordert">
		<a class="border4 ededed bold padding_more" act_chrome_gui="app/app/app_create"
		   vars="table=<?= $table ?>&<?= $HTTP_VARS ?>&vars[nom<?= $Table ?>]=<?= $search ?>&reloadScope[app_select]=<?= $Table ?>"
		   options="{scope:'<?= $table ?>'}">
			  <i class="fa fa-plus-circle textvert"></i><?= idioma('Créer') . ' ' . $APP->nomAppscheme ?>
		</a>
	</div>
<? } ?>
<div class="none" data-need_more="<?= ($rs->count()) - ($rs->count(true)) ?>"></div>
