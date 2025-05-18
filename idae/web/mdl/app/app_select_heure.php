<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 26/11/2015
	 * Time: 20:20
	 */

	if (!empty($_POST['form'])) {
		$inputString = "$('" . $_POST['form'] . "')." . $_POST['input'];
	}
	if (empty($_POST['form'])) {
		$inputString = "$('" . $_POST['input'] . "')";
	}
	$inputString = "$('" . $_POST['input'] . "')";
	$arr_slice_noon   = [[7,14],[14,22]];
	$arr_slice   = ['00', '15', '30', '45'];
?>
<div  class="  blanc">
		<div class="borderb ededed padding applink applinkblock flex_h" style="width:auto;">
			<a class="flex_main aligncenter" 	onclick="$(this).fire('dom:act_click',{value:'PM',id:'PM'})" >PM</a>
			<a  class="flex_main aligncenter" 	onclick="$(this).fire('dom:act_click',{value:'AM',id:'AM'})" >AM</a>
		</div>
		<div >
			<div class="nth2 flex_h flex_align_top">
				<? foreach ($arr_slice_noon as $key_noon => $slice_noon): ?>
				<div class="flex_main   margin">
				<?
					for ($hr = $slice_noon[0]; $hr < $slice_noon[1]; $hr++) {
						?>
						<div class="flex_h flex_align_top borderb nth2">
							<div class="ms-font-l aligncenter padding " style="width:40px;"><a 	onclick="$(this).fire('dom:act_click',{value:'<?= $hr . ':00:00' ?>',id:'00'})" class="inline borderr padding blanc"><?= $hr ?></a></div>
							<div class=" flex_main  flex_h flex_wrap">
								<? foreach ($arr_slice as $key => $slice): ?>
									<div class="applink applinkblock flex_main  demi"  style="width:50%;max-width:50%;min-width:50%;" 	onclick="$(this).fire('dom:act_click',{value:'<?= $hr . ':'.$slice.':00'  ?>',id:'<?=$slice ?>'});">
										<a class="inline padding" > <?= $slice ?></a>
									</div>

								<? endforeach; ?>
							</div>
						</div>
					<? } ?>
				</div>

				<? endforeach; ?>
			</div>
		</div>
</div>
<script>
	fillInput = function (input, vars) {
		$(input).value = vars;
	}
</script>
