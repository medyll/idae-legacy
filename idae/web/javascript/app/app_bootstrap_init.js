/**
 * Created by lebru_000 on 28/11/2015.
 */



/*var scheme_store = localforage.createInstance({
 name: "scheme_store"
 });*/


// alert(Cookies.get('PHPSESSID'),res.PHPSESSID)
function idae_log() {

	if ( Cookies.get ('PHPSESSID') ) {

		get_data ('json_ssid', {}).then (function (res) {
			console.log ('json_ssid')
			console.log (res)
			res = JSON.parse (res);
			if ( res.PHPSESSID == Cookies.get ('PHPSESSID') ) {
				if ( !res.SESSID || res.SESSID == 0 ) {
					request_login ();

				} else {
					if ( res.SESSID != localStorage.getItem ('SESSID') ) {
						request_login ();
					} else {
						// GRANT_IN
						socket.emit ('grantIn', {
							DOCUMENTDOMAIN : window.document.location.hostname,
							IDAGENT        : Cookies.get ('idagent'),
							SESSID         : Cookies.get ('idagent'),
							PHPSESSID      : Cookies.get ('PHPSESSID')
						}, function (data) {
							// $('inBody').socketModule('app/app_gui/app_gui_main');
							// hide_login();
							console.log (data)
						})

						//
						if ( localStorage.getItem ('popup') ) {
							//
							popup  = localStorage.getItem ('popup')
							arrpop = JSON.parse (popup);
							//
							if ( typeof(arrpop) == 'string' ) arrpop = JSON.parse (arrpop);
							console.log ('popup  !!! ');
							console.log (arrpop);
							// MODULE
							$ ('inBody').socketModule (arrpop.mdl, arrpop.vars, { cache : false });

							localStorage.removeItem ('popup')
						} else {
							$ ('inBody').socketModule ('app/app_gui/app_gui_main', '');// , {cache: true}
						}
						// $('main_progress').hide();
					}
				}
			} else {
				// localStorage.removeItem('PHPSESSID');
				get_data ('json_ssid', {}).then (function (res) {
					res = JSON.parse (res);
					localStorage.setItem ('PHPSESSID', res.PHPSESSID);
					localStorage.setItem ('APPID', res.PHPSESSID);
					request_login ();
				})
			}
		})
	} else {
		console.log ('PAS DE PHPSESSID')
		get_data ('json_ssid', {}).then (function (res) {
			console.log (res)

			res = JSON.parse (res);

			localStorage.setItem ('PHPSESSID', res.PHPSESSID);
			localStorage.setItem ('APPID', res.PHPSESSID);
			request_login ();
		})
	}
}

function request_login() {
	// $('main_progress').hide();
	var div_login    = document.createElement ("div");
	$ (div_login).setStyle ({ position : 'absolute', 'bottom' : 0, 'width' : '100%', 'height' : '100%' });
	document.body.appendChild (div_login);
	$ (div_login).id = 'div_login';
	$ (div_login).socketModule ('app/app_login/app_login', '', { cache : true });
	return div_login;
}
function hide_login() {
	// $('main_progress').hide();
	if ( !$ ('div_login') ) return;
	$ ('div_login').fade ();

}
