<?
	include_once($_SERVER['CONF_INC']);


	// vardump_async([$_POST['start'],$_SESSION['email_running']]);
	if (empty($_SESSION['idagent'])) exit;
	if (empty($_POST['start'])) exit;

	/*if (empty($_SESSION['email_running'])) {
		$_SESSION['email_running'] = $_POST['start'];
	}elseif ($_SESSION['email_running'] != $_POST['start']) exit;*/

	use Ddeboer\Imap\Server;

	// vardump_async([$_POST['start'], 'start']);

	$APP = new App();

	/*$APP->init_scheme('sitebase_email', 'email');
	$APP->init_scheme('sitebase_email', 'emailbox');
	$APP->init_scheme('sitebase_email', 'email_mime');*/

	$APP_AGENT     = new App('agent');
	$APP_EMAIL     = new App('email');
	$APP_EMAILBOX  = new App('emailbox');
	$APP_EMAILMIME = new App('email_mime');

	$count_unread = 0;

	$cred_agent = $APP_AGENT->findOne(['idagent' => (int)$_SESSION['idagent']]);

	if (empty($cred_agent['emailAgent'])) exit;
	if (empty($cred_agent['mailPasswordAgent'])) exit;
	$server = new Server(
		SMTPHOST, // required
		'143',
		'/novalidate-cert'
	);

	$connection = $server->authenticate($cred_agent['emailAgent'], $cred_agent['mailPasswordAgent']);

	// vardump_async([$cred_agent['emailAgent'], 'emailAgent']);

	$mailboxes = $connection->getMailboxes();

	foreach ($mailboxes as $mailbox) {

		$test_box = $APP_EMAILBOX->findOne(['nomEmailbox' => $mailbox->getName()]);
		if (empty($test_box['nomEmailbox'])) {
			$idemailbox = $APP_EMAILBOX->create_update(['idagent' => $_SESSION['idagent'], 'nomEmailbox' => $mailbox->getName()]);
		} else {
			$idemailbox = (int)$test_box['idemailbox'];
		}

		$messages = $mailbox->getMessages();

		foreach ($messages as $message) {
			// $message is instance of \Ddeboer\Imap\Message
			// printr($message);
			/*echo "<br>getNumber<br>" . $message->getNumber();
			echo "<br>getId<br>" . $message->getId();
			echo "<br>getSubject<br>" . $message->getSubject();
			echo "<br>getFrom<br>" . $message->getFrom();
			echo "<br>getTo<br>" . $message->getTo();
			echo "<br>getDate<br>" . $message->getDate()->format('Y-m-d H:i:s');
			echo "<br>isAnswered<br>" . $message->isAnswered();
			echo "<br>isDeleted<br>" . $message->isDeleted();
			echo "<br>isDraft<br>" . $message->isDraft();
			echo "<br>isSeen<br>" . $message->isSeen();
			echo "<br>getHeaders<br>" . json_encode($message->getHeaders(), JSON_PRETTY_PRINT);
			echo "<br>getBodyText<br>" . $message->keepUnseen()->getBodyText();
			echo "<br>getBodyHtml<br>" . $message->keepUnseen()->getBodyHtml();*/
			//
			$count_unread += $message->isSeen();
			//
			$out['idemailbox'] = $idemailbox;
			$out['referenceEmail'] = $message->getNumber();
			$out['codeEmail']      = $message->getId();
			$out['nomEmail']       = $message->getSubject();
			$out['fromEmail']        =  utf8_encode($message->getFrom());
			$out['toEmail']        =  utf8_encode($message->getTo());
			$out['dateCreationEmail']    = $message->getDate()->format('Y-m-d');
			$out['heureCreationEmail']   = $message->getDate()->format('H:i:s');
			$out['estReponduEmail']      = $message->isAnswered();
			$out['estSupprimeEmail']     = $message->isDeleted();
			$out['estBrouillonMail']     = $message->isDraft();
			$out['estVuEmail']           = (int)$message->isSeen();
			$out['descriptionHTMLEmail'] = $message->keepUnseen()->getBodyHtml();
			$out['descriptionEmail']     = $message->keepUnseen()->getBodyText();

			$test_email = $APP_EMAIL->findOne(['referenceEmail' => $out['referenceEmail']]);
			if (empty($test_email['referenceEmail'])) {
				$idemail = $APP_EMAIL->create_update(['idagent' => (int)$_SESSION['idagent'], 'referenceEmail' => $out['referenceEmail']], $out);
				// skelMdl::send_cmd('act_notify', ['msg' => $message->keepUnseen()->getBodyHtml(), 'options' => ['stiscky' => true]]);
			} else {

			}

			// skelMdl::send_cmd('act_notify', ['msg' => $message->getFrom(), 'options' => ['sticky' => true]]);

			$attachments = $message->getAttachments();

			foreach ($attachments as $attachment) {
				//  $attachment is instance of \Ddeboer\Imap\Message\Attachment
				//	echo $attachment->getFilename();
			}
		}

	}

	if (!empty($count_unread)) {
		//$a = sprintf('Vous avez  %s mails non lu(s)', $count_unread);
		//skelMdl::send_cmd('act_notify', ['msg' => $a, 'options' => ['sticky' => true]]);
	}
	sleep(10);
	// skelMdl::run('mdl/dyn/dyn_email_agent',['idagent'=>$_SESSION['idagent'],'start'=>$_POST['start']]);

	//skelMdl::send_cmd('act_notify', ['msg' => 'Accés ok ' . $_SESSION['idagent'], 'options' => ['stiscky' => true]]);
	// skelMdl::send_cmd('act_notify' , array( 'msg' => 'Accés ok ' , 'options' => array( 'sticky' => true , 'mdl' => 'app/app/app_fiche_mini' , 'vars' => 'table=produit&table_value=535' ) ) , $session_id);

