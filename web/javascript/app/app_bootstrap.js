
switch (window.document.location.hostname) {
	case 'localhost':
		var HTTPJAVASCRIPT = window.document.location.href.replace (location.hash , "") + '/javascript/';
		var HTTPCSS        = window.document.location.href.replace (location.hash , "") + '/css/';
		break;
	case '192.168.11.44':
		var HTTPJAVASCRIPT = window.document.location.href.replace (location.hash , "") + '/javascript/';
		var HTTPCSS        = window.document.location.href.replace (location.hash , "") + '/css/';
		break;
	case '127.0.0.1':
		var HTTPJAVASCRIPT = window.document.location.href.replace (location.hash , "") + '/javascript/';
		var HTTPCSS        = window.document.location.href.replace (location.hash , "") + '/css/';
		break;

	default:
		var HTTPJAVASCRIPT = document.location.protocol + '//' + window.document.location.host + '/javascript/';
		var HTTPCSS        = document.location.protocol + '//' + window.document.location.host + '/css/';
		break;
}

/*var SHEETS = [
	HTTPCSS + 'chartist/chartist.css' ,
	HTTPCSS + 'calendrier/calendrier.css' ,
	HTTPCSS + 'menu/menuH.css' ,
	HTTPCSS + 'niceForm/niceForm.css' ,
	HTTPCSS + 'onglet/onglet.css' ,
	HTTPCSS + 'pickerHolder/pickerHolder.css' ,
	HTTPCSS + 'sortable/sortable.css' ,
	HTTPCSS + 'validation/validation.css' ,
	HTTPCSS + 'planning/planning.css' ,
	HTTPCSS + 'cropper/cropper.css' ,
	HTTPCSS + 'tinyeditor/style.css' ,
	HTTPCSS + 'officeui/fabric.css' ,
	HTTPCSS + 'officeui/fabric.components.css' ,
	HTTPCSS + 'vendor/animate/animate-min.css'
];

LazyLoad.css (SHEETS , function () {
	console.log ('LazyLoad sheet');
})*/

schemeLoad = function () {
	// console.log('go scheme_l
	// oad')
	var i_scheme_load = 0;

	if (!window.APP)window.APP = [];
	if (!window.APP.APPSCHEMES)window.APP.APPSCHEMES = [];
	if (!window.APP.APPFIELDS)window.APP.APPFIELDS = [];
	if (!window.APP.APPSCHEMES)window.APP.APPFIELDSBOOL = [];
	if (!window.APP.APPSCHEMES)window.APP.APPFIELDSBOOL = [];


	return new RSVP.Promise(function (resolve, reject) {
		app_init_template().then(function(edd){
			i_scheme_load++;
			if (i_scheme_load == 3) {
				console.log(' ok scheme')
				resolve('ok')
			}
		})





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
			//window.scheme_store.setItem('json_scheme_scheme', $H(window.APP.APPSCHEMES));
			i_scheme_load++;
			if (i_scheme_load == 3) {
				console.log(' ok scheme')
				resolve('ok')
			}
		});

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
			//  window.scheme_store.setItem('json_scheme_field', $H(window.APP.APPFIELDS));
			i_scheme_load++;
			if (i_scheme_load == 3) {
				console.log(' ok scheme')
				resolve('ok')
			}

		});
	})


}


/*
schemeLoad ().then (function () {

	QsLoad ();
	initApp ();
	idae_log ();
	console.log ('log ok');

	if ( localStorage.getItem ('wallpaper') ) {
		setTimeout (function () {
			$ ('body').setStyle ({ backgroundImage : localStorage.getItem ('wallpaper') });
		} , 0);
	}
})*/
