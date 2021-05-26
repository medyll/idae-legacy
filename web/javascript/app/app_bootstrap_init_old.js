/**
 * Created by lebru_000 on 28/11/2015.
 */


/*var scheme_store = localforage.createInstance({
 name: "scheme_store"
 });*/

schemeLoad = function () {
	// console.log('go scheme_l
	// oad')
	var i_scheme_load = 0;
	window.scheme_store = localforage.createInstance({
		name: "scheme_store",
		driver: localforage.INDEXEDDB
	});

	if (!window.APP)window.APP = [];
	if (!window.APP.APPSCHEMES)window.APP.APPSCHEMES = [];
	if (!window.APP.APPFIELDS)window.APP.APPFIELDS = [];
	if (!window.APP.APPSCHEMES)window.APP.APPFIELDSBOOL = [];
	if (!window.APP.APPSCHEMES)window.APP.APPFIELDSBOOL = [];


	return new RSVP.Promise(function (resolve, reject) {
		app_init_template().then(function(edd){
			i_scheme_load++;
			if (i_scheme_load == 3) {
				resolve('ok')
			}
		})
		scheme_store.getItem('json_scheme_scheme').then(function (edd) {
			// console.log('json_scheme_scheme from store ',edd);
			if (edd != null) {
				window.APP.APPSCHEMES = edd;
				console.log('scheme_store ok');
				resolve('ok');
			}else{
				console.log('scheme_store bad');
			}
			get_data('json_scheme', {piece: 'scheme'}).then(function (res) {

				window.APP.APPSCHEMES = [];
				res = JSON.parse(res);
				$A(res).forEach(function (node) {
					table = node.codeAppscheme;
					// if (!window.APP.APPSCHEMES) window.APP.APPSCHEMES = {};
					if (!window.APP.APPSCHEMES[table]) {
						window.APP.APPSCHEMES[table] = node;
					}
				});
				window.scheme_store.setItem('json_scheme_scheme', $H(window.APP.APPSCHEMES));
				i_scheme_load++;
				if (i_scheme_load == 3) {
					resolve('ok')
				}
			});
		})

		scheme_store.getItem('json_scheme_field').then(function (edd) {
			// console.log('json_scheme_field from store ',edd);
			// 	if(edd!=null){window.APP.APPFIELDS=edd;console.log('scheme_field ok');resolve('ok');}
			get_data('json_scheme_field', {piece: 'scheme'}).then(function (res) {

				window.APP.APPFIELDS = [];
				res = JSON.parse(res);

				for (var field_key in res) {
					if (res.hasOwnProperty(field_key)) {

						if (!window.APP.APPFIELDS[field_key]) {
							window.APP.APPFIELDS[field_key] = res[field_key];
						}
					}
				}
				window.scheme_store.setItem('json_scheme_field', $H(window.APP.APPFIELDS));
				i_scheme_load++;
				if (i_scheme_load == 3) {
					resolve('ok')
				}

			});
		})

		/*window.scheme_store.iterate(function(value, key, iterationNumber) {
		 console.log('scheme_store ', [key, value],value);
		 }, function(err) {
		 if (!err) {
		 console.log('Iteration has completed');
		 }
		 });*/

		/*get_data('json_scheme', {piece: 'scheme'}).then(function (res) {

		 window.APP.APPSCHEMES = [];
		 res = JSON.parse(res);
		 $A(res).forEach(function (node) {
		 table = node.codeAppscheme;
		 // if (!window.APP.APPSCHEMES) window.APP.APPSCHEMES = {};
		 if (!window.APP.APPSCHEMES[table]) {
		 window.APP.APPSCHEMES[table] = node;
		 }
		 });
		 scheme_store.setItem('json_scheme_scheme',$H(window.APP.APPSCHEMES));
		 i_scheme_load ++;
		 if(i_scheme_load==1){resolve('ok')}
		 });*/

		/*get_data('json_scheme', {piece: 'fields'}).then(function (res) {
		 window.APP.APPFIELDS = [];
		 res                  = JSON.parse(res);
		 $A(res).forEach(function (node) {
		 window.APP.APPFIELDS.push(node);
		 });
		 scheme_store.setItem('json_scheme_fields',window.APP.APPFIELDS);
		 i_scheme_load ++;
		 if(i_scheme_load==4){resolve('ok')}
		 });*/

		/*get_data('json_scheme', {piece: 'bool_fields'}).then(function (res) {
		 res                      = JSON.parse(res);
		 window.APP.APPFIELDSBOOL = res
		 scheme_store.setItem('json_scheme_bool_fields',res);
		 i_scheme_load ++;
		 if(i_scheme_load==4){resolve('ok')}
		 });*/

		/*get_data('json_scheme', {piece: 'bool_fields_icon'}).then(function (res) {
		 res                          = JSON.parse(res);
		 window.APP.APPFIELDSBOOLICON = res
		 scheme_store.setItem('json_scheme_bool_fields_icon',res);
		 i_scheme_load ++;
		 if(i_scheme_load==4){resolve('ok')}
		 });*/
	})


}
construct_args = function (argArray) {
	var inst = Object.create();
	constr.apply(inst, argArray);
	return inst;
};
// alert(Cookies.get('PHPSESSID'),res.PHPSESSID)
idae_log = function () {
	if (Cookies.get('PHPSESSID')) {
		get_data('json_ssid', {}).then(function (res) {
			res = JSON.parse(res);
			if (res.PHPSESSID == Cookies.get('PHPSESSID')) {
				if (!res.SESSID || res.SESSID == 0) {
					request_login();

				} else {
					if (res.SESSID != localStorage.getItem('SESSID')) {
						request_login();
					} else {
						// GRANT_IN
						socket.emit('grantIn',{DOCUMENTDOMAIN:window.document.location.hostname,IDAGENT:Cookies.get('idagent'),SESSID:Cookies.get('idagent'),PHPSESSID:Cookies.get('PHPSESSID'),SSSAVEPATH:Cookies.get('SSSAVEPATH')},function(data){
							// $('inBody').socketModule('app/app_gui/app_gui_main');
							// hide_login();
							console.log(data)
						})

						//
						if (localStorage.getItem('popup')) {
							//
							popup = localStorage.getItem('popup')
							arrpop = JSON.parse(popup);
							//
							if(typeof(arrpop)=='string') arrpop = JSON.parse(arrpop);
							console.log('popup  !!! ');
							console.log(arrpop);
							// MODULE
							$('inBody').socketModule(arrpop.mdl, arrpop.vars, {cache: false});

							localStorage.removeItem('popup')
						} else {
							$('inBody').socketModule('app/app_gui/app_gui_main', '');// , {cache: true}
						}
						// $('main_progress').hide();
					}
				}
			} else {
				// localStorage.removeItem('PHPSESSID');
				get_data('json_ssid', {}).then(function (res) {
					res = JSON.parse(res);
					localStorage.setItem('PHPSESSID', res.PHPSESSID);
					localStorage.setItem('APPID', res.PHPSESSID);
					request_login();
				})
			}
		})
	} else {
		console.log('PAS DE PHPSESSID')
		get_data('json_ssid', {}).then(function (res) {
			console.log(res)

			res = JSON.parse(res);

			localStorage.setItem('PHPSESSID', res.PHPSESSID);
			localStorage.setItem('APPID', res.PHPSESSID);
			request_login();
		})
	}
}

request_login = function () {
	// $('main_progress').hide();
	var div_login = document.createElement("div");
	$(div_login).setStyle({position: 'absolute', 'bottom': 0, 'width': '100%', 'height': '100%'});
	document.body.appendChild(div_login);
	$(div_login).id = 'div_login';
	$(div_login).socketModule('app/app_login/app_login', '', {cache: true});
	return div_login;
}
hide_login = function () {
	// $('main_progress').hide();
	if (!$('div_login')) return;
	$('div_login').fade();

}

progress_plus = function (value, max, text) {
	if (!($('main_progress'))) {
		var prog_hold = '<div id="main_progress_hold"  style="z-index:500000;bottom:0;position:absolute;width:100%;left:0;padding:0;background-color:#ededed;border-top:1px solid #ccc;">' +
			'<div style="padding:1em;position:relative;"><div id="main_progress_text" ></div></div>' +
			'<div style="padding:1em;position:relative;"><progress id="main_progress" min="0"></progress></div>' +
			'</div>';
		document.body.insert(prog_hold);
		$('main_progress').setStyle({position: 'relative', 'bottom': 0, 'left': '50px', 'width': 'calc(100% - 100px)', margin: '0 auto'});
		$('main_progress').show();
		$('main_progress_hold').show();
	}
	$('main_progress').value = value;
	$('main_progress').max = max;
	if (text) {
		$('main_progress_text').update(text)
	}
	//
	if (value == max) {
		$('main_progress_hold').fade();
	}

}