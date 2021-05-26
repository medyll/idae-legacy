<?
	include_once($_SERVER['CONF_INC']);

	$APP      = new App('newsletter');
	$APP_ITEM = new App('newsletter_item');

	$uniqid       = uniqid();
	$_POST        = fonctionsProduction::cleanPostMongo($_POST, 1);
	$idnewsletter = $_POST['idnewsletter'];

	$arr = $APP->query_one(array('idnewsletter' => (int)$idnewsletter));

	$pattern         = '/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/';
	$html_newsletter = preg_replace($pattern, "", $arr['htmlNewsletter']);
	$html_newsletter = stripslashes($html_newsletter);
?>
<div style="height:100%;" class="flex_v">
	<div class="titre_entete ">
		<i class="fa fa-code"></i> <?= idioma('Code source HTML') ?> <?= $arr['nomNewsletter'] ?>
	</div>
	<textarea class="flex_main border4 padding" style="width: 100%;"><?= $html_newsletter ?></textarea>
</div>
