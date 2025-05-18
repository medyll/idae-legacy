<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App('appscheme');
	$APPHASF         = new App('appscheme_has_field');
	$APPSC_FIELD     = new App('appscheme_field');
	$APPSC_FIELD_GROUP     = new App('appscheme_field_group');
	$APPSC_HAS_FIELD = new App('appscheme_has_field');

	ini_set('display_errors', 55);


	$arr_scope = $APP->plug('sitebase_app', 'appscheme')->distinct('codeAppscheme_base');


?>

<div class="blanc flex_v" style="overflow:hidden;">
	<div class="titre_entete">

	</div>
	<div class="applink titre_entete_menu">
		<a onClick="ajaxMdl('app/app_skel/skelbuilder_field_create','Nouveau champ');"><i class="fa fa-plus-circle"></i> Nouveau champ</a>
	</div>
	<div class="blanc flex_main" style="overflow:hidden;">
		<div class="flex_h" style="height:100%;overflow:hidden;">
			<div class="frmCol1 applink applinkblock toggler" style="overflow:auto;">
				<div main_auto_tree>
				<?
					// mainscope_app => groupBy
					foreach ($arr_scope as $field => $scope) {
						$rs = $APP->plug('sitebase_app', 'appscheme')->find(['codeAppscheme_base' => $scope])->sort(['nomAppscheme' => 1]);

						?>

						<div auto_tree class="margin">
							<div class="trait"><?= $scope ?> </div>
						</div>
						<div class="autoBlock"  >
						<?
						while ($arr = $rs->getNext()) {
							$idappscheme = (int)$arr['idappscheme'];
							$arrSF    = $APPHASF->findOne(['idappscheme' => $idappscheme, 'codeAppscheme_field' =>'nom']);
							if(empty($arrSF['codeAppscheme_field'])){
							//	$ARRF = $APPSC_FIELD->findOne(['codeAppscheme_field' =>'nom']);
							//	$APPHASF->create_update(['idappscheme' => $idappscheme, 'idappscheme_field' =>(int)$ARRF['idappscheme_field']],['codeAppscheme_field' =>'nom']);
							}

							?>
							<a class="autoToggle" onclick="$('inner_col_f').loadModule('app/app_scheme/app_scheme_has_field_update','idappscheme=<?= $arr['idappscheme'] ?>');">
								<?= $arr['nomAppscheme'] ?>
							</a>
						<? } ?></div>
					<? } ?>
				</div>
			</div>
			<div class="flex_main" id="inner_col_f" style="height:100%;overflow:auto;"></div>
		</div>

	</div>
</div>
