<?
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	// if ($_SESSION['idagent'] != 1) return;
	$uniqid          = uniqid();
	$table           = $_POST['table'];
	$vars            = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP             = new App($table);
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	//
	$formSearch = 'ct_se' . $uniqid;
?>
<div class="flex_h flex_wrap flex_align_middle flex_inline ">
	<div>
		<form id="<?= $formSearch ?>" expl_form="expl_form" onsubmit="return false;">
			<input type="hidden" name="table" value="<?= $table ?>">
			<div class="flex_h flex_inline flex_align_top">
				<div class="retrait borderr padding">
					<?
						$arr_has = ['statut', 'type', 'group'];
						foreach ($arr_has as $key => $value):
							$Value  = ucfirst($value);
							$_table = $table . '_' . $value;
							$_Table = ucfirst($_table);
							$_id    = 'id' . $_table;
							$_nom   = 'nom' . $_Table;
							if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>
								<div class="flex_h flex_align_middle padding ">
									<label><i class="fa fa-caret-right textgrisfonce"></i></label>
									<div class="borderb">
										<input datalist_input_name="vars[<?= $_id ?>]"
										       datalist_input_value=""
										       datalist="app/app_select"
										       populate
										       paramName="search"
										       vars="table=<?= $_table ?>"
										       value="<?= ucfirst(idioma($Value)) ?>"
										       class="noborder borderb inline textgrisfonce"/>
									</div>
								</div>
							<? endif; ?>
						<? endforeach; ?>
				</div>
				<div class="flex_h flex_align_middle flex_wrap flex_inline" style="max-width: <? //= ceil(sizeof($GRILLE_FK) / 2) * 205 ?>px;">
					<? foreach ($GRILLE_FK as $fk):
						$table_fk  = $fk['table_fk'];
						$id_fk   = 'id' . $fk['table_fk'];
						$rs_dist = $APP->distinct($table_fk, $vars);
						$arr_fk  = $APP->get_fk_id_tables($table_fk);

						$arr_inter = array_intersect_key($vars, $arr_fk);
						?>
						<div style="max-width:50%;" class="cellsearch relative flex_h flex_align_middle" sort_zone="sort_zone">
							<div><?= skelMdl::cf_module('app/app_search/search_item', ['table_main' => $table, 'table' => $table_fk], '', 'table="' . $table_fk . '"') ?></div>
							<div style="width:40px;"><i class="fa fa-caret-left sortprevious"></i><i class="fa fa-caret-right sortnext"></i></div>
						</div>
					<? endforeach; ?>
				</div>
				<? unset($_POST['MODULE'], $_POST['uniqid']); ?>
				<div class="ededed padding">
					<div class="border4 blanc"  act_defer vars="<?= http_build_query($_POST) ?>"
					     mdl="app/app_prod/app_prod_liste_menu_date">
					</div>
				</div>
				<div class="blanc">
					<button type="submit" value="Ok" style="line-height:2;height:4em;"><i class="fa fa-search"></i> <?= idioma('Rechercher') ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	do_reload = function (node) {

		var vars      = 'n=p';
		var ac_elem   = $ (node).up ('.cellsearch');
		var next_elem = ac_elem;
		vars          = '&' + Form.serialize (ac_elem);
		vars += '&' + Form.serializeElements ($ ('<?=$formSearch?>').select ('.act_int'));

		ac_elem.previousSiblings ().each (function (danode) {
			if ( Form.serialize (danode) != '' )vars += '&' + Form.serialize (danode);

		}.bind (this))

		if ( !$ (next_elem).next ('.cellsearch') ) {
			vars = Form.serialize ($ ('<?=$formSearch?>'));

		} else {
			while ($ (next_elem).next ('.cellsearch')) {
				wrkon     = $ (next_elem).next ('.cellsearch');
				mdl       = $ (wrkon).select ('[mdl]').first ().readAttribute ('mdl');
				vars_item = $ (wrkon).select ('[mdl]').first ().readAttribute ('table');

				console.log ($ (next_elem).next ('.cellsearch'))

				if ( next_elem.select ('[table]').first () ) {
					table_from = next_elem.select ('[table]').first ().readAttribute ('table');
				}
				next_elem = $ (next_elem).next ('.cellsearch');
				$ (wrkon).select ('[mdl]').first ().loadModule (mdl, 'table_from=' + table_from + '&table_main=<?=$table?>&table=' + vars_item + '&' + vars);

			}
		}
		// $('<?=$formSearch?>').fire('dom:act_change')
	}

	if ( $ ('<?= $formSearch ?>') != null ) {
		$ ('<?= $formSearch ?>').on ('change', 'select', function (event, node) {
			do_reload (node);

		})
		$ ('<?= $formSearch ?>').observe ('dom:act_change', function (event) {
			//eventElement = Event.element;
			do_reload (event.target);

		})

	}
</script>

