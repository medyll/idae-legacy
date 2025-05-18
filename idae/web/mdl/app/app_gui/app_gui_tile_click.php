<?
	include_once($_SERVER['CONF_INC']);
	//
	if (empty($_POST['table'])) return;
	$APP = new App();
	$APP->init_scheme('sitebase_base', 'agent_tuile');

	$APP   = new App('agent_tuile');
	$APP_T = new App($_POST['table']);

	$_POST = fonctionsProduction::cleanPostMongo($_POST);
	$time  = time();

	$table_value = (int)$_POST['table_value'];
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$id          = 'id' . $table;

	$ARR = $APP_T->query_one([$id => $table_value]);

	$arrT = $APP->findOne(['idagent' => (int)$_SESSION['idagent'], 'codeAgent_tuile' => $table, 'valeurAgent_tuile' => $table_value]);

	$add_vars = "reloadModule[app/app_gui/app_gui_tile_click]=$table_value";
?>
<div class="inline">
	<?
		if (sizeof($arrT) == 0):
			?>
			<a class="textgris"
			   onclick="ajaxValidation('app_create','mdl/app/','<?= $add_vars ?>&table=agent_tuile&vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[codeAgent_tuile]=<?= $table ?>&vars[valeurAgent_tuile]=<?= $table_value ?>&vars[nomAgent_tuile]=<?= strtolower(niceUrl($ARR['nom' . $Table])) ?>')"
			   title="<?= idioma('Mettre en icone sur le bureau') ?>">
				<i class="fa fa-star"></i>
			</a>
		<? else: ?>
			<a class="" onclick="ajaxValidation('app_delete','mdl/app/','<?= $add_vars ?>&table=agent_tuile&table_value=<?= $arrT['idagent_tuile'] ?>')" title="<?= idioma('Enlever icone bureau') ?>">
				<i class="fa fa-star textvert"></i>
			</a>
			<?
		endif;
	?></div>