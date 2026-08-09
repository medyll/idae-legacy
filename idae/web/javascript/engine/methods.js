/**
 * The app's own Element methods.
 *
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). These were registered through `Element.addMethods`,
 * which meant every already-migrated file still reached the shim indirectly
 * the moment it called `socketModule` or `doCheck`. They are now installed
 * directly on `HTMLElement.prototype`, the same host Prototype patched.
 *
 * The signature changed with the mechanism: `Element.addMethods` passed the
 * node as the first argument, a prototype method receives it as `this`.
 * Call sites are unaffected — `el.socketModule(file, vars)` reads the same
 * either way.
 *
 * Dropped rather than migrated (BE_PLAN phase 5: delete dead code instead of
 * porting it): `Appear`, `Fade`, `SlideDown`, `SlideUp` (Scriptaculous
 * wrappers — every `.Appear(`/`.Fade(` call in the codebase is
 * `Effect.Appear`/`Effect.Fade`, never these), plus `Print`, `toggleSrc`,
 * `animateCss`, `unCloneCopy` and `loadFragment`, which have no caller at
 * all.
 */
function prefixedCalc() {
	var prefixes = ["", "-webkit-", "-moz-", "-o-"], el
	for (var i = 0; i < prefixes.length; i++) {
		el = document.createElement('div')
		el.style.cssText = "width:" + prefixes[i] + "calc(9px)"
		if (el.style.length) return prefixes[i]
	}
}

/**
 * Scriptaculous' Effect.Fade, reduced to the three call sites left in the
 * app once the DOM migration reaches them (app_bootstrap_init.js,
 * afterAjaxCall.js, myddeAttach.js): fade opacity to 0 over `duration` ms,
 * then hide the node and restore its opacity so the next `show()` isn't
 * invisible, then call `afterFinish`.
 *
 * Shared here rather than duplicated per file (unlike the other migrated
 * files' local helpers) because it is the one piece all three callers need
 * verbatim, and this file already loads before every one of them
 * (main_bag.js's require_trame). Doing this removes the app's last caller of
 * `.fade()`, which was 1204 of the 2099 shim calls measured in BE_PLAN.md's
 * 2026-08-09 inventory — not because the call itself was hot, but because
 * shim-effects' animation loop drives Element.setOpacity on every frame.
 * Once nothing calls it, shim-effects.js can be deleted outright.
 */
function fadeElement(node, options) {
	if (!node) return node;
	options = options || {};
	var duration = (options.duration || 1.0) * 1000;
	var oldOpacity = window.getComputedStyle(node).opacity || '1';
	var previousTransition = node.style.transition;
	node.style.transition = 'opacity ' + duration + 'ms linear';
	node.style.opacity = '0';
	setTimeout(function () {
		node.style.display = 'none';
		node.style.transition = previousTransition;
		node.style.opacity = oldOpacity;
		if (options.afterFinish) options.afterFinish({element: node});
	}, duration);
	return node;
}

(function (global) {

	/* ------------------------------------------------------------------ *
	 * Local helpers                                                       *
	 * ------------------------------------------------------------------ */

	/** Prototype's `$`: an id resolves to its element, an element passes through. */
	function el_of(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function el_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function el_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function el_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function el_cleanWhitespace(node) {
		if (!node) return node;
		var child = node.firstChild;
		while (child) {
			var next = child.nextSibling;
			if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
			child = next;
		}
		return node;
	}

	function el_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent(event);
		return event;
	}

	/** Prototype's `$F`: a field's value, or null for an unchecked box. */
	function el_fieldValue(ref) {
		var field = el_of(ref);
		if (!field) return null;
		if (field.type === 'checkbox' || field.type === 'radio') return field.checked ? field.value : null;
		return field.value;
	}

	/** Install on the same host Prototype patched — see the file header. */
	function addMethods(methods) {
		Object.keys(methods).forEach(function (name) {
			Object.defineProperty(HTMLElement.prototype, name, {
				value: methods[name],
				writable: true,
				configurable: true,
				enumerable: false
			});
		});
	}

	/* ------------------------------------------------------------------ */

	addMethods({
		// Prototype's version wrapped this in purge()/remove()/purge(). purge
		// detached every observer on the node and its descendants, which had a
		// side effect nothing here relied on deliberately but everything relied
		// on in practice: handlers still queued on a node being destroyed
		// mid-dispatch never ran. There is no native equivalent — you cannot
		// enumerate listeners added with addEventListener — so callers that
		// depended on it have to tolerate running against a removed node
		// instead (see app_window.js's give_focus).
		kill: function () {
			try {
				// removeChild, not this.remove(): the shim replaces
				// Element.prototype.remove with Prototype's version, so calling
				// it would route straight back through the shim.
				if (this.parentNode) this.parentNode.removeChild(this);
			} catch (e) {
			}
		},
		doCheck: function () {
			this.checked = true;
			this.setAttribute('bugchk', 'bugchk');
			this.setAttribute('checked', true);
			var row = this.parentElement && this.parentElement.closest('tr');
			if (row) row.classList.add('selected')
		},
		doUnCheck: function () {
			this.checked = true;
			this.setAttribute('bugchk', 'bugchk');
			this.setAttribute('checked', false);
			this.setAttribute('bugchk', false);
			var row = this.parentElement && this.parentElement.closest('tr');
			if (row) row.classList.remove('selected')
		},
		doRedim: function () {
			var element = this;
			if (!element.parentElement) return element;
			var daRedimParent = element.parentNode;
			// makePositioned / relativize: both amount to making sure the pair
			// is positioned before measuring against each other.
			if (window.getComputedStyle(element).position === 'static') element.style.position = 'relative';
			if (window.getComputedStyle(daRedimParent).position === 'static') daRedimParent.style.position = 'relative';
			var parentHeight = daRedimParent.offsetHeight;
			if (parentHeight != 0) {
				var newHeight = parentHeight - element.offsetTop
				// console.log(newHeight);
				var down = element.nextElementSibling;
				if (down && down.matches('.stayDown')) {
					newHeight = newHeight - down.offsetHeight;
				}
				element.style.height = newHeight + 'px';
				Array.prototype.slice.call(element.children).forEach(function (node) { //select('.table')
					if (node.classList.contains('table')) {
						node.style.height = newHeight + 'px';
						Array.prototype.slice.call(node.querySelectorAll(':scope > .cell')).forEach(function (cell) {
							cell.style.height = newHeight + 'px'
						})
					}
				});
			}
			return element;
		},
		socketModule: function (file, davars, options) {
			var node = this;
			var DOCUMENTDOMAIN = window.document.location.host;
			var request_id = uniqid('app_cache_load');
			if (!node) {
				console.log('pas node', node);
				return;
			}
			if (node.requesting) {
				// node.makeLoading();
				// return;
			}

			var key_name = build_cache_key(file, davars);

			var element = node,
				vars = davars || '',
				ajaxOption = {request_id: request_id, cache: false, key_name: key_name};

			var id_l = 'loading_loader_' + el_identify(element);

			var options = Object.assign(ajaxOption, options || {});

			element.setAttribute('vars', vars);
			element.setAttribute('mdl', file);
			element.setAttribute('data-request_id', request_id);

			// console.log('options cache =>  ',file , options);

			// CACHE : detection de l'attribut
			if (element.getAttribute('data-cache')) {
				options.cache = true;
			}
			if (options.cache && !element.getAttribute('data-cache')) {
				element.setAttribute('data-cache', 'true');
			} //  && element.getAttribute('data-cache')=='true'
			//

			element.insertAdjacentHTML('afterbegin', '<div class="none noneinfinite_loader absolute" id="' + id_l + '"></div>');

			if (element.tagName.toUpperCase() == 'DIV' && (options.seeLoading)) {
				// element.insertAdjacentHTML('beforeend', '<div class="loading" style="height:100%;width:'+element.offsetWidth+'px;top:0;left:0;z-index:4000;position:absolute;"></div>');
			}

			var opt = {
				DOCUMENTDOMAIN: DOCUMENTDOMAIN,
				element: el_identify(element),
				file: file,
				vars: vars,
				options: options
			}
			var sessid_field = document.getElementById('SESSID');
			if (sessid_field) opt.SESSID = sessid_field.value;
			/*if (localStorage.getItem('SSSAVEPATH')) {
				opt.SSSAVEPATH = localStorage.getItem('SSSAVEPATH');
			}*/
			if (localStorage.getItem('PHPSESSID')) {
				opt.PHPSESSID = localStorage.getItem('PHPSESSID');
			}
			if (localStorage.getItem('SESSID')) {
				opt.SESSID = localStorage.getItem('SESSID');
			}

			// CACHE

			if ((localStorage.getItem('cache_mode') == 'on')) {
				// console.log('cache ', key_name, options.cache);

				if (options.cache == true) { // && file.indexOf("select")  == -1 &&  file.indexOf("update")  == -1
					app_cache.getItem(key_name, function (err, value) {
						// console.log( 'localforage cache_mode' , 'key_name',key_name,'value',value);
						if (err) {
							console.error('Oh noes!');
						} else {
							// console.log('test from cache');
							if (!empty(value) && !empty(value.data_body)) {
								//  console.log(file+' from cache');
								element.innerHTML = value.data_body;
								element.setAttribute('data-from_cache', 'true');
								afterAjaxCall(element);
								el_fire(element, 'content:loaded');
								socket.emit('socketModule', opt);
								return element;
							} else {
								// console.log('please do from cache');
								element.setAttribute('data-need_cache', 'true');
							}
						}
					});

				}

			} else {

			}
			//
			node.requesting = true;
			socket.emit('socketModule', opt, function (result) {
				if (options.onComplete) {
					var onComplete = options.onComplete
					if (typeof onComplete === 'function') onComplete(result);
				}
				node.requesting = false;
				node.undoLoading();
			}.bind(this));

			return element;
		},
		loadModule: function (file, vars, options) {
			//ajaxOption = {}
			//options = Object.assign(ajaxOption, options || {});
			// ajaxInMdl => onglet ... => socketModule
			ajaxInMdl(file, this, vars, options);
			return this;
		},
		toggleContent: function () {
			var frm = this;
			var test = window.getComputedStyle(frm).position;
			el_show(frm);
			frm.style.position = 'absolute';
			el_cleanWhitespace(frm.parentNode);
			Array.prototype.slice.call(frm.parentNode.children).forEach(function (node) {
				if (node === frm) return;
				if (!node.classList.contains('avoid')) {
					el_hide(node);
				}
			})
			el_show(frm);
			frm.style.position = test;
			return frm;
		},
		unToggleContent: function () {
			var frm = this;
			el_cleanWhitespace(frm.parentNode);
			el_hide(frm);
			Array.prototype.slice.call(frm.parentNode.children).forEach(function (node) {
				if (node !== frm && !node.classList.contains('avoid')) el_show(node);
			});
			return frm;
		},
		cloneCopy: function (target, options) {
			var source = this;
			options = Object.assign({offsetLeft: ''}, options || {});
			target.oldValue = target.value;
			var append = options.append || null;
			if (options.spy) {
				el_of(options.spy).addEventListener('click', function () {
					if (el_fieldValue(options.spy) == 'on') {
						target.oldValue = target.value;
						target.value = source.value
					} else {
						target.value = target.oldValue
					}
				})
			}
			['keyup', 'change', 'click'].forEach(function (eventName) {
				source.addEventListener(eventName, function () {
					if (el_fieldValue(options.spy)) {
						target.value = append + source.value
					}
				}, true)
			});
		},
		makeLoading: function () {
			var frm = this;
			el_identify(frm);
			frm.insertAdjacentHTML('afterbegin', '<div id="load' + frm.id + '" style="width:100%;height:100%;position:absolute;overflow:hidden;display:table-cell;vertical-align: middle;z-index:500;" class="transpblanc"><div class="loading"></div> </div>');
			var loader = document.getElementById('load' + frm.id);
			el_show(loader);
			loader.makeOnTop();
			frm.loader = function () {
				return document.getElementById('load' + frm.id);
			}
			return frm;
		},
		makeModal: function () {
			var frm = this;
			el_identify(frm);
			frm.insertAdjacentHTML('afterbegin', '<div id="load' + frm.id + '" style="width:100%height:100%;display:block;position:absolute;overflow:hidden;opacity:0.1;z-index:500;" class="noir"><div class="isModal"></div> </div>');
			var modal = document.getElementById('load' + frm.id);
			el_show(modal);
			modal.makeOnTop();
			frm.modal = function () {
				return document.getElementById('load' + frm.id);
			}
			return frm;
		},
		undoLoading: function () {
			var loader = document.getElementById('load' + this.id);
			if (!loader) {
				return false;
			}
			else {
				loader.remove()
			}
		},
		makeOnTop: function () {
			var element = this;
			var tempZindex = 1;
			if (element.parentNode) {
				Array.prototype.slice.call(element.parentNode.children).forEach(function (sibling) {
					if (sibling === element) return;
					var z = parseInt(window.getComputedStyle(sibling).zIndex, 10);
					if (!isNaN(z) && z > tempZindex) tempZindex = z;
				});
			}
			element.style.zIndex = tempZindex + 1;
			//console.log(element);
			return element;
		}
	});

})(window);

Array.prototype.chunk = function(j)  { return this.reduce((a,b,i,g) => !(i % j) ? a.concat([g.slice(i,i+j)]) : a, []);}

window.getInnerWidth = function () {
	if (window.innerWidth) {
		return window.innerWidth;
	} else if (document.body.clientWidth) {
		return document.body.clientWidth;
	} else if (document.documentElement.clientWidth) {
		return document.documentElement.clientWidth;
	}
}

window.getInnerHeight = function () {
	if (window.innerHeight) {
		return window.innerHeight;
	} else if (document.body.clientHeight) {
		return document.body.clientHeight;
	} else if (document.documentElement.clientHeight) {
		return document.documentElement.clientHeight;
	}
}
