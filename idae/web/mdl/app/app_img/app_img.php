<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/01/2016
	 * Time: 01:15
	 */

	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP         = new App($table);
	ini_set('display_errors', 55);

?>
<div class="flex_v blanc">
	<div class="titre_entete">
		<i class="fa fa-key"></i> <?=idioma('Gestion des images'). ' '.$APP->app_field_name_nom?>
	</div>
	<div class="flex_h flex_main">
		<div class="frmCol1">
			<div class="applink applinkblock flex_h borderb toggler">
				<a class="flex_main autoToggle aligncenter active borderr"  onclick="$('app_home_field').toggleContent()"><i class="fa fa-home"></i><br>re</a>
				<a class="flex_main autoToggle aligncenter borderr" onclick="$('app_type_field').toggleContent()"><?=idioma('Types')?></a>
				<a class="flex_main autoToggle aligncenter" onclick="$('app_group_field').toggleContent()"><?=idioma('Groupes')?></a>
			</div>
		</div>
		<div act_defer mdl="app/app_liste/app_liste"    vars="table=<?=$table?>&datadsp=app/app_img/image_dyn" class="blanc flex_main " style="overflow:auto" id="inner_skel">
		</div>
	</div>
</div>
