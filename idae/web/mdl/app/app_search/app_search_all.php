<?
	include_once($_SERVER['CONF_INC']);
	require_once(__DIR__ . '/../../../appclasses/appcommon/MongoCompat.php');
	use AppCommon\MongoCompat;
	// $_POST['table'] = 'client';

	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'], 1);
	$page = (empty($_POST['page'])) ? 0 : $_POST['page'];
	$nbRows = (empty($_POST['nbRows'])) ? 5 : $_POST['nbRows'];
	$APP = new App();
	$whereT = array();
	$where = array();
	//
	if (!empty($_POST['search'])) {
		$search_escaped = MongoCompat::escapeRegex($_POST['search']);
		$regexp         = MongoCompat::toRegex(".*" . $search_escaped . "*.", 'i');
		$whereT         = array('$or' => array(array($nom => $regexp), array($id => (int)$_POST['search'])));
		$where['$or'][] = array($nom => $regexp);
		$where['$or'][] = array($id => (int)$_POST['search']);
		// tourne ds fk
		if (sizeof($GRILLE_FK) != 0) {
			foreach ($GRILLE_FK as $field):
				$nom_fk         = 'nom' . ucfirst($field['table_fk']);
				$nom_fk_escaped = MongoCompat::escapeRegex($nom_fk);
				$regexp         = MongoCompat::toRegex("." . $nom_fk_escaped . "*.", 'i');
				$where['$or'][] = array($nom_fk => $regexp);
			endforeach;
		}
	}
	$RSSCHEME = $APP->get_schemes();
?>
<div class = " blanc applink">
	<div class = "table"
	     style = "width: 100%;">
		<div class = "cell">
			<div class = "titre_entete">123</div>
		</div>
		<div class = "cell aligncenter borderl"
		     style = "width: 30px;">
			<div class = "titre_entete avoid">
				<a onclick = "$('param_se').toggle();">
					<i class = "fa fa-cog"></i>
				</a>
			</div>
		</div>
	</div>
</div><br>
<div>
	<div id = "param_se"
	     class = "applink applinkblock border4 margin absolute blanc avoid"
	     style = "width: 100%;z-index: 100;display: none;">
		<? foreach ($RSSCHEME as $sch):
			$table = $sch['collection'];?>
			<label><input type = "checkbox" onclick="save_setting_mdl_search(this,'<?= $table ?>_search_field');"
			              value = "1" <?=checked( $APP->get_settings($_SESSION['idagent'], $table . '_search_field'));?>>&nbsp;<?= $sch['base'] .'-'. $sch['collection'] ?></label>

		<? endforeach; ?>
	</div>
</div>
<div id="dyn_se"></div>

<div app_gui_flowdown
     style = "overflow: auto;width:550px;">
	<? foreach ($RSSCHEME as $sch):
		$table  = $sch['collection'];
		$id     = 'id' . $table;
		$nom    = 'nom' . ucfirst($table);
		$prenom = 'prenom' . ucfirst($table);
		$email  = 'email' . ucfirst($table);
		$code   = 'code' . ucfirst($table);
		$codeXml   = 'codeXml' . ucfirst($table);
		//
		$where = array();
		if (!empty($_POST['search'])) {
			$search_escaped = MongoCompat::escapeRegex($_POST['search']);
			$regexp         = MongoCompat::toRegex(".*" . $search_escaped . "*.", 'i');
			$where['$or'][] = array($nom => $regexp);
			$where['$or'][] = array($email => $regexp);
			$where['$or'][] = array($code => $regexp);
			$where['$or'][] = array($codeXml => $regexp);
			$where['$or'][] = array($id => (int)$_POST['search']);
			// tourne ds fk
			if (sizeof($GRILLE_FK) != 0) {
				foreach ($GRILLE_FK as $field):
					$nom_fk         = 'nom' . ucfirst($field['table_fk']);
					$nom_fk_escaped = MongoCompat::escapeRegex($nom_fk);
					$regexp         = MongoCompat::toRegex("." . $nom_fk_escaped . "*.", 'i');
					$where['$or'][] = array($nom_fk => $regexp);
				endforeach;
			}
		}
		//
		$APPSC     = new App($table);
		$GRILLE_FK = $APPSC->get_grille_fk();

		$rssc_count = $APPSC->query($where)->count();
		$rssc       = $APPSC->query($vars + $where, (int)$page, (int)$nbRows);
		if ($rssc_count != 0):
			?>
			<div class = "">
				<div auto_tree
				     auto_tree_count = "<?= $rssc_count ?>"
				     style = "width: 100%;"
				     class = "bold  uppercase opened avoid">
					<span>
					<i class = "fa fa-<?= $sch['icon'] ?>"></i>
					<?= $sch['collection'] ?></span>
				</div>
				<div class = "autoBlock applink applinkblock">
					<? while ($arr_rssc = $rssc->getNext()) : ?>
						<table style = "width: 100%;" class="tabletop"  data-contextual="table=<?= $table ?>&table_value=<?= $arr_rssc[$id] ?>">
							<tr class = "mastershow">
								<td style = "width: 50px;"><a><?= $arr_rssc[$id] ?></a></td>
								<td>
									<div <?=(sizeof($GRILLE_FK)==0)? '' : 'auto_tree'; ?>><a act_chrome_gui = "app/app/app_fiche"
									                  vars = "table=<?= $table ?>&table_value=<?= $arr_rssc[$id] ?>"><?= $arr_rssc[$nom] ?> <?= strtolower($arr_rssc[$prenom]) ?></a></div>
									<div class="autoBlock" style="display:none;">
										<? foreach ($GRILLE_FK as $field):
											$id_fk = $field['idtable_fk'];
											//
											if(!empty($arr_rssc[$id_fk])):
											$arrq     = $APP->plug($field['base_fk'], $field['table_fk'])->findOne(['id'.$field['table_fk'] => (int)$arr_rssc[$id_fk]]);
											$dsp_name = $arrq['nom' . ucfirst($field['table_fk'])];
												// icon
												$APPFK     = new App($field['table_fk']);
											?>
											<div class = "ellipsis inline demi" style="width:40%;"><a><i class = "fa fa-<?= $APPFK->app_table_one['icon'] ?>"></i><?= $dsp_name?></a></div>
										<? endif; ?>
										<? endforeach; ?>
									</div>
								</td>


							</tr>
						</table>
					<? endwhile; ?>
				</div>
			</div>
		<? endif; ?>
	<? endforeach; ?>
</div>
<script>
	save_setting_mdl_search = function (node, key) {

		setTimeout(function () {
			dsp = $(node).checked;
			ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + dsp);
		}.bind(this), 500)
	}
</script>