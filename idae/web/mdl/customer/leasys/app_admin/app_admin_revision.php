<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/06/14
	 * Time: 00:22
	 */

	include_once($_SERVER['CONF_INC']);

	$APP       = new App();
	$BASE_SYNC = $APP->plug_base('sitebase_sync');

	ini_set('display_errors', 55);

	$time = time();

	if (empty($_POST['run'])):
		?>
		<div style="width:950px;overflow:hidden;">
			<div class="titre_entete flex_h flex_align_middle">
				<div style="width:90px;text-align:center">
					<i class="fa fa-rebel fa-2x"></i></div>
				<div class="texterouge">
					Construire revision
				</div>
			</div>
			<div class="padding margin">
				<progress value="0" id="auto_first_job" style="display:none;"></progress>
				<div data-progress_zone="auto_first_injob"></div>
			</div>
			<div class="buttonZone">
				<input type="button" class="validButton" value="Lancer" onclick="$('frame_xmlte_fst').loadModule('app/app_admin/app_admin_revision','run=1')">
				<input type="button" value="Fermer" class="cancelClose">
			</div>
			<div style=" width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_fst" scrolling="auto"></div>
		</div>
		<?
		return;
	endif;
	clearstatcache();
	MongoCursor::$timeout = -1;
	$time                 = time();
	$APP                  = new App();

	$directory          = realpath(CUSTOMERPATH);
	$revision_file_name = 'rev-'.date('dmy');
	$exclude            = array('.git', realpath(CUSTOMERPATH).'images_base', realpath(CUSTOMERPATH).'app_install');

	$zip = new ZipArchive();
	$zip->open(CUSTOMERPATH.'app_install/'  . $revision_file_name . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

	/**
	 * @param SplFileInfo                     $file
	 * @param mixed                           $key
	 * @param RecursiveCallbackFilterIterator $iterator
	 * @return bool True if you need to recurse or if the item is acceptable
	 */
	$filter = function ($file, $key, $iterator) use ($exclude) {
		if ($iterator->hasChildren() && !in_array($file->getFilename(), $exclude)) {
			return true;
		}

		return $file->isFile();
	};

	$innerIterator = new RecursiveDirectoryIterator(
		$directory,
		RecursiveDirectoryIterator::SKIP_DOTS
	);
	$iterator      = new RecursiveIteratorIterator(
		new RecursiveCallbackFilterIterator($innerIterator, $filter)
	);
	$size_iteraor  = iterator_count($iterator);
	foreach ($iterator as $name => $file) {
		// do your insertion here
		$i++;

		if (!$file->isDir()){
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = str_replace(realpath(CUSTOMERPATH),'',$filePath);//substr($filePath, strlen($directory) + 1);
			//
			skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
			                                        'progress_value'   => $i,
			                                        'progress_max'     => $size_iteraor,
			                                       /* 'progress_log' => $relativePath,*/
			                                        'progress_message' => $relativePath), session_id());
			//
			$zip->addFile($filePath, $relativePath);
		}

	}
	//
	$zip->close();

	skelMdl::send_cmd('act_progress', array('progress_name'    => 'first_job',
	                                        'progress_value'   => $size_iteraor,
	                                        'progress_max'     => $size_iteraor,
	                                        'progress_message' => 'Traitement terminé '), session_id());
?>