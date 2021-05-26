<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 11/11/2015
	 * Time: 20:00
	 */
	include_once($_SERVER['CONF_INC']);
	$APP = new App();

?>
	<style>
		<? if(!empty($APP->get_settings($_SESSION['idagent'],'app_gui_color'))){
		$app_gui_color = $APP->get_settings($_SESSION['idagent'],'app_gui_color');
		$co = hex2rgb($app_gui_color);
		$rgb  = $co[0].','.$co[1].','.$co[2];
		if (0.3*(hexdec($co[0])) + 0.59*(hexdec($co[1])) + 0.11*(hexdec($co[2])) <= 128){
		$color = "#FFFFFF";
		$color_shadow = "#333333";
		}else{
		$color = "#333333";
		$color_shadow = "#FFFFFF";
		} ?>
		.applink .active, .applink .autoToggle.active, .applink a:hover, .applink label:hover {
			color: <?=$color?>!important;
			text-shadow: 0 0 4px <?=$color_shadow?>!important;
			background-color: <?=$app_gui_color?> !important;
		}

		.frmCol1 {
			border-color: <?=$app_gui_color?> !important;
		}

		.containerdisp.active {
			border: 1px solid <?=$app_gui_color?> !important;
		}
		.taskBar .taskBarButton.active::before {
			position: absolute;width: 100%;height: 2px;display: block;content: " ";background-color: <?=$app_gui_color?>;
		}

		<?}?>
		<? if(!empty($APP->get_settings($_SESSION['idagent'],'app_gui_color_gui'))){
		$app_gui_color_gui = $APP->get_settings($_SESSION['idagent'],'app_gui_color_gui');
		$app_gui_color_gui_contrast = color_contrast($app_gui_color_gui);
		$app_gui_color_gui_contrast_contrast = color_contrast($app_gui_color_gui_contrast);
		$convert = hex2rgb($app_gui_color_gui);
		array_pop($convert);
		$app_gui_color_gui_rgba = implode(',',$convert) ;

		$co = hex2rgb($app_gui_color_gui);
		$rgb  = $co[0].','.$co[1].','.$co[2];
		?>
		.containerdisp.active .handledisp {
			background-color: rgba(<?=$app_gui_color_gui_rgba?>, 0.6) !important;
			color: <?=$app_gui_color_gui_contrast?> !important;
			font-weight: 600;
			text-shadow : 0 0 2px <?=$app_gui_color_gui_contrast_contrast?>,0 0 1px <?=$app_gui_color_gui_contrast_contrast?>;
		}
		.containerdisp .handledisp {
			background-color: rgba(<?=$app_gui_color_gui_rgba?>, 0.2) !important;
			color: <?=$app_gui_color_gui_contrast?> !important;
			text-shadow : 0 0 2px <?=$app_gui_color_gui_contrast_contrast?>;
		}
		.containerdisp .handledisp .titlefrm {
			color: <?=$app_gui_color_gui_contrast?> !important;
		}
		.containerdisp.active .handledisp .titlefrm {
			color: <?=$app_gui_color_gui_contrast?> !important;
		}
		.gradb {
			background: transparent linear-gradient(45deg, rgba(<?=$rgb?>, 0.7), rgba(<?=$rgb?>, 0.3), rgba(<?=$rgb?>, 0.4)) repeat scroll 0% 0% !important;
		}
		.taskBar  .buttonbody{display: table-cell;padding: 0 0.5em;text-shadow:0 0 2px <?=$app_gui_color_gui?>}
		<?}?>
	</style>

