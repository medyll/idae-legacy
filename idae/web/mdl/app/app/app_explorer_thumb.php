<?
	include_once($_SERVER['CONF_INC']);
	if (empty($_POST['table'])) return;
	//
	// $alph = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
	$alph = [];
	// if ($_SESSION['idagent'] != 1) return;
	$uniqid    = uniqid();
	$table     = $_POST['table'];
	$vars      = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$APP       = new App($table);
	$APP_TABLE = $APP->app_table_one;
	$GRILLE_FK = $APP->get_grille_fk();
	$R_FK      = $APP->get_reverse_grille_fk($table);
	// vardump($R_FK);
	$HTTP_VARS       = $APP->translate_vars($vars);
	$BASE_APP        = $APP_TABLE['base'];
	$APP_SORT_FIELDS = $APP->get_date_fields($table);

	// 7 derniers jours
	$startTime       = date('d/m/Y', mktime() - 7 * 3600 * 24);
	$endTime         = date('d/m/Y', mktime());
	$APP_SORT_FIELDS = $APP->get_date_fields($table);
	//
	$arr_has = ['type', 'statut'];
	// un distinct sur les valeurs ?
	$arr_dist = $APP->distinct($table, [], 500, 'no_full', 'nom' . ucfirst($table));

	if (sizeof($arr_dist)==0):
		$arr_dist = $APP->distinct($table, [], 500, 'no_full', 'code' . ucfirst($table));
	endif;
	foreach ($arr_dist as $lgn_dist) {
		$lgn_dist = trim(strtoupper($lgn_dist));
		$substr   = substr($lgn_dist, 0, 1);
		if ($substr == '') continue;
		$alph[$substr] = $substr;
	}
	//
	array_unique($alph);
	ksort($alph);
?>
<form id="form<?= $uniqid ?>"   onsubmit="return false;" style="height: 100%;overflow-y:auto;overflow-x:hidden;">
	<input type="hidden" name="table" value="<?= $table ?>">
	<div class="flex_v">
		<div class="applink applinkblock toggler bordertb ededed flex_h flex_wrap padding">
			<label style="min-width:9%;" class="autoToggle active">
				<span style="z-index:10;position:relative;"> ... </span>
				<input style="position:absolute;visibility: hidden;z-index:-1" type="radio" name="search_start" value="">
			</label>
			<? foreach ($alph as $k => $v) {
				//	$more = (!droits('VOIR_TOUS_CLIENTS'))? '&agent_idagent='.$_SESSION['idagent']: '';
				?>
				<a style="min-width:9%;" class="autoToggle padding">
					<span style="z-index:10;position:relative;"> <?= $v ?></span>
					<input style="position:absolute;visibility: hidden;z-index:-1" type="radio" name="search_start" value="<?= $v ?>">
				</a>
				<? ;
			} ?>
		</div>
		<div class="flex_main" style="overflow:auto;">
			<br>
			<div class="ededed borderb padding ">
				<div class="padding uppercase"><?= ucfirst($table) ?></div>
				<div class="flex_h flex_align_middle">
					<i class="fa fa-search padding margin"></i>
					<input name="search" value="" placeholder="<?= $table ?>">
				</div>
			</div>
			<br>
			<? // =skelMdl::cf_module('app/app_field_add',array('field'=>['prospect','client']))?>


			<?
				$arr_has = ['statut', 'type'];
				foreach ($arr_has as $key => $value):
					$Value  = ucfirst($value);
					$_table = $table . '_' . $value;
					$_Table = ucfirst($_table);
					$_id    = 'id' . $_table;
					$_nom   = 'nom' . $_Table;
					if (!empty($APP_TABLE['has' . $Value . 'Scheme'])): ?>

						<div>
							<?= skelMdl::cf_module('app/app_search/search_item_select', ['table' => $_table, 'search_type'=>'free','input_name'=>'vars']); ?>
						</div>
					<? endif; ?>
				<? endforeach; ?>
			<br>
			<? foreach ($GRILLE_FK as $fk):
				$table_fk  = $fk['table_fk'];
				$id_fk   = 'id' . $fk['table_fk'];
				$rs_dist = $APP->distinct($table_fk, $vars);
				$arr_fk  = $APP->get_fk_id_tables($table_fk);

				$arr_inter = array_intersect_key($vars, $arr_fk);
				?>
				<div>
					<?= skelMdl::cf_module('app/app_search/search_item', ['table' => $table_fk, 'search_type'=>'free','input_name'=>'vars']); ?>
				</div>
			<? endforeach; ?>
			<div class="buttonZone">
				<button type="submit" value="Ok"><i class="fa fa-search"></i></button>
			</div>
			<? if (sizeof($R_FK) != 0): ?>
				<br>
				<div class="margin ededed border4 padding">
					<? foreach ($R_FK as $arr_fk):
						$value_rfk               = $arr_fk['table_value'];
						$table_rfk               = $arr_fk['table'];
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $table_rfk;
						$vars_rfk['table_value'] = $value_rfk;
						$count                   = $arr_fk['count'];
						// if (empty($count)) continue;
						?>
						<div>
							<?= skelMdl::cf_module('app/app_search/search_item', ['table' => $table_rfk, 'search_type'=>'free','input_name'=>'vars_search_rfk']); ?>
						</div>
					<? endforeach; ?>
				</div>
			<? endif; ?>
		</div>
	</div>
</form>




