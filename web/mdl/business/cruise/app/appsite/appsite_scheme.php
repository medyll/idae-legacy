<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 08/11/2016
	 * Time: 16:48
	 */

	$APP      = new App();
	$APP_SITE = new AppSite();

	$filter = ['estSiteAppscheme' => 1];

	$RS_SCH = $APP_SITE->get_site_scheme()->sort(['nomAppscheme'=>1]);
?>
<div style="height:100%;">
	<div class="padding_more ededed" style="height:100%;overflow:hidden;">
		<div class="blanc border4" style="height:100%;overflow-x:hidden;overflow-y:auto;">
			<div class="applink applinkblock toggler">
				<?
					while ($ARR_SCH = $RS_SCH->getNext()) {

						$table     = $ARR_SCH['codeAppscheme'];
						$table_nom = $ARR_SCH['nomAppscheme'];
						?>
						<div class="padding_more borderb">
							<a class="autoToggle app_site_scheme_link" data-vars="table=<?=$table?>"><?= $table_nom ?></a>
						</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>