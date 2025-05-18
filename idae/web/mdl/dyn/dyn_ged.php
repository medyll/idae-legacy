<?
	include_once($_SERVER['CONF_INC']);

	if (!defined('SMTPHOSTGED')) {
		// skelMdl::send_cmd('act_notify', ['msg' => 'ged abort']);
		exit;
	}
	ini_set('display_errors', 55);

	skelMdl::send_cmd('act_notify', ['msg' => '..'], session_id());

	$FILE_COLLECT = array();
	$APP          = new App();
	$APP->init_scheme('sitebase_base', 'document');
	$APP->init_scheme('sitebase_base', 'document_type');
	$APP->init_scheme('sitebase_base', 'document_extension');

	$APP_DOC           = new App('document');
	$APP_DOC_TYPE      = new App('document_type');
	$APP_DOC_EXTENSION = new App('document_extension');
	$APP_CONTRAT       = new App('contrat');
	$APP_AGENT         = new App('agent');
	$APP_CLIENT        = new App('client');

	$base = $APP_AGENT->plug_base('sitebase_ged');
	$GED  = $base->ged;
	$grid = $base->getGridFs('ged_bin');

	$RF_K      = $APP_DOC->get_grille_fk('document');
	$RF_K_KEYS = array_keys($RF_K);

	$imap = new Imap(SMTPHOSTGED, SMTPUSERGED, SMTPPASSGED, '143', 'novalidate-cert');

	$imapObj = $imap->returnImapMailBoxmMsgInfoObj();
	$INBOXES = $imap->returnMailboxListArr();

	if (is_array($INBOXES)) {

		foreach ($INBOXES as $I_INBOXES) {
			if ($MAILBOX == 'Trash') continue;
			$MAILBOX = $I_INBOXES;
			skelMdl::send_cmd('act_notify', ['msg' => $MAILBOX], session_id());
			$emailArr = $imap->returnMailBoxHeaderArr(urldecode($MAILBOX));
			rsort($emailArr);

			if (is_array($emailArr)) {
				foreach ($emailArr as $i) {
					$MSGNO = trim($i['msgno']);
					//
					$email = $imap->returnEmailMessageArr(urldecode($MSGNO));
					skelMdl::send_cmd('act_notify', ['msg' => trim($metadata['subject'])], session_id());
					$metatag        = [];
					$metadata       = $i;//fonctionsProduction::cleanPostMongo($i);
					$metadata['to'] = str_replace('"', '', $metadata['to']);
					$testA          = $APP_AGENT->findOne(array('emailAgent' => $metadata['from']));
					$testC          = $APP_CLIENT->findOne(array('codeClient' => trim($metadata['subject'])));
					$testD          = $APP_CONTRAT->findOne(array('codeContrat' => trim($metadata['subject'])));

					foreach ($RF_K_KEYS as $key_fk => $FK) {
						$name_fk = $FK;
						$id      = 'id' . $name_fk;
						$APP_TMP = new App($FK);
						$testA   = $APP_TMP->findOne(array('code' . ucfirst($FK) => new MongoRegex('/^' . trim($metadata['subject']) . '^/i')));
						if (!empty($testC[$id])) {
							$metatag[$id]           = $metadata[$id] = (int)$testC[$id];
							$metatag['table']       = $FK;
							$metatag['table_value'] = (int)$testC[$id];
						}
					}

					/*
					 * if (!empty($testA['idagent'])) {$metatag['idagent'] =$metadata['idagent'] = (int)$testA['idagent'];}
					if (!empty($testC['idclient'])){ $metatag['idclient'] =$metadata['idclient'] = (int)$testC['idclient'];$metatag['table']='client';}
					if (!empty($testD['idcontrat'])) {$metatag['idcontrat'] = $metadata['idcontrat'] = (int)$testD['idcontrat'];$metatag['table']='contrat';}*/

					$attachments = '';
					if (isset($email['attachments'])) {
						foreach ($email['attachments'] as $IO) {
							foreach ($IO as $key => $value):
								$IO[$key] = Imap::decodeMimeStr($value);
							endforeach;
							echo $IO['name'] . ' ';
							$pattern = '/image0(.*).(gif|jpg|jpeg|png)|ATT0(.*).(gif|jpg|jpeg|png)|img0(.*).(gif|jpg|jpeg|png)|shareon_(.*).(gif|jpg|jpeg|png)|(.*)WRD0(.*).(gif|jpg|jpeg|png)|(.*)';// -__(.*)/i
							$TEST    = preg_match($pattern, $IO['name']);
							if (!$TEST) {
								skelMdl::send_cmd('act_notify', ['msg' => trim($IO['name'])], session_id());
								$FILE_COLLECT[$IO['part']]['filename']    = $IO['name'];
								$FILE_COLLECT[$IO['part']]['filecontent'] = $imap->getRawAttachment($MSGNO, $IO['part']);
								$FILE_COLLECT[$IO['part']]['metadata']    = $metadata;
								$FILE_COLLECT[$IO['part']]['metatag']     = $metatag;
							}
						}
					}
					//
					$_if = (empty($metadata['idagent_owner']) ? '*' : $metadata['idagent_owner']);
					// skelMdl::reloadModule('document/document_spy', $_if);
					$imap->deleteMail($MSGNO);
				}
			}
		}
	}
	foreach ($FILE_COLLECT as $key => $FILE):
		$DOC = [] + $FILE['metatag'];
		echo "<br>" . $FILE['filename'];
		$ext                            = strtolower(substr($FILE['filename'], strrpos($FILE['filename'], ".")));
		$ext                            = str_replace('.', '', $ext);
		$DOC['iddocument_type']         = $APP_DOC_TYPE->create_update(['codeDocument_type' => $FILE['table']], ['nomDocument_type' => $FILE['table']]);
		$DOC['iddocument_type']         = $APP_DOC_TYPE->create_update(['codeDocument_type' => $ext], ['nomDocument_type' => $ext]);
		$DOC['iddocument_extension']    = $APP_DOC_EXTENSION->create_update(['codeDocument_extension' => $ext], ['nomDocument_extension' => $ext]);
		$DOC['codeDocument']            = $FILE['filename'];
		$DOC['descriptionDocument']     = $FILE['body'];
		$DOC['nomDocument']             = $FILE['filename'];
		$DOC['dateCreationDocument']    = date('Y-m-d');
		$DOC['heureCreationDocument']   = date('H:i:s');
		$DOC['iddocument']              = $APP_DOC->insert($DOC);
		$FILE['metadata']['iddocument'] = (int)$DOC['iddocument'];
		$grid->storeBytes($FILE['filecontent'], array('filename' => $FILE['filename'], 'metadata' => $FILE['metadata'], 'metatag' => $FILE['metatag']));

	endforeach;
	$imap->disconnect();