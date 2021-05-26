<?
	include_once($_SERVER['CONF_INC']);
	$time        = time();
	$idappscheme = (int)$_POST['idappscheme'];
	$APP         = new App('appscheme');
	$arr         = $APP->query_one(array('idappscheme' => (int)$idappscheme));
?>
<? if (droit('ADMIN')): ?>
	<div>
		<a onclick="act_chrome_gui('app/app_scheme/app_scheme_has_field_update_model','idappscheme=<?= $idappscheme ?>')">
			<i class="fa fa-user-secret"></i> <?= idioma('Modele') ?>
		</a>
		<a onclick="ajaxInMdl('app/app_scheme/app_scheme_has_field_update','div_redf','idappscheme=<?= $idappscheme ?>',{onglet:'Choix des champs de table'})">
			<i class="fa fa-user-secret"></i> <?= idioma('Choix des champs de table') ?>
		</a>
		<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
			<i class="fa fa-sitemap"></i> dépendances <?= sizeof($arr['grilleFK']) ?>
		</a>
		<hr>
	</div>
<? endif; ?>