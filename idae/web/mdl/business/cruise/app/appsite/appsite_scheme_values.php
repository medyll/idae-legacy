<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 08/11/2016
	 * Time: 16:48
	 */

	include_once($_SERVER['CONF_INC']);

	$table = empty($_POST['table']) ? 'destination' : $_POST['table'];
	$Table = ucfirst($table);

	$APP      = new App($table);
	$APP_SITE = new AppSite();

	$filter = ['estSiteAppscheme' => 1];

	$RS_SCH = $APP_SITE->get_site_scheme();

	$rs = $APP->find()->sort(['estTop' . $Table => -1, 'nom' . $Table => 1])->limit(250);
?>
<div style="height:100%;">
	<div class="padding_more ededed" style="height:100%;overflow:hidden;">
		<div class="blanc border4" style="height:100%;overflow-x:hidden;overflow-y:auto;">
			<div class="padding_more aligncenter borderb blanc" style="position:sticky;top:0;z-index:10">
				<input data-quickFind data-quickFind-where="" type="text">
			</div>
			<div class="applink applinkblock toggler">
				<?
					while ($ARR = $rs->getNext()) {

						?>
						<div class="borderb">
							<a class="autoToggle app_site_scheme_link" data-vars="table=<?= $table ?>&table_value=<?= $ARR['id' . $table] ?>">
								<i class="fa fa-<?= empty($ARR['estTop' . $Table]) ? '' : 'star'; ?> fa-fw"></i>
								<?= $ARR['nom' . $Table] ?></a>
						</div>
					<? } ?>
			</div>
		</div>
	</div>
</div>