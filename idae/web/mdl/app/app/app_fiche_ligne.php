<?
	include_once($_SERVER['CONF_INC']);

	$table = $this->HTTP_VARS['table'];

	$vars = empty($this->HTTP_VARS['vars']) ? [] : fonctionsProduction::cleanPostMongo($this->HTTP_VARS['vars'], 1);
	//
	$APP_HAS_FIELD       = new App('appscheme_has_field');
	$APP_HAS_TABLE_FIELD = new App('appscheme_has_table_field');
	//
	$APP = new App($table);

	$rs  = $APP->find($vars);
	$arr = iterator_to_array($rs);

	$RS_HAS_TABLE_FIELD  = $APP_HAS_FIELD->find(['idappscheme' => (int)$APP->idappscheme,'in_mini_fiche'=>1])->sort(['ordreAppscheme_has_table_field' => 1]);
	$ARR_HAS_TABLE_FIELD = iterator_to_array($RS_HAS_TABLE_FIELD);
	// has_totalAppscheme_field
	$fields = $ARR_HAS_TABLE_FIELD;
	$liste  = $arr;


?>
<div class=" ">
	<table class=" " style="width: 100%;">
		<thead class="bordert borderb">
			<tr>
				<? foreach ($fields as $key => $field) { ?>
					<td class="padding_more">
						<i class="fa fa-<?= $field['iconAppscheme_field'] ?> "></i>
						<?= $field['nomAppscheme_field'] ?>
					</td>
				<? } ?>
			</tr>
		</thead>
		<tbody>
			<? foreach ($liste as $key_liste => $row) {
				$id = 'id' . $table ?>
				<tr data-table="<?= $table ?>" data-table_value="<?= $row[$id] ?>" class="cursor">
					<? foreach ($fields as $key => $field) {
						?>
						<td class="padding_more">
							<?= $this->draw_field(['field_name_raw' => $field['codeAppscheme_has_field'],
							                       'table'          => $table,
							                       'field_value'    => $row[$field['codeAppscheme_has_field']]]); ?>
						</td>
					<? } ?>
				</tr>
			<? } ?>
		</tbody>
	</table>
</div>