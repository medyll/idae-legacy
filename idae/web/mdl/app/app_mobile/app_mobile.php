<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 25/09/2016
	 * Time: 00:57
	 */
	include_once($_SERVER['CONF_INC']);
	$table = 'agent';
?>
<div  class="flex_v blanc mobile_size">
	<div class="flex_h toggler applink applinkblock borderb">
		<div class="flex_main aligncenter borderr">
			<a class="flex_main autoToggle " onclick=""><i class="fa fa-home"></i>

				<br>
				Home
			</a>
		</div>
		<div class="flex_main aligncenter borderr">
			<a class="flex_main autoToggle" onclick=""><i class="fa fa-list-ul"></i>

				<br>
				Voir tout
			</a>
		</div>
		<div class="flex_main aligncenter">
			<a class="flex_main" onclick="">
				<i class="fa fa-search-plus"></i>

				<br><?= idioma('Recherche') ?>
			</a>
		</div>
	</div>
	<div class="app_onglet toggler">
		<a class="autoToggle active" act_target="contenu_explorer_search_<?= $table ?>" mdl="app/app/app_explorer_search" vars="table=<?= $table ?>">Recherche</a>
		<a class="autoToggle" act_target="contenu_explorer_search_<?= $table ?>" mdl="app/app/app_explorer_search_field" vars="table=<?= $table ?>">Plus d'options</a>
	</div>
	<div class="flex_main" id="app_mobile_main<?= $table ?>" act_defer mdl="app/app_gui/app_gui_start_menu" vars="table=<?= $table ?>">
	</div>
</div>
<style>
	.mobile_size {
		width  : 320px;
		min-height : 480px;
		overflow:hidden;
	}
</style>
