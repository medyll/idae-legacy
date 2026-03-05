<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	//
	ini_set('display_errors', 55);
	$APP  = new App();
	$coll = $APP->plug('sitebase_pref', 'agent_pref');
	$mdl  = 'app/app_user_pref/app_user_pref_scheme';
	$code = $_POST['code'];
	$text = empty($_POST['text']) ? '' : $_POST['text'];
	$css  = empty($_POST['css']) ? '' : $_POST['css'];
	$test = $coll->find(['idagent' => (int)$_SESSION['idagent'], 'valeurAgent_pref' => 'true', 'codeAgent_pref' => MongoCompat::toRegex(".*" . MongoCompat::escapeRegex($code) . "*.", 'i')]);
?>
<div class="inline">
	<div class="flex_h flex_align_middle">
		<a class="<?= $css ?>" act_chrome_gui="app/app_user_pref/app_user_pref" data-vars='mdl=<?= $mdl ?>&code=<?= $code ?>'>
			<? if ($test->count() == 0): ?>
				<span class="textorange   inline   "><i class="fa fa-exclamation"></i></span>
			<? endif ?>
			<i class="fa fa-cog"></i>
			<?= $text ?>
		</a>
	</div>
</div>