<?
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	ini_set('display_errors', 55);

	if (!empty($_POST['table'])):
		$table = $_POST['table'];
		//
		$APP = new App($table);
		//
		$settings_groupBy = $APP->get_settings($_SESSION['idagent'], 'groupBy', $table);
		$settings_sortBy  = $APP->get_settings($_SESSION['idagent'], 'sortBy', $table);
		$settings_sortDir = $APP->get_settings($_SESSION['idagent'], 'sortDir', $table);
		$settings_nbRows  = $APP->get_settings($_SESSION['idagent'], 'nbRows', $table);

		$id       = 'id' . $table;
		$nom      = 'nom' . ucfirst($table);
		$id_type  = 'id' . $table . '_type';
		$nom_type = 'nom' . ucfirst($table) . '_type';
		$top      = 'estTop' . ucfirst($table);
		$actif    = 'estActif' . ucfirst($table);
		$visible  = 'estVisible' . ucfirst($table);
	else:
		$APP = new App();
	endif;
	//

	$groupBy = !isset($_POST['groupBy']) ? !isset($settings_groupBy) ? '' : $settings_groupBy : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page    = (empty($_POST['page'])) ? 0 : $_POST['page'] - 1;
	$nbRows  = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
	//
?>
<div class="menu_nav flex_h flex_align_middle flex_margin  padding">
	<div class="flex_main" >
		<div style="z-index: 100;overflow:hidden;" class="fauxInput applink  relative"
		     onclick="$(this).next().toggle();">
			<a>
				<i class="fa fa-caret-right"></i>
				&nbsp;
				<?= $table ?> <?= $groupBy ?>
			</a>
		</div>
		<div style="z-index: 3000;display:none;max-height:350px;overflow:auto;width:100%;" class="absolute blanc border4 shadowbox hide_on_click" act_defer mdl="app/app_gui/app_gui_activity_expl"
		     vars="<?= http_build_query($_POST) ?>"></div>
	</div>
	<div>
		<input placeholder="Rechercher" expl_search_button="expl_search_button" style="width:200px;" type="text" class="">
	</div>
</div>
