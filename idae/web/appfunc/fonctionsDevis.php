<?   
	require_once __DIR__ . '/../appclasses/appcommon/MongoCompat.php';
	use AppCommon\MongoCompat;
class fonctionsDevis
{ 
	function fonctionsDevis(){  
	
	} 
	function testCodeClient($texte,$emailClient){
		$texte=strtoupper($texte);
		//$texte = ereg_replace("[0-9]","",$texte);
		$regexp  = MongoCompat::toRegex('[0-9]' . preg_quote($texte, '/') . '[0-9]', 'i'); 
		$testCli = skelMongo::connect('client','sitebase_devis')->find(array('codeClient'=>$regexp));  
		$testCli->count();
		if($testCli->count()==0){  
				return $texte;//fonctionsDevis::buildCodeClient($texte);
			}else{
				// meme mail ?
				$arr = $testCli->getNext();
				$testCli2 = skelMongo::connect('client','sitebase_devis')->find(array('codeClient'=>$texte,'emailClient'=>$emailClient)); 
				if($testCli2->count()!=0){
					// incremente
					return $testCli->count().$texte;
					}else{
					return $texte;	
						}
			}
		// meme mail ? 
		$testCli2 = skelMongo::connect('client','sitebase_devis')->find(array('codeClient'=>$texte));  
			return $texte.$testCli->count();
		return fonctionsDevis::buildCodeClient($texte.$testCli->count());
	}
	function buildCodeClient($texte,$len=8){ 
		$texte=strtolower($texte);
		$texte = str_replace(
			array(
				'à', 'â', 'ä', 'á', 'ã', 'å',
				'î', 'ï', 'ì', 'í', 
				'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
				'ù', 'û', 'ü', 'ú', 
				'é', 'è', 'ê', 'ë','ê','&' ,strtoupper('&') ,
				'ç', 'ÿ', 'ñ', '\'','"','_','!','?','\\'
			),
			array(
				'a', 'a', 'a', 'a', 'a', 'a', 
				'i', 'i', 'i', 'i', 
				'o', 'o', 'o', 'o', 'o', 'o', 
				'u', 'u', 'u', 'u', 
				'e', 'e', 'e', 'e', 'e','e','e',
				'c', 'y', 'n', '-','-','-','-','-','-'
			),$texte
		);
		$texte = str_replace(
			array(
				 'a','e','i','o','u','y'
			),
			array(
				'','e','i','o','u','y',''
			),$texte
		);
		$arrt = explode(' ',$texte);
		$dsp='';
		foreach($arrt as $value){
				if(strlen($value)>4) {
				$value=substr($value,0,4); 
				}
				$dsp .= $value;
			}  
		//echo "'".$dsp."'  ";
		// $texte = array_unique($texte);
		$texte = str_replace("-","",$dsp); 
		if(strlen($texte)>$len) {
				$texte=substr($texte,0,$len); 
				} 
		$texte = stripslashes(strtoupper($texte));
		//echo $texte.'/';
		return  $texte  ; 
	} 
	    
}  ?>