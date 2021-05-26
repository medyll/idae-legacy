<?
	include_once($_SERVER['CONF_INC']);

	if (empty($_POST['table_value'])) {
		echo skelMdl::cf_module('app/app_contextual/scheme_contextual' , ['table' => $_POST['table']]);;

		return;
	}
?>
<div class="blanc">
	<?
		$table = str_replace('id', '', $_POST['table']);
		$Table = ucfirst($table);
		if ($table == 'agent_tuile' || $table == 'agent_activite' || $table == 'agent_history'):
			$APP                  = new App($table);
			$ARR                  = $APP->query_one(['id' . $table => (int)$_POST['table_value']]);
			$table                = $ARR['code' . $Table];
			$_POST['table_value'] = (int)$ARR['valeur' . $Table];
			$Table                = ucfirst($table);
		endif;
		//
		$APPSC_HAS_FIELD   = new App('appscheme_has_field');
		$APPSC_FIELD       = new App('appscheme_field');
		$APP         = new App($table);
		$TABLE_ONE   = $APP->app_table_one;
		$idappscheme = (int)$APP->idappscheme;

		$GRILLE_FK   = $APP->get_grille_fk();
		$R_FK        = $APP->get_reverse_grille_fk($table, $table_value);
		$table_value = (int)$_POST['table_value'];
		$act_from    = empty($_POST['act_from']) ? '' : $_POST['act_from'];
		$id          = 'id' . $table;
		$nom         = 'nom' . ucfirst($table);
		$arr         = $APP->query_one([$id => $table_value]);
		//
		$ARR_DIST  = $APPSC_HAS_FIELD->distinct('appscheme_field', ['idappscheme' => $idappscheme], 346, 'no_full', 'idappscheme_field');
		$HAS_FIELD = $APPSC_FIELD->distinct('appscheme_field', ['idappscheme_field' => ['$in' => $ARR_DIST]], 346, 'no_full', 'codeAppscheme_field');
	?>
</div>
<div class="applink applinkblock toggler relative hide_gui_pane" style="width:250px;" >
	<?= skelMdl::cf_module('business/' . BUSINESS . '/app/app_contextual/' . $table, [$id => $table_value, 'table' => $table, 'table_value' => $table_value]); ?>
	<?= skelMdl::cf_module('app/app_contextual/' . $table, [$id => $table_value, 'table' => $table, 'table_value' => $table_value]); ?>
	<? if (droit_table($_SESSION['idagent'], 'R', $table)):
		?>
		<? if ($table == 'document') { ?>
		<a class="borderb" onclick="<?= fonctionsJs::app_fiche_preview($table, $table_value) ?>">
			<i class="fa fa-eye"></i>
			<?= idioma('Visualiser document') ?>
		</a>
	<? } ?>
		<a onclick="<?= fonctionsJs::app_fiche($table, $table_value) ?>">
			<i class="fa fa-file-text-o  ms-fontColor-purple"></i>
			<?= idioma('Fiche') . ' ' . coupeChaine($arr[$nom], 15) ?>
		</a>
		<? if (sizeof($GRILLE_FK) > 1) { ?>
	<a onclick="<?= fonctionsJs::app_fiche_maxi($table, $table_value) ?>">
		<i class="fa fa-columns  ms-fontColor-blue"></i>
		<?= idioma('Synthese') . ' ' . coupeChaine($arr[$nom], 15) ?>
		</a><? } ?>
	<? endif; ?>
	<? if (droit_table($_SESSION['idagent'], 'U', $table)):
		if (($APP->has_agent() && $arr['idagent'] == $_SESSION['idagent']) || (droit_table($_SESSION['idagent'], 'CONF', $table) || droit_table($_SESSION['idagent'], 'U', $table))):
			?>
			<a onclick="<?= fonctionsJs::app_update($table, $table_value) ?>">
				<i class="fa fa-pencil ms-fontColor-success"></i>
				<?= idioma('Modifier') ?>
			</a>
		<? endif;
	endif;
		if (droit_table($_SESSION['idagent'], 'C', $table)):
			if (($APP->has_agent() && $arr['idagent'] == $_SESSION['idagent']) || (droit_table($_SESSION['idagent'], 'CONF', $table) || droit_table($_SESSION['idagent'], 'C', $table))):
				?>
				<a onclick="<?= fonctionsJs::app_duplique($table, $table_value) ?>">
					<i class="fa fa-copy ms-fontColor-magenta"></i>
					<?= idioma('Dupliquer') ?>
				</a>
			<? endif; ?>
		<? endif; ?>
	<? if ($TABLE_ONE['hasImageScheme']): ?>
		<a act_chrome_gui="app/app_img/image_app_liste_img" vars="table=<?= $table ?>&table_value=<?= $table_value ?>" options="{ident:'img_<?= $table . $table_value ?>'}">
			<i class="fa fa-file-image-o  ms-fontColor-orangeLight"></i>
			<?= idioma('Images') ?>
		</a>
	<? endif; ?>
	<? if (sizeof($GRILLE_FK) != 0) { ?>
		<? foreach ($GRILLE_FK as $field):
			$id       = 'id' . $field;
			// query for name
			$arrd     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $arr[$field['idtable_fk']]]);
			$dsp_name = $arrd['nom' . ucfirst($field['table_fk'])];
			if (empty($dsp_name)) continue;
			?>
			<a class="ellipsis" title="<?= $field['table_fk'] ?>" onclick="<?= fonctionsJs::app_fiche($field['table_fk'], $arr[$field['idtable_fk']]) ?>">
				<i class="fa fa-<?= $field['icon_fk'] ?>"></i>
				<?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?></a>
		<? endforeach; ?>
	<? } ?>
	<? if ($table=='secteur'): ?>
		<a onclick="<?= fonctionsJs::app_mdl('app/app_custom/app_custom_map_zone',['table'=>$table,'table_value'=>$table_value], '') ?>"  >
			<i class="fa fa-map-pin"></i>
			&nbsp;
			<?= idioma('Geolocalisation zone') ?>
		</a>
	<? endif; ?>
	<? if (in_array('gpsData', $HAS_FIELD)): ?>
		<a onclick="<?= fonctionsJs::app_mdl('app/app_custom/app_custom_map',['table'=>$table,'table_value'=>$table_value], '') ?>"  >
			<i class="fa fa-map-pin"></i>
			&nbsp;
			<?= idioma('Geolocalisation') ?>
		</a>
	<? endif; ?>
	<? if (sizeof($R_FK) != 0) { ?>
		<hr>
		<div class="applinkblock" style="overflow:hidden;"><?= skelMdl::cf_module('app/app/app_fiche_rfk', ['act_chrome_gui' => 'app/app_liste/app_liste_gui', 'table' => $table, 'table_value' => $table_value]) ?></div>
	<? } ?>
	<? if (droit_table($_SESSION['idagent'], 'D', $table)): ?>
		<hr>
		<a onclick="<?= fonctionsJs::app_delete($table, $table_value) ?>">
			<i class="fa fa-times textrouge"></i><?= idioma('Supprimer') ?>
		</a>
	<? endif; ?>
	<? if (droit('DEV') && $table != 'appscheme'): ?>
		<hr>
		<div class="margin ededed border4 padding">
			<a onclick="act_chrome_gui('app/app_scheme/app_scheme_has_field_update_model','idappscheme=<?= $idappscheme ?>')">
				<i class="fa fa-user-secret"></i> <?= idioma('Modele') ?>
			</a>
			<a onclick="ajaxInMdl('app/app_scheme/app_scheme_has_field_update','div_redf','idappscheme=<?= $idappscheme ?>',{onglet:'Choix des champs de table'})">
				<i class="fa fa-user-secret"></i> <?= idioma('Choix des champs de table') ?>
			</a>
			<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','_id=<?= $TABLE_ONE['_id'] ?>',{value:'<?= $TABLE_ONE['_id'] ?>'})">
				<i class="fa fa-sitemap"></i> dépendances <?= sizeof($TABLE_ONE['grilleFK']) ?>
			</a>
		</div>
	<? endif; ?>
</div>