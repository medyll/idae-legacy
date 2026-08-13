/**
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * This is the socket.io command dispatcher: every real-time push from the
 * Node bridge (`app_node/src/socket/handlers.js`) lands in one of the
 * `socket.on(...)` handlers below.
 */
window.opener=null;

var conn_options = {
	'sync disconnect on unload' : true
};


// Determine WebSocket host and port for different environments
var socketHost, socketPort;

switch (document.domain) {
	case "idaertys-preprod.mydde.fr":
	case "tactac_idae.preprod.mydde.fr":
	case "appcrfr.idaertys-preprod.mydde.fr":
	case "appmaw-idaertys-preprod.mydde.fr":
		socketPort = 3006;
		socketHost = document.domain;
		break;
	default:
		// For local development: use localhost instead of host.docker.internal for browser compatibility
		socketPort = 3005;
		if (document.domain === 'host.docker.internal' || document.domain.includes('docker')) {
			socketHost = 'localhost'; // Browser can't resolve host.docker.internal
		} else {
			socketHost = document.domain;
		}
		break;
}

var socketUrl = document.location.protocol + '//' + socketHost + ':' + socketPort;
console.log('[SOCKET] Connecting to:', socketUrl, conn_options);
console.log('[SOCKET] Document cookies:', document.cookie);
console.log('[SOCKET] Local storage PHPSESSID:', localStorage.getItem('PHPSESSID'));

// Ensure cookies are sent with WebSocket connection
conn_options.withCredentials = true;
conn_options.forceNew = true;

var socket = io.connect(socketUrl, conn_options);

/* ------------------------------------------------------------------ *
 * DOM helpers — file-local, same rationale as the other migrated       *
 * files (see BE_PLAN.md phase 5): sharing them would mean touching     *
 * main_bag.js's load graph, which this phase has no reason to disturb. *
 * ------------------------------------------------------------------ */

function sk_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function sk_identify(node) {
	if (!node.id) node.id = uniqid('anonymous_element');
	return node.id;
}

function sk_show(node) {
	if (node) node.style.display = '';
	return node;
}

function sk_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function sk_remove(node) {
	// Not node.remove(): the shim replaces Element.prototype.remove with
	// Prototype's version, so calling it would route back through the shim.
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

function sk_fire(node, eventName, memo) {
	if (!node) return null;
	var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
	event.memo = memo || {};
	node.dispatchEvent(event);
	return event;
}

/** Prototype's Element#up(selector): starts at the parent, never at self. */
function sk_up(node, selector) {
	if (!node || !node.parentElement) return null;
	return selector ? node.parentElement.closest(selector) : node.parentElement;
}

/**
 * Tolerant querySelectorAll, returning a real array. Values interpolated
 * into these selectors — table_value, stream_to, filenames — can be
 * anything a server sends: digit-leading strings, values with dots, none of
 * which are valid unquoted CSS attribute values. Prototype's own selector
 * engine tolerated them; this retries once with the value quoted rather than
 * assuming every caller already sanitized its input. Same algorithm as
 * shim-core.js's tolerantQueryAll, copied rather than called — the point of
 * migrating this file is to stop needing that shim at all.
 */
function sk_qsa(root, selector) {
	var nodes;
	try {
		nodes = root.querySelectorAll(selector);
	} catch (e) {
		var quoted = String(selector).replace(
			/\[([a-zA-Z_][\w-]*)=([^'"\]\s][^\]\s]*)\]/g,
			'[$1="$2"]'
		);
		if (quoted === selector) throw e;
		nodes = root.querySelectorAll(quoted);
	}
	return Array.prototype.slice.call(nodes);
}

/**
 * Prototype's Element#update/#insert both strip `<script>` tags out of the
 * HTML before inserting it, then eval each script's content afterward
 * (deferred ~10ms, so it runs after the DOM settles) — in the GLOBAL scope,
 * via indirect eval, same as Prototype's own evalScripts.
 *
 * This is not a nicety worth dropping: the server-rendered fragments this
 * handler injects routinely end in a bare `<script>` block that calls
 * `load_table_in_zone(...)` or similar to kick off the *next* request —
 * `mdl/app/app_liste/app_liste.php:100` is exactly this, and it is how a
 * list actually gets its rows (the outer window frame is one socketModule
 * response; the row data is a second, separate request this script starts).
 * Plain `node.innerHTML = html` never executes embedded `<script>` tags —
 * no browser does, regardless of Prototype/shim status — so skipping this
 * would silently break every fragment built that way, not just this file.
 */
var SK_SCRIPT_FRAGMENT = /<script[^>]*>([\s\S]*?)<\/script>/img;

function sk_stripScripts(html) {
	return html.replace(SK_SCRIPT_FRAGMENT, '');
}

function sk_evalScriptsDeferred(html) {
	if (!/<script/i.test(html)) return;
	setTimeout(function () {
		var match;
		var re = new RegExp(SK_SCRIPT_FRAGMENT.source, 'img');
		while ((match = re.exec(html))) {
			(1, eval)(match[1]); // indirect eval: run in global scope, like Prototype's
		}
	}, 10);
}

/** Prototype's Element#update: string -> innerHTML, Node -> replace children, null -> clear. */
function sk_update(node, content) {
	if (!node) return node;
	if (content == null) {
		node.innerHTML = '';
	} else if (content.nodeType) {
		node.innerHTML = '';
		node.appendChild(content);
	} else {
		var html = String(content);
		node.innerHTML = sk_stripScripts(html);
		sk_evalScriptsDeferred(html);
	}
	return node;
}

function sk_insertAt(node, position, content) {
	if (content && content.nodeType) {
		switch (position) {
			case 'top': node.insertBefore(content, node.firstChild); break;
			case 'bottom': node.appendChild(content); break;
			case 'before': if (node.parentNode) node.parentNode.insertBefore(content, node); break;
			case 'after': if (node.parentNode) node.parentNode.insertBefore(content, node.nextSibling); break;
		}
		return;
	}
	var html = String(content);
	var stripped = sk_stripScripts(html);
	switch (position) {
		case 'top': node.insertAdjacentHTML('afterbegin', stripped); break;
		case 'bottom': node.insertAdjacentHTML('beforeend', stripped); break;
		case 'before': node.insertAdjacentHTML('beforebegin', stripped); break;
		case 'after': node.insertAdjacentHTML('afterend', stripped); break;
	}
	sk_evalScriptsDeferred(html);
}

/**
 * Prototype's Element#insert: a bare string/Node appends at 'bottom'; an
 * object with top/bottom/before/after keys inserts each at that position.
 */
function sk_insert(node, spec) {
	if (!node) return node;
	if (typeof spec === 'string' || (spec && spec.nodeType)) {
		sk_insertAt(node, 'bottom', spec);
		return node;
	}
	['top', 'bottom', 'before', 'after'].forEach(function (position) {
		if (spec[position] !== undefined) sk_insertAt(node, position, spec[position]);
	});
	return node;
}

/**
 * The three `data-count` refresh blocks in the receive_cmd switch below were
 * byte-identical except for which `table` value each branch had already
 * computed — deduplicated here, that discrepancy preserved: act_close_mdl
 * passes 'id' + vars.table (already the case before this migration; not
 * this pass's place to decide whether that's a pre-existing bug).
 */
function sk_refreshCounts(scanTable, runModuleTable) {
	sk_qsa(document.body, '[data-table="' + scanTable + '"][data-count]').forEach(function (node) {
		var vars = node.getAttribute('data-vars');
		runModule('services/json_data_table', 'table=' + runModuleTable + '&' + vars + '&piece=count&count_id=' + sk_identify(node));
	});
}

/* ------------------------------------------------------------------ */

// Add connection error handling
socket.on('connect', function() {
	console.log('[SOCKET] ✓ Connected successfully:', socket.id);
});

socket.on('connect_error', function(error) {
	console.error('[SOCKET] ✗ Connection failed:', error);
	console.error('[SOCKET] Attempted URL:', socketUrl);
	console.error('[SOCKET] Available cookies:', document.cookie);
	console.error('[SOCKET] Connection options:', conn_options);
});

socket.on ('message', function (data) {
	console.log('message on socket',data);
	sk_update (sk_el ('msg_log'), data);
}.bind (this));
socket.on ('heartbeat_app', function (data) {
	sk_update (sk_el ('msg_log'), data);
}.bind (this));

socket.on ('notify', function (data) {
	var options       = {};
	options.className = 'myddeNotifierBottom';
	a                 = new myddeNotifier (options);
	// Add the missing growl call to actually display the notification
	try {
		a.growl(data.msg || data.message || 'Notification', options);
	} catch(e) {
		console.error('[NOTIFY] Error displaying notification:', e);
	}
}.bind (this));
//

window.data_tmp = [];

socket.on ('receive_cmd', function (data) {
	//console.log('receive_cmd',data.cmd);
	switch (data.cmd) {
		case'act_update_mdl':
			// module,value,data
			setTimeout (function () {
				act_update_mdl (data.vars);
			}.bind (this), 0)
			// unstream_from_cache

			break;
		case'act_close_mdl':
			var vars  = data.vars;
			var id    = 'id' + vars.table;
			var table = 'id' + vars.table;

			if ( document.body.querySelector ('[scope=' + id + ']') ) {
				sk_qsa (document, '[scope=' + id + '][value=' + vars.table_value + ']').forEach (function (node) {
					sk_fire (node, 'dom:close');
				});
				sk_qsa (document, '[scope=' + id + '][value=' + vars.table_value + ']').forEach (sk_remove);
			}
			sk_qsa (document, '[data-table=' + vars.table + '] [data-table_value=' + vars.table_value + ']').forEach (sk_remove);
			// unstream_from_cache

			sk_refreshCounts (vars.table, table);

			break;
		case'act_upd_data': // remplace  upd_data({table: table, table_value: table_value});
			var vars             = data.vars;
			var table            = vars.table;
			new_data             = [];
			new_data.table       = vars.table;
			new_data.table_value = vars.table_value;
			new_data.vars        = vars.new_vars;
			//

			act_upd_data (new_data);

			sk_refreshCounts (vars.table, table);

			break;

		case'act_add_data':
			var vars             = data.vars;
			var table            = vars.table;
			new_data             = [];
			new_data.table       = vars.table;
			new_data.table_value = vars.table_value;
			new_data.vars        = vars.new_vars;
			//

			setTimeout (function () {
				act_add_data (new_data);
			}.bind (this), 0)

			// lance test data-count

			sk_refreshCounts (vars.table, table);
			/* var vars = data.vars;
			 var table = vars.table;
			 // $$('[data-table='+table+']').invoke('fire','dom:data_reload');

			 if (!window.timer_stream_add_data) window.timer_stream_add_data = [];
			 if ($$('[data-table=' + table + ']')) {
			 //  $$('[data-uniqid='+stream_to+']')[0].fire('dom:stream_chunk',data.vars);
			 if (window.timer_stream_add_data[table]) clearTimeout(window.timer_stream_add_data[table])
			 window.timer_stream_add_data[table] = setTimeout(function () {
			 //$$('[data-table='+table+']').first().fire('dom:data_reload');
			 $$('[data-table=' + table + ']').invoke('fire', 'dom:data_reload');
			 }.bind(this), 0)
			 }*/
			break;
		case 'act_count':
			var vars     = data.vars;
			var table    = vars.table;
			var count    = vars.count;
			var count_id = vars.count_id;
			var count_node = sk_el (count_id);
			count_node.classList.add ('animated', 'bounce');
			sk_update (count_node, count);
			var trigger = sk_up (count_node, '[data-count_trigger]');
			if ( trigger ) {
				if ( Number (count) !== 0 ) {
					trigger.setAttribute ('data-count_trigger', 'true');
					// sk_show (trigger);
				} else {
					trigger.setAttribute ('data-count_trigger', 'hide');
					// sk_hide (trigger);
				}
			}

			break;
		case'act_reload_img':
			var vars     = data.vars;
			var filename = vars.filename;
			sk_qsa (document, '[data-filename=' + filename + ']').forEach (function (node) {
				node.src = "blank.png";
				node.src = node.getAttribute ('data-src') + '?act_reload=' + uniqid ();
			});

			break;

		case'act_progress':
			var vars   = data.vars;
			var name_p = 'auto_' + vars.progress_name;
			if ( !sk_el (name_p) && vars.progress_parent ) {
				 if( sk_el (vars.progress_parent) ){
					 sk_insert (sk_el (vars.progress_parent), { top : '<div class="flex_v" style="height:auto!important;overflow:hidden;"><progress id="' + name_p + '"></progress></div>' })
					 }
			}

			if ( sk_el (name_p) ) {
				var progress_node = sk_el (name_p);
				if ( progress_node.time_p ) clearTimeout (progress_node.time_p);
				sk_show (progress_node);
				if ( vars.progress_value ) progress_node.value = vars.progress_value;
				if ( vars.progress_max ) progress_node.max = vars.progress_max;
				if ( vars.progress_value && vars.progress_max ) {
					if ( vars.progress_value == vars.progress_max ) {
						progress_node.time_p = setTimeout (function () {
							sk_hide (progress_node);
						}.bind (this), 5000)
					}
				}
				if ( vars.progress_text ) {
					if ( !sk_el ('text_' + name_p) ) {
						sk_insert (progress_node, { before : '<div class="borderb padding" id="text_' + name_p + '">' + vars.progress_text + '</div>' })
					}
				}
				if ( vars.progress_text_remove ) {
					if ( sk_el ('text_' + name_p) ) {
						sk_remove (sk_el ('text_' + name_p));
					}
				}
				if ( vars.progress_message ) {
					var msg_;
					if ( !sk_el ('msg_' + name_p) ) {
						msg_ = document.createElement ('div');
						msg_.className = 'padding ededed';
						msg_.id = 'msg_' + name_p;
						sk_insert (progress_node, { before : msg_ })
					} else {
						msg_ = sk_el ('msg_' + name_p);
					}
					sk_update (msg_, vars.progress_message);
				}

				if ( vars.progress_message_remove ) {
					if ( sk_el ('msg_' + name_p) ) {
						sk_remove (sk_el ('msg_' + name_p));
					}
				}
				if ( vars.progress_log ) {
					var log_;
					if ( !sk_el ('log_' + name_p) ) {
						log_ = document.createElement ('div');
						log_.id = 'log_' + name_p;
						log_.className = 'flex_main';
						log_.style.overflow = 'auto';
						sk_insert (progress_node, { after : log_ })
					} else {
						log_ = sk_el ('log_' + name_p);
					}
					sk_insert (log_, { bottom : '<div class="retrait padding borderb">' + vars.progress_log + '</div>' });
				}
			}
			;

			break;
		case'act_run':
			  console.log('act_run fini', data);
			var vars = data.vars;
			break;
		case'act_script':
			var vars = data.vars;

			if ( window.timer_script ) clearTimeout (window.timer_script);
			window.timer_script = setTimeout (function () {
				console.log ('act_script', vars.script);
				eval (vars.script)
			}.bind (this), 500);
			break;
		case'act_debug':
			// console.log('act_debug', data)
			var vars = data.vars;
			break;
		case'act_stream_to':

			var data      = data;
			var dvars     = data.vars;
			var stream_to = dvars.stream_to;
			if ( sk_qsa (document, '[data-uniqid=' + stream_to + ']').length !== 0 ) {
				// console.log('stream_to !! ',data)
				var tmp_stream = uniqid ('uniqid');
				if ( !window.register_stream ) window.register_stream = []
				window.register_stream[tmp_stream] = dvars;

				sk_fire (sk_qsa (document, '[data-uniqid=' + stream_to + ']')[0], 'dom:stream_chunk', tmp_stream);
				setTimeout (function () {
					// Re-query: half a second is long enough for the window to
					// have been closed or the table rebuilt, and firing on
					// [0] of an empty result threw "Cannot read properties of
					// undefined (reading 'fire')" — the suite's oldest flake.
					var target = sk_qsa (document, '[data-uniqid=' + stream_to + ']')[0];
					if ( !target ) return;
					sk_fire (target, 'content:loaded', tmp_stream);
				}.bind (this), 500);

			} else {
				// runModule('actions', 'F_action=unstream_to&stream_to=' + stream_to);
			}

			break;

		case'act_notify':
			//
			var vars = data.vars;
			var msg  = vars.msg || '';
			var opt  = vars.options || {};
			try {
				var a = new myddeNotifier ();
				a.growl (vars.msg, opt);
			}
			catch (e) { }

			break;

		case 'act_gui':
			//alert('act_gui')
			// console.log(data);
			var vars  = data.vars;
			var opt   = vars.options || {};
			opt.title = data.title || data.mdl;

			new act_chrome_gui (vars.mdl, vars.vars, opt);

			break;

		case 'act_reload_module':
			if ( !data.vars ) return;
			var vars_data = data.vars;
			// console.log(vars_data);
			// ici, on met à jour le cache de maniere tansparente, si on trouve data.module et data.vars
			reloadModule (vars_data.module, vars_data.value || '*', vars_data.vars || '');

			break;
	}
}.bind (this));


//
socket.on ('reloadModule', function (data) {

	// ici, on met à jour le cache de maniere tansparente, si on trouve data.module et data.vars
	reloadModule (data.module, data.value, data.vars);
}.bind (this));
// reloadScope
socket.on ('reloadScope', function (data) {
	reloadScope (data.scope, data.value);
}.bind (this));
//
socket.on ('loadModule', function (data) {
	new windowGui (data.mdl, data.title, data.mdl, data.vars, { content : data.body })
}.bind (this));
//

socket.on ('socketModule', function (data) {
	//console.log('onsocket_module',data);

	var options   = data.out.options || {}
	var data_body = data.body || '';
	this.out      = data.out;
	var data_raw  = data.out;
	if ( empty (data.body) ) {
		console.log ('data_body vide ', data);
	}

	// thrttling

	// extraire : file / vars / table / table_value / key ?
	var objDta = {
		file      : data_raw.file,
		url_vars  : data_raw.vars,
		data_vars : URLToArray (data_raw.vars),
		data_body : data_body
	}

	if ( data_raw.vars.table )objDta.table = data_raw.vars.table;
	if ( data_raw.vars.table_value )objDta.table_value = data_raw.vars.table_value;
	//
	data_raw.vars        = data_raw.vars || '';
	var arr_inspect_vars = parse_str (data_raw.vars) || {};
	if ( arr_inspect_vars.table ) {
		objDta.table = arr_inspect_vars.table;
	}
	if ( arr_inspect_vars.table_value ) {
		objDta.table_value = arr_inspect_vars.table_value;
	}
	var target = sk_el (this.out.element);
	if ( !target ) {
		//
		return false;
	}
	var id_l = 'loading_loader_' + sk_identify (target);
	//
	if ( sk_el (id_l) ) {
		sk_remove (sk_el (id_l));
	}
	var key_name = build_cache_key (this.out.file, (this.out.vars || ''));

	var cache_body = window.localStorage.getItem (key_name) || '';

	var data_body_compare  = data_body.replace (/(\r\n|\n|\r)/gm, "");
	var cache_body_compare = cache_body.replace (/(\r\n|\n|\r)/gm, "");

	if ( options.fragment ) {
		var fragment        = options.fragment
		var tmp_fragment    = document.createDocumentFragment ();
		var in_tmp_fragment = document.createElement ("div");

		in_tmp_fragment.innerHTML = data_body;

		var frag_node   = in_tmp_fragment.querySelector ('[data-app_fragment=' + fragment + ']');
		var frag_node_2 = in_tmp_fragment.querySelector ('div');

		sk_update (target, frag_node);
		return;
	}

	target.removeAttribute ('data-need_cache')
	if ( options.append ) {
		sk_insert (target, data_body);
	} else if ( options.insertion ) {
		sk_insert (target, { top : data_body });
	} else {
		sk_update (target, data_body);
	}

	afterAjaxCall (target);
	sk_fire (target, 'content:loaded');

	}.bind (this));
//
socket.on ('disconnect', function () {
	if ( sk_el ('socket_status') )  sk_show (sk_el ('socket_status'));
});
socket.on ('reconnect', function () {
	// $('body').undoLoading();
	if ( sk_el ('socket_status') )  sk_hide (sk_el ('socket_status'));
});

socket.on ('reconnecting', function (nextRetry) {
	if ( sk_el ('socket_status') )    sk_update (sk_el ('socket_status'), ' .. ' + (Number (nextRetry) / 1000)) + ' s ';
});
socket.on ('reconnect_failed', function () {
	if ( sk_el ('socket_status') ) sk_update (sk_el ('socket_status'), ' dead ');
});

socket.on ('upd_data', function (data) {
	try {
		var resp = JSON.parse (data.body);
	}
	catch (e) {
		// console.log('fail upd_data', data.body);
		return;
	}
	;

	var data_main = resp.data_main[0];
	if ( !data_main ) return;
	var vars = data_main.vars;
	var rev  = data_main.html;

	var table       = vars.table;
	var table_value = vars.table_value;
	Object.keys (rev).forEach (function (field_name) {
		var field_value = rev[field_name];
		sk_qsa (document.body, '[table="' + table + '"][table_value="' + table_value + '"] [field_name="' + field_name + '"]').forEach (function (node) {
			sk_update (node, field_value);
		});
	});

});
//
socket.on ('receive_data', function (obj_data) {
	var base  = obj_data.base,
	    table = obj_data.collection,
	    op    = obj_data.op,
	    data  = obj_data.data,
	    _id   = obj_data._id;

	var test_tpl = document.body.querySelector ('[data-scheme="' + base + '.' + table + '"]');
	if ( test_tpl ) {

		switch (op) {
			case 'i':
				console.log ('Insertion');
				//
				break;
			case'u':
				console.log ('mise a jour ' + _id);
				// get_json
				setTimeout (function () {
					get_data ('json_data', {
						table : table,
						piece : 'data',
						vars  : { _id : _id }
					}).then (function (res) {
						res = JSON.parse (res);

						Object.keys (data).forEach (function (key) {
							var data_field_name = base + '.' + table + '.' + key;

							sk_qsa (document.body, '[data-field_name="' + data_field_name + '"][data-mongokey="' + _id + '"]').forEach (function (node) {
								sk_update (node, nl2br (data[key]));
							});

						});

					});
				}.bind (this), 0)

				break;
			case'd':
				console.log ('dead zone');
				break;
		}	//
	}
}.bind (this));
