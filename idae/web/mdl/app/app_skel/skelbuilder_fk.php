<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();
	if(!empty($_POST['_id'])){
		$_id = new MongoId($_POST['_id']);
		$arr = $APP->plug('sitebase_app', 'appscheme')->findOne(array('_id' => $_id));
	}

	if(!empty($_POST['idappscheme'])){
		$idappscheme = (int)$_POST['idappscheme'];
		$arr = $APP->plug('sitebase_app', 'appscheme')->findOne(array('idappscheme' => $idappscheme));
	}

	$arr_fk = empty($arr['grilleFK'])? []: $arr['grilleFK'];
?>

<div>
	<div class = "titre_entete fond_noir color_fond_noir">
		<?= $arr['base'] . '.' . $arr['collection'] . ' FK' ?>
	</div>
	<div class = "padding borderb">
		<div class = "applink padding">
			<a onclick = "ajaxValidation('addFK','mdl/app/app_skel/','_id=<?= $arr['_id'] ?>')">Ajouter FK</a>
		</div>

		<div style = "overflow:hidden;max-height:500px;width:750px;"
		     id = " ">
			<table class = "tableinput tablemiddle"
			       style = "width:100%;"
			       cellspacing = "0">
				<thead>
				<tr class = "entete">
					<td></td>
					<td style = "width:110px;"></td>
					<td style = "width:30px;"></td>
				</tr>
				</thead>
				<tbody id = "oio">
				<?
					foreach ($arr_fk as $key => $arrInput):
						?>
						<tr uid = "<?= $arrInput['uid'] ?>">
							<td>
								<input type = "text"
								       class = "inputFree"
								       name = "vars[table]"
								       value = "<?= $arrInput['table'] ?>"/>
							</td>
							<td class = "aligncenter">
								<input type="hidden" name = "vars[requiredInput]" value="<?= $arrInput['requiredInput'] ?>" >
								<input onclick = "$(this).previous().value= (this.checked)? 1 : 0 ;"
							                                 type = "checkbox"
							                                 <?= checked($arrInput['requiredInput']) ?> /></td>
							<td class = "aligncenter"><a
									onclick = "ajaxMdl('app/app_skel/skelbuilder_input_update','Mise à jour champ','_id=<?= $_POST['_id'] ?>&nomInput=<?= $arrInput['nomInput'] ?>');"><img
										src = "<?= ICONPATH ?>delete16.png"/></a></td>
						</tr>
					<?
					endforeach;
				?></tbody>
			</table>
			<div class = "buttonZone">
				<input type = "button"
				       value = "Annuler"
				       class = "cancelClose"/>
			</div>
		</div>
	</div>
	<script>
		register = function (event) {
			var elem = Event.element (event);
			var tr = elem.up ('tr');
			vars = Form.serialize (tr);
			uid = tr.readAttribute ('uid')
			setTimeout (function () {
				ajaxValidation ('updFK', 'mdl/app/app_skel/', '_id=<?=$arr['_id']?>&' + vars + '&uid=' + uid)
			}.bind (this), 100)

		}
		$ ('oio').on ('change', function (event) {
			register (event)
		})
		$ ('oio').on ('dom:datechoosen', function (event) {
			register (event)
		})

	</script>