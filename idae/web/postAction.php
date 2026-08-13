<?php
/**
 * postAction.php — Post-action response renderer (outputs <script> for SPA consumption).
 *
 * Date: 07/07/14
 * Modified: 2026-03-15 — removed extract($_POST), removed display_errors, English comments
 * Modified: 2026-08-11 — Effect.Highlight -> highlightElement (engine/methods.js);
 *                        the Scriptaculous shim was deleted on 2026-08-09 and the
 *                        two calls below had been throwing "Effect is not defined"
 */
	include_once($_SERVER['CONF_INC']);

	set_time_limit(10);
	ini_set('max_execution_time', '10');
	ini_set('max_input_time', '10');

	array_walk_recursive($_POST, 'CleanStr');
	$msg = '';
	$APP = new App();

	// Explicit variable extraction — replaces dangerous extract($_POST)
	$F_action    = $_POST['F_action'] ?? '';
	$table       = $_POST['table'] ?? '';
	$table_value = $_POST['table_value'] ?? '';
?>
	<script>
	/*
	 * Modified: 2026-08-11 — the emitted JavaScript below no longer goes
	 * through the PrototypeJS compatibility shims ($, $$, .each, .fire).
	 *
	 * $(node) was a no-op on an element: the shim, like Prototype, patches
	 * HTMLElement.prototype, so `$(node).foo()` and `node.foo()` reach the same
	 * function. That includes the dynamic `node.<?=$val?>()` afterAction call
	 * and `node.close()`, which app_window.js assigns per instance
	 * (app_window.js:195), and socketModule, which engine/methods.js installs
	 * on the prototype.
	 */
	function pa_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function pa_qsa(selector) {
		return Array.prototype.slice.call(document.querySelectorAll(selector));
	}

	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	function pa_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

		options = {}
		options.className = 'myddeNotifier';

		<?php if(!empty($_POST['noticeMsg'])){$msg = '<br><strong>'.$_POST['noticeMsg'].'</strong>'; } ?>
		<?php if(empty($_POST['silentMsg']) && !(empty($msg))){ ?>
		a = new myddeNotifier(options)
		a.growl('<?=$msg?>');
		<?php } ?>
		<?php
			switch ($F_action){
				case "quitter":
				?>
		localStorage.removeItem('PHPSESSID');
		localStorage.removeItem('SESSID');
		document.location.href = 'index.php';
		<?php
		break;  
		case 'identificationAgent':   
		if(!empty($_SESSION['idagent'])){   
		?>
		// ajaxInMdl('identificationagent/mdlIdentificationGood','div_notification_result_login','idagent=<?=$_SESSION['idagent']?>');  
		pa_el('div_notification_result_login').socketModule('identificationagent/mdlIdentificationGood', 'idagent=<?=$_SESSION['idagent']?>');
		// document.location.href='http://<?=$_SERVER['HTTP_HOST']?>';
		<?php	 } else {  ?>
		ajaxInMdl('identificationagent/mdlIdentificationFail', 'div_notification_result_login', '', {single: true});
		<?php
		}
		break; 
		case "createClient":
		?>
		ajaxMdl('client/client_fiche', 'Fiche client', 'idclient=<?=$_POST['idclient']?>');
		<?php
		break;
		case "makeDevisSite":
		?>
		setTimeout(function () {
			<?=fonctionsJs::devis_update($_POST["iddevis"])?>
		}.bind(this), 1250)
		<?php
		break;
		case "uploadWallpaper":
		?>
		window.parent.reloadModule('settings/mdlSettingsWallpaper', '*');
		<?php
		break; 
		case "delWallPaper":
		?>
		reloadModule('settings/mdlSettingsWallpaper', '*');
		<?php
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
$upd['codeActivite'] = strtoupper(isset($F_action) ? $F_action : '');
$upd['timeActivite'] = (int)$rounded_time;
$upd['dateActivite']  = date('Y-m-d',$rounded_time); 
$upd['heureActivite'] = date('H:i:s',$rounded_time);
$upd['idagent']= (int)(isset($_SESSION['idagent']) ? $_SESSION['idagent'] : 0);
if(isset($upd['codeActivite']) && $upd['codeActivite'] != 'POLL'){
	$APP->plug('sitebase_base','activity')->update($upd,['$set'=>$upd,'$inc'=>['nb'=>1]],['upsert'=>true]);
}


////

if(!empty($_POST['deleteModule'])){ 
$_POST['deleteModule']= (array)$_POST['deleteModule'] ;
foreach($_POST['deleteModule'] as $key=>$val)  { 
	
	if(!is_array($val)){
		?>
		pa_qsa('[mdl="<?=stripslashes($key)?>"]').forEach(function (node) {
			if (node.getAttribute('value') == '<?=$val?>' || '<?=$val?>' == '*') {
				highlightElement(node);
				setTimeout(function () {
					try {
						node.close()
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
					try {
						pa_fire(node, 'dom:close')
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
					try {
						if (node.parentNode) node.parentNode.removeChild(node)
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
				}.bind(this), 500)
			}
		})
		<?php
		}else{
			foreach($val as $keykey=>$realval):
				?>
		pa_qsa('[mdl="<?=stripslashes($keykey)?>"]').forEach(function (node) {
			if (node.getAttribute('value') == '<?=$realval?>' || '<?=$realval?>' == '*') {
				highlightElement(node);
				setTimeout(function () {
					try {
						node.close()
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
					try {
						pa_fire(node, 'dom:close')
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
					try {
						if (node.parentNode) node.parentNode.removeChild(node)
					} catch (e) {
						if (node.parentNode) node.parentNode.removeChild(node);
					}
				}.bind(this), 500)
			}
		})
		<?php
			endforeach;
		}
		}
	}
	if(!empty($_POST['afterAction'])){
	foreach($_POST['afterAction'] as $key=>$val)  {
		?>
		pa_qsa("[mdl='<?=stripslashes($key)?>']").forEach(function (node) {
			try {
				node.<?=$val?>()
			} catch (e) {
			}
		})
		<?php
		}
	}
	?>
	</script>
<?php
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