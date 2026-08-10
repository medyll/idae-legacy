/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * `reloadModule`/`reloadScope`/`closeModule` are called from app_socket.js's
 * receive_cmd handlers (already native) and from client code that needs to
 * refresh every node tagged with a given `mdl`/`scope`.
 *
 * Still calling this app's own native API on purpose: `socketModule` is
 * engine/methods.js (already migrated), not the shim. `.close` on a node is
 * a plain property app_window.js's windowGui assigns to elements it tracks
 * (`el.close = this.close.bind(this)`) — never a Prototype/shim thing.
 */

/* ------------------------------------------------------------------ *
 * DOM/ajax helpers — file-local, same rationale as the other migrated  *
 * files (see BE_PLAN.md phase 5).                                      *
 * ------------------------------------------------------------------ */

function module_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function module_cleanWhitespace(node) {
	if (!node) return node;
	var child = node.firstChild;
	while (child) {
		var next = child.nextSibling;
		if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
		child = next;
	}
	return node;
}

function module_toQueryParams(query) {
	return Object.fromEntries(new URLSearchParams(String(query || '').replace(/^\?/, '')));
}

function module_toQueryString(obj) {
	return new URLSearchParams(obj || {}).toString();
}

function module_remove(node) {
	// Not node.remove(): the shim replaces Element.prototype.remove with
	// Prototype's version, so calling it would route back through the shim.
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

/**
 * Prototype's Ajax.Updater, reduced to what this file's two fallback call
 * sites need — same two-layer script contract app_socket.js/engine.js
 * needed (see their BE_PLAN.md entries): text is always stripped of
 * `<script>` before the innerHTML assignment, and separately, since both
 * callers here pass `evalScripts: true`, the *original* text gets
 * deferred-eval'd (~10ms). `parameters` is the object form Ajax.Request
 * serializes into the POST body itself (no `postBody` given here, unlike
 * engine.js's callers) — done here via module_toQueryString before fetch.
 */
var MODULE_SCRIPT_FRAGMENT = /<script[^>]*>([\s\S]*?)<\/script>/img;

function module_stripScripts(html) {
	return html.replace (MODULE_SCRIPT_FRAGMENT, '');
}

function module_evalScriptsDeferred(html) {
	if (!/<script/i.test (html)) return;
	setTimeout (function () {
		var re = new RegExp (MODULE_SCRIPT_FRAGMENT.source, 'img');
		var match;
		while ((match = re.exec (html))) {
			(1, eval) (match[1]); // indirect eval: run in global scope, like Prototype's
		}
	}, 10);
}

function module_ajaxUpdater(container, url, options) {
	options = options || {};
	fetch (url, {
		method: (options.method || 'post').toUpperCase (),
		headers: {'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'},
		credentials: 'same-origin',
		body: module_toQueryString (options.parameters)
	}).then (function (response) {
		return response.text ();
	}).then (function (text) {
		if (container) container.innerHTML = module_stripScripts (text);
		if (options.evalScripts) module_evalScriptsDeferred (text);
	}).catch (function (error) {
		console.error ('[module] ajax updater failed', url, error);
	});
}

/* ------------------------------------------------------------------ */

URLToArray = function (url) {
  var request = {};
  var pairs = url.split('&');
  for (var i = 0; i < pairs.length; i++) {
    var pair = pairs[i].split('=');
    request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
  }
  return request;
}
reloadScope = function(scope,value,pars,newValue,options){
		var newValue = newValue || value

		Array.prototype.slice.call (document.querySelectorAll ('[scope="'+scope+'"]')).forEach (function(theElem){
		var node = theElem;
			if(node.getAttribute('value')==value || value == '*'){
				//
				options = Object.assign({
					oneLoad: false,
					seeLoading: true,
					unset_key : [],
					force: false
				}, options || {});
				//

				if(options.oneLoad == true && node.children.length!=0) return;
				//
				newvars = node.getAttribute('vars')+'&'+(pars || '') ;
				if(options.force){newvars=options.force;}
				qy = URLToArray(newvars);

				if(options.unset_key.length!=0) {
					options.unset_key.forEach(function(key_name,i){
						 if(qy[key_name]  ){qy[key_name]=null; delete qy[key_name]; }
						 if(qy.vars && qy.vars[key_name]){console.log('unset trouvé',key_name)}
					});
				}

				qy.defer = null;qy.emptyModule=null; delete qy.defer; delete qy.emptyModule;
				node.setAttribute ('vars', module_toQueryString (qy));
				module =  node.getAttribute('mdl')
				//

				if( typeof socket  ==  'object'){
					node.socketModule(module,module_toQueryString (qy),options);
					return node;
				}
				// fallback
				module_ajaxUpdater(node, changeCnameTrick()+'mdl/'+node.getAttribute('mdl')+'.php', {
						parameters: qy ,
						method: 'post',
						evalScripts: true
					});

			}
		})
	}
reloadModule = function(module,value,pars,newValue,options){
		// var newValue = newValue || value


		Array.prototype.slice.call (document.querySelectorAll ('[mdl="'+module+'"]')).forEach (function(theElem){
		var node = theElem;
			if(node.getAttribute('value')==value || value == '*'){
				//
				//options.value = newValue || node.getAttribute('value')

				options = Object.assign({
					oneLoad: false,
					seeLoading: true
				}, options || {});
				//
				if(options.oneLoad == true && node.children.length!=0) return;
				//
				var aars = pars || node.getAttribute('vars')

				var qy = module_toQueryParams (aars)
				// var qy = Object.isString(aars) ? aars : Object.toQueryString(aars);

				module_cleanWhitespace (node);
				qy.defer = null;qy.emptyModule=null;
				qy.module= module;
				node.setAttribute ('vars', module_toQueryString (qy));
				if(newValue) node.setAttribute ('value', newValue);
				//
				if( typeof socket  ==  'object'){
					node.socketModule(module,aars,options);
				}else{
					module_ajaxUpdater(node, changeCnameTrick()+'mdl/'+node.getAttribute('mdl')+'.php', {
						parameters: qy ,
						method: 'post',
						evalScripts: true
					});

				}
				//
				setTimeout(function(){
					// console.log('reloadModule par ajax !!! ')
				}.bind(this),250)

			}
		})
	}
closeModule = function(module,value,pars,newValue){
		var newValue = newValue || value
		Array.prototype.slice.call (document.querySelectorAll ('[mdl="'+module+'"]')).forEach (function(node){
			if(node.getAttribute('value')==value){
				aars = pars || node.getAttribute('vars')
				try{node.close()}catch(e){module_remove(node)}
			}
		})
	}
newValueModule = function(){
	var aa = new Date();
	newValue = 'moduleValue_'+aa.getTime();
	return newValue;
	}
