<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/bin/templates/app//app/app_fiche_fields/app_fiche_fields.latte

use Latte\Runtime as LR;

class Template6abc1924f3 extends Latte\Runtime\Template
{

	function main()
	{
		extract($this->params);
		?><div data-table="<?php echo LR\Filters::escapeHtmlAttr($table) /* line 1 */ ?>" data-table_value="<?php
		echo LR\Filters::escapeHtmlAttr($table_value) /* line 1 */ ?>"  data-contextual="table=<?php echo LR\Filters::escapeHtmlAttr($table) /* line 1 */ ?>&table_value=<?php
		echo LR\Filters::escapeHtmlAttr($table_value) /* line 1 */ ?>">
	<div class=" flex_h   flex_wrap">
<?php
		$iterations = 0;
		foreach ($ARR_FIELDS as $ARR_FIELD_GROUP) {
			if ($titre) {
?>			<div class="borderb textgrisfonce" style="width:100%;">
				<div class="padding_more flex_main     flex_h flex_align_middle flex_padding  relative">
					<div class=""><?php echo LR\Filters::escapeHtmlText($ARR_FIELD_GROUP['appscheme_field_group']['nomAppscheme_field_group']) /* line 9 */ ?></div>
					<div><i class="fa fa-<?php echo LR\Filters::escapeHtmlAttr($ARR_FIELD_GROUP['appscheme_field_group']['iconAppscheme_field_group']) /* line 10 */ ?> "></i></div>
				</div>
			</div>
<?php
			}
?>
			<div class=" flex_h flex_wrap flex_align_middle flex_align_stretch fiche_field_group" >
<?php
			$iterations = 0;
			foreach ($ARR_FIELD_GROUP['appscheme_fields'] as $ARR_FIELD) {
?>
					<div class="flex_h flex_wrap fiche_field" >
						<div  title="<?php echo LR\Filters::escapeHtmlAttr($ARR_FIELD['nom']) /* line 16 */ ?>"  <?php if ($_tmp = array_filter([$hide_field_title ?  'label_field_icon' : 'label_field' ])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
							<i class="fa fa-<?php echo LR\Filters::escapeHtmlAttr($ARR_FIELD['icon']) /* line 17 */ ?> item_icon textgris"></i><span><?php
				echo LR\Filters::escapeHtmlText(call_user_func($this->filters->capitalize, $ARR_FIELD['nom'])) /* line 17 */ ?></span>
						</div>
<?php
				if (($html_map_link )) {
					?>							<div class="cursor" data-module_link="fiche_map" data-vars="<?php echo LR\Filters::escapeHtmlAttr($html_map_link) /* line 20 */ ?>">
								<div class="item_icon more ">
									<i class="fa fa-map-marker"></i>
								</div>
							</div>
<?php
				}
				?>						<div class="flex_main  <?php echo LR\Filters::escapeHtmlAttr($ARR_FIELD['css_bol']) /* line 26 */ ?> ">
							<?php echo empty($edit_field) ? $ARR_FIELD['value_html'] : $ARR_FIELD['value_input'] /* line 27 */ ?>

						</div>
					</div>
<?php
				$iterations++;
			}
			if ($titre) {
?>				<div >
					<br>
				</div>
<?php
			}
?>
			</div>
<?php
			$iterations++;
		}
?>
	</div>
</div><?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['ARR_FIELD'])) trigger_error('Variable $ARR_FIELD overwritten in foreach on line 14');
		if (isset($this->params['ARR_FIELD_GROUP'])) trigger_error('Variable $ARR_FIELD_GROUP overwritten in foreach on line 6');
		
	}

}
