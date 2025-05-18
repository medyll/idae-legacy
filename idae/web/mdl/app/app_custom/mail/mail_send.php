<?
	include_once($_SERVER['CONF_INC']);
	$APP_AG  = new App('agent');
	$APP_CL  = new App('client');
	$APP_EM  = new App('email');
	$APP_NS  = new App('newsletter');
	$APP_DV  = new App('devis');
	$APP_ENT = new App('entite');

	$time     = time();
	$uniqid   = uniqid();
	$mail_tmp = empty($_POST['mail_tmp']) ? 'mail_tmp' . uniqid() : $_POST['mail_tmp'];

	$idagent = (int)$_SESSION['idagent'];

	$ARR_AGENT = $APP_AG->findOne(['idagent' => $idagent]);
	$rsA       = $APP_AG->find(['estActifAgent' => 1]);
	$ARR_ENT   = $APP_ENT->findOne(['identite' => (int)$ARR_AGENT['identite']]);
	//
	$selectA = fonctionsProduction::getSelectMongo('emailFrom', $rsA, 'idagent', 'prenomAgent', $idagent);

	$insert = $after = '';
	if (!empty($_POST['uniqid'])):
		$uniqid = $_POST['uniqid'];
		$test   = $APP_EM->findOne(['uniqid' => $uniqid]);
		$insert = $test['html'];
	endif;
	if (!empty($_POST['iddevis'])):
		$iddevis = (int)$_POST['iddevis'];
		$test    = $APP_DV->query_one(['iddevis' => $iddevis]);
		$insert  = nl2br($test['texteDevis']);
	endif;
	if (!empty($_POST['idnewsletter'])):
		$idnewsletter = (int)$_POST['idnewsletter'];
		$test         = $APP_NS->query_one(['idnewsletter' => $idnewsletter]);
		$after        = $test['htmlNewsletter'];
	endif;
	$subject = (empty($_POST['subject'])) ? '' : $_POST['subject'];
	$sign    = 'Cordialement,<br>' . $ARR_AGENT['prenomAgent'] . ' ' . $ARR_AGENT['nomAgent'] . '<br>' . $ARR_AGENT['emailAgent']; ?>
<div style="width:950px">
	<div class="ededed">
		<form id="form<?= $uniqid ?>" action="mdl/app/app_custom/mail/actions.php" onsubmit="return false;" auto_close="auto_close">
			<input type="hidden" name="F_action" value="sendMail"/>
			<input type="hidden" name="mail_tmp" value="<?= $mail_tmp ?>"/>
			<input type="hidden" name="afterAction[mdl/app/app_custom/mail_send]" value="close"/>
			<input type="hidden" class="inputMedium" name="emailFrom" value="<?= $arrAgent['emailAgent'] ?>"/>
			<input type="hidden" class="inputMedium" name="emailFromName" value="<?= $arrAgent['prenomAgent'] ?> <?= $arrAgent['nomAgent'] ?>"/>
			<div class="titre_entete fond_noir color_fond_noir borderb">
				<div class="table" style="width:100%">
					<div class="cell">
						<i class="fa fa-envelope"></i>
						&nbsp;Envoyer un mail
					</div>
					<div class="cell" style="width:150px;">
						<?= $selectA ?>
					</div>
					<div class="cell aligncenter " style="width:130px;">
						<button style="width:120px;" type="submit" class="cursor" onclick="ajaxFormValidation($('form<?= $uniqid ?>'));">
							<i class="fa fa-envelope"></i>
							Envoyer
						</button>
					</div>
				</div>
			</div>
			<div id="drag<?= $uniqid ?>" class="margin relative padding blanc">
				<table style="width:100%; table-layout:auto" class="tablemiddle">
					<tr>
						<td style="width:80px;">
							<label>A ...</label>
						</td>
						<td id="email<?= $uniqid ?>" style="vertical-align:middle;">
							<div class="fauxInput">
								<input datalist_input_name="emailInfo" datalist="app/app_custom/mail/mail_contact_select" populate name="email" class="inline"/>
								<div id="contact<?= $uniqid ?>" class="inline">
									<?= skelMdl::cf_module('app/app_custom/mail/mail_compose_contact', ['mail_tmp' => $mail_tmp], $mail_tmp) ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td style="width:80px;">
							<label>Cc ...</label>
						</td>
						<td id="email_cc<?= $uniqid ?>" style="vertical-align:middle;">
							<div class="fauxInput">
								<input datalist_input_name="emailInfoCC" datalist="app/app_custom/mail/mail_contact_select" populate name="email" class="inline"/>
								<div id="contact_cc<?= $uniqid ?>" class="inline">
									<?= skelMdl::cf_module('app/app_custom/mail/mail_compose_contact_cc', ['mail_tmp' => $mail_tmp], $mail_tmp) ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<label>Objet</label>
						</td>
						<td>
							<input type="text" style="width:100%" name="objet" value="<?= $subject ?>" required="required"/>
						</td>
					</tr>
					<tr>
						<td>
							<label>Fichiers</label>
						</td>
						<td>
							<div class="inline applink borderr relative" style="overflow:hidden;vertical-align:middle;">
								<i class="fa fa-files-o"></i>
								<a>Ajouter</a>
								<input class="cursor" type="file" multiple="multiple" style="opacity:0;position:absolute;left:0;top:0;"/>
							</div>
							<div class="inline" id="attach<?= $uniqid ?>">
								<?= skelMdl::cf_module('app/app_custom/mail/mail_compose_attach', $_POST + ['scope' => 'mail_tmp', 'mail_tmp' => $mail_tmp], $mail_tmp) ?>
							</div>
							<div id="listing<?= $uniqid ?>"></div>
						</td>
					</tr>
				</table>
			</div>
			<div class="margin relative padding blanc">
				<table style="width:100%; table-layout:auto" class="tablemiddle">
					<tr>
						<td colspan="2"><textarea ext_mce_textarea class="required" style="width:100%;height:400px" name="texteMail"><?= $insert ?><?= $after ?>
								<br><br>
								<br><br>
								<? if(!empty($_POST['confirm'])){
									?>
									<a href="<?=HTTPEXTERNALCUSTOMERSITE?>secure/paiement/<?=$test['md5Devis']?>" target="_blank">procéder au paiement
									<?
								}?>
								<br><br>
								<table border="0">
									<tr>
										<td>
											<img src="<?= Act::imgApp('entite', $ARR_AGENT['identite'], 'square') ?>">
										</td>
										<td style="vertical-align:top;">
											<?= $sign ?>
											<br>
											<br>
											<table>
												<tr>
													<td>Tel Agence</td>
													<td><span style="font-weight: bold;"><?= $ARR_ENT['telephoneEntite'] ?></span></td>
												</tr>
												<tr>
													<td>Mail Général</td>
													<td><?= $ARR_ENT['emailEntite'] ?></td>
												</tr>
												<tr>
													<td>Fax Agence</td>
													<td><?= $ARR_ENT['faxEntite'] ?></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</textarea>
						</td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>
<form id="formdrag<?= $uniqid ?>" action="mdl/app/app_custom/mail/actions.php" onsubmit=";return false">
	<input type="hidden" name="F_action" value="addFichier"/>
	<input type="hidden" name="mail_tmp" value="<?= $mail_tmp ?>"/>
</form>
<script>
	$ ('attach<?=$uniqid?>').on ('click', 'a[filename]', function (event, node) {
		filename = $ (node).readAttribute ('filename');
		ajaxValidation ('deleteAttach', 'mdl/app/app_custom/mail/', 'scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&filename=' + filename);
	});
	$ ('attach<?=$uniqid?>').on ('click', 'a[deleteFichier]', function (event, node) {
		filename = $ (node).readAttribute ('deleteFichier');
		ajaxValidation ('deleteFichier', 'mdl/app/app_custom/mail/', 'scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&filename=' + filename);
	});
	$ ('contact<?=$uniqid?>').on ('click', 'a[email]', function (event, node) {
		email = $ (node).readAttribute ('email');
		ajaxValidation ('deleteContact', 'mdl/app/app_custom/mail/', 'scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&email=' + email + '&reloadModule[mdl/app/app_custom/mail_compose_contact]=<?=$mail_tmp?>');
	});
	$ ('contact_cc<?=$uniqid?>').on ('click', 'a[email]', function (event, node) {
		email = $ (node).readAttribute ('email');
		ajaxValidation ('deleteContactCC', 'mdl/app/app_custom/mail/', 'scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&email=' + email + '&reloadModule[mdl/app/app_custom/mail_compose_contact_cc]=<?=$mail_tmp?>');
	});
</script>
<script>
	var input_contact = $ (document.body.querySelector ('[datalist_input_name=emailInfo]'));
	var input_contact_cc = $ (document.body.querySelector ('[datalist_input_name=emailInfoCC]'));
	// Add contact
	$ (input_contact).observe ('dom:act_change', function (event) {
		var email               = event.memo.value;
		var meta                = event.memo.meta || 'meta[nom]=' + email + '&meta[email]=' + email;
		ajaxValidation ('addContact', 'mdl/app/app_custom/mail/', 'mail_tmp=<?=$mail_tmp?>&email=' + email + '&' + meta);
		$ (input_contact).value = '';
	}.bind (this))
	// addcontact CC
	$ (input_contact_cc).observe ('dom:act_change', function (event) {
		var email              = event.memo.value;
		var meta               = event.memo.meta || 'meta[nom]=' + email + '&meta[email]=' + email;
		ajaxValidation ('addContactCC', 'mdl/app/app_custom/mail/', 'mail_tmp=<?=$mail_tmp?>&email=' + email + '&reloadModule[mdl/app/app_custom/mail_compose_contact]=<?=$mail_tmp?>&' + meta);
		input_contact_cc.value = '';
	}.bind (this))

	// mce_area("textarea#texteMail<?=$time?>");
	//
	new myddeAttach ($ ('drag<?=$uniqid?>'), { form : 'formdrag<?=$uniqid?>', autoSubmit : true });
</script> 
