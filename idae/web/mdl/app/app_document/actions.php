<?
	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	$APP->init_scheme('sitebase_base', 'document',['hasTypeScheme'=>1]);
	$APP->init_scheme('sitebase_base', 'document_type');
	$APP->init_scheme('sitebase_base', 'document_extension');

	$APP_DOC = new App('document');
	$APP_DOC_TYPE = new App('document_type');
	$APP_DOC_EXTENSION = new App('document_extension');
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
		case 'addDoc':
			if (empty($_POST['base']) || empty($_POST['filename'])) break;
			$metadata                  = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars']);
			$FILENAME      = !empty($_POST['mongoImg']) ? $_POST['mongoImg'] : $_POST['filename'];
			$metadata['time']          = time();
			$metadata['date']          = date('Y-m-d');
			$metadata['heure']         = date('H:i:s');
			$metadata['idagent_owner'] = (int)$_SESSION['idagent'];
			$metadata['filesize']      = $_POST['filesize'];
			$metadata['filetype']      = $_POST['filetype'];
			//
			$file          = new stdClass;
			$file->name    = $FILENAME;
			$file->size    = $metadata['filesize'];
			$bytes         = file_get_contents("php://input");
			$originalBytes = $bytes;
			$db            = $APP->plug_base($_POST['base']);
			$grid          = empty($_POST['collection']) ? $db->getGridFs() : $db->getGridFs($_POST['collection']);
			$gridSource    = $db->getGridFs('source');
			$md5           = md5($bytes);
			$test          = $grid->find(array("filename" => $FILENAME, "md5" => md5($bytes)));


			/*if ($test->count() != 0 && empty($_POST['rw'])):
				break;
			endif;*/
			//
			$ext = strtolower(substr($_POST['filename'], strrpos($_POST['filename'], ".")));
			$ext = str_replace('.', '', $ext);
			if(!empty($_POST['vars']['table']) && !empty($_POST['vars']['table_value'])){
				$_doc_table = $_POST['vars']['table'];
				$_doc_table_value = (int)$_POST['vars']['table_value'];
				$APP_TMP = new App($_doc_table);
				$ARR_TMP = $APP_TMP->findOne(['id'.$_doc_table=>$_doc_table_value]);
				$filename = $_doc_table.' '.$ARR_TMP['nom'.ucfirst($_doc_table)];
			}
			$DOC['iddocument_type']              = $APP_DOC_TYPE->create_update(['codeDocument_type' => $_POST['vars']['table']], ['nomDocument_type' => $_POST['vars']['table']]);
			$DOC['iddocument_extension']         = $APP_DOC_EXTENSION->create_update(['codeDocument_extension' => $ext], ['nomDocument_extension' => $ext]);
			$DOC['codeDocument']                 = $_POST['filename'];
			$DOC['descriptionDocument']          = '';
			$DOC['dateCreationDocument']          = date('Y-m-d');
			$DOC['heureCreationDocument']          = date('H:i:s');
			$DOC['nomDocument']                  = $_POST['filename'];// empty($filename)?$_POST['filename'] : $filename;// $APP_DOC->get_full_titre_vars($_POST['vars']);
			$DOC['id' . $_POST['vars']['table']] = (int)$_POST['vars']['table_value'];
			$DOC['iddocument']                   = (int)$APP_DOC->insert($DOC);
			// ECRITURE
			$_id = $grid->storeBytes($bytes,array("filename" => $FILENAME, "metadata" => array_merge($DOC , $metadata), "metatag" => array_merge($DOC , $metadata)));
			// si image

			if (!empty($_POST['act_thumb'])):
				//	$smallbytes   = fonctionsSite::gridImage($_id, $_POST['collection'], $_POST['base']);
				//	$metadata['thumb'] = 1;
				//	$grid->storeBytes($smallbytes, array("filename" => $FILENAME, "metadata" => $metadata));
			endif;

			if (!empty($_POST['tag'])):

				/*skelMdl::send_cmd('act_gui' , array( 'mdl' => 'app/app_document/app_document_tag' ,
				                                     'vars'    => 'table=document&table_value='.$DOC['iddocument'] ,
				                                     'options' => array(  ) ) );*/
			else:

			endif;
			if(!empty($_POST['reloadModule'])){
				$reloadModule = $_POST['reloadModule'];
				unset($_POST['reloadModule']);
				foreach($reloadModule as $key=>$val){
					// skelMdl::send_cmd('act_update_mdl',['mdl'=>$key,'value'=>$val,'html'=>'red']);
				}

			}
				// skelMdl::reloadModule('');
			exit;
			break;
		case "tagDocument":
			if (empty($_POST['_id']) || empty($_POST['base']) || empty($_POST['collection']) || empty($_POST['tag'])) break;
			$tag        = $_POST['tag'];
			$base       = $_POST['base'];
			$collection = $_POST['collection'];
			$baseF      = $APP->plug_base($base);
			$fs         = $baseF->getGridFs($collection);
			//
			foreach ($_POST['_id'] as $_id):
				$fs->update(array('_id' => MongoCompat::toObjectId($_id)), array('$push' => array('metatag' => $tag)));
			endforeach;
			break;
		case "setmetadata":
			if (empty($_POST['_id']) || empty($_POST['base']) || empty($_POST['collection']) || empty($_POST['tag'])) break;
			$tag        = $_POST['tag'];
			$base       = $_POST['base'];
			$collection = $_POST['collection'];
			$baseF      = $APP->plug_base($base);
			$fs         = $baseF->getGridFs($collection);
			//
			foreach ($_POST['_id'] as $_id):
				$fs->update(array('_id' => MongoCompat::toObjectId($_id)), array('$push' => array('metatag' => $tag)));
			endforeach;
			break;
		case 'deleteDoc':
			if (empty($_POST['base']) || empty($_POST['collection'])) break;
			$_id  = MongoCompat::toObjectId($_POST['_id']);
			$db   = $APP->plug_base($_POST['base']);
			$grid = $db->getGridFS($_POST['collection']);
			$grid->remove(array('_id' => $_id), array('multiple' => false));

			break;
		case 'dropDoc':
			$arrDrop   = $_POST['drop'];
			$arrTarget = $_POST['target'];
			// dropped element
			$db          = $APP->plug_base($arrDrop['base']);
			$grid        = $db->getGridFS($arrDrop['collection']);
			$dropped     = $grid->findOne(array('_id' => MongoCompat::toObjectId($arrDrop['_id'])));
			$arrdropped  = $dropped->file;
			$dropped_src = $dropped->getBytes();
			// on deplace
			$db2    = $APP->plug_base($arrTarget['base']);
			$grid2  = $db2->getGridFS($arrTarget['collection']);
			$arrTag = empty($arrTarget['tag']) ? array() : array('metatag' => $arrTarget['tag']);
			$grid2->storeBytes($dropped_src, $arrTag + array("filename" => $arrdropped['filename'], "metadata" => $arrdropped['metadata']));
			// on enleve ancien
			$grid->remove(array('_id' => MongoCompat::toObjectId($arrDrop['_id'])), array('multiple' => false));
			$_POST['deleteModule'][] = array('trfilename' => $arrDrop['_id']);
			break;
		case "multiDoc":
			if (empty($_POST['base']) || empty($_POST['collection'])) break;

			$arr_id = (array)$_POST['_id'];

			if (!empty($_POST['SUPPRIMER'])):
				foreach ($arr_id as $value_id) {
					$db   = $APP->plug_base($_POST['base']);
					$grid = $db->getGridFS($_POST['collection']);
					$grid->remove(array('_id' => MongoCompat::toObjectId($value_id)), array('multiple' => false));

					$_POST['deleteModule'][] = array('trfilename' => $value_id);
				}
				break;
			endif;
			if (!empty($_POST['searchDataClient']) || !empty($_POST['searchDataDevis'])):
				$varsdata = fonctionsProduction::cleanPostMongo($_POST['vars']);

				foreach ($arr_id as $value_id) {
					$db       = $APP->plug_base($_POST['base']);
					$grid     = $db->getGridFS($_POST['collection']);
					$rs       = $grid->findOne(array('_id' => MongoCompat::toObjectId($value_id)));
					$arrDatas = $rs->file['metadata'];
					$toInsert = array_merge($arrDatas, $varsdata);
					$grid->update(array('_id' => MongoCompat::toObjectId($value_id)), array('$set' => array('metadata' => $toInsert)), array('upsert' => true));

				}
				break;
			endif;
			break;
	}

	include_once(DOCUMENTROOT . '/postAction.php');