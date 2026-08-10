/**
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5).
 *
 * Removed, all confirmed zero-caller by grep across the repo (excluding
 * vendor/ and flotr/):
 *   - registerMdl        — body was unreachable anyway (`return ''` on its
 *                          first line), and nothing called it.
 *   - chekIdle / isIdle / isIdleMove / isIdleMoveOut — the whole idle
 *                          cluster. Its only entry point was
 *                          `$('body').observe('mousemove', chekIdle)`,
 *                          commented out, as were the document focus/blur
 *                          hooks. Dropping it removed all three
 *                          `Ajax.Request` call sites in this file.
 *   - gereDate           — date arithmetic helper, no callers.
 *   - edit_in_place      — no callers.
 *
 * Kept and migrated: openDoc, mce_area, changeCnameTrick, popopen,
 * chkDispZone, clean_string, save_setting_autoNext, save_settings,
 * del_settings — all with real callers (save_settings alone has 39).
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function af_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function af_setStyle(node, styles) {
	Object.keys(styles).forEach(function (key) {
		node.style[key] = styles[key];
	});
	return node;
}

function af_getStyle(node, prop) {
	var value = node.style[prop];
	if (!value || value === 'auto') {
		var css = window.getComputedStyle(node, null);
		value = css ? css[prop] : null;
	}
	return value === 'auto' ? null : value;
}

/**
 * Prototype's Element#getDimensions: clientWidth/clientHeight, forcing the
 * element visible first if it is display:none.
 */
function af_getDimensions(node) {
	if (window.getComputedStyle(node).display !== 'none') {
		return {width: node.clientWidth, height: node.clientHeight};
	}
	var style = node.style;
	var originalVisibility = style.visibility,
		originalPosition = style.position,
		originalDisplay = style.display;
	style.visibility = 'hidden';
	if (originalPosition !== 'fixed') style.position = 'absolute';
	style.display = 'block';
	var width = node.clientWidth, height = node.clientHeight;
	style.display = originalDisplay;
	style.position = originalPosition;
	style.visibility = originalVisibility;
	return {width: width, height: height};
}

/* -------------------------------------------------------------------- */

window.document.addEventListener ('content:loaded', function () {
	// time_registerMdl = setTimeout(function(){registerMdl()}.bind(this),500)
});
window.document.addEventListener ('dom:close', function () {
	clearTimeout (time_registerMdl)
	// time_registerMdl = setTimeout(function(){registerMdl()}.bind(this),500)
});

openDoc          = function (uid, collection, base) {
	if ( !af_el ('down_doc') ) {
		down_doc = document.createElement ('iframe');
		down_doc.id = 'down_doc';
	}
	// Not an `else`: the original had a bare block here, so the append runs
	// on every call, re-parenting the same iframe. Kept verbatim.
	{document.body.appendChild (down_doc)}
	down_doc.src = 'popDocument.php?uid=' + uid + '&collection=' + collection + '&base=' + base
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

/**
 * Broken, and left broken on purpose. This is a .js file served raw — no
 * PHP handler is configured for .js — so the `<?= … ?>` below is returned
 * as a literal string, not a rendered URL. Its four callers
 * (engine.js:251,327 and module.js:143,183) all sit inside the
 * `typeof socket == 'object'` Ajax fallbacks, which never execute because
 * app_socket.js always defines `socket` — which is why a return value this
 * broken has never surfaced. Fixing it would mean changing an unreachable,
 * untested path, and the real fix (getting a server-injected constant into
 * a static .js) is a separate decision.
 */
changeCnameTrick = function () {
	// Use server-side HTTPCUSTOMERSITE (includes port when served on non-standard ports)
	return '<?= rtrim(HTTPCUSTOMERSITE, '/') ?>/';
	return '';
}

function popopen(page, w, h, name, scrollbar) {
	handle    = name || 'nom_popup'
	scrollbar = scrollbar || 'no'
	window.open (page, handle, 'resizable=no, location=no, directories=no, status=no,menubar=no, scrollbars=' + scrollbar + ', width=' + w + ', height=' + h);
}

function chkDispZone(node) {

	var viewport        = {width: window.innerWidth, height: window.innerHeight},
	    containerWidth  = af_getDimensions (node).width,
	    containerHeight = af_getDimensions (node).height;

	var positionX = parseInt (node.style.left),
	    positionY = node.offsetTop;

	if ( eval (positionX) > viewport.width ) {
		af_setStyle (node, {
			left : (viewport.width - containerWidth) + 'px'
		});
	}
	if ( eval (positionX + containerWidth) < 0 ) {
		af_setStyle (node, {
			left : '0px'
		});
	}
	if ( eval (positionY + containerHeight) > viewport.height ) {
		af_setStyle (node, {
			top : (viewport.height) + 'px'
		});
	}
	if ( eval (positionY) < 0 ) {
		af_setStyle (node, {
			top : '0px'
		});
	}
	if ( eval (containerHeight) > viewport.height ) {
		af_setStyle (node, {
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
		var dsp = af_getStyle (node.nextElementSibling, 'display')
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
