<?php
// $inputString is interpolated straight into the onClick attributes below as
// the first argument of fillInput(). It used to emit `$('someid')`, a call to
// the Prototype $() shim; it now emits the id as a plain string literal, which
// fillInput resolves itself (see the script at the bottom of this file).
//
// The two conditional branches that were here are gone: the unconditional
// third assignment overwrote both, so the `$('form').field` form had never
// been reachable. Kept the behaviour that actually ran.
$inputString = "'".$_POST['input']."'";
?>

<div style="width:240px;height:162px;" class="border4 blanc">
  <div class="blanc" style="width:100%;left:1px;">
    <div class="borderB mainGui" style="padding:2px; width:auto;">
      <a   onClick="fillInputForm('<?=$form?>','<?=$input?>','00');fillInputForm('<?=$form?>','type<?=$form?>','Evenement matinée');return false;"><font color="#0000FF">PM</font></a>
      &nbsp;
      |&nbsp;
      <a   onClick="fillInputForm('<?=$form?>','<?=$input?>','12');fillInputForm('<?=$form?>','type<?=$form?>','Evenement après-midi');return false;"><font color="#CC6633">AM</font></a>
      <strong>Choisir</strong>
      <span id="spyChooseNbre" ></span>
    </div>
    <table class="infos10 cursor" width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <?php
	for ($hr=7;$hr<14;$hr++) { 
	if($hr==12){$clf = ' hMidi';}else{$clf='';}
	if($hr<8){$clf = ' hMidi';}
	if($hr>18){$clf = ' hMidi';}
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':00:00'?>';" class="mainGui <?=$clf?>" onClick="fillInput(<?=$inputString?>,'<?=$hr.':00:00'?>');" nowrap="nowrap"><strong>
          <?=$hr?>
          </strong>H</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=7;$hr<14;$hr++) { 
	if($hr==12){$clf = ' hMidi';}else{$clf='';}
	if($hr<8){$clf = ' hMidi';}
	if($hr>18){$clf = ' hMidi';}
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':15:00'?>';" class="<?=$clf?>" align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':15:00'?>');">15</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=7;$hr<14;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':30:00'?>';" align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':30:00'?>');">30</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=7;$hr<14;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':45:00'?>';" align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':45:00'?>');">45</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=14;$hr<21;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':00:00'?>';" class="mainGui" onClick="fillInput(<?=$inputString?>,'<?=$hr.':00:00'?>');" nowrap="nowrap"><strong>
          <?=$hr?>
          </strong>H</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=14;$hr<21;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':15:00'?>';"  align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':15:00'?>');">15</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=14;$hr<21;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':30:00'?>';"  align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':30:00'?>');">30</td>
        <?php } ?>
      </tr>
      <tr>
        <?php
	for ($hr=14;$hr<21;$hr++) { 
	?>
        <td onMouseOver="document.getElementById('spyChooseNbre').innerHTML='<?=$hr.':45:00'?>';"  align="right" valign="middle" nowrap="nowrap" onClick="fillInput(<?=$inputString?>,'<?=$hr.':45:00'?>');">45</td>
        <?php } ?>
      </tr>
    </table>
  </div>
</div>
<script>
/*
 * Modified: 2026-08-11 — migrated off the PrototypeJS `$` shim to native DOM.
 * `input` arrives here both as an id and as an element, which is what $()
 * absorbed, so the lookup stays explicit.
 */
fillInput = function(input,vars){
	var node = typeof input === 'string' ? document.getElementById(input) : input;
	if (node) node.value = vars;
}
</script>
