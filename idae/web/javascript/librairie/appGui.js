/**
 * Modified: 2026-08-11 — `appGui` migrated off the PrototypeJS
 * compatibility shims (BE_PLAN.md phase 5). `moveElementTo` below was
 * already migrated on 2026-08-09 (496f371, Effect.Move) and is unchanged.
 *
 * `appGui` drives the desktop's tabbed app areas + task bar. One caller,
 * and a load-bearing one: `app_gui_main.php:151`
 * (`window.JSGUI = new appGui($('mainApp'))`), i.e. every boot.
 *
 * Behaviour unchanged, including the `parent.offsetLeft` bug documented in
 * activate().
 */

/**
 * Scriptaculous' Effect.Move (mode: 'absolute'), reduced to what the one
 * caller below needs: animate an already-positioned element's `left`/`top`
 * to the given absolute pixel values over `duration` seconds. Effect.Move
 * defaulted unspecified x/y to 0, so the caller passing only `x` also slid
 * `top` back to 0 — kept here explicitly rather than reproduced implicitly.
 */
function moveElementTo(node, x, y, duration) {
	if ( !node ) return node;
	x = x || 0;
	y = y || 0;
	duration = (duration || 1.0) * 1000;
	if ( window.getComputedStyle (node).position === 'static' ) node.style.position = 'relative';
	var previousTransition = node.style.transition;
	node.style.transition = 'left ' + duration + 'ms ease, top ' + duration + 'ms ease';
	node.style.left = x + 'px';
	node.style.top = y + 'px';
	setTimeout (function () {
		node.style.transition = previousTransition;
	}, duration);
	return node;
}

(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function ag_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function ag_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	function ag_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	function ag_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function ag_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function ag_remove(node) {
		// Not node.remove(): the shim replaces Element.prototype.remove with
		// Prototype's version, so calling it would route back through the shim.
		if (node && node.parentNode) node.parentNode.removeChild(node);
		return node;
	}

	/** Prototype's Element#siblings(): the parent's other element children. */
	function ag_siblings(node) {
		if (!node || !node.parentNode) return [];
		return Array.prototype.slice.call(node.parentNode.children).filter(function (el) { return el !== node; });
	}

	/** Prototype's Element#cleanWhitespace(): drop whitespace-only text children. */
	function ag_cleanWhitespace(node) {
		var child = node.firstChild;
		while (child) {
			var next = child.nextSibling;
			if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
			child = next;
		}
		return node;
	}

	function ag_fire(node, eventName) {
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = {};
		node.dispatchEvent(event);
		return event;
	}

	/** Prototype's String#gsub with a plain-string pattern: replace every occurrence. */
	function ag_gsub(str, search, replacement) {
		return String(str).split(search).join(replacement);
	}

	/* ------------------------------------------------------------------ */

	var appGui = function () {
		this.initialize.apply(this, arguments);
	};

	appGui.prototype = {
		initialize    : function (element, options) {
			this.element = ag_el (element);
			this.options = Object.assign ({
				title     : 'app ... ',
				mode      : 'slide',
				fitScreen : false
			}, options || {});
			// console.log(this.element)
			ag_cleanWhitespace (this.element);
			/*document.body.observe('resize',function(){
			 $$('.inArea').each(function(node){
			 $(node).setStyle('maxWidth:' + eval((document.body.offsetWidth)-120) + 'px')
			 })
			 }.bind(this))*/
			return this;
		},
		add           : function (options) {

			if ( ag_el (options.element_id) ) {
				this.activate (ag_el (options.element_id).options);
				return;
			}

			this.addHistButton (options);

			options.element_id = 'frm' + ag_gsub (options.title, ' ', '_').toLowerCase ();
			options.onglet_id  = 'ong' + ag_gsub (options.title, ' ', '_').toLowerCase ();
			options.container  = options.container || this.element;
			options.fitScreen  = options.fitScreen || this.options.fitScreen;

			//
			if ( ag_el (options.element_id) ) {
				if ( ag_el (options.element_id).options ) this.activate (ag_el (options.element_id).options);
				return ag_el (options.element_id);
			}
			var wrap = document.createElement ('div');
			wrap.className = 'inArea';
			ag_setStyle (wrap, { position : 'relative' })
			document.body.appendChild (wrap);
			if ( options.mainIdent ) {
				wrap.id = options.mainIdent;
			}
			var element = document.createElement ('div');
			element.id = options.element_id;
			element.setAttribute ('style', 'position:relative;height:100%;');
			ag_setStyle (element, { height : '100%' })

			if ( options.fitScreen == true ) {
				//$(wrap).setStyle({'width' :eval((document.body.offsetWidth))+'px'})
			}

			element.options = options
			//
			ag_el (options.container).appendChild (wrap);

			wrap.appendChild (element);
			/**/
			if ( options.taskBar && options.title ) {
				this.addButton (options);
			}

			this.activate (options);
			if ( options.fitScreen != true ) {
				// Two-arg .on(event, handler): a plain listener under the shim,
				// not delegation.
				element.addEventListener ('click', function () {
					if ( element.offsetLeft < (ag_el (options.container).offsetLeft * (-1)) ) this.activate (options)
				}.bind (this))
			}
			return element
		},
		addButton     : function (options) {

			if ( ag_el (options.onglet_id) ) return true;

			//
			/*if(options.vars){
			 var az = '<div class="slaveshow absolute boxshadow" style="left:0%;min-width:250px;bottom:0;z-index:20000">'
			 + '<div class="blanc" act_defer mdl="app/app/app_fiche_mini" vars="'+options.vars+'"></div>'
			 +'</div>';
			 cell2.insert(az);
			 }*/

			var frag_taskBarButton = window.APP.APPTPL['taskBarButton']
			var elem_taskBarButton = create_element_of (frag_taskBarButton);
			elem_taskBarButton.id  = options.onglet_id;

			var buttonbody  = elem_taskBarButton.querySelector ('.buttonbody');
			var inbody      = elem_taskBarButton.querySelector ('.inbody');
			var buttonmax   = elem_taskBarButton.querySelector ('.buttonmax');
			var buttonclose = elem_taskBarButton.querySelector ('.buttonclose');

			inbody.insertAdjacentHTML ('beforeend', options.title);

			ag_el (options.taskBar).appendChild (elem_taskBarButton);

			buttonmax.addEventListener ('click', function (event) {
				event.preventDefault (); event.stopPropagation ();
				davars = { titre : options.title, mdl : options.file, vars : options.vars };
				localStorage.setItem ('popup', JSON.stringify (davars));
				localStorage.setItem ('popup_master', JSON.stringify (davars));
				popopen ('index.php?titre=' + options.title + '&mdl=' + options.file + '&' + options.vars, '', '', options.title, true)
			}.bind (this))
			buttonclose.addEventListener ('click', function (event) {
				event.preventDefault (); event.stopPropagation ();
				this.close (options);
			}.bind (this))
			inbody.addEventListener ('dblclick', function (event) {
				event.preventDefault (); event.stopPropagation ();
				this.close (options);
			}.bind (this))
			inbody.addEventListener ('click', function (event) {
				if ( ag_el (options.onglet_id).classList.contains ('active') ) {
					event.preventDefault (); event.stopPropagation ();
					ag_hide (ag_el (options.container));
					ag_el (options.onglet_id).classList.remove ('active');
					return;
				}
				this.activate (options);
			}.bind (this))
			//
			this.addHistButton (options);
		},
		addHistButton : function (options) {
			var taskBar = ag_el (options.taskBar);
			if ( ag_qsa (taskBar, '.back').length == 0 && ag_qsa (taskBar, '.taskBarButton').length > 1 ) {
				var appback = document.createElement ('div');
				appback.className = 'back';
				appback.id = 'back' + options.element_id;
				appback.setAttribute ('style', 'position:relative;');
				var firstButton = ag_qsa (taskBar, '.taskBarButton')[0];
				firstButton.parentNode.insertBefore (appback, firstButton);
			}
		},
		activate      : function (options) {
			ag_show (ag_el (options.container));
			daParent = ag_el (options.element_id).parentElement || ag_el (options.element_id);
			//
			if ( options.taskBar && options.title ) {
				if(ag_el (options.onglet_id)) {
					ag_el (options.onglet_id).classList.add ('active');
					ag_siblings (ag_el (options.onglet_id)).forEach (function (n) { n.classList.remove ('active'); });
				}
			}
			if ( options.mode == 'slide' ) {
				delta = eval (daParent.offsetLeft);
				if ( options.fitScreen != true ) {
					// Pre-existing bug, kept verbatim: `parent` here is
					// window.parent, not daParent — a top-level window has no
					// offsetLeft, so this is `undefined - number` => NaN. The
					// slide below then writes "NaNpx", which CSS discards, so
					// the non-fitScreen branch has never actually moved
					// anything.
					delta = eval (parent.offsetLeft) - eval (eval (document.body.offsetWidth) / 5)
				}
				moveElementTo (ag_el (options.container), eval (-1) * delta, 0, 0.1);
				ag_show (ag_el (options.onglet_id));
			} else {
				ag_show (ag_el (options.onglet_id));
				ag_siblings (daParent).forEach (function (n) { ag_hide (n); });
				setTimeout (function () {ag_show (daParent); }.bind (this), 50)
			}

		},
		close         : function (options) {

			if ( options.taskBar && options.title ) {
				ag_remove (ag_el (options.onglet_id));
			}
			if ( ag_el (options.element_id) ) {
				ag_fire (ag_el (options.element_id), 'dom:close');
				if ( ag_el (options.element_id) != ag_el (options.container) ) {
					ag_remove (ag_el (options.element_id).parentElement);
				}
			}

			ag_hide (ag_el (options.container));
		},
		next          : function (options) {

		},
		previous      : function (options) {

		}
	}

	global.appGui = appGui;

})(window);
