<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	// POST
	$table = $_POST['table'];
	$table_value = (int)$_POST['table_value'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	//
	$APP_TABLE = $APP->app_table_one;
	$GRILLE_FK = $APP->get_grille_fk();
	$R_FK = $APP->get_reverse_grille_fk($table, $table_value);
	$HTTP_VARS = $APP->translate_vars($vars);
	$BASE_APP = $APP_TABLE['base'];
	$arrFieldsBool = $APP->get_array_field_bool();
	$arrFieldsMeta = $APP->get_meta_fields();
	$arrFields = $APP->get_basic_fields();
	//
	$id = 'id' . $table;
	$ARR = $APP->query_one([$id => $table_value]);
	//
	$top = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom = 'nom' . ucfirst($table);



?>
<div class="flex_v" style="height: 100%;width:100%;">
	<div class="relative"><?= skelMdl::cf_module('app/app/app_menu', ['table' => $table, 'table_value' => $table_value, 'act_from' => 'preview']) ?></div>
	<div class="barre_entete alignright ">
		<div  style="display: inline-table;">
			<? foreach ($arrFieldsBool as $bool => $arr_ico):
				$set_value = empty($arr[$bool . ucfirst($table)]);
				$css       = empty($arr[$bool . ucfirst($table)]) ? 'textgris' : '';
				$name      = $bool . ucfirst($table);
				$uri       = "table=$table&table_value=$arr[$id]&vars[$name]=$set_value&scope=$id";
				?>
				<div class="aligncenter cell <?= $css ?>">
						<div class="padding"><li class="fa fa-<?= $arr_ico[(int)$set_value] ?>"></li> </div>

				</div>
			<? endforeach; ?></div></div>
	<div main_auto_tree class="flex_main" style="overflow:auto;">
		<div auto_tree ><div class="trait"> <?= idioma('Fiche') ?></div></div>
		<div class="retrait">
			<?=skelMdl::cf_module('app/app/app_fiche_mini', array('table' => $table, 'table_value' => $table_value)) ?>
		</div> 
		<? if (sizeof($GRILLE_FK) != 0): ?>
			<br>
			<div auto_tree onclick="save_setting_autoNext(this,'<?= $table ?>_preview_grillefk')"><div class="trait"> <?= idioma('Propriétés') ?></div></div>
			<div class="retrait" style="display:<?= $APP -> get_settings($_SESSION['idagent'], $table . '_preview_grillefk') ?>;">
				<table class="table_info">
					<? foreach ($GRILLE_FK as $field):
						// query for name
						$arr      = $APP->plug($field['base_fk'], $field['table_fk'])->findOne([$field['idtable_fk'] => $ARR[$field['idtable_fk']]]);
						$dsp_name = $arr['nom' . ucfirst($field['table_fk'])];
						?>
						<tr>
							<td class="padding"><?= ucfirst($field['table_fk']) ?></td>
							<td class="padding"><a act_chrome_gui="app/app/app_fiche"
							                               vars="table=<?= $field['table_fk'] ?>&table_value=<?= $ARR[$field['idtable_fk']] ?>"
							                               options="{ident:'<?= $field['table_fk'] ?>',scope:'<?= $field['table_fk'] ?>'}"><?= empty($dsp_name) ? 'Aucun ' : $dsp_name; ?></a>
							</td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
		<? endif; ?>
		<? if(sizeof($R_FK)!=0): ?>
			<br>	<br>
		<div auto_tree ><div class="trait ededed"> <?= idioma('Liens') ?></div></div>
		<div class="retrait" >
			<table class="table_info">
				<?
					foreach ($R_FK as $arr_fk):
					$value_rfk               = $arr_fk['table_value'];
					$table_rfk               = $arr_fk['table'];
					$vars_rfk['vars']        = ['id' . $table => $table_value];
					$vars_rfk['table']       = $table_rfk;
					$vars_rfk['table_value'] = $value_rfk;
					$count                   = $arr_fk['count'];
					?>
					<tr>
						<td><?= $table_rfk ?></td>
						<td><a act_chrome_gui="app/app_liste/app_liste_gui"
						       vars="<?= http_build_query($vars_rfk); ?>">
								<i class="fa fa-<?=$arr_fk['icon'] ?>"></i> <?=
									$count . ' ' . $table_rfk . '' . (($count == 0) ? '' : 's') ?></a>
						</td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
		<? endif; ?>

		<? if(!empty($APP_TABLE['hasLigneScheme'])): ?>	<br>	<br>
			<div auto_tree ><div class="trait"> <?= idioma('Lignes') ?></div></div>
			<div class="retrait" id="has_ligne<?=$table_value?>" data-dsp="table_line"></div>
			<script >
				load_table_in_zone('table=<?=$table?>_ligne&vars[<?=$id?>]=<?=$table_value?>', 'has_ligne<?=$table_value?>');
			</script >
		<? endif; ?>

		 
	</div>
</div>