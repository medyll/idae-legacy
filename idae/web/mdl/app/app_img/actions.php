<?
	include_once($_SERVER['CONF_INC']);
	global $buildArr;
	global $IMG_SIZE_ARR;

	$_POST += $_GET;
	array_walk_recursive($_POST, 'CleanStr', $_POST);

	$time = time();
	ini_set('display_errors', 55);
	if (isset($_POST['F_action'])) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}

	switch ($F_action) {
		case "deleteImageMongo":
			$APP = new App();
			if (empty($_POST['src'])) {
				return;
			}
			$db   = $APP->plug_base('sitebase_image'); //$con->sitebase_image;
			$grid = $db->getGridFS();
			$file = $grid->findOne($_POST['src']); // Find file in GridFS
			$id   = $file->file['_id']; // Get the files ID
			$grid->delete($id); // Delete the file
			// delete from flattenImg

			break;
		case 'addDoc':

			if (empty($_POST['base']) || empty($_POST['filename'])) {
				break;
			}
			// file extension
			$arr_type = explode('.', $_POST['filename']);
			$ext      = strtolower(end($arr_type));
			//
			$table       = $_POST['table'];
			$table_value = (int)$_POST['table_value'];
			$name_id     = 'id' . $table;
			//
			$APP = new App();
			//
			$base            = empty($_POST['base']) ? 'sitebase_image' : $_POST['base'];
			$collection      = empty($_POST['collection']) ? 'fs' : $_POST['collection'];
			$codeTailleImage = empty($_POST['codeTailleImage']) ? 'small' : $_POST['codeTailleImage'];
			$vars            = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
			$csssource       = (empty($_POST['csssource'])) ? '' : $_POST['csssource'];
			$codeImage       = empty($_POST['codeImage']) ? $table . '-' . strtolower($codeTailleImage) . '-' . $table_value : $_POST['codeImage'];
			$codeImage       = empty($_POST['multiple']) ? $codeImage : $codeImage . '-' . time();
			$file_name       = empty($_POST['keep_file_name']) ? $codeImage . '.' . strtolower($ext) : $_POST['filename'];
			$width           = $IMG_SIZE_ARR[$codeTailleImage][0];
			$height          = $IMG_SIZE_ARR[$codeTailleImage][1];
			if (empty($width)):
				$width  = $buildArr[$codeTailleImage][0];
				$height = $buildArr[$codeTailleImage][1];
			endif;
			//
			$ins                  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
			$ins['time']          = time();
			$ins['date']          = date('Y-m-d');
			$ins['heure']         = date('H:i:s');
			$ins['idagent_owner'] = (int)$_SESSION['idagent'];
			$ins['real_filename'] = $_POST['filename'];
			$ins['filename']      = $file_name;
			$ins['size']          = $codeImage;
			$ins['filesize']      = $_POST['filesize'];
			$ins['filetype']      = $_POST['filetype'];
			$ins['metatag']       = empty($_POST['metatag']) ? [] : $_POST['metatag'];

			//
			/*$file          = new stdClass;
			$file->name    = $ins['filename'];
			$file->size    = $ins['filesize'];*/

			$bytes         = file_get_contents("php://input");
			$originalBytes = $bytes;
			$db            = $APP->plug_base($base);
			$grid          = empty($collection) ? $db->getGridFs() : $db->getGridFs($collection);
			$gridSource    = $db->getGridFs('source');
			$md5           = md5($bytes);
			$test          = $grid->find(["filename" => $file_name, "md5" => md5($bytes)]);

			// $_POST['tag'] = ! empty($_POST['tag']) ? $_POST['tag'] : 'notag';
			if (!empty($_POST['mongoTag'])):
				$ins['mongoTag']  = $_POST['mongoTag'];
				$ins['table']     = $table;
				$ins['tag']       = $_POST['mongoTag'];
				$ins['metatag'][] = $_POST['mongoTag'];
			endif;

			foreach ($vars as $key => $input):
				$ins[$key] = $input;
			endforeach;
			if ($test->count() != 0 && empty($_POST['rw'])):
				break;
			endif;

			if (!empty($_POST['height']) && !empty($_POST['width']) && !empty($_POST['display_width']) && !empty($_POST['original_width']) && !empty($_POST['act_crop'])): // on modifie les bytes
				$Rz['width']           = $_POST['width'];
				$Rz['height']          = $_POST['height'];
				$Rz['original_width']  = $_POST['original_width'];
				$Rz['original_height'] = $_POST['original_height'];
				$Rz['display_width']   = $_POST['display_width'];
				$Rz['display_height']  = $_POST['display_height'];
				$Rz['x1']              = $_POST['x1'];
				$Rz['y1']              = $_POST['y1'];
				$Rz['x2']              = $_POST['x2'];
				$Rz['y2']              = $_POST['y2'];
				$Rz['final_width']     = $width;
				$Rz['final_height']    = $height;
				// ces tailles
				$grid->remove(['filename' => $file_name]);
				$_id = $grid->storeBytes($bytes, ["filename" => $file_name, "metadata" => $ins]); // ,"metadata" =>$ins)+$insmetatag
				// on donne la taille de l'écran :
				$bytes = fonctionsSite::cropImage($_id, $collection, $base, $Rz);
				$grid->remove(['_id' => $_id]);

			elseif (!empty($width) && !empty($height) && !empty($_POST['act_crop'])):
				$Rz['width']  = $width;
				$Rz['height'] = $height;
				$grid->remove(['filename' => $file_name]);
				$bytes = fonctionsSite::imageBytesResize($bytes, $Rz);

			endif;

			// vardump_async($ins, true);
			// ECRITURE
			$_id   = $grid->storeBytes($bytes, ["filename" => $file_name, "metadata" => $ins]);
			$image = $grid->findOne(["filename" => $file_name]);

			// BUILD mongoSize
			if (!empty($_POST['sizeImg'])):
				// Act=>consolidate_image
				switch ($codeTailleImage):
					case  'square':
						$vars['build'] = ['small'  => ['from' => $codeTailleImage, 'width' => $buildArr['small'][0], 'height' => $buildArr['small'][1]],
						                  'squary' => ['from' => $codeTailleImage, 'width' => $buildArr['squary'][0], 'height' => $buildArr['squary'][1]]];
						break;
					case  'small' :
						$vars['build'] = ['smally' => ['from' => $codeTailleImage, 'width' => $buildArr['smally'][0], 'height' => $buildArr['smally'][1]],
						                  'tiny'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['tiny'][0], 'height' => $IMG_SIZE_ARR['tiny'][1]],
						                  'square'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]]
						];
						break;
					case  'large':
						$vars['build'] = ['long'   => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['long'][0], 'height' => $IMG_SIZE_ARR['long'][1]],
						                  'small'  => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['small'][0], 'height' => $IMG_SIZE_ARR['small'][1]],
						                  'tiny'  => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['tiny'][0], 'height' => $IMG_SIZE_ARR['tiny'][1]],
						                  'square' => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]]];
						break;
					case  'wallpaper':
						$vars['build'] = ['small'  => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['small'][0], 'height' => $IMG_SIZE_ARR['small'][1]],
						                  'smally' => ['from' => $codeTailleImage, 'width' => $buildArr['smally'][0], 'height' => $buildArr['smally'][1]],
						                  'square' => ['from' => $codeTailleImage, 'width' => $IMG_SIZE_ARR['square'][0], 'height' => $IMG_SIZE_ARR['square'][1]],
						                  'tiny'   => ['from' => $codeTailleImage, 'width' => $buildArr['tiny'][0], 'height' => $buildArr['tiny'][1]]];
						break;

				endswitch;
				//
				$ins_size = $ins;
				unset($ins_size['filename']);
				foreach ($vars['build'] as $key => $build):
					if (empty($build['width']) || empty($build['height'])) continue;
					$new_src = str_replace($codeTailleImage, $key, $file_name);
					// test _exist //
					$image_sized = $grid->findOne(["filename" => $new_src]);

					if (!empty($image_sized)  ) continue;
					// if (!empty($image_sized->getFilename())  ) continue;
					// GRIDFS
					$thumbed =fonctionsSite::makeGdThumb($file_name, $build['width'], $build['height'], $key, $_POST['mongoTag'], $build['from'], ['real_filename' => $ins['real_filename']]);
					$image_file_sized = str_replace('.jpg','',$dir . $new_src);
					//
					skelMdl::send_cmd('act_notify', ['msg' => ' Génération => '. $new_src.' '.$image_file_sized], session_id());
					//
					skelMdl::reloadModule('app/app_img/image_dyn', $image_file_sized);

				endforeach;

				// THUMB
				$ins_thumb = $ins;
				unset($ins_thumb['filename']);
				$new_file_name         = str_replace($codeTailleImage, 'thumb', $file_name);
				$smallbytes            = fonctionsSite::gridImage($_id, $collection, $base, 50, 50);
				$ins_thumb['thumb']    = 1;
				$ins_thumb['filename'] = $new_file_name;
				// écriture mongodb GRIFS
				$grid->remove(["filename" => $new_file_name]);
				$grid->storeBytes($smallbytes, ["filename" => $new_file_name, "metadata" => $ins_thumb]);

			endif;

			// ecriture sur disk // si keep_file_name sinon
			$dir        = FLATTENIMGDIR . $table . '/';
			$image_file = $dir . $file_name;

			if (!file_exists($dir)) {
				mkdir($dir, 0777);
				exec("chmod $dir 0777");
			}
			exec("chmod $image_file 0777");
			if (file_exists($image_file)) {
				chmod($image_file, 0777);
			}
			$image->write($image_file);

			/*if (!empty($vars)):
				foreach ($vars['build'] as $key => $build):
					$new_src          = str_replace($_POST['mongoSize'], $key, $_POST['mongoImg']);
					$image_sized      = $grid->findOne(array("filename" => $new_src));
					$image_file_sized = $dir . $new_src . '.' . $ext;
					$image_sized->write($image_file_sized);
				endforeach;
			endif;*/

			if (empty($_POST['tag'])):
				?>
				<!--<script > ajaxMdl('document/document_tag', 'TAG ', '<?/*=http_build_query($_POST)*/
				?>', {runonce: true});</script >-->
				<?
			else:

			endif;
			skelMdl::reloadModule('app/app_img/image_dyn', $codeImage);

			// skelMdl::send_cmd('act_reload_img' , array( 'filename' => $ins['filename'] ));
			break;
		case
		"tagDocument":
			if (empty($_POST['_id']) || empty($_POST['base']) || empty($_POST['collection']) || empty($_POST['tag'])) {
				break;
			}
			$tag        = $_POST['tag'];
			$base       = $_POST['base'];
			$collection = $_POST['collection'];
			$baseF      = skelMongo::connectBase($base);
			$fs         = $baseF->getGridFs($collection);
			//
			foreach ($_POST['_id'] as $_id):
				$fs->update(['_id' => new MongoId($_id)], ['$push' => ['metatag' => $tag]]);
			endforeach;
			break;
		case "setmetadata":
			if (empty($_POST['_id']) || empty($_POST['base']) || empty($_POST['collection']) || empty($_POST['tag'])) {
				break;
			}
			$tag        = $_POST['tag'];
			$base       = $_POST['base'];
			$collection = $_POST['collection'];
			$baseF      = skelMongo::connectBase($base);
			$fs         = $baseF->getGridFs($collection);
			//
			foreach ($_POST['_id'] as $_id):
				$fs->update(['_id' => new MongoId($_id)], ['$push' => ['metatag' => $tag]]);
			endforeach;
			break;
		case 'deleteDoc':
			$APP = new App();
			if (empty($_POST['base']) || empty($_POST['collection'])) {
				break;
			}
			$_id  = new MongoId($_POST['_id']);
			$db   = $APP->plug_base($_POST['base']);
			$grid = $db->getGridFS($_POST['collection']);
			$grid->remove(['_id' => $_id], ['multiple' => false]);

			break;
		case 'dropDoc':
			$arrDrop   = $_POST['drop'];
			$arrTarget = $_POST['target'];
			// dropped element
			$db          = skelMongo::connectBase($arrDrop['base']);
			$grid        = $db->getGridFS($arrDrop['collection']);
			$dropped     = $grid->findOne(['_id' => new MongoId($arrDrop['_id'])]);
			$arrdropped  = $dropped->file;
			$dropped_src = $dropped->getBytes();
			// on deplace
			$db2    = skelMongo::connectBase($arrTarget['base']);
			$grid2  = $db2->getGridFS($arrTarget['collection']);
			$arrTag = empty($arrTarget['tag']) ? [] : ['metatag' => $arrTarget['tag']];
			$grid2->storeBytes($dropped_src, $arrTag + ["filename" => $arrdropped['filename'], "metadata" => $arrdropped['metadata']]);
			// on enleve ancien
			$grid->remove(['_id' => new MongoId($arrDrop['_id'])], ['multiple' => false]);
			$_POST['deleteModule'][] = ['trfilename' => $arrDrop['_id']];
			break;
		case "multiDoc":
			if (empty($_POST['base']) || empty($_POST['collection'])) {
				break;
			}

			$arr_id = (array)$_POST['_id'];

			if (!empty($_POST['SUPPRIMER'])):
				foreach ($arr_id as $value_id) {
					$db   = skelMongo::connectBase($_POST['base']);
					$grid = $db->getGridFS($_POST['collection']);
					$grid->remove(['_id' => new MongoId($value_id)], ['multiple' => false]);

					$_POST['deleteModule'][] = ['trfilename' => $value_id];
				}
				break;
			endif;
			if (!empty($_POST['searchDataClient']) || !empty($_POST['searchDataDevis'])):
				$varsdata = fonctionsProduction::cleanPostMongo($_POST['vars']);

				foreach ($arr_id as $value_id) {
					$db       = skelMongo::connectBase($_POST['base']);
					$grid     = $db->getGridFS($_POST['collection']);
					$rs       = $grid->findOne(['_id' => new MongoId($value_id)]);
					$arrDatas = $rs->file['metadata'];
					$toInsert = array_merge($arrDatas, $varsdata);
					$grid->update(['_id' => new MongoId($value_id)], ['$set' => ['metadata' => $toInsert]], ['upsert' => true]);

				}
				break;
			endif;
			break;
	}
	echo "red";
	include_once(DOCUMENTROOT . '/postAction.php');
// skelMdl::reloadModule('document/document_spy', (int)$_SESSION['idagent'], $vars);