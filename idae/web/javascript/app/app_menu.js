/**
 * Created by lebru_000 on 06/11/2015.
 *
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Global contextual-menu handler: self-instantiates
 * at load (last line) and delegates clicks on `[data-menu]` anywhere in
 * the page. Load-bearing on every list — myddeExplorer's search input is
 * given `data-menu` by act_expl_search_input.
 *
 * Behaviour unchanged, including the pre-existing listener leak documented
 * on onClickBtn's removeEventListener.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function am_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function am_qsa(selector, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(selector));
	}

	function am_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function am_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function am_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	/**
	 * Prototype's Element#getDimensions: measures clientWidth/clientHeight,
	 * temporarily forcing the element visible if it is display:none (which
	 * matters here — menus are measured while still hidden).
	 */
	function am_getDimensions(node) {
		var style = node.style;
		if (window.getComputedStyle(node).display !== 'none') {
			return {width: node.clientWidth, height: node.clientHeight};
		}
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

	function am_getHeight(node) { return am_getDimensions(node).height; }
	function am_getWidth(node) { return am_getDimensions(node).width; }

	function am_cumulativeOffset(element) {
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
	 * Same hand-port already used by app_insertionQ.js, myddeDatalist.js and
	 * app_conge.js (not idae-be's, which offsets by transform).
	 */
	function am_clonePosition(target, source, options) {
		options = Object.assign({
			setLeft: true, setTop: true, setWidth: true, setHeight: true,
			offsetTop: 0, offsetLeft: 0
		}, options || {});

		var p = am_cumulativeOffset(source);
		var delta = {left: 0, top: 0};
		if (window.getComputedStyle(target).position === 'absolute') {
			delta = am_cumulativeOffset(target.offsetParent || document.documentElement);
		}

		if (options.setLeft) target.style.left = (p.left - delta.left + options.offsetLeft) + 'px';
		if (options.setTop) target.style.top = (p.top - delta.top + options.offsetTop) + 'px';
		if (options.setWidth) target.style.width = source.offsetWidth + 'px';
		if (options.setHeight) target.style.height = source.offsetHeight + 'px';
		return target;
	}

	/** Prototype's Element#update: strip <script> before innerHTML, eval the original on a 10ms defer. */
	function am_update(node, content) {
		if (content === undefined) content = '';
		content = String(content);
		node.innerHTML = engine_stripScripts(content);
		if (/<script/i.test(content)) {
			setTimeout(function () { engine_evalScripts(content); }, 10);
		}
		return node;
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function am_delegate(root, eventName, selector, handler) {
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

	var app_menu = function () {
		this.initialize.apply(this, arguments);
	};

	app_menu.prototype = {
		initialize: function (options) {
			this.options = Object.assign({}, options || {});
			this.build();
		},
		build: function () {
			this.element = document.createElement('div');
			this.element.id = 'div_app_menu';
			this.element.classList.add('context_app_menu');
			// Always a no-op: the element was just created and is not in the
			// document yet, so it has no next sibling. Kept verbatim.
			if(this.element.nextElementSibling)this.element.nextElementSibling.classList.add('context_app_menu_after');
			document.body.appendChild(this.element);
			this.hideMenu();
			am_delegate(document.body, 'click', '[data-menu]', this.onDataMenu.bind(this));
			this.element.addEventListener('content:loaded', this.repositionMenu.bind(this))
		},
		showMenu: function (x, y) {
			this.element.style.left = x + 'px';
			this.element.style.top = y + 'px';
			am_show(this.element);
		},
		hideMenu: function () {
			setTimeout(function () {
				am_hide(this.element);
			}.bind(this), 250)
		},
		onDataMenu: function (e, node) {
			e.preventDefault();
			e.stopPropagation();
			am_qsa('.context_app_menu_after').forEach(function (n) { am_hide(n); });
			// Pre-existing leak, kept verbatim: onClickBtn is added here with
			// a fresh .bind() on every [data-menu] click, and the matching
			// removeEventListener in onClickBtn binds *again* — a different
			// function object, so it never removes anything. These listeners
			// have always accumulated for the life of the page.
			document.addEventListener('click', this.onClickBtn.bind(this), false);
			var clone = node.getAttribute('data-clone');
			if (clone) {
				am_clonePosition(this.element, node, {
					setWidth: false,
					setHeight: false,
					offsetTop: am_getHeight(node)
				});
				am_update(this.element, node.nextElementSibling.innerHTML);
				am_show(this.element);
				this.repositionMenu();
				am_show(this.element);
			} else {
				var next = node.nextElementSibling;
				if (!node.getAttribute('data-menu_free')) {
					am_setStyle(next, {
						position: 'absolute'
					});
					am_show(next);
					next.classList.add('hide_on_click'); //
					am_clonePosition(next, node, {
						setWidth: false,
						setHeight: false,
						offsetTop: am_getHeight(node)
					});
				} else {
					next.classList.add('hide_on_click'); //
				}
				am_show(next);
				this.repositionMenu(next);
			}
		},
		onClickBtn: function (e, node) {
			node = e.target;
			if (node.getAttribute && node.getAttribute('data-clone')) return;
			this.hideMenu();
			document.removeEventListener('click', this.onClickBtn.bind(this));
			am_qsa('.right_clicked').forEach(function (n) { n.classList.remove('right_clicked'); });
		},
		repositionMenu: function (element) {
			var element = am_el(element) || this.element;
			if (element.getAttribute('data-menu_free')) return;
			element.makeOnTop();
			var theCSSprop = window.getComputedStyle(element.parentNode, null).getPropertyValue('overflow');
			if (theCSSprop == 'visible' || theCSSprop == 'auto') return;
			this.pageOffset = 5;
			var viewport = {width: window.innerWidth, height: window.innerHeight},
				offset = {left: window.pageXOffset || 0, top: window.pageYOffset || 0},
				containerWidth = am_getWidth(element),
				containerHeight = am_getHeight(element),
				positionX = parseInt(element.style.left),
				positionY = element.offsetTop;
			am_setStyle(element, {
				left: ((positionX + containerWidth + this.pageOffset) > viewport.width ? (viewport.width - containerWidth - this.pageOffset) : positionX) + 'px',
				top: ((positionY - offset.top + containerHeight) > viewport.height && (positionY - offset.top) > containerHeight ? (positionY - containerHeight) : positionY) + 'px'
			});
		}
	}

	global.app_menu = app_menu;

	new app_menu();

})(window);
