/**
 * app_tree — the collapsible tree/accordion behaviour behind `[auto_tree]`
 * (structure built by app_insertionQ.js's `[auto_tree]` watcher) and
 * `[main_auto_tree]` (instantiated by that same file's watcher).
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function at_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call (root.querySelectorAll (selector));
	}

	function at_visible(node) {
		return !!node && node.style.display !== 'none';
	}

	function at_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function at_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function at_stripTags(html) {
		return String (html).replace (/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
	}

	/** Prototype's Element#up(selector): starts at the parent, never at self. */
	function at_up(node, selector) {
		if (!node || !node.parentElement) return null;
		return selector ? node.parentElement.closest (selector) : node.parentElement;
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler) —
	 * called with only (event, handler) (no selector) attaches a plain,
	 * non-delegated listener instead, matching the shim's own dual signature. */
	function at_on(root, eventName, selectorOrHandler, maybeHandler) {
		if (maybeHandler === undefined) {
			root.addEventListener (eventName, selectorOrHandler);
			return;
		}
		var selector = selectorOrHandler, handler = maybeHandler;
		root.addEventListener (eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches (selector)) {
					return handler (event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	/* ------------------------------------------------------------------ */

	var app_tree = function () {
		this.initialize.apply (this, arguments);
	};

	app_tree.prototype = {
		initialize : function (element, options) {
			this.options = {
				duration : 10
			}
			this.element = element;
			this.build ();
			this.listen();
			// data-tree
		},
		build      : function () {
			//
			if ( !this.element.classList.contains ('tree_ready') ) {
				this.element.classList.add ('tree_ready');
				// console.log(this.element)

				at_qsa (this.element, '.auto_tree').forEach (function (node) {
					if ( node.nextElementSibling ) {
						node.nextElementSibling.classList.add ('auto_tree_next')
						if ( at_visible (node.nextElementSibling) ) {
							node.classList.add ('opened')
						} else {
							node.classList.remove ('opened')
						}
					}
				}.bind (this))
				at_on (this.element, 'click', '.auto_tree_caret', function (event, node) {
					this.clicked (node);
				}.bind (this));
				at_on (this.element, 'click', '[auto_tree_click]', function (event, node) {
					this.clicked (at_up (node, '.auto_tree').querySelector ('.auto_tree_caret'));
				}.bind (this));
			}
		},
		listen      : function () {
			at_on (this.element, 'content:loaded' ,function (event) {

			})

			at_on (this.element, 'content:loaded','.auto_tree_next', function (event, node) {
				this.check(node);
			}.bind(this))
		},
		check      : function (node) {
			// auto_tree_caret

			var caret = node.previousElementSibling && node.previousElementSibling.querySelector ('.auto_tree_caret');
			var inner_node = at_stripTags (node.innerHTML).trim ();
			if ( inner_node.length == 0 ) { caret.classList.add ('hidden_caret')}else{
				caret.classList.remove ('hidden_caret')
			}

		},
		reposition : function () {

		},
		clicked    : function (node) {
			var parent = at_up (node, '.auto_tree');
			//
			parent.classList.toggle ('opened');
			if ( parent.classList.contains ('opened') ) {
				at_show (parent.nextElementSibling);
				parent.nextElementSibling.classList.remove ('none');
			} else {
				at_hide (parent.nextElementSibling);
				parent.nextElementSibling.classList.add ('none');
			}
			if ( this.element.getAttribute ('auto_tree_accordeon') ) {
				at_qsa (this.element, '.auto_tree_next').forEach (function (node) {
					if ( node !== parent.nextElementSibling ) at_hide (node);
				});
				at_qsa (this.element, '.auto_tree').forEach (function (node) {
					if ( node !== parent ) node.classList.remove ('opened');
				});
			}
		}
	}

	global.app_tree = app_tree;

})(window);
