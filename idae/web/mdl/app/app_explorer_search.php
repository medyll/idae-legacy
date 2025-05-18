<?
include_once($_SERVER['CONF_INC']);  
$uniqid 	= uniqid();
$_POST 		= fonctionsProduction::cleanPostMongo($_POST,1);

?>
<div class="titre_entete  alignright"><div class="table tablemiddle">
		<div class="cell "><div class="padding borderl"> <i class="fa fa-search"></i></div></div>
		<div class="cell"><input expl_search_button="expl_search_button" type="search"   style="width:200px;" /></div></div>
</div>
