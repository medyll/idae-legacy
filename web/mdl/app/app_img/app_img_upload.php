<?
	include_once($_SERVER['CONF_INC']);

	global $buildArr;
	global $IMG_SIZE_ARR;
/*ini_set('display_errors',55);

	vardump($_POST);*/
	$table       = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$name_id     = 'id' . $table;
	$Table       = ucfirst($table);
	$APP         = new App($table);

	$ARR = $APP->findOne([$name_id => $table_value]);

	$base            = empty($_POST['base']) ? 'sitebase_image' : $_POST['base'];
	$collection      = empty($_POST['collection']) ? 'fs' : $_POST['collection'];
	$vars            = empty($_POST['vars']) ? [] : $_POST['vars'];
	$codeTailleImage = empty($_POST['codeTailleImage']) ? 'small' : $_POST['codeTailleImage'];
	$csssource       = (empty($_POST['csssource'])) ? '' : $_POST['csssource'];
	$codeImage       = empty($_POST['codeImage']) ? $table . '-' . strtolower($codeTailleImage) . '-' . $table_value : $_POST['codeImage'];

 	$width           = $IMG_SIZE_ARR[$codeTailleImage][0];
	$height          = $IMG_SIZE_ARR[$codeTailleImage][1];

	if(empty($width)):
		$width  = $buildArr[$codeTailleImage][0];
		$height = $buildArr[$codeTailleImage][1];
	endif;

	$grid = $APP->plug_base($base)->getGridFs($collection);
	$test = $grid->findOne($codeImage);

	$time        = uniqid();
	$listing_img = 'ls' . $time;
	$hoverZone   = 'hv' . $time;
	$imgid       = 'img' . $time;

	// should receive : / table / table_value / image_name
?>
<?
	if (!empty($_POST['multiple'])) {
		$codeImage .= '-' . time();
	}
?>
<div class="flex_v" style="min-width:550px;min-height:350px;position:relative;overflow:hidden;" id="myddeUpload<?= $time ?>">
	<div class="titre_entete applink">
		<div class="flex_h" style="width:100%;">
			<div class=" " style="width:150px;">
				<a class="cursor inline relative" style="overflow:hidden;width:140px">
					<i class="fa fa-download"></i> <?= idioma('Charger une image') ?>
					<input name="file" id="file" class="cursor inline" type="file" style="opacity:0;position:absolute;left:0;top:0;z-index:0;"/>
				</a>
			</div>
			<div class="flex_main">
				<? if (!empty($_POST['needResize'])) { ?>
					<div class="disinput">
						<a onclick="needResize()">
							<i class="fa fa-crop"></i>
							&nbsp;Retailler cette image
						</a>
					</div>                <? } ?></div>
			<div class="borderl"  >
				<div class="disinput">
					<a onclick="$('image_upload_<?= $time ?>').fire('dom:submit');">
						<i class="fa fa-check"></i> <?= idioma('Valider et terminer') ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="aligncenter flex_main flex_h flex_align_middle" id="<?= $hoverZone ?>" style="position:relative;background-color:#333;overflow:hidden;">
		<div style="width:100%">
			<div id="<?= $listing_img ?>" class="inline fond_noir" style="position:relative;text-align:center;min-width:<?= $width ?>px;min-height:<?= $height ?>px">
				<? if(!empty($test)){ ?>
					<img id="<?= $imgid ?>" src="<?= Act::imgSrc($codeImage); ?>?time=<?= $time ?>"/>
				<? } ?>
			</div>

		</div>
		<div class="absolute aligncenter padding margin" style="vertical-align:middle;bottom:0px;right:0px;color:#fff;text-shadow:0 0 3px #000"><span id="spy_x1">&nbsp;</span>&nbsp;
			<span id="spy_y1"></span>&nbsp; <span id="spy_x2"></span>&nbsp; <span id="spy_y2"></span>&nbsp; </div>
	</div>
</div>
<div class="titreFor"><?= $table . ' ' . $ARR['nom' . $Table] ?></div>
<form id="image_upload_<?= $time ?>" onsubmit="return false;" action="mdl/app/app_img/actions.php" method="post" auto_close="true">
	<input type="hidden" name="F_action" value="addDoc">
	<input type="hidden" name="table" value="<?= $table ?>">
	<input type="hidden" name="table_value" value="<?= $table_value ?>">
	<input type="hidden" name="base" value="<?= $base ?>">
	<input type="hidden" name="collection" value="<?= $collection ?>">
	<input type="hidden" name="rw" value="rw">
	<? foreach ($vars as $key => $input): ?>
		<input type = "hidden" name = "vars[<?= $key ?>]" value = "<?= $input ?>" >
	<? endforeach; ?>
	<!--<input type="hidden"   name="tag" value="enews">-->
	<input type="hidden" name="sizeImg" value="<?= $width ?>"> <? if (!empty($height)) { ?>
		<input type="hidden" name="sizeHeightImg" value="<?= $height ?>">    <? } ?>
	<? if (!empty($_POST['needResize'])) { ?>
		<input type="hidden" name="x1" id="x1" value="">
		<input type="hidden" name="y1" id="y1" value="">
		<input type="hidden" name="x2" id="x2" value="">
		<input type="hidden" name="y2" id="y2" value="">
		<input type="hidden" name="width" id="width" value="">
		<input type="hidden" name="height" id="height" value="">
		<input type="hidden" id="original_width" name="original_width" value="">
		<input type="hidden" id="original_height" name="original_height" value="">
		<input type="hidden" id="display_width" name="display_width" value="">
		<input type="hidden" id="display_height" name="display_height" value="">
		<input type="hidden" name="act_crop" value="needResize">    <? } ?>
	<? if (!empty($codeImage)) { ?>
		<input type="hidden" name="reloadModule[app/app_img/app_image_dyn]" value="<?= $codeImage ?>">
		<input type="hidden" name="afterAction[app/app_img/app_img_upload]" value="close">
		<input type="hidden" name="mongoImg" value="<?= $codeImage ?>">
		<input type="hidden" name="codeImage" value="<?= $codeImage ?>">
		<input type="hidden" name="mongoName" value="<?= $_POST['mongoName'] ?>">
		<input type="hidden" name="codeTailleImage" value="<?= $codeTailleImage ?>">
		<input type="hidden" name="mongoId" value="<?= $_POST['mongoId'] ?>">
		<input type="hidden" name="mongoTag"    value="<?= $table ?>">
		<input type="hidden" name="table"       value="<?= $table ?>">
		<input type="hidden" name="tag"         value="<?= $table ?>">
	<? } ?>

</form>
<script>
	new myddeAttach($('myddeUpload<?=$time?>'), {preview_zone: '<?=$listing_img?>', form: 'image_upload_<?=$time?>'});
</script>
<script>
	recalcSizeImg = function (args) {

		setTimeout(function () {
			if (args == null) return false;
			if ($('<?=$listing_img?>').select('img').size() == 0) {
				return false;
			}
			img = $('<?=$listing_img?>').select('img').first();
			dim = $('<?=$listing_img?>').select('img').first().getDimensions();
			/*img.width  = args.width
			 img.height = args.height */
			ratiowidth = args.width / dim.width
			ratioheight = args.height / dim.height
			img.writeAttribute({ratiowidth: ratiowidth, ratioheight: ratioheight})
		}.bind(this), 1000)
	}
	endUpload_img = function () {
		if ($('<?=$listing_img?>').select('img').size() == 0) {
			alert('Echec upload');
			return false;
		}
	}
	<? if(!empty($_POST['needResize']) ){ ?>
	savResizeeCoords = function (coords, dimensions) {
		if (!$('<?=$listing_img?>')) return;
		if (!$('<?=$listing_img?>').show()) return;
		if ($('<?=$listing_img?>').select('img').size() == 0) {
			return false;
		}
		img = $('<?=$listing_img?>').select('img').first();
		ratiowidth = eval(img.readAttribute('ratiowidth')) || 1;
		ratioheight = eval(img.readAttribute('ratioheight')) || 1;
		if ($('x1') == null) return
		$('x1').value = eval(coords.x1) * ratiowidth;
		$('y1').value = eval(coords.y1) * ratioheight;
		$('x2').value = eval(coords.x2) * ratiowidth;
		$('y2').value = eval(coords.y2) * ratioheight;
		$('width').value = eval(dimensions.width) * ratiowidth;
		$('height').value = eval(dimensions.height) * ratioheight;
		$('display_width').value = $('<?=$listing_img?>').getWidth();
		$('display_height').value = $('<?=$listing_img?>').getHeight();
		//
		$('spy_x1').update(eval(coords.x1) * ratiowidth);
		$('spy_y1').update(eval(coords.y1) * ratioheight);
		$('spy_x2').update(eval(coords.x2) * ratiowidth);
		$('spy_y2').update(eval(coords.y2) * ratioheight);
	}

	<? } ?>
</script>
<script>
	needResize = function () {
		$('<?=$listing_img?>').select('img.just_uploaded').each(function (node) {
			$(node).removeClassName('just_uploaded');//.setStyle({visibility:'hidden'});
			dim = $(node).getDimensions();
			$('original_width').value = dim.width;
			$('original_height').value = dim.height;
			$(node).addClassName('just_uploaded').setStyle({visibility: 'visible'});
		})

		if ($('<?=$listing_img?>').select('img').size() == 0) {
			return false;
		}
		var daCrop = new Cropper.Img(
			$('<?=$listing_img?>').select('img').first().identify(),
			{
				minWidth:<?=$width?>,
				minHeight:<?=empty($height)? 0 : $height ?>,
				displayOnInit: true,
				ratioDim: {x:<?=$width?>, y:<?=$height?>},
				onEndCrop: savResizeeCoords,
				onloadCoords: {x1: 0, y1: 0, x2:<?=$width?>, y2:<?=$height?>}
			}
		)
	}
</script>
<style>
	img.just_uploaded {
		max-width: 1800px;
		max-height: 800px;
	}
</style>
