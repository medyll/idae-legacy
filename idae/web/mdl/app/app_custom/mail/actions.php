<?
	include_once($_SERVER['CONF_INC']);
	$_POST += $_GET;
	$APP = new App();
	array_walk_recursive($_POST , 'CleanStr' , $_POST);

	$time = time();
	ini_set('display_errors' , 55);
	if ( isset($_POST['F_action']) ) {
		$F_action = $_POST['F_action'];
	} else {
		exit;
	}

	include_once(APPCLASSES . 'ClassSMTP.php');

	$APP_TMP = $APP->plug('sitebase_email','email_compose_tmp' );
	$APP_SENT = $APP->plug('sitebase_email','email_sent' );
	$APP_AGENT = $APP->plug('sitebase_base','agent' );
	$APP_CONTACT = $APP->plug('sitebase_email','email_contact' );
	$APP_FACT = $APP->plug('sitebase_devis','facture' );
	$APP_DEVIS = $APP->plug('sitebase_devis','devis' );
	$APP_BROCH = $APP->plug('sitebase_devis','brochure' );




	// vardump($_POST);
	switch ($F_action) {
		case "sendMail";
			$arrCollectContact = array();
			$mail_tmp          = $_POST['mail_tmp'];
			$TMP               = $APP_TMP->findOne(array( 'mail_tmp' => $mail_tmp ));
			if ( sizeof($TMP['grilleContact']) == 0 ) {
				exit;
			}
			//
			$arrA                   = $APP_AGENT->findOne(array( 'idagent' => (int)$_POST['emailFrom'] ));
			$_POST['emailFrom']     = $arrA['emailAgent'];
			$_POST['emailFromName'] = $arrA['prenomAgent'] . ' ' . $arrA['nomAgent'];
			//
			$arrF = fonctionsProduction::cleanPostMongo($_POST);
			$Body = skelTpl::cf_template('app/mail/mail' , $arrF);

			// echo SMTPHOST.SMTPUSER .SMTPPASS;

			$mail = new PHPMailer();
			$body = $HTML;
			$mail->IsSMTP();
			$mail->IsHTML();
			$mail->WordWrap  = 50;
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth  = true;
			$mail->CharSet   = 'UTF-8';
			$mail->Hostname  = APPSITE;
			$mail->Helo      = DOCUMENTDOMAIN;
			$mail->Host      = SMTPHOST;
			$mail->Username  = SMTPUSER;
			$mail->Password  = SMTPPASS;
			$mail->SetFrom($_POST['emailFrom'] , $_POST['emailFromName']);
			$mail->AddReplyTo($_POST['emailFrom'] , $_POST['emailFromName']);
			$mail->Subject = $_POST['objet'];
			$mail->AltBody = strip_tags($Body);
			// html to ?
			$search  = '/(src=["\'])([^"\']+)(["\'])/';
			$content = preg_replace_callback($search , create_function('$matches' , 'return $matches[1] . data_uri($matches[2]) . $matches[3];') , $Body);

			// DESTINATAIRES
			foreach ($TMP['grilleContact'] as $EM):
				// echo $EM['email'];
				if ( filter_var($EM['email'] , FILTER_VALIDATE_EMAIL) ) {
					$mail->AddAddress($EM['email'] , 'destinataire');
				}
				$mail->AddAddress($EM['email'] , 'destinataire');
				$APP_CONTACT->update(array( 'idagent'      => (int)$_SESSION['idagent'] ,
				                            'emailContact' => $EM['email'] ) , array( '$set' => array( 'timeContact' => time() ) ) , array( 'upert' => true ));
			endforeach;
			// DESTINATAIRES CC
			if ( ! empty($TMP['grilleContactCC']) ):
				foreach ($TMP['grilleContactCC'] as $EM):
					if ( filter_var($EM['email'] , FILTER_VALIDATE_EMAIL) ) {
						$mail->AddCC($EM['email'] , 'destinataire');
					}
					$mail->AddCC($EM['email'] , 'destinataire');
					$APP_CONTACT->update(array( 'idagent'      => (int)$_SESSION['idagent'] ,
					                                                                      'emailContact' => $EM['email'] ) , array( '$set' => array( 'timeContact' => time() ) ) , array( 'upert' => true ));
				endforeach;
			endif;
			// DEVIS ?
			if ( ! empty($TMP['grilleDevis']) ):
				foreach ($TMP['grilleDevis'] as $BR):
					$iddevis = (int)$BR['iddevis'];
					$ct          = $APP_DEVIS->findOne(array( 'iddevis' => $iddevis ));
					$content     = $ct['htmlDevis'];
					$search      = '/(src=["\'])([^"\']+)(["\'])/';
					$content     = preg_replace_callback($search , create_function('$matches' , 'return $matches[1] . data_uri($matches[2]) . $matches[3];') , $content);
					// to newsletter
					$instyle     = new InStyle();
					$content     = $instyle->convert($content);
					//
					$mail->AddStringAttachment(utf8_decode($content) , 'Devis '.$BR['filename'] , "base64" , "text/html");
					$arrF['iddevis']=$iddevis;
					$arrF['nomDevis']=$ct['nomDevis'];
				endforeach;
			endif;
			// BROCHURE ?
			if ( ! empty($TMP['grilleBrochure']) ):
				foreach ($TMP['grilleBrochure'] as $BR):
					$md5Brochure = $BR['md5Brochure'];
					$ct          = $APP_BROCH->findOne(array( 'md5Brochure' => $md5Brochure ));
					$content     = $ct['htmlBrochure'];
					$search      = '/(src=["\'])([^"\']+)(["\'])/';
					$content     = preg_replace_callback($search , create_function('$matches' , 'return $matches[1] . data_uri($matches[2]) . $matches[3];') , $content);

					$mail->AddStringAttachment(utf8_decode($content) , $BR['filename'] , "base64" , "text/html");
				endforeach;
			endif;
			// FICHIER ?
			if ( ! empty($TMP['grilleFichier']) ):
				foreach ($TMP['grilleFichier'] as $BR):
					$db   = $APP->plug_base('sitebase_email');
					$grid = $db->getGridFS('email_attach');
					$rs   = $grid->findOne(array( 'filename' => $BR['filename'] ));
					$img  = $rs->getBytes();
					$mail->AddStringAttachment($img , $BR['filename'] , "base64" , "application/octet-stream");
					//
					$db   = $APP->plug_base('sitebase_email');
					$grid = $db->getGridFS('email_attach');
					$grid->remove(array( 'filename' => $BR['filename'] ));
				endforeach;
			endif;
			//
			// FACTURE ?
			if ( ! empty($TMP['grilleFacture']) ):
				foreach ($TMP['grilleFacture'] as $BR):
					$idfacture = (int)$BR['idfacture'];
					$ct        = $APP_FACT->findOne(array( 'idfacture' => $idfacture ));
					$content   = $ct['documentFacture'];
					$search    = '/(src=["\'])([^"\']+)(["\'])/';
					$content   = preg_replace_callback($search , create_function('$matches' , 'return $matches[1] . data_uri($matches[2]) . $matches[3];') , $content);

					$mail->AddStringAttachment(utf8_decode($content) , $BR['filename'] , "base64" , "text/html");
				endforeach;
			endif;
			//
			$mail->MsgHTML($Body);
			// $mail= new PHPMailer_mine(); //
			if ( ! $mail->Send() ) {
				echo "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$arrContact = array_merge((array)$TMP['grilleContact'] , (array)$TMP['grilleContactCC']);
				foreach ($arrContact as $key => $ins):
					$ins['last_email_time'] = time();
					$ins['last_email_date'] = date('Y-m-d');
					$APP_CONTACT->update(array( 'email'   => $ins['email'] ,
					                                                                      'idagent' => (int)$_SESSION['idagent'] ) , array( '$set' => $ins ,
					                                                                                                                        '$inc' => array( 'email_sent' => 1 ) ) , array( 'upsert' => true ));
				endforeach;
				$arrF['nomEmail_sent'] = trim($_POST['objet']) ;
				$arrF['descriptionEmail_sent'] = trim($Body) ;
				$APP_SENT->insert($arrF + array( 'idagent' => (int)$_SESSION['idagent'] ));
				$APP_TMP->remove(array( 'mail_tmp' => $mail_tmp ));
			}
			break;
		case 'addContact':
			$mail_tmp = $_POST['mail_tmp'];
			$ins      = array( 'email' => $_POST['email'] );
			$ins      = empty($_POST['meta']) ? array() : $_POST['meta'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$push' => array( 'grilleContact' => $ins ) ) , array( 'upsert' => true ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact',$mail_tmp);
			break;
		case 'addContactCC':
			$mail_tmp = $_POST['mail_tmp'];
			if ( ! filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL) ) {
				break;
			}
			$ins = array( 'email' => $_POST['email'] );
			$ins = empty($_POST['meta']) ? array() : $_POST['meta'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$push' => array( 'grilleContactCC' => $ins ) ) , array( 'upsert' => true ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact_cc',$mail_tmp);
			break;
		case 'addContactCCI':
			$mail_tmp = $_POST['mail_tmp'];
			if ( ! filter_var($_POST['email'] , FILTER_VALIDATE_EMAIL) ) {
				break;
			}
			$ins = array( 'email' => $_POST['email'] );
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$push' => array( 'grilleContactCCI' => $ins ) ) , array( 'upsert' => true ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact_cci',$mail_tmp);
			break;
		case 'addFichier':
			ini_set('display_errors' , 55);

			$mail_tmp        = $_POST['mail_tmp'];
			$ins['filename'] = $_POST['filename'];
			$ins['filesize'] = $_POST['filesize'];
			$ins['filetype'] = $_POST['filetype'];
			$ins['mail_tmp'] = $_POST['mail_tmp'];

			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$push' => array( 'grilleFichier' => $ins ) ) , array( 'upsert' => true ));
			//
			$file       = new stdClass;
			$file->name = $ins['filename'];
			$file->size = $ins['filesize'];
			$bytes      = file_get_contents("php://input");

			$db   = $APP->plug_base('sitebase_email');
			$grid = $db->getGridFS('email_attach');
			$grid->storeBytes($bytes , array( "filename" => $ins['filename'] , "metadata" => $ins ));

			skelMdl::reloadModule('app/app_custom/mail/mail_compose_attach',$mail_tmp);
			break;
		case 'deleteContact':
			$mail_tmp = $_POST['mail_tmp'];
			$email    = $_POST['email'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$pull' => array( 'grilleContact' => array( 'email' => $email ) ) ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact',$mail_tmp);
			break;
		case 'deleteContactCC':
			$mail_tmp = $_POST['mail_tmp'];
			$email    = $_POST['email'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$pull' => array( 'grilleContactCC' => array( 'email' => $email ) ) ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact_cc',$mail_tmp);
			break;
		case 'deleteContactCCI':
			$mail_tmp = $_POST['mail_tmp'];
			$email    = $_POST['email'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$pull' => array( 'grilleContactCCI' => array( 'email' => $email ) ) ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_contact_cci',$mail_tmp);
			break;
		case 'deleteAttach':
			$mail_tmp = $_POST['mail_tmp'];
			$filename = $_POST['filename'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$pull' => array( 'grilleBrochure' => array( 'filename' => $filename ) ) ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_attach',$mail_tmp);
			break;
		case 'deleteFichier':
			$mail_tmp = $_POST['mail_tmp'];
			$filename = $_POST['filename'];
			$APP_TMP->update(array( 'mail_tmp' => $mail_tmp ) , array( '$pull' => array( 'grilleFichier' => array( 'filename' => $filename ) ) ));

			$db   = $APP->plug_base('sitebase_email');
			$grid = $db->getGridFS('email_attach');
			$grid->remove(array( 'filename' => $filename ));
			skelMdl::reloadModule('app/app_custom/mail/mail_compose_attach',$mail_tmp);
			break;
		case"dropAttach":
			break;
	}
	include_once(DOCUMENTROOT . '/postAction.php');
?>