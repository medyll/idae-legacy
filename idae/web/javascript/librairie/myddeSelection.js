/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Rubber-band drag selection over a list's file zone
 * (`myddeExplorer.js:711`, act_drag_selection_zone). Behaviour unchanged,
 * including two pre-existing bugs kept verbatim — see the comments on
 * `startX` in onMouseDown and on `vp_size` in onMouseMove.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function ms_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function ms_qsa(selector, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	function ms_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function ms_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	function ms_remove(node) {
		if (node && node.parentNode) node.parentNode.removeChild(node);
		return node;
	}

	function ms_fire(node, eventName) {
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = {};
		node.dispatchEvent(event);
		return event;
	}

	/** Prototype's Event.pointerX/pointerY: page coordinates, not client ones. */
	function ms_pointerX(event) {
		return event.pageX || (event.clientX +
			(document.documentElement.scrollLeft || document.body.scrollLeft) -
			(document.documentElement.clientLeft || 0));
	}

	function ms_pointerY(event) {
		return event.pageY || (event.clientY +
			(document.documentElement.scrollTop || document.body.scrollTop) -
			(document.documentElement.clientTop || 0));
	}

	/* ------------------------------------------------------------------ */

	var myddeSelection = function () {
		this.initialize.apply(this, arguments);
	};

	myddeSelection.prototype = {
		initialize: function (element, options) {
			this.options = Object.assign({
				only: null,
				selectedClassName: 'selected'
			}, options || {});
			this.only = this.options.only;
			this.selectedClassName = this.options.selectedClassName;
			this.element = ms_el(element);
			this.selecting = false;
			this.selectableElements = {};
			// bindAsEventListener(this, this.element) passed the element as a
			// trailing bound argument after the event; none of these three
			// handlers declares a second parameter, so a plain bind(this) is
			// exactly equivalent here.
			this.element.addEventListener('mousedown', this.onMouseDown.bind(this), false);
			document.addEventListener('mouseup', this.onMouseUp.bind(this), false);
			document.addEventListener('mousemove', this.onMouseMove.bind(this), false);
		},
		checkSelect: function () {
			this.selectableElements.forEach(function (node, index) {
				rect1 = node.getBoundingClientRect();
				rect2 = ms_el('drag_selection').getBoundingClientRect();
				overlap = !(rect1.right < rect2.left ||
				rect1.left > rect2.right ||
				rect1.bottom < rect2.top ||
				rect1.top > rect2.bottom)
				if (overlap) {
					node.classList.add('selected');
					ms_qsa('input[type=checkbox]', node).forEach(function (box) { box.doCheck(); });
				} else {
					node.classList.remove('selected');
					ms_qsa('input[type=checkbox]', node).forEach(function (box) { box.doUnCheck(); });
				}
			}.bind(this));
		},
		onMouseMove: function (event) {
			var div = ms_el('drag_selection');
			if (!div || !this.selecting) return;
			if (this.initSelect) {
				this.selectableElements.forEach(function (node) { node.classList.remove('selected'); });
			}
			var mouse_pos = [ms_pointerX(event), ms_pointerY(event)];
			if (this.startX > mouse_pos[0] + 4 && this.startX > mouse_pos[0] + 4) {
				//code
			}
			if (this._last_pos && (this._last_pos.join(', ') == mouse_pos.join(', '))) return;
			this._last_pos = mouse_pos;
			if (mouse_pos[0] > div.startPos[0]) {
				ms_setStyle(div, {
					width: (mouse_pos[0] - div.startPos[0]) + 'px'
				});
			} else {
				ms_setStyle(div, {
					left: mouse_pos[0] + 'px',
					width: (div.startPos[0] - mouse_pos[0]) + 'px'
				});
			}
			if (mouse_pos[1] > div.startPos[1]) {
				ms_setStyle(div, {height: (mouse_pos[1] - div.startPos[1]) + 'px'});
			} else {
				ms_setStyle(div, {
					top: mouse_pos[1] + 'px',
					height: (div.startPos[1] - mouse_pos[1]) + 'px'
				});
			}
			// Prototype's document.viewport.getScrollOffsets() returned an
			// array-like [x, y] (indexed access works), but getDimensions()
			// returned a plain {width, height} — so `vp_size[1]` below is and
			// always was `undefined`, making the scroll-down branch dead
			// (NaN comparison). Ported verbatim, bug included: the scroll-up
			// branch is the only one that has ever run.
			var vp_pos = [global.pageXOffset || 0, global.pageYOffset || 0];
			var vp_size = {width: global.innerWidth, height: global.innerHeight};
			if (mouse_pos[1] >= vp_pos[1] + vp_size[1] - 10) {
				global.scrollTo(0, vp_pos[1] + 15);
			} else if (mouse_pos[1] <= vp_pos[1] + 10) {
				global.scrollTo(0, vp_pos[1] - 15);
			}
			this.checkSelect();
		},
		onMouseDown: function (event) {
			el = event.target;
			if(el!=this.element) return;
			if (el.tagName) {
				if (el.tagName == 'A') {
					// el.preventDefault();
					// Event.stop(el);
					return;
				}
				if (el.getAttribute('draggable') ) {
					// el.preventDefault();
					// Event.stop(el);
					return;
				}
			}
			if (el.matches(this.only)) return;
			if (el.parentElement && el.parentElement.matches(this.only)) return;
			if (el.closest('input')) return;
			event.preventDefault();
			this.selecting = true;
			this.selectableElements = ms_qsa(this.only);
			console.log(this.only);
			console.log(this.selectableElements);
			// Pre-existing bug, kept verbatim: these assign the *function*
			// Event.pointerX/Y, never its result — the original never passed
			// `event`. startY is read nowhere, and the single startX
			// comparison in onMouseMove guards an empty block, so nothing
			// observable depends on it.
			this.startX = ms_pointerX;
			this.startY = ms_pointerY;
			var div = document.createElement('div');
			div.id = 'drag_selection';
			div.className = 'drag_selection border4 fond_noir';
			div.startPos = [ms_pointerX(event), ms_pointerY(event)];
			ms_el('body').appendChild(div);
			div.style.opacity = 0.6;
			ms_setStyle(div, {position: 'absolute', left: ms_pointerX(event) + 'px', top: ms_pointerY(event) + 'px'});
			ms_fire(this.element, 'dom:unSelectionMade');
		},
		onMouseUp: function (event) {
			this.selecting = false;
			this._last_pos = null;
			this.initSelect = false;
			if (!ms_el('drag_selection')) return;
			ms_fire(this.element, 'dom:click');
			var div = ms_el('drag_selection');
			ms_remove(div);
			console.log(ms_identify(this.element) + ' .' + this.selectedClassName);
			if (ms_qsa('#' + ms_identify(this.element) + ' .' + this.selectedClassName).length > 0) ms_fire(this.element, 'dom:selectionMade');
		}
	}

	global.myddeSelection = myddeSelection;

})(window);
