<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
$time=time();
	$uniqid = uniqid();
	array_walk_recursive($_POST, 'CleanStr', $_POST);
	//
	if (empty($_POST['table']) && empty($_POST['search'])):
		// echo skelMdl::cf_module('app/app_liste/app_liste_home', $_POST);

		return;
	elseif (empty($_POST['table']) && !empty($_POST['search'])):
		echo skelMdl::cf_module('app/app_liste_search_all', $_POST);

		return;
	elseif (!empty($_POST['img_zone'])):

	endif;

	$table = $_POST['table'];
	//
	$APP = new App($table);
	//
	$settings_groupBy = $APP->get_settings($_SESSION['idagent'], 'groupBy', $table);
	$settings_sortBy = $APP->get_settings($_SESSION['idagent'], 'sortBy', $table);
	$settings_sortDir = $APP->get_settings($_SESSION['idagent'], 'sortDir', $table);
	$settings_nbRows = $APP->get_settings($_SESSION['idagent'], 'nbRows', $table);

	$vars = $_POST['vars'] = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars'], 'strlen'), 1);

	$groupBy = !isset($_POST['groupBy']) ? !isset($settings_groupBy) ? '' : $settings_groupBy : $_POST['groupBy'];
	$sortBy = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page = (empty($_POST['page'])) ? 0 : $_POST['page'] - 1;
	$nbRows = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
	// SETTINGS
	$APP->set_settings($_SESSION['idagent'], ['sortBy_' . $table => $sortBy, 'sortDir_' . $table => $sortDir, 'groupBy_' . $table => $groupBy, 'nbRows_' . $table => $nbRows]);

	$id = 'id' . $table;
	$id_type = 'id' . $table.'_type';
	$rs = $APP->query($vars, (int)$page, (int)$nbRows, array($id => 1));
	$max_count = $rs->count();
	$total = $rs->count();
	$nbPage = ceil($total / $nbRows);



	$vars_hist = $_POST;
	unset($vars_hist['sortBy'],$vars_hist['sortDir'],$vars_hist['sortDir']);
	$uid = md5(http_build_query($vars_hist));
	$APP->set_hist($_SESSION['idagent'], ['uid'=>$uid]+$vars_hist);

?>
<div id="app_<?= $uniqid ?>"
     style="overflow:hidden;position: relative;height:100%;">
	<div
	     style="overflow:auto;height:100%;">
		<div class="table">
			<div class="cell"><div class="relative"
			                       act_drag_selection_zone>
					<?= skelMdl::cf_module('app/app_liste_table', $_POST + ['scope' => 'scope_prod_liste', "defer" => true], $uniqid) ?></div></div>
		</div>
	</div>
	<div class="stayDown absolute blanc bordert"
	     style="bottom:0;width: 100%;">
		<div class="table tablemiddle">
			<div class="cell">
				<div class="titre_entete">
					<li class="fa fa-sign-in"></li>
					<?= $max_count . ' ' . $table . '' . (($max_count > 0) ? '' : 's') ?>
				</div>
			</div>
			<div class="cell aligncenter"
			     style="width: 90px;">
				<input type="text"
				       class="inputTiny avoid noborder alignright"
				       value="<?= $nbRows ?>"
				       onchange="reloadScope('scope_prod_liste','<?= $uniqid ?>','nbRows='+$(this).value)"> /page
			</div>
			<? if ($nbPage > 1) { ?>
				<div class="cell applink">

					<div class="titre_entete toggler">
						Page
						<output style="vertical-align: middle;" for="range" id="output"><?= $page ?></output>
						<input style="vertical-align: middle;" type="range" id="range" name="range" min="0"
						       max="<?= $nbPage ?>" value="<?= $page ?>"
						       onchange="$('output').update(this.value);$('vaildpg').show();">
						<a id="vaildpg" style="display:none;"
						   onclick="reloadScope('scope_prod_liste','<?= $uniqid ?>','page='+$('output').value);$(this).fade();">
							<li class="fa fa-sign-out"></li>
						</a>
					</div>
				</div>
			<? } ?>
			<div class="cell">

			</div>
		</div>
	</div>
</div>
<style>
	input[type='range'] {
		-webkit-appearance: none;
		border-radius: 5px;
		box-shadow: inset 0 0 5px #333;
		background-color: #999;
		height: 10px;
		vertical-align: middle;
	}

	input[type='range']::-moz-range-track {
		-moz-appearance: none;
		border-radius: 5px;
		box-shadow: inset 0 0 5px #333;
		background-color: #999;
		height: 10px;
		vertical-align: middle;
	}

	input[type='range']::-moz-range-thumb, input[type='range']::-webkit-slider-thumb {
		-moz-appearance: none;
		-webkit-appearance: none;
		border-radius: 20px;
		background-color: #FFF;
		box-shadow: inset 0 0 10px rgba(000, 000, 000, 0.5);
		border: 1px solid #999;
		height: 20px;
		width: 20px;
	}
</style>