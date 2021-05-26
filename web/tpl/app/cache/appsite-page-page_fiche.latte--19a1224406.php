<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/tpl/app//appsite/page/page_fiche.latte

use Latte\Runtime as LR;

class Template19a1224406 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>

page fiche
<div class="padding_more container-fluid blanc">
	<div><?php echo $page_entete /* line 4 */ ?></div>
	<div class="flex_h flex_align_top flex_wrap">
		<?php echo $page_calendar /* line 6 */ ?>

	</div>
	<?php echo $page_carrousel /* line 8 */ ?>

	<div class="flex_h flex_align_stretch flex_wrap">
		<?php echo $page_rfk /* line 10 */ ?>

	</div>
	<div class="flex_h flex_align_top flex_wrap">
		<?php echo $page_fk /* line 13 */ ?>

	</div>
</div>
<style>
	.carre:after {
		padding-top : 1%;
		/* 16:9 ratio  56.25% */
		display     : block;
		content     : '';
	}
</style><?php
		return get_defined_vars();
	}

}
