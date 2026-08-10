/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * Called on every AJAX-loaded fragment (app_socket.js's socketModule
 * handler, app_datatable.js) to wire the `cancelClose`/`cancelClean`/
 * `cancelRemove`/`cancelHide`/`cancelButton`/`cancelToggle`/`cancelFade`
 * class-based click handlers a server-rendered form or fragment carries.
 *
 * Still calling this app's own API on purpose: `unToggleContent` and
 * `fadeElement` are native already (engine/methods.js), not Prototype's.
 */

/* ------------------------------------------------------------------ *
 * DOM helpers — file-local, same rationale as the other migrated       *
 * files (see BE_PLAN.md phase 5).                                      *
 * ------------------------------------------------------------------ */

function aac_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function aac_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function aac_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function aac_remove(node) {
	// Not node.remove(): the shim replaces Element.prototype.remove with
	// Prototype's version, so calling it would route back through the shim.
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

function aac_fire(node, eventName, memo) {
	if (!node) return null;
	var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
	event.memo = memo || {};
	node.dispatchEvent(event);
	return event;
}

/** Prototype's Element#up(): parentNode, no selector matching needed here. */
function aac_up(node) {
	return node ? node.parentElement : null;
}

/** Prototype's Form.Element#activate(): focus, then select() unless hidden. */
function aac_activate(element) {
	try {
		element.focus ();
		if (element.select && element.type !== 'hidden') element.select ();
	} catch (e) {}
	return element;
}

/**
 * Prototype's Event.element(event): normally event.target, except a load/
 * error event's target is reported as its currentTarget, and a text node
 * target is reported as its parent.
 */
function aac_eventElement(event) {
	var node = event.target, type = event.type, currentTarget = event.currentTarget;
	if (currentTarget && currentTarget.tagName) {
		if (type === 'load' || (type === 'error' && (currentTarget.tagName === 'IMG' || currentTarget.tagName === 'BODY'))) {
			node = currentTarget;
		}
	}
	return node.nodeType === 3 ? node.parentNode : node;
}

/* ------------------------------------------------------------------ */

var afterAjaxCall = function (div) {

	div = aac_el (div);
	if (!div) {
		return;
	}
	aac_identify (div);
	// Preserved verbatim, including the 3rd argument replace() has always
	// ignored (native String#replace only reads flags off a RegExp pattern,
	// not a string one — ", 'gi'" here has never done anything, shim or not,
	// so this line's behaviour is unchanged by the migration). Wraps the id
	// in an array-literal string and replaces its first '/' with '","' — a
	// no-op for the ids this function actually receives (uniqid()-generated,
	// never containing '/'), which is why frm below always ends up being
	// $(div) again, looked up by its own id.
	mdlDiv = ('["' + div.id + '"]').replace('/', '","', 'gi');
	var mdlDivArr = eval (mdlDiv);
	var frm = aac_el (mdlDivArr[mdlDivArr.length - 1]);
	if (!frm) return;
	frm.addEventListener ('dom:close', function (event) {
		//aac_hide(frm)
		//event.preventDefault();
	})
	frm.addEventListener ('click', function (event) {
		var element = aac_eventElement (event);
		if (element.classList.contains ('cancelClose')) {
			setTimeout (function(){
				aac_fire (aac_up (element), 'dom:close');
				//event.preventDefault();
			}.bind (this), 350);
		}
		if (element.classList.contains ('cancelClean')) {
			frm.innerHTML = ''
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.classList.contains ('cancelRemove')) {
			frm.innerHTML = ''
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.classList.contains ('cancelHide')) {
			aac_hide (frm)
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.classList.contains ('cancelButton')) {
			aac_fire (aac_up (aac_eventElement (event)), 'dom:close');
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.classList.contains ('cancelToggle')) {
			frm.unToggleContent ();
		}
		if (element.classList.contains ('cancelFade')) {
			// Was Scriptaculous' Effect.Fade via the shim; fadeElement
			// (engine/methods.js) is the native replacement, same contract.
			fadeElement (frm, {afterFinish: function () {
				aac_remove (frm)
			}})
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.classList.contains ('cancelRemove')) {
			aac_remove (frm);
			event.preventDefault ();
			event.stopPropagation ();
		}
		if (element.matches ('input[type=submit]')) {
			element.blur ()
		}
	}, false)

	Array.prototype.slice.call (frm.querySelectorAll ('input[type=text]')).forEach (function (element, i) {
		element.addEventListener ('focus', function (event) {
			var target = aac_eventElement (event);
			aac_activate (target);
		}.bind (this), true);
	}.bind (this))

	return frm;
};
