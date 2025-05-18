/**
 * Created by lebru_000 on 28/02/15.
 */

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
        $('app_chat_panel').show(); // app_chat_button
        $$('.app_chat_button').invoke('addClassName', 'active');
    } else {
        $('app_chat_panel').hide();
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
    if ($$('.app_chat_button').first().hasClassName('active')) {
        $('app_chat_panel').hide();
        $$('.app_chat_button').invoke('removeClassName', 'active');
        appchat_store_key('appchat_show_panel', false);
    } else {
        $('app_chat_panel').show();
        $$('.app_chat_button').invoke('addClassName', 'active');
        appchat_store_key('appchat_show_panel', true);
    }

}

appchat_agent_state_retrieve = function () {
    if (appchat_get_key('appchat_connected') == true) {
        $$('.appchat_connected').invoke('addClassName', 'active');
        $$('.appchat_disconnected').invoke('removeClassName', 'active');
    } else {
        $$('.appchat_connected').invoke('removeClassName', 'active');
        $$('.appchat_disconnected').invoke('addClassName', 'active');
    }
}
appchat_disconnect_agent = function () {
    // agent présent pour affichage bouton sur site
    socket_app_chat.emit('agent_disconnected', {APPID: localStorage.PHPSESSID});
    // statut bouton
    $$('.appchat_connected').invoke('removeClassName', 'active');
    $$('.appchat_disconnected').invoke('addClassName', 'active');
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
    $$('.appchat_connected').invoke('addClassName', 'active');
    $$('.appchat_disconnected').invoke('removeClassName', 'active');
    // appchat_store_key
    appchat_store_key('appchat_connected', true);
}
//
appchat_msgzone_update = function (APPID, value) {
    appchat_store_key('appchat_last_talk.' + APPID, value);
    $$('[data-appid=' + APPID + '] .appchat_msgzone').invoke('insert', '<div class="flex_h borderr"><div class="padding"><i class=\'fa fa-comment-o fa-2x textgris\'></i></div><div  class="flex_1 padding">' + value + '</div></div>');
}
appchat_msgzone_self_update = function (APPID, value) {
    appchat_store_key('appchat_last_self_talk.' + APPID, value);
    $$('[data-appid=' + APPID + '] .appchat_msgzone').invoke('insert', '<div class="flex_h borderl"><div  class="flex_1 padding">' + value + '</div><div class="padding"><i class=\'fa fa-comment-o fa-2x\'></i></div></div>');
}
//
appchat_talk_agent = function (form) {
    // demande des contacts
    socket_app_chat.emit('contact_ask', {idagent: localStorage.IDAGENT, APPID: localStorage.APPID, ROOMREQUESTED: $(form).ROOMREQUESTED.value, MSGTXT: $(form).MSGTXT.value});
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
    $$('[data-appid='+appid+'] .app_chat_ask_contact').invoke('toggleContent')
    $$('[data-appid='+appid_2+'] .app_chat_ask_contact').invoke('toggleContent')
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
    if ($$('[data-appid=' + appid + '][data-appchatbox]').size() != 0) return;

    $$('[data-appid=' + appid + '] .app_chat_accept').invoke('toggleContent');
    $$('[data-appid=' + appid + '] .glue_requested').invoke('show');
    $$('[data-appid=' + appid + '] .app_chat_ask_contact_ok').invoke('toggleContent');
    var options = {};
    // options.className = 'myddeNotifierBottom';
    options.sticky = true;
    options.mdl = 'app/app_chat/app_chat_item_button';
    options.id = appid;
    options.vars = $H(data).toQueryString();
    //
    var a = new myddeNotifier(options);
    a.growl(msg, options);

});
socket_app_chat.on('contact_accepted', function (data) {
    //
    vars = $H({APPID: data.APPID, idagent: data.IDAGENT}).toQueryString();
    // fenetre talk
    act_chrome_gui('app/app_chat/app_chat_talk', vars, {onclose: function () {
        appchat_stop_contact_agent(data)
    }});
    // controle boutons
    $$('[data-appid=' + data.APPID + '] .app_chat_accept').invoke('hide');
    $$('[data-appid=' + data.APPID + '] .app_chat_ask_contact_ok').invoke('toggleContent');
});


$('body').on('click', '.app_chat_button', function () {
    appchat_panel_toggle();
});
$('body').on('click', '.appchat_connected', function () {
    appchat_connect_agent();
});
$('body').on('click', '.appchat_disconnected', function () {
    appchat_disconnect_agent();
});
$('body').on('click', '.app_chat_ask_contact', function (event, elem) {
    console.log(elem); // app_chat_ask_contact()d
    // quelle room ?
    if ($(elem).up('[data-appid]')) {
        roomtogo = $(elem).up('[data-appid]').readAttribute('data-appid');
    } else {
        return;
    }
    $(elem).next().toggleContent();
    appchat_ask_contact_agent({ROOMREQUESTED: roomtogo, APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});
    // socket_app_chat.emit('contact_accept', {ROOMREQUESTED:roomtogo,APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});

});
$('body').on('click', '.app_chat_accept', function (event, elem) {
    // quelle room ?
    if ($(elem).up('[data-appid]')) {
        roomtogo = $(elem).up('[data-appid]').readAttribute('data-appid');
    } else {
        return;
    }
    // quelle agent ?
    if ($(elem).up('[data-idagent]')) {
        idagent_togo = $(elem).up('[data-idagent]').readAttribute('data-idagent');
    } else {
        return;
    }
    // open chrome_gui
    vars = {idagent: idagent_togo, ROOMREQUESTED: roomtogo, ROOMREQUESTER: localStorage.APPID, APPID: roomtogo} ;
    var vars_2 = {idagent: idagent_togo, ROOMREQUESTED: roomtogo, ROOMREQUESTER: roomtogo, APPID: roomtogo} ;
    act_chrome_gui("app/app_chat/app_chat_talk",$H(vars).toQueryString() ,{onclose: function () {
        appchat_stop_contact_agent(vars_2)
    }});
    // act_chrome_gui('app/app_chat/app_chat_talk','ROOMREQUESTED=ojg55u7a7mlg5026js62fmujq2&ROOMREQUESTER=fi253ehuudmamkj6715j1ohlu2&idagent=13&module=app%2Fapp_chat%2Fapp_chat_item_button&mdl=app%2Fapp_chat%2Fapp_chat_item_button&PHPSESSID=ojg55u7a7mlg5026js62fmujq2&APPID=fi253ehuudmamkj6715j1ohlu2',{onclose:function(){ }})
    //
    $$('[data-appid=' + roomtogo + '] .app_chat_accept').invoke('hide');
    socket_app_chat.emit('contact_accept', {ROOMREQUESTED: roomtogo, APPID: localStorage.PHPSESSID, IDAGENT: localStorage.IDAGENT});

});


// chat

var appchat_tracker_timer = [];

appchat_user_add = function (vars) {
    var url_vars = $H(vars).toQueryString();
    var id = vars.APPID;
    var idagent = vars.idagent || '';

    if (!$('socket_appchat_log')) return;
    if (appchat_tracker_timer[id]) clearTimeout(appchat_tracker_timer[id]);
    if ($(id)) {
        $(id).removeClassName('ededed').addClassName('blanc')
        return;
    }

    var lnk = '<div id="' + id + '" act_defer data-appid="' + id + '" data-idagent="' + idagent + '"  mdl="app/app_chat/app_chat_item" vars="' + url_vars + '">' + id + '</div>';
    $('socket_appchat_log').insert(lnk);
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
    if (!$('socket_appchat_log')) return;
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    if ($(id)) {
        $(id).removeClassName('blanc').addClassName('ededed');
        $(id).remove();
    }
}
