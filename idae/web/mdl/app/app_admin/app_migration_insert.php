<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	ignore_user_abort(true);
	$time = time(); 
 
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
					insérer un élément artis<br>
					<form id="che_form_in">
					<input name="DATABLE" type="text" placeholder="element recherché"><br><input name="DANID" type="text" placeholder="dans table">
					<br>  </form>
				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_check_job"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Rechercher" onclick="$('frame_xmlte_ch_ins').loadModule('app/app_admin/app_migration_insert',$('che_form_in').serialize()+'&run=1')"  >
			<input type="button" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte_ch_ins" scrolling="auto"></div>
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
	
	$DATABLE = empty($_GET['DATABLE'])? 't_bien': $_GET['DATABLE'] ; 
	$DANID = empty($_GET['DANID'])? '0023': $_GET['DANID'] ;
	
 	$COLL = $APP->plug('sitebase_sync',$DATABLE);
 	
 	$rs = $COLL -> find(array('N_ID'=>$DANID));
	echo $rs->count();
	?>
	<div main_auto_tree>
	<?
	$i=0;
	while ($arr = $rs -> getNext()) { 
			 do_artis_rows($DATABLE,$arr);
		} 
?>
</div> 