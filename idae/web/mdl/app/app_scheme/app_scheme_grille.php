<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	$APP = new App();

	if (!empty($_POST['_id'])) {
		$_id         = MongoCompat::toObjectId($_POST['_id']);
		$arr         = $APP->plug('sitebase_app', 'appscheme')->findOne(['_id' => $_id]);
		$idappscheme = (int)$arr['idappscheme'];
	}

	if (!empty($_POST['idappscheme'])) {
		$idappscheme = (int)$_POST['idappscheme'];
		$arr         = $APP->plug('sitebase_app', 'appscheme')->findOne(['idappscheme' => $idappscheme]);
	}
	$table  = $arr['codeAppscheme'];

	$arr_fk = empty($arr['grilleFK']) ? [] : $arr['grilleFK'];
?>
<div id="" app_gui_explorer>
	<div class="padding ">
		<div class="applink padding">
			<a onclick="ajaxValidation('addFK','mdl/app/app_scheme/','_id=<?= $arr['_id'] ?>')"><i class="fa fa-adjust textvert"></i> Ajouter FK pour <?= ' grille ' . $arr['codeAppscheme_base'] . '  ' . $arr['codeAppscheme'] ?></a>
		</div>
		<div style="overflow:hidden;max-height:500px;width:750px;">
			<table class="  tableinput tablemiddle"
			       style="width:100%;"
			       cellspacing="0">
				<thead>
					<tr class="">
						<td><?= idioma('table') ?></td>
						<td style="width:110px;"><?= idioma('obligatoire') ?></td>
						<td style="width:80px;"></td>
						<td class="aligncenter" style="width:30px;"><i class="fa fa-times"></i></td>
					</tr>
				</thead>
				<tbody id="oio" sort_zone_drag="true">
					<?
						foreach ($arr_fk as $key => $arrInput):
							?>
							<tr uid="<?= $arrInput['uid'] ?>" draggable="true" data-sort_element="true" sort_zone="sort_zone">
								<td>
									<input type="text"
									       class="inputFree"
									       name="vars[table]"
									       value="<?= $arrInput['table'] ?>"/>
								</td>
								<td class="aligncenter">
									<?= chkSch('requiredInput', $arrInput['requiredInput']) ?>
								</td>
								<td>
									<div class="flex_h">
										<div class="padding   sortprevious">
											<i class="fa fa-chevron-up"></i>
										</div>
										<div class="padding    sortnext">
											<i class="fa fa-chevron-down"></i>
										</div>
									</div>
								</td>
								<td class="aligncenter">
									<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille_delete','Suppression champ','idappscheme=<?= $idappscheme ?>&nomInput=<?= $arrInput['table'] ?>&uid=<?= $arrInput['uid'] ?>');">
										<i class="fa fa-times textrouge"></i>
									</a>
								</td>
							</tr>
							<?
						endforeach;
					?></tbody>
			</table>
			<br>
			<br>
		</div>
	</div>
	<script>
		register_fk = function (event) {
			var elem = Event.element (event);
			var tr   = elem.up ('tr');
			vars     = Form.serialize (tr);
			uid      = tr.readAttribute ('uid')
			setTimeout (function () {
				ajaxValidation ('updFK', 'mdl/app/app_scheme/', '_id=<?=$arr['_id']?>&' + vars + '&uid=' + uid)
			}.bind (this), 100)

		}
		$ ('oio').on ('change', function (event) {
			register_fk (event)
		})
		$ ('oio').on ('dom:datechoosen', function (event) {
			register_fk (event)
		})
		$ ('oio').on ('dom:act_sort', function (event) {

			var pair = {};
			$ ('oio').select ('[data-sort_element]').collect (function (node, index) {
				pair['ordreFK[' + index + ']'] = node.readAttribute ('uid');
			}.bind (this));
			vars     = Object.toQueryString (pair);
			url      = vars + '&table=<?=$table?>';
			setTimeout (function () {
				ajaxValidation ('reorderFK', 'mdl/app/app_scheme/', '_id=<?=$arr['_id']?>&' + url)
			}.bind (this), 100)
		})
	</script>