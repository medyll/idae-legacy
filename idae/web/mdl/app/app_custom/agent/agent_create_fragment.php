<?
	include_once($_SERVER['CONF_INC']);
	$time       = time();
	$idagent    = (int)$_POST['table_value'];
	$APP        = new App('agent');
	$arr        = $APP->query_one(array('idagent' => (int)$idagent));
	$arr_droits = ['ADMIN', 'DEV'];
?>

<div class="padding">
	<div class="padding borderb"><?= idioma('Droits d\'application') ?></div>
	<br>
	<div class="retrait borderr">
		<? foreach ($arr_droits as $key => $value) { ?>
			<div class="padding">
				<label><input type="checkbox" name="vars[droit_app][<?= $value ?>]" value="1" > <?= $value ?></label>
			</div>
		<? } ?>
	</div>
</div>
