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
		<? if (!empty($ARR['codeAgent_note'])) { ?>
			<div>
				<?= skelMdl::cf_module('app/app/app_fiche_thumb', ['table' => $ARR['codeAgent_note'], 'table_value' => $ARR['valeurAgent_note']]) ?>
			</div>
		<? } ?>
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
	$('content_edit_note<?=$table_value?>').on('click', function () {
		$('content_edit_note<?=$table_value?>').removeClassName('cursor');
		if (!this.readAttribute('contenteditable')) $('content_edit_note<?=$table_value?>').setAttribute('contenteditable', 'true')
	})
	$('content_edit_note<?=$table_value?>').on('blur', function () {
		$('content_edit_note<?=$table_value?>').addClassName('cursor');
		var desc = $('content_edit_note<?=$table_value?>').innerHTML;
		desc = desc.escapeHTML();
		$('content_testarea_note<?=$table_value?>').update(desc)
		ajaxFormValidation($('form_testarea_note<?= $table_value ?>'))
		$('content_edit_note<?=$table_value?>').removeAttribute('contenteditable');
	})
</script>
<style>
	/* min-height: 150px;min-width:150px;max-width:250px;overflow:hidden;margin:1em;resize:both;*/
</style>