<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 27/02/15
	 * Time: 23:22
	 */
	include_once($_SERVER['CONF_INC']);

	$id = $_POST['APPID'];

	$APP = new App();
	$APP_ONLINE = new App('online_site');
	$ARR = $APP_ONLINE->query_one(['codeOnline_site' => $id]);

	$nom = $ARR['nomOnline_site'];

	$table = 'online_site';
	$Table = 'Online_site';
	$table_value = (int)$ARR['idonline_site'];
?>
<div class="relative">
	<div class="glue_requested padding applink flex_h bordert" style="width: 100%;display:none;">
		<span class="relative flex_main">
			<a class="glue_reserve textvert"><i class="fa fa-bell"></i> prendre</a>
			<a class="glue_reserved text-success" style="display: none;"><i class="fa fa-bell"></i> ok </a>
			<a class="glue_reject textgris" style="display: none;"><i class="fa fa-ban"></i> réservé</a>
		</span>
		<span class="relative alignright">
			<a class="glue_busy" style="display: none;"><i class="fa fa-rss"></i></a>
		</span>
	</div>
	<div class="flex_h flex_main" style="width:100%;"  data-contextual="table=<?= $table ?>&table_value=<?= $table_value ?>"
	     table="<?= $table ?>" table_value="<?= $table_value ?>">
		<div class="aligncenter padding"><div class="padding"> <i class="fa fa-street-view fa-2x"></i><br>
			Vues<br>
				<?= $APP->draw_field(['field_name_raw' => 'nombreVue', 'table' => $table, 'field_value' => $ARR['nombreVue' . $Table]]) ?>
			</div></div>
		<div class="flex_main" style="width:100%;overflow: hidden;">
			<div class="relative">
				<div style="width: 100%;">
					<div class="glue_phone bold titre1 aligncenter" style="display:none;" ></div>
					<div class="bold ededed borderb bordert borderl titre_entete">
						<?= $APP->draw_field(['field_name_raw' => 'nom', 'table' => $table, 'field_value' => $ARR['nom' . $Table]]) ?>
					</div>
					<div class="padding">
						<div>Date de
							création <?= $APP->draw_field(['field_name_raw' => 'dateCreation', 'table' => $table, 'field_value' => $ARR['dateCreation' . $Table]]) ?>
							<?= $APP->draw_field(['field_name_raw' => 'heureCreation', 'table' => $table, 'field_value' => $ARR['heureCreation' . $Table]]) ?>
						</div>
						<div>Date de
							modification <?= $APP->draw_field(['field_name_raw' => 'dateModification', 'table' => $table, 'field_value' => $ARR['dateModification' . $Table]]) ?></div>
						<div>Heure de
							fin <?= $APP->draw_field(['field_name_raw' => 'heureModification', 'table' => $table, 'field_value' => $ARR['heureModification' . $Table]]) ?></div>
						<div>
							Durée <?= $APP->draw_field(['field_name_raw' => 'duree', 'table' => $table, 'field_value' => $ARR['duree' . $Table]]) ?></div>
						<div>
							Url <?= $APP->draw_field(['field_name_raw' => 'url', 'table' => $table, 'field_value' => $ARR['url' . $Table]]) ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>