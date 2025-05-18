<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	ignore_user_abort(true);
	$time = time();
	$vars = array( 'notify' => 'Début mise en production' );

	$db                     = $APP->plug_base('sitebase_production');
	$collection             = $db->produit;
	$collection_tarif       = $db->produit_tarif;
	$collection_tarif_gamme = $db->produit_tarif_gamme;

?>
<?
	if(empty($_POST['run'])): 
?>
	<div style="width:950px;">
		<table class="tabletop">
			<tr>
				<td style="width:90px;text-align:center"><br>
					<i class="fa fa-database fa-2x"></i></td>
				<td class="texterouge"><br>
					Voulez vous lancer une recherche ?<br>
					<form id="che_form">
					<input name="DAVALUE" type="text" placeholder="element recherché"><br><input name="DAVALUE2" type="text" placeholder="Autre valeur"><br><input name="DAKEY" type="text" placeholder="dans table">
					<br>  </form>
				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_check_job"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Rechercher" onclick="$('frame_xmlte_ch').loadModule('app/app_admin/app_migration_check',$('che_form').serialize()+'&run=1')"  >
			<input type="button" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_ch" scrolling="auto"></div>
	</div>
<?
	return;
endif;
	set_time_limit(0);
	ini_set('max_execution_time' , 0);
	ini_set('max_input_time' , 0);
	ini_set('display_errors' , 55);
	////////////////////////////////////////////////////

	$_GET = empty($_GET) ? $_POST : $_GET;

	$arr_tables = get_artis_table();
	 
	
	$DAVALUE = empty($_GET['DAVALUE'])? 'CCBO': $_GET['DAVALUE'] ; 
	$DAVALUE2 = empty($_GET['DAVALUE2'])? '': $_GET['DAVALUE2'] ;
	$DAKEY = empty($_GET['DAKEY'])? '--': $_GET['DAKEY'] ;
	
 
	?>
	<div main_auto_tree>
	<?
	$i=0;
	foreach ($arr_tables as $key => $name_table) {
		$i++;
		$id = 'id' . $name_table;
		$app_cli = $APP -> plug('sitebase_sync' , $name_table  ); 
		 
			
		$rs = $app_cli->find();
			
		while ($arr = $rs -> getNext()) { 
			 foreach ($arr as $key => $value) {
			 	if ($key=="N_ID" && !empty($_POST['NONID'])) continue; 
				 if($value==$DAVALUE ):
					 $css =  '';
					 if(!empty($DAVALUE2)){
						 foreach ($arr as $key2 => $value2) {
							 if ($value2 != $DAVALUE2) continue;
							 $css = 'textrouge';
						 }
					 }
					if(str_find( $key,$DAKEY,true)) $css .= 'border4 ededed';
					 ?>
					 <div auto_tree >
					 <span  class="<?=$css?>" ><?=$name_table.' : N_ID:'.$arr['N_ID'].'  KEY : '.$key.' VALUE : '.$value?></span>
					 </div>
					 <div  class="retrait" style="display:none"> 
					 	<div act_defer mdl="app/app_admin/app_migration_check" vars="run=1&NONID=1&DAVALUE=<?=$arr['N_ID']?>&DAKEY=<?=str_replace('t_', '', $key)?>">
					 		
					 	</div>  
					 </div>
					 <?  
				 endif;
			 }
		}
//
		skelMdl::send_cmd('act_progress' , array( 'progress_name'    => 'check_job' ,
		                                          'progress_value'   => $i ,
		                                          'progress_max'     => sizeof($arr_tables) ,
		                                          'progress_message' => $DAKEY.' pour table '.$name_table ) , session_id());
												  
	}
?>
</div> 