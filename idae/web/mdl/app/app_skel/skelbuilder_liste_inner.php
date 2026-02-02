<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	$APP = new App('appscheme');
	ini_set('display_errors', 0);
	$arr_f = $APP->app_default_fields;
	/*
	 * foreach ($arr_f as $field => $title):
		echo $field;
		$out = [];
		$out['field_raw'] = $field;
		$out['field_title'] = $title;
		$out['field_group'] = '';
		$out['field_type']  = '';
		$APP->plug('sitebase_app', 'appscheme_field')->insert($out);
	endforeach; */
	$rs = $APP->find()->sort(['codeAppscheme_base' => 1, 'codeAppscheme' => 1]);

	// test fiedld group
?>

<div class="flex_v blanc">
	<div class="titre_entete">
		<div class="applink">
			<span><?= $rs->count(); ?> tables</span>
			<a onClick="ajaxMdl('app/app_skel/skelbuilder_create','nouvelle table');">Nouvelle table</a>
		</div>
	</div>
	<div class="blanc flex_main "
	     style="overflow:auto">
		<table width="max-100%"
		       cellspacing="0"
		       class="explorer" act_sort>
			<thead>
			<tr class="entete">
				<td style="width:80px"></td>
				<td style="width:120px">base</td>
				<td style="width:250px">collection</td>
				<td style="width:250px">Champs</td>
				<td>mainscope_app</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			</thead>
			<tbody id="tbody_web_stat" class="toggler">
			<?
				/*$rs_field = $APP->plug('sitebase_app', 'appscheme_field')->find()->sort(['field_group' => 1, 'field_raw' => 1]);
				while ($arr_field = $rs_field->getNext()) {

					if (empty($arr_field['idappscheme_field'])):
						$arr_field['idappscheme_field'] = (int)$APP->getNext('idappscheme_field');
						$APP->plug('sitebase_app', 'appscheme_field')->update(['_id' => MongoCompat::toObjectId($arr_field['_id'])], ['$set' => ['idappscheme_field' => $arr_field['idappscheme_field']]]);
					endif;
				}*/
				$rs_field = $APP->plug('sitebase_app', 'appscheme_field')->find()->sort(['field_group' => 1, 'field_raw' => 1]);
				while ($arr = $rs->getNext()) {
					//$APP->update(['idappscheme'=>(int)$arr['idappscheme']],['iconAppscheme'=>$arr['icon']])
					while ($arr_field = $rs_field->getNext()) {
						$raw = $arr_field['codeAppscheme_field'];
						if (!empty($arr['has' . ucfirst($raw) . 'Scheme'])) {
							$out_t = ['idappscheme' => (int)$arr['idappscheme'], 'idappscheme_field' => (int)$arr_field['idappscheme_field']];
							$arr_coll_field = $APP->plug('sitebase_app', 'appscheme_has_field')->findOne($out_t);
							if (empty($arr_coll_field['idappscheme_has_field'])) {
								$out_t['idappscheme_has_field'] = (int)$APP->getNext('idappscheme_has_field');
								$out_t['codeAppscheme_field'] = $raw;
								$out_t['codeAppscheme'] = $arr['codeAppscheme'];
								$out_t['codeAppscheme_has_field'] = $raw.ucfirst($arr['codeAppscheme']);
								$out_t['nomAppscheme_has_field'] = $raw.' '.$arr['codeAppscheme'];
								$APP->plug('sitebase_app', 'appscheme_has_field')->insert($out_t);
							}
						}
					}
					$rs_field->reset();
					?>
					<tr class="autoToggle">
						<td class="aligncenter">
							<i class="fa fa-<?= $arr['icon'] ?>"></i>
						</td>
						<td><?= $arr['nomAppscheme_base'] ?></td>
						<td>
							<a onclick="<?=fonctionsJs::app_update('appscheme',$arr['idappscheme'])?>"><?= $arr['nomAppscheme'] ?></a>
						</td>
						<td>
							<a onclick="ajaxMdl('app/app_scheme/app_scheme_has_field_update_scheme','Choix des champs de table','_id=<?= $arr['_id'] ?>')"><?= $arr['mainscope_app'] ?></a>
						</td>
						<td>
							<a onclick="ajaxMdl('app/app_scheme/app_scheme_has_field_update_model','Choix des champs de table','_id=<?= $arr['_id'] ?>')">Champs</a>
						</td>
						<td>
							<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
								grilleFK <?= sizeof($arr['grilleFK']) ?>
							</a></td>
						<td>
							<a onclick="ajaxMdl('app/app_skel/skelbuilder_grille','Fiche grille app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
								grille <?= sizeof($arr['grille']) ?>
							</a>
							<a onclick="ajaxMdl('app/app_skel/skelbuilder_input','Fiche app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
								input
							</a></td>
						<td>
							<a onclick="ajaxMdl('app/app_skel/skelbuilder_add','Ajout champ app_builder','_id=<?= $arr['_id'] ?>')">
								add
							</a></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
</div>
