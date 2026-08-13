/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Global click handler: checkbox toggling via
 * doCheck/doUnCheck, `.autoNext` accordion-style reveal, and dismissing
 * `.hide_on_click` popovers on any outside click. Instantiated once at
 * boot (`new selfObservers('body')`, engine/initApp.js). Behaviour
 * unchanged; the large commented-out blocks (a long-press edit-field
 * prototype) are left exactly as they were — dead comments, nothing to
 * migrate.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function so_qsa(selector, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	/** Prototype's Element#up(selector): starts at the parent, never at self. */
	function so_up(node, selector) {
		if (!node || !node.parentElement) return null;
		return selector ? node.parentElement.closest(selector) : node.parentElement;
	}

	function so_visible(node) {
		return node.style.display !== 'none';
	}

	function so_hide(node) {
		node.style.display = 'none';
		return node;
	}

	function so_show(node) {
		node.style.display = '';
		return node;
	}

	/* ------------------------------------------------------------------ */

	var selfObservers = function () {
		this.initialize.apply(this, arguments);
	};

	selfObservers.prototype = {
		initialize: function (element) {
			this.element = element


			//
			document.addEventListener('click', function (event) {
			/*	if (longpress) {
					return false;
				};*/
				elementEvent = event.target;
				if (!elementEvent) return false;
				if (elementEvent.matches('input[type=checkbox]')) {
					if (elementEvent.checked) {
						if (so_up(elementEvent, 'tr') && so_visible(so_up(elementEvent, 'tr'))) {
							elementEvent.doCheck();
						} else if (!so_up(elementEvent, 'tr')) {
							elementEvent.doCheck()
						}
						// elementEvent.doCheck()
					} else {
						elementEvent.doUnCheck()
					}
					return;
				}

				if (elementEvent.classList.contains('autoNext')) {
					var sib = elementEvent.nextElementSibling;
					if (sib) { so_visible(sib) ? so_hide(sib) : so_show(sib); }
					if (sib && so_visible(sib)) {
						elementEvent.classList.add('active');
					} else {
						elementEvent.classList.remove('active');
					}
				}
				if (!elementEvent.classList.contains('avoid') && !elementEvent.classList.contains('hide_on_click')) {
					if (!so_up(elementEvent).classList.contains('avoid') ) {
						so_qsa('.hide_on_click').forEach(function (n) { so_hide(n); });
					/*	if (  !elementEvent.up('.hide_on_click')) {
							$$('.hide_on_click').invoke('hide');
						}*/
					}
				}

			})


			var pressTimer;
			var longpress;
			/*$(document.body).on('mousedown', '[vars] [data-field_name]', function (event, node) {

				longpress = false;
				pressTimer = window.setTimeout(function () {
					// your code here $$('tr[vars] td')
					if (!$('edit_node')) {
						var edit_node = new Element('div', {id: 'edit_node', className: 'absolute blanc border4 edit_node'});
						$('body').appendChild(edit_node);
					}

					var vars = $(node).up().readAttribute('vars') || '';
					vars += '&field_name=' + $(node).readAttribute('field_name') || '';
					vars += '&field_name_raw=' + $(node).readAttribute('field_name_raw') || '';
					$('edit_node').clonePosition($(node)).show();
					//
					$('edit_node').loadModule('app/app_field_update', vars);
					longpress = true;
				}.bind(this), 1000)
			});*/

			/*document.on('mouseup', function () {
				clearTimeout(pressTimer);
			});*/
			this.shots = 0

		}
	}

	global.selfObservers = selfObservers;

})(window);
