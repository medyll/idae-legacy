/**
 * windowGui — the desktop window manager: builds the chrome, positions it,
 * makes it draggable, and tears it down.
 *
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 */
window.windowsGui_fragment = '<div class="containerdisp appgui_windowcolor">'
	+ '<div class="handledisp">'
	+ '<div class="cell buttondisp iconedisp aligncenter">'
	+ '<i class="fa fa-th cursor" ></i>'
	+ '</div>'
	+ '<div class="cell">'
	+ '<span data-title="" class="titlefrm"></span>'
	+ '</div>'
	+ '<div class="cell buttondisp buttonreduce aligncenter" >'
	+ '<i class="fa fa-minus cursor"></i>'
	+ '</div>'
	+ '<div class="cell buttondisp popperdisp aligncenter">'
	+ '<i class="fa fa-expand  cursor"></i>'
	+ '</div>'
	+ '<div class="cell buttondisp buttonclose aligncenter">'
	+ '<i class="fa fa-times cursor"></i>'
	+ '</div>'
	+ '</div>'
	+ '<div class="menudisp applink applinkblock" style="display: none;">'
	+ '<a class="buttonrefresh"><i class="fa fa-refresh"></i> Recharger</a><a><i class="fa fa-thumb-tack butonpin"></i> Pin to</a><a><i class="fa fa-share butonshare"></i> Partager</a><a class="buttonclose"><i class="fa fa-times"></i> Fermer</a></div>'
	+ '<div class="entetedisp" style="display: none;"></div>'
	+ '<div class="innerdisp" ></div>'
	+ '<div class="footerdisp" style="display:none"></div>'
	+ '</div>';

(function (global) {

/* ---------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files.   *
 * ---------------------------------------------------------------------- */

function wg_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function wg_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function wg_show(node) {
	if (node) node.style.display = '';
	return node;
}

function wg_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function wg_visible(node) {
	return !!node && node.style.display !== 'none';
}

function wg_siblings(node) {
	if (!node || !node.parentNode) return [];
	return Array.prototype.slice.call(node.parentNode.children).filter(function (sibling) {
		return sibling !== node;
	});
}

function wg_delegate(root, eventName, selector, handler) {
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

function wg_remove(node) {
	// Not node.remove(): the shim replaces Element.prototype.remove with
	// Prototype's, so calling it keeps this file on the shim. removeChild is
	// the same operation, natively.
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

function wg_fire(node, eventName, memo) {
	if (!node) return null;
	var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
	event.memo = memo || {};
	node.dispatchEvent(event);
	return event;
}

function wg_stop(event) {
	if (!event) return;
	event.preventDefault();
	event.stopPropagation();
}

function wg_stripTags(html) {
	return String(html).replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
}

/**
 * Scriptaculous' Effect.Fade, reduced to what the one caller needs: fade the
 * opacity out over `duration`, then take the node out of the flow and restore
 * the opacity so the next `show()` is not invisible.
 */
function wg_fadeOut(node, duration, done) {
	if (!node) return;
	var previousTransition = node.style.transition;
	node.style.transition = 'opacity ' + duration + 'ms ease-out';
	node.style.opacity = '0';
	setTimeout(function () {
		wg_hide(node);
		node.style.transition = previousTransition;
		node.style.opacity = '';
		if (done) done();
	}, duration);
}

/* ---------------------------------------------------------------------- */

var windowGui = function () {
	this.initialize.apply(this, arguments);
};

windowGui.prototype = {
	initialize: function (frm, title, rep, vars, options) {
		// console.log(frm,title,rep,vars , options)
		this.options = Object.assign({
			ident: clean_string(frm),
			className: '',
			parent: document.body,
			buttonClose: true,
			buttonReduce: true,
			hasHandle: true,
			inTask: true,
			content: false,
			icone: true,
			runonce: false,
			cookieArrayName: 'windowGui',
			onclose: '',
			startPosition: this.setLastPos.bind(this)
		}, options || {});
		this.options.scope = this.options.scope || this.options.parent

		this.container_id = 'container' + this.options.ident;

		this.parent = wg_el(this.options.parent);

		this.frm = frm;
		this.title = title;
		this.rep = rep;
		this.vars = vars || '';

		var existing = wg_el(this.options.ident);
		if (existing) {
			this.give_focus();
			if (this.options.runonce == true) {
				wg_fire(existing, 'dom:appreload', {vars: this.vars});
				return existing;
			}
			if (this.options.ajaxLoad) {
				if (typeof socket == 'object') {
					setTimeout(function () {
						wg_el(this.options.ident).socketModule(this.options.file, this.vars);
					}.bind(this), 1)

					return existing;
				}
				// FIX: fallback ajaxLoad quand socket indisponible et element deja existant
				this.ajaxLoad();
				return existing;
			}

			return existing;
		}

		this.buildDivs();
		this.centerGuiFrm();
		setTimeout(function () {
			this.give_focus();
		}.bind(this), 0)
		setTimeout(function () {
			this.container.makeOnTop();
			this.makeDraggable();
		}.bind(this), 0)
		//
		wg_hide(this.entete);

		wg_el(this.options.ident).close = this.close.bind(this);

		this.innerDisp.addEventListener('content:loaded', this.content_loaded.bind(this));

		if (this.options.ajaxLoad) {
			this.ajaxLoad();
		} else {
			this.ready()
		}
		//
		if (this.options.startPosition == 'cascade') {
			this.cascadeGuiFrm()
		}

		//
		this.container.addEventListener('dom:close', function (event) {
			wg_stop(event);
			this.close();
		}.bind(this));
		//
		//
		if (this.options.content != false) {
			this.innerDisp.innerHTML = this.options.content;
			wg_fire(this.innerDisp, 'content:loaded');
		}
		this.container.addEventListener('click', function (event) {
			this.chkDispZone();
			this.give_focus();
		}.bind(this));

		// buttonrefresh
		wg_delegate(this.container, 'click', '.buttonrefresh', function (event, node) {
			this.ajaxLoad.bind(this)
		}.bind(this))
		// buttonclose
		wg_delegate(this.container, 'click', '.buttonclose', function (event, node) {
			this.close.bind(this)
		}.bind(this))

		this.innerDisp.addEventListener('click', function (event) {
			wg_hide(this.menuDisp);
		}.bind(this));
		this.innerDisp.addEventListener('click', function (event) {
			wg_hide(this.menuDisp);
		}.bind(this));
		//
		return wg_el(this.options.ident);
	},
	content_loaded: function () {
		// Kept faithfully ineffective: the original called stopObserving with a
		// freshly-bound function, which never matches the one that was
		// attached, so content_loaded has always run on *every* content:loaded,
		// not just the first. Modules reload into innerdisp routinely, so
		// "fixing" the removal here would change behaviour, not restore it.
		this.innerDisp.removeEventListener('content:loaded', this.content_loaded.bind(this));
		if (this.options.startPosition != 'cascade') {
			this.setLastPos();
		}
		// console.log(this.innerDisp.querySelectorAll('.enteteFor'))
		var ent = this.innerDisp.querySelector('.enteteFor');
		if (ent) {
			this.entete.innerHTML = ent.innerHTML;
			wg_show(this.entete);
			wg_remove(ent);
		}
		var titreFor = this.innerDisp.querySelector('.titreFor');
		if (titreFor) {
			this.title = titreFor.innerHTML
			this.titre.innerHTML = titreFor.innerHTML;
			wg_show(this.titre);
			wg_remove(titreFor);
		}
		var footerFor = this.innerDisp.querySelector('.footerFor');
		if (footerFor) {
			this.footerDisp.innerHTML = footerFor.innerHTML;
			wg_show(this.footerDisp);
			wg_remove(footerFor);
		}
		this.chkDispZone();
		this.give_focus();

		setTimeout(function(){
			// addResizeListener(this.container, this.resize.bind(this));
		}.bind(this),1000)
	},
	resize:function(){
		var debug = {innerdisp:[this.innerDisp.offsetWidth, window.getComputedStyle(this.innerDisp).width]};
		debug['first child'] = this.innerDisp.firstElementChild
		debug['first child size'] = this.innerDisp.firstElementChild.offsetWidth
		// console.log('resize .. ',debug)
	},
	get_key: function (key) {
		var value = localStorage.getItem('app_window_pos_' + key);

		return value && JSON.parse(value);
	},
	store_key: function (key, value) {
		localStorage.setItem('app_window_pos_' + key, JSON.stringify(value));
	},
	give_focus: function () {
		// console.log(this.container_id);
		// The window may already be gone: closing it removes the container
		// while the click that closed it is still bubbling, and this handler
		// sits on the container itself. Prototype hid that — kill() called
		// purge(), which detached every observer before the event reached
		// them. Nothing native does that, and nothing should: focusing a
		// destroyed window is meaningless, so bail instead.
		var container = wg_el(this.container_id);
		if (!container) return;
		container.makeOnTop();
		if (!container.classList.contains('active')) {
			wg_siblings(container).forEach(function (sibling) {
				sibling.classList.remove('active');
			});
			container.classList.add('active');
		}

	},
	ident: function () {
		var adate = new Date();
		var ident = adate.getMilliseconds();
		ident += adate.getSeconds();
		ident += adate.getMinutes();
		return 'ident' + ident;
	},
	// construction des divs
	buildDivs: function () {
		var tmp_elem = document.createElement('div');
		tmp_elem.innerHTML = window.windowsGui_fragment;
		// ASSIGNATION
		this.container = tmp_elem.querySelector('.containerdisp');
		this.innerDisp = tmp_elem.querySelector('.innerdisp');
		this.handleDisp = tmp_elem.querySelector('.handledisp');
		this.titre = tmp_elem.querySelector('.titlefrm');
		this.entete = tmp_elem.querySelector('.entetedisp');
		this.menuDisp = tmp_elem.querySelector('.menudisp');
		this.footerDisp = tmp_elem.querySelector('.footerdisp');
		this.iconeDisp = tmp_elem.querySelector('.iconedisp');
		this.buttonClose = tmp_elem.querySelector('.buttonclose');
		this.buttonReduce = tmp_elem.querySelector('.buttonreduce');
		this.buttonPopper = tmp_elem.querySelector('.popperdisp');
		// ASSIGN
		this.container.id = this.container_id;
		this.innerDisp.id = this.options.ident;

		// MENAGE
		if (this.options.icone != true) {
			wg_remove(this.iconeDisp);
		}
		;
		if (this.options.buttonClose != true) {
			wg_remove(this.buttonClose);
		}
		if (this.options.buttonReduce != true) {
			wg_remove(this.buttonReduce);
		}
		// TPL
		if (this.options.hasHandle == true) {
			this.titre.innerHTML = this.title || '';
		}
		// APPEND
		// if(this.options.hasHandle != true){this.handleDisp.appendChild(this.entete); }


		// handle
		//if(this.options.hasHandle == true){
		// this.makeHandleDisp();
		//}

		if (this.options.className != '') {
			this.container.classList.add.apply(this.container.classList, this.options.className.split(/\s+/).filter(Boolean));
		}
		if (this.options.hasHandle == true) {
			this.titre.innerHTML = this.title || '';
		}

		this.container.loaded = true;
		this.container.style.visibility = 'visible';
		this.container.style.top = 0;
		this.container.style.left = '0';
		this.container.style.maxWidth = screen.width + 'px';

		// DOM
		this.parent.appendChild(this.container);
		// ACTIVATE
		this.clickOdrome();
	},
	clickOdrome: function () {

		wg_delegate(this.container, 'click', '.buttonclose', function (event) {
			wg_stop(event);
			this.close();
		}.bind(this));
		wg_delegate(this.container, 'click', '.buttonreduce', function (event) {
			wg_stop(event);
			this.isReduced();
		}.bind(this));
		wg_delegate(this.container, 'click', '.iconedisp', function () {
			if (wg_visible(this.menuDisp)) wg_hide(this.menuDisp); else wg_show(this.menuDisp);
		}.bind(this));
		wg_delegate(this.container, 'click', '.popperdisp', function (event) {
			var davars = {titre: this.title, mdl: this.options.file, vars: this.vars};
			localforage.setItem('app_popup', JSON.stringify(davars)).then(function () {
				//popopen('index.php?titre=' + this.title + '&mdl=' + this.options.file + '&' + this.vars, this.innerDisp.offsetWidth, this.innerDisp.offsetHeight, this.title, true);

				// this.close();
			})
			popopen('index.php?', this.innerDisp.offsetWidth, this.innerDisp.offsetHeight, wg_stripTags(this.title), true);
			localStorage.set('popup', JSON.stringify(davars));
			wg_stop(event);

		}.bind(this))//alert(this.options.file,this.vars);

		wg_delegate(this.container, 'click', '.buttonrefresh', function () {
			this.ajaxLoad();
			wg_hide(this.menuDisp)
		}.bind(this)); // ;
		wg_delegate(this.container, 'click', '.buttonshare', function () {
			this.ajaxLoad()
		}.bind(this)); // ;
		wg_delegate(this.container, 'click', '.buttonpin', function () {
			this.ajaxLoad()
		}.bind(this)); // ;
	},
	makeDraggable: function () {
		this.mydrag = new Draggabilly(this.container, {
			// options...
			handle: ('.handledisp')
		});
		this.mydrag.on('dragStart', this.startDrag.bind(this));
		this.mydrag.on('dragEnd', this.endDrag.bind(this));

	},
	startDrag: function () {
		this.innerDisp.style.visibility = 'hidden';
	},
	endDrag: function () {
		// this.mydrag.destroy();
		this.chkDispZone();
		wg_show(this.innerDisp);
		this.innerDisp.style.visibility = 'visible';

		var position_store = {top: this.container.offsetTop, left: this.container.offsetLeft, width: this.container.offsetWidth, height: this.container.offsetHeight};
		this.store_key(this.options.ident, position_store);
	},
	ajaxLoad: function () {
		//this.centerGuiFrm();
		if (typeof socket == 'object') {
			wg_el(this.options.ident).socketModule(this.options.file, this.vars);
			return;
		}
		// Socket-less fallback. Unreachable in practice — app_socket.js always
		// defines `socket` — but kept because the branch above is guarded, and
		// a silent no-op here would be worse than a request. Was an
		// Ajax.Updater with evalScripts; inline <script> tags are re-executed
		// below because injecting HTML never runs them.
		var target = wg_el(this.options.ident);
		fetch(this.rep + '.php', {
			method: 'POST',
			credentials: 'same-origin',
			headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
			body: this.vars
		}).then(function (response) {
			return response.text();
		}).then(function (html) {
			target.innerHTML = html;
			Array.prototype.slice.call(target.querySelectorAll('script')).forEach(function (old) {
				var script = document.createElement('script');
				if (old.src) script.src = old.src; else script.textContent = old.textContent;
				old.parentNode.replaceChild(script, old);
			});
			wg_fire(target, 'content:loaded');
		}).catch(function (error) {
			console.error('[windowGui] ajaxLoad failed', error);
		});
	},
	onComplete: function (transport) {
		// this.innerDisp.fire('dom:resize');

		if (this.options.startPosition == 'cascade') {
			this.cascadeGuiFrm.bind(this)
		} else {
			this.centerGuiFrm();
		}
		//afterAjaxCall(this.innerDisp);
	},
	hasCookie: function () {
		if (this.get_key(this.options.ident) != null) {
			return true;
		} else {
			return false;
		}
	},
	setLastPos: function () {
		var position_store = this.get_key(this.options.ident);
		//console.log(position_store)
		if (position_store) {
			this.container.style.top = position_store.top + 'px';
			this.container.style.left = position_store.left + 'px';
			this.container.style.visibility = 'visible';
		} else {
			this.centerGuiFrm()
		}

	},
	centerGuiFrm: function () {
		if (!this.container || !this.container.parentElement) {
			return false;
		}
		var parent = this.container.parentElement;
		var LeftPosition = (parent.offsetWidth / 2) - (this.container.offsetWidth / 2)
		var TopPosition = (parent.offsetHeight / 3) - (this.container.offsetHeight / 2)

		this.container.style.top = TopPosition + 'px';
		this.container.style.left = LeftPosition + 'px';
		this.container.style.visibility = 'visible';
		this.ready();
	},
	cascadeGuiFrm: function () {
		var container = wg_el(this.container.id);
		if (!container) {
			return false;
		}
		if (this.hasCookie()) {
			this.setLastPos();
			return
		}
		var LeftPosition = (document.body.offsetWidth / 4) + wg_siblings(container).length * 5;
		var TopPosition = wg_siblings(container).length * 25;
		this.container.style.visibility = 'visible';
		this.container.style.left = LeftPosition + 'px';
		this.ready();
	},
	ready: function () {
		this.container.style.visibility = 'visible';
		wg_show(this.container);
		// this.setLastPos();
		if (this.options.onComplete) {
			this.options.onComplete();
		}
	},
	chkDispZone: function () {
		this.options.pageOffset = 100;
		var viewportWidth = document.documentElement.clientWidth,
			viewportHeight = document.documentElement.clientHeight,
			containerWidth = this.container.offsetWidth,
			containerHeight = this.container.offsetHeight;

		var positionX = parseInt(this.container.style.left);
		var positionY = parseInt(this.container.offsetTop);

		if ((positionX + this.options.pageOffset) > viewportWidth) {
			this.container.style.left = (viewportWidth - this.options.pageOffset) + 'px';
		}
		if ((positionX + containerWidth - this.options.pageOffset) < 0) {
			this.container.style.left = (this.options.pageOffset - containerWidth) + 'px';
		}
		// if ((positionY + containerHeight - this.options.pageOffset) > viewportHeight) {
		if ((positionY + this.options.pageOffset) > viewportHeight) {
			this.container.style.top = (viewportHeight - this.options.pageOffset) + 'px';
		}
		if (positionY < 0) {
			this.container.style.top = '0px';
		}
		if (containerHeight > viewportHeight) {
			this.container.style.height = viewportHeight + 'px';
			this.container.style.top = '0px';
		}
	},
	reOpen: function () {
		this.setLastPos();
		this.container.style.visibility = 'visible';
		this.container.style.position = 'absolute';
		wg_show(this.container);
		wg_show(wg_el(this.options.ident));
		var frm = wg_el(this.frm);
		if (frm) frm.style.visibility = 'visible';
		if (this.options.ajaxLoad) {
			this.ajaxLoad();
		}
	},
	close: function (event) {
		if (event) wg_stop(event);

		this.removeFinal();
		if (this.options.onclose) {
			this.options.onclose();
		}
		var onglet = wg_el('ongl' + this.container.id);
		if (onglet) onglet.kill();
	},
	removeFinal: function () {
		if (wg_el(this.container.id)) {
			this.container.kill();
			if (this.butonInTask) {
				if (wg_el(this.butonInTask.id)) {
					this.butonInTask.kill();
				}
			}
		}
	},
	isReduced: function (event) {
		if (event) {
			wg_stop(event)
		}
		//this.container.className= 'containerdisp';
		wg_fadeOut(this.container, 300);
		window.JSGUI.addButton({vars: this.vars, container: this.container, element_id: wg_identify(this.container), taskBar: 'taskBar', onglet_id: 'ongl' + this.container.id, title: this.title});
	}


}

global.windowGui = windowGui;

})(window);
