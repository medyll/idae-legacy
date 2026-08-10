/**
 * Created by lebru_000 on 28/02/15.
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Global functions (no Class.create here), so no
 * IIFE wrapper is needed — just file-local `ac_*` helpers replacing the
 * shimmed `$`/`$$`/`$H`/Element.* calls. Behaviour unchanged, including
 * one pre-existing bug carried forward as-is (see chat_user_remove).
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as the other migrated files *
 * (see BE_PLAN.md phase 5).                                              *
 * -------------------------------------------------------------------- */

function ac_el(ref) {
	return typeof ref === 'string' ? document.getElementById(ref) : ref;
}

function ac_qsa(selector, root) {
	return Array.prototype.slice.call((root || document).querySelectorAll(selector));
}

/** Prototype's Element#up(selector): starts at the parent, never at self. */
function ac_up(node, selector) {
	if (!node || !node.parentElement) return null;
	return node.parentElement.closest(selector);
}

/** Event delegation, Prototype's Element#on(event, selector, handler). */
function ac_delegate(root, eventName, selector, handler) {
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
function ac_toQueryString(obj) {
	return Object.keys(obj || {}).map(function (key) {
		var value = obj[key];
		return encodeURIComponent(key) + '=' + encodeURIComponent(value == null ? '' : value);
	}).join('&');
}

function ac_show(node) {
	if (node) node.style.display = '';
	return node;
}

function ac_hide(node) {
	if (node) node.style.display = 'none';
	return node;
}

function ac_remove(node) {
	if (node && node.parentNode) node.parentNode.removeChild(node);
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

appchat_init = function () {
    // appchat_agent_state_retrieve();
    if (appchat_get_key('appchat_show_panel') == true) {
        ac_show(ac_el('app_chat_panel')); // app_chat_button
        ac_qsa('.app_chat_button').forEach(function (n) { n.classList.add('active'); });
    } else {
        ac_hide(ac_el('app_chat_panel'));
    }
    if (appchat_get_key('appchat_connected')) {
        appchat_connect_agent();
    }
    appchat_agent_state_retrieve();
}

appchat_get_key = function (key) {
    value = localStorage.getItem('appchat_' + key);

    return value && JSON.parse(value);
}
appchat_store_key = function (key, value) {
    localStorage.setItem('appchat_' + key, JSON.stringify(value));
}
appchat_panel_toggle = function () {
    var button = ac_qsa('.app_chat_button')[0];
    if (button && button.classList.contains('active')) {
        ac_hide(ac_el('app_chat_panel'));
        ac_qsa('.app_chat_button').forEach(function (n) { n.classList.remove('active'); });
        appchat_store_key('appchat_show_panel', false);
    } else {
        ac_show(ac_el('app_chat_panel'));
        ac_qsa('.app_chat_button').forEach(function (n) { n.classList.add('active'); });
        appchat_store_key('appchat_show_panel', true);
    }

}

appchat_agent_state_retrieve = function () {
    if (appchat_get_key('appchat_connected') == true) {
        ac_qsa('.appchat_connected').forEach(function (n) { n.classList.add('active'); });
        ac_qsa('.appchat_disconnected').forEach(function (n) { n.classList.remove('active'); });
    } else {
        ac_qsa('.appchat_connected').forEach(function (n) { n.classList.remove('active'); });
        ac_qsa('.appchat_disconnected').forEach(function (n) { n.classList.add('active'); });
    }
}
appchat_disconnect_agent = function () {
    // agent présent pour affichage bouton sur site
    socket_app_chat.emit('agent_disconnected', {APPID: localStorage.PHPSESSID});
    // statut bouton
    ac_qsa('.appchat_connected').forEach(function (n) { n.classList.remove('active'); });
    ac_qsa('.appchat_disconnected').forEach(function (n) { n.classList.add('active'); });
    // appchat_store_key
    appchat_store_key('appchat_connected', false);
}
appchat_connect_agent = function () {
    // console.log('appchat_connect_agent ', localStorage.APPID);
    // agent présent pour affichage bouton sur site
    socket_app_chat.emit('agent_connected', {APPID: localStorage.APPID});
    // demande des contacts
    socket_app_chat.emit('contact_list_ask', {APPID: localStorage.APPID});
    // statut bouton
    ac_qsa('.appchat_connected').forEach(function (n) { n.classList.add('active'); });
    ac_qsa('.appchat_disconnected').forEach(function (n) { n.classList.remove('active'); });
    // appchat_store_key
    appchat_store_key('appchat_connected', true);
}
//
appchat_msgzone_update = function (APPID, value) {
    appchat_store_key('appchat_last_talk.' + APPID, value);
    ac_qsa('[data-appid=' + APPID + '] .appchat_msgzone').forEach(function (n) {
        n.insertAdjacentHTML('beforeend', '<div class="flex_h borderr"><div class="padding"><i class=\'fa fa-comment-o fa-2x textgris\'></i></div><div  class="flex_1 padding">' + value + '</div></div>');
    });
}
appchat_msgzone_self_update = function (APPID, value) {
    appchat_store_key('appchat_last_self_talk.' + APPID, value);
    ac_qsa('[data-appid=' + APPID + '] .appchat_msgzone').forEach(function (n) {
        n.insertAdjacentHTML('beforeend', '<div class="flex_h borderl"><div  class="flex_1 padding">' + value + '</div><div class="padding"><i class=\'fa fa-comment-o fa-2x\'></i></div></div>');
    });
}
//
appchat_talk_agent = function (form) {
    // demande des contacts
    var formEl = ac_el(form);
    socket_app_chat.emit('contact_ask', {idagent: localStorage.IDAGENT, APPID: localStorage.APPID, ROOMREQUESTED: formEl.ROOMREQUESTED.value, MSGTXT: formEl.MSGTXT.value});
}
appchat_ask_contact_agent = function (data) {
    // demande des contacts
    socket_app_chat.emit('contact_ask', {idagent: localStorage.IDAGENT, APPID: localStorage.APPID, ROOMREQUESTED: data.ROOMREQUESTED, MSGTXT: data.MSGTXT});
}
appchat_stop_contact_agent = function (data) {
    console.log('quit room', data)
    // demande des contacts

    socket_app_chat.emit('contact_stop', {idagent: data.idagent, APPID: data.APPID, ROOMREQUESTED: data.ROOMREQUESTED});
}



/**
 *
 * socket_app_chat
 */
var socket_app_chat = io(document.location.protocol+'//' + document.domain + ':' + port + '/app_chat');

socket_app_chat.on('connect', function () {
    if (!localStorage.PHPSESSID || !localStorage.IDAGENT) return;
    console.log('connected, emit agent_register');
    socket_app_chat.emit('agent_register', {APPID: localStorage.APPID, PHPSESSID: localStorage.PHPSESSID, idagent: localStorage.IDAGENT});
    //
});
socket_app_chat.on('disconnect', function (data) {

});
socket_app_chat.on('reconnecting', function (retry_count) {

});
//
socket_app_chat.on('reconnect', function (data) {

});
//
socket_app_chat.on('agent_connected', function (data) {
    //  console.log('un agent est connected ', data);
    appchat_user_add(data);
}); //
socket_app_chat.on('agent_disconnected', function (data) {
    //   console.log('disconnected', data);
    chat_user_remove(data);
}); //
socket_app_chat.on('agent_heartbeat', function (data) {

});
socket_app_chat.on('contact_stopped', function (data) {
   // console.log('contact_stopped', data);
    var appid = data.ROOMREQUESTED;
    var appid_2 = data.APPID;
    // app_chat_ask_contact
    ac_qsa('[data-appid='+appid+'] .app_chat_ask_contact').forEach(function (n) { n.toggleContent(); });
    ac_qsa('[data-appid='+appid_2+'] .app_chat_ask_contact').forEach(function (n) { n.toggleContent(); });
})
socket_app_chat.on('contact_asked', function (data) {
    //  console.log('contact_asked', data);
    var appid = data.ROOMREQUESTER;
    var msg = '<div class="padding">' + data.MSGTXT + '</div>';
    //
    // if (!$(appid)) return;
    appchat_msgzone_self_update(appid, data.MSGTXT);
    //
    //
    if (ac_qsa('[data-appid=' + appid + '][data-appchatbox]').length !== 0) return;

    ac_qsa('[data-appid=' + appid + '] .app_chat_accept').forEach(function (n) { n.toggleContent(); });
    ac_qsa('[data-appid=' + appid + '] .glue_requested').forEach(function (n) { ac_show(n); });
    ac_qsa('[data-appid=' + appid + '] .app_chat_ask_contact_ok').forEach(function (n) { n.toggleContent(); });
    var options = {};
    // options.className = 'myddeNotifierBottom';
    options.sticky = true;
    options.mdl = 'app/app_chat/app_chat_item_button';
    options.id = appid;
    options.vars = ac_toQueryString(data);
    //
    var a = new myddeNotifier(options);
    a.growl(msg, options);

});
socket_app_chat.on('contact_accepted', function (data) {
    //
    vars = ac_toQueryString({APPID: data.APPID, idagent: data.IDAGENT});
    // fenetre talk
    act_chrome_gui('app/app_chat/app_chat_talk', vars, {onclose: function () {
        appchat_stop_contact_agent(data)
    }});
    // controle boutons
    ac_qsa('[data-appid=' + data.APPID + '] .app_chat_accept').forEach(function (n) { ac_hide(n); });
    ac_qsa('[data-appid=' + data.APPID + '] .app_chat_ask_contact_ok').forEach(function (n) { n.toggleContent(); });
});


ac_delegate(document.body, 'click', '.app_chat_button', function () {
    appchat_panel_toggle();
});
ac_delegate(document.body, 'click', '.appchat_connected', function () {
    appchat_connect_agent();
});
ac_delegate(document.body, 'click', '.appchat_disconnected', function () {
    appchat_disconnect_agent();
});
ac_delegate(document.body, 'click', '.app_chat_ask_contact', function (event, elem) {
    console.log(elem); // app_chat_ask_contact()d
    // quelle room ?
    var upNode = ac_up(elem, '[data-appid]');
    if (upNode) {
        roomtogo = upNode.getAttribute('data-appid');
    } else {
        return;
    }
    elem.nextElementSibling.toggleContent();
    appchat_ask_contact_agent({ROOMREQUESTED: roomtogo, APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});
    // socket_app_chat.emit('contact_accept', {ROOMREQUESTED:roomtogo,APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});

});
ac_delegate(document.body, 'click', '.app_chat_accept', function (event, elem) {
    // quelle room ?
    var upAppId = ac_up(elem, '[data-appid]');
    if (upAppId) {
        roomtogo = upAppId.getAttribute('data-appid');
    } else {
        return;
    }
    // quelle agent ?
    var upIdAgent = ac_up(elem, '[data-idagent]');
    if (upIdAgent) {
        idagent_togo = upIdAgent.getAttribute('data-idagent');
    } else {
        return;
    }
    // open chrome_gui
    vars = {idagent: idagent_togo, ROOMREQUESTED: roomtogo, ROOMREQUESTER: localStorage.APPID, APPID: roomtogo} ;
    var vars_2 = {idagent: idagent_togo, ROOMREQUESTED: roomtogo, ROOMREQUESTER: roomtogo, APPID: roomtogo} ;
    act_chrome_gui("app/app_chat/app_chat_talk", ac_toQueryString(vars), {onclose: function () {
        appchat_stop_contact_agent(vars_2)
    }});
    // act_chrome_gui('app/app_chat/app_chat_talk','ROOMREQUESTED=ojg55u7a7mlg5026js62fmujq2&ROOMREQUESTER=fi253ehuudmamkj6715j1ohlu2&idagent=13&module=app%2Fapp_chat%2Fapp_chat_item_button&mdl=app%2Fapp_chat%2Fapp_chat_item_button&PHPSESSID=ojg55u7a7mlg5026js62fmujq2&APPID=fi253ehuudmamkj6715j1ohlu2',{onclose:function(){ }})
    //
    ac_qsa('[data-appid=' + roomtogo + '] .app_chat_accept').forEach(function (n) { ac_hide(n); });
    socket_app_chat.emit('contact_accept', {ROOMREQUESTED: roomtogo, APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});

});


// chat

var appchat_tracker_timer = [];

appchat_user_add = function (vars) {
    var url_vars = ac_toQueryString(vars);
    var id = vars.APPID;
    var idagent = vars.idagent || '';

    if (!ac_el('socket_appchat_log')) return;
    if (appchat_tracker_timer[id]) clearTimeout(appchat_tracker_timer[id]);
    var existing = ac_el(id);
    if (existing) {
        existing.classList.remove('ededed');
        existing.classList.add('blanc');
        return;
    }

    var lnk = '<div id="' + id + '" act_defer data-appid="' + id + '" data-idagent="' + idagent + '"  mdl="app/app_chat/app_chat_item" vars="' + url_vars + '">' + id + '</div>';
    ac_el('socket_appchat_log').insertAdjacentHTML('beforeend', lnk);
}
/*
 chat_user_update = function (vars) {
 var APPID = vars.APPID;
 if (!$('socket_keep_on_log')) return;
 if (!$(APPID)) return;
 if (vars.GLUEREQUESTED) {
 // $(APPID).select('.glue_requested').invoke('show');
 }
 ;
 if (vars.CONTACTREQUESTED) {
 $(APPID).select('.glue_requested').invoke('show');
 } else {
 //  $(APPID).select('.glue_reserved').invoke('unToggleContent');
 }
 ;
 //
 if (vars.RESERVEDID) {
 $(APPID).select('.glue_busy').invoke('show');
 }
 ;
 if (chat_tracker_timer[APPID]) clearTimeout(chat_tracker_timer[APPID]);
 }*/
chat_user_remove = function (vars) {
    var id = vars.id;
    if (!ac_el('socket_appchat_log')) return;
    // Pre-existing bug, carried forward unchanged: `chat_tracker_timer` (no
    // `appchat_` prefix) is never declared anywhere in this file — only
    // `appchat_tracker_timer` is. This throws a ReferenceError whenever this
    // line is reached, same as it always has under the shim.
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    var node = ac_el(id);
    if (node) {
        node.classList.remove('blanc');
        node.classList.add('ededed');
        ac_remove(node);
    }
}
