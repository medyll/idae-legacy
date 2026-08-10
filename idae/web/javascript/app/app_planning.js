/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Native drag & drop for the planning/calendar view
 * ([data-dragtache]/[data-droptache]). Behaviour unchanged; `this` inside
 * every handler below is still `window` — that was already true before the
 * migration (top-level `this`, `.bind(this)` captured it as-is), not
 * something the shim provided.
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function ap_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function ap_qsa(root, selector) {
	if (!root) return [];
	return Array.prototype.slice.call(root.querySelectorAll(selector));
}

function ap_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function ap_writeAttributes(node, attrs) {
	Object.keys(attrs).forEach(function (key) {
		node.setAttribute(key, attrs[key]);
	});
	return node;
}

/** Prototype/shim setStyle: bare numbers get 'px' except known unitless CSS props. */
var AP_UNITLESS_STYLE_PROPS = {zIndex: 1, fontWeight: 1, opacity: 1, zoom: 1, lineHeight: 1};
function ap_setStyle(node, styles) {
	Object.keys(styles).forEach(function (key) {
		var value = styles[key];
		if (typeof value === 'number' && !AP_UNITLESS_STYLE_PROPS[key]) value = value + 'px';
		node.style[key] = value;
	});
	return node;
}

function ap_fire(node, eventName) {
	node.dispatchEvent(new CustomEvent(eventName, {bubbles: true, cancelable: true}));
}

/** Event delegation, Prototype's Element#on(event, selector, handler) — works for custom events too (dom:resizetache). */
function ap_delegate(root, eventName, selector, handler) {
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

/* -------------------------------------------------------------------- */

ap_delegate(document.body, "dragstart", '[data-dragtache]', function (event, node) {
	node.style.opacity = '0.4'
	event.dataTransfer.effectAllowed = "move";
	event.dataTransfer.setData('dragid', ap_identify(node));

}.bind(this));

ap_delegate(document.body, "dragend", '[data-dragtache]', function (event, node) {

	if (this.dnd_successful) {
		node.style.opacity = '1';
		node.style.background = "";
		// event.target.parentNode.removeChild(event.target);
	} else {
		node.style.opacity = '1';
		node.style.background = "";
		// $(this.element).select('[bugchk]').invoke('setOpacity', '1');
	}
}.bind(this));

ap_delegate(document.body, 'dragover', '[data-droptache]', function (event, node) {
	event.dataTransfer.dropEffect = "move";
	event.preventDefault();
	node.style.background = "#FC3";
	return false;
});

ap_delegate(document.body, 'dragleave', '[data-droptache]', function (event, node) {
	event.preventDefault();
	node.style.background = "";
	return false;
});

//

// planning
ap_delegate(document.body, 'drop', '[data-droptache]', function (event, node) {
	// console.log(event.dataTransfer)
	//
	if (!event.dataTransfer.getData('dragid')) return;
	node.style.background = "";
	this.dnd_successful = true;
	var dragid = event.dataTransfer.getData('dragid');
	event.preventDefault();
	var node_parent = node.parentNode;
	//
	var heure = node.getAttribute('heuredebut') || '',
		dateDebut = node.getAttribute('dropvalue'),
		idtache = ap_el(dragid).getAttribute('data-table_value');
	// MAJ
	if (node.hasAttribute('heuredebut')) {
		ajaxValidation('app_update', 'mdl/app/', 'table=tache&table_value=' + idtache + '&vars[dateDebutTache]=' + dateDebut + '&vars[heureDebutTache]=' + heure)
	} else {
		ajaxValidation('app_update', 'mdl/app/', 'table=tache&table_value=' + idtache + '&vars[dateDebutTache]=' + dateDebut)
	}
	//
	ap_writeAttributes(ap_el(dragid), {heuredebut: heure, datedebut: dateDebut});
	//
	if (node.getAttribute('data-droptache') == 'rebound') {
		var rebound = ap_qsa(document, '[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]');
		if (rebound.length != 0) {
			// console.log(rebound);
			rebound[0].insertAdjacentElement('beforeend', ap_el(dragid));
		} else {
			var dragged = ap_el(dragid);
			if (dragged && dragged.parentNode) dragged.parentNode.removeChild(dragged);
		}
	} else {
		// insertion en place
		node.insertAdjacentElement('beforeend', ap_el(dragid));
		ap_el(dragid).style.opacity = '1';
	}
	//
	ap_fire(node_parent, 'dom:resizetache');
	ap_fire(node, 'dom:resizetache');

}.bind(this));

ap_delegate(document.body, 'dblclick', '[data-droptache]', function (event, element) {
	var heure = element.getAttribute('heuredebut');
	var dateDebut = element.getAttribute('dropvalue');
	ajaxMdl('app/app/app_create', 'Nouvelle tache', 'table=tache&vars[heureDebutTache]=' + heure + '&vars[dateDebutTache]=' + dateDebut);
})

ap_delegate(document.body, 'dom:resizetache', '[data-droptache]', function (event, elemnt) {
	var element = elemnt;
	if (element.classList.contains('evenement') || element.classList.contains('caseMois')) return;
	ap_qsa(element, '.dyntache').forEach(function (node) {
		var att = node.getAttribute('data-heureDebut');
		var group = ap_qsa(element, '.dyntache[data-heureDebut="' + att + '"]');
		var size = group.length;
		var aa = 0;

		group.forEach(function (renode) {
			width = renode.parentNode.offsetWidth / size
			if (size == 1) {
				margin = 0;
			} else {
				margin = (width * aa)
			}
			ap_setStyle(renode, {'width': width + 'px', 'marginLeft': (margin) + 'px'});
			renode.classList.add(att)
			aa++;
		})

	}.bind(this));
	var count_z = 0;
	ap_qsa(element, '.dyntache').sort(function (a, b) {
		return a.offsetTop - b.offsetTop;
	}).forEach(function (w) {
		count_z++;
		ap_setStyle(w, {zIndex: (count_z)});
	})
});
//
