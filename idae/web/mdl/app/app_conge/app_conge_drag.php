<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$idconge = (int)$_POST['idconge'];
	$APP     = new App('conge');
	$APP_TY  = new App('conge_type');
	//
	$arrT = $APP->findOne(['idconge' => $idconge]);

?>
<div style="display:block;height:100%;" class="mastershow">
	<div style="top:0;position:absolute;right:100%;margin-right:5px;" class="padding transpblanc">
		<?= $APP->draw_field(['field_name_raw' => 'dateDebut', 'table' => 'conge', 'field_value' => $arrT['dateDebutConge']]) ?>
	</div>
	<div style="top:0;position:absolute;left:100%;margin-left:5px;" class="padding transpblanc">
		<?= $APP->draw_field(['field_name_raw' => 'dateFin', 'table' => 'conge', 'field_value' => $arrT['dateFinConge']]) ?>
	</div>
	<div class="flex_h flex_align_middle blanc">
		<div class="padding ededed borderr">
			<?= $APP->draw_field(['field_name_raw' => 'icon', 'table' => 'conge_statut', 'field_value' => $arrT['iconConge_statut']]) ?>
		</div>
		<div class="ellipsis flex_main">
			<?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => 'conge', 'field_value' => $arrT['nomConge']]) ?> </div>
		<div>
			<?= $APP->draw_field(['field_name_raw' => 'color', 'table' => 'conge_type', 'field_value' => $arrT['colorConge_type']]) ?>
		</div>
	</div>
	<div class="slaveshow absolute  " style="left:0%;min-width:250px;max-width:450px;bottom:100%;z-index:20000">
		<div class="blanc border4 padding_more boxshadow" data-cache="true" act_defer mdl="app/app/app_fiche_entete" vars="table=conge&table_value=<?= $idconge ?>"></div>
	</div>
</div>

