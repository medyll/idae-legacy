<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);

	$vars       = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$code       = (empty($_POST['code'])) ? 'app_search' : $_POST['code'];
	$page       = (empty($_POST['page'])) ? 0 : $_POST['page'];
	$nbRows     = (empty($_POST['nbRows'])) ? 5 : $_POST['nbRows'];
	$APP        = new App();
	$APP_BASE   = new App('appscheme_base');
	$APP_SCH    = new App('appscheme');
	$APP_SCH_TY = new App('appscheme_type');

	$ARR_TYPE = $APP_SCH->distinct_all('idappscheme_type');
	$ARR_TYPE = array_values(array_filter($ARR_TYPE));

	$RSTYPE     = $APP_SCH_TY->find(['idappscheme_type' => ['$in' => $ARR_TYPE]])->sort(['nomAppscheme_type' => 1]);
	$RSNOSCHEME = $APP_SCH->find(['idappscheme_type' => ['$nin' => $ARR_TYPE]])->sort(['nomAppscheme' => 1]);

	$arr_code = ['app_search' => 'Recherche rapide', 'app_menu_create' => 'Création rapide', 'app_panel' => 'Panneau latéral droit', 'app_menu' => 'Panneau latéral gauche', 'app_menu_start' => 'Menu démarrage, accés aux espaces'];
?>
<div style="height: 100%;">
	<div class="titre_entete">
		<?= idioma('Préférences') ?> : <?= idioma($titre_zone) ?>
	</div>
	<br>
	<form onsubmit="ajaxValidation('init_settings', 'mdl/app/', $(this).serialize());return false;">
		<input type="hidden" name="vars[idagent]" value="<?=$_SESSION['idagent']?>" >
		<div class="flex_main applink applinkblock blanc" style="overflow-y:auto;overflow-x:hidden;">
			<? foreach ($arr_code as $key => $value) { ?>
				<div class="padding">
					<label class="block"><span class="padding"><input name="vars[arr_settings][<?= $key ?>]" type="checkbox"></span> <?= $value ?></label>
				</div>
			<? } ?>
		</div>
		<div>
			<div class="buttonZone">
				<input type="submit" value="Réinitialiser">
			</div>
		</div>
	</form>
</div>