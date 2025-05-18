<?php
	include_once($_SERVER['CONF_INC']);
// ini_set('display_errors',55);
	//
	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	//
	$APP  = new App($table);
	$Idae = new Idae($table);
	//
	$GRILLE_FK      = $APP->get_grille_fk();
	$GRILLE_RFK_BIS = $APP->get_grille_rfk($table);
	$GRILLE_R_FK    = $APP->get_reverse_grille_fk($table, $table_value);
	$HTTP_VARS      = $APP->translate_vars($vars);
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$zone_1 = uniqid($table);
	$zone_2 = uniqid($table);

	$setting_home_name = 'home_fiche_maxi';
	$setting_home_save = $setting_home_name . '_' . $table;
	$mdl1              = "app/app/app_fiche_maxi_liste";
	$mdl2              = "app/app/app_fiche_maxi_liste_rfk";
	$mdl3              = "app/app_liste/app_liste";
	$mdl_load          = !empty($APP->get_settings($_SESSION['idagent'], $setting_home_name, $table)) ? $APP->get_settings($_SESSION['idagent'], $setting_home_name, $table) : $mdl1;
	$mdl_load          = !empty($_POST['mdl_load']) ? $mdl3 : $mdl_load;

	if (!empty($_POST['mdl_load'])) {
		$css_entete = 'none';
		$http_post  = 'table=' . $_POST['mdl_load'] . "&vars[id$table]=$table_value";
	} else {
		$css_entete = '';
		$http_post  = http_build_query($_POST);
	}
	$rgba_table = implode(',', hex2rgb($APP->colorAppscheme, 0.5));
?>
<div class="flex_h relative" style="width:100%;height: 100%;overflow:hidden;">
	<div id="<?= $zone_1 ?>" class="flex_v boxshadowr" style="overflow-x:hidden;overflow-y: auto;width:50%;z-index:200;">
		<div class="flex_main flex_h boxshadowb  ">
			<div class="padding_more aligncenter borderr" style="width:50px;"><i class="fa fa-id-card"></i></div>
			<div class="flex_main">
				<div class="padding_more boxshadowr ">
					<?= $Idae->module('app/app/fiche_fields', ['table'       => $table,
					                                           'titre'       => 1,
					                                           'text_titre'  => 1,
					                                           'table_value' => $table_value]) ?></div>
			</div>
		</div>
		<? if ($GRILLE_R_FK['contact']) { ?>
			<div class="padding_more ededed  borderb">
				contacts
			</div>
			<div class="flex_h">
				<div style="width:50px;" class="borderr padding_more aligncenter"><i class="fa fa-info"></i></div>
				<div class="flex_main">
					<div class="blanc bordert" data-dsp-css="grid" data-dsp_liste=""
					     data-vars="table=contact&vars[<?= 'id' . $table ?>]=<?= $table_value ?>&nbRows=15"
					     data-dsp="mdl"
					     data-dsp-mdl="app/app/app_fiche_entete"
					     data-data_model="miniModel" expl_file_list>
					</div>
				</div>
			</div>
		<? } ?>
		<? if ($GRILLE_R_FK['site']) { ?>
			<div class="padding_more ededed bordert borderb">
				sites
			</div>
			<div class="flex_h">
				<div style="width:50px;" class="borderr padding_more aligncenter"><i class="fa fa-info"></i></div>
				<div class="flex_main">
					<div class="blanc bordert" data-dsp-css="grid" data-dsp_liste=""
					     data-vars="table=site&vars[<?= 'id' . $table ?>]=<?= $table_value ?>&nbRows=15"
					     data-dsp="mdl"
					     data-dsp-mdl="app/app/app_fiche_micro"
					     data-data_model="miniModel" expl_file_list>
					</div>
				</div>
			</div>
		<? } ?>
		<? if ($GRILLE_R_FK['transport']) { ?>
			<div class="padding_more ededed bordert borderb">
				navires
			</div>
			<div class="flex_h">
				<div style="width:50px;" class="borderr padding_more aligncenter"><i class="fa fa-ship"></i></div>
				<div class="flex_main">
					<div class="blanc bordert" data-dsp-css="grid" data-dsp_liste=""
					     data-vars="table=transport&vars[<?= 'id' . $table ?>]=<?= $table_value ?>&nbRows=15"
					     data-dsp="mdl"
					     data-dsp-mdl="app/app/app_fiche_micro"
					     data-data_model="miniModel" expl_file_list>
					</div>
				</div>
			</div>
		<? } ?>
		<? if (sizeof($GRILLE_FK) != 0) { ?>
			<br>
			<div class="flex_h  bordert">
				<div class="padding_more aligncenter borderr ededed" style="width:50px;"><i class="fa fa-link"></i>
				</div>
				<div class="relative flex_main">
					<? foreach ($GRILLE_FK as $field):
						$id       = 'id' . $field;
						// query for name
						$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];

						if (!empty($ARR[$field['idtable_fk']])): ?>
							<div class=" " style="min-width:50%;">
								<div class="  padding_more  border4 margin_more" act_defer
								     mdl="app/app/app_fiche_entete"
								     vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>">&nbsp;</div>
							</div>
						<? endif;
					endforeach; ?>
				</div>
			</div>
		<? } ?>
		<div class="flex_h   ededed bordert">
			<div class="padding_more aligncenter borderr ededed" style="width:50px;"><i class="fa fa-cubes"></i></div>
			<div
				class="flex_main"><?= skelMdl::cf_module('app/app/app_fiche_analogue', $_POST + ['moduleTag' => 'none'], $table); ?></div>
		</div>
	</div>
	<div style="overflow:hidden;width:50%;" class="flex_v">

			<div class="app_onglet toggler">
				<?
					$a=0;
					foreach ($GRILLE_RFK_BIS as $grp_fk => $arr_type) {
						$table_rfk = $arr_type['codeAppscheme'];
						$idappscheme_type = $arr_type['idappscheme_type'];

					++$a;
					$css = ($a==1)? 'active':''; ?>
					<a act_target="<?= $zone_2 ?>" mdl="app/app/app_fiche_maxi_liste" vars="idappscheme_type=<?=$idappscheme_type?>&table=<?= $table ?>&table_value=<?= $table_value ?>&vars[<?= 'id' . $table ?>]=<?= $table_value ?>" class="autoToggle padding_more <?=$css?>"><i class="fa fa-<?= $arr_type['iconAppscheme_type'] ?>"></i><?= $arr_type['nomAppscheme_type'] ?></a>
				<? } ?>
			</div>
		<div class="flex_main" id="<?= $zone_2 ?>" vars="<?= $http_post ?>" act_defer mdl="<?= $mdl_load ?>"></div>
	</div>
</div>
<div class="bordert ededed boxshadow flex_h none">
	<div
		class="flex_main"><?= skelMdl::cf_module('app/app/app_fiche_rfk', $_POST + ['moduleTag' => 'none'], $table); ?></div>
	<div><? //= skelMdl::cf_module('app/app/app_fiche_analogue', $_POST + ['moduleTag' => 'none'], $table); ?></div>
</div>