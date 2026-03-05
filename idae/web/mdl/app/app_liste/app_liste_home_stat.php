<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	if (empty($_POST['table']))
		return;
	ini_set('display_errors', 55);
	//
	$table = $_POST['table'];

	//
	$APP = new App($table);
	//

	$arrFieldsBool = $APP->get_array_field_bool();
	//
	$id = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	//
	//
	$whereT = array();
	if (!empty($_POST['search'])) {
		$regexp = MongoCompat::toRegex(MongoCompat::escapeRegex($_POST['search']), 'i');
		$whereT = array('$or' => array(array($nom => $regexp), array($id => (int)$_POST['search'])));
	}

?>
<div class="flex_h">
	<?
		foreach ($arrFieldsBool as $field => $arr_ico):
			$field_name_bool = $field . ucfirst($table);
		$rs1 = $APP->query([$field_name_bool => 1])->count();
		$rs2 = $APP->query([$field_name_bool => ['$ne' => 1]])->count();
		?>
		<div class="border4 aligncenter margin" style="width:100%;">
		<div class="ellipsis padding aligncenter" >
			<i class = "fa fa-<?= $arr_ico[0] ?>"></i><?= $rs1 ?> |
			<i class = "fa fa-<?= $arr_ico[1] ?>"></i><?= $rs2 ?></div>
		</div>
	<?
	endforeach; ?>
</div>