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
							document.getElementById ('inBody').socketModule (arrpop.mdl, arrpop.vars, { cache : false });

							localStorage.removeItem ('popup')
						} else {
							document.getElementById ('inBody').socketModule ('app/app_gui/app_gui_main', '');// , {cache: true}
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
	// Modified: 2026-08-11 — migrated off the $ / setStyle shims. div_login is
	// an element this function just created, so `$(div_login)` was already a
	// no-op: the shim patches HTMLElement.prototype and hands the node back.
	div_login.style.position = 'absolute';
	div_login.style.bottom = 0;
	div_login.style.width = '100%';
	div_login.style.height = '100%';
	document.body.appendChild (div_login);
	div_login.id = 'div_login';
	div_login.socketModule ('app/app_login/app_login', '', { cache : true });
	return div_login;
}
function hide_login() {
	// $('main_progress').hide();
	if ( !document.getElementById ('div_login') ) return;
	// Was Scriptaculous' Effect.Fade via the shim, replaced by a native
	// opacity transition (fadeElement, engine/methods.js) — same contract:
	// fade to 0, then hide and restore opacity for the next show().
	fadeElement (document.getElementById ('div_login'));

}
