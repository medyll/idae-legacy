<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();

	$APP_FIELD         = new App('appscheme_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');
	$APPSC_FIELD_TYPE  = new App('appscheme_field_type');
	$arr_f             = $APP->app_default_fields;

	$rs  = $APP_FIELD->find()->sort(array('ordreAppscheme_field' => 1, 'nomAppscheme_field' => 1));
	$rsG = $APPSC_FIELD_GROUP->find()->sort(['group_ordre' => 1]);
	$rsT = $APPSC_FIELD_TYPE->find()->sort(['type_ordre' => 1]);
?>
<div class="flex_v blanc"  style="overflow:hidden;">
	<div class="flex_h" style="height:100%;overflow: hidden">
		<div class="frmCol1">
			<div class="applink applinkblock flex_h borderb toggler">
				<a class="flex_main autoToggle aligncenter active borderr"  onclick="$('app_home_field').toggleContent()"><i class="fa fa-home"></i><br>re</a>
				<a class="flex_main autoToggle aligncenter borderr" onclick="$('app_type_field').toggleContent()"><?=idioma('Types')?></a>
				<a class="flex_main autoToggle aligncenter" onclick="$('app_group_field').toggleContent()"><?=idioma('Groupes')?></a>
			</div>
			<div>
				<div class="applink applinkblock applinkbig toggler" id="app_home_field" >
					<a class="autoToggle active" onclick="$('appscheme_field_zone').loadModule('app/app_liste/app_liste','table=appscheme_field&sortBy=nomAppscheme_field')"><?=idioma('Voir tout')?></a>
					<a class="autoToggle" onclick="$('appscheme_field_zone').loadModule('app/app_liste/app_liste','table=appscheme_field&groupBy=appscheme_field_type')"><?=idioma('Par type')?></a>
					<a class="autoToggle" onclick="$('appscheme_field_zone').loadModule('app/app_liste/app_liste','table=appscheme_field&groupBy=appscheme_field_group')"><?=idioma('Par groupe')?></a>
				</div>
				<div class="applink applinkblock" id="app_group_field" style="display:none;">
					<div class="titre_entete"><?= idioma('Groupes') ?></div>
					<div class="applinkbig">
					<? while ($arrg = $rsG->getNext()) {
						?>
							<a class="autoToggle" onclick="$('appscheme_field_zone').loadModule('app/app_liste/app_liste','table=appscheme_field&vars[idappscheme_field_group]=<?=$arrg['idappscheme_field_group']?>')"><?= ucfirst($arrg['nomAppscheme_field_group']) ?></a>

					<? }
						$rsG->reset();?></div>
				</div>
				<div class="applink applinkblock" id="app_type_field" style="display:none;">
					<div class="titre_entete"><?= idioma('Type') ?></div>
					<? while ($arrg = $rsT->getNext()) {
						?>
						<div style="position:relative;">
							<a class="autoToggle"><?= ucfirst($arrg['nomAppscheme_field_type']) ?></a>
						</div>
					<? } ?>
				</div>
			</div>
		</div>
		<div act_defer mdl="app/app_liste/app_liste" vars="table=appscheme_field"  class="blanc flex_main " style="overflow:hidden" id="appscheme_field_zone">

	</div>
</div>
