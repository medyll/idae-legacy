<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 26/11/2015
	 * Time: 20:20
	 */

	// $inputString used to be built here, three times, as a snippet of
	// JavaScript calling the Prototype $() shim. It was never read: nothing in
	// this file interpolates it, and the third assignment overwrote both
	// branches above it unconditionally anyway. Removed 2026-08-11 with the
	// shim migration rather than ported.
	$arr_slice_noon   = [[7,14],[14,22]];
	$arr_slice   = ['00', '15', '30', '45'];
?>
<div  class="  blanc">
		<div class="borderb ededed padding applink applinkblock flex_h" style="width:auto;">
			<a class="flex_main aligncenter" 	onclick="sh_fire(this,'dom:act_click',{value:'PM',id:'PM'})" >PM</a>
			<a  class="flex_main aligncenter" 	onclick="sh_fire(this,'dom:act_click',{value:'AM',id:'AM'})" >AM</a>
		</div>
		<div >
			<div class="nth2 flex_h flex_align_top">
				<?php foreach ($arr_slice_noon as $key_noon => $slice_noon): ?>
				<div class="flex_main   margin">
				<?php
					for ($hr = $slice_noon[0]; $hr < $slice_noon[1]; $hr++) {
						?>
						<div class="flex_h flex_align_top borderb nth2">
							<div class="ms-font-l aligncenter padding " style="width:40px;"><a 	onclick="sh_fire(this,'dom:act_click',{value:'<?= $hr . ':00:00' ?>',id:'00'})" class="inline borderr padding blanc"><?= $hr ?></a></div>
							<div class=" flex_main  flex_h flex_wrap">
								<?php foreach ($arr_slice as $key => $slice): ?>
									<div class="applink applinkblock flex_main  demi"  style="width:50%;max-width:50%;min-width:50%;" 	onclick="sh_fire(this,'dom:act_click',{value:'<?= $hr . ':'.$slice.':00'  ?>',id:'<?=$slice ?>'});">
										<a class="inline padding" > <?= $slice ?></a>
									</div>

								<?php endforeach; ?>
							</div>
						</div>
					<?php } ?>
				</div>

				<?php endforeach; ?>
			</div>
		</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS `$` shim to native
	 * DOM. `input` arrives here both as an id and as an element, which is what
	 * $() absorbed, so the lookup stays explicit.
	 */
	/** Prototype's Element#fire: a bubbling, cancelable CustomEvent carrying `memo`. */
	sh_fire = function (node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	fillInput = function (input, vars) {
		var node = typeof input === 'string' ? document.getElementById(input) : input;
		if (node) node.value = vars;
	}
</script>
