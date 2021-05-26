<?
	ob_start();
	include_once($_SERVER['CONF_INC']);
	ob_end_clean();

	$_POST       = array_merge($_POST, $_GET);
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$name_id     = 'id' . $table;
	$table_value = (integer)$_POST['table_value'];  // iddocument
	$vars        = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy     = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP       = new App($table);
	$ARR       = $APP->findOne([$name_id => $table_value]);
	$RF_K      = $APP->get_grille_fk('document');
	$RF_K_KEYS = array_keys($RF_K);

	$_POST['MODULE']='/app/app_custom/document/document_preview';

	if (empty($_GET['frameLoaded'])) {
		foreach ($RF_K as $key => $value) {
			if (!empty($ARR[$value['idtable_fk']])) {
				$value_grille = $ARR[$value['idtable_fk']];
				$table_grille = $key;
				$id_grille    = $value['idtable_fk'];
				$NEDD         = true;
			}
		}
		?>
		<div class="flex_v" style="overflow:hidden;height:100%;width:100%;min-width:750px;">
			<div class="titre_entete"><?= $ARR['nomDocument'] ?></div>
			<? if (empty($NEDD)) { ?>
				<div class="padding">
					<form action="<?=ACTIONMDL?>app/actions.php" onsubmit="ajaxFormValidation(this);return false;">
						<input type="hidden" name="F_action" value="app_update">
						<input type="hidden" name="table" value="<?=$table?>">
						<input type="hidden" name="table_value" value="<?=$table_value?>">
						<input type="hidden" name="reloadModule[<?= $_POST['MODULE'] ?>]" value="<?=$table_value?>">
						<div class="flex_h flex_align_middle">
							<div><?= skelMdl::cf_module('app/app_field_add', array('display_mode' => 'horiz', 'module_value' => 456156, 'field' => $RF_K_KEYS), 456156); ?></div>
							<div><input type="submit" value="valider"></div>
						</div>
					</form>
				</div>
			<? } else { ?>


			<? }?>
			<div class="relative flex_main padding" style="overflow:hidden;">
				<iframe src="/mdl/<?= $_POST['MODULE'] ?>.php?frameLoaded=frameLoaded&<?= http_build_query($_POST); ?>" style="display:block;height:100%;overflow:auto;width:100%;margin:0 auto;" marginheight="0" marginwidth="0"
				        frameborder="0">
				</iframe>
			</div>
		</div>
		<?

	} else {
		$collection = 'ged_bin';
		$base       = 'sitebase_ged';

		$base = $APP->plug_base('sitebase_ged');
		$GED  = $base->ged;
		$grid = $base->getGridFs('ged_bin');

		$ct = $grid->findOne(['metadata.iddocument' => $table_value]);

		$file_extension = strtolower(substr(strrchr($ct->file['filename'], '.'), 1));

		switch ($file_extension) {
			case 'pdf':
				$ctype = 'application/pdf';
				break;
			case 'exe':
				$ctype = 'application/octet-stream';
				break;
			case 'zip':
				$ctype = 'application/zip';
				break;
			case 'doc':
				$ctype = 'application/msword';
				break;
			case 'docx':
				$ctype = 'application/msword';
				break;
			case 'xls':
				$ctype = 'application/vnd.ms-excel';
				break;
			case 'csv':
				$ctype = 'application/vnd.ms-excel';
				break;
			case 'xlsx':
				$ctype = 'application/vnd.ms-excel';
				break;
			case 'ppt':
				$ctype = 'application/vnd.ms-powerpoint';
				break;
			case 'pptx':
				$ctype = 'application/vnd.ms-powerpoint';
				break;
			case 'gif':
				$ctype = 'image/gif';
				?>
				<img style="width:100%;" src="data:image/gif;base64,<?= base64_encode($ct->getBytes()) ?>">
				<?
				exit;
				break;
			case 'png':
				$ctype = 'image/png';
				?>
				<img style="width:100%;" src="data:image/png;base64,<?= base64_encode($ct->getBytes()) ?>">
				<?
				exit;
				break;
			case 'jpeg':
				$ctype = 'image/jpeg';
				?>
				<img style="width:100%;" src="data:image/jpeg;base64,<?= base64_encode($ct->getBytes()) ?>">
				<?
				exit;
				break;
			case 'jpg':
				$ctype = 'image/jpg';
				?>
				<img style="width:100%;" src="data:image/jpg;base64,<?= base64_encode($ct->getBytes()) ?>">
				<?
				exit;
				break;
			case 'mp3':
				$ctype = 'audio/mpeg';
				break;
			case 'wav':
				$ctype = 'audio/x-wav';
				break;
			case 'mpeg':
			case 'mpg':
			case 'mpe':
				$ctype = 'video/mpeg';
				break;
			case 'mov':
				$ctype = 'video/quicktime';
				break;
			case 'avi':
				$ctype = 'video/x-msvideo';
				break;
			case 'htm':
				$ctype = 'text/html';
				break;
			case 'html':
				$ctype = 'text/html';
				break;
			case 'css':
				$ctype = 'text/html';
				break;
			case 'php':
			case 'txt':
				$ctype = 'text/plain';
				break;
			case 'rtf':
				$ctype = 'application/rtf';
				break;
			case 'xml':
				$ctype = 'text/xml';
				break;
			default:
				$ctype = 'image/jpeg';
				?>
				<img src="data:image/jpeg;base64,<?= base64_encode($ct->getBytes()) ?>">
				<?
				exit;
				break;
		}
		/* echo $ctype;
		 echo $ct->file['length'];*/
		$size = $ct->file['length'];
		header("Content-Type: " . $ctype . "; name=\"" . $ct->file['filename'] . "\"");
		echo $ct->getBytes();
		//
	}
?>