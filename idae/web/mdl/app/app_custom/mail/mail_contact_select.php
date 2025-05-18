<?
	include_once($_SERVER['CONF_INC']);
	$APP  = new App();
	$time = time();
	ini_set('display_errors', 0);
	$vars = (empty($_POST['vars'])) ? [] : $_POST['vars'];
	$add  = [];

	$col_contact = $APP->plug('sitebase_email', 'email_sent');
	//
	$remember  = trim($_POST['email']);
	$arrEmail  = explode(';', $_POST['email']);
	$DAMAIL    = trim(end($arrEmail));
	$NODAMAIL  = str_replace($DAMAIL, '', $remember);
	$arrSearch = explode(' ', trim($DAMAIL));
	foreach ($arrSearch as $key => $value) {
		$out[] = new MongoRegex("/.*" . (string)$arrSearch[$key] . "*./i");
	}
	if (sizeof($out) == 1) {
		$add = ['$or' => [['nomClient' => ['$in' => $out]], ['prenomClient' => ['$in' => $out]]]];
	}
	if (sizeof($out) > 1) {
		$add = ['$and' => [['nomClient' => ['$in' => $out]], ['prenomClient' => ['$in' => $out]]]];
	}
	//
	if (sizeof($out) == 1) {
		$add2 = ['$or' => [['from' => ['$in' => $out]], ['from_name' => ['$in' => $out]]]];
	}
	if (sizeof($out) > 1) {
		$add2 = ['$and' => [['from' => ['$in' => $out]], ['from_name' => ['$in' => $out]]]];
	}
	//
	if (!empty($_POST['noidclient'])) {
		$add['idclient'] = ['$ne' => (int)$_POST['noidclient']];
	}

	$baseDevis = $APP->plug('devis', 'sitebase_devis');

	$rsOri = $APP->plug('sitebase_email', 'email_contact')->find()->sort(['last_email_time' => -1, 'email_sent' => 1])->limit(10);
	$rs    = $APP->plug('sitebase_devis', 'client')->find($add)->limit(10);
	$rs2   = $APP->plug('sitebase_email', 'email_contact')->find($add2)->limit(10);

?>
<?
	if (empty($_POST['email'])) {
		while ($arr = $rsOri->getNext()) {
			$value = strtolower($arr["email"]);
			$name  = empty($arr["nom"]) ? $value : $arr["nom"] . ' ' . $value;
			$meta  = 'meta[nom]=' . $name;
			$meta .= '&meta[email]=' . $value;
			?>
			<?= display_contact($email, $arr["nom"], $meta) ?>
		<? }
	} ?>
<? while ($arr = $rs2->getNext()) {
	$value = strtolower($arr["email"]);
	$name  = empty($arr["nom"]) ? $value : $arr["nom"] . ' ' . $value;
	$meta  = 'meta[nom]=' . $name;
	$meta .= '&meta[email]=' . $value;
	?>
	<?= display_contact($value, $arr["nom"], $meta) ?>
<? } ?>
<? while ($rs->hasNext()) {
	$arr      = $rs->getNext();
	$idclient = (int)$arr["idclient"];
	$rsD      = $baseDevis->find(['idclient' => $idclient]);
	$value    = strtolower($arr["emailClient"]);
	$nom      = strtolower($arr["prenomClient"]) . ' ' . ucfirst(strtolower($arr["nomClient"]));
	$meta     = 'meta[idclient]=' . $idclient . '&meta[nom]=' . strtolower($arr["prenomClient"]) . ' ' . ucfirst(strtolower($arr["nomClient"]));
	$meta .= '&meta[email]=' . $value;
	?>
	<?= display_contact($value, $nom, $meta) ?>
<? } ?>
<? if (filter_var($DAMAIL, FILTER_VALIDATE_EMAIL)) {
	$meta = 'meta[email]=' . $DAMAIL; ?>
	<div class="fond_noir color_fond_noir padding_more flex_h flex_margin flex_align_middle" onclick="$(this).fire('dom:act_click',{value:'<?= $DAMAIL ?>',meta:'<?= $meta ?>'})">
		<div class="padding borderr"><i class="fa fa-link"></i></div>
		<div>
			<a class="autoToggle">
				Ajouter
				<br><?= $DAMAIL ?>
			</a>
		</div>
	</div>
<? } ?>
<?
	function display_contact($email, $nom, $meta = '') {

		$onclick = "onclick=\"$(this).fire('dom:act_click',{value:'$email',meta:'$meta'})\"";

		$out = "<div $onclick class='borderb padding'><a  class='autoToggle flex flex_h flex_margin flex_align_top cursor'>";
		$out .= "<div><i class='fa fa-user-o'></i></div>";
		$out .= "<div  ><div class='ucfirst'>$nom</div><div>$email</div></div>";
		$out .= '</a></div>';

		return $out;
	}