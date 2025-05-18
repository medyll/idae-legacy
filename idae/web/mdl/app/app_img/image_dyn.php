<?
	include_once($_SERVER['CONF_INC']);
	global $buildArr;
	global $IMG_SIZE_ARR;

	ini_set('display_errors', 55);

	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$name_id     = 'id' . $table;
	$APP         = new App($table);

	$ARR = $APP->findOne([$name_id => $table_value]);

	// a globaliser dans Act

	//
	$time            = time();
	$base            = empty($_POST['base']) ? 'sitebase_image' : $_POST['base'];
	$collection      = empty($_POST['collection']) ? 'fs' : $_POST['collection'];
	$codeTailleImage = empty($_POST['codeTailleImage']) ? 'small' : $_POST['codeTailleImage'];
	$csssource       = (empty($_POST['csssource'])) ? '' : $_POST['csssource'];
	$codeImage       = empty($_POST['codeImage']) ? $table . '-' . strtolower($codeTailleImage) . '-' . $table_value : $_POST['codeImage'];
	$width           = $IMG_SIZE_ARR[$codeTailleImage][0];
	$height          = $IMG_SIZE_ARR[$codeTailleImage][1];
	//
	// $codeImage .= '.jpg';
	$grid = $APP->plug_base($base)->getGridFs($collection);
	$test = $grid->findOne($codeImage);
	if (empty($test)) {
		$test = $grid->findOne($codeImage . '.jpg');
	}
	if (!empty($test)) {
		if (!empty($_POST['reflect'])) {
			$testReflect = $grid->findOne($_POST['mongoImg'] . '_reflect');
			$timeMain    = (int)($test->file['uploadDate']->sec);
			$timeReflect = (int)($testReflect->file['uploadDate']->sec);
			if ($timeReflect < ($timeMain + 10)) {
				$grid->remove(['filename' => $_POST['mongoImg'] . '_reflect']);
				// $reflect = fonctionsSite::reflectImage(HTTPCUSTOMERSITE . 'mediatheque-' . $_POST['mongoImg'] . '.jpg');
				//$myMeta = ['filename' => $_POST['mongoImg'] . '_reflect', 'metadata' => ['name' => $_POST['mongoImg'] . '_reflect', 'contentType' => 'image/png']];
				//$grid->storeBytes($reflect, $myMeta);
			}

		}
	}
	if (!empty($_POST['show'])):
		$srcF   = str_replace($codeTailleImage, $_POST['show'], $codeImage);
		$width  = $IMG_SIZE_ARR[$_POST['show']][0];
		$height = $IMG_SIZE_ARR[$_POST['show']][1];
		if (empty($width)):
			$width  = $buildArr[$_POST['show']][0];
			$height = $buildArr[$_POST['show']][1];
		endif;
	else:
		$srcF = $codeImage;
	endif;
	if ($table == 'agent' && $table_value != $_SESSION['idagent']) $_POST['noEdit'] = true;
?>
<div style="position:relative;max-width:100%;overflow:hidden;" class="<?= $csssource ?> blanc inline cursor">
	<div class="flex_h">
		<div style="width:<?= $width ?>px;height:<?= $height ?>px;" <? if (empty($_POST['noEdit'])){ ?>data-menu="" data-clone="data-clone"<? } ?> class="flex_h flex_align_middle aligncenter color_fond_noir fond_noir">
			<? if (!empty($test)): ?>
				<img src="<?= Act::imgSrc($srcF); ?>?time=<?= time(); ?>" style="width:<?= $width ?>px;height:<?= $height ?>px;max-width:100%;"/>
			<? else: ?>
				<div title="<?= addslashes(Act::imgSrc($srcF)); ?>" class="aligncenter" style="min-width:50px;min-height:50px;width:100%;"><i class="fa fa-unlink" title="<?= $srcF ?>"></i>
				</div>
			<? endif; ?>
		</div>
		<? if (empty($_POST['noEdit'])) { ?>
			<div class="applink bordert blanc flex_v flex_wrap absolute boxshadow" style="display:none;width:100%;min-height:100%;bottom:0;right:0;">
				<div class="applinkblock applink">
					<a class="flex_main" onclick="load_panel_img('needResize=true&<?= http_build_query($_POST) ?>');">
						<i class="fa fa-upload  fa-fw bold"></i><?= idioma('charger image') ?>
					</a>
					<a class="flex_main" onclick="ajaxMdl('app/app_img/app_img_delete','','src=<?= $codeImage ?>');Event.stop(event);return false;">
						<i class="fa fa-times textrouge  fa-fw bold"></i><?= idioma('supprimer image') ?>
					</a>
				</div>
			</div>
		<? } ?>
	</div>
	<? if (!empty($_POST['show_info'])): ?>
		<div class="titre_entete">
			<?= $srcF ?>
		</div>
	<? endif; ?>
</div>
<script>
	load_panel_img = function (vars) {
		if ( !$ ('auto_expl_preview_zone') ) {
			var frag_app_left_panel = window.APP.APPTPL['app_left_panel']
			var elem_left_panel     = create_element_of (frag_app_left_panel);
			document.body.appendChild (elem_left_panel);
			this.expl_preview_zone  = elem_left_panel;
		} else {
			this.expl_preview_zone = $ ('auto_expl_preview_zone');
		}
		this.act_target = this.expl_preview_zone.select ('[expl_preview_zone_file]').first();
		this.expl_preview_zone.show ();
		this.act_target.loadModule ('app/app_img/app_img_upload',vars);
	}
</script>