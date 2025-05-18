<?
	include_once($_SERVER['CONF_INC']);
	$APP      = new App('newsletter');
	$APP_ITEM = new App('newsletter_item');

	$uniqid        = uniqid();
	$nomNewsletter = '';
	$idnewsletter  = (int)$_POST['idnewsletter'];
	$arr           = $APP->query_one(array('idnewsletter' => (int)$idnewsletter));
	$nomNewsletter = $arr['nomNewsletter'];

	unset($_SESSION['blockid']);
	// $idnewsletter = (!empty($idnewsletter))? $idnewsletter : rand(1,1000).time() ;
	$action = (!empty($idnewsletter)) ? 'updateNewsletters' : 'createNewsletters';
	$titre  = (!empty($idnewsletter)) ? 'Mise a jour' : 'Cr&eacute;ation';
	//

	$arrTag = array('idproduit' => 'mdl_idproduit', 'titre' => 'mdl_titre', 'Atout' => 'mdl_sstitre', 'Description' => 'mdl_description', 'prix' => 'mdl_prix', 'url' => 'mdl_url');
	//
	$formSearch = 'u' . uniqid();
?>
<div class="applink applinkblock" style="overflow:auto;">
	<div class="uppercase bold titre_entete bordert">
		<?= idioma('Liste des modules') ?>
	</div>
	<div class="  applink applinkblock">
		<a act_chrome_gui="app/app/app_create"
		   vars="table=newsletter_item&vars[idnewsletter]=<?= $idnewsletter ?>"
		   options="{scope:'newsletter_item'}">
			<i class="fa fa-plus-circle" ></i><?= idioma('Nouveau') . ' block item '; ?>
		</a>
	</div>
	<div class="relative">
		<br><br>
		<div class="titre_entete">
			<?= idioma('Ordonner') ; ?>
		</div>
		<div class="  padding margin" style="position:relative;" id="<?= $formSearch ?>" sort_zone_drag="true">
			<? if (!empty($idnewsletter)) {

				$rs_item = $APP_ITEM->query(array('idnewsletter' => $idnewsletter))->sort(['ordreNewsletter_item'=>1]);
				while ($arr_item = $rs_item->getNext()):
					$idnewsletter_item  = (int)$arr_item['idnewsletter_item'];
					$uid_grille_block   = $arr_item['uid_grille_block'];
					$nomNewsletter_item = $arr_item['nomNewsletter_item'];
					$type               = $arr_item['type'];
					?>
					<div draggable="true" data-contextual="table=newsletter_item&table_value=<?= $idnewsletter_item ?>" class="flex_h flex_align_middle borderb margin" style="width:100%;position:relative;" sort_zone="sort_zone">
						<div  >
							<div class="relative" style="width: 160px;">
								<div class="ellipsis" id="element_<?= $id ?>" value="<?= $id ?>"
								     act_spy='<?= $uid_grille_mdl ?>'>
									<?= $nomNewsletter_item ?>
								</div>
							</div>
						</div>
						<div class="alignright" style="width:40px;vertical-align:top;">
							<div class="padding">
								<i class="fa fa-chevron-up sortprevious"></i>
								<i class="fa fa-chevron-down sortnext"></i>
							</div>
						</div>
					</div>
					<?
				endwhile;

			} ?>
		</div>
	</div>
</div>
<script>
	$('<?= $formSearch ?>').insert({before: '<div id="django" style="display:none;"  </div>'})
	$('body').on('dragstart', '[draggable]', function (event, node) {
		event.dataTransfer.effectAllowed = "move";
		event.dataTransfer.setData('dragid', $(node).identify());
		if ($$('#django').size() == 0) {
			node.insert({before: '<div id="django" class=""></div>'})
		}
		node.setAttribute('dragged', 'dragged');
	})
	$('<?= $formSearch ?>').on('dragover', '[draggable]', function (event, node) {
		node.insert({before: $('django').show()})
	})
	$('<?= $formSearch ?>').on('dragend', '[draggable]', function (event, node) {
		$('django').hide();
		$$('[dragged]').invoke('removeAttribute', 'dragged')
	})
	$('<?= $formSearch ?>').on('drop', '[draggable]', function (event, node) {
		if (!event.dataTransfer.getData('dragid')) return;
		this.dnd_successful = true;
	//	if ($$('[dragged]').size() != 0) {
			tmpdiv = Element.clone($$('[dragged]').first(), true);
			tmpdiv.removeAttribute('dragged');
			$$('[dragged]').invoke('remove');
			$('django').insert({before: tmpdiv});
			$$('[dragged]').invoke('removeAttribute', 'dragged');
			$('django').hide();

			//
			var pair = {};
			a = $$('#dropzone<?=$uniqid?> [sortable]').collect(function (node, index) {
				pair['ordreBlock[' + index + ']'] = node.readAttribute('value');
			});
			vars = Object.toQueryString(pair);
			console.log(vars)
			// ajaxValidation('reorder_block_enews', 'mdl/app//app_newsletter/', vars + '&idnewsletter=<?=$idnewsletter?>&uid_grille_block=' + node.readAttribute('value'))
	//	}
	})
</script>