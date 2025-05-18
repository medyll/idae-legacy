<? 
include_once($_SERVER['CONF_INC']);
ini_set('display_errors',55); 
$table = $_POST['table'];
$container = (empty($_POST['container']))? 'stat_container'.uniqid() : $_POST['container'] ;
$typeDate = (empty($_POST['typeDate']))? 'dateCreation' : $_POST['typeDate'] ;
$incr = (empty($_POST['incr']))? 'day' : $_POST['incr'] ;
$Table = ucfirst($table);
$name_id = 'id'.$table;

$APP = new App($table); 
 
$incr = '+1 day';

$dateDebut = date_mysql($_POST['dateDebut']);
$dateFin = date_mysql($_POST['dateFin']);  


$dateStart = new DateTime($dateDebut);  
$dateEnd = new DateTime($dateFin); 

while ($dateStart-> format('Y-m-d') <= $dateEnd->format('Y-m-d')){
 	$DADATE =   $dateStart -> format('Y-m-d');
 	$DADATE_MONTH =  $dateStart -> format('Y-m');
	$dateStart -> modify('+1 '.$incr);
	//
	$ct = $APP->find(array($typeDate.$Table=> new MongoRegex('/^'.$DADATE.'/') ))->count();
	//
	$labels[$DADATE] =  '"'.$DADATE.'"';
	$labels_month[$DADATE_MONTH] =  $DADATE;
	$series[$DADATE] =  $ct;
	$series_month[$DADATE_MONTH] =  $ct;

}

$flat_labels =  implode(',',$labels);
$flat_serie =  implode(',',$series);
?> 
 <script type="text/javascript">
	 new Chartist.Bar('#<?=$container?>', {
		 labels: [<?=$flat_labels?>],
		 series: [[<?=$flat_serie?>]]
	 }, {
		 fullWidth: true
	 });


  </script>
  <div id="<?=$container?>"   style="height: 100%; width: 100%;">
  </div>  
