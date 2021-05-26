window.document.observe ('content:loaded', function () {
	// time_registerMdl = setTimeout(function(){registerMdl()}.bind(this),500)
});
window.document.observe ('dom:close', function () {
	clearTimeout (time_registerMdl)
	// time_registerMdl = setTimeout(function(){registerMdl()}.bind(this),500)
});

registerMdl = function () {
	return '';
	var pair     = {};
	a            = $$ ('[mdl]').collect (function (node, index) {
		mdl                      = $ (node).readAttribute ('mdl');
		value                    = $ (node).readAttribute ('value');
		vars                     = $ (node).readAttribute ('vars');
		pair['mdl[' + mdl + ']'] = value;
	});
	vars         = Object.toQueryString (pair) + '&PHPSESSID=&idagent=' + $ ('SESSID').value;
	clearTimeout (timerCollect);
	timerCollect = setTimeout (function () {
		new Ajax.Request (
			'mdlRegister.php', {
				onComplete     : function (transport) {
					transport.responseText.evalScripts ()
				},
				method         : 'post', evalScripts : true,
				sanitize       : false,
				requestHeaders : ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8'],
				postBody       : vars
			});
	}.bind (this), 250)
}

openDoc          = function (uid, collection, base) {
	if ( !$ ('down_doc') ) down_doc = $ ('down_doc') || new Element ('iframe', { id : 'down_doc' })
	{document.body.appendChild (down_doc)}
	$ (down_doc).src = 'popDocument.php?uid=' + uid + '&collection=' + collection + '&base=' + base
}
var timerCollect = new Array ();
var time_registerMdl;



mce_area = function (var_string) {

	tinyMCE.suffix = '.min';
	tinyMCE.baseURL =  HTTPJAVASCRIPT+"vendor/tinymce/";// trailing slash important

	tinyMCE.init ({
		selector           : var_string,
		theme              : "modern",
		menubar            : false,
		toolbar_items_size : 'small',
		add_unload_trigger : false,
		toolbar            : "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | link unlink image",
		statusbar          : false
	});
	// tinyMCE.baseURL = "URL_TO/tinymce/jscripts/tiny_mce/";
}

var timermonitor;
//

changeCnameTrick = function () {
	return 'http://<?=DOCUMENTDOMAIN?>/';
	return '';
}
gereDate         = function (debut, trim) {
	if ( debut.length < 10 ) return debut
	day       = debut.substring (0, 2);
	month     = debut.substring (3, 5);
	year      = debut.substring (6, 10);
	var D_Deb = new Date ();
	var D_Fin = new Date ();

	D_Deb.setYear (year);
	D_Deb.setMonth (eval (month));
	D_Deb.setDate (day);

	D_Fin.setYear (year);
	D_Fin.setMonth (month);
	D_Fin.setDate (eval (day) + eval (trim));

	damonth = D_Fin.getMonth ();
	if ( damonth < 10 ) damonth = '0' + damonth;

	daday = D_Fin.getDate ();
	if ( daday < 10 ) daday = '0' + daday;

	return daday + '/' + damonth + '/' + D_Fin.getFullYear ();
}
var chkIdle;
var chkIdleMove;
var chkIdleMoveOut;
var activeIdle
chekIdle         = function () {
	window.clearTimeout (chkIdle);
	window.clearTimeout (chkIdleMove);
	window.clearTimeout (chkIdleMoveOut);
	if ( chkIdle ) {
		window.clearTimeout (chkIdle);
	}
	chkIdle = window.setTimeout (function () {
		isIdle ();
	}.bind (this), 3500);//3600000
	// chkIdleMove = window.setTimeout(function(){isIdleMove();}.bind(this),1000);//3600000
	// chkIdleMoveOut = window.setTimeout(function(){isIdleMoveOut();}.bind(this),60000);//3600000
}
// $('body').observe('mousemove',chekIdle)
function isIdle() {
	document.location.href = 'login.php'
}
// document.on('focus',function(event){isIdleMove();})
// document.on('blur',function(event){isIdleMoveOut();})

function isIdleMove() {
	if ( activeIdle != true ) {
		new Ajax.Request (
			'mdl/liveidle/actions.php', {
				onComplete     : function (transport) {
					transport.responseText.evalScripts ()
				},
				method         : 'post', evalScripts : true,
				sanitize       : false,
				requestHeaders : ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8'],
				postBody       : 'F_action=removeIdle'
			});

		activeIdle = true
	}
}

function isIdleMoveOut() {
	new Ajax.Request (
		'mdl/liveidle/actions.php', {
			onComplete     : function (transport) {
				transport.responseText.evalScripts ()
			},
			method         : 'post', evalScripts : true,
			sanitize       : false,
			requestHeaders : ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8'],
			postBody       : 'F_action=setIdle'
		});
	activeIdle = false;
}

function popopen(page, w, h, name, scrollbar) {
	handle    = name || 'nom_popup'
	scrollbar = scrollbar || 'no'
	window.open (page, handle, 'resizable=no, location=no, directories=no, status=no,menubar=no, scrollbars=' + scrollbar + ', width=' + w + ', height=' + h);
}

function chkDispZone(node) {

	var viewport        = document.viewport.getDimensions (),
	    containerWidth  = node.getWidth (),
	    containerHeight = node.getHeight ();

	var positionX = parseInt ($ (node).style.left),
	    positionY = $ (node).offsetTop;

	if ( eval (positionX) > viewport.width ) {
		node.setStyle ({
			left : (viewport.width - containerWidth) + 'px'
		});
	}
	if ( eval (positionX + containerWidth) < 0 ) {
		node.setStyle ({
			left : '0px'
		});
	}
	if ( eval (positionY + containerHeight) > viewport.height ) {
		node.setStyle ({
			top : (viewport.height) + 'px'
		});
	}
	if ( eval (positionY) < 0 ) {
		node.setStyle ({
			top : '0px'
		});
	}
	if ( eval (containerHeight) > viewport.height ) {
		node.setStyle ({
			height : (viewport.height) + 'px',
			top    : '0px'
		});
	}
}

function clean_string(str, separ) {
	var sep = separ || '-'
	// convert spaces to '-'
	str = str.replace (/ /g, sep);
	// Make lowercase
	str = str.toLowerCase ();
	// Remove characters that are not alphanumeric or a '-'
	str = str.replace (/[^a-z0-9-]/g, "");
	// Combine multiple dashes (i.e., '---') into one dash '-'.
	str = str.replace (/[-]+/g, sep);
	return str;
}

var time_set_ayto_next;
function save_setting_autoNext(node, key) {
	clearTimeout (time_set_ayto_next);
	time_set_ayto_next = setTimeout (function () {
		var dsp = node.next ().getStyle ('display')
		ajaxValidation ('set_settings', 'mdl/app/', 'key=' + key + '&value=' + dsp);
	}.bind (this), 500)
}

function save_settings(key, value) {
	ajaxValidation ('set_settings', 'mdl/app/', 'key=' + key + '&value=' + value);
	clearTimeout (time_set_ayto_next);
	time_set_ayto_next = setTimeout (function () {
		//  ajaxValidation('set_settings', 'mdl/app/', 'key=' + key + '&value=' + value);
	}.bind (this), 500)
}
function del_settings(key, value) {
	setTimeout (function () {
		ajaxValidation ('deleteTile', 'mdl/app/app_gui', 'table=' + key + '&table_value=' + value);
	}.bind (this), 500)
}

function edit_in_place(node) {

	if ($(node).select('.edit_node').size() != 0) {
		return false;
	}

	real_node = $(node).select('[data-field_name]').first();

	if (!$('edit_node')) {
		var edit_node = new Element('div', {
			id: 'edit_node',
			className: ' ededed  edit_node'
		});
	}else{
		var edit_node =$('edit_node');
	}

	$(real_node).update(edit_node);
	var vars = $(real_node).up().readAttribute('vars') || '';
	vars += '&field_name=' + $(real_node).readAttribute('data-field_name') || '';
	vars += '&field_name_raw=' + $(real_node).readAttribute('data-field_name_raw') || '';

	$(real_node).update(edit_node);
	//
	$(edit_node).loadModule('app/app_field_update', vars);

}