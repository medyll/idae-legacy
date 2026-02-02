<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/07/14
	 * Time: 04:04
	 * return array of raw data queried
	 * 
	 * MIGRATION NOTE: MongoId/MongoRegex converted to MongoCompat (2026-02-02)
	 */

	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	$_POST = array_merge($_POST,$_GET);
	ini_set('display_errors', 55);
	$table = $_POST['table'];
	if (!empty($_POST['table_value']))
		$_POST['vars']['id' . $table] = $_POST['table_value'];
	// if (!empty($_POST['table_mongoid'])) $_POST['vars']['id' . $table] = $_POST['table_mongoid'];
	$PIECE = !isset($_POST['piece']) ? 'data' : $_POST['piece'];

	$APP      = new App();
	$ARR_SCH  = $APP->get_schemes();
	$ARR_Bool = $APP->get_array_field_bool();
	$ARR_SORT = $APP->get_sort_fields();

	$APP = new App($table);
//	$spy_vars = array('notify' => $table);
//	skelMdl::reloadModule('activity/appActivity', $_SESSION['idagent'], $spy_vars);
	//
	$id       = 'id' . $table;
	$nom      = 'nom' . ucfirst($table);
	$id_type  = 'id' . $table . '_type';
	$nom_type = 'nom' . ucfirst($table) . '_type';

	$prix = 'prix' . ucfirst($table);
	//

	$vars    = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = !isset($_POST['table_groupby']) ? !isset($settings_groupBy) ? '' : $settings_groupBy : $_POST['table_groupby'];
	$sortBy  = empty($_POST['sortBy']) ? empty($settings_sortBy) ? $nom : $settings_sortBy : $_POST['sortBy'];
	$sortDir = empty($_POST['sortDir']) ? empty($settings_sortDir) ? 1 : (int)$settings_sortDir : (int)$_POST['sortDir'];
	$page    = (empty($_POST['page'])) ? 0 : $_POST['page'] - 1;
	$nbRows  = (empty($_POST['nbRows'])) ? empty($settings_nbRows) ? 1000 : (int)$settings_nbRows : $_POST['nbRows'];
	//
	if (!empty($_POST['vars']['_id'])) {
		$vars['_id'] = MongoCompat::toObjectId($_POST['vars']['_id']);
	}
	//
	// vardump($vars);
	$APP_TABLE       = $APP->app_table_one;
	$GRILLE_FK       = $APP->get_grille_fk();
	$HTTP_VARS       = $APP->translate_vars($vars);
	$RS_APP          = $APP->query($vars, 0);
	$APP_DATE_FIELDS = $APP->get_date_fields($table);
	$GRILLE_SORT     = array_keys($APP->get_sort_fields($table));
	$arrFieldsBool   = $APP->get_array_field_bool();
	$APP_FIELD_BOOL  = $APP->get_bool_fields($table);
	// scheme
	if ($PIECE == 'scheme') { //
		foreach ($APP_FIELD_BOOL as $bool => $value):
			$APP_TABLE['grilleBool'][] = ['field_raw' => $bool, 'field' => $value, 'header' => $bool, 'width' => 40]; //"{type:'$bool',field:'$value',header:'" . $bool . "',width:40}";
		endforeach;
		$APP_TABLE['grilleBoolIcon'] = $arrFieldsBool;
		echo trim(json_encode($APP_TABLE));
		exit;
	}
	//
	$where = array();
	if (!empty($_POST['search'])) {
		$search_escaped = MongoCompat::escapeRegex($_POST['search']);
		$regexp         = MongoCompat::toRegex(".*" . $search_escaped . "*.", 'i');
		$where['$or'][] = array($nom => $regexp);
		$where['$or'][] = array($id => (int)$_POST['search']);
		// tourne ds fk
		if (sizeof($GRILLE_FK) != 0) {
			foreach ($GRILLE_FK as $field):
				$nom_fk         = 'nom' . ucfirst($field['table_fk']);
				$regexp         = MongoCompat::toRegex("." . $nom_fk . "*.", 'i');
				$where['$or'][] = array($nom_fk => $regexp);
			endforeach;
		}
	}

//
	$JSON_HEAD        = [];
	$REQUESTED_FIELDS = [];
	$DSP_FIELDS       = [];
	// CONSTRUCTION COLONNES
	$REQUESTED_FIELDS [] = $id;
	foreach ($APP_FIELD_BOOL as $bool => $value):
		$JSON_HEAD[]         = "{field_raw:'$bool',field:'$value',header:'" . $bool . "',width:40}";
		$REQUESTED_FIELDS [] = $value;
		$DSP_FIELDS []       = $value;
	endforeach;
	$JSON_HEAD[]         = "{field_raw:'prod',field:'prod',header:'Prod','width':40}";
	$REQUESTED_FIELDS [] = 'prod';
	$DSP_FIELDS []       = 'prod';
	$JSON_HEAD[]         = "{type:'nom',header: '" . $table . "',width:150}";
	$REQUESTED_FIELDS [] = $nom;
	$DSP_FIELDS []       = $nom;
	foreach ($GRILLE_FK as $fk):
		$JSON_HEAD[]         = "{field_raw:'fk',field:'" . $fk['nomtable_fk'] . "',header: '" . $fk['table_fk'] . "',width:100}";
		$REQUESTED_FIELDS [] = $fk['idtable_fk'];
		$REQUESTED_FIELDS [] = $fk['nomtable_fk'];
		$DSP_FIELDS []       = $fk['nomtable_fk'];
	endforeach;
	if (!empty($APP_TABLE['hasPrixScheme'])):
		$JSON_HEAD[]         = "{field_raw:'prix',field:'$prix',header:'Prix',width:100}";
		$REQUESTED_FIELDS [] = $prix;
		$DSP_FIELDS []       = $prix;
	endif;
	if (!empty($APP_TABLE['hasTypeScheme'])):
		$JSON_HEAD[]         = "{field_raw:'type',field:'$nom_type',header:'Type',width:40}";
		$REQUESTED_FIELDS [] = $id_type;
		$REQUESTED_FIELDS [] = $nom_type;
		$DSP_FIELDS []       = $nom_type;
	endif;
	$json_head = '[' . implode(',', $JSON_HEAD) . ']';
////
	if ($PIECE == 'header') {
		echo $json_head;
		exit;
	}

	$rs = $APP->query($vars + $where);
	$max_count = $rs->count();
	$rs->sort(array($sortBy => $sortDir));
	$rs->limit($nbRows)->skip($page * $nbRows);
//
//
	if ($PIECE == 'table_groupby'):
		$rs_dist    = $APP->distinct($groupBy, $vars + $where);
		$dist_count = $rs_dist->count();
		// $nbRows = (ceil($nbRows / $dist_count));
		$rs_dist->limit(30)->skip($page * 30);
		
		$JSON_STR = []; 
		foreach ($rs_dist as $arr_dist):
		// vardump($arr_dist);
			$vars_rfk = array();
			$vars['id' . $groupBy] = (int)$arr_dist['id' . $groupBy];
			$rs                    = $APP->query($vars + $where)->sort(array($sortBy => $sortDir));
			//
			$vars_rfk['table']       = 'id' . $groupBy; 
			$vars_rfk['vars'] = ['id' . $groupBy => $arr_dist['id' . $groupBy]];
			$vars_rfk['table_value'] = $arr_dist['id' . $groupBy];
			$vars_rfk['table_value_name'] = $arr_dist['nom' . ucfirst($groupBy)];
			$JSON_STR[] = $vars_rfk;
		endforeach;
		echo trim(json_encode($JSON_STR));
		exit;
	endif;

	// CONSTRUCTION DATAS
	$JSON_STR = [];
	while ($arr = $rs->getNext()):
		$JSON_DATA        = array();
		$JSON_DATA['_id'] = (string)$arr['_id'];
		$prod_vars        = Act::decodeVars([$id => (int)$arr['id' . $table]]);
		if (!empty($APP_TABLE['hasProdScheme'])):
			$ct_prod = $APP->plug('sitebase_production', 'produits')->find($prod_vars, ['idproduit' => 1])->count();
		else:
			$ct_prod = 0;
		endif;
		$JSON_DATA[$id] = (int)$arr['id' . $table];
		foreach ($arrFieldsBool as $bool => $arr_ico):
			$set_value        = empty($arr[$bool . ucfirst($table)]);
			$css              = empty($arr[$bool . ucfirst($table)]) ? 'textgris' : '';
			$name             = $bool . ucfirst($table);
			$uri              = "table=$table&table_value=$arr[$id]&vars[$name]=$set_value&scope=$id";
			$JSON_DATA[$name] = $arr[$name];
		endforeach;
		$JSON_DATA['prod'] = $ct_prod;
		$JSON_DATA[$nom]   = $arr[$nom];
		foreach ($GRILLE_FK as $field):
			$code  = $BASE_APP . $table . $field['table_fk'] . $arr[$id];
			$id_fk = $field['idtable_fk'];
			//
			//if (!empty($field['idtable_fk']) && empty($arr['nom' . ucfirst($field['table_fk'])])):
			$arrq                             = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => (int)$arr[$id_fk]], [$field['idtable_fk'] => 1, $field['nomtable_fk'] => 1]);
			$dsp_name                         = $arrq['nom' . ucfirst($field['table_fk'])];
			$JSON_DATA[$field['nomtable_fk']] = $dsp_name;

		endforeach;
		if (!empty($APP_TABLE['hasPrixScheme'])):
			$JSON_DATA['prix' . ucfirst($table)] = $arr['prix' . ucfirst($table)];
		endif;
		if (!empty($APP_TABLE['hasTypeScheme'])):
			$JSON_DATA[$nom_type] = $arr[$nom_type];
		endif;
		//			echo implode(',', $JSON_DATA)."<br>";exit;
		$JSON_DATA['description' . ucfirst($table)] = $arr['description' . ucfirst($table)];
		$JSON_DATA['petitNom' . ucfirst($table)]    = $arr['petitNom' . ucfirst($table)];
		$JSON_DATA['code' . ucfirst($table)]        = $arr['code' . ucfirst($table)];
		
		$JSON_DATA['nom']         = $arr[$nom];
		$JSON_DATA['description'] = $arr['description' . ucfirst($table)];
		$JSON_DATA['petitNom']    = $arr['petitNom' . ucfirst($table)];
		$JSON_DATA['code']        = $arr['code' . ucfirst($table)];
		$JSON_DATA['linkSrc_mini']   = Act::imageSite($table, $arr[$id], 'mini');
		$JSON_DATA['linkSrc_tiny']   = Act::imageSite($table, $arr[$id], 'tiny');
		$JSON_DATA['linkSrc_squary'] = Act::imageSite($table, $arr[$id], 'squary');
		$JSON_DATA['linkSrc_small']  = Act::imageSite($table, $arr[$id], 'small');
		$JSON_DATA['linkSrc_long']   = Act::imageSite($table, $arr[$id], 'long');
		$JSON_DATA['linkSrc_large']  = Act::imageSite($table, $arr[$id], 'large');
		$JSON_DATA['linkSrc_reflect']  = Act::imageSite($table, $arr[$id], 'large','reflect');
			
		$JSON_STR[]                                 = $JSON_DATA;

	endwhile;
	if ($PIECE == 'data') {
		echo trim(json_encode($JSON_STR));
		exit;
	}
	if ($PIECE == 'query') {
		$q['count']=$rs->count();
		$q['maxcount']=$max_count;
		$q['rs']= $JSON_STR;
		echo  trim(json_encode($q));
		exit;
	}