<?php

	include_once($_SERVER['CONF_INC']);
	$APP      = new App('client');
	$idclient = (int)$_POST['idclient'];
	$arrCli   = $APP->findOne(['idclient' => $idclient]);
	$table    = 'client';
	//
	// LOG_CODE
	// $APP->set_log($_SESSION['idagent'], 'client', $idclient, 'DETAIL');

?>
<div class="blanc flex_v" style="overflow:hidden;height:100%;width:100%;">
	<div class="titre_entete fond_noir color_fond_noir">
		<div class="">
			Client&nbsp;&nbsp;<?= $arrCli['nomClient'] ?>
		</div>
	</div>
	<div class="applink titre_entete_menu blanc none">
		<a onclick="<?= fonctionsJs::client_big_fiche($idclient); ?>"><i class="fa fa-refresh"></i></a>
		<a><i class="fa fa-pencil textjaune"></i>&nbsp;Editer</a>
		<a onclick="<?= fonctionsJs::app_create('tache', ['add_table' => 'client', 'add_table_value' => $idclient]); ?>" class="ucfirst">
			<i class="fa fa-paperclip"></i>&nbsp;<?= idioma('creer une tache') ?>
		</a>
		<a onclick="<?= fonctionsJs::app_create('opportunite', ['add_table' => 'client', 'add_table_value' => $idclient]); ?>"><i
				class="fa fa-calendar"></i>&nbsp;<?= idioma('Opportunités') ?>
		</a>
		<a><i class="fa fa-random textorange"></i>&nbsp;Attribution</a>
		<a><i class="fa fa-remove textrouge"></i>&nbsp;Supprimer</a>
	</div>
	<div><?= skelMdl::cf_module('app/app/app_menu', ['act_from' => 'fiche', 'table' => $table, 'table_value' => $table_value]) ?></div>
	<div class="flex_h flex_main" style="width:100%;height:100%;overflow:hidden;">
		<div style="width:50%;overflow:hidden;height:100%;" class="borderr"><?= skelMdl::cf_module('app/app_custom/client/client_espace_left', $_POST) ?></div>
		<div style="width:50%;overflow:hidden;height:100%;"><?= skelMdl::cf_module('app/app_custom/client/client_espace_right', $_POST) ?></div>
	</div>
</div>