<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	$APP = new App();
ini_set('display_errors', 55);
$_POST['_id'] = MongoCompat::toObjectId($_POST['_id']);
$arr = $APP->plug('sitebase_app','appscheme')->findOne(array('_id' => $_POST['_id']));

?>

<div>
	<div class="titre_entete fond_noir color_fond_noir">
		 <?= $arr['base'] . '.' . $arr['collection'] ?>
	</div>
	<div class="padding borderb">
		<div class="applink padding">
			<a onclick="ajaxMdl('app/app_skel/skelbuilder_code','Maj skelbuilder','_id=<?= $arr['_id'] ?>')">Form
				create</a>
			<a onclick="ajaxValidation('addInput_','mdl/app/app_skel/','_id=<?= $arr['_id'] ?>')">Ajouter champ</a>
		</div>

		<div style="overflow:hidden;max-height:500px;width:750px;" id=" ">
			<table class="tableinput tablemiddle" style="width:100%;" cellspacing="0">
				<thead>
				<tr class="entete">
					<td></td>
					<td style="width:110px;"></td>
					<td style="width:90px;"></td>
					<td style="width:30px;"></td>
					<td style="width:30px;"></td>
				</tr>
				</thead>
				<tbody id="oio">
				<?
				foreach ($arr['grilleFK'] as $key => $arrInput):
					// skelMongo::connect('skel_builder_type', 'sitebase_skelbuilder')->update(array('typeInput' => $arrInput['typeInput']), array('typeInput' => $arrInput['typeInput']),
					                                                                       //  array('upsert' => true));
					// skelMongo::connect('skel_builder_size', 'sitebase_skelbuilder')->update(array('sizeInput' => $arrInput['sizeInput']), array('sizeInput' => $arrInput['sizeInput']),
					                                                                       //  array('upsert' => true));
					?>
					<tr>
						<td><input type="hidden" name="idinput" value="<?= $arrInput['idinput'] ?>"/><input type="text"
																											class="inputFree"
																											name="nomInput"
																											value="<?= $arrInput['table'] ?>"/>
						</td>
						<td><input class="toPick" type="hidden" name="sizeInput"/><input type="text"
																						 class="inputFree select"
																						 select="app/app_skel/skelbuilder_size_select"
																						 value="<?= $arrInput['sizeInput'] ?>"/>
						</td>
						<td><input class="toPick" type="hidden" name="typeInput"/><input type="text"
																						 class="inputFree select"
																						 select="app/app_skel/skelbuilder_select"
																						 value="<?= $arrInput['typeInput'] ?>"
																						 populate='true'
																						 readonly="readonly"/></td>
						<td class="aligncenter"><input style="margin:4px auto;" type="checkbox" class="inputFree"
													   value="1" <?= checked($arrInput['requiredInput']) ?> /></td>
						<td class="aligncenter"><a
								onclick="ajaxMdl('app/app_skel/skelbuilder_input_update','Mise à jour champ','_id=<?= $_POST['_id'] ?>&nomInput=<?= $arrInput['nomInput'] ?>');"><img
									src="<?= ICONPATH ?>delete16.png"/></a></td>
					</tr>
				<?
				endforeach;
				?></tbody>
			</table>
			<div class="buttonZone">
				<input type="button" value="Annuler" class="cancelClose"/>
			</div>
		</div>
	</div>
	<script>
		register = function (event) {
			elem = Event.element(event)
			vars = Form.serialize(elem.up('tr'));
			setTimeout(function () {
				ajaxValidation('updInput', 'mdl/app/app_skel/', '_id=<?=$arr['_id']?>&' + vars)
			}.bind(this), 1250)

		}
		$('oio').on('change', function (event) {
			register(event)
		})
		$('oio').on('dom:datechoosen', function (event) {
			register(event)
		})

	</script>