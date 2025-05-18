<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors' , 55);
	$APP = new App();
	$vars    = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars']) , 1);

	$ARR_SCH = $APP->get_schemes();
	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-15 day');
	$DADATE = $dateStart->format('Y-m-d');

	$col_activity = $APP->plug('sitebase_base' , 'activity_expl');


	$COL = array();


	$rs = $col_activity->find(array('vars.table'=>$_POST['table'], 'idagent' => (int)$_SESSION['idagent'] )+$vars)->sort(array( 'heureActivite' => - 1 ))->limit(1000);

?>
<div class = "applink applinkblock ededed">
	<?
		 while($arr= $rs->getNext()):


			 ?>
		 <a  app_button="app_button" vars="<?=http_build_query($arr['vars'])?>"><?=$arr['nomActivity_expl']?> <?=http_build_query($arr['vars'])?></a>
		 <?
		endwhile;
	?>
</div>

