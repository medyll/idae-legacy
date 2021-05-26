<?    
include_once($_SERVER['CONF_INC']); 
$_POST+=$_GET;
array_walk_recursive($_POST, 'CleanStr',$_POST);  

$time=time(); 
ini_set('display_errors',55);  
if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;}

include_once(APPROOT.'classes/ClassSMTP.php');
// vardump($_POST);
switch ($F_action){	 
	case "sendMail";
		$arrCollectContact = array();
		$mail_tmp = $_POST['mail_tmp'];
		$TMP = skelMongo::connect('email_compose_tmp','sitebase_email')->findOne(array('mail_tmp'=>$mail_tmp));
		if(sizeof($TMP['grilleContact'])==0) exit; 
		//
		$arrA     =   skelMongo::connect('agent','sitebase_base')->findOne(array('idagent'=>(int)$_POST['emailFrom'])); 
        $_POST['emailFrom']     = $arrA['emailAgent'];
        $_POST['emailFromName'] = $arrA['prenomAgent'].' '.$arrA['nomAgent'];
        // 
		$arrF = fonctionsProduction::cleanPostMongo($_POST);
		$Body = skelTpl::cf_template('app/app_mail/app_mail',$arrF);
		
		$mail = new PHPMailer();  
		$body = $HTML ;
		$mail->IsSMTP();  
		$mail->IsHTML(); 
		$mail->WordWrap = 50; 
		$mail->SMTPDebug = 0;  
		$mail->SMTPAuth = true;  
		$mail->CharSet = 'UTF-8';
		$mail->Host = "mail.destinationsreve.com";   
		$mail->Username = "meddy@destinationsreve.com"; 
		$mail->Password = "drmeet2013";  
		$mail->SetFrom($_POST['emailFrom'],$_POST['emailFromName']); 
		$mail->AddReplyTo($_POST['emailFrom'],$_POST['emailFromName']); 
		$mail->Subject = $_POST['objet'];
		$mail->AltBody = strip_tags($Body);
		// html to ?
		$search = '/(src=["\'])([^"\']+)(["\'])/';
		$content = preg_replace_callback($search, create_function(
				'$matches',
				'return $matches[1] . data_uri($matches[2]) . $matches[3];'
			), $Body);
		
		// DESTINATAIRES
		foreach($TMP['grilleContact'] as $EM):   
            // echo $EM['email'];
			if(filter_var($EM['email'], FILTER_VALIDATE_EMAIL)){$mail->AddAddress($EM['email'],'destinataire');	}	
             $mail->AddAddress($EM['email'],'destinataire');
             skelMongo::connect('email_contact','sitebase_email')->update(array('idagent'=>(int)$_SESSION['idagent'],'emailContact'=>$EM['email']),array('$set'=>array('timeContact'=>time())),array('upert'=>true));
		endforeach;  
		// DESTINATAIRES CC
		if(!empty($TMP['grilleContactCC'])):
		foreach($TMP['grilleContactCC'] as $EM):   
			if(filter_var($EM['email'], FILTER_VALIDATE_EMAIL)){$mail->AddCC($EM['email'],'destinataire');	}	
            $mail->AddCC($EM['email'],'destinataire');		
             skelMongo::connect('email_contact','sitebase_email')->update(array('idagent'=>(int)$_SESSION['idagent'],'emailContact'=>$EM['email']),array('$set'=>array('timeContact'=>time())),array('upert'=>true));
		endforeach;
        endif;
		// BROCHURE ?
		if(!empty($TMP['grilleBrochure'])):
			foreach($TMP['grilleBrochure'] as $BR):
				$md5Brochure = $BR['md5Brochure'];
				$ct = skelMongo::connect('brochure','sitebase_base')->findOne(array('md5Brochure'=>$md5Brochure)); 
				$content = $ct['htmlBrochure'];
				$search = '/(src=["\'])([^"\']+)(["\'])/';
				$content = preg_replace_callback($search, create_function(
						'$matches',
						'return $matches[1] . data_uri($matches[2]) . $matches[3];'
					), $content);
			 
				$mail->AddStringAttachment(utf8_decode($content),$BR['filename'],"base64","text/html");
			endforeach;
		endif;
		// FICHIER ?
		if(!empty($TMP['grilleFichier'])):
			foreach($TMP['grilleFichier'] as $BR):
				$db  	=	skelMongo::connectBase('sitebase_email');
				$grid 	= 	$db->getGridFS('email_attach'); 
				$rs 	= 	$grid->findOne(array('filename'=>$BR['filename']));
			 	$img = $rs->getBytes();
				$mail->AddStringAttachment($img,$BR['filename'],"base64","application/octet-stream");
				//
				$db  	=  	skelMongo::connectBase('sitebase_email');
				$grid 	= 	$db->getGridFS('email_attach'); 
				$grid->remove(array('filename'=>$BR['filename']));
			endforeach;
		endif;
		//   
		// FACTURE ?
		if(!empty($TMP['grilleFacture'])):
			foreach($TMP['grilleFacture'] as $BR):
				$idfacture = (int)$BR['idfacture'];
				$ct = skelMongo::connect('facture','sitebase_devis')->findOne(array('idfacture'=>$idfacture)); 
				$content = $ct['documentFacture'];
				$search = '/(src=["\'])([^"\']+)(["\'])/';
				$content = preg_replace_callback($search, create_function(
						'$matches',
						'return $matches[1] . data_uri($matches[2]) . $matches[3];'
					), $content);
			 
				$mail->AddStringAttachment(utf8_decode($content),$BR['filename'],"base64","text/html");
			endforeach;
		endif;
		//
		$mail->MsgHTML($Body); 
		// $mail= new PHPMailer_mine();
		if(!$mail->Send()) { echo "Mailer Error: " . $mail->ErrorInfo; }else{
			$arrContact = array_merge((array)$TMP['grilleContact'],(array)$TMP['grilleContactCC']);
			foreach($arrContact as $key=>$ins):
				$ins['last_email_time']=time();
				$ins['last_email_date']=date('Y-m-d');
				skelMongo::connect('email_contact','sitebase_email')->update(array('email'=>$ins['email'],'idagent'=>(int)$_SESSION['idagent']),array('$set'=>$ins,'$inc'=>array('email_sent'=>1)),array('upsert'=>true));
			endforeach;
		     skelMongo::connect('email_sent','sitebase_email')->insert($arrF+array('idagent'=>(int)$_SESSION['idagent']));
			 skelMongo::connect('email_compose_tmp','sitebase_email')->remove(array('mail_tmp'=>$mail_tmp));
		} 
	break; 
	case 'addContact': 
		$mail_tmp = $_POST['mail_tmp'];
		$ins = array('email'=>$_POST['email']);
		$ins = empty($_POST['meta'])? array() : $_POST['meta'];
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleContact'=>$ins)),array('upsert'=>true)); 
	break;
	case 'addContactCC': 
		$mail_tmp = $_POST['mail_tmp'];
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) break;
		$ins = array('email'=>$_POST['email']);
		$ins = empty($_POST['meta'])? array() : $_POST['meta'];
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleContactCC'=>$ins)),array('upsert'=>true));
		break;
	case 'addContactCCI': 
		$mail_tmp = $_POST['mail_tmp'];
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) break;
		$ins = array('email'=>$_POST['email']);
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleContactCCI'=>$ins)),array('upsert'=>true)); 
	break;
	case 'addFichier': 
		ini_set('display_errors',55);
	 
		$mail_tmp		 = $_POST['mail_tmp']; 
		$ins['filename'] = $_POST['filename'] ;
		$ins['filesize'] = $_POST['filesize'] ;
		$ins['filetype'] = $_POST['filetype'] ;
		$ins['mail_tmp'] = $_POST['mail_tmp'] ;

		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$push'=>array('grilleFichier'=>$ins)),array('upsert'=>true)); 
		//
		$file = new stdClass;
		$file->name =   $ins['filename'];
		$file->size =   $ins['filesize'];
		$bytes 		= file_get_contents("php://input"); 	
		
		$db  =skelMongo::connectBase('sitebase_email');
		$grid = $db->getGridFS('email_attach'); 
		$grid->storeBytes($bytes, array("filename"=>$ins['filename'],"metadata" =>$ins));
	
	break;
	case 'deleteContact': 
		$mail_tmp = $_POST['mail_tmp'] ;
		$email = $_POST['email'] ;
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$pull'=>array('grilleContact'=>array('email'=>$email))));
	break;
	case 'deleteContactCC': 
		$mail_tmp = $_POST['mail_tmp'] ;
		$email = $_POST['email'] ;
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$pull'=>array('grilleContactCC'=>array('email'=>$email))));
	break;
	case 'deleteAttach': 
		$mail_tmp = $_POST['mail_tmp'] ;
		$filename = $_POST['filename'] ;
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$pull'=>array('grilleBrochure'=>array('filename'=>$filename))));
	break;
	case 'deleteFichier': 
		$mail_tmp = $_POST['mail_tmp'] ;
		$filename = $_POST['filename'] ;
		skelMongo::connect('email_compose_tmp','sitebase_email')->update(array('mail_tmp'=>$mail_tmp),array('$pull'=>array('grilleFichier'=>array('filename'=>$filename))));
	
		$db  =skelMongo::connectBase('sitebase_email');
		$grid = $db->getGridFS('email_attach'); 
		$grid->remove(array('filename'=>$filename));
			
	break;
	case"dropAttach":
	break;
}   
include_once(DOCUMENTROOT.'/postAction.php');
?>