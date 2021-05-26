<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 28/09/15
	 * Time: 22:33
	 */

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	$rs_g = $APP->plug('sitebase_app', 'appscheme_field_group')->find()->sort(array('group_ordre' => 1));
	$rs_t = $APP->plug('sitebase_app', 'appscheme_field_type')->find()->sort(array('type_ordre' => 1));
?>

<div>
	<div class="titre_entete">
		<div class="titre">Nouveau champ</div>
	</div>
	<div class="titre_entete_menu alignright">
		* saisie obligatoire
	</div>
	<div class="padding borderb">
		<div style="overflow:hidden;max-height:500px;" id=" ">
			<form class="Form" action="mdl/app/app_skel/actions.php" onSubmit="ajaxFormValidation(this);return false">
				<input type="hidden" value="add_field" name="F_action">
				<input type="hidden" value="close" name="afterAction[app/app_skel/skelbuilder_field_create]">
				<input type="hidden" value="*" name="reloadModule[app/app_skel/skelbuilder_liste_fields]">

				<div class="ms-TextField is-required">
					<label class="ms-Label">field_title</label>
					<input placeholder="Titre du champ" required type="text" name="vars[field_title]" class="ms-TextField-field" value="">
				</div>
				<div class="ms-TextField is-required">
					<label class="ms-Label">field_raw</label>
					<input placeholder="Nom du champ"  required type="text" name="vars[field_raw]" class="ms-TextField-field" value="">
				</div>
				<div class="ms-TextField is-required">
					<label class="ms-Label">field_group</label>
					<select required name="vars[idappscheme_field_group]" class="ms-TextField-field" >
					<? while($arr_g = $rs_g->getNext()): ?>
					<option value="<?=$arr_g['idappscheme_field_group']?>"><?=$arr_g['nomAppscheme_field_group']?></option>
					<?
						endwhile;?></select>
				</div>
				<div class="ms-TextField is-required">
					<label class="ms-Label">field_type</label>
					<select required name="vars[field_type]" class="ms-TextField-field" >
					<? while($arr_t = $rs_t->getNext()): ?>
					<option value="<?=$arr_t['type_name']?>"><?=$arr_t['type_name']?></option>
					<?
						endwhile;?></select>
				</div>
				<div class="buttonZone">
					<input type="button" value="Annuler" class="cancelClose"/>
					<input type="submit" value="Valider"/>
				</div>
			</form>
		</div>
	</div>