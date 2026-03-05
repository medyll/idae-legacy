<?
	include_once($_SERVER['CONF_INC']);
	//
	if (empty($_POST['table'])) return;
	$APP = new App();
	$APP->init_scheme('sitebase_pref', 'agent_table');

	$APP   = new App('agent_table');
	$APP_T = new App($_POST['table']);

	$_POST = fonctionsProduction::cleanPostMongo($_POST);
	$time  = time();

	$table_value = (int)$_POST['table_value'];
	$table       = $_POST['table'];
	$Table       = ucfirst($table);
	$id          = 'id' . $table;

	$ARR = $APP_T->query_one([$id => $table_value]);

	$arrT = $APP->findOne(['idagent' => (int)$_SESSION['idagent'], 'codeAgent_table' => $table, 'valeurAgent_table' => $table_value]);

	 $add_vars = "reloadModule[app/app_component/app_component_table_pin]=$table";

	if (empty($arrT) || sizeof($arrT) == 0):
		?>
		<a class="textgris   inline "
		   onclick="ajaxValidation('app_create','mdl/app/','<?= $add_vars ?>&table=agent_table&vars[idagent]=<?= $_SESSION['idagent'] ?>&vars[codeAgent_table]=<?= $table ?>&vars[valeurAgent_table]=<?= $table_value ?>&vars[nomAgent_table]=<?= strtolower(niceUrl($ARR['nom' . $Table])) ?>')"
		   title="<?= idioma('Mettre en icone sur le bureau') ?>">
			<i class="fa fa-thumb-tack"></i>
		</a>
	<? else: ?>
		<a style="padding:10px;vertical-align:baseline;" class="inline" onclick="ajaxValidation('app_delete','mdl/app/','<?= $add_vars ?>&table=agent_table&table_value=<?= $arrT['idagent_table'] ?>')" title="<?= idioma('Enlever icone bureau') ?>">
			<i class="fa fa-thumb-tack  textvert"></i>
		</a>
		<?
	endif;
?>