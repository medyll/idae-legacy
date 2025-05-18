<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	vardump($_POST);
	//
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$page = (empty($_POST['page'])) ? 0 : $_POST['page'];
	$rppage = (empty($_POST['rppage'])) ? 30 : $_POST['rppage'];
	$rppage = 250;
	//
	$APP = new App();
	//
	$HTTP_VARS = $APP->translate_vars($vars);
	// collecte
	$add = array();
	if (!empty($_POST['mainscope'])) {

	}

	//
	if (!empty($_POST['search'])):
		$search    = trim($_POST['search']);
		$arrSearch = explode(' ', trim($search));
		foreach ($arrSearch as $key => $value) {
			$out[] = new MongoRegex("/.*" . (string)$arrSearch[$key] . "*./i");
		}

		$add = array('$or' => array(array($nom => array('$in' => $out)), array($nom => array('$in' => $out))));

	endif;
	$rs = $APP->get_schemes($vars+$add)->limit($rppage);


?>

<div class="flex_h flex_wrap"
     style = "height:100%;width:100%;overflow:auto;">
	<? foreach ($rs as $arr): ?>
		<div class = "padding" style = "width: 25%;min-height:150px;overflow:hidden;">
			<div class="flex_h blanc shadowbox">
				<div class = "padding bold aligncenter ededed" style="width:50px;"><i class="fa fa-<?= $arr['icon'] ?> fa-2x"></i></div>
				<div class="flex_main">
					<div class="flex_h" style="width:100%;">
					<div class="padding uppercase bold"> <?= $arr['collection'] ?></div>
					<div class = "padding applink alignright flex_main">
						<a  onclick="ajaxInMdl('app/app_prod/app_prod_search','tmp_exp_search_<?=$arr['collection']?>','vars[codeAppscheme]=<?= $arr['codeAppscheme'] ?>&table=<?= $arr['codeAppscheme'] ?>',{onglet:'Espace <?= $arr['nomAppscheme'] ?>'});">
							<?=idioma('Recherche')?> <i class="fa fa-search"></i>
						</a>
					</div></div>
					<div style = "max-height:100px;width:100%;"
					     class = "relative blanc">HOME<?= skelMdl::cf_module('app/app/app_explorer_entete_rfk', ['defer'=>true,'table' => $arr['codeAppscheme'], 'nbRows' => 5]); ?></div>
				</div>
			</div>


		</div>
	<? endforeach; ?>

</div>