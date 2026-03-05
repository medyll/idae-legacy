<?php
include_once($_SERVER['CONF_INC']);
ini_set('display_errors', 55);
$dspLog = (empty($_SESSION["idagent"])) ? '' : 'none';
$dspNotify = (empty($_SESSION["idagent"])) ? 'none' : '';
$login = (empty($_COOKIE["login"])) ? '' : $_COOKIE["login"];

?>
<div style="width:100%;position:relative;height:100%;" class="table tablemiddle transpnoir">
	<div class="cell">

		<div class="transpblanc aligncenter">
			<div class="inline" style="text-align: left;">

				<form class="Form" action="mdl/app/app_login/actions.php" onsubmit="ajaxFormValidation(this);return false;"
					name="formIdentificationUtilisateur" id="formIdentificationUtilisateur">
					<div class="bold uppercase aligncenter padding margin borderb">Identification</div>

					<input type="hidden" name="F_action" id="F_action" value="app_log">

					<div class="ms-TextField is-required">
						<label class="ms-Label">Login</label>
						<input class="ms-TextField-field" placeholder="<?= idioma('Identification') ?>" autofocus required type="text" name="loginAgent" value="<?= $login ?>">

						<div class="ms-Button-description">Saisir votre login</div>
					</div>
					<div class="ms-TextField is-required">
						<label class="ms-Label">Mot de passe</label>
						<input class="ms-TextField-field" placeholder="<?= idioma('Mot de passe') ?>" required type="password" name="passwordAgent">
					</div>

					<div class="ms-TextField">
						<input type="submit" value="Valider" class="ms-Button">
					</div>
					<br>
				</form>
				<div class="aligncenter padding applink"><i class="fa fa-trash"></i> <a onclick="app_cache_reset()">Vider
						le
						cache
						d'application</a>
				</div>
			</div>
		</div>
	</div>
</div>