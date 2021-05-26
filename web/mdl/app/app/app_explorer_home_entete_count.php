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

	$RS    = $APP->find($vars);
	$count = $RS->count();
?>
<div class="aligncenter padding_more">
	<div class="padding aligncenter">
		<?=$APP->nomAppscheme?>
	</div>
	<div class="flex_h flex_inline flex_margin flex_align_middle ededed border4" style="border-width: 3px;border-color:<?=$APP->colorAppscheme?>">
		<div class="bold margin_more aligncenter ms-font-l">
			<i style="color:<?= $APP->colorAppscheme ?>" class="fa fa-<?= $APP->iconAppscheme ?>  fa-2x"></i>
		</div>
		<div class=" ms-font-l padding_more">
			<?= $count ?>
		</div>
	</div>
</div>