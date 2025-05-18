<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 16/03/2016
	 * Time: 10:51
	 */
	$uniqid = uniqid();

	$idnewsletter = $_POST['table_value'];
	$App = new App('newsletter');
	$arr = $App->findOne(array('idnewsletter' => (int)$idnewsletter));
?>
<div id="dev<?=$uniqid?>"  ></div>
<script>
	//$('dev<?=$uniqid?>').fire('dom:close')
	//ajaxInMdl('app/app_newsletter/app_newsletter_build','newsletter_build_<?=$idnewsletter?>','BIG_SCREEN=1&idnewsletter=<?=$idnewsletter?>',{onglet:'<?=addslashes($arr['nomNewsletter'])?>'});
</script>
