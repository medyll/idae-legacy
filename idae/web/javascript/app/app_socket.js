
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
	$ ('msg_log').update (data);
}.bind (this));
socket.on ('heartbeat_app', function (data) {
	$ ('msg_log').update (data);
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
				$$ ('[scope=' + id + '][value=' + vars.table_value + ']').invoke ('fire', 'dom:close');
				$$ ('[scope=' + id + '][value=' + vars.table_value + ']').invoke ('remove');
			}
			$$ ('[data-table=' + vars.table + '] [data-table_value=' + vars.table_value + ']').invoke ('remove');
			// unstream_from_cache

			$A (document.body.querySelectorAll ('[data-table="' + vars.table + '"][data-count]')).each (function (node) {
				var vars = $ (node).readAttribute ('data-vars');
				runModule ('services/json_data_table', 'table=' + table + '&' + vars + '&piece=count&count_id=' + node.identify ());
			}.bind (this))

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

			$A (document.body.querySelectorAll ('[data-table="' + vars.table + '"][data-count]')).each (function (node) {
				var vars = $ (node).readAttribute ('data-vars');
				runModule ('services/json_data_table', 'table=' + table + '&' + vars + '&piece=count&count_id=' + node.identify ());
			}.bind (this))

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

			$A (document.body.querySelectorAll ('[data-table="' + vars.table + '"][data-count]')).each (function (node) {
				var vars = $ (node).readAttribute ('data-vars');
				runModule ('services/json_data_table', 'table=' + table + '&' + vars + '&piece=count&count_id=' + node.identify ());
			}.bind (this))
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
			$ (count_id).addClassName ('animated bounce');
			$ (count_id).update (count);
			if ( $ (count_id).up ('[data-count_trigger]') ) {
				if ( eval (count) != 0 ) {
					$ (count_id).up ('[data-count_trigger]').setAttribute('data-count_trigger','true');
					// $ (count_id).up ('[data-count_trigger]').show ();
				} else {
					$ (count_id).up ('[data-count_trigger]').setAttribute('data-count_trigger','hide');
					// $ (count_id).up ('[data-count_trigger]').hide ();
				}
			}

			break;
		case'act_reload_img':
			var vars     = data.vars;
			var filename = vars.filename;
			$$ ('[data-filename=' + filename + ']').each (function (node) {
				node.src = "blank.png";
				node.src = node.readAttribute ('data-src') + '?act_reload=' + uniqid ();
			}.bind (this));

			break;

		case'act_progress':
			var vars   = data.vars;
			var name_p = 'auto_' + vars.progress_name;
			if ( !$ (name_p) && vars.progress_parent ) {
				 if($ (vars.progress_parent)){
					 $ (vars.progress_parent).insert ({ top : '<div class="flex_v" style="height:auto!important;overflow:hidden;"><progress id="' + name_p + '"></progress></div>' })
					 }
			}

			if ( $ (name_p) ) {
				if ( $ (name_p).time_p ) clearTimeout ($ (name_p).time_p);
				$ (name_p).show ();
				if ( vars.progress_value ) $ (name_p).value = vars.progress_value;
				if ( vars.progress_max ) $ (name_p).max = vars.progress_max;
				if ( vars.progress_value && vars.progress_max ) {
					if ( vars.progress_value == vars.progress_max ) {
						$ (name_p).time_p = setTimeout (function () {
							$ (name_p).hide ();
						}.bind (this), 5000)
					}
				}
				if ( vars.progress_text ) {
					if ( !$ ('text_' + name_p) ) {
						var text_ = new Element ('div', { id : 'progress_text' + name_p });
						$ (name_p).insert ({ before : '<div class="borderb padding" id="text_' + name_p + '">' + vars.progress_text + '</div>' })
					} else {
						var text_ = $ ('text_' + name_p);
					}
				}
				if ( vars.progress_text_remove ) {
					if ( $ ('text_' + name_p) ) {
						$ ('text_' + name_p).remove ();
					}
				}
				if ( vars.progress_message ) {
					if ( !$ ('msg_' + name_p) ) {
						var msg_ = new Element ('div', { className:'padding ededed',id : 'msg_' + name_p });
						$ (name_p).insert ({ before : msg_ })
					} else {
						var msg_ = $ ('msg_' + name_p);
					}
					msg_.update (vars.progress_message);
				}

				if ( vars.progress_message_remove ) {
					if ( $ ('msg_' + name_p) ) {
						$ ('msg_' + name_p).remove ();
					}
				}
				if ( vars.progress_log ) {
					if ( !$ ('log_' + name_p) ) {
						var msg_ = new Element ('div', { id : 'log_' + name_p, className:'flex_main',style : '' });
						   msg_.setStyle ({ overflow : 'auto'  });
						$ (name_p).insert ({ after : msg_ })
					} else {
						var msg_ = $ ('log_' + name_p);
					}
					msg_.insert ({ bottom : '<div class="retrait padding borderb">' + vars.progress_log + '</div>' });
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
			if ( $$ ('[data-uniqid=' + stream_to + ']').size () != 0 ) {
				// console.log('stream_to !! ',data)
				var tmp_stream = uniqid ('uniqid');
				if ( !window.register_stream ) window.register_stream = []
				window.register_stream[tmp_stream] = dvars;

				$$ ('[data-uniqid=' + stream_to + ']')[0].fire ('dom:stream_chunk', tmp_stream);
				setTimeout (function () {
					$$ ('[data-uniqid=' + stream_to + ']')[0].fire ('content:loaded', tmp_stream);
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

// Handles socketModule response to update DOM
socket.on ('socketModule', function (data) {
    if (data.out && data.out.element) {
        var el = $(data.out.element);
        if (el) {
            el.update(data.body);
            if (el.undoLoading) el.undoLoading();
            if (window.afterAjaxCall) afterAjaxCall(el);
            el.fire('content:loaded');
            
            // Handle caching if needed (mirroring app_test.js idea but simpler)
             if (window.app_cache && data.out.options && data.out.options.cache) {
                 // Cache update logic could go here
            }
        } else {
            console.warn('socketModule: Element not found', data.out.element);
        }
    }
});

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
	if ( !$ (this.out.element) ) {
		//
		return false;
	}
	var id_l = 'loading_loader_' + $ (this.out.element).identify ();
	//
	if ( $ (id_l) ) {
		$ (id_l).remove ();
	}
	var key_name = build_cache_key (this.out.file, (this.out.vars || ''));

	var cache_body = window.localStorage.get (key_name) || '';

	var data_body_compare  = data_body.replace (/(\r\n|\n|\r)/gm, "");
	var cache_body_compare = cache_body.replace (/(\r\n|\n|\r)/gm, "");

	if ( options.fragment ) {
		var fragment        = options.fragment
		var tmp_fragment    = document.createDocumentFragment ();
		var in_tmp_fragment = document.createElement ("div");

		in_tmp_fragment.innerHTML = data_body;

		var frag_node   = in_tmp_fragment.querySelector ('[data-app_fragment=' + fragment + ']');
		var frag_node_2 = in_tmp_fragment.querySelector ('div');

		$ (this.out.element).update (frag_node);
		return;
	}

	if ( localStorage.getItem ('cache_mode') == 'on' && $ (this.out.element).readAttribute ('data-cache') && $ (this.out.element).readAttribute ('data-need_cache')!="true"  ) { //  && $(this.out.element).readAttribute('data-from_cache')

		$ (this.out.element).removeAttribute ('data-from_cache');

	} else {

		$ (this.out.element).removeAttribute ('data-need_cache')
		if ( options.append ) {
			$ (this.out.element).insert (data_body);
		} else if ( options.insertion ) {
			$ (this.out.element).insert ({ top : data_body });
		} else {
			$ (this.out.element).update (data_body);
		}

		afterAjaxCall ($ (this.out.element));
		$ (this.out.element).fire ('content:loaded');
	}

	if ( localStorage.getItem ('cache_mode') == 'on' && $ (this.out.element).readAttribute ('data-cache') ) {
		window.app_cache.setItem (key_name, objDta, function (err, result) {

		});
	}

	}.bind (this));
//
socket.on ('disconnect', function () {
	if ( $ ('socket_status') )  $ ('socket_status').show ();
});
socket.on ('reconnect', function () {
	// $('body').undoLoading();
	if ( $ ('socket_status') )  $ ('socket_status').hide ();
});

socket.on ('reconnecting', function (nextRetry) {
	if ( $ ('socket_status') )    $ ('socket_status').update (' .. ' + eval (nextRetry) / 1000) + ' s ';
});
socket.on ('reconnect_failed', function () {
	if ( $ ('socket_status') ) $ ('socket_status').update (' dead ');
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
	$H (rev).each (function (node) {
		var field_name  = node.key;
		var field_value = node.value;
		$A (document.body.querySelectorAll ('[table="' + table + '"][table_value="' + table_value + '"] [field_name="' + field_name + '"]')).each (function (node) {
			$ (node).update (field_value);
		}.bind (this))
	}.bind (this))

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

						$H (data).each (function (pair) {
							data_field_name = base + '.' + table + '.' + pair.key;

							$A (document.body.querySelectorAll ('[data-field_name="' + data_field_name + '"][data-mongokey="' + _id + '"]')).each (function (node) {
								node.update (nl2br (pair.value));
							})

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