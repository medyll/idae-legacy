<?
	include_once($_SERVER['CONF_INC']);

	$table_value = (int)$_POST['table_value'];
	$table       = $_POST['table'];

	$nom         = 'nom' . ucfirst($table);
	//
	//
	$APPSC             = new App('appscheme');
	$APPSC_FIELD       = new App('appscheme_field');
	$APPSC_HAS_FIELD   = new App('appscheme_has_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');

	$ARRSCH = $APPSC_HAS_FIELD->findOne(['codeAppscheme' => $table]);

	$idappscheme = (int)$ARRSCH['idappscheme'];
	$APP         = new App($table);
	$APPOBJ      = $APP->appobj($table_value);
	$TABLE_ONE   = $APP->app_table_one;
	$id          = 'id' . $table;
	$ARR         = $APP->query_one([$id => $table_value]);
	$GRILLE_FK   = $APP->get_grille_fk();
	$R_FK        = $APP->get_reverse_grille_fk($table, $table_value);

	// remplace les hastable hasCodeScheme
	$ARR_DIST  = $APPSC_HAS_FIELD->distinct('appscheme_field', ['idappscheme' => $idappscheme], 346, 'no_full', 'idappscheme_field');
	$HAS_FIELD = $APPSC_FIELD->distinct('appscheme_field', ['idappscheme_field' => ['$in' => $ARR_DIST]], 346, 'no_full', 'codeAppscheme_field');

	$arr_ico = ['fiche' => 'file-text-o', 'update' => 'pencil', 'map' => 'map-marker'];
	//
	$APP->consolidate_scheme($ARR[$id]);
	//
	$TEST_AGENT = array_search('agent', array_column($GRILLE_FK, 'table_fk'));
?>
<div class="titre_entete boxshadow flex_margin   ededed   flex_h   borderb flex_align_top" style="width: 100%;">
	<div class="aligncenter paddingmore"><i class="fa fa-<?= $APP->iconAppscheme ?>" style="color:<?= $APP->colorAppscheme ?>"></i>
	</div>
	<div class="flex_main">
		<div class="uppercase"><?= $ARR[$nom] ?></div>
	<div><?= $APP->nomAppscheme ?></div>
	</div>
	<? if (isset($TEST_AGENT)): ?>
		<div class="alignright  ">
			<i class="fa fa-user"></i>
			<?= nomAgent($ARR['idagent']) ?>
		</div>
	<? endif; ?>
</div>
<div class="ok_hide applink  titre_entete_menu ededed   bordert" style="border-color:<?=$APP->colorAppscheme?>"> <!--dark_2-->
	<div class="flex_h in_menu">
		<? if (droit_table($_SESSION['idagent'], 'R', $table)): ?>
			<a onclick="<?= fonctionsJs::app_fiche($table, $table_value) ?>" class="cancelClose">
				<i class="fa fa-file-text-o textvert"></i>
				&nbsp;
				<?= idioma('Fiche') ?>
			</a>
		<? else : ?>
			<a class="textgris">
				<i class="fa fa-file-text-o"></i>
				&nbsp;
				<?= idioma('Fiche') ?>
			</a>
		<? endif; ?>
		<? if (droit_table($_SESSION['idagent'], 'R', $table)): ?>
			<a class="cancelClose none" onclick="<?= fonctionsJs::app_fiche_preview($table, $table_value) ?>">
				<i class="fa fa-eye"></i>
				<?= idioma('Détails') ?>
			</a>
			<a class="cancelClose" onclick="<?= fonctionsJs::app_fiche_maxi($table, $table_value) ?>">
				<i class="fa fa-columns textbold"></i>
				<?= idioma('Synthèse') ?>
			</a>
		<? endif; ?>
		<? if (droit_table($_SESSION['idagent'], 'U', $table)): ?>
			<a onclick="<?= fonctionsJs::app_update($table, $table_value) ?>" class="cancelClose">
				<i class="fa fa-pencil textjaune "></i>
				<?= idioma('Modifier') ?>
			</a>
		<? endif; ?>
		<? if (in_array('ordre', $HAS_FIELD)): ?>
			<a onclick="<?= fonctionsJs::app_sort($table, '') ?>" class="cancelClose">
				<i class="fa fa-sort"></i>
				&nbsp;
				<?= idioma('Ordonner') ?>
			</a>
		<? endif; ?>
		<? if (!empty($APPOBJ->APP_TABLE['hasImageScheme'])): ?>
			<a act_chrome_gui="app/app_img/image_app_liste_img" vars="table=<?= $table ?>&table_value=<?= $table_value ?>"
			   options="{ident:'img_<?= $table . $table_value ?>'} " class="cancelClose">
				<i class="fa fa-file-image-o"></i>
				&nbsp;
				<?= idioma('Images') ?>
			</a>
		<? endif; ?>
	</div>
	<div class="applink flex_main in_menu"><?= skelMdl::cf_module('app/app/app_menu_custom', ['moduleTag' => 'none'] + $_POST, $table_value); ?></div>
	<? if (sizeof($R_FK) != 0): ?>
		<div class="applink   aligncenter ededed border4 none">
			<a data-menu="data-menu" class="  bold    "><i class="fa fa-plus-square textvert"></i> <?= idioma('Ajouter') ?></a>
			<div class="blanc applinkblock border4 boxshadow" style="text-align:left;display:none;position:absolute;z-index:1000;">
				<? foreach ($R_FK as $arr_fk):
					$final_rfk[$arr_fk['scope']][] = $arr_fk;
				endforeach; ?>
				<? foreach ($final_rfk as $key => $arr_final):

					foreach ($arr_final as $arr_fk):
						$tmp_table = $arr_fk['codeAppscheme'];
						$APP_TMP          = new App($tmp_table);
						$vars_rfk['vars'] = ['id' . $table => $table_value];
						if (!droit_table($_SESSION['idagent'], 'C', $arr_fk['table'])) continue;
						$ct        = $APP_TMP->has_field('dateDebut') . '  ' . $APP_TMP->has_field_fk('agent');
						?>
						<a onclick="<?= fonctionsJs::app_create($arr_fk['table'], ['vars' => ['id' . $table => $table_value, 'idagent' => $_SESSION['idagent']]]) ?>">
							<i class="fa fa-<?= $arr_fk['icon'] ?> borderr"></i> <? //=$ct
							?> <?= $arr_fk['nomAppscheme'] ?>
						</a>
					<? endforeach; ?>
				<? endforeach; ?>
			</div>
		</div>
	<? endif; ?>
	<div><?= skelMdl::cf_module('app/app_scheme/app_scheme_menu_icon', $_POST) ?></div>
</div>