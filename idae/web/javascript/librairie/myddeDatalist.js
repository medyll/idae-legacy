/**
 * myddeDatalist — the autocomplete dropdown attached to `[datalist]` inputs
 * (wired by app_insertionQ.js's `[datalist]` and `.heure` watchers).
 *
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * Still calling Element methods on purpose: `loadModule` and `makeOnTop` are
 * this app's own API from engine/methods.js, already native.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function dl_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	function dl_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function dl_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function dl_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function dl_visible(node) {
		return !!node && node.style.display !== 'none';
	}

	/** Prototype's Element#empty(): no content beyond whitespace. */
	function dl_empty(node) {
		return !node || node.innerHTML.trim().length === 0;
	}

	function dl_remove(node) {
		// Not node.remove(): the shim replaces Element.prototype.remove with
		// Prototype's version, so calling it would route back through the shim.
		if (node && node.parentNode) node.parentNode.removeChild(node);
		return node;
	}

	function dl_delegate(root, eventName, selector, handler) {
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

	function dl_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	function dl_stop(event) {
		if (!event) return;
		event.preventDefault();
		event.stopPropagation();
	}

	function dl_stripTags(html) {
		return String(html).replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
	}

	/** Prototype's Element#wrap: put `wrapper` where `node` is, node inside. */
	function dl_wrap(node, wrapper) {
		if (node.parentNode) node.parentNode.insertBefore(wrapper, node);
		wrapper.appendChild(node);
		return wrapper;
	}

	/** Prototype's Element#previous/next(selector): nearest matching sibling. */
	function dl_sibling(node, selector, direction) {
		var current = node && node[direction];
		while (current) {
			if (current.matches(selector)) return current;
			current = current[direction];
		}
		return null;
	}

	/** Sum of offsetTop/offsetLeft up the offsetParent chain. */
	function dl_cumulativeOffset(element) {
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
	 * Deliberately not idae-be's, which offsets by transform; the dropdown
	 * needs the top/left flavour so its own `top` override below still works.
	 */
	function dl_clonePosition(target, source, options) {
		options = Object.assign({
			setLeft: true, setTop: true, setWidth: true, setHeight: true,
			offsetTop: 0, offsetLeft: 0
		}, options || {});

		var p = dl_cumulativeOffset(source);
		var delta = {left: 0, top: 0};
		if (window.getComputedStyle(target).position === 'absolute') {
			delta = dl_cumulativeOffset(target.offsetParent || document.documentElement);
		}

		if (options.setLeft) target.style.left = (p.left - delta.left + options.offsetLeft) + 'px';
		if (options.setTop) target.style.top = (p.top - delta.top + options.offsetTop) + 'px';
		if (options.setWidth) target.style.width = source.offsetWidth + 'px';
		if (options.setHeight) target.style.height = source.offsetHeight + 'px';
		return target;
	}

	/** Prototype's Element#viewportOffset(): position relative to the viewport. */
	function dl_viewportOffset(element) {
		var rect = element.getBoundingClientRect();
		return {left: rect.left, top: rect.top};
	}

	/** Prototype's Element#scrollTo(): scroll the window to the element. */
	function dl_scrollTo(element) {
		var offset = dl_cumulativeOffset(element);
		window.scrollTo(offset.left, offset.top);
		return element;
	}

	/* ------------------------------------------------------------------ */

	var myddeDatalist = function () {
		this.initialize.apply(this, arguments);
	};

	myddeDatalist.prototype = {
		initialize: function (element, options) {
			this.element = typeof element === 'string' ? document.getElementById(element) : element;
			if (this.element.getAttribute('act_job_done')) return;
			// done :
			this.element.setAttribute('act_job_done', true);
			var mdlOption = {scope: '', vars: '', paramName: false, populate: true, autoclose: false}
			this.options = Object.assign(mdlOption, options || {});
			//
			this.timer = 0;
			this.timerSaisie = 0;
			this.state = 0;
			this.firstLoad = false;
			this.oldValue = '';


			if (this.element.getAttribute('vars')) {
				this.options.vars = this.element.getAttribute('vars');
				this.element.http_vars = this.element.getAttribute('vars');
			}
			if (this.element.getAttribute('target')) {
				this.options.target = this.element.getAttribute('target');
			}
			if (this.element.getAttribute('dsp')) {
				new resizeInput(this.element);
			}
			if (this.element.getAttribute('scope')) {
				this.options.scope = this.element.getAttribute('scope');
			}
			if (this.element.getAttribute('paramName')) {
				this.options.paramName = this.element.getAttribute('paramName');
			} else {
				this.options.paramName = this.element.getAttribute('name');
			}

			if (this.element.getAttribute('datalist')) {
				this.options.datalist = this.element.getAttribute('datalist');
			}
			if (this.element.getAttribute('populate')) {
				this.options.populate = true;
			}
			if (this.element.getAttribute('autoclose')) {
				this.options.autoclose = true;
			}

			this.prepare();
			if (this.options.populate == true) {
				this.emit_populate();
			}
			this.still_here();
		},
		prepare: function () {
			dl_identify(this.element);
			if (this.element.getAttribute('datalist_input_name')) {
				var name = this.element.getAttribute('datalist_input_name');
				this.toPick = document.createElement('input');
				this.toPick.type = 'hidden';
				this.toPick.name = name;
				this.element.after(this.toPick);
				if (this.element.getAttribute('datalist_input_value')) {
					this.toPick.value = this.element.getAttribute('datalist_input_value');
				}
			}
			if (!this.wrapper) {
				this.wrapper_holder = document.createElement('div');
				this.wrapper_holder.className = 'wrapper_holder';
				this.wrapper_holder.style.display = 'inline-block';
				this.wrapper_holder.style.position = 'relative';
				dl_wrap(this.element, this.wrapper_holder);
			}

			if (this.options.target) {
				this.datalist_element = document.getElementById(this.options.target);
				//
			} else {
				//
				this.datalist_element = document.getElementById('dtl_' + this.element.id);
				if (!this.datalist_element) {
					this.datalist_element = document.createElement('div');
					this.datalist_element.id = 'dtl_' + this.element.id;
					this.datalist_element.className = 'act_dropdown applink applinkblock';
				}
				this.datalist_element.style.position = 'absolute';
				this.datalist_element.style.maxHeight = '300px';
				this.datalist_element.style.minWidth = this.element.offsetWidth + 'px';
				this.datalist_element.style.overflow = 'auto';
				//
				this.btn = document.createElement('i');
				this.btn.className = 'fa fa-caret-down textgrisfonce';
				this.btn.style.position = 'absolute';
				this.btn.style.right = '5px';
				this.btn.style.top = '7px';

				// this.element.after(dl_hide(this.datalist_element));
				document.body.appendChild(dl_hide(this.datalist_element))
				dl_clonePosition(this.datalist_element, this.element, {setWidth: false, setHeight: false})
				var offset = dl_viewportOffset(this.element)
				this.datalist_element.style.top = (offset.top + 25) + 'px';

				this.element.after(this.btn);

			}
			this.element.setAttribute('list', this.datalist_element.id);

			this.element.addEventListener('click', this.onclick.bind(this));
			this.element.addEventListener('focus', this.onfocus.bind(this));
			this.element.addEventListener('blur', this.onblur.bind(this));
			this.element.addEventListener('keydown', this.onkeydown.bind(this), false);
			this.element.addEventListener('keyup', this.emit.bind(this));
			dl_delegate(this.datalist_element, 'click', '.avoid', this.reset_close.bind(this));
			this.datalist_element.addEventListener('dom:act_click', this.act_fire.bind(this));
			if (this.element.form) {
				this.element.form.addEventListener('submit', this.prevent_submit.bind(this), true);
			}

			return true;
		},
		onfocus: function () {
			clearTimeout(this.timer);
			if (!this.options.target) {
				dl_clonePosition(this.datalist_element, this.element, {setWidth: false, setHeight: false})
				var offset = dl_viewportOffset(this.element)
				this.datalist_element.style.top = (offset.top + 25) + 'px';
				this.datalist_element.makeOnTop();
			}

			if (dl_empty(this.datalist_element)) {
				this.emit();
			}
			if (this.options.populate) {
				dl_show(this.datalist_element);
			}
			return true;

		},
		reset_close: function (event) {
			clearTimeout(this.timer);
			this.element.focus();
			clearTimeout(this.timer);
		},
		onclick: function () {
			if (this.options.populate) {
				dl_show(this.datalist_element);
			}
		},
		onblur: function () {
			this.timer = setTimeout(function () {
				if (!this.options.target || this.options.autoclose == true) dl_hide(this.datalist_element);
			}.bind(this), 150);
		},
		onkeyup: function () {
			this.emit();
		},
		onkeydown: function (event) {

			if (event.keyCode == 39) {
				dl_stop(event);
				return false;
			} // Right Arrow
			if (event.keyCode == 37) {
				dl_stop(event);
				return false;
			}// Left Arrow

			if (event.keyCode == 38) { // Up Arrow
				dl_stop(event);
				if (this.timerSaisie) {
					clearTimeout(this.timerSaisie)
				}
				var actives = dl_qsa(this.datalist_element, '.autoToggle.active');
				if (actives.length == 0) {
					var all = dl_qsa(this.datalist_element, '.autoToggle');
					if (all.length) all[all.length - 1].classList.add('active');
				} else {
					var previous = dl_sibling(dl_qsa(this.datalist_element, '.active')[0], '.autoToggle', 'previousElementSibling');
					actives.forEach(function (node) { node.classList.remove('active'); });
					if (previous) previous.classList.add('active');
				}
				dl_scrollTo(this.element);
				return false;
			}
			if (event.keyCode == 40) { // Down Arrow
				dl_stop(event);
				if (this.timerSaisie) {
					clearTimeout(this.timerSaisie)
				}
				if (dl_qsa(this.datalist_element, 'a.active').length == 0) {
					var firstLink = dl_qsa(this.datalist_element, 'a')[0];
					if (firstLink) firstLink.classList.add('active');
				} else {
					var next = dl_sibling(dl_qsa(this.datalist_element, '.active')[0], '.autoToggle', 'nextElementSibling');
					dl_qsa(this.datalist_element, '.autoToggle.active').forEach(function (node) { node.classList.remove('active'); });
					if (next) next.classList.add('active');
				}
				dl_scrollTo(this.element);
				return false;
			}
			if (event.keyCode == 13) { // Enter (Open Item)
				dl_stop(event);
				// Guarded: with nothing highlighted this used to throw on
				// `.first().readAttribute(...)`. The keystroke is already
				// swallowed above, so bailing is the same outcome minus the
				// exception.
				var active = dl_qsa(this.datalist_element, '.active[onclick]')[0];
				if (!active) return false;
				var func = active.getAttribute('onclick');
				var identi = dl_identify(active);
				eval(func.replace("this", identi));
				return false;
			}
			return true;
		},
		prevent_submit: function (event) {
			if (dl_visible(this.datalist_element)) {
				dl_stop(event);
				console.log('yeah')
			}
		},

		act_fire: function (event) {
			var act_event;
			act_event = event;

			dl_stop(event);
			if (this.toPick && act_event.memo.id) {
				this.toPick.value = act_event.memo.id
			} else if (this.toPick && act_event.memo.value) {
				this.toPick.value = act_event.memo.value
			}
			this.element.value = act_event.memo.value;
			dl_fire(this.element, 'dom:act_change', act_event.memo);
			dl_hide(this.datalist_element);
		},
		emit_populate: function () {
			var vars;
			vars = this.options.vars;
			vars = vars + '&' + this.options.paramName
			// clearTimeout(this.timerSaisie);
			this.timerSaisie = setTimeout(function () {
				this.datalist_element.loadModule(this.options.datalist, vars, {
					scope: this.options.scope, value: this.options.scope, onComplete:this.oncomplete.bind(this)
				})
			}.bind(this), 250);

			this.oldValue = this.element.value;
		},
		emit: function () {

			//console.log(this.oldValue, ' old new ', this.element.value)
			if (this.oldValue == this.element.value) {
				// clearTimeout(this.timerSaisie);
				return;
			}
			// str_len
			/*if (dl_qsa(this.datalist_element, '[data-need_more]')[0]) {
				var need_more = dl_qsa(this.datalist_element, '[data-need_more]')[0].getAttribute('data-need_more');
				// console.log(this.oldLen, this.element.value.length);
				if (eval(need_more) == 0) {
					if ((this.oldLen || 0) > this.element.value.length) {

						this.oldLen = this.element.value.length;
					} else {
						this.oldLen = this.element.value.length;
						this.search_here();
						return;
					}
				}

			}*/
			if ((this.oldLen || 0) > this.element.value.length) {

				this.oldLen = this.element.value.length;
			} else {
				this.oldLen = this.element.value.length;
				// this.search_here();
				//return;
			}

			var vars;
			vars = this.options.vars;
			vars = vars + '&' + this.options.paramName + '=' + this.element.value

			if (this.timerSaisie) clearTimeout(this.timerSaisie);
			this.timerSaisie = setTimeout(function () {

				this.datalist_element.loadModule(this.options.datalist, vars, {
					scope: this.options.scope, value: this.options.scope, onComplete:this.oncomplete.bind(this)
				})
			}.bind(this), 250);

			this.oldValue = this.element.value;
		},
		still_here: function () {
			if (!document.getElementById(dl_identify(this.element))) {
				dl_remove(this.datalist_element);
			} else {
				setTimeout(function () {
					this.still_here()
				}.bind(this), 5000);
			}
		},
		search_here: function () {

			dl_qsa(this.datalist_element, '.app_select').forEach(function (node) {
				var inn = dl_stripTags(node.innerHTML).toLowerCase();

				if (inn.indexOf(this.element.value.toLowerCase()) === -1) {
					setTimeout(function () { dl_hide(node); }, 10);
				} else {
					setTimeout(function () { dl_show(node); }, 10);
				}
			}.bind(this))
		},
		oncomplete: function(red){
			// console.log(red)
		}
	}

	global.myddeDatalist = myddeDatalist;

})(window);
