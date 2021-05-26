<?
include_once ($_SERVER['CONF_INC']);
ini_set('display_errors', 55);
$APP = new App();
$_POST['_id'] = new MongoId($_POST['_id']);
$arr = $APP -> plug('sitebase_app', 'appscheme') -> findOne(array('_id' => $_POST['_id']));
$table = $arr['collection'];
$Table = ucfirst($table);

$types = $APP->app_default_fields;
?>
     


<div style = "width:750px;" >
	<div class = "padding borderb" >
		<div style = "overflow:hidden;" id = " " >
			<form action = "mdl/app/app_skel/actions.php" onSubmit = "ajaxFormValidation(this);return false" >
				<input type = "hidden" value = "updateForm" name = "F_action" >
				<input type = "hidden" value = "close" name = "afterAction[app/app_scheme/app_scheme_has_field_update]" >
				<input type = "hidden" value = "*" name = "reloadModule[app/app_skel/skelbuilder_liste]" >
				<input type = "hidden" name = "_id" value = "<?= $_POST['_id'] ?>" />
				<div class="padding">
					<input type="text" name="modelName" >
				</div>
				<table style = "width:100%" act_table class = "table_form table-bordered" >
					<thead>
						<tr class="entete" >
						<td >Champ</td >
						<td  >Présent</td > 
					</tr >
						</thead>
					<tr >
						<td ><label ><?= $arr['collection'] ?>_type</label ></td >
						<td ><?=chkSch('hasTypeScheme', $arr['hasTypeScheme']); ?></td >
					</tr >
					<tr >
						<td ><label >BOOL </label ></td >
						<td >
							<?=chkSch('hasBoolScheme', $arr['hasBoolScheme']); ?>
					 	</td > 
					</tr >

					<tr >
						<td ><label ><?= $arr['collection'] ?>_statut</label ></td >
						<td  >
							<?=chkSch('hasStatutScheme', $arr['hasStatutScheme']); ?></td >
					</tr >
					<tr >
						<td ><label ><?= $arr['collection'] ?>_ligne</label ></td >
						<td  >
							<?=chkSch('hasLigneScheme', $arr['hasLigneScheme']); ?></td >
					</tr >
					<? foreach ($types as $key => $value) {
						$Key = ucfirst($key);
						$Name = empty($value)? $key : $value ;
						?>
						<tr >
						<td ><?=$Name?></td >
						<td ><?= chkSch('has' . $Key . 'Scheme', $arr['has' . $Key . 'Scheme']); ?></td >
					</tr >
						<?

						}
					?>
					   
				</table >
				<div class = "buttonZone" >
					<input type = "button" value = "Annuler" class = "cancelClose" />
					<input type = "submit" value = "Valider" />
				</div >
			</form >
		</div >
	</div >
</div >
<style >
	.label {
		width: 80px;
		vertical-align: middle;
		line-height: 20px;
		text-overflow: ellipsis;
	}
	</style >

