<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$APP = new App($table);
	$arrFields = $APP->get_basic_fields_nude($table);

?>
<script>

	get_data('json_data', {table: '<?= $table ?>', table_value: '<?= $table_value ?>',piece: 'data'}).then(function (res) {
		table_header = eval(res);
		return res;
	}).then(function(res){
		res = eval(res)[0];
		console.log(res)
		var prod_fiche_tpl = new Template($('tolototo').innerHTML);
		out = prod_fiche_tpl.evaluate(res);
		tolototo.update(out);
	})
</script>
<div style = "width: 900px;"
     class = "relative" id="tolototo">
	<div act_defer
	     mdl = "app/app/app_menu"
	     vars = "act_from=fiche&table=<?= $table ?>&table_value=<?= $table_value ?>"></div>
	<div class = "barre_entete alignright">
		<? foreach ($arrFieldsBool as $field => $arr_ico):
			$fa  = empty($ARR[$field . ucfirst($table)]) ? 'circle-thin' : 'check-circle';
			$css = empty($ARR[$field . ucfirst($table)]) ? 'textgris' : 'textvert';
			?>
			<div class = "inline padding">
				<i class = "fa fa-<?= $fa ?> <?= $css ?> "></i> <?= ucfirst(idioma($field)) ?></div>
		<? endforeach; ?></div>
	<div class = "table tabletop"
	     style = "width: 100%;">
		<div class = "cell padding">
			<table class = "table_info">
				<? foreach ($arrFields as $field): ?>
					<tr>
						<td><?= ucfirst(idioma($field)) ?></td>
						<td class = "justify">
							<div style = "max-height:200px;overflow:auto;">
								#{<?=$field . ucfirst($table)?>} </div>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
			<? if(sizeof($R_FK)!=0): ?>
				<div class="bordert">
			<table class="table_info">
				<? foreach ($R_FK as $arr_fk):
					$value_rfk               = $arr_fk['table_value'];
					$table_rfk               = $arr_fk['table'];
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$vars_rfk['table']       = $table_rfk;
					$vars_rfk['table_value'] = $value_rfk;
					$count                   = $arr_fk['count'];
					?>
					<tr>
						<td><?= $table_rfk ?></td>
						<td><a act_chrome_gui="app/app_liste/app_liste_gui"
						       vars="<?= http_build_query($vars_rfk); ?>">
								<li class="fa fa-<?= $APP->iconAppscheme ?>"></li> <?=
									$count . ' ' . $table_rfk . '' . (($count == 0) ? '' : 's') ?></a></td>
					</tr>

				<? endforeach; ?>
			</table>     </div>
			<? endif; ?>
		</div>
		<div class = "cell padding"
		     style = "width: 200px;">
			<br>

			<div class = "margin padding borderl">
				<? if (!empty($APP_TABLE['hasTypeScheme'])):
					// $APP->query($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					?>
					<div class = "padding textgris"><?= ucfirst(idioma('type')) ?></div>
					<div class = "padding retrait bold"><?= $nomType ?></div>
					<hr>
				<? endif; ?>
				<? foreach ($GRILLE_FK as $field):
					$id = 'id' . $field;
					// query for name
					$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
					$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
					?>
					<div class = "padding textgris"><?= ucfirst($field['table_fk']) ?></div>
					<div class = "padding retrait"><a act_chrome_gui = "app/app/app_fiche"
					                                  vars = "table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"
					                                  options = "{ident:'<?= $field['table_fk'] ?>',scope:'<?= $field['table_fk'] ?>'}"><?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?></a>
					</div>
				<? endforeach; ?></div>
		</div>
	</div>
	<div class = "buttonZone">
		<input type = "button"
		       class = "cancelClose"
		       value = "<?= idioma('Fermer') ?>"></div>
</div>
<div class = "titreFor">
	<?= idioma('Fiche') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div>