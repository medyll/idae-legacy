<?php 

class POP3{
		function __construct($host,$port,$user,$pass,$folder="INBOX",$ssl=false){
			imap_timeout(1,5);
			$this->host = $host;
			$this->port = $port;
			$this->folder = $folder;
			$ssl=($ssl==false)?"/novalidate-cert":"";
			$this->conn = imap_open("{"."$host:$port/imap$ssl"."}$folder",$user,$pass)or die('Cannot connect to '.$host.': ' . print_r(imap_errors()));
			return $this->conn ;
		}
		function reopen($folder="INBOX",$ssl=false){
			imap_timeout(1,5);
			$ssl=($ssl==false)?"/novalidate-cert":"";
			imap_reopen($this->conn ,"{"."$this->host:$this->port/imap$ssl"."}$folder")or die('Cannot connect to '.$host.': ' . print_r(imap_errors()));
			return $this->conn ;
		}
		function close(){
			imap_close($this->conn);
			}
		function expunge(){
			imap_expunge($this->conn);
			} 
			
		function getmailboxes(){
			return imap_getmailboxes($this->conn, "{"."$this->host:$this->port}", "*");
		}
		function pop3_stat($connection){
			$check = imap_mailboxmsginfo($connection);
			return ((array)$check);
		}
		function pop3_list($message=""){
			$connection = $this->conn;
			if ($message){
				$range=$message;
			} else {
				$MC = imap_check($connection);
				$range = "1:".$MC->Nmsgs;
			}
			$response = imap_fetch_overview($connection,$range);
			foreach ($response as $msg) $result[$msg->msgno]=(array)$msg;
			$ret = (empty($result))? '':$result;
			return $ret;
		}
		function pop3_retr($message){
			$connection = $this->conn;
			return(imap_fetchheader($connection,$message,FT_PREFETCHTEXT));
		}
		function pop3_dele($message){
			$connection = $this->conn;
			return(imap_delete($connection,$message));
		}
		function mail_parse_headers($headers){
			$headers=preg_replace('/\r\n\s+/m', '',$headers);
			$headers=trim($headers)."\r\n"; /* a hack for the preg_match_all in the next line */
			preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
			foreach ($matches[1] as $key =>$value) $result[$value]=$matches[2][$key];
			return ($result); 
		}
		function mail_mime_to_array($mid,$parse_headers=false){
			$imap = $this->conn;
			$mail = imap_fetchstructure($imap,$mid,FT_UID);
			$mail = $this->mail_get_parts($mid,$mail,0);
			if ($parse_headers) $mail[0]["parsed"]=$this->mail_parse_headers($mail[0]["data"]);
			return($mail);
		}
		function mail_get_parts($mid,$part,$prefix){  
			$imap = $this->conn; 
			$attachments=array();
			$attachments[$prefix]=$this->mail_decode_part($mid,$part,$prefix);
			if (isset($part->parts)) // multipart
			{
				$prefix = ($prefix == "0")?"":"$prefix."; 
				foreach ($part->parts as $number=>$subpart)
					$attachments=array_merge($attachments, $this->mail_get_parts($mid,$subpart,$prefix.($number+1)));
			}
			return $attachments;
		}
		function mail_decode_part($message_number,$part,$prefix){ 
			$connection = $this->conn;
			$attachment = array();
		   if(empty($part)) return;
			if($part->ifdparameters) {
				foreach($part->dparameters as $object) {
					$attachment[strtolower($object->attribute)]=$object->value;
					if(strtolower($object->attribute) == 'filename') {
						$attachment['is_attachment'] = true;
						$attachment['filename'] = $object->value;
						//echo "filename ".$object->value."<br>";
					}
				}
			}
		
			if($part->ifparameters) {
				foreach($part->parameters as $object) {
					$attachment[strtolower($object->attribute)]=$object->value;
					if(strtolower($object->attribute) == 'name') {
						$attachment['is_attachment'] = true;
						$attachment['name'] = $object->value;
						//echo "name ".$object->value."<br>";
					}
				}
			}
		
			$attachment['data'] = imap_fetchbody($connection, $message_number, $prefix,FT_UID); 
			if($part->encoding == 3) { // 3 = BASE64
				$attachment['data'] = base64_decode($attachment['data']);
			}
			elseif($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
				$attachment['data'] = quoted_printable_decode($attachment['data']);
			}
			if(!empty($attachment['charset'])){
			//$attachment['data'] = mb_convert_encoding($attachment['data'],'UTF-8',$attachment['charset']  );
			}
			//$enc =  mb_detect_encoding($attachment['data']);
			foreach($attachment as $index=>$v){
				//echo $index.'-';
				//if($enc!='UTF-8' && !empty($enc))$attachment[$index]=mb_convert_encoding($v,'UTF-8',$enc);
				}
			//echo "<hr>";
			//var_dump($attachment['data']);
			return($attachment);
		}
    
} 

?>