<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);

	$APP      = new App('agent');
	$arrAgent = $APP->findOne(['idagent' => (int)$_SESSION['idagent']]);
	// Act::imgApp('agent',$_SESSION['idagent'],'square');
?>
<div class="" style="text-align: left;">
	<div class="inline boxshadow" style="z-index:1;bottom:2em;right:2em;"  >
		<img src="<?=Act::imgApp('agent',$_SESSION['idagent'],'square');?>">
	</div>
	<div class=" aligncenter padding margin borderb bold"><?= $arrAgent['prenomAgent'] ?></div>
	<div class=" aligncenter padding margin">En ligne</div>
</div>