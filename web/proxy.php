<? 
// clone dans mdl
include_once('conf.inc.php');
ini_set('display_errors',1);
$_POST+=$_GET; 
$time = time();
unset($_POST['defer']);
$value= (empty($_POST['value']))? rand() :$_POST['value'] ;
echo skelMdl::cf_module($_POST['mdl'],$_POST,$value);?>