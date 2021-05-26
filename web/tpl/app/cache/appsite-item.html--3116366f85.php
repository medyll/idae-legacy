<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/tpl/app//appsite/item.html

use Latte\Runtime as LR;

class Template3116366f85 extends Latte\Runtime\Template
{
	public $blocks = [
		'entete_fiche' => 'blockEntete_fiche',
		'entete_fiche_detail' => 'blockEntete_fiche_detail',
		'fiche_rfk' => 'blockFiche_rfk',
		'fiche_fk' => 'blockFiche_fk',
	];

	public $blockTypes = [
		'entete_fiche' => 'html',
		'entete_fiche_detail' => 'html',
		'fiche_rfk' => 'html',
		'fiche_fk' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		return get_defined_vars();
	}


	function blockEntete_fiche($_args)
	{
		extract($_args);
?>
<div class="padding_more">
	<div class="flex_h flex_align_middle">
		<div class="padding_more">
			<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 5 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 5 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 5 */ ?>-small-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 5 */ ?>.jpg" >
		</div>
		<div class="padding_more flex_main">
			<h1><?php echo LR\Filters::escapeHtmlText($nom) /* line 8 */ ?></h1>
			<h2 ><?php echo LR\Filters::escapeHtmlText($petitNom) /* line 9 */ ?></h2>

			<div class="padding_more">
				<?php echo LR\Filters::escapeHtmlText($description) /* line 12 */ ?>

				<div class="margin_more padding_more bordert flex flex_h flex_align_middle">
					<div class="flex_main aligncenter">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 15 */ ?>">fiche</a>
					</div>
					<div class="flex_main aligncenter">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 18 */ ?>">fiche detail</a>
					</div>
					<div class="flex_main aligncenter">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 21 */ ?>" >hub</a>
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
			<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 33 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 33 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 33 */ ?>-large-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 33 */ ?>.jpg">
		</div>
		<div class="padding_more flex_main">
			<h1 class="aligncenter"><?php echo LR\Filters::escapeHtmlText($nom) /* line 36 */ ?></h1>

			<div class="  boxshadow">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 39 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 39 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 39 */ ?>-small-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 39 */ ?>.jpg"
				     style="max-width:100%">
			</div>
			<div class="margin_more padding_more bordert flex flex_h flex_align_middle">
				<div class="flex_main aligncenter">
					<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 44 */ ?>">fiche</a>
				</div>
				<div class="flex_main aligncenter">
					<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 47 */ ?>">fiche detail</a>
				</div>
				<div class="flex_main aligncenter">
					<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 50 */ ?>" >hub</a>
				</div>
			</div>
		</div>
	</div>
	<div class="padding_more">
		<?php echo LR\Filters::escapeHtmlText($information) /* line 56 */ ?>

	</div>
</div>
<?php
	}


	function blockFiche_rfk($_args)
	{
		extract($_args);
?>
<div class="padding_more">
	<div><h3><?php echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 62 */ ?></h3></div>
	<div>
		(<?php echo LR\Filters::escapeHtmlText($count) /* line 64 */ ?>)	<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['liste'])) /* line 64 */ ?>">Liste <?php
		echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 64 */ ?></a>
	</div>
</div>
<?php
	}


	function blockFiche_fk($_args)
	{
		extract($_args);
?>
<div class="padding_more border4">
	<div class="boxshadow">
		<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($HTTPCUSTOMERSITE)) /* line 71 */ ?>images_base/<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 71 */ ?>/<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table)) /* line 71 */ ?>-tiny-<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($table_value)) /* line 71 */ ?>.jpg" style="max-width:100%">
	</div>
	<div class="aligncenter"><h4><?php echo LR\Filters::escapeHtmlText($nom) /* line 73 */ ?></h4></div>
	<div class="padding_more aligncenter">
		<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 75 */ ?>">Fiche <?php
		echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 75 */ ?> <?php echo LR\Filters::escapeHtmlText($petitNom) /* line 75 */ ?></a>
		<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 76 */ ?>">Fiche détail <?php
		echo LR\Filters::escapeHtmlText($petitNom) /* line 76 */ ?></a>
	</div>
</div>
<?php
	}

}
