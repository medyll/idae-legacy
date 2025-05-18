<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 13/11/2016
	 * Time: 18:35
	 */

	include_once($_SERVER['CONF_INC']);

	$APP  = new App();
	$POST = empty($MDLPOST) ? $_POST : $MDLPOST;
	$time = time();
	$arr  = $APP->plug('sitebase_base', 'agent')->findOne(['idagent' => (int)$_SESSION['idagent']]);

	if (empty($_SESSION['idagent'])):
		die('Non identifié');
	endif;

?>
<div class="tile  " act_chrome_gui="app/app_calendrier/app_calendrier_echeance">
	<div class="tile_icon_main">
		<div class="tile_background"><i class="border4 fa fa-file fa-2x"></i></div>
		<div class="tile_icon"><i class="border4 fa fa-calendar fa-2x"></i></div>
	</div>
	<div class="tile_bottom">
		<div class="tile_text  tile_text_desk">
			<?= idioma('Calendrier des taches') ?>
		</div>
	</div>
</div>
<br>
<div  data-count_trigger="hide" class="tile" act_chrome_gui="app/app_liste/app_liste_gui" vars="table=tache&vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[ne][codeTache_statut]=END&vars[lte][dateDebutTache]=<?= date('Y-m-d') ?>">
	<div class="tile_icon_main">
		<div class="tile_background"><i class="border4 fa fa-file fa-2x"></i></div>
		<div class="tile_icon"><i class="border4 fa fa-calendar-o fa-2x"></i></div>
	</div>
	<div class="tile_bottom">
		<div class="  tile_text  tile_text_desk">
			<?= idioma('Taches à traiter') ?>
		</div>
	</div>
	<div class="tile_count" data-count="" data-count_auto="true" data-table="tache" data-vars="table=tache&vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[ne][codeTache_statut]=END&vars[lte][dateDebutTache]=<?= date('Y-m-d') ?>"></div>
</div>
