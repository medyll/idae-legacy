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
	$HTTP_VARS = $APP->translate_vars($vars);

	// un distinct sur les valeurs ?
	$arr_dist = $APP->distinct($table, $vars, 500, 'no_full', 'nom' . ucfirst($table));

	$nom_table = $APP->nomAppscheme;
	if (sizeof($arr_dist) == 0):
		$arr_dist = $APP->distinct($table, $vars, 500, 'no_full', 'code' . ucfirst($table));
	endif;
	foreach ($arr_dist as $lgn_dist) {
		$lgn_dist = trim(strtoupper($lgn_dist));
		$substr   = substr($lgn_dist, 0, 1);
		$substr2   = substr($lgn_dist, 0, 2);
		$substr3   = substr($lgn_dist, 0, 3);
		if ($substr == '') continue;
		$alph[$substr]  = $substr;
		$alph1[$substr2] = $substr2;
		$alph2[$substr3] = $substr3;
	}
	//
	array_unique($alph);
	ksort($alph);
	// vardump_async($GRILLE_FK,true);
	/*$arr_more_fk = [];
	foreach ($GRILLE_FK as $fk):
		$table_fk = $fk['table_fk'];
		if (!empty($vars["id$table_fk"])) continue;
		$arr_fk    = $APP->get_fk_id_tables($table_fk);
		$arr_inter = array_intersect_key($vars, $arr_fk);
		$TR_FK      = $APP->get_reverse_grille_fk($table_fk);
		//vardump_async($TR_FK,true);
		foreach ($TR_FK as $arr_fk):
			$value_rfk               = $arr_fk['table_value'];
			$table_rfk               = $arr_fk['table'];
			$vars_rfk['vars']        = ['id' . $table => $table_value];
			$vars_rfk['table']       = $table_rfk;
			$vars_rfk['table_value'] = $value_rfk;

			$arr_more_fk[] = ['table_fk'=>$table_fk,'table'=>$table_rfk];
		endforeach;
	endforeach;*/
	// vardump_async($arr_more_fk,true);
?>
<form id="form<?= $uniqid ?>" onclick="gain_searchbutton(this,event)" onfocus="gain_search_summary(this,event)" onkeyup="$('main_search_<?= $table ?>').loadModule('app/app_search/app_search_summary',$(this).serialize());"
      onsubmit="return false;"
      style="height: 100%;overflow:hidden;">
	<? foreach ($vars as $key => $input): ?>
		<input type="hidden" name="vars[<?= $key ?>]" value="<?= $input ?>">
	<? endforeach; ?>
	<input type="hidden" name="table" value="<?= $table ?>">
	<div class="flex_v">
		<div class="padding_more borderb ededed  ">
			<input placeholder="recherche <?= $nom_table ?>" type="text" name="search" value="" class="inputFull">
		</div>
		<div id="main_search_<?= $table ?>"></div>
		<div class="relative  zone_button   padding aligncenter" style="margin: 0 0.5rem;">
			<div class=" ">
				<input type="reset" value="annuler" class="no_border_input" style="display:none;border:none;background-color: transparent;" onclick="$(this).hide()">
				<button class="padding" type="submit" value="Ok">rechercher <i class="fa fa-search"></i></button>
			</div>
		</div>
		<div class="titre_entete  "><i class="fa fa-search-plus"></i>Affiner la recherche</div>
		<div class="flex_main ededed" style="overflow:auto;">
			<?
				$arr_has = ['statut', 'type', 'categorie', 'group', 'groupe'];
				foreach ($arr_has as $key => $value):
					$Value  = ucfirst($value);
					$_table = $table . '_' . $value;
					$_Table = ucfirst($_table);

					if (!empty((int)$APP_TABLE['has' . $Value . 'Scheme'])):
						?>
						<div class="blanc borderb">
							<?= skelMdl::cf_module('app/app_search/search_item_select', ['table' => $_table, 'search_type' => 'free', 'input_name' => 'vars_search_fk']); ?>
						</div>
					<? endif; ?>
				<? endforeach; ?>
			<? if (sizeof($GRILLE_FK) != 0): ?>
				<br>
				<div class="   ">
					<? foreach ($GRILLE_FK as $fk):
						$table_fk  = $fk['table_fk'];
						if (!empty($vars["id$table_fk"])) continue;
						$arr_fk    = $APP->get_fk_id_tables($table_fk);
						$arr_inter = array_intersect_key($vars, $arr_fk);
						?>
						<div class="borderb">
							<?= skelMdl::cf_module('app/app_search/search_item', ['table' => $table_fk, 'vars' => $arr_inter, 'search_type' => 'free', 'input_name' => 'vars_search_fk']); ?>
						</div>
					<? endforeach; ?>
				</div>
				<br>
			<? endif; ?>
			<? if (sizeof($R_FK) != 0): ?>
				<div class=" ">
					<? foreach ($R_FK as $arr_fk):
						$value_rfk               = $arr_fk['table_value'];
						$table_rfk               = $arr_fk['table'];
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $table_rfk;
						$vars_rfk['table_value'] = $value_rfk;
						?>
						<div class="borderb">
							<?= skelMdl::cf_module('app/app_search/search_item', ['table' => $table_rfk, 'search_type' => 'free', 'input_name' => 'vars_search_rfk']); ?>
						</div>
					<? endforeach; ?>
				</div>
			<? endif; ?>
		</div>
		<div class="applink applinkblock toggler   boxshadow blanc flex_h flex_wrap padding_more searchMdl  ">
			<label style="min-width:9%;" class="autoToggle active">
				<span style="z-index:10;position:relative;"> ... </span>
				<input style="position:absolute;visibility: hidden;z-index:-1" type="radio" name="search_start" value="">
			</label>
			<? foreach ($alph as $k => $v) {
				//	$more = (!droits('VOIR_TOUS_CLIENTS'))? '&agent_idagent='.$_SESSION['idagent']: '';
				?>
				<label style="min-width:9%;" class="autoToggle padding cursor">
					<span style="z-index:10;position:relative; "> <?= $v ?></span>
					<input style="position:absolute;visibility: hidden;z-index:11" type="radio" name="search_start" value="<?= $v ?>">
				</label>
			<? } ?>
		</div>
	</div>
</form>
<script>
	gain_searchbutton = function (form, event) {
		if ( form.querySelector ('.zone_button') ) {
			var button_zone = $ (form.querySelector ('.zone_button'));
			if ( event.target.match ('input[type=text]') || event.target.match ('input[type=radio]') ) {
				var input_zone = $ (event.target);
				if ( input_zone.up ('.searchMdl') ) {
					if ( !input_zone.up ('.searchMdl').next () || !input_zone.next ().hasClassName ('zone_button') ) {
						var input_insert_after = input_zone.up ('.searchMdl');
					}
				} else if ( !input_zone.next () || !input_zone.next ().hasClassName ('zone_button') ) {
					var input_insert_after = input_zone

				}
				input_insert_after.insert ({ after : button_zone })
			}
		}
		if ( event.target.match ('input[type=text]') ) {
			// gain_search_summary($(event.target));
		}
	}
	gain_search_summary = function (input) {
		//$('main_search_<?= $table ?>').clonePosition(input);
	}
</script>

