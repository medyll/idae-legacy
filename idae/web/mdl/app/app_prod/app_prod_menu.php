<?
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);
	$APP = new App('appscheme');

	//
	$uniqid = uniqid();
	$vars = empty($_POST['vars']) ? [] : $_POST['vars'];
	$mainscope_app = empty($_POST['vars']['mainscope_app']) ? 'prod' : $_POST['vars']['mainscope_app'];

	if(empty($_POST['table'])){
		$vars['table']=$_POST['table'];
	}
	$arr_sc = $APP->findOne($vars);

	$louid = uniqid('louid');
?>
<div class="app_onglet toggler">
	<a class="padding aligncenter applink" act_target="zone_<?= $uniqid ?>" mdl="app/app_liste/app_liste_home" vars="<?= http_build_query($_POST); ?>"><i class="fa fa-home"></i> </a>
	<a class="autoToggle avoid fond_noir color_fond_noir"><i class="fa fa-<?= $arr_sc['iconAppscheme'] ?>"></i> <?= $mainscope_app ?></a>
	<a class="active autoToggle" 	expl_act_target="<?=$louid?>" mdl="app/app_prod/app_prod_liste_menu"><?=idioma('Accueil')?></a>
	<a class="autoToggle" 			expl_act_target="<?=$louid?>" mdl="app/app_prod/app_prod_liste_menu_search" value="<?= $_SESSION['idagent'] ?>"><?=idioma('Recherche')?></a>
	<a expl_act_target="<?=$louid?>" mdl="app/app_prod/app_prod_liste_menu_views" class="autoToggle"><?=idioma('Affichage')?></a>
	<a expl_act_target="<?=$louid?>" mdl="app/app_prod/app_prod_liste_menu_export" class="autoToggle"><?=idioma('Exporter')?></a>
	<a expl_act_target="<?=$louid?>" mdl="app/app_prod/app_prod_liste_menu_export" class="autoToggle"><?=idioma('Administrer')?></a>
</div>
<div class="borderb tablemiddle flex_h" style="width:100%;background-color: #F5F6F7;">
	<div expl_file_reload mdl="app/app_prod/app_prod_liste_menu"  vars="<?=http_build_query($_POST)?>" expl_act_target_receiver="<?=$louid?>">
		<div style="height:100%;position:relative;">
			<?=skelMdl::cf_module("app/app_prod/app_prod_liste_menu",$_POST)?>
		</div>
	</div>
</div>