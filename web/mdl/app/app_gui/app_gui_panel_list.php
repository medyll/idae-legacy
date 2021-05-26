<?
	include_once($_SERVER['CONF_INC']);

	$_POST = array_merge($_GET,$_POST);

	//
	$APP = new App();
	//
	$RSSCHEME = $APP->get_schemes([],'',370);//'codeAppscheme_base'=>'sitebase_base'
	//
	$vars    = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars']) , 1);

	foreach ($RSSCHEME as $arr_dist):
		//
		$table  = $arr_dist['codeAppscheme'];
		if($APP->get_settings($_SESSION['idagent'], 'app_panel_'.$table   )!='true' )continue;
		if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;

		//
		$out       = [ ];
		$APPSC     = new App($table);
		$GRILLE_FK = $APPSC->get_grille_fk();

?>
 <div act_defer class="blanc"  mdl="app/app_gui/app_gui_panel" vars="table=<?=$table?>&vars[idagent]=<?=$_SESSION['idagent']?>" value="<?=$table?>"></div>

		<?

		//
	endforeach;
?>

