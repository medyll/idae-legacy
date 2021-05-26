<?
	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	set_time_limit(10);
	ini_set('max_execution_time', 10);
	ini_set('max_input_time', 10);

//if(isset($_POST['F_action'])){ $F_action =$_POST['F_action'];} else{exit;} 
	array_walk_recursive($_POST, 'CleanStr', $_POST);
	$msg = '';
	$APP = new App();
?>
	<script>
		options = {}
		options.className = 'myddeNotifier';

		<? if(!empty($_POST['noticeMsg'])){$msg = '<br><strong>'.$_POST['noticeMsg'].'</strong>'; } ?>
		<? if(empty($_POST['silentMsg']) && !(empty($msg))){ ?>
		a = new myddeNotifier(options)
		a.growl('<?=$msg?>');
		<? } ?>
		<?
		extract($_POST);
			switch ($F_action){
				case "quitter":
				?>
		localStorage.removeItem('PHPSESSID');
		localStorage.removeItem('SESSID');
		document.location.href = 'index.php';
		<?
		break;  
		case 'identificationAgent':   
		if(!empty($_SESSION['idagent'])){   
		?>
		// ajaxInMdl('identificationagent/mdlIdentificationGood','div_notification_result_login','idagent=<?=$_SESSION['idagent']?>');  
		$('div_notification_result_login').socketModule('identificationagent/mdlIdentificationGood', 'idagent=<?=$_SESSION['idagent']?>');
		// document.location.href='http://<?=$_SERVER['HTTP_HOST']?>';
		<?	 } else {  ?>
		ajaxInMdl('identificationagent/mdlIdentificationFail', 'div_notification_result_login', '', {single: true});
		<?
		}
		break; 
		case "createClient":
		?>
		ajaxMdl('client/client_fiche', 'Fiche client', 'idclient=<?=$_POST['idclient']?>');
		<?
		break;
		case "makeDevisSite":
		?>
		setTimeout(function () {
			<?=fonctionsJs::devis_update($_POST["iddevis"])?>
		}.bind(this), 1250)
		<?
		break;
		case "uploadWallpaper":
		?>
		window.parent.reloadModule('settings/mdlSettingsWallpaper', '*');
		<?
		break; 
		case "delWallPaper":
		?>
		reloadModule('settings/mdlSettingsWallpaper', '*');
		<?
		break;
	}
////
$upd = [];
if(!empty($_POST['table'])){
	$upd['table'] = $_POST['table'];
}
if(!empty($_POST['table_value'])){
	$upd['table'] = (int)$_POST['table_value'];
}
if(!empty($_POST['iddevis'])){
	$upd['iddevis'] = (int)$_POST['iddevis'];
}
if(!empty($_POST['idclient'])){
	$upd['idclient'] = (int)$_POST['idclient']; 
}
if(!empty($_POST['idproduit'])){
	$upd['idproduit'] = (int)$_POST['idproduit']; 
}
if(!empty($_POST['idfournisseur'])){
	$upd['idfournisseur'] = (int)$_POST['idfournisseur']; 
}
// spy code
$round_numerator = 60 * 5;
$rounded_time = ( round ( time() / $round_numerator ) * $round_numerator );
$upd['codeActivite'] = strtoupper($F_action);
$upd['timeActivite'] = (int)$rounded_time;
$upd['dateActivite']  = date('Y-m-d',$rounded_time); 
$upd['heureActivite'] = date('H:i:s',$rounded_time);
$upd['idagent']= (int)$_SESSION['idagent'];  
$APP->plug('sitebase_base','activity')->update($upd,['$set'=>$upd,'$inc'=>['nb'=>1]],['upsert'=>true]);


////

if(!empty($_POST['deleteModule'])){ 
$_POST['deleteModule']= (array)$_POST['deleteModule'] ;
foreach($_POST['deleteModule'] as $key=>$val)  { 
	
	if(!is_array($val)){
		?>
		$$('[mdl="<?=stripslashes($key)?>"]').each(function (node) {
			if (node.getAttribute('value') == '<?=$val?>' || '<?=$val?>' == '*') {
				new Effect.Highlight(node);
				setTimeout(function () {
					try {
						$(node).close()
					} catch (e) {
						$(node).remove();
					}
					try {
						$(node).fire('dom:close')
					} catch (e) {
						$(node).remove();
					}
					try {
						$(node).remove()
					} catch (e) {
						$(node).remove();
					}
				}.bind(this), 500)
			}
		})
		<?
		}else{
			foreach($val as $keykey=>$realval):
				?>
		$$('[mdl="<?=stripslashes($keykey)?>"]').each(function (node) {
			if (node.getAttribute('value') == '<?=$realval?>' || '<?=$realval?>' == '*') {
				new Effect.Highlight(node);
				setTimeout(function () {
					try {
						$(node).close()
					} catch (e) {
						$(node).remove();
					}
					try {
						$(node).fire('dom:close')
					} catch (e) {
						$(node).remove();
					}
					try {
						$(node).remove()
					} catch (e) {
						$(node).remove();
					}
				}.bind(this), 500)
			}
		})
		<?
			endforeach;
		}
		}
	}
	if(!empty($_POST['afterAction'])){
	foreach($_POST['afterAction'] as $key=>$val)  {
		?>
		$$("[mdl='<?=stripslashes($key)?>']").each(function (node) {
			try {
				$(node).<?=$val?>()
			} catch (e) {
			}
		})
		<?
		}
	}
	?>
	</script>
<?
	if (!empty($_POST['table'] && !empty($_POST['vars']['idnewsletter']))) {
		echo $_POST['vars']['idnewsletter'];
		skelMdl::reloadModule('app/app_newsletter/app_newsletter_preview', $_POST['vars']['idnewsletter']);
	}
	if (!empty($_POST['editAfter'])) {
		$editAfter = $_POST['editAfter'];
		skelMdl::send_cmd('act_gui', ['mdl'     => 'app/app/app_update',
		                              'vars'    => 'table=' . $_POST['table'] . '&table_value=' . $_POST['table_value'],
		                              'options' => []]);
	}
	if (!empty($_POST['maxiAfter'])) {

	}
	if (!empty($_POST['scope'])) {
		$scope = $_POST['scope'];
		if (empty($$scope)) $$scope = $_SESSION['idagent'];
		if (!is_array($$scope)) {
			skelMdl::doCurl('http://' . DOCUMENTDOMAINNOPORT . ':' . SOCKETIO_PORT . '/postScope', ['scope' => $scope, 'value' => $$scope]);
		} else {
			foreach ($$scope as $val):
				skelMdl::doCurl('http://' . DOCUMENTDOMAINNOPORT . ':' . SOCKETIO_PORT . '/postScope', ['scope' => $scope, 'value' => $val]);
			endforeach;
		}
	}
	if (!empty($_POST['reloadModule'])) {
		$i    = 0;
		$json = '';
		$arrj = [];
		foreach ($_POST['reloadModule'] as $key => $val) {
			if (!is_array($val)) {
				$module  = stripslashes($key);
				$arrjson = ['F_action' => 'reloadModule', 'timeStamp' => (int)time(), 'module' => stripslashes($key), 'value' => $val, 'idagent' => $_SESSION['idagent']];
				$arrj[]  = json_encode($arrjson);
				$json .= time() . '|' . json_encode($arrjson);
				$json .= "\r\n";

				echo skelMdl::doCurl('http://' . DOCUMENTDOMAINNOPORT . ':' . SOCKETIO_PORT . '/postReload', ['module' => $module, 'value' => $val]);
			} else {
				foreach ($val as $keykey => $realval) {
					$module  = stripslashes($key);
					$arrjson = ['F_action' => 'reloadModule', 'timeStamp' => (int)time(), 'module' => stripslashes($key), 'value' => $realval, 'idagent' => $_SESSION['idagent']];
					$arrj[]  = json_encode($arrjson);
					$json .= time() . '|' . json_encode($arrjson);
					$json .= "\r\n";
					//skelMongo::connect('reloadModule','sitebase_sockets')->update(array('module'=>$module),array('$set'=>$arrjson),array('upsert'=>true));

					echo skelMdl::doCurl('http://' . DOCUMENTDOMAINNOPORT . ':' . SOCKETIO_PORT . '/postReload', ['module' => $module, 'value' => $realval]);
				}
			}
		}
	}

?>