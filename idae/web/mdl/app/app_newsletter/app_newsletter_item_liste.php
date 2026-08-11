<?php
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
			<?php if (!empty($idnewsletter)) {

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
					<?php
				endwhile;

			} ?>
		</div>
	</div>
</div>
<script>
	/*
	 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
	 * ($, $$, .on, .insert, .identify, .size, .first, .invoke, .collect,
	 * .readAttribute, .show, .hide, Object.toQueryString) to native DOM, with
	 * file-local `nl_` helpers.
	 */
	function nl_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function nl_qsa(selector) {
		return Array.prototype.slice.call(document.querySelectorAll(selector));
	}

	function nl_show(node) { if (node) node.style.display = ''; return node; }
	function nl_hide(node) { if (node) node.style.display = 'none'; return node; }

	/**
	 * Prototype's Element#insert({before: …}), for the two shapes used here:
	 * an HTML string, and an existing element to be moved into place.
	 */
	function nl_insertBefore(node, content) {
		if (!node) return node;
		if (typeof content === 'string') {
			node.insertAdjacentHTML('beforebegin', content);
		} else if (content && node.parentNode) {
			node.parentNode.insertBefore(content, node);
		}
		return node;
	}

	/** Prototype's Element#identify: give the node an id if it has none, return it. */
	function nl_identify(node) {
		if (!node.id) node.id = 'anonymous_element_' + Math.random().toString(36).slice(2);
		return node.id;
	}

	/**
	 * Prototype's Element#on. With a selector it delegates, calling the handler
	 * as (event, matchedElement); without one it is a plain listener.
	 */
	function nl_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (!root) return;
		if (maybeHandler === undefined) {
			root.addEventListener(eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	nl_insertBefore(nl_el('<?= $formSearch ?>'), '<div id="django" style="display:none;"  </div>')
	nl_on(document.body, 'dragstart', '[draggable]', function (event, node) {
		event.dataTransfer.effectAllowed = "move";
		event.dataTransfer.setData('dragid', nl_identify(node));
		if (nl_qsa('#django').length == 0) {
			nl_insertBefore(node, '<div id="django" class=""></div>')
		}
		node.setAttribute('dragged', 'dragged');
	})
	nl_on(nl_el('<?= $formSearch ?>'), 'dragover', '[draggable]', function (event, node) {
		nl_insertBefore(node, nl_show(nl_el('django')))
	})
	nl_on(nl_el('<?= $formSearch ?>'), 'dragend', '[draggable]', function (event, node) {
		nl_hide(nl_el('django'));
		nl_qsa('[dragged]').forEach(function (n) { n.removeAttribute('dragged'); })
	})
	nl_on(nl_el('<?= $formSearch ?>'), 'drop', '[draggable]', function (event, node) {
		if (!event.dataTransfer.getData('dragid')) return;
		this.dnd_successful = true;
	//	if (nl_qsa('[dragged]').length != 0) {
			// Was `Element.clone(node, true)`. Prototype has no Element.clone --
			// not in 1.7.3, not in the shim -- so this line has thrown
			// "Element.clone is not a function" for as long as it has existed,
			// killing the drop handler before anything below it ran. Predates
			// the idae-be migration; found 2026-08-11 by template-api-guard.spec.ts.
			// cloneNode(true) is the deep copy it was reaching for.
			var dragged = nl_qsa('[dragged]');
			var tmpdiv = dragged[0].cloneNode(true);
			tmpdiv.removeAttribute('dragged');
			// `.invoke('remove')` ran Prototype's Element#remove, which detached
			// the node — same thing removeChild does here.
			dragged.forEach(function (n) { if (n.parentNode) n.parentNode.removeChild(n); });
			nl_insertBefore(nl_el('django'), tmpdiv);
			// The original re-ran removeAttribute('dragged') over a fresh $$
			// query here. Those nodes were just detached, so the query returned
			// nothing and the call was a no-op; dropped rather than reproduced.
			nl_hide(nl_el('django'));

			//
			var pair = {};
			nl_qsa('#dropzone<?=$uniqid?> [sortable]').forEach(function (snode, index) {
				pair['ordreBlock[' + index + ']'] = snode.getAttribute('value');
			});
			var vars = new URLSearchParams(pair).toString();
			console.log(vars)
			// ajaxValidation('reorder_block_enews', 'mdl/app//app_newsletter/', vars + '&idnewsletter=<?=$idnewsletter?>&uid_grille_block=' + node.getAttribute('value'))
	//	}
	})
</script>