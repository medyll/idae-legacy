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

/*
 * get_data resilience — see the long comment inside get_data() for why this
 * exists at all.
 *
 * Two independent recovery paths, because neither alone is enough:
 *
 *   - The reconnect hook below reacts to the actual failure signal. It is the
 *     precise one, but socket.io does not always emit a reconnect: the
 *     transport can be swapped out under a request without one.
 *   - A short re-emit timer catches what the hook misses. Measured: dropping
 *     it in favour of the hook alone cost the suite ~28 minutes of boot
 *     timeouts, so it is doing real work, not defensive padding.
 *
 * Only re-emittable reads get the short timer. Anything that streams or
 * writes (see `retryable` below) is sent once and only gets the long
 * backstop, which sits above the Node bridge's own 30s bound
 * (app_node/src/services/phpBridge.js) so it fires only when the ack is
 * genuinely never coming — a slow json_data_table must not be mistaken for a
 * lost one.
 */
var GET_DATA_RETRY_DELAY = 10000;
var GET_DATA_MAX_ATTEMPTS = 3;
var GET_DATA_ACK_TIMEOUT = 45000;

// In-flight retryable requests, re-emitted when the socket reconnects.
var get_data_inflight = [];
var get_data_reconnect_hooked = false;

function get_data_hook_reconnect() {
	if ( get_data_reconnect_hooked || typeof socket !== 'object' || !socket ) return;
	get_data_reconnect_hooked = true;
	// A reconnect gets a new sid; anything the server acks after that answers
	// the dead connection and never reaches us. Re-emitting on the new one is
	// the only way those promises ever settle.
	['reconnect', 'connect'].forEach (function (evt) {
		socket.on (evt, function () {
			if ( !get_data_inflight.length ) return;
			console.warn ('[get_data] socket reconnected - re-emitting', get_data_inflight.length, 'in-flight request(s)');
			get_data_inflight.slice ().forEach (function (entry) {
				entry.resend ();
			});
		});
	});
}

var get_data = function (file, file_vars, options) {
	var options = Object.assign ({}, options || {});

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

		// An emit whose ack never comes back used to hang forever. socket.io
		// drops in-flight acks when the connection is replaced (a reconnect
		// gets a new sid, and the server answers the old one), which is
		// exactly what killed the boot: schemeLoad() awaits three get_data
		// promises, and one lost ack left APPSCHEMES empty with no login form,
		// no #desktop, no error — a blank app until reload.
		//
		// Most endpoints here are plain reads (json_scheme, json_ssid,
		// json_data, json_droit_table…) and re-emitting them is free. Two
		// shapes are not: a streaming request (`stream_to`) pushes its chunks
		// into the DOM as they arrive, so a second run duplicates them, and
		// `csv_export` makes json_data_table write. Those are never re-emitted
		// — they only get the backstop, so a lost ack surfaces as a rejection
		// instead of a hang, without anything being sent twice.
		var settled   = false;
		var timer     = null;
		var entry     = null;
		var attempts  = 0;
		var retryable = !options.stream_to &&
			String (typeof file_vars === 'string' ? file_vars : JSON.stringify (file_vars || {})).indexOf ('csv_export') === -1;

		var settle = function (fn, value) {
			if ( settled ) return;
			settled = true;
			if ( timer ) {
				clearTimeout (timer);
				timer = null;
			}
			if ( entry ) {
				var at = get_data_inflight.indexOf (entry);
				if ( at !== -1 ) get_data_inflight.splice (at, 1);
				entry = null;
			}
			fn (value);
		};

		var done = function (data) {
			settle (function (payload) {
				setTimeout (function () {
					resolve (payload);
				}, 0);
			}, data);
		};

		var arm = function () {
			if ( settled ) return;
			var retrying = retryable && attempts < GET_DATA_MAX_ATTEMPTS;
			timer = setTimeout (function () {
				timer = null;
				if ( settled ) return;
				if ( retrying ) {
					console.warn ('[get_data] no ack for', file, '- re-emitting, attempt', attempts + 1);
					return send ();
				}
				console.error ('[get_data] no answer for', file, 'after', attempts, 'attempt(s)');
				settle (reject, new Error ('get_data timeout: ' + file));
			}, retrying ? GET_DATA_RETRY_DELAY : GET_DATA_ACK_TIMEOUT);
		};

		var send = function () {
			if ( settled ) return;
			attempts++;
			if ( timer ) {
				clearTimeout (timer);
				timer = null;
			}
			socket.emit ('get_data', data_vars, options, done);
			// A synchronous ack settles inside the emit above; arming then
			// would leave a timer running for nothing.
			arm ();
		};

		get_data_hook_reconnect ();

		if ( retryable ) {
			entry = { file: file, resend: send };
			get_data_inflight.push (entry);
		}

		send ();
	})
};

var upd_data = function (file_vars, options) {
	return new RSVP.Promise (function (resolve, reject) {
		var ajaxOption = {},
		    options    = Object.assign (ajaxOption, options || {}),
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

// go_json() removed 2026-08-10 (BE_PLAN.md phase 5): zero callers anywhere
// in the repo, confirmed by grep. It also referenced `windowJSGUI` and
// `APP.APPOBJ.build_big`, neither of which exists anywhere else in this
// codebase — dead code calling into more dead code.

