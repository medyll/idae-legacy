/**
 * Created by lebru_000 on 17/08/15.
 */

var user_id = 0;

var webSocket = {};
webSocket = Class.create();
webSocket.prototype = {
	initialize   : function (options) {

		this.options = Object.extend({
			serverUrl: 'ws://idaertys.mydde.fr:9000'
		}, options || {});

		this.build();
	},
	build        : function () {
		if (window.MozWebSocket) {
			this.web_socket = new MozWebSocket(this.options.serverUrl);
		} else if (window.WebSocket) {
			this.web_socket = new WebSocket(this.options.serverUrl);
		}
		//
		this.web_socket.binaryType = 'blob';

		this.listen();
	},
	listen       : function () {
		this.web_socket.onopen = function (msg) {
			$$('.status').invoke('update','<span class="label label-info">connection</span>');
			this.register_user();
			return true;
		}.bind(this);

		this.web_socket.onmessage = function (msg) {

			var response;
			response = JSON.parse(msg.data);
			this.checkJson(response);
			return true;
		}.bind(this);

		this.web_socket.onclose = function (msg) {
			$$('.status').invoke('update','<span class="label label-danger">Disconnected</span>');
			setTimeout(function () {
					this.register_user();
				}.bind(this)
				, 2000);
			return true;
		}.bind(this);
	},
	register_user: function () {
		payload = new Object();
		payload.action = 'register';
		payload.PHPSESSID = localStorage.getItem('PHPSESSID');

		this.web_socket.send(JSON.stringify(payload));
	},
	checkJson    : function (res) {
		if(res.OWN && res.OWN != this.PHPSESSID) return;

		switch (res.action){
			case 'registred' :

				if(res.PHPSESSID){
					this.PHPSESSID = res.PHPSESSID;
				}
				// $('#status').html('<span class="label label-success">Registred</span>');
				// $('#chat_button').removeAttr('disabled');
				// $('#text_message').removeAttr('disabled');
				// $('#chat-head').html('<b>User-' + res.user_id + '</b> (' + res.users_online + ' Users Online)');
				// this.user_id = res.user_id;
			break;
			case 'chat_text':
				if (res.user_id == this.user_id) {

				}
				break;
			case 'loadModule':
					// res.element
					// res.response
				if(res.element) {
					$(res.element).update(res.response);
					afterAjaxCall($(res.element));
					$(res.element).fire('content:loaded');
				}
				break;
			case 'send_cmd':
				var data = res;
				switch (res.cmd) {
					case'act_close_mdl':
						var vars = data.vars;
						var id = 'id' + vars.table;
						if (document.body.querySelector('[scope=' + id + ']')) {
							$$('[scope=' + id + '][value=' + vars.table_value + ']').invoke('fire', 'dom:close');
							$$('[scope=' + id + '][value=' + vars.table_value + ']').invoke('remove');
						}
						$$('[data-table=' + vars.table + '] [data-table_value=' + vars.table_value + ']').invoke('remove');
						// unstream_from_cache

						break;
					case'act_upd_data': // remplace  upd_data({table: table, table_value: table_value});
						// recevoir aussi les datas ici !!!!
						var vars = data.vars || ''; // les valeurs
						var table_value = vars.table_value;
						var table = vars.table;

						if ($$('[data-table=' + table + ']')) {

							for (var property in vars) {
								if (vars.hasOwnProperty(property)) {
									// data_vars[property] = vars[property];
									$$('[data-table=' + table + '][data-table_value=' + table_value + '] [data-field_name=' + property + ']').invoke('update',vars[property]);
								}
							}

							$$('[data-table=' + table + '][data-uniqid]').invoke('fire', 'dom:data_reload',vars);

						}

						break;

					case'act_add_data':
						var vars = data.vars;
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
						}
						break;
					case'act_reload_img':
						var vars = data.vars;
						var filename = vars.filename;
						$$('[data-filename=' + filename + ']').each(function (node) {
							node.src = "blank.png";
							node.src = node.readAttribute('data-src') + '?act_reload=' + uniqid();
						}.bind(this));

						break;

					case'act_progress':
						var vars = data.vars;
						var name_p = 'auto_' + vars.progress_name;
						if (!$(name_p) && $('loader_progress_pane')) {
							$('loader_progress_pane').insert({top: '<div id="auto_progress_div" class="padding margin border4" style="max-height:450px;overflow:auto;"><progress id="' + name_p + '"></progress></div>'})
						}

						if ($(name_p)) {
							if ($(name_p).time_p) clearTimeout($(name_p).time_p);
							$(name_p).show();
							if (vars.progress_value) $(name_p).value = vars.progress_value;
							if (vars.progress_max) $(name_p).max = vars.progress_max;
							if (vars.progress_value && vars.progress_max) {
								if (vars.progress_value == vars.progress_max) {
									$(name_p).time_p = setTimeout(function () {
										$(name_p).hide();
									}.bind(this), 2000)
								}
							}
							if (vars.progress_text) {
								if (!$('text_' + name_p)) {
									var text_ = new Element('div', {id: 'progress_text' + name_p});
									$(name_p).insert({before: '<div id="text_' + name_p + '">' + vars.progress_text + '</div>'})
								} else {
									var text_ = $('text_' + name_p);
								}
							}
							if (vars.progress_text_remove) {
								if ($('text_' + name_p)) {
									$('text_' + name_p).remove();
								}
							}
							if (vars.progress_message) {
								if (!$('msg_' + name_p)) {
									var msg_ = new Element('div', {id: 'msg_' + name_p});
									$(name_p).insert({after: msg_})
								} else {
									var msg_ = $('msg_' + name_p);
								}
								msg_.update(vars.progress_message);
							}

							if (vars.progress_message_remove) {
								if ($('msg_' + name_p)) {
									$('msg_' + name_p).remove();
								}
							}
							if (vars.progress_log) {
								if (!$('log_' + name_p)) {
									var msg_ = new Element('div', {id: 'log_' + name_p, style: 'overflow:auto;max-height:200px'});
									msg_.setStyle({overflow: 'auto', maxHeight: '3050px'});
									$(name_p).insert({after: msg_})
								} else {
									var msg_ = $('log_' + name_p);
								}
								msg_.insert({top: '<div>' + vars.progress_log + '</div>'});
							}
						}
						;

						break;
					case'act_run':
						// console.log('act_run', data)
						var vars = data.vars;
						break;
					case'act_debug':
						// console.log('act_debug', data)
						var vars = data.vars;
						break;
					case'act_stream_to':
						// console.log(data)
						var data = data;
						var dvars = data.vars;
						var stream_to = dvars.stream_to;
						if ($$('[data-uniqid=' + stream_to + ']').size() != 0) {
							var tmp_stream = uniqid('uniqid');
							if (!window.register_stream) window.register_stream = []
							window.register_stream[tmp_stream] = dvars;

							$$('[data-uniqid=' + stream_to + ']')[0].fire('dom:stream_chunk', tmp_stream);

						} else {

							// runModule('actions', 'F_action=unstream_to&stream_to=' + stream_to);
						}

						break;

					case'act_notify':
						//
						var vars = data.vars;
						var opt = vars.options || {};
						try {
							var a = new myddeNotifier();
							a.growl(vars.msg, opt);
						} catch (e) { }


						break;

					case 'act_gui':
						//alert('act_gui')
						// console.log(data);
						var vars = data.vars;
						var opt = vars.options || {};
						opt.title = data.title || data.mdl;

						new act_chrome_gui(vars.mdl,vars.vars, opt);

						break;
				}
				break;
		}


	},
	send : function(msg){
		payload = new Object();
		payload.user_id = localStorage.getItem('PHPSESSID');
		payload.PHPSESSID = localStorage.getItem('PHPSESSID');

		if(typeof msg == 'object'){
			payload.action = msg.action || 'chat_text';
			payload.data = msg || '';
		}else{
			obj_msg = JSON.parse(msg);
			payload.action = obj_msg.action || 'chat_text';
			payload.data = obj_msg || '';
		}


		this.web_socket.send(JSON.stringify(payload));
	},
	emit_action : function(action,data){
		payload = new Object();
		payload.action = action;
		payload.user_id = this.user_id;
		payload.data = data;
		this.web_socket.send(JSON.stringify(payload));
	}
}

if(!window.do_websocket){
	// window.do_websocket = new webSocket();
}


/*

	$("#chat_button").click(function () {
		console.log('Triggred');
		payload = new Object();
		payload.action = 'chat_text';
		payload.user_id = user_id;
		payload.chat_text = $('#text_message').val();
		web_socket.send(JSON.stringify(payload));
	});
 */
