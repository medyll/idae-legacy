<?
include_once($_SERVER['CONF_INC']);  
$uniqid 	= uniqid();
$_POST 		= fonctionsProduction::cleanPostMongo($_POST,1);
$tag 		= empty($_POST['tag'])? 'notag' : $_POST['tag'] ;
$vars 		= empty($_POST['vars'])? array() : $_POST['vars'] ;
$base 		= empty($_POST['base'])? 'sitebase_ged' : $_POST['base']; 
$collection 	= empty($_POST['collection'])? 'ged_client' : $_POST['collection']; 
$base 	 	= skelMongo::connectBase($base); 
$fs 	 	= $base->getGridFs($collection);
if($tag!='notag'):
	$vars['metatag']=	array('$in'=>array($tag));
else:
	// $vars['$or']	=	array(array('metatag'=>array('$exists'=>0)));
endif;

$vars['metatag']=   array('$in'=>array($tag));
 
$rs 	 	= skelMongo::connect('ged_tag','sitebase_ged')->find()->sort(array('titre'=>1)); 
while($arr=$rs->getNext()){
	$collection = $arr['collection'];
	$base 		= $arr['base'];
	$arr['grilleTag']['notag']="";
	$arr['grilleTag']['TRASH']="Corbeille";
	foreach($arr['grilleTag'] as $TAG=>$value_text):
	$ct 	 	= skelMongo::connectBase($base)->getGridFs($collection)->find(array('metatag'=>array('$in'=>array($TAG))))->count();
		$total=($ct==0)? '' : $ct ;
		?>
		<script>
			$$('[base=<?=$base?>][collection=<?=$collection?>][tag=<?=$TAG?>] [count]').invoke('update',' <?=$total?>')
		</script>
		<? 
	endforeach;
}

?>