<?
	include_once($_SERVER['CONF_INC']);

	$APP               = new App('appscheme');
	$APPHASF           = new App('appscheme_has_field');
	$APPSC_FIELD       = new App('appscheme_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');
	$APPSC_HAS_FIELD   = new App('appscheme_has_field');

	ini_set('display_errors', 55);

	$arr_scope = $APP->plug('sitebase_app', 'appscheme')->distinct('codeAppscheme_base');

?>
<div class="blanc flex_v" style="overflow:hidden;">
	<div class="titre_entete applink">
		<a class="appbutton" onclick="<?= fonctionsJs::app_create('appscheme',[],'full') ?>"><?= idioma('Créer une table de données') ?></a>
	</div>
	<div class="blanc flex_main" style="overflow:hidden;">
		<div class="flex_h" style="height:100%;overflow:hidden;">
			<div class="frmCol1 flex_v" style="overflow:hidden;">
				<div class="applink applinkblock flex_h flex_align_middle borderb toggler">
					<a class="flex_main autoToggle aligncenter active borderr" onclick="$('app_home_field').toggleContent()"><i class="fa fa-home"></i>

						<br>
						home
					</a>
					<a class="flex_main autoToggle aligncenter borderr" onclick="$('app_type_field').toggleContent()"><?= idioma('Types') ?></a>
					<a class="flex_main autoToggle aligncenter" onclick="$('app_group_field').toggleContent()"><?= idioma('Base') ?></a>
				</div>
				<div class="padding borderb aligcenter inline alignright ededed">
					<input type="text" data-quickFind="" data-quickFind-where="zone_has_field_menu" data-quickFind-tag=".autoToggle" data-quickFind-spy="uyt">
				</div>
				<div class="flex_main" style="overflow:auto;">
					<div data-dsp="line" id="zone_has_field_menu">
						<script>load_table_in_zone ('table=appscheme&sortBy=nomAppscheme&groupBy=appscheme_base', 'zone_has_field_menu')</script>
					</div>
				</div>
			</div>
			<div class="flex_main" id="inner_col_f" style="height:100%;overflow:auto;"></div>
		</div>
	</div>
</div>
<script>
	$ ('zone_has_field_menu').on ('click', '[data-table][data-table_value]', function (event, node) {
		var table       = node.readAttribute ('data-table');
		var table_value = node.readAttribute ('data-table_value');
		$ ('inner_col_f').loadModule ('app/app_scheme/app_scheme_has_field_update', 'table=' + table + '&idappscheme=' + table_value);
	})
</script>
