/**
 * myddeNotifier — toast notifications (`growl()`), triggered from
 * app_socket.js's `notify`/`act_notify` handlers.
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 * The 2026-08-09 inventory only measured `Effect.Opacity` (already migrated
 * to `appearElement` — see BE_PLAN.md's shim-effects entry); this file's
 * other, much larger shim surface never showed up because the probe never
 * triggered a toast at all.
 *
 * Still calling this app's own native API on purpose: `appearElement` and
 * `loadModule` are engine/methods.js (already migrated), not the shim.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function mn_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function mn_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function mn_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function mn_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function mn_setStyle(node, styles) {
		Object.keys (styles).forEach (function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	function mn_remove(node) {
		// Not node.remove(): the shim replaces Element.prototype.remove with
		// Prototype's version, so calling it would route back through the
		// shim. The original call — n.remove({duration: 0.3}) — passed an
		// options object Prototype's real Element#remove() never accepted
		// either; it has always been ignored, shim or not (confirmed against
		// shim-element.js's own remove(), which takes no arguments at all).
		if (node && node.parentNode) node.parentNode.removeChild(node);
		return node;
	}

	function mn_insertTop(node, content) {
		if (typeof content === 'string') {
			node.insertAdjacentHTML ('afterbegin', content);
		} else {
			node.insertBefore (content, node.firstChild);
		}
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function mn_delegate(root, eventName, selector, handler) {
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

	var myddeNotifier = function () {
		this.initialize.apply (this, arguments);
	};

	myddeNotifier.prototype = {
		initialize: function (options) {
			this.options = Object.assign({
				location: "tr",
				width: "tr",
				className: 'myddeNotifier',
				vars: ''
			}, options || {});

			this.tpl = '<div id="notifierNotice flex_h"><div class=""></div><div class=""></div></div>'

			if (!mn_el ('myddeNotifier')) {
				this.growler = document.createElement ("div");
				this.growler.className = this.options.className;
				this.growler.id = "myddeNotifier";
				// Prototype's wrap(document.body): since this.growler has no
				// parent yet, its "replace self with wrapper" step is a no-op,
				// leaving only wrapper.appendChild(this) — i.e. document.body
				// adopts the fresh growler. Written directly, not via a wrap()
				// that would only be doing this one thing anyway.
				document.body.appendChild (this.growler);
			} else {
				this.growler = mn_el ('myddeNotifier');
			}
			mn_setStyle (this.growler, {width: this.options.width, zIndex: "50000"});
			switch (this.options.location) {
				case "br":
					mn_setStyle (this.growler, {bottom: 0, right: 0});
					break;
				case "tl":
					mn_setStyle (this.growler, {top: 0, left: 0});
					break;
				case "bl":
					mn_setStyle (this.growler, {top: 0, right: 0});
					break;
				case "tc":
					mn_setStyle (this.growler, {top: 0, left: "25%", width: "50%"});
					break;
				case "bc":
					mn_setStyle (this.growler, {bottom: 0, left: "25%", width: "50%"});
					break;
				default:
					//mn_setStyle(this.growler, {top: 0, right: 0});
					break;
			}

		}
		, growl: function (msg, options) {
			this.options = Object.assign({
				//location: 			"tr",
				//width: 			"250px",
				//sticky:			false
			}, options || {});
			this.buildNotice(this.growler, msg);
		}
		, buildNotice: function (growler, msg) {
			var notice;
			mn_show (this.growler);
			if (this.options.id) {

				var existing = mn_el ('notice_' + this.options.id);
				if (existing) {
					existing.insertAdjacentHTML ('beforeend', msg);
					return existing;
				}
				notice = document.createElement ("div");
				notice.className = "notifierNotice";
				notice.innerHTML = msg;
				notice.id = 'notice_' + this.options.id;
			} else {
				notice = document.createElement ("div");
				notice.className = "notifierNotice";
				notice.innerHTML = msg;
			}
			if (this.options.sticky) {

			} else {

			}
			this.growler.appendChild (notice);
			if (this.options.mdl) {
				notice.innerHTML = '<div id="in_' + mn_identify (notice) + '"></div>'
				mn_el ('in_' + mn_identify (notice)).loadModule(this.options.mdl, this.options.vars);
			}
			if (this.options.sticky) {
				mn_insertTop (notice, '<div class="titre_entete alignright closer applink applinblock"><a><i class="fa fa-times"></i> fermer</a></div> ')
				mn_delegate (notice, 'click', '.closer', function () {
					this.removeNotice(notice)
				}.bind(this));
			}
			// Was Scriptaculous' Effect.Opacity via the shim; appearElement
			// (engine/methods.js) is the native replacement, same contract.
			appearElement(notice, {to: 0.85, duration: this.options.speedin});
			if (!this.options.sticky) {
				var zz = setTimeout(function () {
					this.removeNotice(notice);
				}.bind(this), 5000)
			}


			return notice;
		},
		removeNotice: function (n) {
			mn_remove (n);
			if (this.growler.innerHTML == null || this.growler.innerHTML.trim ().length === 0) mn_hide (this.growler);
			;
		}
	}

	global.myddeNotifier = myddeNotifier;

})(window);
