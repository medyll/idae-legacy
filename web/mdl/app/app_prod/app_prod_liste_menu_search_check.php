<?
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	// if ($_SESSION['idagent'] != 1) return;
	$uniqid = uniqid();
	$formSearch = 'form' . $uniqid;
	$table = $_POST['table'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP = new App($table);
	$APP_TABLE = $APP->app_table_one;
	$GRILLE_FK = $APP->get_grille_fk();

?>
<div style="height:100%;overflow:hidden;">
	<form style="height:100%;overflow:hidden;" id="<?= $formSearch ?>" expl_form="expl_form" onsubmit="return false;">
		<input type="hidden" name="table_main" value="<?=$table?>" class="act_int">
		<div style="height:100%;width:100%" class="flex_v">
			<div class="titre_entete color_fond_noir">
				<i class="fa fa-search"></i> Recherche <?= $table ?>
			</div>
			<div style="overflow-x:hidden;height:100%;">
				<div><input type="hidden" name="table" value="<?= $table ?>"></div>			
				<? if (!empty($APP_TABLE['hasTypeScheme'])): ?>
					<div class="cellsearch">
						<?= skelMdl::cf_module('app/app_search/search_item_check', array('table_main'=>$table,'table' => $table . '_type'), '', 'item="' . $table . '_type"') ?>
					</div>
				<? endif; ?>
				<? foreach ($GRILLE_FK as $fk):
					$table_fk = $fk['table_fk'];
					$id_fk    = 'id' . $fk['table_fk'];
					$rs_dist  = $APP->distinct($table_fk, $vars);
					$arr_fk   = $APP->get_fk_id_tables($table_fk);

					$arr_inter = array_intersect_key($vars, $arr_fk);
					?>
					<div class="cellsearch">
						<?= skelMdl::cf_module('app/app_search/search_item_check', array('table_main'=>$table,'item' => $table_fk), '', 'item="' . $table_fk . '"') ?>
					</div>
				<? endforeach; ?>
			</div>
			
			<div class="buttonZone">
				<div class="alignright">
					<button type="submit" value="Ok"><i class="fa fa-search-plus"></i> <?= idioma('Rechercher') ?></button>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	$('<?=$formSearch?>').on('change', 'input', function (event, node) {


		var vars = 'n=p';
		var ac_elem = $(node).up('.cellsearch');
		var next_elem = ac_elem;
		vars = '&' + Form.serialize(ac_elem);
		vars += '&' + Form.serializeElements($('<?=$formSearch?>').select('.act_int'));

		ac_elem.previousSiblings().each(function (danode) {
			if (Form.serialize(danode) != '')vars += '&' + Form.serialize(danode);

		}.bind(this))


		if (!$(next_elem).next('.cellsearch')) {
			vars = Form.serialize($('<?=$formSearch?>'));
			alert('red')

		} else {
			while ($(next_elem).next('.cellsearch')) {
				wrkon = $(next_elem).next('.cellsearch');
				mdl = $(wrkon).select('[mdl]').first().readAttribute('mdl');
				vars_item = $(wrkon).select('[mdl]').first().readAttribute('item');

				next_elem = $(next_elem).next('.cellsearch');
				$(wrkon).select('[mdl]').first().loadModule(mdl, 'item=' + vars_item + vars);
			}
		}
		$('<?=$formSearch?>').fire('dom:act_change')
	})
</script>