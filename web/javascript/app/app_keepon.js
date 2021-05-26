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

/**
 *
 * socket_keep_on
 */
var socket_keep_on = io(document.location.protocol+'//' + document.domain + ':' + port + '/keep_on');

socket_keep_on.on('mitete', function (vars) {
    var APPID = vars.APPID;
    if (!$('socket_keep_on_log')) return;
    if (!$(APPID)) return;
    $(APPID).select('.glue_phone').invoke('show');
    $(APPID).select('.glue_phone').invoke('update', vars.MSG);
});
socket_keep_on.on('connect', function (data) {
    if (!localStorage.PHPSESSID || !localStorage.IDAGENT) return;

});
socket_keep_on.on('disconnect', function (data) {
    socket_keep_on.emit('hide_glue');
    if ($('socket_keep_on_status')) {
        $('socket_keep_on_status').show();
    }
});
socket_keep_on.on('reconnecting', function (retry_count) {
    if ($('socket_keep_on_status')) {
        $('socket_keep_on_status').update('<i class="fa fa-wifi textrouge"></i> - ' + retry_count);
    }
});
//
socket_keep_on.on('reconnect', function (data) {
    if (!localStorage.PHPSESSID || !localStorage.IDAGENT) return;
    if ($('socket_keep_on_status')) {
        $('socket_keep_on_status').hide();
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
    if (!$(appid)) return;
    $(appid).select('.glue_requested').invoke('show');
    $(appid).select('.glue_reserve').invoke('toggleContent');
});
socket_keep_on.on('request_contact_unsubscribe', function (data) {
    console.log('request request_contact_unsubscribe ', data);
    var appid = data.APPID;
    if (!$(appid)) return;
    $(appid).select('.glue_requested').invoke('hide');
});
// glue_reserve
socket_keep_on.on('glue_reserve', function (data) {
    //
    var iuj = $$('[data-appid=' + data.RESERVEDID + ']').first();
    // cache item aux autres
    if (data.APPID == localStorage.APPID) {
        console.log('glue_reserve bye WIN !!!');
        $(iuj).select('.glue_reserved').invoke('toggleContent');
    } else {
        console.log('glue_reserve sorry !!!');
        $(iuj).select('.glue_reject').invoke('toggleContent');
    }
    $(iuj).select('.glue_busy').invoke('show');
});
// message
socket_keep_on.on('message', function (data) {
    if (!$('socket_keep_on_log')) return;
    // console.log(data)
});
socket_keep_on.on('request_glue', function (data) {
    console.log('request_glue ', data); // not used
});


$('body').on('click', '.keepon_connected', function () {
    keepon_connect_agent();
});
$('body').on('click', '.keepon_disconnected', function () {
    keepon_disconnect_agent();
});
$('body').on('click', '#socket_keep_on_log .glue_reserve', function (event, node) {
    if (!socket_keep_on) return;
    var iuj = node.up('[data-appid]');
    var APPID = iuj.readAttribute('data-appid');
    // lance demande reserve
    socket_keep_on.emit('glue_reserve', {APPID: localStorage.PHPSESSID, RESERVEDID: APPID, IDAGENT: localStorage.IDAGENT});
})

// chat

var chat_tracker_timer = [];

chat_user_add = function (vars) {
    var url_vars = $H(vars).toQueryString();
    var id = vars.id;
    if (!$('socket_keep_on_log')) return;
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    if ($(id)) {
        $(id).removeClassName('ededed').addClassName('blanc')
        return;
    }
    var lnk = '<div id="' + id + '" act_defer data-appid="' + id + '"   mdl="app/app_keepon/app_keepon_item" vars="' + url_vars + '">' + id + '</div>';
    $('socket_keep_on_log').insert(lnk);
}
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
}
chat_user_remove = function (vars) {
    var id = vars.id;
    if (!$('socket_keep_on_log')) return;
    if (chat_tracker_timer[id]) clearTimeout(chat_tracker_timer[id]);
    if ($(id)) {
        $(id).removeClassName('blanc').addClassName('ededed')
    }
    chat_tracker_timer[id] = setTimeout(function () {
        if ($(id)) {
            $(id).remove();
        }
    }, 5000);
}