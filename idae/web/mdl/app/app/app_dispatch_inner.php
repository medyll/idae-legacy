<?
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 22/05/14
	 * Time: 00:11
	 */
	include_once($_SERVER['CONF_INC']);
	//
	$table = $_POST['table'];
	$type  = $_POST['type'];
	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	//
	$uniqid = uniqid();

	ini_set('display_errors', 55);

	$TABLE_TYPE     = $table . '_' . $type;
	$NAME_ID        = 'id' . $TABLE_TYPE;
	$TABLE_TYPE_NOM = 'nom' . ucfirst($TABLE_TYPE);
	$APP_TMP        = new App($TABLE_TYPE);
	//
?>
<div class="flex_v" style="height:100%; overflow:hidden;width:100%;">
	<div class="flex_main flex flex_v" style="overflow:hidden;">
		<div class="none">
			<form onsubmit="load_table_in_zone($(this).serialize(),'<?= $patolon_bismuth ?>');$('<?= $patolon_bismuth ?>').show();return false;">
				<input type="hidden" name="table" value="<?= $table ?>">
				<button type="submit" style="position:absolute;right: 0.5em; z-index: 10;border: none;background-color: transparent;"><i
						class="fa fa-search"></i></button>
				<input placeholder="Recherche" name="search" value="" type="text" class="border4"/>
				<br>
			</form>
		</div>
		<div class="flex_main flex_h flex_margin" style="overflow-y: hidden;overflow-x: auto;">
			<div class="flex_v borderr boxshadow" style="overflow:hidden;min-width:230px;width:230px;position:sticky;left:0;z-index:10">
				<div class="padding_more ededed borderb ">
					<input data-quickFind="" data-quickFind-tag=".stype" type="text"/>
				</div>
				<div class="padding_more ededed  ">
					<?= $TABLE_TYPE ?>(s)
				</div>
				<div class="blanc  flex_h flex_wrap  " style="overflow: auto;">
					<?
						$RS_TMP = $APP_TMP->find()->sort(['nom' . ucfirst($TABLE_TYPE) => 1, 'code' . ucfirst($TABLE_TYPE) => 1]);
						while ($ARR_TMP = $RS_TMP->getNext()) {
							$value_id = (int)$ARR_TMP[$NAME_ID];
							$name     = $ARR_TMP[$TABLE_TYPE_NOM];
							$res_det  = $APP->find([$NAME_ID => $value_id]);
							?>
							<div class="flex_main demi stype">
								<div class="padding_more edededHover margin border4 boxshadow" data-append="true" dropzone data-vars="vars[<?= $NAME_ID ?>]=<?= $value_id ?>">
									<div class="ellipsis borderb bold"><?= $name ?></div>
									<div class="aligncenter">(<?= $res_det->count(); ?>)</div>
								</div>
							</div>
						<? }
						$RS_TMP->reset(); ?>
				</div>
				<div class="flex_main">
				</div>
				<div class="padding_more bordert ededed">
					<div class="flex_main  ">
						<br>
						<div class="padding_more bold margin aligncenter" data-append="true" dropzone data-vars="vars[<?= $NAME_ID ?>]=">
							<div class="ellipsis borderb"><?= idioma('Sans ') . ' ' . $TABLE_TYPE_NOM ?> </div>
						</div>
						<br>
					</div>
				</div>
			</div>
			<div class="flex_main flex_v  padding_more ededed" style="overflow : hidden; ">
				<div class="padding_more ededed">
					<input data-quickFind="" data-quickFind-where="dispatch<?= $table ?>" data-quickFind-tag="[draggable]" data-quickFind-parent=".sparent" type="text"/>
				</div>
				<?
					$res_det = $APP->find([$NAME_ID => ['$in' => [null, 0, '']]])->sort(['nom' . $Table => 1, 'code' . $Table => 1]);;
				?>
				<div class="flex_main flex_h flex_margin padding_more ededed" style="overflow-y: hidden;overflow-x: auto;" id="dispatch<?= $table ?>">
					<div class="border4 flex_v blanc sparent" style="overflow:hidden;min-width:150px;width:150px;">
						<div class="barre_entete bold borderb">
							<?= idioma('Sans ') ?> (<?= $res_det->count(); ?>)
						</div>
						<div class="applink applinkblock flex_main " data-append="true" dropzone data-vars="vars[<?= $NAME_ID ?>]=" style="overflow-y:auto;overflow-x:hidden;">
							<?
								while ($arr_det = $res_det->getNext()) {
									?>
									<a draggable="true" data-vars="table=<?= $table ?>&table_value=<?= $arr_det['id' . $table] ?>"><?= $arr_det['nom' . $Table] ?></a>
								<? } ?>
						</div>
					</div>
					<?
						$RS_TMP = $APP_TMP->find()->sort(['nom' . $Table => 1, 'code' . $Table => 1]);
						while ($ARR_TMP = $RS_TMP->getNext()) {
							$value_id = (int)$ARR_TMP[$NAME_ID];
							$name     = $ARR_TMP[$TABLE_TYPE_NOM];
							$res_det  = $APP->find([$NAME_ID => $value_id]);
							if($res_det->count()==0)continue;
							?>
							<div class="border4 flex_v   blanc sparent" style="min-width:150px;max-width:10%;overflow:hidden;">
								<div class="barre_entete bold">
									<div class="ellipsis"><?= $name ?> (<?= $res_det->count(); ?>)</div>
								</div>
								<div class="applink applinkblock flex_main" data-append="true" dropzone data-vars="vars[<?= $NAME_ID ?>]=<?= $value_id ?>" style="overflow-y:auto;overflow-x:hidden;">
									<?
										while ($arr_det = $res_det->getNext()) {
											?>
											<a draggable="true" data-vars="table=<?= $table ?>&table_value=<?= $arr_det['id' . $table] ?>"><?= $arr_det['nom' . $Table] ?></a>
											<?
										}
									?>
								</div>
							</div>
						<? } ?>
				</div>
			</div>
		</div>
	</div>
</div>