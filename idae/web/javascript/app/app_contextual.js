/**
 * Created by lebru_000 on 06/11/2015.
 *
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Right-click contextual menu: self-instantiates at
 * load (last line) and delegates `contextmenu` on `[data-contextual]`.
 *
 * Wrapped in an IIFE, unlike the original: this file's helpers would
 * otherwise collide at global scope with app_chat.js's (both are
 * global-scope files in the same page).
 *
 * Note, in contrast to app_menu.js's leak: this class stores its click
 * handler once in `this._clickHandler`, so add/removeEventListener match
 * and the listener really is removed.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function ctx_qsa(selector, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	function ctx_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function ctx_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function ctx_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	/**
	 * Prototype's Element#getDimensions: clientWidth/clientHeight, forcing
	 * the element visible first if it is display:none — which matters here,
	 * the menu is measured while still hidden.
	 */
	function ctx_getDimensions(node) {
		if (window.getComputedStyle(node).display !== 'none') {
			return {width: node.clientWidth, height: node.clientHeight};
		}
		var style = node.style;
		var originalVisibility = style.visibility,
			originalPosition = style.position,
			originalDisplay = style.display;
		style.visibility = 'hidden';
		if (originalPosition !== 'fixed') style.position = 'absolute';
		style.display = 'block';
		var width = node.clientWidth, height = node.clientHeight;
		style.display = originalDisplay;
		style.position = originalPosition;
		style.visibility = originalVisibility;
		return {width: width, height: height};
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function ctx_delegate(root, eventName, selector, handler) {
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

	var app_context = function () {
		this.initialize.apply(this, arguments);
	};

	app_context.prototype = {
		initialize: function (options) {
			this.options = Object.assign({}, options || {});
			this.build();
		},
		build: function () {
			this.element = document.createElement('div');
			this.element.id = 'app_contextual_menu';
			this.element.classList.add('contextmenu');
			this.element.setAttribute('data-cache','true');
			this._clickHandler = this.onClick.bind(this);
			document.body.appendChild(this.element);
			this.hideMenu();
			ctx_delegate(document.body, 'contextmenu', '[data-contextual]', this.onContextMenu.bind(this));
			this.element.addEventListener('content:loaded', this.repositionMenu.bind(this))
		},
		showMenu: function (x, y) {
			this.element.style.left = x + 'px';
			this.element.style.top = y + 'px';
			ctx_show(this.element);
		},

		hideMenu: function () {
			ctx_hide(this.element);
		},

		onContextMenu: function (e, node) {
			e.preventDefault();
			document.addEventListener('click', this._clickHandler, false);
			var vars = node.getAttribute('data-contextual');
			var file = 'app/app_contextual/app_contextual';
			node.classList.add('right_clicked');

			// .update() with no argument empties the element; socketModule is
			// the app's own method (engine/methods.js, already native).
			this.element.innerHTML = '';
			this.element.socketModule(file, vars);
			this.showMenu(e.pageX, e.pageY);
		},

		onClick: function (e, node) {
			node = e.target;
			if (node.getAttribute && node.getAttribute('data-menu')) return;
			this.hideMenu();
			document.removeEventListener('click', this._clickHandler, false);
			ctx_qsa('.right_clicked').forEach(function (n) { n.classList.remove('right_clicked'); });
		},
		repositionMenu: function () {

			this.pageOffset = 10;
			var viewport = {width: window.innerWidth, height: window.innerHeight},
				offset = {left: window.pageXOffset || 0, top: window.pageYOffset || 0},
				containerWidth = ctx_getDimensions(this.element).width,
				containerHeight = ctx_getDimensions(this.element).height,
				positionX = parseInt(this.element.style.left),
				positionY = this.element.offsetTop;

			ctx_setStyle(this.element, {
				left: ((positionX + containerWidth + this.pageOffset) > viewport.width ? (viewport.width - containerWidth - this.pageOffset) : positionX) + 'px',
				top: ((positionY - offset.top + containerHeight) > viewport.height && (positionY - offset.top) > containerHeight ? (positionY - containerHeight) : positionY) + 'px'
			});

		}
	}

	global.app_context = app_context;

	new app_context();

})(window);
