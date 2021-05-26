<?
	include_once($_SERVER['CONF_INC']);
	$APP       = new App();
	$APP_AGENT = new App('agent');
	$APP_MAIL  = new App('email');

	$time     = time();
	$arrAgent = $APP_AGENT->findOne(['idagent' => (int)$_SESSION['idagent']]);
	imap_timeout(1, 5);
	imap_timeout(4, 5);
	$mbox = imap_open("{" . SMTPHOST . ":143/imap/novalidate-cert}", $arrAgent['emailAgent'], $arrAgent['mailPasswordAgent']);

?>
<?= ($arrAgent['emailAgent'] . ' ' . $arrAgent['mailPasswordAgent']) ?>
<div class="applink">
	<img src="<?= ICONPATH ?>email16.png"/>
	&nbsp;Dossier personnel
</div>
<?
	// $folders = imap_listmailbox($mbox, "{127.0.0.1:143}", "*");

	$list = imap_getmailboxes($mbox, "{" . SMTPHOST . ":143}", "*");
	if (is_array($list)) {
		foreach ($list as $key => $val) {
			$arr  = explode('INBOX.', $val->name);
			$arr2 = explode('}', $val->name);
			unset($arr[0]);
			$out = str_replace(['Sent', 'Drafts', 'Junk', 'Trash'], ['Envoyés', 'Brouillons', 'Spams', 'Corbeille'], implode('-', $arr));
			if (empty($out)) $out = 'Boite de reception';
			?>
			<div class="applink applinkblock retrait">
				<a onclick="reloadModule('app/app_mail/app_mail_liste','*','mailbox=<?= $arr2[1] ?>')">
					<?= $out ?>
				</a>
			</div>
			<?
		}
	} else {
		echo "imap_getmailboxes a échoué : " . imap_last_error() . "\n";
	}

	imap_close($mbox);
?>
