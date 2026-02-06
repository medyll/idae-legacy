if(typeof bag !== 'undefined') bag.clear();
if(typeof localforage !== 'undefined') localforage.clear();
localStorage.clear();
var HTTPCSS = "css/";

var require_trame = {
	require_boot       : ['javascript/vendor/localforage.js', 'javascript/vendor/basket.js'],
	require_polyfill   : [
		'javascript/vendor/polyfill/rsvp.js',
		'javascript/vendor/polyfill/EventSource.js',
		'javascript/vendor/moment.js',
		'javascript/vendor/polyfill/json2.js'],
	require_hell       : [
		'javascript/vendor/prototype/prototype-1.7.3.js',
		'javascript/vendor/scriptaculous/scriptaculous.js',
		'javascript/vendor/scriptaculous/effects.js',
		'javascript/vendor/scriptaculous/dragdrop.js',
	],
	require_insertionQ : ['javascript/app/app.js',/*'javascript/app/app_mutateobserve.js',*/'javascript/vendor/insertionQ.js', 'javascript/app/app_insertionQ.js'],
	require_to_log     : [
		'javascript/engine/module.js',
		'javascript/engine/methods.js',
		'javascript/app/app_php.js',
		'javascript/app/app_functions.js',
		'javascript/app/app_bootstrap_init.js',
		'javascript/engine/initApp.js'
	],
	require_scripts    : [  // vendor
		'javascript/vendor/chartist.js',
		'javascript/vendor/chart.bundle.min.js',
		'javascript/vendor/draggabilly.pkgd.min.js',
		'javascript/vendor/lory.js',
		'javascript/vendor/rio-compiler.js',
		'javascript/vendor/detect-element-resize.js',
		'javascript/vendor/pikaday.js',
		'javascript/vendor/jsoncookie.js',
		'javascript/vendor/sizzle.js',
		'javascript/vendor/tinymce/tinymce.min.js',
		'javascript/vendor/swiper.min.js'],
	require_boostrap   : ['javascript/app/app_bootstrap.js'],
	require_app        : [
		'javascript/app/app_cache.js',
		'javascript/app/app_socket.js',
		'javascript/app/app_live_data.js',
		'javascript/app/app_datatable.js',
		'javascript/app/app_keepon.js',
		'javascript/app/app_chat.js',
		'javascript/app/app_planning.js',
		'javascript/app/app_conge.js',
		'javascript/app/app_window.js',
		'javascript/app/app_calendrier.js',
		'javascript/app/app_contextual.js',
		'javascript/app/app_menu.js',
		'javascript/app/app_quickfind.js',
		'javascript/app/app_tree.js',
		'javascript/app/app_init_template.js',
		'javascript/engine/engine.js?v=debug1',
		'javascript/engine/afterAjaxCall.js',
		'javascript/engine/contextuel.js',
		'javascript/engine/initApp.js'],
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
var require_sheet = [
	HTTPCSS + 'fontawesome/css/font-awesome.css',
	HTTPCSS + 'chartist/chartist.css',
	HTTPCSS + 'calendrier/calendrier.css',
	HTTPCSS + 'menu/menuH.css',
	HTTPCSS + 'niceForm/niceForm.css',
	HTTPCSS + 'onglet/onglet.css',
	HTTPCSS + 'pickerHolder/pickerHolder.css',
	HTTPCSS + 'sortable/sortable.css',
	HTTPCSS + 'validation/validation.css',
	HTTPCSS + 'planning/planning.css',
	HTTPCSS + 'cropper/cropper.css',
	HTTPCSS + 'tinyeditor/style.css',
	HTTPCSS + 'officeui/fabric.css',
	HTTPCSS + 'officeui/fabric.components.css',
	HTTPCSS + 'vendor/animate/animate-min.css'
];
// BANG
// Force cache refresh
var cache_buster = '?v=' + new Date().getTime();
for (var key in require_trame) {
    if (require_trame.hasOwnProperty(key)) {
        for(var i=0; i<require_trame[key].length; i++) {
        	require_trame[key][i] += cache_buster;
        }
    }
}
for(var i=0; i<require_sheet.length; i++) {
    require_sheet[i] += cache_buster;
}

var app_dom_loaded = false;
var require_size   = 0;// .push(data);
var require_queue = [];// .push(data);
var ct_l = 0;

for (var prop in require_trame) {
	if ( require_trame.hasOwnProperty (prop) ) {
		require_size += require_trame[prop].length
		require_queue.push (prop);
	}
}

var require_progress_size  = require_size;
var require_progress_count = 0;

// BAG
var is_onLoad = function (node) {
	var moduleName = node.key;
	//  console.log (moduleName)
	require_progress (++require_progress_count, require_progress_size, moduleName + ' chargé');
}
var bag       = new window.Bag ({ onLoad : is_onLoad });

var dyn_require = function () {
	ct_l++;
	if ( ct_l > 500 ) return;
	if ( !require_queue[0] ) {

		QsLoad ();
		initApp ();
		schemeLoad ().then (function () {
			idae_log ();
			console.log ('log ok');
			if ( localStorage.getItem ('wallpaper') ) {
				setTimeout (function () {
					$ ('body').setStyle ({ backgroundImage : localStorage.getItem ('wallpaper') });
				}, 0);
			}
		})
		return;
	}

	var require_elem = require_queue[0];
	require_queue.shift ();

	bag.require (require_trame[require_elem]).then (
		function () {
			if ( require_elem == 'require_to_log' ) {

			}
			dyn_require ();
		}
	);
}

function require_progress(value, max, text) {
	if ( !document.getElementById ('main_progress') ) {
		var prog_hold = '<div id="main_progress_hold"  style="z-index:500000;bottom:2em;position:absolute;width:calc( 100% - 4em );left:2em;padding:0;background-color:#ededed;border-top:1px solid #ccc;">' +
		                '<div style="padding:1em;position:relative;border-bottom:1px solid #ccc;"><div id="main_progress_text" ></div></div>' +
		                '<div style="padding:1em;position:relative;"><progress id="main_progress" min="0" style="position: relative; bottom: 0; width:calc( 100% - 2em ); margin: 0 auto"></progress></div>' +
		                '</div>';
		document.body.insertAdjacentHTML ('beforeend', prog_hold);
	}
	document.getElementById ('main_progress').value = value;
	document.getElementById ('main_progress').max   = max;
	if ( text ) {
		document.getElementById ('main_progress_text').innerHTML = text;
	}
	//
	if ( value == max ) {
		$ ('main_progress_hold').fade ('bounce');
	}
}

var require_boot = ['javascript/vendor/js.cookie.js' + cache_buster, 'javascript/vendor/socket.io.js' + cache_buster];
bag.require (require_sheet).then (function () {
	bag.require (require_boot).then (
		function () {
			dyn_require ();
		}
	);
})

