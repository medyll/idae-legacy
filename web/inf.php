<?
	echo phpinfo();
	echo get_cfg_var("session.gc_maxlifetime");;
	echo ' cool';
	include_once($_SERVER['CONF_INC']);

	exit;
	$_POST += $_GET;
	array_walk_recursive($_POST, 'CleanStr', $_POST);

	ini_set('display_errors', 55);

	include_once(APPROOT . 'classes/ClassSMTP.php');

	$arrCollectContact = array();

	$_POST['emailFrom']     = 'mlebrun@leasys.fr';
	$_POST['emailFromName'] = 'lebrun meddy';
	$_POST['objet']         = 'un petit test';
	//
	$TMP['grilleContact'] []  = ['email'=>'lebtun.meddy@gmail.com'];
	$TMP['grilleContactCC'][] = ['email'=>'lebtun.meddy@gmail.com'];
	//
	//$arrF = fonctionsProduction::cleanPostMongo($_POST);
	$Body = 'hello';//skelTpl::cf_template('app/app_mail/app_mail',$arrF);

	$mail = new PHPMailer();
	$body = $Body;
	$mail->IsSMTP();
	$mail->IsHTML();
	$mail->WordWrap  = 50;
	$mail->SMTPDebug = 2;
	$mail->SMTPAuth  = true;
	//$mail->Port        = 465; // 587 // 465 // 443
	//$mail->SMTPSecure = 'tls';
	$mail->Timeout        = 5;

	$mail->CharSet   = 'UTF-8';
	$mail->Host      = SMTPHOST;
	$mail->Username  = SMTPUSER;
	$mail->Password  = SMTPPASS;
	$mail->SetFrom($_POST['emailFrom'], $_POST['emailFromName']);
	$mail->AddReplyTo($_POST['emailFrom'], $_POST['emailFromName']);
	$mail->Subject = $_POST['objet'];
	$mail->AltBody = strip_tags($Body);
	// html to ?
	$search  = '/(src=["\'])([^"\']+)(["\'])/';
	$content = preg_replace_callback($search, create_function(
		'$matches',
		'return $matches[1] . data_uri($matches[2]) . $matches[3];'
	), $Body);

	// DESTINATAIRES
	foreach ($TMP['grilleContact'] as $EM):
		// echo $EM['email'];
		if (filter_var($EM['email'], FILTER_VALIDATE_EMAIL)) {
			$mail->AddAddress($EM['email'], 'destinataire');
		}
		$mail->AddAddress($EM['email'], 'destinataire');
		// skelMongo::connect('email_contact','sitebase_email')->update(array('idagent'=>(int)$_SESSION['idagent'],'emailContact'=>$EM['email']),array('$set'=>array('timeContact'=>time())),array('upert'=>true));
	endforeach;
	// DESTINATAIRES CC
	if (!empty($TMP['grilleContactCC'])):
		foreach ($TMP['grilleContactCC'] as $EM):
			if (filter_var($EM['email'], FILTER_VALIDATE_EMAIL)) {
				$mail->AddCC($EM['email'], 'destinataire');
			}
			$mail->AddCC($EM['email'], 'destinataire');
			//	skelMongo::connect('email_contact','sitebase_email')->update(array('idagent'=>(int)$_SESSION['idagent'],'emailContact'=>$EM['email']),array('$set'=>array('timeContact'=>time())),array('upert'=>true));
		endforeach;
	endif;
	//
	$mail->MsgHTML($Body);
	// $mail= new PHPMailer_mine();
	if (!$mail->Send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$arrContact = array_merge((array)$TMP['grilleContact'], (array)$TMP['grilleContactCC']);
		foreach ($arrContact as $key => $ins):
			$ins['last_email_time'] = time();
			$ins['last_email_date'] = date('Y-m-d');
			//	skelMongo::connect('email_contact','sitebase_email')->update(array('email'=>$ins['email'],'idagent'=>(int)$_SESSION['idagent']),array('$set'=>$ins,'$inc'=>array('email_sent'=>1)),array('upsert'=>true));
		endforeach;
		//skelMongo::connect('email_sent','sitebase_email')->insert($arrF+array('idagent'=>(int)$_SESSION['idagent']));
		// skelMongo::connect('email_compose_tmp','sitebase_email')->remove(array('mail_tmp'=>$mail_tmp));
	}