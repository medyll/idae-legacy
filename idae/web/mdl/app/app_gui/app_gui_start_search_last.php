<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	ini_set('display_errors', 55);
	$table  = empty($_POST['table']) ? 'agent_recherche' : $_POST['table'];
	$mode   = empty($_POST['mode']) ? 'last' : $_POST['mode'];
	$vars   = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$nbRows = empty($_POST['nbRows']) ? 12 : $_POST['nbRows'];
	$page   = !isset($_POST['page']) ? 0 : $_POST['page'];

	$name_id = 'id' . $table;
	$Table   = ucfirst($table);

	$APP = new App('agent_recherche');

	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-30 day');
	$DADATE = $dateStart->format('Y-m-d');

	$sort = ['dateCreationAgent_recherche' => -1, 'heureCreationAgent_recherche' => -1];
	if ($mode == 'more') $sort['valeurAgent_recherche'] = -1;
	$RS = $APP->find(['idagent' => (int)$_SESSION['idagent']] + $vars, ['sort' => $sort, 'skip' => ($page * $nbRows), 'limit' => $nbRows]);

	$APP_TMP = new App($table);

	$uniqid = uniqid();
?>
<div class="blanc boxshadowb">
	<div class="avoid flex_h flex_align_middle borderb applink applinkblock toggler">
		<div class="capitalize bold borderr padding flex_main"><?= idioma('recherches') ?></a></div>
		<div class="avoid capitalize aligncenter borderr flex_h padding">
			<a class="autoToggle <?= ($mode == 'last') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','mode=last')"><?= idioma('dernieres') ?></a>
			<a class="autoToggle <?= ($mode == 'more') ? 'active' : '' ?>" onclick="loadFragment('remenber_search','mode=more')"><?= idioma('fréquentes') ?></a>
		</div>
	</div>
	<div class="" style="width:100%;z-index:200;" data-app_fragment="remenber_search">
		<div class="flex_h flex_wrap applink applinkblock" data-table="<?=$table?>">
			<?
				while ($arr = $RS->getNext()):

					$tr_vars = ['table_value' => (int)$arr['valeurAgent_recherche'], 'table' => $table, 'field_name_raw' => 'nom', 'field_value' => strtolower($arr['nom' . $Table])];
					?>
					<div  data-table="<?=$table?>" data-table_value="<?=$arr['id' . $table]?>" class="demi flex_h flex_align_middle mastershow">
						<a class="flex_main ellipsis" onclick="main_item_search.load_data('search=<?= addslashes($arr['nom' . $Table]) ?>');$('main_item_search_zone').toggleContent();">
							<?= $APP_TMP->draw_field($tr_vars) ?>
						</a>
						<div>
							<?=$arr['quantiteAgent_recherche']?>
						</div>
						<div class="avoid">
							<a class="slaveshow avoid" onclick="ajaxValidation('app_delete','mdl/app/','table=<?=$table?>&table_value=<?=$arr['id' . $table]?>')"><i class="fa f-fw fa-minus textrouge"></i></a>
						</div>
					</div>
					<?
				endwhile;
			?>
		</div>
		<div class="avoid alignright none" onclick="loadFragment('remenber_search','mode=last&page=<?= ++$page ?>')">
			<a>more</a>
		</div>
	</div>
</div>