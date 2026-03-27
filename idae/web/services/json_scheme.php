<?php
declare(strict_types=1);
/**
 * json_scheme.php — Build and return appscheme JSON models consumed by the frontend SPA.
 * Assembles fieldModel, columnModel, miniModel, defaultModel, hasModel for window.APP.APPSCHEMES.
 *
 * Date: 07/07/14
 * Modified: 2026-03-15 — strict_types, exit→return, English comments
 */

include_once($_SERVER['CONF_INC']);

$_POST = array_merge($_GET, $_POST);

	$APP                 = new App();
	$APP_SCH             = new App('appscheme');
	$APP_FIELD           = new App('appscheme_field');
	$APP_HAS_FIELD       = new App('appscheme_has_field');
	$APP_HAS_TABLE_FIELD = new App('appscheme_has_table_field');

	$PIECE = !isset($_POST['piece']) ? 'scheme' : $_POST['piece'];

	$vars          = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$RS_APP        = $APP_SCH->find($vars)->sort(['codeAppscheme' => 1]);
	$COLLECT       = [];
	foreach ($RS_APP as $ARR_APP):
		$idappscheme = (int)$ARR_APP['idappscheme'];
		$table       = $ARR_APP['codeAppscheme'];
		$Table       = ucfirst($ARR_APP['codeAppscheme']);

		// All fields bound to this entity
		$RS_HAS_FIELD       = $APP_HAS_FIELD->find(['idappscheme' => (int)$idappscheme]);
		// Fields shown in the table grid, ordered by display position
		$RS_HAS_TABLE_FIELD = $APP_HAS_TABLE_FIELD->find(['idappscheme' => (int)$idappscheme])->sort(['ordreAppscheme_has_table_field' => 1]);
		// Fields shown in the mini-fiche panel
		$RS_HAS_MINI_FIELD  = $APP_HAS_FIELD->find(['idappscheme' => (int)$idappscheme, 'in_mini_fiche' => 1])->sort(['ordreAppscheme_has_table_field' => 1]);

		$APP          = new App($table);
		$APP_TABLE    = $APP->app_table_one;
		$GRILLE_FK    = $APP->get_grille_fk();
		$GRILLE_COUNT = $APP->get_grille_count($table);

		$arrFields = $APP->get_basic_fields_nude($table);

		$fieldModel = [];
		foreach ($RS_HAS_FIELD as $ARR_HAS_FIELD): // all fields declared in the schema
			$ARR_FIELD    = $APP_FIELD->findOne(['idappscheme_field' => (int)$ARR_HAS_FIELD['idappscheme_field']]);
			$fieldModel[] = ['field_name'       => $ARR_FIELD['codeAppscheme_field'] . $Table,
			                 'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'],
			                 'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'],
			                 'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'],
			                 'icon'             => $ARR_FIELD['iconAppscheme_field'],
			                 'title'            => $ARR_FIELD['nomAppscheme_field']];
		endforeach;
		$miniModel = [];
		foreach ($RS_HAS_MINI_FIELD as $ARR_HAS_MINI_FIELD): // all fields declared in the mini-fiche schema
			$ARR_FIELD = $APP_FIELD->findOne(['idappscheme_field' => (int)$ARR_HAS_MINI_FIELD['idappscheme_field']]);
			//
			switch ($ARR_FIELD['codeAppscheme_field_type']):
				case "date":
					$css = 'date_field';
					break;
				case "heure":
					$css = 'heure_field';
					break;
				case "color":
					$css = 'color_field';
					break;
				default:
					if (empty($ARR_FIELD['codeAppscheme_field_type'])) {
						$css = 'fk ';
					} else {
						$css = 'css_field_' . $ARR_FIELD['codeAppscheme_field_type'];
					}

					break;
			endswitch;
			//
			$miniModel[] = ['field_name'       => $ARR_FIELD['codeAppscheme_field'] . $Table,
			                'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'],
			                'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'],
			                'className'        => $css,
			                'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'],
			                'icon'             => $ARR_FIELD['iconAppscheme_field'],
			                'title'            => $ARR_FIELD['nomAppscheme_field']];
		endforeach;
		//
		$columnModel   = [];
		$default_model = [];
		$hasModel      = [];
		$updateModel   = [];

		// default_model: user-customisable table view built from appscheme_has_table_field
		foreach ($RS_HAS_TABLE_FIELD as $ARR_HAS_TABLE_FIELD): // all fields declared in has_table_field
			$ARR_SCH_FIELD = $APP_SCH->findOne(['idappscheme' => (int)$ARR_HAS_TABLE_FIELD['idappscheme_link']]);
			$ARR_FIELD     = $APP_FIELD->findOne(['idappscheme_field' => (int)$ARR_HAS_TABLE_FIELD['idappscheme_field']]);
			$ARR_HAS_FIELD = $APP_HAS_FIELD->findOne(['idappscheme_field' => (int)$ARR_HAS_TABLE_FIELD['idappscheme_field']]);

			$DA_TABLE      = $ARR_SCH_FIELD['codeAppscheme'];
			$DA_TABLE_NANE = $ARR_SCH_FIELD['nomAppscheme'];
			$title         = ($ARR_HAS_TABLE_FIELD['idappscheme'] == $ARR_HAS_TABLE_FIELD['idappscheme_link']) ? $ARR_FIELD['nomAppscheme_field'] : $ARR_FIELD['nomAppscheme_field'] . ' ' . $DA_TABLE_NANE;
			switch ($ARR_FIELD['codeAppscheme_field_type']):
				case "date":
					$css = 'date_field';
					break;
				case "heure":
					$css = 'heure_field';
					break;
				case "color":
					$css = 'color_field';
					break;
				default:
					if (empty($ARR_FIELD['codeAppscheme_field_type'])) {
						$css = 'fk ';
					} else {
						$css = 'css_field_' . $ARR_FIELD['codeAppscheme_field_type'];
					}

					break;
			endswitch;
			if ($ARR_FIELD['codeAppscheme_field'] == 'nom' && $DA_TABLE == $table) $css = 'main_field';
			if ($ARR_FIELD['codeAppscheme_field'] == 'nom' && $DA_TABLE != $table) $css = 'name_field';

			$default_model[] = ['field_name'       => $ARR_FIELD['codeAppscheme_field'] . ucfirst($DA_TABLE),
			                    'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'],
			                    'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'],
			                    'title'            => $title,
			                    'className'        => $css,
			                    'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'],
			                    'icon'             => $ARR_FIELD['iconAppscheme_field']];
			if ($ARR_FIELD['codeAppscheme_field'] == 'adresse'):
				$default_model[] = ['field_name' => 'codePostal' . ucfirst($DA_TABLE), 'field_name_raw' => 'codePostal', 'title' => idioma('code postal')];
				$default_model[] = ['field_name' => 'ville' . ucfirst($DA_TABLE), 'field_name_raw' => 'ville', 'title' => idioma('ville')];
			endif;
		endforeach;

		if (!empty($APP_TABLE['hasTypeScheme'])):
			$type          = 'type';
			$table_type    = $table . '_' . $type;
			$Table_type    = ucfirst($table_type);
			$columnModel[] = ['field_name' => 'nom' . $Table_type, 'field_name_raw' => 'nom' . $Table_type, 'title' => $Table_type, 'width' => ''];

		endif;
		// columnModel: default table columns — code/identification fields + FK columns only
		foreach ($RS_HAS_FIELD as $ARR_HAS_FIELD):
			$ARR_FIELD = $APP_FIELD->findOne(['idappscheme_field' => (int)$ARR_HAS_FIELD['idappscheme_field']]);
			if ($ARR_FIELD['codeAppscheme_field_group'] == 'codification' || $ARR_FIELD['codeAppscheme_field_group'] == 'identification') {
				$columnModel[] = ['field_name'       => $ARR_FIELD['codeAppscheme_field'] . $Table,
				                  'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'],
				                  'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'],
				                  'title'            => idioma($ARR_FIELD['nomAppscheme_field']),
				                  'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'],
				                  'icon'             => $ARR_FIELD['iconAppscheme_field']];
				$hasModel[]    = ['field_name'       => $ARR_FIELD['codeAppscheme_field'] . $Table,
				                  'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'],
				                  'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'],
				                  'title'            => idioma($ARR_FIELD['nomAppscheme_field']),
				                  'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'],
				                  'icon'             => $ARR_FIELD['iconAppscheme_field']];
			};
		endforeach;

		foreach ($GRILLE_FK as $fk):
			$columnModel[] = ['field_name_group' => '', 'field_name' => 'nom' . ucfirst($fk['table_fk']), 'field_name_raw' => $fk['table_fk'], 'title' => $fk['table_fk'], 'className' => 'fk', 'icon' => $fk['icon_fk']];
		endforeach;
		foreach ($GRILLE_COUNT as $key_count => $fk):
			$columnModel[]   = ['field_name_group' => '', 'field_name' => 'count_' . $key_count, 'field_name_raw' => 'count_' . $key_count, 'title' => 'nb ' . $key_count, 'className' => 'nb_field', 'icon' => ''];
			$default_model[] = ['field_name_group' => '', 'field_name' => 'count_' . $key_count, 'field_name_raw' => 'count_' . $key_count, 'title' => 'nb ' . $key_count, 'className' => 'nb_field', 'icon' => ''];
		endforeach;
		if (!empty($APP_TABLE['hasCodeScheme'])):
			$updateModel[] = ['field_name' => 'code' . $Table, 'field_name_raw' => 'code', 'title' => idioma('code')];
		endif;
		if (!empty($APP_TABLE['hasOrdreScheme'])):
			$updateModel[] = ['field_name' => 'ordre' . $Table, 'field_name_raw' => 'ordre', 'title' => idioma('ordre')];
		endif;
		if (!empty($APP_TABLE['hasRangeScheme'])):
			$updateModel[] = ['field_name' => 'range' . $Table, 'field_name_raw' => 'range', 'title' => idioma('rang')];
		endif;

		$APP_TABLE['columnModel']  = $columnModel;   // default grid: nom/code/reference + FK columns
		$APP_TABLE['defaultModel'] = $default_model; // user-customisable view
		$APP_TABLE['hasModel']     = $hasModel;      // identification fields only, no FK
		$APP_TABLE['fieldModel']   = $fieldModel;    // all declared fields
		$APP_TABLE['miniModel']    = $miniModel;     // mini-fiche fields

		//
		$COLLECT[] = $APP_TABLE;
	endforeach;

	if ($PIECE == 'scheme'):
		echo trim(json_encode($COLLECT));
		return;
	endif;
	if ($PIECE == 'fields'):
		echo trim(json_encode($arrFields));
		return;
	endif;
	if ($PIECE == 'bool_fields'):
		echo trim(json_encode(array_keys($APP->get_bool_fields())));
		return;
	endif;
	if ($PIECE == 'bool_fields_icon'):
		echo trim(json_encode($APP->get_array_field_bool()));
		return;
	endif;