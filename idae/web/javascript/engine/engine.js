/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * `act_chrome_gui`/`ajaxMdl`/`ajaxInMdl` are the app's core navigation
 * primitives — nearly every window this app opens goes through one of them,
 * which means the existing Playwright suite already exercises this file
 * continuously; a full green run is unusually strong evidence for this file
 * specifically.
 *
 * Still calling this app's own native API on purpose: `makeModal`,
 * `undoLoading`, `socketModule` are engine/methods.js (already migrated),
 * not the shim. `windowGui` is app_window.js (already migrated).
 */

/* ------------------------------------------------------------------ *
 * DOM/string helpers — file-local, same rationale as the other         *
 * migrated files (see BE_PLAN.md phase 5).                             *
 * ------------------------------------------------------------------ */

function engine_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function engine_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function engine_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function engine_show(node) {
	if (node) node.style.display = '';
	return node;
}

function engine_stripTags(html) {
	return String(html).replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
}

function engine_cleanWhitespace(node) {
	if (!node) return node;
	var child = node.firstChild;
	while (child) {
		var next = child.nextSibling;
		if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
		child = next;
	}
	return node;
}

function engine_toQueryParams(query) {
	return Object.fromEntries(new URLSearchParams(String(query || '').replace(/^\?/, '')));
}

/**
 * Prototype's Form.serialize: name=value pairs, url-encoded, skipping
 * disabled fields and — unconditionally, regardless of any `submit` option —
 * every `type=submit` input. Not FormData: FormData has no notion of "which
 * button triggered the submit" when called outside a submit event, so it
 * includes every submit button's value; Prototype's version never does.
 */
function engine_formSerialize(form) {
	if (!form) return '';
	var pairs = [];
	Array.prototype.slice.call (form.querySelectorAll ('input, select, textarea, button')).forEach (function (field) {
		if (field.disabled || !field.name || field.type === 'file' || field.type === 'submit') return;
		if ((field.type === 'checkbox' || field.type === 'radio') && !field.checked) return;
		if (field.tagName.toLowerCase () === 'select' && field.multiple) {
			Array.prototype.slice.call (field.options).forEach (function (option) {
				if (option.selected) pairs.push (encodeURIComponent (field.name) + '=' + encodeURIComponent (option.value));
			});
			return;
		}
		pairs.push (encodeURIComponent (field.name) + '=' + encodeURIComponent (field.value));
	});
	return pairs.join ('&');
}

/**
 * Prototype's Element#update on a fragment fetched via `mdlDiv`/`eval`
 * below is *not* the migration risk it looks like — this parses a literal
 * array-string, not server-fetched HTML, so it needs no script handling.
 * These two DO fetch server HTML and need it, mirroring app_socket.js's
 * sk_stripScripts/sk_evalScriptsDeferred exactly (see that file's entry in
 * BE_PLAN.md for why: a browser never executes a `<script>` tag set via
 * plain innerHTML, so skipping this silently breaks anything a fetched
 * fragment's own inline script was supposed to do next).
 */
var ENGINE_SCRIPT_FRAGMENT = /<script[^>]*>([\s\S]*?)<\/script>/img;

function engine_stripScripts(html) {
	return html.replace (ENGINE_SCRIPT_FRAGMENT, '');
}

/** Immediate (non-deferred) — matches ajaxValidation's own onComplete-time call. */
function engine_evalScripts(html) {
	var re = new RegExp (ENGINE_SCRIPT_FRAGMENT.source, 'img');
	var match;
	while ((match = re.exec (html))) {
		(1, eval) (match[1]); // indirect eval: run in global scope, like Prototype's
	}
}

function engine_ajaxHeaders(options) {
	var headers = {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'};
	if (Array.isArray (options.requestHeaders)) {
		for (var i = 0; i < options.requestHeaders.length; i += 2) {
			headers[options.requestHeaders[i]] = options.requestHeaders[i + 1];
		}
	}
	return headers;
}

/**
 * Prototype's Ajax.Request, reduced to what ajaxValidation needs: POST,
 * hand the raw response back to onComplete as `{status, responseText}`.
 * Script eval is the *caller's* job here (ajaxValidation calls
 * engine_evalScripts on the responseText itself, immediately, matching
 * Ajax.Request's transport.responseText.evalScripts() being called directly
 * inside onComplete rather than something this helper does automatically).
 */
function engine_ajaxRequest(url, options) {
	options = options || {};
	fetch (url, {
		method: (options.method || 'post').toUpperCase (),
		headers: engine_ajaxHeaders (options),
		credentials: 'same-origin',
		body: options.postBody
	}).then (function (response) {
		return response.text ().then (function (text) {
			if (options.onComplete) options.onComplete ({status: response.status, responseText: text});
		});
	}).catch (function (error) {
		console.error ('[engine] ajax request failed', url, error);
	});
}

/**
 * Prototype's Ajax.Updater, reduced to what ajaxFormValidationReal needs:
 * POST, strip `<script>` before setting `container.innerHTML`, then — only
 * when `options.evalScripts` is explicitly true, Ajax.Updater's own opt-in,
 * separate from the base Ajax.Request script handling — eval the *original*
 * (unstripped) response deferred ~10ms, same as Prototype's own
 * `responseText.evalScripts.bind(responseText).defer()`.
 */
function engine_ajaxUpdater(container, url, options) {
	options = options || {};
	fetch (url, {
		method: (options.method || 'post').toUpperCase (),
		headers: engine_ajaxHeaders (options),
		credentials: 'same-origin',
		body: options.postBody
	}).then (function (response) {
		return response.text ().then (function (text) {
			if (container) container.innerHTML = engine_stripScripts (text);
			if (options.evalScripts && /<script/i.test (text)) {
				setTimeout (function () { engine_evalScripts (text); }, 10);
			}
			if (options.onComplete) options.onComplete ({status: response.status, responseText: text});
		});
	}).catch (function (error) {
		console.error ('[engine] ajax updater failed', url, error);
	});
}

/* ------------------------------------------------------------------ */

var searchtimer;
quickFind = function (value, where, tag, spy) {
	clearTimeout(searchtimer);
	value = value.toLowerCase();
	searchString = value;
	searchtimer = setTimeout(function () {
		matrixView = null;
		i = 0;
		if (value.length === 0 || value.length < 2) {
			//return
		}
		if (value.length === 0 || value.length < 2) {
			Array.prototype.slice.call (engine_el (where).querySelectorAll (tag)).forEach (engine_show);
			// $$('#cometspy li').invoke('show');
		} else {
			Array.prototype.slice.call (engine_el (where).querySelectorAll (tag)).forEach (function (node) {
				inn = engine_stripTags (node.innerHTML).toLowerCase();
				if (inn.indexOf (value) === -1) {
					setTimeout (function () { engine_hide (node); }, 10);
				} else {
					setTimeout (function () { engine_show (node); }, 10);
					i++;
					// $('resultStatus').update(i+' resultats pour "'+value+'"');
				}
			})
		}
		if (spy) {
			if (engine_el (spy)) {
				spy = document.createElement ('div');
				spy.className = 'inline ededed padding border4';
			}
			if (!(spy.innerHTML == null || spy.innerHTML.trim ().length === 0)) {
				engine_el (spy).innerHTML = '<div class="inline ededed padding border4">' + i + "</div"
			}
		}
	}, 250)
	/*engine_el(where).addEventListener('content:loaded',function(){
	 quickFind (value,where,tag,spy)
	 }.bind(this));*/
}

act_chrome_gui = function (file, vars, options) {

	var vars = (vars || '').trim (),
		filekey = clean_string(file + vars);

	// Preserved verbatim, including the 3rd argument replace() has always
	// ignored (native String#replace only reads flags off a RegExp pattern,
	// never a string one — see BE_PLAN.md's afterAjaxCall.js entry). Unlike
	// that file, here `file` genuinely contains '/' (an mdl path like
	// "app/app/app_fiche"), so this isn't a no-op: only the *first* '/'
	// becomes '","', so onlyFile ends up as everything after that first
	// slash, not literally the last path segment. That quirk is unchanged.
	mdlDiv = ('["' + file + '"]').replace('/', '","', 'gi')
	var mdlDivArr = eval(mdlDiv);
	onlyFile = mdlDivArr[mdlDivArr.length - 1];
	valueMdl = onlyFile
	var ajaxOption = {file: file, ajaxLoad: true, parent: 'inBody', inTask: true, sanitizeJSON: false}

	var options = Object.assign({
		ajaxLoad: true,
		parent: 'inBody',
		ident: filekey,
		inTask: true,
		scope: false
	}, options || {});
	// on rajoute le nom du module à pars
	vars += '&module=' + file
	if (options) {

		if (options.value) {
			valueMdl = options.value
		}
		if (options.ident) {
			ajaxOption.ident = options.ident;
		}
	}
	Object.assign(ajaxOption, options || {});

	url = changeCnameTrick() + 'mdl/' + file;
	// on ecrit options ident ( onlyfile par zefault )
	temp = engine_toQueryParams (vars);
	if (temp && temp.table && temp.table_value) {
		ajaxOption.ident = temp.table + temp.table_value;
	}
	var crh_gui = new windowGui(file, '', url, vars, ajaxOption);
	var guiElement = crh_gui.innerDisp;
	if (!guiElement) return;
	guiElement.setAttribute ('mdl', file);
	guiElement.setAttribute ('vars', vars);
	guiElement.setAttribute ('value', valueMdl);

	if (temp && temp.table && temp.table_value) {
		guiElement.setAttribute ('table', temp.table);
		guiElement.setAttribute ('scope', 'id' + temp.table);
		guiElement.setAttribute ('value', temp.table_value); // , ident: temp.table + temp.table_value
	} else if (temp && temp.table) {
		guiElement.setAttribute ('scope', temp.table);
		guiElement.setAttribute ('table', temp.table);
	} else {
		if (options.scope) {
			guiElement.setAttribute ('scope', options.scope);
		}
	}

	// document.location.href = '#' + file + '#' + vars;
}
ajaxMdl = function (file, title, davars, options) { // utilser act_chrome_gui !!!

	var vars = vars || '';
	file1 = file;
	mdlDiv = ('["' + file1 + '"]').replace('/', '","', 'gi')
	var mdlDivArr = eval(mdlDiv);
	onlyFile = mdlDivArr[mdlDivArr.length - 1];
	valueMdl = onlyFile
	rep = mdlDivArr.filter (function (item) { return item !== onlyFile; });
	vars = davars || '';
	ajaxOption = {ident: clean_string(file + vars), file: file, ajaxLoad: true, parent: 'inBody', inTask: true, sanitizeJSON: false}

	this.options = Object.assign({
		ajaxLoad: true,
		parent: 'inBody',
		inTask: true,
		scope: false
	}, options || {});
	// on rajoute le nom du module à pars
	// vars += '&module=' + file
	if (options) {
		if (options.modalOn) {
			// Original: $(options.modalOn).makeModal().modal().setStyle({zIndex:0})
			// — the chain's final value (and what `dmp`/ajaxOption.parent end up
			// being) is the modal overlay div, not modalOn itself. makeModal()
			// returns modalOn unchanged (engine/methods.js); .modal() is the
			// accessor it attaches, returning the overlay div just created.
			var modalOn = engine_el (options.modalOn);
			modalOn.makeModal ();
			dmp = modalOn.modal ();
			dmp.style.zIndex = 0;
			options.buttonClose = false;
			options.buttonReduce = false;
			options.inTask = false;
			options.onclose = function () {
				modalOn.undoLoading();
			}.bind(this);
			ajaxOption.parent = dmp;
		}
		if (options.value) {
			valueMdl = options.value
		}
		if (options.ident) {
			ajaxOption.ident = options.ident;
		}
	}
	Object.assign(ajaxOption, options || {});

	url = changeCnameTrick() + 'mdl/' + file;

	var guiElement = new windowGui(onlyFile, title, url, vars, ajaxOption);

	if (options) {
		if (options.ident) {
			onlyFile = options.ident
		}
	}
	var onlyFileEl = engine_el (onlyFile);
	if (onlyFileEl) {
		//onlyFileEl.makeOnTop();
		onlyFileEl.setAttribute ('mdl', file);
		onlyFileEl.setAttribute ('vars', vars);
		onlyFileEl.setAttribute ('value', valueMdl);  // ,title:file
		if (this.options.scope) {
			onlyFileEl.setAttribute ('scope', this.options.scope);
		}
		onlyFileEl.value = valueMdl;

	}

	document.location.href = '#' + file + '#' + vars;
}


//
ajaxInMdl = function (file, element, vars, options) {
	// console.log('vars'+ vars);
	//
	var guivars
	// si en module sous repertoire
	var tmpDivArr = eval (('["' + file + '"]').replace('/', '","', 'gi'));
	var tmpDiv = tmpDivArr[tmpDivArr.length - 1];

	// on rajoute le nom du module à pars

	var vars = vars || '';
	vars += '&module=' + file;
	vars += '&mdl=' + file;

	var mdlOption = {
		value: null,
		seeLoading: true,
		onglet: false,
		tabParent: null,
		taskBar: null,
		reloaded: true,
		single: true,
		fitScreen: true,
		cache: false,
		scope:''
	}

	this.options = Object.assign(mdlOption,options || {});

	if (this.options.onglet != false) {
		var barreTache = this.options.taskBar || document.getElementById ('taskBar');
		var divParent = this.options.tabParent || document.getElementById ('mainApp');

		element = engine_el (element);
		if (!element) {
		} else {
			element.classList.add ('flex_v');
			engine_show (element);
			if (!document.getElementById ('taskbar_' + engine_identify (element))) {
				barreTache = create_element_in('div', element, {className: 'taskBar ededed', id: 'taskbar_' + engine_identify (element)});
			} else {
				barreTache = document.getElementById ('taskbar_' + engine_identify (element));
			}
			divParent = create_element_in('div', element, {className: 'flex_main'});
		}
		if (!barreTache) {
			popopen('proxyIndex.php?titre=' + this.options.onglet + '&mdl=' + this.options.file + '&' + vars, '', '', this.options.onglet, true)
			return;
		}

		guivars = {title: this.options.onglet, file: file, vars: vars, taskBar: engine_identify (barreTache), container: divParent, fitScreen: this.options.fitScreen};

		if (this.options.mainIdent) {
			guivars.mainIdent = this.options.mainIdent;
		}

		element = window.JSGUI.add(guivars);

	}
	// atrributs !!!

	element = engine_el (element);
	element.setAttribute ('vars', vars);
	element.setAttribute ('mdl', file);
	element.setAttribute ('value', this.options.value || '');
	element.setAttribute ('scope', this.options.scope || '');
	//.log()
	engine_cleanWhitespace (element);

	if (this.options.reloaded == true) {
		element.socketModule(file, vars, this.options)
	}

	if (this.options.reloaded == false && !element.children[element.children.length - 1]) {
		element.socketModule(file, vars, this.options)
	}
	// document.location.href = '#' + file;
}.bind(this);

ajaxValidation = function (action, path, pars) {
	path = path || '';
	var url = path + 'actions.php' || 'actions.php';
	/*opt.PHPSESSID = Cookies.get('PHPSESSID') || '';
	 opt.SESSID = Cookies.get('SESSID') || '';*/

	engine_ajaxRequest (
		url, {
			onComplete: function (transport) {
				engine_evalScripts (transport.responseText)
			},
			method: 'post',
			requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8'],
			postBody: 'F_action=' + action + '&_csrf=' + (window.APP && window.APP.CSRF_TOKEN || '') + '&' + pars
		});
	// ,onComplete: function(){div.remove()}.bind(this)
	//requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8','Set-Cookie','PHPSESSID='+localStorage.getItem('PHPSESSID')+';path=/'],
	return true;
}
runSocketModule = function () {

}
ajaxFormValidation = function (form) {
	console.log('ajaxFormValidation', form);
	if (form.getAttribute ('auto_close')) {
		form.makeLoading();
	}
	setTimeout(function () {
		ajaxFormValidationReal(form)
	}, 300);
}
ajaxFormValidationReal = function (form) {
	console.log('ajaxFormValidationReal', form);
	var vars
	this.loadingform = form;
	/*valid = new Validation (form, {immediate: true, useTitles: true});
	 result = valid.validate ();
	 if (!result) {
	 return;
	 }*/
	if (!document.getElementById ('div_form_validation')) {
		div_form_validation = document.createElement ('div')
		div_form_validation.id = 'div_form_validation';
		document.body.appendChild(div_form_validation);
		engine_hide (div_form_validation);
	}
	//
	var options = {
		method: 'post', evalScripts: true,
		requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8']
	}
	if (form.getAttribute ('auto_close')) {
		options.onComplete = function () {
			engine_hide (form);
			var event = new CustomEvent ('dom:close', {bubbles: true, cancelable: true});
			form.dispatchEvent (event);
		}.bind(this)
	}
	if (form.getAttribute ('auto_reset')) {
		options.onComplete = function () {
			form.reset();
		}.bind(this)
	}
	if (form.action) {
		var url = form.action;
	} else {
		var url = 'actions.php';
	}
	options.postBody = engine_formSerialize (form) + '&_csrf=' + encodeURIComponent(window.APP && window.APP.CSRF_TOKEN || '')
	lodaj = engine_ajaxUpdater(document.getElementById ('div_form_validation'), url, options);
	console.log(lodaj)
	return true;
}
