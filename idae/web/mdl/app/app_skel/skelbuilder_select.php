<?
	if (file_exists('../conf.inc.php')) include_once('../conf.inc.php');
	if (file_exists('../../conf.inc.php')) include_once('../../conf.inc.php');
	if (file_exists('../../../conf.inc.php')) include_once('../../../conf.inc.php');
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	// ini_set('display_errors',55);
	$time   = uniqid();
	$whereT = [];
	$vars   = empty($_POST['vars']) ? [] : $_POST['vars'];
	//	vardump($vars);
	if (!empty($_POST['typeInput'])) {
		$search_escaped = MongoCompat::escapeRegex($_POST['typeInput']);
		$regexp = MongoCompat::toRegex($search_escaped, 'i');
		$whereT = ["typeInput" => $regexp];
	}
	//
	if (!empty($_POST['search'])) {
		$or = ['$or' => [['idproduit' => (int)$_POST['search']], ['arrTag.word' => ['$all' => $out]]]];
	}

	$rs = skelMongo::connect('skel_builder_type', 'sitebase_skelbuilder')->find($whereT + $vars)->sort(['typeInput' => 1])->limit(50);
?>
<div class="relative applink applinkblock autoToggle" style="max-height:350px;overflow:auto;">
	<? while ($arr = $rs->getNext()) {
		?>
		<a class="relative inline" onclick='$(this).fire("dom:datechoosen",{value: "<?= addslashes(niceUrl($arr["typeInput"])) ?>",id:"<?= $arr["typeInput"] ?>"})'>
			<?= $arr["typeInput"] ?>
		</a>
	<? } ?>
</div>
