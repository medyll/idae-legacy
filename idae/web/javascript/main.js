var app_dom_loaded = false;

var require_shim = {
	'scriptaculous'          : {
		deps    : ['prototype'],
		exports : 'Scriptaculous'
	},
	'scriptaculous_dragdrop' : {
		deps    : ['prototype', 'scriptaculous', 'scriptaculous_effects'],
		exports : 'Draggable'
	},
	'scriptaculous_effects'  : {
		deps    : ['prototype', 'scriptaculous'],
		exports : 'Effect'
	},
	'methods'                : { deps : ['app_functions', 'app'] },
	'app_bootstrap'          : { deps : ['lazyload', 'app'] },
	'app_insertionQ'         : { deps : ['insertionQ'] },
	'domready'               : { exports : 'domReady' },
	'jsoncookie'             : { exports : 'CookieJar' },
	'moment'                 : { exports : 'moment' },
	'pikaday'                : { deps : ['moment'] },
	'tinyMCE'                : { exports : 'tinyMCE' },
	'lazyload'               : { exports : 'LazyLoad' },
	'socketio'               : { exports : 'io' }
}

var require_paths = {
	'app'                    : 'app/app',
	'app_functions'          : 'app/app_functions',
	'app_php'                : 'app/app_php',
	'app_bootstrap'          : 'app/app_bootstrap',
	'app_bootstrap_init'     : 'app/app_bootstrap_init',
	'app_insertionQ'         : 'app/app_insertionQ',
	'app_mutateobserve'      : 'app/app_mutateobserve',
	'methods'                : 'engine/methods',
	'basket'                 : 'vendor/basket',
	'domready'               : 'vendor/domReady',
	'prototype'              : 'vendor/prototype/prototype-1.7.3',
	'insertionQ'             : 'vendor/fromcommon/insertionQ',
	'jsoncookie'             : 'librairie/jsoncookie',
	'jscookie'               : 'vendor/js.cookie',
	'localforage'            : 'vendor/localforage',
	'lazyload'               : 'vendor/lazyload',
	'moment'                 : 'vendor/moment',
	'tinyMCE'                : 'tinymce/tinymce.min',
	'pikaday'                : 'vendor/pikaday',
	'scriptaculous'          : 'vendor/scriptaculous/scriptaculous',
	'scriptaculous_dragdrop' : 'vendor/scriptaculous/dragdrop',
	'scriptaculous_effects'  : 'vendor/scriptaculous/effects',
	'socketio'               : '//cdn.socket.io/socket.io-1.4.5'
};

var require_trame = {
	require_boot       : ['localforage', 'basket'],
	require_polyfill   : [
		'javascript/vendor/polyfill/rsvp.js',
		'javascript/vendor/polyfill/EventSource.js',
		'moment',
		'javascript/vendor/polyfill/json2.js'],
	require_hell       : [
		'prototype',
		'scriptaculous',
		'scriptaculous_dragdrop',
		'scriptaculous_effects'
	],
	require_dom        : ['domready'],
	require_insertionQ : ['insertionQ'],
	require_to_log     : [
		'app',
		'app_php',
		'app_functions',
		'methods',
		'app_bootstrap',
		'app_bootstrap_init',
		'app_insertionQ'
	],
	require_scripts    : [  // vendor
		'javascript/vendor/chartist.js',
		'javascript/vendor/chart.bundle.min.js',
		'javascript/vendor/draggabilly.pkgd.min.js',
		'javascript/vendor/lory.js',
		'javascript/vendor/rio-compiler.js',
		'javascript/vendor/detect-element-resize.js',
		'pikaday',
		'javascript/vendor/sizzle.js'],
	require_app        : [
		'app_cache',
		'app_socket',
		'app_live_data',
		'app_datatable',
		'app_keepon',
		'app_chat',
		'app_planning',
		'app_conge',
		'app_window',
		'app_calendrier',
		'app_contextual',
		'app_menu',
		'app_quickfind',
		'app_tree',
		'app_init_template'],
	require_engine     : [
		'javascript/engine/engine.js',
		'javascript/engine/afterAjaxCall.js',
		'javascript/engine/contextuel.js',
		'javascript/engine/initApp.js',
		'javascript/engine/module.js',
		'javascript/engine/doredim.js'],
	require_librairie  : [
		'javascript/librairie/observers.js',
		'javascript/librairie/appGui.js',
		'javascript/librairie/validation.js',
		'javascript/librairie/autoToggle.js',
		'javascript/librairie/resize.js',
		'javascript/librairie/myddeNotifier.js',
		'javascript/librairie/cropper.js',
		'javascript/librairie/resizeGui.js',
		'javascript/librairie/sortdiv.js',
		'javascript/librairie/tableGui.js',
		'jsoncookie',
		'javascript/librairie/textarea.js',
		'javascript/librairie/picPicker.js',
		'javascript/librairie/mask.js',
		'javascript/librairie/myddeupload.js',
		'javascript/librairie/myddeAttach.js',
		'javascript/librairie/myddeview.js',
		'javascript/librairie/myddeSelection.js',
		'javascript/librairie/myddeExplorer.js',
		'javascript/librairie/myddeDatalist.js',
		'javascript/librairie/sorttable.js']
}

// BANG
var require_size = 0;// .push(data);
var require_queue = [];// .push(data);
var ct_l = 0;

for (var prop in require_trame) {
	if ( require_trame.hasOwnProperty (prop) ) {
		require_size += require_trame[prop].length
		require_queue.push (prop);
	}
}

require_trame.require_app.forEach (function (thread) {
	window.require_shim[thread]  = { deps : ['prototype'] }
	window.require_paths[thread] = 'app/' + thread
})

var require_progress_size  = require_size;
var require_progress_count = 0;
// CONFIG
require.config ({
	baseUrl       : 'javascript',
	shim          : require_shim,
	paths         : require_paths,
	waitSeconds   : 60,
	onNodeCreated : function (node, config, moduleName, url) {
		// console.log('module ' + moduleName + ' is about to be loaded');
		node.addEventListener ('load', function () {
			if ( moduleName == 'insertionQ' ||  moduleName == 'domready' ) {
				console.log (moduleName)
			}
			require_progress (++require_progress_count, require_progress_size, moduleName + ' chargé');
		});

		node.addEventListener ('error', function () {
			console.log ('module ' + moduleName + ' could not be loaded');
		});
	}
});

requirejs.onError = function (err) {
	console.log (err.requireType, err);
	if ( err.requireType === 'timeout' ) {
		console.log ('modules: ' + err.requireModules);
	}

	throw err;
};

function dyn_require() {
	ct_l++;
	if ( ct_l == 1 )console.time ('require_all')
	if ( !require_queue[0] ) {
		// idae_log ();
		console.timeEnd ('require_all');

		console.log ('idae_logm domready');
		idae_log ();

		/*require(['insertionQ'],function(wazza){
		 console.log(wazza)
		 window.insertionQ = wazza
		 /!*insq('div').every(function (node) {

		 node.setAttribute('autocomplete', 'off');
		 node.setAttribute('type', 'text');

		 });*!/

		 } )*/

		return;
	}
	var require_elem = require_queue[0];
	require_queue.shift ();

	require (require_trame[require_elem], function () {
		if ( ct_l > 500 ) return;
		if ( require_elem == 'require_to_log' ) {
			console.log ('stuff from domready');
			/*if ( app_dom_loaded === true ) {
			 console.log ('stuff from require_to_log');
			 idae_log ();
			 }
			 app_dom_loaded = true;*/
		}

		dyn_require ()
	});
}

function require_progress(value, max, text) {
	if ( !document.getElementById ('main_progress') ) {
		var prog_hold = '<div id="main_progress_hold"  style="z-index:500000;bottom:2em;position:absolute;width:calc( 100% - 4em );left:2em;padding:0;background-color:#ededed;border-top:1px solid #ccc;">' +
		                '<div style="padding:1em;position:relative;border-bottom:1px solid #ccc;"><div id="main_progress_text" ></div></div>' +
		                '<div style="padding:1em;position:relative;"><progress id="main_progress" min="0" style="position: relative; bottom: 0; width:calc( 100% - 2em ); margin: 0 auto"></progress></div>' +
		                '</div>';
		document.body.insertAdjacentHTML ('beforeend', prog_hold); // appendChild(document.createTextNode(prog_hold));
		console.log ('wazaaa ');
	}
	document.getElementById ('main_progress').value = value;
	document.getElementById ('main_progress').max   = max;
	if ( text ) {
		document.getElementById ('main_progress_text').innerHTML = text;
	}
	//
	if ( value == max ) {
		$ ('main_progress_hold').show ('bounce');
	}

}

var require_boot = ['localforage', 'jscookie', 'lazyload', 'socketio'    /*, 'require-css.min.js'*/];

require (require_boot, function (lf, co, domReady, ll, r_socket, insq  /*req_css*/) {
	window.localforage = lf
	window.Cookies     = co

	dyn_require ();
});

