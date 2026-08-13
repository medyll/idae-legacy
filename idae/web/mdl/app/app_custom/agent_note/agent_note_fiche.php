<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 02/10/2015
	 * Time: 22:18
	 */
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	//
	// POST
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$table_value = (int)$_POST['table_value'];
	$id          = 'id' . $table;
	//
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);

	// INITIAL
	$APP = new App($table);
	$ARR = $APP->query_one([$id => $table_value]);
	if (!empty($ARR['codeAgent_note'])) {
		// CONVERSION
		$APP_TMP       = new App($ARR['codeAgent_note']);
		$ARR_TMP       = $APP_TMP->query_one(['id' . $ARR['codeAgent_note'] => (int)$ARR['valeurAgent_note']]);
		$TABLE_ONE_TMP = $APP_TMP->app_table_one;
	}
	//
?>
<div class=" fond_noir" style="max-width:350px;height:100%;" data-table="<?=$table?>" data-table_value="<?=$table_value?>">
	<div class="fond_noir flex_v flex " style="height:100%;">
		<div class="flex_h edededHover mastershow color_fond_noir borderb flex_align_middle">
			<div class="padding" style="padding:15px;"><i class="fa fa-sticky-note-o"></i></div>
			<div><?= date_fr($ARR['dateCreationAgent_note']); ?></div>
			<div class="ellipsis bold"><?= ($ARR['idagent']!=$ARR['idagent_writer'])? ' - '.nomAgent($ARR['idagent_writer']) : ''  ; ?></div>
			<div class=" alignright    flex_main ">
				<div class="slaveshow">
					<a onclick="ajaxValidation('app_delete', 'mdl/app/', 'table=<?= $table ?>&table_value=<?= $table_value ?>')"><i class="fa fa-times fa-2x textrouge"></i></a>
				</div>
			</div>
		</div>
		<?php if (!empty($ARR['codeAgent_note'])) { ?>
			<div>
				<?= skelMdl::cf_module('app/app/app_fiche_thumb', ['table' => $ARR['codeAgent_note'], 'table_value' => $ARR['valeurAgent_note']]) ?>
			</div>
		<?php } ?>
		<form action="<?= ACTIONMDL ?>app/actions.php" id="form_testarea_note<?= $table_value ?>">
			<input type="hidden" name="F_action" value="app_update">
			<input type="hidden" name="table" value="<?= $table ?>">
			<input type="hidden" name="table_value" value="<?= $table_value ?>">
			<textarea name="vars[descriptionAgent_note]" class="none" id="content_testarea_note<?= $table_value ?>"><?= $ARR['descriptionAgent_note'] ?></textarea>
			<div data-field_name="descriptionAgent_note" data-field_name_raw="description" style="padding:1em;" class="flex_main  color_fond_noir margin   cursor " id="content_edit_note<?= $table_value ?>">
				<?= $ARR['descriptionAgent_note'] ?>
			</div>
		</form>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .on, .readAttribute, .add/removeClassName, .update, String#escapeHTML)
	 * to native DOM, with file-local `agn_` helpers.
	 *
	 * Both .on() calls are two-argument with no selector, so they were never
	 * delegation — the shim fell through to Event.observe. Ported as plain
	 * listeners, which also keeps `this` pointing at the element.
	 */
	function agn_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's String#escapeHTML: & < > only, exactly as 1.7.3 did it. */
	function agn_escapeHTML(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	}

	agn_el('content_edit_note<?=$table_value?>').addEventListener('click', function () {
		agn_el('content_edit_note<?=$table_value?>').classList.remove('cursor');
		if (!this.getAttribute('contenteditable')) agn_el('content_edit_note<?=$table_value?>').setAttribute('contenteditable', 'true')
	})
	agn_el('content_edit_note<?=$table_value?>').addEventListener('blur', function () {
		agn_el('content_edit_note<?=$table_value?>').classList.add('cursor');
		var desc = agn_el('content_edit_note<?=$table_value?>').innerHTML;
		desc = agn_escapeHTML(desc);
		agn_el('content_testarea_note<?=$table_value?>').innerHTML = desc
		ajaxFormValidation(agn_el('form_testarea_note<?= $table_value ?>'))
		agn_el('content_edit_note<?=$table_value?>').removeAttribute('contenteditable');
	})
</script>
<style>
	/* min-height: 150px;min-width:150px;max-width:250px;overflow:hidden;margin:1em;resize:both;*/
</style>