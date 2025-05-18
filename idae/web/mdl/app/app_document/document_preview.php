<?
	ob_start();
	include_once($_SERVER['CONF_INC']);
	ob_end_clean();
	ini_set('display_errors', 55);

	print_r($_POST);

	if (!empty($_GET['frameLoaded'])) {
		$_id        = $_GET['uid'];
		$collection = $_GET['collection'];
		$base       = $_GET['base'];

		$grid           = skelMongo::connectBase($base)->getGridFs($collection);
		$ct             = $grid->findOne(array('_id' => new MongoId($_id)));
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
			case 'html':
				$ctype = 'text/html';
				break;
			case 'php':
			case 'txt':
				$ctype = 'text/plain';
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
	} else {
		$_id        = $_GET['uid'];
		$collection = $_GET['collection'];
		$base       = $_GET['base'];

		$mongodoc = $_POST['mongodoc'];
		?>
		<div class="relative none" style="overflow:hidden;width:100%;">
			<iframe src="/mdl/document/document_preview.php?frameLoaded=frameLoaded&<?= http_build_query($_POST); ?>" style="display:block;height:100%;overflow:auto;width:100%;margin:0 auto;" marginheight="0" marginwidth="0"
			        frameborder="0">
			</iframe>
		</div>
		<?
	}
?>