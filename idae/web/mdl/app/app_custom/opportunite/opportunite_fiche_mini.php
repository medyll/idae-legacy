<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors' , 55);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($table);
	//
	$table_value = (int)$_POST['table_value'];
	$vars = empty($_POST['vars']) ? [ ] : fonctionsProduction::cleanPostMongo($_POST['vars'] , 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	$APP = new App($table);
	$APP_OPOR_LGN = new App('opportunite_ligne');
	//
	$APP_TABLE = $APP->app_table_one;
	$id = 'id' . $table;
	$ARR = $APP->query_one([ $id => $table_value ]);

	//
	$arr_dsp_fields = $APP->get_display_fields($table);
	unset($arr_dsp_fields['description'] , $arr_dsp_fields['commentaire']);
	// opportunite_ligne
	$RS_OPOR_LGN = $APP_OPOR_LGN->find([ 'idopportunite' => (int)$table_value ]);
?>
<div style = "min-width:230px;" class = "blanc inline flex_h  " data-contextual = "table=<?= $table ?>&table_value=<?= $table_value ?>" data-table = "<?= $table ?>" data-table_value = "<?= $table_value ?>">
	<div class = "margin border4">
		<div class = "bold padding flex_h">
			<span class="flex_main"><?= $APP->draw_field([ 'field_name' => 'nomClient' , 'field_name_raw' => 'nom' , 'field_value' => $ARR['nomClient'] ]) ?></span>
			<span><i class="fa fa-battery-<?=$ARR['rangOpportunite']?> fa-rotate-270" ></i></span>
		</div>
		<div class = "retrait applink applinkblock">
			<? while ($ARR_LGN = $RS_OPOR_LGN->getNext()) {
				$q = (int)$ARR_LGN['quantiteOpportunite_ligne'];
				$p = $ARR_LGN['nomProduit'];
				?>
				<a  class = "flex flex_h flex_align_middle ">
					<span style="width:15px;"><?= $APP->draw_field([ 'field_name' => 'quantiteOpportunite_ligne' , 'field_name_raw' => 'quantite' , 'field_value' => $q ]) ?></span>
					<span style="width:15px;" class="aligncenter">*</span>
					<span class="flex_main"><?= $APP->draw_field([ 'field_name' => 'nomProduit' , 'field_name_raw' => 'nom' , 'field_value' => $p ]) ?></span>
				</a>
			<? } ?>
		</div>
		<div class = "bold padding titre aligncenter ededed bordert">
			<?= $APP->draw_field([ 'field_name' => 'montantOpportunite' , 'field_name_raw' => 'montant' , 'field_value' => $ARR['montantOpportunite'] ]) ?>
		</div>
	</div>
</div>