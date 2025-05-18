<? 
include_once($_SERVER['CONF_INC']);
	// nbre par date
ini_set('display_errors',55); 
$table = $_POST['table'];
$incr = (empty($_POST['incr']))? 'day' : $_POST['incr'] ;
$Table = ucfirst($table);
$name_id = 'id'.$table;

$APP = new App($table); 
 
$incr = 'month';

$dateDebut = date_mysql($_POST['dateDebut']);
$dateFin = date_mysql($_POST['dateFin']);  


$dateStart = new DateTime($dateDebut);  
$dateEnd = new DateTime($dateFin); 

while ($dateStart-> format('Y-m-d') <= $dateEnd->format('Y-m-d')){
 	$DADATE =   $dateStart -> format('Y-m-d');
 	$DADATE_MONTH =  $dateStart -> format('Y-m');
 	$DADATE_YEAR =  $dateStart -> format('Y');
	//
	switch($incr){
		case "day":
			$ct = $APP->find(array('dateCreation'.$Table=> new MongoRegex('/^'.$DADATE.'/') ))->count();
			$INDEX = $DADATE;
			break;
		case "month":
			$ct = $APP->find(array('dateCreation'.$Table=> new MongoRegex('/^'.$DADATE_MONTH.'/') ))->count();
			$INDEX = $DADATE_MONTH;
			break;
		case "year":
			$ct = $APP->find(array('dateCreation'.$Table=> new MongoRegex('/^'.$DADATE_YEAR.'/') ))->count();
			$INDEX = $DADATE_YEAR; 
			break;
	}
	//
	$labels[$INDEX] =  $$INDEX;
	$series[$INDEX] =  $ct;
	// 
	$dateStart -> modify('+1 '.$incr);

	// type / statut
}

$flat_labels =  implode(',',$labels);
$flat_serie =  implode(',',$series);
	//vardump($flat_serie);
?> 
 <script type="text/javascript">
	 new Chartist.Bar('#chartContainer', {
		 labels: [<?=$flat_labels?>],
		 series: [[<?=$flat_serie?>],[<?=$flat_serie?>]]
	 }, {
		 fullWidth: true
	 });


  </script>
  <div id="chartContainer"   style="height: 100%; width: 100%;">
  </div>  
