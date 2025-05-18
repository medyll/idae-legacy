<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$table  = empty($_POST['table']) ? 'client' : $_POST['table'];
	$vars   = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$nbRows = empty($_POST['nbRows']) ? 15 : $_POST['nbRows'];
	$sortBy = empty($_POST['sortBy']) ? 'quantite' : $_POST['sortBy'];

	$name_id = 'id' . $table;
	$Table   = ucfirst($table);

	$APP = new App('agent_history');

	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-30 day');
	$DADATE = $dateStart->format('Y-m-d');

	$RS = $APP->find(['codeAgent_history' => $table] + $vars)->sort([$sortBy . 'Agent_history' => -1, 'dateAgent_history' => -1, 'heureAgent_history' => -1])->limit($nbRows);

	$APP_TMP = new App($table);

?>
<div auto_tree_main auto_tree_accordeon="true" right="right" class=" " style="margin-bottom:0.5em;">
	<div auto_tree  style="padding:0.5em;" class="borderb">
		<div onclick="save_setting_autoNext(this,'<?= $table ?>_panel')"><i class="textorange fa fa-<?= $APP_TMP->iconAppscheme ?>"></i> <?= strtoupper($table) ?></div>
	</div>
	<div class="retrait applink applinkblock" style="display:<?= $APP->get_settings($_SESSION['idagent'], $table . '_panel') ?>;">
		<?
			while ($arr = $RS->getNext()):
				// if (empty($arr['nomAgent_history'])) continue;
				$arr_table = $APP_TMP->findOne([$name_id => (int)$arr['valeurAgent_history']]);
				if (empty($arr_table[$name_id])) {
					$APP->remove(['valeurAgent_history' => (int)$arr['valeurAgent_history'], 'codeAgent_history' => $table]);
					continue;
				}
				$tr_vars   = ['table_value' => (int)$arr['valeurAgent_history'], 'table' => $table, 'field_name_raw' => 'nom', 'field_value' => strtolower($arr_table['nom' . $Table])];
				?>
				<div data-contextual="table=<?= $table ?>&table_value=<?= $arr['valeurAgent_history'] ?>" class="flex_h">
					<div class="flex_main ellipsis hide_gui_pane">
						<a act_chrome_gui="app/app/app_fiche" vars="table=<?= $table ?>&table_value=<?= $arr['valeurAgent_history'] ?>">
							<?= $APP_TMP->draw_field($tr_vars) ?></a>
					</div>
				</div>
				<?
			endwhile;
		?>
	</div>
</div>