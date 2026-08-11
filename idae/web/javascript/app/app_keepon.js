/**
 * Created by lebru_000 on 28/02/15.
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Global functions (no Class.create here), so no
 * IIFE wrapper is needed — just file-local `kp_*` helpers replacing the
 * shimmed `$`/`$$`/`$H`/Element.* calls. Behaviour unchanged, including a
 * pre-existing bug carried forward as-is (see the click delegates near the
 * bottom).
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function kp_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

/**
 * Tolerant querySelectorAll, matching what `$$` did through the shim
 * (`__idaeQSA`, shim-core.js). `[data-appid=<sid>]` is built from PHP
 * session ids, which routinely start with a digit — unquoted attribute
 * values are invalid CSS there and native querySelectorAll throws. Retry
 * once with the values quoted.
 */
function kp_qsa(selector, root) {
	root = root || document;
	var found;
	try {
		found = root.querySelectorAll(selector);
	} catch (e) {
		var quoted = String(selector).replace(
			/\[([a-zA-Z_][\w-]*)=([^'"\]\s][^\]\s]*)\]/g,
			'[$1="$2"]'
		);
		if (quoted === selector) throw e;
		found = root.querySelectorAll(quoted);
	}
	return Array.prototype.slice.call(found);
}

/** Prototype's Element#up(selector): starts at the parent, never at self. */
function kp_up(node, selector) {
	if (!node || !node.parentElement) return null;
	return selector ? node.parentElement.closest(selector) : node.parentElement;
}

/** Event delegation, Prototype's Element#on(event, selector, handler). */
function kp_delegate(root, eventName, selector, handler) {
	root.addEventListener(eventName, function (event) {
		var target = event.target;
		while (target && target !== root) {
			if (target.nodeType === 1 && target.matches(selector)) {
				return handler(event, target);
			}
			target = target.parentNode;
		}
	}, false);
}

/** Prototype's $H(obj).toQueryString() — plain object, no nesting here. */
function kp_toQueryString(obj) {
	return Object.keys(obj || {}).map(function (key) {
		var value = obj[key];
		return encodeURIComponent(key) + '=' + encodeURIComponent(value == null ? '' : value);
	}).join('&');
}

function kp_show(node) {
	if (node) node.style.display = '';
	return node;
}

function kp_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function kp_remove(node) {
	if (node && node.parentNode) node.parentNode.removeChild(node);
	return node;
}

/** Prototype's Element#update(content): strip <script> before innerHTML, eval the original text on a 10ms defer if any were present. */
function kp_update(node, content) {
	if (content === undefined) content = '';
	content = String(content);
	node.innerHTML = engine_stripScripts(content);
	if (/<script/i.test(content)) {
		setTimeout(function () { engine_evalScripts(content); }, 10);
	}
	return node;
}

/* -------------------------------------------------------------------- */

switch (document.domain) {
	case "idaertys-preprod.mydde.fr":
		var port = 3006;
		break;
	case "tactac_idae.preprod.mydde.fr":
		var port = 3006;
		break;
	case "appcrfr.idaertys-preprod.mydde.fr":
		var port = 3006;
		break;
	case "appmaw-idaertys-preprod.mydde.fr":
		var port = 3006;
		break;
	default:
		var port = 3005;
		break;

}

/**
 *
 * socket_keep_on
 */
var socket_keep_on = io(document.location.protocol+'//' + document.domain + ':' + port + '/keep_on');

socket_keep_on.on('mitete', function (vars) {
    var APPID = vars.APPID;
    if (!kp_el('socket_keep_on_log')) return;
    if (!kp_el(APPID)) return;
    kp_qsa('.glue_phone', kp_el(APPID)).forEach(function (n) { kp_show(n); });
    kp_qsa('.glue_phone', kp_el(APPID)).forEach(function (n) { kp_update(n, vars.MSG); });
});
socket_keep_on.on('connect', function (data) {
    if (!localStorage.PHPSESSID || !localStorage.IDAGENT) return;

});
socket_keep_on.on('disconnect', function (data) {
    socket_keep_on.emit('hide_glue');
    if (kp_el('socket_keep_on_status')) {
        kp_show(kp_el('socket_keep_on_status'));
    }
});
socket_keep_on.on('reconnecting', function (retry_count) {
    if (kp_el('socket_keep_on_status')) {
        kp_update(kp_el('socket_keep_on_status'), '<i class="fa fa-wifi textrouge"></i> - ' + retry_count);
    }
});
//
socket_keep_on.on('reconnect', function (data) {
    if (!localStorage.PHPSESSID || !localStorage.IDAGENT) return;
    if (kp_el('socket_keep_on_status')) {
        kp_hide(kp_el('socket_keep_on_status'));
    }
    socket_keep_on.emit('declare_glue', {APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});
});
//
socket_keep_on.on('client_connected', function (data) {
    chat_user_add(data);
    socket_keep_on.emit('show_glue', data);
}); //
socket_keep_on.on('client_disconnected', function (data) {
    chat_user_remove(data);
}); //
socket_keep_on.on('client_heartbeat', function (data) {
    // console.log(data);
    chat_user_add(data);
    chat_user_update(data);
});
socket_keep_on.on('request_contact', function (data) {
    console.log('request contact ', data);
    var a = new myddeNotifier();
    var appid = data.APPID;
    var msg = data.MSG;
    a.growl('request contact  ' + msg, {sticky: true});
    //
    if (!kp_el(appid)) return;
    kp_qsa('.glue_requested', kp_el(appid)).forEach(function (n) { kp_show(n); });
    kp_qsa('.glue_reserve', kp_el(appid)).forEach(function (n) { n.toggleContent(); });
});
socket_keep_on.on('request_contact_unsubscribe', function (data) {
    console.log('request request_contact_unsubscribe ', data);
    var appid = data.APPID;
    if (!kp_el(appid)) return;
    kp_qsa('.glue_requested', kp_el(appid)).forEach(function (n) { kp_hide(n); });
});
// glue_reserve
socket_keep_on.on('glue_reserve', function (data) {
    //
    var iuj = kp_qsa('[data-appid=' + data.RESERVEDID + ']')[0];
    // cache item aux autres
    if (data.APPID == localStorage.APPID) {
        console.log('glue_reserve bye WIN !!!');
        kp_qsa('.glue_reserved', iuj).forEach(function (n) { n.toggleContent(); });
    } else {
        console.log('glue_reserve sorry !!!');
        kp_qsa('.glue_reject', iuj).forEach(function (n) { n.toggleContent(); });
    }
    kp_qsa('.glue_busy', iuj).forEach(function (n) { kp_show(n); });
});
// message
socket_keep_on.on('message', function (data) {
    if (!kp_el('socket_keep_on_log')) return;
    // console.log(data)
});
socket_keep_on.on('request_glue', function (data) {
    console.log('request_glue ', data); // not used
});


// keepon_connect_agent/keepon_disconnect_agent are called below but never
// defined anywhere in the repo (confirmed by grep) — a pre-existing bug,
// not introduced by this migration. Clicking .keepon_connected/
// .keepon_disconnected (real markup, app_keepon_panel.php) throws
// "keepon_connect_agent is not defined" today exactly as it always has.
kp_delegate(document.body, 'click', '.keepon_connected', function () {
    keepon_connect_agent();
});
kp_delegate(document.body, 'click', '.keepon_disconnected', function () {
    keepon_disconnect_agent();
});
kp_delegate(document.body, 'click', '#socket_keep_on_log .glue_reserve', function (event, node) {
    if (!socket_keep_on) return;
    var iuj = kp_up(node, '[data-appid]');
    var APPID = iuj.getAttribute('data-appid');
    // lance demande reserve
    socket_keep_on.emit('glue_reserve', {APPID: localStorage.PHPSESSID, RESERVEDID: APPID, IDAGENT: localStorage.IDAGENT});
})

// chat

var chat_tracker_timer = [];

chat_user_add = function (vars) {
    var url_vars = kp_toQueryString(vars);
    var id = vars.id;
    if (!kp_el('socket_keep_on_log')) return;
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    var existing = kp_el(id);
    if (existing) {
        existing.classList.remove('ededed');
        existing.classList.add('blanc');
        return;
    }
    var lnk = '<div id="' + id + '" act_defer data-appid="' + id + '"   mdl="app/app_keepon/app_keepon_item" vars="' + url_vars + '">' + id + '</div>';
    kp_el('socket_keep_on_log').insertAdjacentHTML('beforeend', lnk);
}
chat_user_update = function (vars) {
    var APPID = vars.APPID;
    if (!kp_el('socket_keep_on_log')) return;
    if (!kp_el(APPID)) return;
    if (vars.GLUEREQUESTED) {
        // kp_qsa('.glue_requested', kp_el(APPID)).forEach(function (n) { kp_show(n); });
    }
    ;
    if (vars.CONTACTREQUESTED) {
        kp_qsa('.glue_requested', kp_el(APPID)).forEach(function (n) { kp_show(n); });
    } else {
        //  kp_qsa('.glue_reserved', kp_el(APPID)).forEach(function (n) { n.unToggleContent(); });
    }
    ;
    //
    if (vars.RESERVEDID) {
        kp_qsa('.glue_busy', kp_el(APPID)).forEach(function (n) { kp_show(n); });
    }
    ;
    if (chat_tracker_timer[APPID]) clearTimeout(chat_tracker_timer[APPID]);
}
chat_user_remove = function (vars) {
    var id = vars.id;
    if (!kp_el('socket_keep_on_log')) return;
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    var node = kp_el(id);
    if (node) {
        node.classList.remove('blanc');
        node.classList.add('ededed');
    }
    chat_tracker_timer[id] = setTimeout(function () {
        var late = kp_el(id);
        if (late) {
            kp_remove(late);
        }
    }, 5000);
}
