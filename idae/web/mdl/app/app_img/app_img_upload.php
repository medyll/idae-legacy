<?php
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
<?php
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
				<?php if (!empty($_POST['needResize'])) { ?>
					<div class="disinput">
						<a onclick="needResize()">
							<i class="fa fa-crop"></i>
							&nbsp;Retailler cette image
						</a>
					</div>                <?php } ?></div>
			<div class="borderl"  >
				<div class="disinput">
					<a onclick="iu_fire(iu_el('image_upload_<?= $time ?>'), 'dom:submit');">
						<i class="fa fa-check"></i> <?= idioma('Valider et terminer') ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="aligncenter flex_main flex_h flex_align_middle" id="<?= $hoverZone ?>" style="position:relative;background-color:#333;overflow:hidden;">
		<div style="width:100%">
			<div id="<?= $listing_img ?>" class="inline fond_noir" style="position:relative;text-align:center;min-width:<?= $width ?>px;min-height:<?= $height ?>px">
				<?php if(!empty($test)){ ?>
					<img id="<?= $imgid ?>" src="<?= Act::imgSrc($codeImage); ?>?time=<?= $time ?>"/>
				<?php } ?>
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
	<?php foreach ($vars as $key => $input): ?>
		<input type = "hidden" name = "vars[<?= $key ?>]" value = "<?= $input ?>" >
	<?php endforeach; ?>
	<!--<input type="hidden"   name="tag" value="enews">-->
	<input type="hidden" name="sizeImg" value="<?= $width ?>"> <?php if (!empty($height)) { ?>
		<input type="hidden" name="sizeHeightImg" value="<?= $height ?>">    <?php } ?>
	<?php if (!empty($_POST['needResize'])) { ?>
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
		<input type="hidden" name="act_crop" value="needResize">    <?php } ?>
	<?php if (!empty($codeImage)) { ?>
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
	<?php } ?>

</form>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, .select, .size, .first, .getDimensions, .writeAttribute, .fire,
	 * .update, .getWidth/.getHeight, .readAttribute, .each, .add/removeClassName,
	 * .setStyle, .identify) to native DOM, with file-local `iu_` helpers.
	 *
	 * The helpers are redefined on every render rather than guarded: this
	 * module can be opened several times in one session (each instance keys its
	 * ids off $time), and the functions below are pure, so the last definition
	 * winning is harmless. That is already how recalcSizeImg / needResize /
	 * savResizeeCoords behaved — they are globals overwritten per instance, and
	 * cropper.js calls them back by name.
	 */
	function iu_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's `element.select(css)`, as a real Array. */
	function iu_select(ref, selector) {
		var node = iu_el(ref);
		if (!node) return [];
		return Array.prototype.slice.call(node.querySelectorAll(selector));
	}

	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	function iu_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	/**
	 * Prototype's Element#getDimensions: offset box when the node is displayed,
	 * otherwise measured behind a temporary visibility:hidden / display:block so
	 * a hidden node still reports a real size instead of 0x0.
	 */
	function iu_getDimensions(node) {
		if (!node) return {width: 0, height: 0};
		if (window.getComputedStyle(node).display !== 'none') {
			return {width: node.offsetWidth, height: node.offsetHeight};
		}
		var style = node.style;
		var originalVisibility = style.visibility,
			originalPosition = style.position,
			originalDisplay = style.display;
		style.visibility = 'hidden';
		if (originalPosition !== 'fixed') style.position = 'absolute';
		style.display = 'block';
		var width = node.clientWidth, height = node.clientHeight;
		style.display = originalDisplay;
		style.position = originalPosition;
		style.visibility = originalVisibility;
		return {width: width, height: height};
	}

	/** Prototype's Element#identify: give the node an id if it has none, return it. */
	function iu_identify(node) {
		if (!node.id) node.id = 'anonymous_element_' + Math.random().toString(36).slice(2);
		return node.id;
	}

	new myddeAttach(iu_el('myddeUpload<?=$time?>'), {preview_zone: '<?=$listing_img?>', form: 'image_upload_<?=$time?>'});
</script>
<script>
	recalcSizeImg = function (args) {

		setTimeout(function () {
			if (args == null) return false;
			var imgs = iu_select('<?=$listing_img?>', 'img');
			if (imgs.length == 0) {
				return false;
			}
			var img = imgs[0];
			var dim = iu_getDimensions(img);
			/*img.width  = args.width
			 img.height = args.height */
			var ratiowidth = args.width / dim.width;
			var ratioheight = args.height / dim.height;
			img.setAttribute('ratiowidth', ratiowidth);
			img.setAttribute('ratioheight', ratioheight);
		}, 1000)
	}
	endUpload_img = function () {
		if (iu_select('<?=$listing_img?>', 'img').length == 0) {
			alert('Echec upload');
			return false;
		}
	}
	<?php if(!empty($_POST['needResize']) ){ ?>
	savResizeeCoords = function (coords, dimensions) {
		var zone = iu_el('<?=$listing_img?>');
		if (!zone) return;
		// Was `if (!$(zone).show()) return;`. Prototype's show() returns the
		// element, so the guard could never fire — the line's only real effect
		// was displaying the zone. Kept as that, without the dead branch.
		zone.style.display = '';
		var imgs = iu_select(zone, 'img');
		if (imgs.length == 0) {
			return false;
		}
		var img = imgs[0];
		var ratiowidth = parseFloat(img.getAttribute('ratiowidth')) || 1;
		var ratioheight = parseFloat(img.getAttribute('ratioheight')) || 1;
		if (iu_el('x1') == null) return
		iu_el('x1').value = coords.x1 * ratiowidth;
		iu_el('y1').value = coords.y1 * ratioheight;
		iu_el('x2').value = coords.x2 * ratiowidth;
		iu_el('y2').value = coords.y2 * ratioheight;
		iu_el('width').value = dimensions.width * ratiowidth;
		iu_el('height').value = dimensions.height * ratioheight;
		iu_el('display_width').value = iu_getDimensions(zone).width;
		iu_el('display_height').value = iu_getDimensions(zone).height;
		//
		iu_el('spy_x1').innerHTML = coords.x1 * ratiowidth;
		iu_el('spy_y1').innerHTML = coords.y1 * ratioheight;
		iu_el('spy_x2').innerHTML = coords.x2 * ratiowidth;
		iu_el('spy_y2').innerHTML = coords.y2 * ratioheight;
	}

	<?php } ?>
</script>
<script>
	needResize = function () {
		iu_select('<?=$listing_img?>', 'img.just_uploaded').forEach(function (node) {
			node.classList.remove('just_uploaded');
			var dim = iu_getDimensions(node);
			iu_el('original_width').value = dim.width;
			iu_el('original_height').value = dim.height;
			node.classList.add('just_uploaded');
			node.style.visibility = 'visible';
		})

		var imgs = iu_select('<?=$listing_img?>', 'img');
		if (imgs.length == 0) {
			return false;
		}
		var daCrop = new Cropper.Img(
			iu_identify(imgs[0]),
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
