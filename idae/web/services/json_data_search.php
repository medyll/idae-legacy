<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;

	ini_set('display_errors', 55);
	$_POST = array_merge($_GET, $_POST);

	if (empty($_POST['search'])) {
		return;
	}
	if (!empty($_POST['stream_to'])) {
		if (!empty($_POST['url_data'])) {
			$_POST['url_data'] .= '&stream_to=' . $_POST['stream_to'];
		}
	}
	if (!empty($_POST['url_data'])) {
		parse_str($_POST['url_data'], $_POST);
	}
	$uniqid = uniqid();
	//
	$APP = new App('appscheme');
	//
	$RSSCHEME = $APP->find([]); // 'codeAppscheme_base'=>'sitebase_base'
	//
	$vars    = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo(array_filter($_POST['vars']), 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	$sortBy  = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page    = (!isset($_POST['page'])) ? 0 : $_POST['page'];
	$nbRows  = 25; //(empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 250 : (int)$settings_nbRows : $_POST['nbRows'];
	//  vars_date
	if (!empty($_POST['vars_date'])):
		$key_date        = $_POST['vars_date'] ['name_key'];
		$vars[$key_date] = $_POST['vars_date'][$key_date];
	endif;

	$APP_DATE_FIELDS = $APP->get_date_fields();
	$GRILLE_SORT     = array_keys($APP->get_sort_fields());
	$arrFieldsBool   = $APP->get_array_field_bool();
	$APP_FIELD_BOOL  = $APP->get_bool_fields();
	//
	$FIELDS = array_merge(array_keys($APP_DATE_FIELDS), array_values($APP_FIELD_BOOL));
	//

	// MAIN_DATA

	$data_main = [];
	$strm      = [];

	$i      = 0;
	$nb_res = 0;

	$SEARCH = trim($_POST['search']);

	$search_escaped  = MongoCompat::escapeRegex($SEARCH);
	$RSSCHEME_SEARCH = $APP->find(['codeAppscheme' => MongoCompat::toRegex($search_escaped, 'i')]);
	$maxcount        = $RSSCHEME_SEARCH->count();
	$count           = $RSSCHEME_SEARCH->count(true);

	//
	if ($RSSCHEME_SEARCH->count() != 0) {
		$data_main[] = ['groupBy' => 'appscheme', 'html' => '<i class="fa fa-link"></i> Espaces'];

	}
	while ($ARR_SCh = $RSSCHEME_SEARCH->getNext()) {
		$table = $ARR_SCh['codeAppscheme'];
		$nom   = $ARR_SCh['nomAppscheme'];
		$icon  = $ARR_SCh['iconAppscheme'];
		$color = $ARR_SCh['colorAppscheme'];

		if (!droit_table($_SESSION['idagent'], 'L', $table)) continue;
		if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;

		$in = '<div><a class="textgrisfonce">Espace ' . $nom . '</a></div>';
		//if (droit_table($_SESSION['idagent'], 'C', $table)) $in .= '<div><a class="textgris" onclick="' . fonctionsJs::app_create($table) . '">Créer ' . $nom . '</a></div>';

		$data_out['nom']    = '<div onclick="' . fonctionsJs::app_explorer($table) . '" class="shadowbox hide_gui_pane cursor applink flex_h marginb padding edededhover"><div class="aligncenter padding margin ededed border4" style="width:46px;border-color: ' . $color . '!important;"><i class="textbold fa fa-' . $icon . ' fa-2x"></i></div><div><span class="titre1 borderb padding" >' . $nom . '</span>' . $in . '</div></div>';
		$data_out['nom_fk'] = '';

		$data_main[] = ['html' => $data_out, 'value' => '$arr[$id]', 'name_id' => '$id', 'table' => 'appscheme'];

		$out_model = ['data_main' => $data_main, 'maxcount' => $maxcount];
		$strm_vars = ['stream_to' => $_POST['stream_to'], 'data' => $out_model, 'data_size' => sizeof($data_main)];
		skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)), session_id());
		$data_main = [];

	}

	foreach ($RSSCHEME as $arr_dist):
		//
		$table = $arr_dist['codeAppscheme'];
		if ($APP->get_settings($_SESSION['idagent'], 'app_search_' . $table) != 'true') continue;
		if (!droit_table($_SESSION['idagent'], 'R', $table)) continue;

		//
		$out       = [];
		$APPSC     = new App($table);
		$Idae      = new Idae($table);
		$GRILLE_FK = $APPSC->get_grille_fk();
		$APP_TABLE = $APPSC->app_table_one;
		$i++;
		//
		$color     = $APPSC->colorAppscheme;
		$Table     = ucfirst($table);
		$id        = 'id' . $table;
		$nom       = 'nom' . $Table;
		$prenom    = 'prenom' . $Table;
		$email     = 'email' . $Table;
		$code      = 'code' . $Table;
		$telephone = 'telephone' . $Table;
		$icon      = $arr_dist['iconAppscheme'];
		$icon_css  = '<i class="padding textgris fa fa-' . $icon . '" style="color:' . $color . '!important;"></i>'; //
		$where     = [];

		if (!empty($_POST['search'])) {
			if (!is_int($_POST['search'])):
				$regexp = MongoCompat::toRegex($search_escaped, 'i');

				if (!empty($APP_TABLE['hasAdresseScheme'])) $where['$or'][] = ['ville' . $Table => $regexp];

				if ($APPSC->has_field('nom')) $where['$or'][] = [$nom => $regexp];
				if ($APPSC->has_field('prenom')) $where['$or'][] = [$prenom => $regexp];
				if ($APPSC->has_field('email')) $where['$or'][] = [$email => $regexp];
				if ($APPSC->has_field('code')) $where['$or'][] = [$code => $regexp];
				if ($APPSC->has_field('telephone')) $where['$or'][] = [$telephone => $regexp];
				$where['$or'][] = [$id => (int)$_POST['search']];
			else :
				$where = [$id => (int)$_POST['search']];
			endif;
		}

		if ($APPSC->has_agent() && !droit_table($_SESSION['idagent'], 'CONF', $table)) {
			$where['idagent'] = (int)$_SESSION['idagent'];
		};

		$rssc       = $APPSC->query([$id => ['$ne' => 0]] + $vars + $where, (int)$page, (int)$nbRows);
		$rssc_count = $rssc->count();

		if ($rssc->count() != 0):
			$rss_html = "<div   class='flex_h flex_align_middle'>$icon_css<div class='flex_main'><span class='bold padding'> " . ucfirst($arr_dist['nomAppscheme']) . "</span></div>$rssc_count <i class='fa fa-angle-double-left'></i> </div>";

			$data_main[] = $strm[] = ['groupBy' => $table, 'html' => $rss_html];
		endif;

		while ($arr = $rssc->getNext()) {
			// sleep(1);
			$i++;
			$nb_res++;
			$data_out = [];
			//
			$data_out['chk'] = '<input type = "checkbox" value = "' . $arr[$id] . '" name = "id[]" />';
			$data_out[$id]   = $arr[$id];

			$name = ucfirst(strtolower($arr[$nom] . ' ' . $arr[$prenom]));

			$data_out['nom_fk']       = [];
			$data_out['nom_fk_large'] = [];

			foreach ($GRILLE_FK as $field):
				$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
				$id_fk = $field['idtable_fk'];
				//
				$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
				$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
				//
				if (!empty($dsp_name)) {
					$data_out['nom_fk'][]       = "<a class='textgris'>" . strtolower($dsp_name) . "</a>";
					$data_out['nom_fk_large'][] = "<i class='textgris   fa fa-" . $field['iconAppscheme'] . "'></i><div class='flex_main'><div ><span class='textgrisfonce'>" . ucfirst($field['nomAppscheme']) . "</span></div><div>" . $dsp_name . "</div></div>";
				}
			endforeach;

			$onclick = fonctionsJs::app_fiche($table, $arr[$id]);

			// $data_out['nom'] = skelMdl::cf_module('app/app/app_fiche_search',['table'=>$table,'table_value'=>]);

			if ($rssc_count <= 4) {
				$in = '<div class="textbold" >' . $table . '</div>';

				$data_out['nom']    = $Idae->module('app/app/app_fiche_mini', ['table' => $table, 'table_value' => $arr[$id]]);
				$data_out['nom_fk'] = '';
			} elseif ($rssc_count <= 10) {
				$in = '<div class="textgris" >' . $table . '</div>';
				$in .= implode(', ', $data_out['nom_fk']);
				$data_out['nom']    = '<div onclick="' . $onclick . '" class="flex_h marginb edededhover"><div class="aligncenter padding" style="width:46px;"><i class="textbold padding border4 fa fa-' . $icon . ' fa-2x"></i></div><div><span class="titre1" >' . $name . '</span>' . $in . '</div></div>';
				$data_out['nom_fk'] = '';
			} else {
				$data_out['nom']    = '<a class="retrait" act_chrome_gui = "app/app/app_fiche" vars = "table=' . $table . '&table_value=' . $arr[$id] . '" scope = "id' . $table . '" options = "{ident:\'id' . $table . '\',value:\'' . $arr[$id] . '\',scope:\'id' . $table . '\'}" >' . $name . '</a>';
				$data_out['nom_fk'] = implode(', ', $data_out['nom_fk']);
			}

			if ($rssc_count == 1) {
			} else {

			}

			$data_main[] = ['html' => $data_out, 'value' => $arr[$id], 'name_id' => $id, 'table' => $table];
			$strm[]      = ['html' => $data_out, 'value' => $arr[$id], 'name_id' => $id, 'table' => $table];

			// stream
			if ($i == 1 || ($i % 50) == 0 || !$rssc->hasNext()) {
				if (!empty($_POST['stream_to'])):
					$out_model = ['data_main' => $strm, 'maxcount' => $maxcount];
					$strm_vars = ['stream_to' => $_POST['stream_to'], 'data' => $out_model, 'data_size' => sizeof($strm)];
					skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)), session_id());
					$strm = [];
				endif;
			}

		}
		//
	endforeach;

	if ($nb_res == 0):
		$data_main[] = ['groupBy' => 'fin ...', 'html' => '<div class="aligncenter padding ededed border4">
															<i class="fa fa-frown-o fa-2x textorange"></i>
															<br>Aucun résultat pour<br>"' . $_POST['search'] . '"'
			. '<div class="textvert padding bordert aligncenter">' . skelMdl::cf_module('app/app_gui/app_gui_tile_user', ['code' => 'app_search', 'moduleTag' => 'span'])
			. '<div class="padding bordert">Elargir la recherche</div>
															</div>
															</div>'];

		$out_model = ['columnModel' => $columnModel, 'data_main' => $data_main, 'maxcount' => $maxcount];
		$strm_vars = ['stream_to' => $_POST['stream_to'], 'data' => $out_model, 'data_size' => sizeof($strm)];
		skelMdl::send_cmd('act_stream_to', json_decode(json_encode($strm_vars)), session_id());

	endif;
	if ($nb_res != 0):
		$APP_SA            = new APP('agent_recherche');
		$idagent_recherche = $APP_SA->create_update(['quantiteAgent_recherche' => $nb_res, 'codeAgent_recherche' => $_POST['search']], ['dateCreationAgent_recherche' => date('Y-m-d'), 'heureCreationAgent_recherche' => date('H:i:s'), 'timeAgent_recherche' => time(), 'nomAgent_recherche' => $_POST['search'], 'idagent' => (int)$_SESSION['idagent']]);
		$APP_SA->update_inc(['idagent_recherche' => $idagent_recherche], 'valeurAgent_recherche');
		skelMdl::reloadModule('app/app_gui/app_gui_start_search_last', $_SESSION['idagent']);
	endif;
	//
	$out_model = ['columnModel' => $columnModel, 'data_main' => $data_main, 'maxcount' => $maxcount];
	//
	if (empty($_POST['stream_to'])):
		echo trim(json_encode($out_model));
	endif;

	function dotr($arr) {

	}