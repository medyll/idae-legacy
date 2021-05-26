<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	//
	$table = empty($_POST['table']) ? 'produit_type' : $_POST['table'];
	$Table = ucfirst($table);
	$APP   = new App($table);
	//
	$input_name = empty($_POST['input_name']) ? 'vars' : $_POST['input_name'];
	$table_main = empty($_POST['table_main']) ? $table : $_POST['table_main'];
	$vars       = empty($_POST['vars']) ? [] : $_POST['vars'];
	$vars       = fonctionsProduction::cleanPostMongo($vars, true);
	$GRILLE_FK  = $APP->get_grille_fk();
	// $arr_idfks = array_column($GRILLE_FK, 'idtable_fk');
	$arr_idfks = array_map(function ($element) { return $element['idtable_fk']; }, $GRILLE_FK);
	foreach ($vars as $key_vars => $value_vars):
		foreach ($GRILLE_FK as $key_fk => $arr_fk):
			if (is_array($value_vars)) $vars[$key_vars] = ['$in' => array_values($value_vars)];
			if (!in_array($key_vars, $arr_idfks)) unset($vars[$key_vars]);
		endforeach;
	endforeach;

	//
	$vars = array_filter($vars);
	//
	$VARSTABLE = array_keys(Act::decodeVars(['id' . $table => '456']));
	//
	$out = [];
	foreach ($vars as $key => $value) {
		if (is_array($value)) {
			$tmp       = array_values($value);
			$out[$key] = ['$in' => $tmp];
		}
	}

	/*$tmp_conn = $APP->plug('sitebase_base', $table_main);
	$arrCol   = $tmp_conn->distinct('id' . $table_main, $out);*/
	// $arrCollect = $APP_MAIN->distinct( $table_main, $out,200,'nofull' );
	// if(!$arrCol) $arrCol = array(); 
	// echo 'main => id' . $table_main;
	//
	//	vardump($arrCol);

	//$rs = $APP->query($vars, '', 50, array('id' . $table => 1, 'nom' . $Table => 1))->sort(array('estTop' . $table => -1, 'nom' . $Table => 1))->limit(20);
	$rs = $APP->find($vars)->sort(['nom' . $Table => 1]);
	//
	$target = uniqid('r');
?>
<div id="<?= $target ?>">
	<div>
		<div class="flex_h flex_align_middle border4 blanc">
			<div class="aligncenter padding borderr cursor" data-menu="data-menu"><i class="fa fa-<?= $APP->iconAppscheme ?> textgris"></i></div>
			<div class="contextmenu" style="position:absolute;display:none;" act_defer mdl="app/app_search/app_search_item_change" vars="from=check&target=<?= $target ?>&<?= http_build_query($_POST); ?>"></div>
			<div class="flex_main">
				<input class="noborder ededed" type="text" placeholder="<?= $table ?>" onkeyup="quickFind(this.value,'in_<?= $target ?>','.flex_main');$('in_<?= $target ?>').show()">
			</div>
		</div>
		<div class="boxshadow ededed border4">
			<div id="in_return_<?= $target ?>" class="flex_h flex_wrap padding borderu fond_noir color_fond_noir"></div>
			<div id="in_<?= $target ?>" class="margin padding applink">
				<div class="flex_v">
					<div class="alignright borderb">
						<a onclick="this.up().up().up().hide()"><i class="fa fa-times textrouge"></i></a>
					</div>
					<div style="overflow-y:auto;overflow-x:hidden;max-height:250px;">
						<?
							while ($arr = $rs->getNext()):

								?>
								<div class="flex_main saerch_in">
									<label class="ellipsis cursor borderb padding">
										<input type="checkbox" data-name="<?= $arr['nom' . $Table] ?>" value="<?= $arr['id' . $table] ?>" name="<?= $input_name ?>[<?= 'id' . $table ?>][]">
										<?= $arr['nom' . $Table] ?>
									</label>
								</div>
								<?
							endwhile;
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('in_<?= $target ?>').observe('click', function () {
		arr = $$('#<?= $target ?> [type=checkbox]').collect(function (s) {
			if (!s.checked) return false;
			red = s.readAttribute('data-name');
			$('in_return_<?= $target ?>').appendChild(s.up());
			return red;
		});
	})
	$('in_return_<?= $target ?>').observe('click', function () {
		arr = $$('#<?= $target ?> [type=checkbox]').collect(function (s) {
			if (!s.checked) return false;
			red = s.readAttribute('data-name');
			$('in_<?= $target ?>').appendChild(s.up());
			return red;
		});
	})
</script>