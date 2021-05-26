<?
	include_once($_SERVER['CONF_INC']);

	$time = time();
// vardump($_POST);
	if (!empty($_POST['notify'])):
		if ($_POST['notify'] == 'reboot'):
			?>
			<script>
				alert ('Merci de relancer l\'application dès que possible (ctrl + F5)');
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'exit_application'):
			?>
			<script>
				ajaxValidation ('quitter');
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'act_close_mdl'):
			$table = $_POST['table'];
			$table_value = (int)$_POST['table_value'];
			$id = 'id'.$table;
			?>
			<script>
				console.log("close <?=$table.' '.$table_value?>");
				if(document.body.querySelector('[scope=<?=$id?>]')){
			 	$$('[scope=<?=$id?>][value=<?=$table_value?>]').invoke('fire','dom:close');
				$$('[scope=<?=$id?>][value=<?=$table_value?>]').invoke('remove');
				}
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'act_upd_mdl'):
			$table = $_POST['table'];
			$table_value = (int)$_POST['table_value'];
			$id = 'id'.$table;
			?>
			<script>
                alert('redd')
				if(document.body.querySelector('[scope=<?=$id?>]')){
				    alert('udp');
				}
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'popModule'):
			$width  = empty($_POST['width']) ? 750 : $_POST['width'];
			$height = empty($_POST['height']) ? 350 : $_POST['height'];
			?>
			<script>
				handle = 'popModule<?=niceUrl($_POST['loadModule'])?>'
				loadPop = false;
				if (!popModule) {
					loadPop = true;
				} else {
					if (!popModule.document.location) {
						loadPop = true;
					}
				}
				popModule.focus ();
				if (loadPop == true) popModule = window.open ('proxyIndex.php?mdl=<?=$_POST['loadModule']?>&<?=http_build_query($_POST['vars'])?>', handle, 'menubar=no, status=no, scrollbars=no, menubar=no, width=<?=$width?>, height=<?=$height?>,resizable = no');
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'loadModule'):
			$value = ($_POST['vars']['value'] == '*') ? 'val_' . $time : $_POST['vars']['value'];
			?>
			<script>
				ajaxMdl ('<?=$_POST['loadModule']?>', 'appLive <?=$value?>', '<?=http_build_query($_POST['vars'])?>', {value: '<?=$_POST['vars']['value']?>', ident: '<?=$value?>', className: 'widget'})
			</script>
			<?
			exit;
		endif;
		if ($_POST['notify'] == 'loadModuleOnce'):
			$value = ($_POST['vars']['value'] == '*') ? 'val_' . $time : $_POST['vars']['value'];
			?>
			<script>
				if ($$ ('[mdl="<?=$_POST['loadModule']?>"]').size () == 0) {
					ajaxMdl ('<?=$_POST['loadModule']?>', ' <?=$value?>', '<?=http_build_query($_POST['vars'])?>', {value: '<?=$_POST['vars']['value']?>'})
				}
			</script>
			<?
			exit;
		endif;
		?>
		<script>
			g = new myddeNotifier ()
			g.growl ('<?=str_replace('+',' ',$_POST['notify'])?>');
		</script>
	<?
	endif;
?>