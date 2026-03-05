/*if ('serviceWorker' in navigator) {
 navigator.serviceWorker.register('http://localhost/idaertys_preprod.mydde.fr/web/javascript/app/app_worker.js', {scope: 'javascript/app/app_worker/'}).then(function (reg) {
 // registration worked
 console.log('registration worked')

 }).catch(function (error) {
 // registration failed
 console.log('registration failed',error)
 });
 } else {
 console.log('serviceWorker absent')
 }
 ;*/

Storage.prototype.set = function (key, value) {
	this.setItem (key, JSON.stringify (value));
};

Storage.prototype.get = function (key) {
	var value = this.getItem (key);

	return value && JSON.parse (value);
};

switch (window.document.location.hostname) {
	case 'localhost':
		var DOCUMENTDOMAIN = window.document.location.host;
		break;
	case '192.168.11.44':
		var DOCUMENTDOMAIN = window.document.location.host;
		break;

	default:
		var DOCUMENTDOMAIN = window.document.location.host;
		break;
}

function runModule(file, vars, options) {

	options = options || {};
	setTimeout (function () {
		opt = {
			DOCUMENTDOMAIN : window.document.location.host,
			file           : file,
			mdl           : file,
			vars           : vars,
			options        : options
		}

		if ( localStorage.getItem ('PHPSESSID') ) {opt.PHPSESSID = localStorage.getItem ('PHPSESSID');}
		if ( localStorage.getItem ('SESSID') ) {opt.SESSID = localStorage.getItem ('SESSID');}
		// if ( localStorage.getItem ('SSSAVEPATH') ) {opt.SSSAVEPATH = localStorage.getItem ('SSSAVEPATH');}
		// console.log('runmodule',opt)
		socket.emit ('runModule', opt)
	}.bind (this), 0);
}

var get_data = function (file, file_vars, options) {
	var options = Object.extend ({}, options || {});

	if ( options.stream_to ) file_vars.stream_to = options.stream_to;
	var ajaxOption = {},
	    data_vars  = {
		    DOCUMENTDOMAIN : DOCUMENTDOMAIN,
		    mdl            : file,
		    vars           : file_vars,
		    options        : options,
		    directory      : options.directory || null,
		    extension      : options.extension || null
	    };
	delete options.directory , options.extension;
	//  console.log('get_data',file, data_vars, options);
	return new RSVP.Promise (function (resolve, reject) {

		if ( localStorage.getItem ('SESSID') ) data_vars.SESSID = localStorage.getItem ('SESSID');
		if ( localStorage.getItem ('PHPSESSID') ) data_vars.PHPSESSID = localStorage.getItem ('PHPSESSID');
		// if ( localStorage.getItem ('SSSAVEPATH') ) data_vars.SSSAVEPATH = localStorage.getItem ('SSSAVEPATH');

 		socket.emit ('get_data', data_vars, options, function (data) {

			setTimeout (function () {
				resolve (data);
			}.bind (this), 0);
		})
	})
};

var upd_data = function (file_vars, options) {
	return new RSVP.Promise (function (resolve, reject) {
		var ajaxOption = {},
		    options    = Object.extend (ajaxOption, options || {}),
		    data_vars  = {
			    DOCUMENTDOMAIN : DOCUMENTDOMAIN,
			    vars           : file_vars,
			    options        : options
		    };

		if ( localStorage.getItem ('SESSID') ) data_vars.SESSID = localStorage.getItem ('SESSID');
		if ( localStorage.getItem ('PHPSESSID') ) data_vars.PHPSESSID = localStorage.getItem ('PHPSESSID');
		// if ( localStorage.getItem ('SSSAVEPATH') ) data_vars.SSSAVEPATH = localStorage.getItem ('SSSAVEPATH');

		socket.emit ('upd_data', data_vars, function (data) {
			resolve (data);
		})
	})
};

var go_json = function (table, options) {
	this.options = Object.extend ({}, options || {});
	// JSGUI onglet
	red = JSGUI.add ({
		title   : table,
		taskBar : $ ('taskBar')
	})
	//
	var zone = APP.APPOBJ.build_big (red);
	//
	new myddeExplorer ($ (zone.element));
}

