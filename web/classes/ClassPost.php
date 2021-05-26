<?   
class Post
{
	function Post(){
	}
	function sendPost($params){
		extract($params,EXTR_OVERWRITE) ;
		// The url to submit the form to 
		$method = "POST"; 
		// Build the request data string
		$request_data = "";
		foreach ($data as $name => $value){
			if (strlen($request_data) > 0) { $request_data .= '&';}
			//$request_data .= $name.'='.urlencode($value);
			}
		$request_data = http_build_query($data);
		// Build the request
		$url = parse_url($url);
		$method = strtoupper($method);
		$request     = $method." ";
		$request .= $url['path']." HTTP/1.0\r\n".
		"Host: ".$url['host']."\r\n"."Content-type: application/x-www-form-urlencoded\r\n".
		"Content-length: ".strlen($request_data)."\r\n\r\n".$request_data;
		//echo $url['path'];
		// return;
		$this->url = $url;
		$this->request = $request;
		$this->doPost();
	}
	
	function doPost(){  
	$response = '';
	// Open the connection 
	$fp = fsockopen($this->url['host'], 80, $err_num, $err_msg, 5); 
	// Submit form
	fputs($fp, $this->request);
	// Get the response 
	while (!feof($fp)) {echo  fgets($fp, 1024); flush();}
	fclose($fp); 	
	}
}
?>