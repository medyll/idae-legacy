<?
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php');
if(file_exists('../../../conf.inc.php')) include_once('../../../conf.inc.php'); 

ini_set('display_errors',55);
  

include_once(REPFONCTIONS_APP.'dom2pdf/dompdf_config.inc.php');
$md5Devis = strip_tags(str_replace('<br />','',$_POST['md5Devis'])); 

$arrDevis  = skelMongo::connect('devis','sitebase_devis')->findOne(array('md5Devis'=>$md5Devis));  
$arrDevisHTML  = skelMongo::connect('devis_html','sitebase_devis')->findOne(array('iddevis'=>(int)$arrDevis['iddevis']));  
$iddevis = (int) $arrDevis['iddevis'];
$idclient = (int) $arrDevis['idclient'];
$md5Devis = $arrDevis['md5Devis'];
$css='';
ob_start();
	if(file_exists('head.css.inc.php')) include_once('head.css.inc.php');
	if(file_exists('../head.css.inc.php')) include_once('../head.css.inc.php');
	if(file_exists('../../head.css.inc.php')) include_once('../../head.css.inc.php');
	$css  = ob_get_contents();
ob_end_clean();

$content = $css.$arrDevisHTML['htmlDevis'];  

$dompdf = new DOMPDF();
$dompdf->load_html($content);
$dompdf->render();  
$pdffile  =  $dompdf->output();
//
$filename = niceUrl($arrDevis['produit']['nomProduit']).'.pdf';
//
$grid = skelMongo::connectFs('sitebase_devis');
$grid->remove(array('md5Devis'=>$md5Devis));   
//
$obj = array('filename'=> $filename,'md5Devis'=>$md5Devis,'idclient'=>$idclient,'iddevis'=>$iddevis,'date'=>new MongoDate()); 
$grid->storeBytes($pdffile,$obj);  


$vars = array('notify'=>'Fin Géneration PDF');
skelMdl::reloadModule('activity/appActivity',$_SESSION['idagent'],$vars);
$vars = array('iddevis'=>$iddevis,'md5Devis'=>$arrDevis['md5Devis']);
skelMdl::reloadModule('devis/devis_preview_inner',$iddevis,$vars); 
?>
<script>
if($('gen_pdf_<?=$iddevis?>')){
	$('gen_pdf_<?=$iddevis?>').unToggleContent();
	}
</script>
