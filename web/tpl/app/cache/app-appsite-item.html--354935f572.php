<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/tpl/app/appsite/item.html

use Latte\Runtime as LR;

class Template354935f572 extends Latte\Runtime\Template
{
	public $blocks = [
		'entete_fiche' => 'blockEntete_fiche',
		'entete_fiche_detail' => 'blockEntete_fiche_detail',
		'fiche_rfk' => 'blockFiche_rfk',
	];

	public $blockTypes = [
		'entete_fiche' => 'html',
		'entete_fiche_detail' => 'html',
		'fiche_rfk' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!-- START entete_fiche -->
<?php
		if ($this->getParentName()) return get_defined_vars();
?>
<!-- END entete_fiche -->
<!-- START entete_fiche_detail -->
<!-- END entete_fiche_detail -->
<!-- START fiche_rfk -->
<!-- END fiche_rfk --><?php
		return get_defined_vars();
	}


	function blockEntete_fiche($_args)
	{
		extract($_args);
?>
<div class="padding_more">
	<div class="flex_h flex_align_middle">
		<div class="padding_more">
			<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 6 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 6 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 6 */ ?>-small-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 6 */ ?>.jpg" style="max-width:100%">
		</div>
		<div class="padding_more flex_main">
			<h1><?php echo LR\Filters::escapeHtmlText($nom) /* line 9 */ ?></h1>

			<h2><?php echo LR\Filters::escapeHtmlText($petitNom) /* line 11 */ ?></h2>

			<div class="padding_more">
				<?php echo LR\Filters::escapeHtmlText($description) /* line 14 */ ?>

				<div class="margin_more padding_more bordert flex_h flex_align_middle">
					<div class="flex_main aligncenter">
						<a href="">fiche</a>
					</div>
					<div class="flex_main aligncenter">
						<a href="">fiche detail</a>
					</div>
					<div class="flex_main aligncenter">
						<a href="">liste produits</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	}


	function blockEntete_fiche_detail($_args)
	{
		extract($_args);
?>
<div class="padding_more">
	<div class="flex_h flex_align_top">
		<div class="padding_more borderr">
			<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 37 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 37 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 37 */ ?>-large-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 37 */ ?>.jpg" style="max-width:100%">
		</div>
		<div class="padding_more flex_main">
			<h1 class="aligncenter"><?php echo LR\Filters::escapeHtmlText($nom) /* line 40 */ ?></h1>

			<div class="  boxshadow">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 43 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 43 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 43 */ ?>-small-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 43 */ ?>.jpg" style="max-width:100%">
			</div>
			<div class="margin_more padding_more bordert flex flex_h flex_align_middle">
				<div class="flex_main aligncenter">
					<a href="">fiche</a>
				</div>
				<div class="flex_main aligncenter">
					<a href="">fiche detail</a>
				</div>
				<div class="flex_main aligncenter">
					<a href="">liste produits</a>
				</div>
			</div>
		</div>
	</div>
	<div class="padding_more">
		<?php echo LR\Filters::escapeHtmlText($information) /* line 59 */ ?>

	</div>
</div>
<?php
	}


	function blockFiche_rfk($_args)
	{
		extract($_args);
?>
<div class="padding flex_h flex_align_middle">
	<div>
		<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 68 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 68 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 68 */ ?>-tiny-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 68 */ ?>.jpg" style="max-width:100%">
	</div>
	<div><?php echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 70 */ ?></div>
	<div><?php echo LR\Filters::escapeHtmlText($count) /* line 71 */ ?></div>
</div>
<?php
	}

}
