<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/tpl/app//appsite/page/page_fiche_detail.latte

use Latte\Runtime as LR;

class Template8ee0be94fc extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
page fiche détail
<div class="padding_more">
	$page_entete
	<div><?php echo $page_entete /* line 4 */ ?></div>
	$ liste_produit
	<?php echo $liste_produit /* line 6 */ ?>

	$page_rfk
	<div class="">
		 <?php echo $page_rfk /* line 9 */ ?>

	</div>
	<?php echo $page_carrousel /* line 11 */ ?>

	$page_fk
	<div class="flex_h flex_align_top flex_wrap">
		<?php echo $page_fk /* line 14 */ ?>

	</div>
</div>
<?php
		return get_defined_vars();
	}

}
