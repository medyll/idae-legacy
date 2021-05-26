<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 24/08/2015
	 * Time: 16:05
	 */

	include_once($_SERVER['CONF_INC']);

	$vars  = empty($_POST['vars']) ? [] : fonctionsProduction::cleanPostMongo($_POST['vars']);
	$table = empty($_POST['table']) ? 'client' : $_POST['table'];

	$vars_noagent = $vars;
	unset($vars_noagent['idagent']);
	$name_id           = 'id' . $table;
	$Table             = ucfirst($table);
	$APP               = new App($table);

	$GRILLE_RFK_BIS    = $APP->get_grille_rfk($table);

	$dateStart         = new DateTime(date('Y-m-d'));
	$dateStart->modify('-60 day');

	$zone = uniqid($table);

	if (!droit_table($_SESSION['idagent'], 'CONF', $table) && $APP->has_agent()):
		// $vars['idagent']=(int)$_SESSION['idagent'];
	endif;

	$arr_rfk = $vars;
	$sort_fk = empty($_POST['sort_fk']) ? 'dateCreation' : $_POST['sort_fk'];
	$sort_fk = empty($_POST['sort_fk']) ? 'count' : $_POST['sort_fk'];

	if (sizeof($GRILLE_RFK_BIS) == 0) return;
?>
<div class="applink applinkblock">
	<div class="padding_more boxshadowb blanc">
		Voir aussi
	</div>
	<?
		foreach ($GRILLE_RFK_BIS as $grp_fk => $arr_type) {

			?>
			<div class="padding_more borderb bold"><i class="fa fa-<?= $arr_type['iconAppscheme_type'] ?>"></i><?= $arr_type['nomAppscheme_type'] ?></div>
			<div class="flex_v retrait">
				<?
					foreach ($arr_type['appscheme'] as $table_fk => $arr_rfk) {

						?>
						<div style="width:100%;order:-<?= $arr_rfk['count'] ?>">
							<a onclick="<?= fonctionsJs::app_explorer($arr_rfk['codeAppscheme']) ?>" vars="table=<?= $arr_rfk['codeAppscheme'] ?>" class="flex_h flex_align_middle" style="width:100%;">
								<i style="color:<?= empty($arr_rfk['colorAppscheme']) ? '#ededed' : $arr_rfk['colorAppscheme'] ?>;text-shadow: #ededed 0 0 2px"
								   class="fa fa-<?= $arr_rfk['iconAppscheme'] ?>"></i>
								<span class="flex_main"><?= ucfirst($arr_rfk['nomAppscheme']) ?> </span>
								<span>(<?= $arr_rfk['count'] ?>)</span>
							</a>
						</div>
					<? } ?>
			</div>
			<?
		}

	?>
</div>
