/**
 * autoToggle — delegated `.autoToggle` click handling: exactly one item
 * under a container gets `.active` at a time. Instantiated by
 * app_insertionQ.js's `.toggler` watcher and directly by app_datatable.js.
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * `autoPush` — the second class this file used to define — is dropped
 * rather than migrated: zero callers anywhere in the repo (`new autoPush(`
 * doesn't appear once outside its own definition), per this phase's own
 * rule of deleting confirmed-dead code instead of porting it.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function at_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function at_cleanWhitespace(node) {
		if (!node) return node;
		var child = node.firstChild;
		while (child) {
			var next = child.nextSibling;
			if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
			child = next;
		}
		return node;
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function at_delegate(root, eventName, selector, handler) {
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

	/* ------------------------------------------------------------------ */

	var autoToggle = function () {
		this.initialize.apply (this, arguments);
	};

	autoToggle.prototype = {
		initialize: function(element , options){
			this.options = Object.assign({
				activeClass: ' ',
				inactiveClass:  ' ',
				activeWay: 'click'
			}, options || {});

			this.element = at_el (element);
			at_cleanWhitespace (this.element);
			at_delegate (this.element, 'click', '.autoToggle', this.isclicked.bind (this));
			return this.element;
		},
		isclicked: function(event,node){
			if (event.button !== 0) return; // Prototype's Event.isLeftClick(event)
			Array.prototype.slice.call (this.element.querySelectorAll ('.autoToggle')).forEach (function (n) {
				n.classList.remove ('active');
			});
			if (this.element.classList.contains ('toggler_visible')) node.unToggleContent ()
			node.classList.add ('active')
		}
	}

	global.autoToggle = autoToggle;

})(window);
