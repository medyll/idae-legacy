<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/bin/templates/app//app/app_fiche/app_fiche_mini.latte

use Latte\Runtime as LR;

class Template78896df0a2 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
?>
<div >
	<div    onclick="save_setting_autoNext(this ,'<?php echo $table /* line 2 */ ?>_preview_fk')" style="border-bottom:1px solid <?php
		echo $colorAppscheme /* line 2 */ ?>"  data-contextual="table=<?php echo LR\Filters::escapeHtmlAttr($table) /* line 2 */ ?>&table_value=<?php
		echo LR\Filters::escapeHtmlAttr($table_value) /* line 2 */ ?>">
		<div class="relative ">
			<div class="flex_h flex_align_middle">
				<div class="padding"><i class="fa fa-<?php echo $iconAppscheme /* line 5 */ ?>"></i></div>
				<div class="padding ">
					<div class="ellipsis"> <?php echo $nomAppscheme /* line 7 */ ?> </div>
				</div>
				<div class="padding flex_main">
					<a class="ellipisis" act_chrome_gui="app/app/app_fiche"  vars="table=<?php echo LR\Filters::escapeHtmlAttr($table) /* line 10 */ ?>&table_value=<?php
		echo LR\Filters::escapeHtmlAttr($table_value) /* line 10 */ ?>">le nom ci  </a>
				</div>
			</div>
		</div>
	</div>
	<div class="relative  " style=" ">
		<?php echo $tpl_table_fields /* line 16 */ ?>

	</div>
</div><?php
		return get_defined_vars();
	}

}
