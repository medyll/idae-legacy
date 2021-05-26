<?
	include_once($_SERVER['CONF_INC']);

?>
<div class="flex_v blanc">
	<div class="titre_entete">
		<i class="fa fa-key"></i> <?=idioma('Modele de données')?>
	</div>
	<div class="app_onglet toggler">
		<a class="autoToggle active" onclick="$('inner_skel').loadModule('app/app_liste/app_liste','table=appscheme');"><?=idioma('tables de données')?></a>
		<a class="autoToggle" onclick="$('inner_skel').loadModule('app/app_scheme/app_scheme_has_field');"><?=idioma('modèle de données')?></a>
		<a class="autoToggle" onclick="$('inner_skel').loadModule('app/app_scheme/app_scheme_field','table=appscheme_field');">champs définis</a>
	</div>
	<div act_defer mdl="app/app_liste/app_liste" vars="table=appscheme" class="blanc flex_main " style="overflow:auto" id="inner_skel">
	</div>
</div>
