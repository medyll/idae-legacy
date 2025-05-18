<?

	class skelDaemon {
		static function tacheDaemon() {

			$APP_T = new App('tache');
			$APP_A = new App('agent');
			// $rs = $base->onLine->find(array('online'=>1));
			$rsA = $APP_A->find(array('estActifAgent' => 1));
			if ($rsA->count() == 0) die();
			while ($arr = $rsA->getNext()):
				$idagent = (int)$arr['idagent'];
				$arrT    = $APP_T->findOne(array('timeRappelTache' => array('$exists' => 1), 'notifiedTache' => array('$ne' => 1), 'idagent' => $idagent, 'timeDebutTache' => array('$lte' => time(), '$gte' => time() - 120)));
				if (!empty($arrT['idtache'])):
					$vars = array('notify' => 'loadModule', 'loadModule' => 'tache/mdlTacheFiche', 'vars' => array('idtache' => $arrT['idtache'], 'value' => $idagent));
					//skelMdl::reloadModule('activity/appActivity',$idagent,$vars);
					//skelMdl::send_cmd('act_upd_data', $vars);
					skelMdl::send_cmd('act_gui', array('mdl'     => 'app/app_custom/tache/tache_notif',
					                                   'vars'    => 'table=produit&table_value=535',
					                                   'options' => array('runonce' => true)), $arr['PHPSESSID']);
				endif;
			endwhile;

		}

		function _construct() {

		}

		public function rebootSocketApp() {
			exec('forever restart app1.app.js');
		}

		public function saveProdDaemon() {
			$colProd = skelMongo::connect('produit_preprod');
			$colSave = skelMongo::connect('produit_save' . date('dmY'), 'sitebase_save');
			$rs      = $colProd->find();
			while ($arr = $rs->getNext()):
				unset($arr['_id']);
				$out = fonctionsProduction::cleanPostMongo($arr, true);
				$colSave->insert($out);
			endwhile;

		}

		public function prodDaemon() {
			return;
			$bP  = skelMongo::connect('produits');
			$bD  = skelMongo::connect('devis', 'sitebase_devis');
			$bPr = skelMongo::connect('produit_preprod');
			$bPs = skelMongo::connect('produit_saveprod');
			$rs  = $bP->find();
			while ($arr = $rs->getNext()):
				$nbdevis = $bD->find(array('typeDevis' => 'site', 'produit.idproduit' => (int)$arr['idproduit']))->count();
				if ($nbdevis != 0) $bPr->update(array('idproduit' => (int)$arr['idproduit']), array('$set' => array('nbreDevisProduit' => $nbdevis)), array('upsert' => true));
				if (!empty($arr['nombreVueProduit'])) $bPr->update(array('idproduit' => (int)$arr['idproduit']), array('$set' => array('nombreVueProduit' => $arr['nombreVueProduit'])), array('upsert' => true));
			endwhile;

		}

		public function xmlSilversea() {
			exit;

		}

		public function gedDaemon() {
			ini_set('display_errors', 55);

			$FILE_COLLECT = array();
			$APP_DOC          =     new App('document');//skelMongo::connect('agent', 'sitebase_base');
			$APP_DOC_TYPE          =     new App('document_type');//skelMongo::connect('agent', 'sitebase_base');
			$APP_CONTRAT          =     new App('contrat');//skelMongo::connect('agent', 'sitebase_base');
			// $APP->init_scheme('sitebase_base','document');
			$colAgent     =     new App('agent');//skelMongo::connect('agent', 'sitebase_base');
			$colClient    =     new App('client');//skelMongo::connect('client', 'sitebase_devis');

			$base         =$colAgent->plug_base('sitebase_ged');
			$GED          = $base->ged;
			$grid         = $base->getGridFs('ged_bin');

			// $imap = new Imap('mail.destinationsreve.com','ged@destinationsreve.com','malaterre654','143','novalidate-cert');
			// $imap    = new Imap(SMTPHOST, SMTPUSER, SMTPPASS, '993', 'novalidate-cert');
			$imap    = new Imap(SMTPHOST, SMTPUSER, SMTPPASS, '143', 'novalidate-cert');

			$imapObj = $imap->returnImapMailBoxmMsgInfoObj();
			vardump($imapObj);
			$INBOXES = $imap->returnMailboxListArr();

			if (is_array($INBOXES)) {
				vardump($INBOXES);
				foreach ($INBOXES as $I_INBOXES) {
					$MAILBOX = urlencode($I_INBOXES);

					$emailArr = $imap->returnMailBoxHeaderArr(urldecode($MAILBOX));
					rsort($emailArr);
					// vardump($emailArr);
					if (is_array($emailArr)) {
						foreach ($emailArr as $i) {
							$MSGNO = trim($i['msgno']);
							//
							$email          = $imap->returnEmailMessageArr(urldecode($MSGNO));

							$metatag= [];
							$metadata       = fonctionsProduction::cleanPostMongo($i);
							$metadata['to'] = str_replace('"', '', $metadata['to']);
							$testA          = $colAgent->findOne(array('emailAgent' => $metadata['from']));
							$testC          = $colClient->findOne(array('codeClient' => trim($metadata['subject'])));
							$testD          = $APP_CONTRAT->findOne(array('codeContrat' => trim($metadata['subject'])));
							if (!empty($testA['idagent'])) $metatag['idagent'] = (int)$testA['idagent'];
							if (!empty($testC['idclient'])) $metatag['idclient'] = (int)$testC['idclient'];
							if (!empty($testD['idcontrat'])) $metatag['idcontrat'] = (int)$testD['idcontrat'];
							$attachments = '';
							if (isset($email['attachments'])) {
								foreach ($email['attachments'] as $IO) {
									foreach ($IO as $key => $value):
										$IO[$key] = Imap::decodeMimeStr($value);
									endforeach;
									echo  $IO['name'].' ';
									$pattern = '/image0(.*).(gif|jpg|jpeg|png)|ATT0(.*).(gif|jpg|jpeg|png)|img0(.*).(gif|jpg|jpeg|png)|shareon_(.*).(gif|jpg|jpeg|png)|(.*)WRD0(.*).(gif|jpg|jpeg|png)|(.*)-__(.*)/i';
									$TEST    = preg_match($pattern, $IO['name']);
									if (!$TEST) {
										echo ' OK !<br>';
										$FILE_COLLECT[$IO['part']]['filename']    = $IO['name'];
										$FILE_COLLECT[$IO['part']]['filecontent'] = $imap->getRawAttachment($MSGNO, $IO['part']);
										$FILE_COLLECT[$IO['part']]['metadata']    = $metadata;
										$FILE_COLLECT[$IO['part']]['metatag']     = $metatag;
									}
								}
							}
							$_if = (empty($metadata['idagent_owner']) ? '*' : $metadata['idagent_owner']);

							// skelMdl::reloadModule('document/document_spy', $_if);
							//
							// $imap->deleteMail($MSGNO);
						}
					}
				}
			}
			foreach ($FILE_COLLECT as $key => $FILE):
				$DOC = []+$FILE['metatag'];
				echo "<br>".$FILE['filename'];
				$ext = strtolower(substr($FILE['filename'], strrpos($FILE['filename'], ".")));
				$ext = str_replace('.','',$ext);
				$DOC['iddocument_type'] = $APP_DOC_TYPE->create_update(['codeDocument'=>$ext]);
				$DOC['codeDocument'] = $FILE['filename'];
				$DOC['nomDocument'] = $APP_DOC->get_full_titre_vars($FILE['metatag']);
				$DOC['iddocument'] = $APP_DOC->insert($DOC);
				$grid->storeBytes($FILE['filecontent'], array('filename' => $FILE['filename'], 'metadata' => $FILE['metadata'], 'metatag' => $FILE['metatag']));

			endforeach;
			$imap->disconnect();

			return 'END';
			sleep(1);
		}

		public function bosseapartirdecaDaemon() {
			$vars = array('notify' => 'Ged');
			skelMdl::reloadModule('activity/appActivity', '*', $vars);
			$base = skelMongo::connectBase('sitebase_ged');
			$GED  = $base->ged;
			$grid = $base->getGridFs('ged_bin');

			// $imap = new Imap('{mail.destinationsreve.com:143/imap/novalidate-cert}', 'ged@destinationsreve.com','malaterre654');
			$imap    = new Imap('mail.destinationsreve.com', 'ged@destinationsreve.com', 'malaterre654', '143', 'novalidate-cert');
			$imapObj = $imap->returnImapMailBoxmMsgInfoObj();
			echo '<h3>Mailbox Stats <span class="code">imap::returnImapMailBoxmMsgInfoObj()</span></h3><p>Unread: (' . $imapObj->Unread . ') Deleted: (' . $imapObj->Deleted . ') Emails: (' . $imapObj->Nmsgs . ') Size: (' . round($imapObj->Size / 1024 / 1024, 1) . ' MB)</p>';
			echo '<h3>MailBoxes <span class="code">imap::returnMailboxListArr()</span></h3>';
			$INBOXES = $imap->returnMailboxListArr();

			if (is_array($INBOXES)) {
				foreach ($INBOXES as $I_INBOXES) {
					$MAILBOX = urlencode($I_INBOXES);
					echo '<div><a href="?mailbox=' . urlencode($I_INBOXES) . '">' . $I_INBOXES . '</a></div>';

					$emailArr = $imap->returnMailBoxHeaderArr(urldecode($MAILBOX));
					rsort($emailArr);

					if (is_array($emailArr)) {
						foreach ($emailArr as $i) {
							$MSGNO = trim($i['msgno']);
							echo '<div>
							<a href="?mailbox=' . urlencode($MAILBOX) . '&msgno=' . trim($i['msgno']) . '" class="' . ((strtoupper($i['status']) != 'READ') ? 'bold' : '') . '">' . urldecode($i['subject']) . '</a> (' . $i['status'] . ')<br/>
							Date: ' . $i['date'] . '<br/>
							From: ' . $i['from'] . '<br/>
							Size: ' . round(1024 / $i['size'], 1) . ' KB<br/>
							</div>';
							//
							$email = $imap->returnEmailMessageArr(urldecode($MSGNO));

							echo '<div class="block round"> 
							<h4>Body</h4>' . base64_decode($email['html']) . $attachments . '</div>';

							$attachments = '';
							if (isset($email['attachments'])) {
								$attachments = '<h4>Attachments</h4>
								<em>NOTE: In the demo attachments are saved to the current working directory. Make sure this script has write permissions to the folder.</em>
								<ul>';

								foreach ($email['attachments'] as $IO) {
									foreach ($IO as $key => $value):
										$IO[$key] = Imap::decodeMimeStr($value);
									endforeach;
									$attachments .= '<li><a href="?mailbox=' . urlencode($MAILBOX) . '&msgno=' . trim($MSGNO) . '&part=2&name=' . $IO['name'] . '" target="_blank">' . $IO['name'] . ' (' . round(1024 / $IO['bytes'], 1) . ' KB)</a></li>';
								}
								$attachments .= '</ul>';
							}
						}
					}
				}
			}
			// $mailBoxArr = $imap->returnMailboxListArr();

			//
			return 'END';
//		foreach($arrMail as $key=>$value):
//			$OUT 				= array();
//			$mail 				= $mailbox->getMail($value);
//			$OUT['dateCreationGed'] = date('Y-m-d',strtotime($mail->date));
//			$OUT['heureCreationGed'] = date('H:i:s',strtotime($mail->date));
//			$OUT['infoMeta'] 			=	$mail->subject;
//			$OUT['fromNameGed'] 	=	$mail->fromName;
//			$OUT['fromAdressGed'] 	=	$mail->fromAdress;
//			//
// 			if($mail->getAttachments()) {
//				 
//					 
//				foreach($arrAttach as $key_att=>$value_att): 
//					$OUT['attach_id']   = $value_att->id;
//					$OUT['filename']    = $value_att->name;
//					$OUT['mongodoc']   = md5($OUT['uniq_name']);
//					$OUT['md5']   = md5($value_att->data);
//					echo $data = $value_att->data;
//					$pattern = '/image0(.*).(gif|jpg|jpeg|png)|ATT0(.*).(gif|jpg|jpeg|png)|img0(.*).(gif|jpg|jpeg|png)|shareon_(.*).(gif|jpg|jpeg|png)|(.*)WRD0(.*).(gif|jpg|jpeg|png)/i';
//					$TEST = preg_match($pattern,$OUT['filename']);
//					if(!$TEST){ 
//						$ct = $grid->find(array('filename'=>$OUT['filename']));  
//						//
//						if($ct->count()==0):  
//							$grid->storeBytes($data, array('filename'=>$OUT['filename'],'metadata'=>$OUT) ); 
//						endif;
//					}
//				endforeach;
//				}
//			$mailbox->deleteMail($mail->id);
//		endforeach; 
//		$mailbox->expungeDeletedMails();
			sleep(1);
		}

		public function mailDaemon() {
			// en ged_user d'un coté, en mail de l'autre => génération référence ( md5 ); 
			ignore_user_abort(false);
			set_time_limit(0);
			ini_set('display_errors', 55);
			setlocale(LC_CTYPE, 'fr_FR.UTF8');
			//
			$base = skelMongo::connectBase('sitebase_ged');
			$GED  = $base->ged;
			$grid = $base->getGridFS('ged_bin');

			$collectionMail = skelMongo::connect('email', 'sitebase_email');
			$collectionMail->ensureIndex(array('idagent' => 1));
			$collectionMail->ensureIndex(array('uniqid' => 1));

			$rsAgent = skelMongo::connect('agent', 'sitebase_base')->find(array('idagent' => 1, 'estActifAgent' => 1, 'emailAgent' => array('$exists' => true), 'mailPasswordAgent' => array('$exists' => true)))->sort(array('mailPasswordAgent' => 1));
			while ($arrAgent = $rsAgent->getNext()):
				$OUT            = $OUTMAIL = array();
				$OUT['idagent'] = (int)$idagent = (int)$arrAgent['idagent'];
				echo "<br> ";
				buffer_flush();
				echo $emailAgent = $arrAgent['emailAgent'];
				echo "<br> ";
				buffer_flush();
				$mailPasswordAgent = $arrAgent['mailPasswordAgent'];
				//
				$MAILBOX = new ImapMailbox('{' . SMTPHOST . ':143/imap/novalidate-cert}', $emailAgent, $mailPasswordAgent, '', 'utf-8');
				// $MAILBOX = new ImapMailbox('{mail.destinationsreve.com:143/imap/novalidate-cert}','meddy@destinationsreve.com','drmeet2013','', 'utf-8');
				$mailStream = $MAILBOX->getImapStream();
				$mails      = array();
				//
				$i         = 0;
				$listINBOX = imap_getmailboxes($mailStream, "{" . SMTPHOST . ":143}", "*");
				if (is_array($listINBOX)):
					krsort($listINBOX);
					foreach ($listINBOX as $keyINBOX => $valINBOX):
						$INBOX = '';
						$valINBOX->name;
						$arrINBOX  = explode('INBOX.', $valINBOX->name);
						$arr2INBOX = explode('}', $valINBOX->name);
						unset($arrINBOX[0]);
						$INBOX = implode('-', $arrINBOX);
						if (empty($INBOX)) {
							$INBOX = 'INBOX';
						} else {
							$INBOX = 'INBOX.' . $INBOX;
						}
						//
						$mailbox = new ImapMailbox('{' . SMTPHOST . ':143/imap/novalidate-cert}' . $INBOX, $emailAgent, $mailPasswordAgent, '', 'utf-8');
						// Get some mail
						$rsMail = array();
						$ai     = $mailbox->searchMailBox('ALL');
						$rsMail = $mailbox->getMailsInfo($ai);
						echo "<br>" . $INBOX;
						if (!$rsMail) {
							echo '    Mailbox is empty ';
						} else {
							echo '    ' . sizeof($rsMail);
						}

						foreach ($rsMail as $arrMail):
							$i++;
							echo ". $i . ";
							buffer_flush();
							$mail = $mailbox->getMail($arrMail->uid);

							$mailInfo = $arrMail;
							echo $UNIQUID = $mailInfo->uid . '-' . $idagent;

							$OUT['inbox']            = $INBOX;
							$OUT['message_id']       = $mailInfo->message_id;
							$OUT['uid']              = $mailInfo->uid;
							$OUT['msgno']            = $mailInfo->msgno;
							$OUT['dateCreationGed']  = date('Y-m-d', strtotime($mail->date));
							$OUT['heureCreationGed'] = date('H:i:s', strtotime($mail->date));
							$OUT['tagGed']           = $mail->subject;
							$OUT['fromNameGed']      = $mail->fromName;
							$OUT['fromAdressGed']    = $mail->fromAddress;
							$OUT["uniqid"]           = $UNIQUID;

							/*if($mail->getAttachments() && !empty($mailInfo->uid)):
								$arrAttach = $mail->getAttachments();
								foreach($arrAttach as $key_att=>$value_att):
										$OUT['attach_id']   = $value_att->id;

										$OUT['filename']    = date('ymd-his-',strtotime($mail->date)).'_'.$value_att->name;
										$OUT['name']   = trim($value_att->name);

										$OUT['size']   = strlen($value_att->data);
										$OUT['mongodoc']   = md5($OUT['uniq_name']);
										$data = $value_att->data;
										$pattern = '/image0(.*).(gif|jpg|jpeg|png)|ATT0(.*).(gif|jpg|jpeg|png)|img0(.*).(gif|jpg|jpeg|png)|shareon_(.*).(gif|jpg|jpeg|png)|(.*)WRD0(.*).(gif|jpg|jpeg|png)|(.*)-__(.*)/i';
										$TEST =  preg_match($pattern,$OUT['filename']);
										if(!$TEST){
											$ct = $grid->find(array('filename'=>$OUT['filename']));

											if($ct->count()==0):
												$OUT['md5']   = md5($value_att->data);
												$grid->storeBytes($data, array('filename'=>$OUT['filename'],'metadata'=>$OUT) );
											endif;
										}
								endforeach;
							endif;	*/
							ini_set('mbstring.substitute_character', "none");
							// EN BASE EMAIL
							if (!empty($mailInfo->uid)):
								// UNIQID
								$OUTMAIL["uniqid"] = $UNIQUID;
								//
								$OUTMAIL['inbox']     = $INBOX;
								$OUTMAIL["date"]      = date('Y-m-d', strtotime($mail->date));
								$OUTMAIL["heure"]     = date('H:i:s', strtotime($mail->date));
								$OUTMAIL["from"]      = $mail->fromAddress;
								$OUTMAIL["from_name"] = $mail->fromName;
								$OUTMAIL["idagent"]   = (int)$idagent;
								$OUTMAIL["subject"]   = iconv_mime_decode($mailInfo->subject, 2, "UTF-8");  // $mailInfo->subject;
								//$OUTMAIL["text"] = 		$mail->textPlain;
								$OUTMAIL["html"]        = $mail->textHtml;
								$OUTMAIL["to"]          = (array)$mail->to; //
								$OUTMAIL["to_name"]     = $mail->toString;
								$OUTMAIL["cc"]          = $mail->cc;
								$OUTMAIL["reply_to"]    = $mail->replyTo; //
								$OUTMAIL["in_reply_to"] = $mailInfo->in_reply_to;
								$OUTMAIL["uid"]         = $mailInfo->uid;
								// FLAGS
								$OUTMAIL["recent"]   = $mailInfo->recent;
								$OUTMAIL["flagged"]  = $mailInfo->flagged;
								$OUTMAIL["answered"] = $mailInfo->answered;
								$OUTMAIL["deleted"]  = $mailInfo->deleted;
								$OUTMAIL["seen"]     = $mailInfo->seen;
								$OUTMAIL["draft"]    = $mailInfo->draft;
								//
								// vardump($OUTMAIL);
								// echo "<hr>";
								$OUTM = skelDaemon::clean($OUTMAIL);
								// vardump($OUTM);
								$OUTM["html"]    = htmlentities($mail->textHtml, ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8');
								$OUTM["html"]    = html_entity_decode($OUTM["html"], ENT_QUOTES, 'UTF-8');
								$OUTM["idagent"] = (int)$idagent;
								// CLIENT ?
								$arrClient        = skelMongo::connect('client', 'sitebase_devis')->findOne(array('emailClient' => trim($mail->fromAddress)));
								$OUTM["idclient"] = $arrClient["idclient"];
								//
								$collectionMail->update(array('uniqid' => $UNIQUID), array('$set' => $OUTM), array('upsert' => true));
							else :
								echo " fail here ";
							endif;
							if ($i == 340) exit;
						endforeach;

					endforeach;
				endif;
				buffer_flush();
			endwhile;
		}

		public function clean($mail) {
			$outmail = array();
			foreach ($mail as $keyf => $valuef):
				if (is_array($valuef)) {
					$outmail[$keyf] = skelDaemon::clean($valuef);
				} //$outmail[$keyf] =  iconv_mime_decode($valuef,2,"UTF-8");
				else {
					$outmail[$keyf] = iconv_mime_decode($valuef, 2, "UTF-8");
				}
				//$outmail[$keyf] =  mb_convert_encoding($valuef, 'UTF-8', 'UTF-8');
				//$outmail[$keyf] = htmlentities($valuef, ENT_QUOTES, 'UTF-8');
			endforeach;

			return $outmail;
		}
	} ?>