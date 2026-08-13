// TABLE search
/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Client-side filter over a list: typing hides every
 * `tag` node whose text does not contain the query. Instantiated by
 * app_insertionQ.js:244 on any `[data-quickFind]` input — real, used on
 * ~6 screens (app_dispatch_inner, app_fiche_maxi_liste, app_scheme_*,
 * app_user_pref_scheme, appsite_scheme_values).
 *
 * Distinct from the global `quickFind(value, where, tag, spy)` function in
 * engine/engine.js, which several older templates call from inline
 * `onkeyup` attributes — same idea, unrelated code.
 *
 * Behaviour unchanged, including the broken `spy` path — see get_count().
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function qf_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function qf_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	function qf_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	/** Prototype's String#stripTags, same regex. */
	function qf_stripTags(html) {
		return String(html).replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?(\/)?>|<\/\w+>/gi, '');
	}

	/** Prototype's Function#defer: setTimeout(..., 10), args forwarded. */
	function qf_deferHide(node) {
		setTimeout(function () { node.style.display = 'none'; }, 10);
	}

	function qf_deferShow(node) {
		setTimeout(function () { node.style.display = ''; }, 10);
	}

	function qf_fire(node, eventName, memo) {
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	/* ------------------------------------------------------------------ */

	var QuickFind = function () {
		this.initialize.apply(this, arguments);
	};

	QuickFind.prototype = {
		search_icon: '<i search_icon="search_icon"  class="fa fa-search"></i>',
		spy_element: '<div spy_element="spy_element" class="inline ededed padding border4 boxshadow"></div>',
		initialize: function (element, options) {
			this.element = qf_el(element)
			this.options = Object.assign({
				where: null,
				tag: 'div',
				spy: false,
				parent: false,
				post: false
			}, options || {});
			//

			qf_identify(this.element);
			this.counter = 0;
			if (!this.options.where) {
				// console.log(this.element);
				var where_elem = this.element.nextElementSibling || (this.element.parentElement && this.element.parentElement.nextElementSibling);
				this.options.where = qf_identify(where_elem)
			}
			this.element.insertAdjacentHTML('afterend', this.search_icon);

			// Two-arg .on(event, handler) — a plain listener under the shim,
			// not delegation (shim-event.js's delegateOn falls through to
			// Event.observe when the third argument is undefined).
			this.element.addEventListener('keyup', function (event) {
				if (this.timer) clearTimeout(this.timer);
				this.timer = setTimeout(this.perform_search.bind(this), 500);
			}.bind(this));
		},
		perform_search: function () {

			var value = this.element.value.toLowerCase();
			// post
			if (this.options.post) {
				qf_qsa(qf_el(this.options.where), '[data-table_count][data-table_count_max]').forEach(function (node) {
					// enlever vars[search]
					var tmp_vars = [];
					parse_str(node.getAttribute('vars'),tmp_vars);
					tmp_vars['search']
					console.log(node.getAttribute('vars'),tmp_vars);
					// console.log(node.getAttribute('data-table_count'),node.getAttribute('data-table_count_max'));
					if (node.getAttribute('data-table_count_max') > node.getAttribute('data-table_count') || tmp_vars['search'] ) {
						console.log('red')
						qf_fire(node, 'dom:load_data', {url_data:node.getAttribute('vars')+'&search='+value});
					}
				}.bind(this))

			}

			i = 0;
			if (value === '' || value.length < 2) {
				//return
			}
			if (value === '' || value.length < 1) {
				qf_qsa(qf_el(this.options.where), this.options.tag).forEach(function (n) { n.style.display = ''; });
				if (this.options.parent) qf_qsa(qf_el(this.options.where), this.options.parent).forEach(function (n) { n.style.display = ''; });
			} else {
				qf_qsa(qf_el(this.options.where), this.options.tag).forEach(function (node) {

					var inn = qf_stripTags(node.innerHTML).toLowerCase();
					if (inn.indexOf(value) === -1) {
						qf_deferHide(node);
					} else {
						qf_deferShow(node);
						this.counter++;
					}
				}.bind(this))

				if (this.options.parent) {
					qf_qsa(qf_el(this.options.where), this.options.parent).forEach(function (node) {
						if (this.options.post) {
							var tmp_vars = [];
							parse_str(node.getAttribute('vars'),tmp_vars);
							tmp_vars['search']
							// console.log(node.getAttribute('vars'),tmp_vars);
							// console.log(node.getAttribute('data-table_count'),node.getAttribute('data-table_count_max'));
							if (node.getAttribute('data-table_count_max') > node.getAttribute('data-table_count') || tmp_vars['search'] ) {
								// console.log('red')
								qf_fire(node, 'dom:load_data', {url_data:node.getAttribute('vars')+'&search='+value});

							}
						}
						var inn = qf_stripTags(node.innerHTML).toLowerCase();
						if (inn.indexOf(value) === -1) {
							qf_deferHide(node);
						} else {
							qf_deferShow(node);
						}
					}.bind(this))

				}
			}

			if (this.options.spy) {
				this.get_count();
			}
		},
		perform_data_search : function(){

		},
		get_count: function () {
			// Pre-existing bug, ported verbatim: `spy` arrives as an element
			// id string (data-quickFind-spy, e.g. "uyt" on
			// app_scheme_field_type.php and app_scheme_has_field.php). No
			// element with that id exists anywhere in the repo, so the lookup
			// is null and the spy markup gets inserted *after* the input —
			// but the line below then searches for it *inside* the input,
			// which is an <input>, i.e. void: querySelector always returns
			// null. `this.options.spy.update(...)` then throws. Typing in
			// those two search boxes has always raised a TypeError here.
			if (!qf_el(this.options.spy)) {
				this.element.insertAdjacentHTML('afterend', this.spy_element);
				this.options.spy = this.element.querySelector('[spy_element]')
			}

			this.options.spy.innerHTML = this.counter;
		},
		get_search: function (element, value) {

		}

	}

	global.QuickFind = QuickFind;

})(window);
