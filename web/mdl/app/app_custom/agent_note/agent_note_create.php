<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 03/10/2015
	 * Time: 01:45
	 */

	include_once($_SERVER['CONF_INC']);
	$time = time();
	if(!empty($_POST['add_table'])){

	}
?>
<div style="min-width:220px" class=" fond_noir color_fond_noir">
	<div class="enteteFor">
		<div class="titre_entete toggler">
			<div class="alignright">
				<a onclick="addUtilisateurAgent_note();" class="autoToggle">+
					<?= idioma('Partager avec') ?>
				</a>
			</div>
		</div>
	</div>
	<form action="mdl/app/actions.php" name="form_createNote" id="form_createNote" onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close"/>
	<input type="hidden" name="F_action" id="F_action" value="app_create"/>
	<input type="hidden" name="table" value="agent_note"/>
	<input type="hidden" name="vars[estActifAgent_note]" value="1"/>
	<input type="hidden" name="vars[idagent][]" value="<?= $_SESSION['idagent'] ?>">
	<input type="hidden" name="vars[idagent_writer]" value="<?= $_SESSION['idagent'] ?>">
	<input type="hidden" name="vars[codeAgent_note]" value="<?= $_POST['add_table'] ?>">
	<input type="hidden" name="vars[valeurAgent_note]" value="<?= $_POST['add_table_value'] ?>">
	<div>
		<div class="table" style="width:100%">
			<div class="cell padding">
				<div class="relative padding">
					<textarea wrap="off" name="vars[descriptionAgent_note]" class="textareaAgent_note fond_noir color_fond_noir" required="required"></textarea>
				</div>
			</div>
			<div class="cell borderl blanc" style="display:none;width:180px;" id="div_choix_user<?= $time ?>"></div>
		</div>
		<div class="buttonZone applink">
			<a class="cancelClose">
				<i class="fa fa-times"></i> <?= idioma('Annuler') ?>
			</a>
			&nbsp;
			<input type="submit" value="<?= idioma('Ok') ?>" class="validButton"/>
		</div>
	</div>
	</form>
</div>
<style>
	.textareaAgent_note {
		min-height: 250px;
		min-width: 220px;
		height: auto;
		border: solid 0px #ccc !important;
	}
</style>
<script>
	addUtilisateurAgent_note = function () {
		$('div_choix_user<?=$time?>').show();
		$('div_choix_user<?=$time?>').loadModule('app/app_search/search_item_check', 'table=agent')
	}
</script>