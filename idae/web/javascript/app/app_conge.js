/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Drag & drop for the leave (congé) planning board:
 * `[data-dragconge]` items dropped onto `[data-dropzone=conge]` slots.
 * Same shape as app_planning.js, with a clonePosition instead of a DOM
 * re-parent. Behaviour unchanged, including the commented-out
 * ajaxValidation call — the drop is client-side only today, nothing is
 * persisted.
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function cg_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function cg_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function cg_writeAttributes(node, attrs) {
	Object.keys(attrs).forEach(function (key) {
		node.setAttribute(key, attrs[key]);
	});
	return node;
}

function cg_cumulativeOffset(element) {
	var top = 0, left = 0;
	do {
		top += element.offsetTop || 0;
		left += element.offsetLeft || 0;
		element = element.offsetParent;
	} while (element);
	return {left: left, top: top};
}

/**
 * Prototype's Element#clonePosition — place `target` over `source`.
 * Deliberately not idae-be's clonePosition (which offsets by transform and
 * takes different options); this is the same hand-port already used by
 * app_insertionQ.js and myddeDatalist.js.
 */
function cg_clonePosition(target, source, options) {
	options = Object.assign({
		setLeft: true, setTop: true, setWidth: true, setHeight: true,
		offsetTop: 0, offsetLeft: 0
	}, options || {});

	var p = cg_cumulativeOffset(source);
	var delta = {left: 0, top: 0};
	if (window.getComputedStyle(target).position === 'absolute') {
		delta = cg_cumulativeOffset(target.offsetParent || document.documentElement);
	}

	if (options.setLeft) target.style.left = (p.left - delta.left + options.offsetLeft) + 'px';
	if (options.setTop) target.style.top = (p.top - delta.top + options.offsetTop) + 'px';
	if (options.setWidth) target.style.width = source.offsetWidth + 'px';
	if (options.setHeight) target.style.height = source.offsetHeight + 'px';
	return target;
}

/** Event delegation, Prototype's Element#on(event, selector, handler). */
function cg_delegate(root, eventName, selector, handler) {
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

    cg_delegate(document.body, "dragstart", '[data-dragconge]', function (event, node) {
        node.style.opacity = '0.4'
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData('dragid', cg_identify(node));

    }.bind(this));

    cg_delegate(document.body, "dragend", '[data-dragconge]', function (event, node) {

        if (this.dnd_successful) {
            node.style.opacity = '1';
           // node.style.background = "";
            // event.target.parentNode.removeChild(event.target);
        } else {
            node.style.opacity = '1';
            //node.style.background = "";
            // $(this.element).select('[bugchk]').invoke('setOpacity', '1');
        }
    }.bind(this));

    cg_delegate(document.body, 'dragover', '[data-dropzone=conge]', function (event, node) {
        event.dataTransfer.dropEffect = "move";
        event.preventDefault();
        node.style.background = "#FC3";
        return false;
    });

    cg_delegate(document.body, 'dragleave', '[data-dropzone=conge]', function (event, node) {
        event.preventDefault();
        node.style.background = "";
        return false;
    });

   //

    // planning
    cg_delegate(document.body, 'drop', '[data-dropzone=conge]', function (event, node) {
        //
        if (!event.dataTransfer.getData('dragid')) return;
        node.style.background = "";
        this.dnd_successful = true;
        var dragid = event.dataTransfer.getData('dragid');
        event.preventDefault();
        var node_parent = node.parentNode;
        //
        var heureDebut  = node.getAttribute('heuredebut') || '',
        dateDebut       = node.getAttribute('datedebut'),
        idconge         = cg_el(dragid).getAttribute('data-idconge');
        // MAJ
	   // ajaxValidation('app_update', 'mdl/app/', 'table=conge&table_value=' + idconge + '&vars[dateDebutConge]=' + dateDebut)
        //
        cg_writeAttributes(cg_el(dragid), {datedebut: dateDebut});
        //
        console.log(cg_el(dragid))
        //
	    cg_clonePosition(cg_el(dragid), node, {setWidth:false,setHeight:false});


    }.bind(this));
	//
    cg_delegate(document.body, 'dblclick', '[data-dropconge]', function (event,element) {
        var heure = element.getAttribute('heuredebut');
        var dateDebut = element.getAttribute('datedebut');
        var idagent = element.getAttribute('data-idagent');
        ajaxMdl('app/app/app_create', 'Nouveau congé', 'table=conge&vars[heureDebutConge]=' + heure + '&vars[dateDebutConge]=' + dateDebut + '&vars[idagent]=' + idagent );
    })
