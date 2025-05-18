<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 11/11/2015
	 * Time: 20:00
	 */
$arrstyle = ['app_gui_color'=>'Interface','app_gui_color_gui'=>'Fenetres'];
?>
<div class="autoBlock" id="color_gui_ch">
	<? foreach($arrstyle as $key=>$value){ ?>
	<div id="swcolor" code="<?=$key?>" >
		<div class="padding uppercase"><?=$value?></div>
		<div>
			<span data-color="#313942" style="background-color:#313942;" class="swatch_3d_32"></span>
			<span data-color="#89c2f3" style="background-color:#89c2f3;" class="swatch_3d_32"></span>
			<span data-color="#ffffff" style="background-color:#ffffff;" class="swatch_3d_32"></span>
			<span data-color="#333333" style="background-color:#333333;" class="swatch_3d_32"></span>
		</div>
		<div id="10143" >
			<span data-color="#A0151E" style="background-color:#A0151E;" class="swatch_3d_32"></span>
			<span data-color="#5C000F" style="background-color:#5C000F;" class="swatch_3d_32"></span>
			<span data-color="#661400" style="background-color:#661400;" class="swatch_3d_32"></span>
			<span data-color="#CC7400" style="background-color:#CC7400;" class="swatch_3d_32"></span>
			<span data-color="#FF3333" style="background-color:#FF3333;" class="swatch_3d_32"></span>
			<span data-color="#E55419" style="background-color:#E55419;" class="swatch_3d_32"></span>
			<span data-color="#994C00" style="background-color:#994C00;" class="swatch_3d_32"></span>
			<span data-color="#FFB366" style="background-color:#FFB366;" class="swatch_3d_32"></span>
			<span data-color="#FF9933" style="background-color:#FF9933;" class="swatch_3d_32"></span>
			<span data-color="#AD7F49" style="background-color:#AD7F49;" class="swatch_3d_32"></span>
			<span data-color="#57470C" style="background-color:#57470C;" class="swatch_3d_32"></span>
			<span data-color="#995700" style="background-color:#995700;" class="swatch_3d_32"></span>
			<span data-color="#FFD633" style="background-color:#FFD633;" class="swatch_3d_32"></span>
			<span data-color="#FFEA99" style="background-color:#FFEA99;" class="swatch_3d_32"></span>
			<span data-color="#FFC366" style="background-color:#FFC366;" class="swatch_3d_32"></span>
			<span data-color="#734400" style="background-color:#734400;" class="swatch_3d_32"></span>
			<span data-color="#735A34" style="background-color:#735A34;" class="swatch_3d_32"></span>
			<span data-color="#CF7806" style="background-color:#CF7806;" class="swatch_3d_32"></span>
		</div>
		<div id="9156" class="colors">
			<span data-color="#FF1629" style="background-color:#FF1629;" class="swatch_3d_32"></span>
			<span data-color="#FF16C4" style="background-color:#FF16C4;" class="swatch_3d_32"></span>
			<span data-color="#FF166C" style="background-color:#FF166C;" class="swatch_3d_32"></span>
			<span data-color="#FF2D16" style="background-color:#FF2D16;" class="swatch_3d_32"></span>
			<span data-color="#FF505E" style="background-color:#FF505E;" class="swatch_3d_32"></span>
			<span data-color="#FF8A94" style="background-color:#FF8A94;" class="swatch_3d_32"></span>
			<span data-color="#FFC5C9" style="background-color:#FFC5C9;" class="swatch_3d_32"></span>
			<span data-color="#FF50D3" style="background-color:#FF50D3;" class="swatch_3d_32"></span>
			<span data-color="#FF8AE1" style="background-color:#FF8AE1;" class="swatch_3d_32"></span>
			<span data-color="#FFC5F0" style="background-color:#FFC5F0;" class="swatch_3d_32"></span>
		</div>
		<div id="9954" class="colors">
			<span data-color="#539A2F" style="background-color:#539A2F;" class="swatch_3d_32"></span>
			<span data-color="#2F9A70" style="background-color:#2F9A70;" class="swatch_3d_32"></span>
			<span data-color="#2F6D9A" style="background-color:#2F6D9A;" class="swatch_3d_32"></span>
			<span data-color="#2F3A9A" style="background-color:#2F3A9A;" class="swatch_3d_32"></span>
			<span data-color="#5D2F9A" style="background-color:#5D2F9A;" class="swatch_3d_32"></span>
			<span data-color="#8B2A81" style="background-color:#8B2A81;" class="swatch_3d_32"></span>
			<span data-color="#7B2633" style="background-color:#7B2633;" class="swatch_3d_32"></span>
			<span data-color="#7B3726" style="background-color:#7B3726;" class="swatch_3d_32"></span>
			<span data-color="#7B5126" style="background-color:#7B5126;" class="swatch_3d_32"></span>
			<span data-color="#7B6226" style="background-color:#7B6226;" class="swatch_3d_32"></span>
			<span data-color="#7B7326" style="background-color:#7B7326;" class="swatch_3d_32"></span>
			<span data-color="#798328" style="background-color:#798328;" class="swatch_3d_32"></span>
		</div>
	</div>
	<?}?>
</div>
<style>
	.swatch_3d_32 {
		display: inline-block;
		height: 16px;
		margin: 1px;
		width: 16px;
		cursor:pointer;
		box-shadow:0 0 1px #fff inset, 0 0 3px #ccc;
		border: 1px solid #999;
	}
</style>
<script>
	$('color_gui_ch').on('click','[data-color]',function(event,node){
		var color = node.readAttribute('data-color');
		code = node.up('[code]').readAttribute('code');
		save_settings(code,color);
		setTimeout(function(){reloadModule('app/app_user_pref/app_user_pref_css','*')},1250)
	})
</script>
