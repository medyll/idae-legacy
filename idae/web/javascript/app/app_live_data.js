/**
 * Created by Mydde on 12/10/2015.
 *
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Socket-driven live data: throttled DOM updates for
 * rows/fields that changed server-side, plus the tache/conge re-parenting
 * on the planning boards.
 *
 * Behaviour unchanged, including the always-true `if ($$(...))` guards
 * documented on act_upd_data — `$$` returned an array, so those checks
 * never actually filtered anything, and querySelectorAll behaves the same.
 */
window.data_subscribe = [];
/*if( typeof (insertionQ)!='object'){
 setTimeout(function () {
 init_q_data_live()
 }.bind(this),1000)
 }else{
 init_q_data_live()
 }
 init_q_data_live = function(){
 insertionQ('[data-table]').every(function (node) {
 window.data_subscribe[node.readAttribute('data-table')]=1;

 });

 console.log('init_q_data_live '  )
 }*/

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function ld_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

/**
 * Tolerant querySelectorAll, matching what `$$`/`Element#select` did through
 * the shim (`__idaeQSA`, shim-core.js). Prototype's selector engine accepted
 * unquoted attribute values that CSS forbids — and this file builds exactly
 * those: `[data-table_value=5]` is invalid CSS because the value starts with
 * a digit, and native querySelectorAll throws a SyntaxError on it. Since
 * table_value is an integer primary key throughout Idae, that is the normal
 * case here, not an edge case. Retry once with the attribute values quoted.
 */
function ld_qsa(selector, root) {
	root = root || document;
	var found;
	try {
		found = root.querySelectorAll(selector);
	} catch (e) {
		var quoted = String(selector).replace(
			/\[([a-zA-Z_][\w-]*)=([^'"\]\s][^\]\s]*)\]/g,
			'[$1="$2"]'
		);
		if (quoted === selector) throw e;
		found = root.querySelectorAll(quoted);
	}
	return Array.prototype.slice.call(found);
}

function ld_remove(node) {
	// Not node.remove(): the shim replaces Element.prototype.remove with
	// Prototype's version, so calling it would route back through the shim.
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

/** Prototype's Element#update: strip <script> before innerHTML, eval the original on a 10ms defer. */
function ld_update(node, content) {
	if (content === undefined) content = '';
	content = String(content);
	node.innerHTML = engine_stripScripts(content);
	if (/<script/i.test(content)) {
		setTimeout(function () { engine_evalScripts(content); }, 10);
	}
	return node;
}

function ld_fire(node, eventName, memo) {
	var event = new CustomEvent(eventName, {bubbles: true, cancelable: true});
	event.memo = memo || {};
	node.dispatchEvent(event);
	return event;
}

function ld_writeAttributes(node, attrs) {
	Object.keys(attrs).forEach(function (key) {
		node.setAttribute(key, attrs[key]);
	});
	return node;
}

/* -------------------------------------------------------------------- */

var timer_upd_data = [];
var throttle_delay = 100, throttle_queue = [], throttle_timer = false, throttle_elem, throttle_trigger_size = 10;

throttling         = function (data, fn) {
	// console.log('throttling ', data, throttle_queue, throttle_queue.length);

	if ( data ) {
		throttle_queue.push (data);
		// console.log('throttle_queue.push',throttle_queue.length)
	}
	if ( !throttling_reached () && data ) {
		act_upd_data (data, true);
		return true;
	}
	;
	throttling_run ();

}
throttling_reached = function () {
	return (throttle_queue.length >= throttle_trigger_size)
}
throttling_run     = function () {

	if ( !throttle_timer ) {
		//console.clear();
		//console.log('ini timer')
		throttle_timer = setInterval (function () {
			throttling_shift ()
		}.bind (this), throttle_delay);
	}

}
throttling_shift   = function () {

	throttle_elem = throttle_queue[0];
	// console.log('size before shift',throttle_queue.length)
	throttle_queue.shift ();
	// console.log('size after shift',throttle_queue.length)
	act_upd_data (throttle_elem, true);

	// console.log(throttle_timer)
	if ( throttle_queue.length == 0 ) {
		clearInterval (throttle_timer);
		throttle_timer = false;
	}
}

act_upd_data = function (data, from_throttle) {
	var table_value = data.table_value;
	var table       = data.table;
	var Table       = ucfirst (table);
	var vars        = data.vars || [];

	// console.log(' => act_upd_data ', table, vars, data);

	if ( !from_throttle ) {
		throttling (data);
		return false;
	}

	if ( !window.data_subscribe[table] ) {
		// console.log ('refus data_live ' + table);
		return;
	}
	// Always true, before as after: `$$` returned an array (truthy even when
	// empty) and ld_qsa returns one too. The "desinscription" branch at the
	// bottom has therefore never run. Kept verbatim.
	if ( ld_qsa ('[data-table=' + table + ']') ) {
		// console.log('mise à jour data_live '+table,vars);
		/*clearTimeout(timer_upd_data[table + table_value]);
		 timer_upd_data[table + table_value] = setTimeout(function () {
		 for (var property in vars) {
		 if (vars.hasOwnProperty(property)) {
		 $$('[data-table=' + table + '][data-table_value=' + table_value + '] [data-field_name=' + property + ']').invoke('update', vars[property]);
		 }
		 }
		 }.bind(this), 5)*/
		for (var property in vars) {
			if ( vars.hasOwnProperty (property) ) {
				ld_qsa ('[data-table=' + table + '][data-table_value=' + table_value + '] [data-field_name=' + property + ']').forEach (function (n) { ld_update (n, vars[property]); });
			}
		}

		clearTimeout (timer_upd_data[table]);
		timer_upd_data[table] = setTimeout (function () {
			ld_qsa ('[data-table=' + table + '][data-uniqid]').forEach (function (n) { ld_fire (n, 'dom:data_reload', vars); });
		}.bind (this), 100);

		switch (table) {
			case 'tache':
				if ( vars['dateDebut' + Table] || vars['heureDebut' + table] ) {
					var dateDebut  = vars['dateDebut' + Table],
					    heureDebut = vars['heureDebut' + Table],
					    heureFin   = vars['heureFin' + Table];

					// console.log(Table,table_value,ld_qsa('[data-table=' + table + '][data-table_value=' + table_value + ']'))
					ld_qsa ('[data-table=' + table + '][data-table_value=' + table_value + ']').forEach (function (tache_node) {

						if ( tache_node.getAttribute ('data-parent') ) {
							tache_node.setAttribute ('data-dragtache', 'tache');
							tache_node.setAttribute ('data-dateDebut', dateDebut);
							tache_node.setAttribute ('data-heureDebut', heureDebut);
							// data-parent holds an element id, so this is a lookup.
							var tache_parent = ld_el (tache_node.getAttribute ('data-parent'));
							if ( ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="' + heureDebut + '"]', tache_parent).length != 0 ) {
								ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="' + heureDebut + '"]', tache_parent).forEach (function (node_parent) {
									node_parent.appendChild (tache_node);
								}.bind (this));
							} else if ( ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="AM"]', tache_parent).length != 0 ) {
								ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="AM"]', tache_parent).forEach (function (node_parent) {
									node_parent.appendChild (tache_node);
								}.bind (this))
							} else if ( ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]', tache_parent).length != 0 ) {
								ld_qsa ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]', tache_parent).forEach (function (node_parent) {
									node_parent.appendChild (tache_node);
								}.bind (this));
							} else {
								ld_remove (tache_node);
							}
							ld_fire (tache_node, 'dom:resizetache');
						}
					}.bind (this))
				}
				break;
			case 'conge':

				if ( vars['dateDebut' + Table] || vars['heureDebut' + table] || vars['dateFin' + Table] || vars['heureFin' + table] ) {

					ld_qsa ('[data-table=' + table + '][data-table_value=' + table_value + ']').forEach (function (tache_node) {
						if ( tache_node.getAttribute ('data-parent') ) {
							var data_parent = ld_el (tache_node.getAttribute ('data-parent'));
							if ( !data_parent ) return;
							var attr = {}
							if ( vars['dateDebut'+ Table] ) attr['data-datedebut'] = vars['dateDebut' + Table];
							if ( vars['heureDebut'+ Table] ) attr['data-heuredebut'] = vars['heureDebut' + Table];
							if ( vars['dateFin'+ Table] ) attr['data-datefin'] = vars['dateFin' + Table];
							if ( vars['heureFin'+ Table] ) attr['data-heurefin'] = vars['heureFin' + Table];
							if ( vars['duree'+ Table] ) attr['data-duree'] = vars['duree' + Table];

							ld_writeAttributes (tache_node, attr);

							data_parent.appendChild(tache_node);


						}
					})
				}
				break;
		}

	} else {
		console.log ('desinscription data_live ' + table);
		delete window.data_subscribe[table];
	}
}

act_update_mdl = function (data) {
	// mdl,value,data
	var mdl = data.mdl, value = data.value, html = data.html;

	if ( !window.timer_stream_upd_mdl ) window.timer_stream_upd_mdl = [];
	if ( ld_qsa ('[mdl="' + mdl + '"][value="' + value + '"]') ) {
		console.log (data);
		//
		if ( window.timer_stream_upd_mdl[mdl + '_' + value] ) clearTimeout (window.timer_stream_upd_mdl[mdl + '_' + value])
		window.timer_stream_upd_mdl[mdl + '_' + value] = setTimeout (function () {
			//
			ld_qsa ('[mdl="' + mdl + '"][value="' + value + '"]').forEach (function (n) { ld_update (n, html); });
		}.bind (this), 0)
	}
}
act_add_data   = function (data) {
	var table = data.table;

	if ( !window.timer_stream_add_data ) window.timer_stream_add_data = [];
	if ( ld_qsa ('[data-table=' + table + '][data-uniqid]') ) {
		//
		if ( window.timer_stream_add_data[table] ) clearTimeout (window.timer_stream_add_data[table])
		window.timer_stream_add_data[table] = setTimeout (function () {
			//
			ld_qsa ('[data-table=' + table + '][data-uniqid]').forEach (function (n) { ld_fire (n, 'dom:data_reload', data); });
		}.bind (this), 0)
	}
}

act_close_mdl = function (data) {
	var vars = data.vars;
	var id   = 'id' + vars.table;
	if ( document.body.querySelector ('[scope=' + id + ']') ) {
		ld_qsa ('[scope=' + id + '][value=' + vars.table_value + ']').forEach (function (n) { ld_fire (n, 'dom:close'); });
		ld_qsa ('[scope=' + id + '][value=' + vars.table_value + ']').forEach (function (n) { ld_remove (n); });
	}
	ld_qsa ('[data-table=' + vars.table + '] [data-table_value=' + vars.table_value + ']').forEach (function (n) { ld_remove (n); });
}
