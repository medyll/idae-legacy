<?php
	include_once($_SERVER['CONF_INC']);
	//
	$vars         = empty($_POST['vars']) ? [] : $_POST['vars'];
	$table        = empty($_POST['table']) ? 'client' : $_POST['table'];
	$name_id      = 'id' . $table;
	$Table        = ucfirst($table);
	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);

	$APP               = new App($table);
	//
	$dateStart = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');

	$nom_table = $APP->nomAppscheme;

	$arrDate = ['dateCreation' => 'créé(s)', 'dateModification' => 'modifié(s)'];
?>
<div class="flex_h flex_margin flex_align_middle" style="height:100%;">
	<div class="flex_main flex_h flex_margin flex_align_middle border4 ededed" style="min-width:250px;">
		<div style="width:40px;" class="aligncenter ededed">
			<i  class="fa fa-history  fa-2x"></i>
		</div>
		<div class="flex_main borderl ">
			<div>
				<div class="bold padding_more ">
					<span class="uppercase" style="border-bottom: 2px solid <?= $APP->colorAppscheme ?>"><?= ucfirst($nom_table) ?> </span>
					: vues <?= idioma('récentes') ?></div>
				<div class="flex_h" data-dsp_fields="codeAgent_history;heureAgent_history" data-dsp_liste="dsp_lists"
				     data-vars="table=agent_history&vars[codeAgent_history]=<?= $table ?>&vars[idagent]=<?= $_SESSION['idagent'] ?>&nbRows=5&sortBy=dateAgent_history&sortDir=-1&sortBySecond=heureAgent_history&sortDirSecond=-1"
				     data-dsp="line">
				</div>
			</div>
		</div>
	</div>
	<?php foreach ($arrDate as $type_date => $nom_type_date) {

		?>
		<div class="flex_main flex_h flex_margin flex_align_middle boxshadow" style="min-width:250px;">
			<div style="width:40px;" class="aligncenter">
				<i style="color:<?= $APP->colorAppscheme ?>" class="fa fa-calendar-o fa-2x"></i>
			</div>
			<div class="flex_main borderl">
				<div>
					<div class="bold padding_more ">
						<span class="uppercase" style="border-bottom: 2px solid <?= $APP->colorAppscheme ?>"><?= ucfirst($nom_table) ?> </span>
						: <?= $nom_type_date ?> <?= idioma('récemment') ?></div>
					<div class="flex_h" data-dsp_liste="dsp_lists" data-vars="table=<?= $table ?>&nbRows=5&sortBy=<?= $type_date . $Table ?>&sortDir=-1" data-dsp="line">
					</div>
				</div>
			</div>
		</div>
		<?

	} ?>
</div>