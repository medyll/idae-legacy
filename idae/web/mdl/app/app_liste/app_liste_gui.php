<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/06/14
	 * Time: 00:59
	 */

	include_once($_SERVER['CONF_INC']); 

	$table = $_POST['table'];
	$table_value = $_POST['table_value'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$id = 'id' . $table;
	$nom = 'nom' . ucfirst($table);

	//
	$zone = 'app_liste_gui_'.implode('_',array_keys($vars)).'_'.implode('_',array_values($vars));
?>
<div style="max-width:1050px;"  >
	<div style="height:550px;overflow: auto;position:relative;"  >
		<div style="height:100%;">
			<?= skelMdl::cf_module('app/app_liste/app_liste', $_POST); ?>
		</div>
	</div>
</div>
<div class="titreFor">
	<?= idioma('Liste')  ?>   <?= $APP->get_titre_vars($vars); ?>
</div>
<? if (droit('DEV')) { ?>
	<div class="footerFor">
		<div class="padding bordert ededed">     <?= $_POST['module'] ?>  <? printr($_POST) ?></div>
	</div>
<? } ?>