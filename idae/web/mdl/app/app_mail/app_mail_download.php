<?php
include_once($_SERVER['CONF_INC']);
require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
use AppCommon\MongoCompat;

$APP = new App();
$APP_AGENT = new App('agent');
$APP_MAIL = new App('email');

$arrAgent = $APP_AGENT->findOne(array('idagent'=>(int)$_SESSION['idagent']));
$idagent = (int)$_SESSION['idagent'];
$con = new POP3(SMTPHOST,'143',$arrAgent['emailAgent'],$arrAgent['mailPasswordAgent']); 
$list = $con->pop3_list(); 
$count_mail = 0;

$connMailbox = $APP->plug('sitebase_email','email');
$connMailbox->ensureIndex(array('idmail'=>1));

$connMailboxMail = $APP->plug('sitebase_email','mailbox_email');
$connMailboxMail->ensureIndex(array('idmail'=>1));

$connMailboxMime = $APP->plug('sitebase_email','mailbox_mime');
$connMailboxMime->ensureIndex(array('idmail'=>1));
?>
<div class="padding borderb">
<a class="iconedesk" onclick="ajaxInMdl('app/app_mail/app_mail','tmp_mail','',{onglet:'Boite de reception'})"><img src="<?=ICONPATH?>mail16.png" > <span id="count_mail"></span></a>
</div> 
<?
foreach($list as $num=>$msg): 
	if(!empty($msg['message_id'])):
	$out = array();
	$out = fonctionsProduction::cleanPostMongo($msg);
	$out['uid'] = (int)$out['uid'];
	$uid =(int)$out['uid'];
	$out['mongoDate']   = MongoCompat::toDate(strtotime($out['date'])); 
	$out['accountMail'] = $arrAgent['emailAgent'];
	$out['idagent']  = $_SESSION['idagent'];
	$out['idmail'] = $idmail = (int)$APP->getNext('idemail');
	//
	foreach($out as $key=>$value):
		$out[$key] = @iconv_mime_decode($value,0,"UTF-8");
	endforeach;
	// données partagées
	$forAll['idmail'] = $out['idmail'];
	$forAll['accountMail'] = $out['accountMail'];
	$forAll['idagent'] = (int)$out['idagent'];
	$forAll['mongoDate']= $out['mongoDate'];
	$forAll['date']= $out['date'];
	$forAll['message_id'] = $out['message_id'] ; 
	$forAll['from']= $out['from'];
	$forAll['to'] = $out['to'];
	$forAll['nomEmail'] = $out['from'];
	//
	$count = $connMailbox->findOne(array('idmail'=>$idmail));
		if(empty($count)):  
			$connMailbox->insert($out);
		else:
			$connMailbox->update(array('idmail'=>$idmail),array('$set'=>$out),array('upsert'=>true));
		endif;
		$test_header = 	$connMailboxMail->findOne(array('idmail'=>$idmail));
		// pas en base
		if(empty($test_header)):
			$count_mail ++;
			$out = array(); 
			$out['accountMail'] = $arrAgent['emailAgent'];
			$out['idagent'] = (int)$_SESSION['idagent'];
			$out['mongoDate']=MongoCompat::toDate(strtotime($msg['date']));
			$out['message_id'] = $msg['message_id'] ; 
			$out['date'] = iconv_mime_decode($msg['date'],2,"UTF-8");   
			$out['from'] = iconv_mime_decode($msg['from'],2,"UTF-8"); 
			$out['uid'] = (int)$uid;
			$out['msgno'] = $msg['msgno'];
			$out['headers'] = $con->pop3_retr($msg['msgno']);
			$out['headers'] = $con->mail_parse_headers($out['headers']);
			//
			foreach($out['headers'] as $key=>$value):  
				$enc = mb_detect_encoding($out['headers'][$key]);
				if($enc!='ASCII'):  
					$out['headers'][$key] =  iconv_mime_decode($value,0,"UTF-8");    
				endif; 
			endforeach;  
			$arrMime = $con->mail_mime_to_array($out['msgno']);
			if(!empty($arrMime)):
				foreach($arrMime as $key=>$value): 
					if(!empty($value['filename'])){  $out['mime']=1;  }
				endforeach;
			endif;

			// corps du mail
			$testMime = $connMailboxMime->find(array('idmail'=>$idmail));
			if($testMime->count()==0):
				$arrMime = $con->mail_mime_to_array($uid,true);
				$tmp = array();
				$charset='';
					// corps du mail
					foreach($arrMime as $key=>$value):   
						if(!empty($value['charset'])):
							$charset = $value['charset'];
							$array = array('data'=>$value['data']); 
							$tmp[] = $array;   
						endif;
					endforeach; 
					if(sizeof($tmp)==2): 
						$outMime = $tmp[1]['data'];
						$text = $tmp[0]['data'];
					else:   
						$outMime = $text = $tmp[0]['data'];
					endif;	
					if(!empty($outMime)): 
						$enc = mb_detect_encoding($outMime);  
						$outMime = mb_convert_encoding($outMime,'UTF-8',$charset); 
						$outText = mb_convert_encoding($text,'UTF-8',$charset);  
						$connMailboxMime->update(array('idmail'=>$idmail),array('$set'=>$forAll+array('uid'=>(int)$uid,'charset'=>$charset,'mime'=>$outMime,'text'=>$outText)),array('upsert'=>true));
					else:
						echo " empty out ";
					endif;
					$tmp = array();
					// piece jointe
					foreach($arrMime as $key=>$value): 
						if(!empty($value['filename'])){ 
							$array = array('filename'=> iconv_mime_decode($value['filename'],0,"UTF-8"),'name'=>$value['name'],'data'=>$value['data']);
							$tmp[] = $array;  
						} 
					endforeach;
					foreach($tmp as $key=>$value):
						// data to mongo
						$grid = $APP->plug_base('sitebase_mail')->getGridFs();
						$ct = $grid->find(array('idmail'=>$idmail,'filename'=>$value['filename']));
						if($ct->count()==0):
							$grid->storeBytes($value['data'], $forAll+array('uid'=>(int)$uid,'filename'=>$value['filename'])); 
						endif;
					endforeach;
					
			endif;   
			//
			$connMailboxMail->update(array('idmail'=>$idmail),array('$set'=>$forAll+$out),array('upsert'=>true));
			$APP_MAIL->update(array('idmail'=>$idmail),array('$set'=>$forAll+$out),array('upsert'=>true));
			// mongoSocket

		endif;
	
	else:   
		//$uid = $msg["uid"];
		//$con->pop3_dele("$uid:$uid");
		//$con->expunge();
		//$con->close();
	endif;
endforeach;
?>  
<script>
$('count_mail').update('<?=$count_mail?>')
reloadModule("app/app_mail/app_mail_liste","*");
</script>