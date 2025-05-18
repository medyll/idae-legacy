<?
	include_once($_SERVER['CONF_INC']);

?>
<div class="border4">
	<div class="padding borderb">Installation requise :</div>
	<div class="padding">
		<input type="button" value="Installer Idae.io" onclick="go_install();">
	</div>
	<div class="padding" id="in_install">

	</div>
</div>
<style>
	.padding { padding : 0.5em; }
	.border4 { border : 1px solid #ccc; }
	.borderb { border-bottom : 1px solid #ccc; }
</style>
<script>
	function go_install(){
		var varframe = "<iframe frameborder='0' src='<?=HTTPAPP."appconf/conf_install_go.php"?>' style='height:750px;width:750px'></iframe>";
		document.getElementById('in_install').innerHTML= varframe
	}
</script>