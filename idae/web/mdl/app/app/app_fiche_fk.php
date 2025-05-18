<?
	include_once($_SERVER['CONF_INC']);

	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$mode        = empty($_POST['mode']) ? 'icone' : $_POST['mode'];

	$APP       = new App($table);
	$GRILLE_FK = $APP->get_grille_fk();

	if (sizeof($GRILLE_FK) == 0) return;

	$name_id = 'id' . $table;
	$ARR     = $APP->findOne([$name_id => $table_value]);

	switch ($mode):
		case 'icone': ?>
			<div class="flex_h flex_wrap" style="overflow:hidden;">
				<? foreach ($GRILLE_FK as $field):
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_code = $arr['code' . ucfirst($field['table_fk'])];
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
					if (empty($dsp_name)) continue;
					?>
					<div act_defer mdl="app/app/app_fiche_icone"
					     data-cache="true"
					     vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"><?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?></div>
				<? endforeach; ?>
			</div>
			<?
			break;
		case 'fiche': ?>
			<div main_auto_tree auto_tree_accordeon class="">
				<? foreach ($GRILLE_FK as $field):
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_code = $arr['code' . ucfirst($field['table_fk'])];
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
					if (empty($dsp_name)) continue;
					?>
					<div act_defer mdl="app/app/app_fiche_entete_arbo"
					     data-cache="true"
					     vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"><?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?></div>
				<? endforeach; ?>
			</div>
			<?
			break;
		case 'fields': ?>
			<div >
				<? foreach ($GRILLE_FK as $field):
					if (empty($ARR[$field['idtable_fk']])) continue;
					$Idae = new Idae($field['table_fk']);
					echo $Idae->module('app/app/app_fiche_fields',['table'=>$field['table_fk'],'table_value'=>$ARR[$field['idtable_fk']]]);
				 endforeach; ?>
			</div>
			<?
			break;
		default : ?>
			<div class=" ">
				<? foreach ($GRILLE_FK as $field):
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
					if (empty($dsp_name)) continue;
					?>
					<div class="textgrisfonce  ">
						<div onclick="<?= fonctionsJs::app_fiche($field['table_fk'], $arr['id' . $field['table_fk']]) ?>" class="cursor  alignright ">
							<?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?>
							<i class="fa fa-<?= $field['iconAppscheme'] ?>"></i>
						</div>
					</div>
				<? endforeach; ?>
			</div>
			<?
			break;
	endswitch;