<?
	include_once($_SERVER['CONF_INC']);

	$APP               = new App('appscheme');
	$Idae              = new Idae('appscheme');
	$APPHASF           = new App('appscheme_has_field');
	$APPSC_FIELD       = new App('appscheme_field');
	$APPSC_FIELD_GROUP = new App('appscheme_field_group');
	$APPSC_HAS_FIELD   = new App('appscheme_has_field');

	$idappscheme = $table_value = (int)$_POST['idappscheme'];
	$arr         = $APP->findOne(['idappscheme' => $idappscheme]);
	$table       = $arr['codeAppscheme'];
	$Table       = ucfirst($table);

	$types = $APP->app_default_fields;

	$rsG        = $APPSC_FIELD_GROUP->find()->sort(['ordreAppscheme_field_group' => 1]);
	$Idae_table = new Idae($table);
?>
<div class="flex_v  blanc" app_gui_explorer style="height:100%;overflow:hidden;">
	<div class="titre_entete">
		<?= $arr['mainscope_app'] ?> <i class="fa fa-caret-right"></i> <?= $arr['nomAppscheme_base'] ?>
		<i class="fa fa-caret-right"></i> <?= $arr['nomAppscheme'] ?>
	</div>
	<div class="applink titre_entete_menu">
		<div class="in_menu padding_more">
			<a class=""><i class="fa fa-navicon"></i></a>
			<a onclick="<?= fonctionsJs::app_update('appscheme', $table_value) ?>"><i class="fa fa-cog"></i>
				<?= idioma('Modèle') ?> <?= $arr['codeAppscheme'] . ' ' ?><?= ($arr['codeAppscheme'] != $arr['nomAppscheme']) ? $arr['nomAppscheme'] : ''; ?></a>
			<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Grille','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
				<i class="fa fa-sitemap"></i> dépendances (<?= sizeof($arr['grilleFK']) ?>)
			</a>
			<a onclick="ajaxMdl('app/app_scheme/app_scheme_grille','Fiche FK app_builder','_id=<?= $arr['_id'] ?>',{value:'<?= $arr['_id'] ?>'})">
				<i class="fa fa-laptop"></i> grilles (<?= sizeof($arr['grille']) ?>)
			</a>
			<a onclick="ajaxMdl('app/app_scheme/app_scheme_has_field_update_model','Choix des champs de table','idappscheme=<?= $arr['idappscheme'] ?>')">
				<i class="fa fa-table"></i> <?= idioma('Champs de table') ?>
			</a>
			<a onclick="<?= fonctionsJs::app_sort('appscheme_has_field', '', ['vars' => ['idappscheme' => $idappscheme]]) ?>" class="cancelClose">
				<i class="fa fa-sort"></i>&nbsp;<?= idioma('Ordonner les champs de fiche') ?>
			</a>
			<a onclick="<?= fonctionsJs::app_sort('appscheme_has_table_field', '', ['vars' => ['idappscheme' => $idappscheme]]) ?>" class="cancelClose">
				<i class="fa fa-sort"></i>&nbsp;<?= idioma('Ordonner les champs de table') ?>
			</a>
		</div>
	</div>
	<div class="flex_main flex_h" style="overflow:hidden;">
		<div class="boxshadow" style="height:100%;width:250px;overflow:hidden;" id="only_name_field" expl_left_zone>
			<?= $Idae->module('app_scheme/app_scheme_field_type', $_POST); ?>
		</div>
		<div class="flex_main" style="height:100%;overflow:auto;">
			<div>
				<?= $Idae->module('app_scheme/app_scheme_has_field_update_inner', $_POST); ?>
			</div>
			<div class="flex_h">
				<div style="width:150px;">
				</div>
				<div>
					<?= $Idae->module('app_scheme/app_scheme_grille', $_POST); ?>
				</div>
				<hr>
				<div class="   ">
					<div class="padding_more">Mini fiche</div>
					<div class="padding_more border4">
						<?= $Idae_table->module('app/app/fiche_fields', ['table'            => $table,
						                                                 'in_mini_fiche'    => 1,
						                                                 'hide_empty'       => 0,
						                                                 'hide_field_title' => 0,
						                                                 'table_value'      => $table_value]) ?>
					</div>
				</div>
				<br/>
				<div class="   ">
					<div class="padding_more">Tout les champs</div>
					<div class="padding_more border4">
						<?= $Idae_table->module('app/app/fiche_fields', ['table'            => $table,
							'titre'=>1,
						                                                 'table_value'      => $table_value]) ?>
					</div>
				</div>
				<br/>
				<div class="   ">
					<div class="padding_more">Vue table</div>
					<div class="padding_more border4">
						<?= $Idae_table->module('app/app/fiche_fields', ['table'            => $table,
						                                                 'table_fields'     => 1,
						                                                 'hide_empty'       => 0,
						                                                 'hide_field_title' => 0,
						                                                 'table_value'      => $table_value]) ?>
					</div>
				</div>
				<br/>
			</div>
		</div>
	</div>
</div>
