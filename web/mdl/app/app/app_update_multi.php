<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 31/05/14
	 * Time: 16:18
	 */
	include_once($_SERVER['CONF_INC']);
	$time     = time();
	$arr_id   = $_POST['id'];
	$F_action = $_POST['F_action'];
	//
	//
	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;

	$GRILLE_FK = $APP->get_grille_fk();
	//
	$id  = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//

	//
	$arrFieldsBool = $APP->get_field_list(['codeAppscheme_field_type' => 'bool']);

?>
<div>
	<div class="titre_entete">
		<i class="fa fa-question-circle"></i> <?= idioma('Modification de plusieurs éléments ') ?>
	</div>
	<div class="barre_entete bold   uppercase borderb "> <span>
						<?= sizeof($arr_id) ?>
			<?= $table ?> seront modifiés</span>
	</div>
	<form action="<?= ACTIONMDL ?>app/actions.php"
	      onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close">
		<input type="hidden"
		       name="F_action"
		       value="app_multi_update"/>
		<input type="hidden"
		       name="table"
		       value="<?= $table ?>"/>
		<? foreach ($arr_id as $value) { ?>
			<input name="arr_id[]"
			       type="hidden"
			       value="<?= $value ?>">
		<? } ?>
		<div class="titre_entete_menu ">
			<div class="retrait flex_h flex_align_middle">
			<? foreach ($arrFieldsBool as $keyBool => $valueBool) {
				?>
				<div class="borderr flex_h flex_align_middle">
					<div class="padding aligncenter">
						<i class="fa fa-fw fa-<?= $valueBool['iconAppscheme_field'] ?>"></i><br>
						<input type="checkbox" name="verify[<?=$keyBool ?>]">
					</div>
					<div class="padding_more">
						 <div class="ellipsis bold"><?= ucfirst($valueBool['nomAppscheme_field']) ?></div>
						<div class="bordert padding">
							<?=chkSch($valueBool['field_name'],0)?>
						</div>
					</div>
				</div>
			<? } ?></div>
		</div>
		<div class="table" style="width:100%;max-width:800px;">
			<div class="cell">
				<div class="margin padding flex_h flex_align_top">
					<div class=" margin">
						<div class="flex_h flex_wrap flex_margin flex_wrap">
							<? foreach ($GRILLE_FK as $field):
								$id  = 'id' . $field['table_fk'];
								$nom = 'nom' . ucfirst($field['table_fk']);
								$arr = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
								?>
								<div class="margin padding borderb">
									<div class="padding_more flex_h flex_align_middle bold"><?= ucfirst($field['nomAppscheme']) ?></div>
									<div class="padding retrait flex flex_align_middle  flex_margin">
										<input type="checkbox"
										       name="verify[<?= $field['table_fk'] ?>]"
										       value="1">
										<input datalist_input_name="vars[<?= $id ?>]"
										       datalist_input_value=""
										       datalist="app/app_select"
										       populate
										       name="vars[<?= $nom ?>]"
										       paramName="search"
										       vars="table=<?= $field['table_fk'] ?>"
										       value=""
										       class="inputMedium"/>
									</div>
								</div>
							<? endforeach; ?>
						</div>
					</div>
					<div class=" margin">
						<?
							$arr_has = ['statut', 'type', 'categorie','groupe','group'];
							foreach ($arr_has as $key => $value):
								$Value  = ucfirst($value);
								$_table = $table . '_' . $value;
								$_Table = ucfirst($_table);
								$_id    = 'id' . $_table;
								$_nom   = 'nom' . $_Table;
								if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
									<div class="margin padding borderb">
										<div class="padding_more flex_h flex_align_middle bold"><?= ucfirst(idioma($Value)) ?></div>
										<div class="padding retrait flex flex_align_middle flex_margin">
											<input type="checkbox"
											       name="verify[<?= $_table ?>]"
											       value="1">
											<input datalist_input_name="vars[<?= $_id ?>]"
											       datalist_input_value=""
											       datalist="app/app_select"
											       populate
											       name="vars[<?= $_nom ?>]"
											       paramName="search"
											       vars="table=<?= $_table ?>" />
										</div>
									</div>
								<? endif; ?>
							<? endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="padding">
			<progress value="0" id="auto_progress_multi_update_<?= $table ?>" style="display:none;"></progress>
		</div>
		<div class="buttonZone">
			<input type="submit"
			       value="Valider">
			<input type="button"
			       value="Annuler"
			       class="cancelClose">
		</div>
	</form>
</div>