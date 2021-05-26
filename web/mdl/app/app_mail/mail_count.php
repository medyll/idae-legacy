<?
if(file_exists('conf.inc.php')) include_once('conf.inc.php'); 
include_once($_SERVER['CONF_INC']);

ini_set('display_errors',0);

 
//
$rsAgent = 	skelMongo::connect('agent','sitebase_base')->find(array('idagent'=>(int)$_POST['idagent']));

$col = 	skelMongo::connect('email','sitebase_email');//->findOne(array('uniqid'=>$uniqid));  
while($arrAgent = $rsAgent->getNext()){
	$idagent= (int)$arrAgent['idagent'];
	if(!empty($arrAgent['emailAgent']) && !empty($arrAgent['mailPasswordAgent'])){
		$mbox=imap_open( "{".SMTPHOST.":143/imap/novalidate-cert}" , $arrAgent['emailAgent'],$arrAgent['mailPasswordAgent']);
		if ($mbox){  
			if ($hdr = imap_check($mbox)) {
			  $msgCount = $hdr->Nmsgs; 
			}  
			
			$unread_emails = imap_search($mbox,'UNSEEN'); 
			$emails = imap_search($mbox,'ALL', SE_UID);
		///	echo count($unread_emails).'-'.count($emails);
			
			$result = imap_search($mbox, '\\UNSEEN'); 
			if($result==false){
				 
			}else{  
				
				foreach ($result as $mail) {
					$out = $col->findOne(array('uid'=>(int)$mail,'idagent'=>$idagent ));
					// $status = imap_setflag_full($mbox, $mail, "\\Seen \\Flagged", ST_UID);
					unset($out['html'],$out['text']);
					if(sizeof($out)!=0){
						$final[]= $out;
						 // var_dump(sizeof($out)); echo '<br>';
						// $col->update(array('uid'=>(int)$mail,'idagent'=>(int)$idagent ),array('$set'=>array('SEEN'=>1)),array('upsert'=>true));
						}
				} 
			}
		}  else {    };  
	}
} 
//	$mail= new PHPMailer_mine();
//	code to handle phpmailer
//	$result=$mail->Send();
//	if ($result) {
//	$mail_string=$mail->get_mail_string();
//  imap_append($ImapStream, $folder, $mail_string, "\\Seen");
//	}		 
?>  
 <a style="vertical-align:middle" onClick="ajaxInMdl('app/app_mail/app_mail','tmp_mail','',{onglet:'appMail'})"><?=count($unread_emails)?>&nbsp;<img src="<?=ICONPATH?>mail16.png" /></a>
