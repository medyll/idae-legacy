<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App('newsletter');
	$APP->init_scheme('sitebase_newsletter', 'newsletter_block');
	ini_set('display_errors', 55);
	$uniqid       = uniqid();
	$table = 'newsletter';
	$Table = ucfirst($table);
	$table_value = $idnewsletter = (int)$_POST['idnewsletter'];
	$arr          = $APP->findOne(array('idnewsletter' => (int)$table_value));
	$dyn_pr =  uniqid('dyn_pr');
?>
<div style="height: 100%;overflow:auto;" class="fond_noir">
	<div class="inline"  expl_file_zone >
		<div style="margin-left:2em;" class="blanc"><?= skelMdl::cf_module('app/app/app_fiche_mini', array('table' => 'newsletter', 'table_value' => $idnewsletter, 'scope' => 'idnewsletter'), $idnewsletter); ?></div>

		<div style="margin-left:2em;" class="inline flex_main fond_noir" id="<?=$dyn_pr?>" data-dsp="mdl" data-dsp-mdl="app/app/app_fiche_mini"></div>
	</div>
</div>
<script>
	load_table_in_zone('table=newsletter_block&vars[idnewsletter]=<?=$idnewsletter?>', '<?=$dyn_pr?>');
</script>
<style>
	#django {
		height: 5px;
		margin-right: 5px;
		margin-left: 5px;
		position: relative;
		background-color: #333;
	}

	.info_entete {
		margin: 0.5em;
		padding: 0.5em;
	}
</style>
