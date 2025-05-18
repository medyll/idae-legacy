/**
 * Created by lebru_000 on 25/12/14.
 */

var sessionMgm = require('sessionManagement');

var app = require('http').createServer(handler)
    ,	io 		= 	require('socket.io')(app)
    ,   http    =   require('http')
    ,   express =   require('express')
    ,   cookie  =   require('cookie')
    ,   connect =   require('connect')
    , 	fs 		= 	require('fs')
    , 	qs 		= 	require('qs')
    , 	url 	= 	require('url')
    , 	request =  	require('request')
    , 	mongo 	=  	require('mongodb')
    , 	Server 	= 	mongo.Server
    , 	Db 		= 	mongo.Db
    , 	MongoOplog = require('mongo-oplog')


app.listen(3005);

// move post here, this way
http.get('/', function (req, res) {
    // console.log(res)
    // res.sendfile(__dirname + '/index.html');
});

io.set('authorization', function (handshakeData, accept) {
    // check if there's a cookie header
    if (handshakeData.headers.cookie) {
        // if there is, parse the cookie
        handshakeData.cookie = cookie.parse(handshakeData.headers.cookie);
        cookie_obj = cookie.parse(handshakeData.headers.cookie);
        if(cookie_obj.PHPSESSID=='undefined'){ console.log('Bad transmitted.'); return accept('Bad transmitted.', false);}
        // }
    } else {
        return accept('No cookie transmitted.', false);
    }
    accept(null, true);
});


var server2 = new Server('localhost', 27017, {auto_reconnect: true});
var db2 = new Db('sitebase_sockets', server2,{safe:false});
db2.open(function(err,db){db.admin().authenticate('admin', 'gwetme2011',function(err,result){}) });
var colOnline = db2.collection('onLine');
var colOnlineSite = db2.collection('onLineSite');


//
function handler(req, res) {
    if (req.url === '/favicon.ico') {
        res.writeHead(200, {'Content-Type': 'image/x-icon'} );
        res.end();
        return;
    }
    var path = url.parse(req.url).pathname;
    switch (path){
        case '/postScope':
            var fullBody = '';
            req.on('data', function(chunk) {
                fullBody += chunk.toString();
                if (fullBody.length > 1e6) {
                    req.connection.destroy();
                }
            });
            req.on('end', function() {
                out = qs.parse(fullBody);
                //
                reloadVars={scope:out.scope,value:out.value}
                if(out.vars) reloadVars.vars = qs.stringify(out.vars)
                if(out.scope && out.value){
                    io.sockets.emit('reloadScope',reloadVars);
                }
                res.writeHead(200, {'Content-Type': 'text/html'})
                res.end();
            });
            break;
        case '/runModule':
            var fullBody = '';
            req.on('data', function(chunk) {
                fullBody += chunk.toString();
                if (fullBody.length > 1e6) {
                    req.connection.destroy();
                }
            });
            req.on('end', function() {
                data = qs.parse(fullBody);
                //
                vars = data.vars || '';
                title = data.title || '';
                mdl = data.mdl || '';
                SESSID = data.SESSID || '';
                PHPSESSID = data.PHPSESSID || '';
                DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
                //
                request.post({
                    uri:'http://'+DOCUMENTDOMAIN+'/mdl/'+data.mdl+'.php?SESSID='+SESSID+'&PHPSESSID='+PHPSESSID,
                    headers:{'content-type': 'application/x-www-form-urlencoded'},
                    body: 'SESSID='+SESSID+'&PHPSESSID='+PHPSESSID+'&'+ qs.stringify(vars)
                },function(err,res,body){
                    console.log(body)
                    io.sockets.emit('act_run',{body:body})

                });
                res.writeHead(200, {'Content-Type': 'text/html'})
                res.end();
            });


            break;
        case '/runForgetModule':
            var fullBody = '';
            req.on('data', function(chunk) {
                fullBody += chunk.toString();
                if (fullBody.length > 1e6) {
                    req.connection.destroy();
                }
            });
            req.on('end', function() {
                data = qs.parse(fullBody);
                //
                vars = data.vars || '';
                title = data.title || '';
                mdl = data.mdl || '';
                SESSID = data.SESSID || '';
                PHPSESSID = data.PHPSESSID || '';
                DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
                //
                request.post({
                    uri:'http://'+DOCUMENTDOMAIN+'/mdl/'+data.mdl+'.php?SESSID='+SESSID+'&PHPSESSID='+PHPSESSID,
                    headers:{'content-type': 'application/x-www-form-urlencoded'},
                    body: 'SESSID='+SESSID+'&PHPSESSID='+PHPSESSID+'&'+ qs.stringify(vars)
                },function(err,res,body){
                    res.resume();
                    console.log(body)
                    io.sockets.emit('act_run',{body:body})
                    res.end('done runForgetModule');

                });
                // res.end();
            });


            break;
        case '/postReload':
            var fullBody = '';
            req.on('data', function(chunk) {
                fullBody += chunk.toString();
                if (fullBody.length > 1e6) {
                    req.connection.destroy();
                }
            });
            req.on('end', function() {
                var out = qs.parse(fullBody);
                //
                reloadVars={module:out.module,value:out.value || ''};
                //
                if(out.vars){
                    if(typeof(out.vars)=='object') reloadVars.vars = qs.stringify(out.vars)
                }


                if(out.cmd && out.vars){
                    // console.log('io.sockets.cmd => ',out);
                    if(out.OWN){
                        io.sockets.to(out.OWN).emit('receive_cmd',out);
                    }else{
                        io.sockets.emit('receive_cmd',out);
                    }
                }
                if(out.module && out.value){
                    io.sockets.emit('reloadModule',reloadVars);
                }
                res.writeHead(200, {'Content-Type': 'text/html'})
                res.end();
            });
            break;
    }
}


var keepFidel = io
    .of('/fidel')
    .on('connection', function (socket) {

        socket.on('disconnect', function(){
            colOnlineSite.remove({socket:socket.id});
            // socket.broadcast.emit('reloadModule',{module:'web_stat/web_stat_online' ,value:'*'});
        });
        //
        socket.on('grantSite', function (data) {
            data = data || {}
            data.ip = data.ip || ''
            data.ssid = data.ssid || ''
            data.host = data.host || ''
            data.uri = data.uri || ''
            dadate = new Date();
            time = dadate.getTime();
            colOnlineSite.update({socket:socket.id},{'$set':{online:1,ip:data.ip,ssid:data.ssid,host:data.host,uri:data.uri,time:eval(time/1000)}},{upsert:true});
            // socket.broadcast.emit('reloadModule',{module:'web_stat/web_stat_online' ,value:'*'});
        });
    })


io.on('connection', function(socket){

    console.log('connection socket');

    if(socket.handshake.headers.cookie){
        json_cookie = cookie.parse(socket.handshake.headers.cookie);
        if(json_cookie.PHPSESSID){
            socket.join(json_cookie.PHPSESSID);
           // socket.id = json_cookie.PHPSESSID;
            socket.PHPSESSID = json_cookie.PHPSESSID;
            socket.cookie_string = socket.handshake.headers.cookie;
        }
    }
    // if(!socket.PHPSESSID) { console.log('refused ',socket.handshake); return;}
    // HEARTBEAT
    var sender = setInterval(function () {
        socket.emit('message', new Date().getTime());
        socket.emit('heartbeat_app', new Date().getTime());
    }, 10000)

    socket.on('disconnect', function() {
        clearInterval(sender);
    })

    socket.on('disconnect', function(){

        dar = sessionMgm.getBySession(socket.id);
        if( dar != null){
            if(dar.SESSID){
                colOnline.update({idagent:eval(dar.SESSID)},{'$set':{online:0}},{upsert:true})
                sessionMgm.removeBySession(socket.id);
            }
        }
    });
    //
    socket.on('grantIn', function (data,fn) {

        var sess            = new Object();

        sess.sessionId 		= socket.id;
        sess.userId			= data.SESSID;
        sess.SESSID			= data.SESSID;
        sess.PHPSESSID		= data.PHPSESSID;
        //
        socket.broadcast.send('user connected');
        //
        sessionMgm.add(sess);
        //

        if(data.SESSID){
            colOnline.update({idagent:eval(data.SESSID)},{'$set':{online:1}},{upsert:true});
            socket.broadcast.emit('reloadModule',{module:'gadget/onliveGadget' ,value:'*'});
            // socket.emit('accessGRANTED',data);
            if(fn) fn('woot');

            socket.emit('notify',data);
        }
    })
    //
    socket.on('message', function(message){
        //	socket.send('cool');
    });
    socket.on('reloadModule', function(data) {
        socket.broadcast.emit('reloadModule',data);
    });
    socket.on('loadModule', function(data) {
        vars = data.vars || '';
        title = data.title || '';
        mdl = data.mdl || '';
        SESSID = data.SESSID || '';
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
        //
        request.post({
            uri:'http://'+DOCUMENTDOMAIN+'/mdl/'+data.mdl+'.php?SESSID='+SESSID,
            headers:{'content-type': 'application/x-www-form-urlencoded'},
            body: 'SESSID='+SESSID+'&'+ qs.stringify(vars)
        },function(err,res,body){
            socket.emit('loadModule',{body:body,vars:vars,mdl:mdl,title:title})
        });
    });
    socket.on('upd_data', function(data) {
        vars = data.vars || '';
        title = data.title || '';
        mdl = data.mdl || '';
        SESSID = data.SESSID || '';
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
        //
        request.post({
            uri:'http://'+DOCUMENTDOMAIN+'/services/json_data_table_row.php?SESSID='+SESSID,
            headers:{'content-type': 'application/x-www-form-urlencoded'},
            body: 'SESSID='+SESSID+'&'+ qs.stringify(vars)
        },function(err,res,body){
            io.sockets.emit('upd_data',{body:body,vars:vars,mdl:mdl,title:title});
            // socket.emit('upd_data',{body:body,vars:vars,mdl:mdl,title:title})
        });
    });

    socket.on('get_data', function(data,options,fn) {
        var vars = data.vars || '',
            mdl = data.mdl || '',
            options = options || {},
            DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
        //  	 fait tout planter mongo ICI
        var url = 'http://'+DOCUMENTDOMAIN+'/services/'+data.mdl+'.php'
        var j = request.jar();
        var soc_cook = socket.cookie_string;


        if(soc_cook !== undefined  && soc_cook !== undefined  && typeof soc_cook === 'string'  && socket.PHPSESSID !== undefined  ){

            request.post({
                url	:	url ,
                jar	:	j,
                method: 'POST',
                headers:{'content-type'	: 'application/x-www-form-urlencoded'},
                body:   qs.stringify(vars) +'&PHPSESSID='+socket.PHPSESSID
            },function(err,res,body){
                fn(body,options)
            });
        }else{
            // console.log('socket.cookie_string ',socket.cookie_string,'socket.PHPSESSID ',socket.PHPSESSID)
            //
            request.post({
                url	:	url ,
                method: 'POST',
                headers:{'content-type'	: 'application/x-www-form-urlencoded'},
                body:   qs.stringify(vars)
            },function(err,res,body){
                fn(body,options)
            });
        }

    });
    // npm install --save tough-cookie
    socket.on('socketModule', function(data) {

        data.vars = data.vars || ''
        data.options = data.options || {}
        data.vars.defer = '';
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';
        //
        var url = 'http://'+DOCUMENTDOMAIN+'/mdl/'+data.file+'.php'
//		var j = request.jar()
//		var cookie = request.cookie(socket.cookie_string)
//		j.setCookie(cookie, 'http://'+DOCUMENTDOMAIN);

        request.post({
            url	:	url ,
            //	jar	:	j,
            method: 'POST',
            headers:{'content-type': 'application/x-www-form-urlencoded'},
            body:  data.vars +'&PHPSESSID='+socket.PHPSESSID
        },function(err,res,body){
            socket.emit('socketModule',{body:body,out:data});
        });
    });
    socket.on('runModule', function(data) {

        data.vars = data.vars || ''
        data.options = data.options || {}
        data.vars.defer = '';
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';
        //
        var url = 'http://'+DOCUMENTDOMAIN+'/'+data.file+'.php'
//		var j = request.jar()
//		var cookie = request.cookie(socket.cookie_string)
//		j.setCookie(cookie, 'http://'+DOCUMENTDOMAIN);

        request.post({
            url	:	url ,
            method: 'POST',
            headers:{'content-type': 'application/x-www-form-urlencoded'},
            body:  data.vars
        },function(err,res,body){
            console.log('runModule ',body);
        });
    });
})