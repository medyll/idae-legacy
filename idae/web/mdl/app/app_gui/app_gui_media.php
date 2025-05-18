<? 
include_once($_SERVER['CONF_INC']); 
$time = time(); 
?>

<div class="minibox">
	<div class="cell first"><img src="images/icones/prestation16.png" ></div>
	<div class="cell hide_gui_pane">
		<a onclick="ajaxInMdl('advert/advert','tmp_Advert','',{onglet:'Advert'});">
			<?=idioma('Bandeaux et publicité')?>
		</a>
		<a onclick="ajaxInMdl('enews/enews','tmp_denews','',{onglet:'Gestion des enews'})">
			<?=idioma('Espace newsletter')?>
		</a>
		<a onclick="ajaxInMdl('production/image/image_frame','tmp_img','',{onglet:'Images et médias'});">
			<?=idioma('Espace Images')?>
		</a>
		<hr>
		<a  onclick="ajaxInMdl('app/app_img/app_img','tmp_app_img_scheme','',{onglet:'Images et médias'});">
			 <?=idioma('app_img')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_app_img','vars[hasImageScheme]=1&expl_file=app/app_img/image_app_liste',
		{onglet:'Images et médias'});">
			 <?=idioma('app_img_scheme')?>
		</a>
		<hr>

		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_site_EXPLORERst','',{onglet:'EXPLORER'});">
			<?=idioma('EXPLORER')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_prod','vars[mainscope_app]=prod',{onglet:'Espace production'});">
			 <?=idioma('app_exp prod')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_exp_prod','vars[mainscope_app]=prod&expl_file=production/produitliste/produit_liste_table',
		{onglet:'Espace production : liste des produits'});">
			 <?=idioma('app_exp prod liste des produits')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_img_geo','vars[mainscope_app]=geo',{onglet:'Espace géographie'});">
			 <?=idioma('app_exp geo')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_img_devi','vars[mainscope_app]=devis',{onglet:'Espace Devis'});">
			 <?=idioma('app_exp devis')?>
		</a>
        <a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_img_newsenewsvi','vars[mainscope_app]=media',{onglet:'Espace newsletter'});">
             <?=idioma('app_exp newsletter')?>
        </a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_img_test','vars[mainscope_app]=admin',{onglet:'Espace autre'});">
			 <?=idioma('app_exp test')?>
		</a>
		<a  onclick="ajaxInMdl('app/app_prod/app_prod','tmp_site_test','vars[mainscope_app]=site',{onglet:'Espace sites annexes'});">
			 <?=idioma('app_exp site')?>
		</a>
	</div>
</div>