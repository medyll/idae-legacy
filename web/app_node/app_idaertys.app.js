/**
 * Created by lebru_000 on 25/12/14.
 */

var sessionMgm = require('sessionManagement');

var app = require('http').createServer(http_handler)
    ,	io 		= 	require('socket.io')(app)
   /* ,   https    =   require('https')*/
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
    ,   clustered_node = require("clustered-node");

http.globalAgent.maxSockets = Infinity;

// var app = express();



var port = ('/var/www/idaertys_preprod.mydde.fr/web/app_node'==__dirname)? '3006' : '3005';

console.log('app_idaertys demarré sur port '+port);

app.listen(port);



// move post here, this way
http.get('/', function (req, res) {

});

io.set('authorization', function (handshakeData, accept) {

	console.log('auth',handshakeData.headers)
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
function http_handler(req, res) {
	//
    if (req.url === '/favicon.ico') {
        res.writeHead(200, {'Content-Type': 'image/x-icon'} );
        res.end();
        return;
    }
    var path = url.parse(req.url).pathname;

    switch (path){
        case '/postScope':
            // DOCUMENTDOMAIN
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
                reloadVars={scope:data.scope,value:data.value}
                if(data.vars) reloadVars.vars = qs.stringify(data.vars)
                if(data.scope && data.value){
                    io.sockets.emit('reloadScope',reloadVars);
                }
                res.writeHead(200, {'Content-Type': 'text/html'})
                res.end();
            });
            break;
	    case "/run":
		    var fullBody = '';
		    req.on('data', function(chunk) {
			    fullBody += chunk.toString();
			    if (fullBody.length > 1e6) {
				    req.connection.destroy();
			    }
		    });
		    req.on('end', function() {
			    data = qs.parse(fullBody);

			    data.vars = data.vars || ''
			    data.options = data.options || {}
			    data.vars.defer = '';
			    SESSID = data.SESSID || '';
			    PHPSESSID = data.PHPSESSID || 'none';
			    DOCUMENTDOMAIN = data.DOCUMENTDOMAIN;
			    //
			    var url = 'http://'+DOCUMENTDOMAIN+'/'+data.mdl+'.php'

			    request.post({
				    url	:	url ,
				    method: 'POST',
				    headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
				    body: qs.stringify(data.vars)
			    },function(err,res,body){
				   //  console.log('run ',body);
			    });
		    })

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
                    uri:'http://'+DOCUMENTDOMAIN+'/mdl/'+data.mdl+'.php',
                    headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
                    body: qs.stringify(vars)
                },function(err,res,body){
	                console.log('rumModule',mdl,body);
                    io.sockets.emit('act_run',{body:body})

                });
                res.writeHead(200, {'Content-Type': 'text/html'})
                res.end();
            });


            break;
        case '/postReload': // => middleware ici
            //  DOCUMENTDOMAIN
            var fullBody = '';

            req.on('data', function(chunk) {
                fullBody += chunk.toString();
	           // console.log(fullBody,fullBody.length,1e6)
                if (fullBody.length > 1e6) {
	                console.log('destroy ',fullBody.length)
                    req.connection.destroy();
                }
            });
            req.on('end', function() {


                var data = qs.parse(fullBody);

	            res.writeHead(200, {'Content-Type': 'text/html'});
	            res.end();
                //
	            DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || '';
                reloadVars={module:data.module,value:data.value || ''};
                //

	           // console.log( 'Type postReload ', typeof(data.vars));

                if(data.cmd && data.vars){
                    if(data.OWN){
                        io.sockets.to(data.OWN).emit('receive_cmd',data);
                    }else{
	                    io.sockets.to(DOCUMENTDOMAIN).emit('receive_cmd',data);
                    }
                }
	            if(data.vars){
		            if(typeof(data.vars)=='object') reloadVars.vars = qs.stringify(data.vars)
	            }
                if(data.module && data.value){
                    io.sockets.emit('reloadModule',reloadVars);
                }
            });
            break;
    }
}




io.on('connection', function(socket){
    if(socket.handshake.headers.cookie){
	    if(socket.handshake.headers.host){
		    console.log((socket.handshake.headers.host))
		    socket.join(socket.handshake.headers.host)
	    }
        json_cookie = cookie.parse(socket.handshake.headers.cookie);
        if(json_cookie.PHPSESSID){
            socket.join(json_cookie.PHPSESSID);

            socket.PHPSESSID = json_cookie.PHPSESSID;
            socket.cookie_string = socket.handshake.headers.cookie;

        }
    };
    // if(!socket.PHPSESSID) { console.log('refused ',socket.handshake); return;}
    // HEARTBEAT
    var sender = setInterval(function () {
        socket.emit('message', new Date().getTime());
        socket.emit('heartbeat_app', new Date().getTime());
    }, 10000);

    socket.on('disconnect', function(){
        clearInterval(sender);

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
		console.log('grantin ',data)
        sess.sessionId 		= socket.id;
        sess.userId			= data.SESSID;
        sess.SESSID			= data.SESSID;
        sess.PHPSESSID		= data.PHPSESSID;
       // sess.SSSAVEPATH		= data.SSSAVEPATH;
	    //
	    if(data.DOCUMENTDOMAIN){
		    socket.join(data.DOCUMENTDOMAIN)
	    }
        //
	    io.sockets.to(data.DOCUMENTDOMAIN).send('user connected');
        // socket.broadcast.send('user connected');
        //
        sessionMgm.add(sess);
        //

        if(data.SESSID){
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
        PHPSESSID = data.PHPSESSID || '';
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
	    //
	    if(DOCUMENTDOMAIN){
		    socket.join(DOCUMENTDOMAIN)
	    }
        //
        request.post({
            uri:'http://'+DOCUMENTDOMAIN+'/mdl/'+data.mdl+'.php?SESSID='+SESSID,
            headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
            body:  qs.stringify(vars)
        },function(err,res,body){
            socket.emit('loadModule',{body:body,vars:vars,mdl:mdl,title:title})
        });
    });

	// npm install --save tough-cookie
	socket.on('socketModule', function(data) {
		data.vars = data.vars || ''
		data.options = data.options || {}
		data.vars.defer = '';
		SESSID = data.SESSID || '';
		PHPSESSID = data.PHPSESSID || '';
		//SSSAVEPATH = data.SSSAVEPATH || '/';

		DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';
		//
		if(DOCUMENTDOMAIN){
			socket.join(DOCUMENTDOMAIN);
		}
		//
		var url = 'http://'+DOCUMENTDOMAIN+'/mdl/'+data.file+'.php';

		request.post({
			url	:	url ,
			method: 'POST',
			headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
			body:  data.vars
		},function(err,res,body){
			socket.emit('socketModule',{body:body,out:data});
		});
	});
    socket.on('upd_data', function(data) {
        vars = data.vars || '';
        title = data.title || '';
        mdl = data.mdl || '';
        SESSID = data.SESSID || '';
		PHPSESSID = data.PHPSESSID || '';
			    
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
        //
        request.post({
            uri:'http://'+DOCUMENTDOMAIN+'/services/json_data_table_row.php',
            headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
            body:  qs.stringify(vars)
        },function(err,res,body){
            io.sockets.emit('upd_data',{body:body,vars:vars,mdl:mdl,title:title});
            // socket.emit('upd_data',{body:body,vars:vars,mdl:mdl,title:title})
        });
    });

    socket.on('get_data', function(data,options,fn) {
        var vars = data.vars || '',
            mdl = data.mdl || '',
            options = options || {},
            SESSID = data.SESSID || '',
		    PHPSESSID = data.PHPSESSID || '',
           /* SSSAVEPATH = data.SSSAVEPATH || '/',*/
            DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
        //  	 fait tout planter mongo ICI
        var url = 'http://'+DOCUMENTDOMAIN+'/services/'+data.mdl+'.php'


        console.log('get_data parameters : ',data);
        var j = request.jar();
        var soc_cook = socket.cookie_string;

        if(soc_cook !== undefined  && soc_cook !== undefined  && typeof soc_cook === 'string'  && socket.PHPSESSID !== undefined  ){

            request.get({
                url	:	url ,
                jar	:	j,
                method: 'GET',
                headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type'	: 'application/x-www-form-urlencoded'},
                qs:   vars
            },function(err,res,body){
                fn(body,options)
            });
        }else{
            // console.log('socket.cookie_string ',socket.cookie_string,'socket.PHPSESSID ',socket.PHPSESSID)
            //
            request.get({
                url	:	url ,
                method: 'GET',
                headers:{'content-type'	: 'application/x-www-form-urlencoded'},
                qs:   vars
            },function(err,res,body){
                fn(body,options)
            });
        }

    });

    var runningRunModule = {};

    socket.on('runModule', function(data) {

        data.vars = data.vars || ''
        data.options = data.options || {}
        data.vars.defer = '';
        var SESSID = data.SESSID || '',
		PHPSESSID = data.PHPSESSID || '',
        /*SSSAVEPATH = data.SSSAVEPATH || '/',*/
        DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';

        //
        var url = 'http://'+DOCUMENTDOMAIN+'/'+data.file+'.php';
        // ssid + file + vars
        key = SESSID+data.file+data.vars;
        //
        request.post({
            url	:	url ,
            method: 'POST',
            headers:{'Cookie':'PHPSESSID='+PHPSESSID+'; path=/','content-type': 'application/x-www-form-urlencoded'},
            body:  data.vars
        },function(err,res,body){

        });

    });
})