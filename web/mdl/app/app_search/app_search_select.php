<?
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	//
	$alph = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

	// if ($_SESSION['idagent'] != 1) return;
	$uniqid = uniqid();
	$table = $_POST['table'];
	$vars = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP = new App($table);
	$APP_TABLE = $APP->app_table_one;
	$GRILLE_FK = $APP->get_grille_fk();
	$R_FK = $APP->get_reverse_grille_fk($table);
	//
	$arr_has =  ['type','statut'];
?>
<form class="Form" id="form<?= $uniqid ?>" expl_form="expl_form" onsubmit="return false;" >
	<input type="hidden" name="table" value="<?= $table ?>" >

	<div class="menu_nav" >
		<div class="applink applinkblock toggler border4 padding margin ededed flex_h flex_wrap"  >
			<? foreach ($alph as $v) {
				//	$more = (!droits('VOIR_TOUS_CLIENTS'))? '&agent_idagent='.$_SESSION['idagent']: '';
				?>
				<a style="width:9%;" class="autoToggle" onClick="reloadModule('client/mdlClientListe','<?= $valueModule ?>','lettre=<?= $v ?><?= $more ?>')" >
					<?= $v ?>
				</a >
				<? ;
			} ?></div >

		<? foreach($arr_has as $key=>$value):
			$Value = ucfirst($value);
			$_id = 'id'.$value;
			$_nom = 'nom'.ucfirst($value);
			 if (!empty($APP_TABLE['has'.$Value.'Scheme'])): ?>
			<div class="  retrait" >
				<input datalist_input_name="vars[<?= $_id ?>]"
				       datalist_input_value=""
				       datalist="app/app_select"
				       populate
				       dsp="inline"
				       placeholder="<?= $value ?>"
				       paramName="search"
				       class="noborder"
				       vars="table=<?= $value . '_type' ?>"
				       value="" />
			</div >
		<? endif; ?>


		<? endforeach; ?>
		FK
		<? foreach ($GRILLE_FK as $fk):
			$table_fk = $fk['table_fk'];
			$id_fk = 'id' . $fk['table_fk'];
			$rs_dist = $APP->distinct($table_fk, $vars);
			$arr_fk = $APP->get_fk_id_tables($table_fk);

			$arr_inter = array_intersect_key($vars, $arr_fk);
			?>
			<div >
				<?= skelMdl::cf_module('app/app_search/search_item', ['item' => $table_fk], '', 'item="' . $table_fk . '"') ?>
			</div >

		<? endforeach; ?>
		<? if (sizeof($R_FK) != 0): ?>
<hr>
			RFK
			<? foreach ($R_FK as $arr_fk):
				$value_rfk = $arr_fk['table_value'];
				$table_rfk = $arr_fk['table'];
				$vars_rfk['vars'] = ['id' . $table => $table_value];
				$vars_rfk['table'] = $table_rfk;
				$vars_rfk['table_value'] = $value_rfk;
				$count = $arr_fk['count'];
				// if (empty($count)) continue;
				?>
				<div class="retrait" >
					<input datalist_input_name="vars_rfk[id<?= $table_rfk ?>]"
					       datalist_input_value="<?= $arrType[$id_type] ?>"
					       datalist="app/app_select"
					       populate
					       placeholder="<?=$table_rfk ?>"
					       paramName="search"
					       vars="table=<?= $table_rfk ?>"
					       style ="width: 190px;"  />

				</div >
			<? endforeach; ?>

		<? endif; ?>
		<div >
			<button type="submit" value="Ok" >Ok</button >
		</div >
	</div >
</form >




