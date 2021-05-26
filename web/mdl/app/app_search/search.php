<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 10/12/14
	 * Time: 23:54
	 */
	include_once($_SERVER['CONF_INC']);
	$formSearch = 'ct_se';
	$list = 'ct_ls';

?>
<form name = "<?= $formSearch ?>" id = "<?= $formSearch ?>" method = "post" action = "actions.php" >
	<input type = "hidden" name = "F_action" value = "act_search_maw" />

	<div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item_date' , array( 'item' => 'date' ) , '' , 'item="date"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'destination' ) , '' , 'item="destination"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'fournisseur' ) , '' , 'item="fournisseur"') ?>
		</div >
		<div class = "cellsearch padding relative" >
			<?= skelMdl::cf_module('app/app_search/search_item' , array( 'item' => 'ville' ) , '' , 'item="ville"') ?>
		</div >
	</div >
</form >
<script >
	$('<?=$formSearch?>').on('change', 'select', function (event, node) {

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
</script >