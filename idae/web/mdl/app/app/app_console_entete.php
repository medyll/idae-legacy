<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$vars       = empty($_POST['vars']) ? [] : $_POST['vars'];
	$table      = empty($_POST['table']) ? 'client' : $_POST['table'];
	$name_id    = 'id' . $table;
	$Table      = ucfirst($table);
	$APP        = new App($table);
	$APP_TABLE  = $APP->app_table_one;
	$GRILLE_FK  = $APP->get_grille_fk();
	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
	$ARR_SCH    = $APP->get_schemes();
	$dateStart  = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');
	$DADATE = $dateStart->format('Y-m-d');

	$COL = [];

	// $zer = array_count_values($final_array);
	// data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini""
	if ( isset($TEST_AGENT)):
		// $vars['idagent'] = $_SESSION['idagent'];
	endif;
	$zone    = uniqid($table);
	$rs      = $APP->find();
	$rs_vars = $APP->find($vars);
?>
<div style="overflow: hidden;width:100%" class="flex_h ">
	<div id="<?= $zone ?>" style="width:100%;overflow: hidden;" class="flex_h flex_wrap flex_main">
		<?
			$arr_has = ['statut', 'type'];
			foreach ($arr_has as $key => $value):
				$Value  = ucfirst($value);
				$_table = $table . '_' . $value;
				$_Table = ucfirst($_table);
				$_id    = 'id' . $_table;
				$_nom   = 'nom' . $_Table;
				$_nom   = 'code' . $_Table;
				$_icon  = 'icon' . $_Table;
				$_color = 'color' . $_Table;
				if (!empty($APP_TABLE['has' . $Value . 'Scheme'])):
					$APP_TMP = new App($_table);
					$rs_tmp  = $APP_TMP->find()->sort(['ordre' . $_Table => -1]);
					?>
					<div class="flex_h   flex_align_top flex_margin padding">
						<div class="margin padding"><?= $value ?></div>
						<div>
							<div class="flex_h flex_align_middle border4  ">
								<? while ($arr_tmp = $rs_tmp->getNext()):
									$rs_tmp_ct = $APP->find($vars + [$_id => $arr_tmp[$_id]])->sort(['ordre' . $_Table => 1]);
									if ($rs_tmp_ct->count() == 0) continue;
									?>
									<div class="padding ededed   borderr">
										<div class="textgrisfonce borderb">
											<div class="ellipsis"><i class="fa fa-<?= empty($arr_tmp[$_icon]) ? 'caret-right' : $arr_tmp[$_icon]; ?>"
											                         style="color:<?= $arr_tmp[$_color] ?>;"></i> <?= strtolower($APP->get_titre_vars([$_id => $arr_tmp[$_id]])) ?></div>
										</div>
										<div class="textgrisfonce   aligncenter  "><?= $rs_tmp_ct->count(); ?></div>
									</div>
								<? endwhile; ?>
							</div>
						</div>
					</div>
				<? endif; ?>
			<? endforeach; ?>
	</div>
	<div class="flex_h flex_align_middle padding margin alignright ">
		<div class="flex_main   borderr padding">
			<a onclick="<?= fonctionsJs::app_create($table, $vars) ?>" class="ellipsis">
				<i class="fa fa-copy textbleu"></i> <?= idioma('Créer') . ' ' . $Table ?>
			</a>
		</div>
		<div class="margin border4 padding"><?= $rs->count() ?> /
			<?= $rs_vars->count() ?><? if (!empty($TEST_AGENT)): ?>
				<i class="fa fa-user-secret"></i>
			<? endif; ?></div>
	</div>
</div>