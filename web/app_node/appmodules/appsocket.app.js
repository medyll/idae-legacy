/**
 * Created by lebru_000 on 25/12/14.
 */

// console.log('NODE_TLS_REJECT_UNAUTHORIZED ',process.env.NODE_TLS_REJECT_UNAUTHORIZED)
// process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;

var fs = require ('fs');

/*var ssl_opt = {
 key: fs.readFileSync('/etc/letsencrypt/live/idaertys-preprod.mydde.fr/privkey.pem'),
 cert: fs.readFileSync('/etc/letsencrypt/live/idaertys-preprod.mydde.fr/fullchain.pem'),
 ca: fs.readFileSync('/etc/letsencrypt/live/idaertys-preprod.mydde.fr/chain.pem'),
 requestCert: false,
 rejectUnauthorized: false
 }*/

var https = require ('https');

var app          = require ('http').createServer (http_handler)
var io           = require ('socket.io') (app);
var url          = require ('url');
var qs           = require ('qs');
var request      = require ('request');
var cookieParser = require ('socket.io-cookie');
var mongoClient  = require ('mongodb').MongoClient

function http_handler(req, res) {

	if ( !req.url ) return;

	if ( req.url === '/favicon.ico' ) {
		res.writeHead (200, { 'Content-Type' : 'image/x-icon' });
		res.end ();
		return;
	}
	var path = url.parse (req.url).pathname;

	//
	var fullBody = '';

	req.on ('data', function (chunk) {
		fullBody += chunk.toString ();
		if ( fullBody.length > 1e6 ) {
			req.connection.destroy ();
		}
	});

	//
	switch (path) {
		case '/postScope':

			req.on ('end', function () {
				data = qs.parse (fullBody);
				//
				reloadVars = { scope : data.scope, value : data.value }
				if ( data.vars ) reloadVars.vars = qs.stringify (data.vars)
				if ( data.scope && data.value ) {
					io.sockets.emit ('reloadScope', reloadVars);
				}
				res.writeHead (200, { 'Content-Type' : 'text/html' })
				res.end ();
			});
			break;
		case "/run":

			req.on ('end', function () {
				data = qs.parse (fullBody);
				//  console.log("/run data",data)
				data.vars       = data.vars || ''
				data.options    = data.options || {}
				data.vars.defer = '';
				SESSID          = data.SESSID || '';
				PHPSESSID       = data.PHPSESSID || 'none';
				DOCUMENTDOMAIN  = data.DOCUMENTDOMAIN;
				//
				var url = 'http://' + DOCUMENTDOMAIN + '/' + data.mdl + '.php'
				//console.log('run => '+url)
				request.post ({
					url     : url,
					method  : 'POST',
					headers : { 'Cookie' : 'PHPSESSID=' + PHPSESSID + '; path=/', 'content-type' : 'application/x-www-form-urlencoded' },
					body    : qs.stringify (data.vars)
				}, function (err, res, body) {
					// console.log('run <= ',body,err);
				});
			})

			break;
		case '/runModule':

			req.on ('end', function () {
				data = qs.parse (fullBody);
				//
				vars           = data.vars || '';
				title          = data.title || '';
				mdl            = data.mdl || '';
				SESSID         = data.SESSID || '';
				PHPSESSID      = data.PHPSESSID || '';
				DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
				//
				console.log('rumModule => ', mdl);
				request.post ({
					uri     : 'http://' + DOCUMENTDOMAIN + '/mdl/' + data.mdl + '.php',
					headers : { 'Cookie' : 'PHPSESSID=' + PHPSESSID + '; path=/', 'content-type' : 'application/x-www-form-urlencoded' },
					body    : qs.stringify (vars)
				}, function (err, res, body) {
					console.log('rumModule <= ', mdl, body);
					io.sockets.emit ('act_run', { body : body })

				});
				res.writeHead (200, { 'Content-Type' : 'text/html' })
				res.end ();
			});

			break;
		case '/postReload': // => middleware ici

			req.on ('end', function () {

				var data = qs.parse (fullBody);

				res.writeHead (200, { 'Content-Type' : 'text/html' });
				res.end ();

				//
				DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || '';
				reloadVars     = { module : data.module, value : data.value || '' };
				//
				// console.log( 'Type postReload ', typeof(data.vars));

				if ( data.cmd && data.vars ) {
					if ( data.OWN ) {
						io.sockets.to (data.OWN).emit ('receive_cmd', data);
					} else {
						io.sockets.to (DOCUMENTDOMAIN).emit ('receive_cmd', data);
					}
				}
				if ( data.vars ) {
					if ( typeof(data.vars) == 'object' ) reloadVars.vars = qs.stringify (data.vars)
				}
				if ( data.module && data.value ) {
					io.sockets.emit ('reloadModule', reloadVars);
				}
			});
			break;
	}
}

function init_app() {
	// console.log('ini_app function');

	io.use (cookieParser);
	io.use (authorization);
	socket_onconnection ();
}
//
function authorization(socket, next) {
	// console.log('authorization')
	// console.log('auth', socket.handshake.headers);
	// console.log('-----------------------------');

	// socket.request.headers : origin: 'http://idaertys-preprod.mydde.fr',  host: 'idaertys-preprod.mydde.fr:3006',
	if ( socket.request.headers.cookie ) {
		var cookie = socket.request.headers.cookie;
		//  PHPSESSID TEST
		if ( !cookie.PHPSESSID ) {
			next (new Error ('not authorized'));
			return false;
		}
		//
		//console.log('auth ok', socket.request.headers.cookie);
		socket.PHPSESSID = cookie.PHPSESSID;
		next ();
		authorization_ok (socket);

	} else {
		{
			if ( socket.handshake ) {
				console.log ('Echec .request', socket.request.headers);

			}
			next (new Error ('not authorized'));
			return false;
		}
	}
};

/*init_app = function () {
 var port = ('/var/www/idaertys_preprod.mydde.fr/web/app_node' == __dirname) ? '3006' : '3005';

 console.log('app_idaertys_socket sur port ' + port);

 }*/
var mongo_url;
declare_db = function (host) {
	if ( host == null ) return false;
	console.log(host)
	switch (host) {
		case 'http://appmaw.mydde.fr':
		case 'http://maw.idae.preprod.lan':
		case 'http://appmaw-idaertys-preprod.mydde.fr':
			prefix = 'maw';
			break;
		case 'http://appcrfr.mydde.fr':
			prefix = 'crfr';
			break;
		case 'http://appcrfr.idaertys-preprod.mydde.fr':
			prefix = 'crfr';
			break;
		case 'http://idaertys.mydde.fr':
			prefix = 'idaenext';
			break;
		case 'http://leasys.idae.preprod.lan':
		case 'http://idaertys-preprod.mydde.fr':
			prefix = 'idaenext';
			break;
		case 'http://tactac.idae.preprod.lan':
		case 'http://tactac_idae.preprod.mydde.fr':
			prefix = 'tactac';
			break;
		case 'http://idae.io.idae.preprod.lan':
			prefix = 'idae_io';
			break;
		case 'http://blog.idae.preprod.lan':
			prefix = 'appblog';
			break;
	}
console.log([host,prefix])

	//var dbname = prefix + '_sitebase_base';
	//mongo_url  = 'mongodb://admin:gwetme2011@localhost/' + dbname + '?authMechanism=DEFAULT&authSource=admin';

//	mongo_url_main = 'mongodb://admin:gwetme2011@localhost/appmain_sitebase_base?authMechanism=DEFAULT&authSource=admin';

/*	mongoClient.connect (mongo_url_main, { server : { auto_reconnect : true, disconnect : true } }, function (error, db) {
		if ( error ) throw error;
		table_cron_job = db.collection ('sockets');
		table_cron_job.update ({ idagent : eval (dar.SESSID) }, { '$set' : { online : 0 } }, { upsert : true })
		colOnline      = db2.collection ('onLine');
		colOnlineSite  = db2.collection ('onLineSite');
	})*/

}

authorization_ok = function (socket) {

	declare_db (socket.request.headers.origin);
	console.log('authorization_ok done');
	socket.emit ('ask_grantIn');

	// socket_onconnection();
}

build_header = function (data) {
	//
	if ( !data.DOCUMENTDOMAIN ) return '';
	data.vars = data.vars || ''
	SESSID    = data.SESSID || '';
	PHPSESSID = data.PHPSESSID || '';
	// SSSAVEPATH = data.SSSAVEPATH || '/';

	DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';
	//
	headers = { 'Cookie' : 'PHPSESSID=' + PHPSESSID + '; path=/', 'content-type' : 'application/x-www-form-urlencoded' };

	return headers;
}
build_vars   = function (data) {
	if ( !data.DOCUMENTDOMAIN ) return '';
	data.vars = data.vars || ''

	return data.vars;
};

function socket_onconnection() {

	io.on ('connection', function (socket) {

		// console.log('connection');
		if ( socket.PHPSESSID ) {
			// console.log('join PHPSESSID');
			socket.join (socket.PHPSESSID);
		}

		// HEARTBEAT
		/*var sender = setInterval(function () {
		 socket.emit('heartbeat_app', [new Date().getTime(),socket.PHPSESSID]);
		 }, 5000);*/

		socket.on ('disconnect', function (data) {
			// clearInterval(sender);


		});
		//
		socket.on ('grantIn', function (data, fn) {
			// console.log('grantIn',data)
			var sess            = new Object ();
			sess.sessionId      = socket.id;
			sess.DOCUMENTDOMAIN = data.DOCUMENTDOMAIN;
			sess.SESSID         = data.SESSID;
			sess.PHPSESSID      = data.PHPSESSID;
			// sess.SSSAVEPATH = data.SSSAVEPATH;
			//
			if ( data.DOCUMENTDOMAIN ) {
				socket.join (data.DOCUMENTDOMAIN)
			}
			if ( data.IDAGENT ) {
				socket.idagent = data.IDAGENT;
				socket.join (data.DOCUMENTDOMAIN + 'IDAGENT' + data.IDAGENT);
				/*mongoClient.connect (mongo_url, { server : { auto_reconnect : true, disconnect : true } }, function (error, db) {
					if ( error ) throw error;

					var table_agent = db.collection ('agent');
					table_agent.update ({ idagent : eval (data.IDAGENT) }, { '$set' : { onlineAgent : 1 } }, { upsert : true })
				});*/
			}
			//
			io.sockets.to (data.DOCUMENTDOMAIN).send ('user connected');
			// socket.broadcast.send('user connected');
			if ( data.SESSID ) {
				if ( fn ) fn (true);
				// socket.emit('notify',data);
			}
		})
		//
		socket.on ('message', function (message) {
			// console.log(message) //	socket.send('cool');
		});
		// dispatcher
		socket.on ('dispatch_cmd', function (data) {
			// console.log('dispatch_cmd ',data)
			io.sockets.emit (data.cmd, { vars : data });
			if ( data.dispatch_to ) {
				io.sockets.to (data.dispatch_to).emit ('receive_cmd', { cmd : data.cmd, vars : data.dispatch_vars });
			} else {
				io.sockets.to (DOCUMENTDOMAIN).emit ('receive_cmd', { cmd : data.cmd, vars : data.dispatch_vars });
			}

		})
		socket.on ('reloadModule', function (data) {
			socket.broadcast.emit ('reloadModule', data);
		});
		socket.on ('reloadScope', function (data) {
			// console.log(data);
			if ( !data ) return;
			if ( !data.scope ) return;
			reloadVars = { scope : data.scope, value : data.value }
			if ( data.vars ) reloadVars.vars = qs.stringify (data.vars)
			if ( data.scope && data.value ) {
				io.sockets.emit ('reloadScope', reloadVars);
			}
		});
		// loadModule
		socket.on ('loadModule', function (data, func) {
			var fn = func || null
			vars   = data.vars || '';
			title  = data.title || '';
			mdl    = data.mdl || '';

			DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
			//
			if ( DOCUMENTDOMAIN ) {
				socket.join (DOCUMENTDOMAIN)
			}
			//
			request.post ({
				uri     : 'http://' + DOCUMENTDOMAIN + '/mdl/' + data.mdl + '.php',
				headers : build_header (data),
				body    : qs.stringify (vars)
			}, function (err, res, body) {
				socket.emit ('loadModule', { body : body, vars : vars, mdl : mdl, title : title })
			});
		});

		//
		socket.on ('socketModule', function (data, fun) {
			var fn = fun || null

			data.vars       = data.vars || ''
			data.options    = data.options || {}
			data.vars.defer = '';

			DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';
			//
			if ( DOCUMENTDOMAIN ) {
				socket.join (DOCUMENTDOMAIN);
			}
			//
			var url = 'http://' + DOCUMENTDOMAIN + '/mdl/' + data.file + '.php';

			request.post ({
				url     : url,
				method  : 'POST',
				headers : build_header (data),
				body    : data.vars
			}, function (err, res, body) {
				socket.emit ('socketModule', { body : body, out : data });
				if ( fn ) fn ({ body : body, data : data });
			});
		});
		socket.on ('upd_data', function (data) {
			vars  = data.vars || '';
			title = data.title || '';
			mdl   = data.mdl || '';

			DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';
			//
			request.post ({
				uri     : 'http://' + DOCUMENTDOMAIN + '/services/json_data_table_row.php',
				headers : build_header (data),
				body    : qs.stringify (vars)
			}, function (err, res, body) {
				io.sockets.emit ('upd_data', { body : body, vars : vars, mdl : mdl, title : title });
				// socket.emit('upd_data',{body:body,vars:vars,mdl:mdl,title:title})
			});
		});

		socket.on ('get_data', function (data, options, fn) {
			var vars           = data.vars || '',
			    mdl            = data.mdl || '',
			    options        = options || {},
			    DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'app.destinationsreve.com';

			var directory = (data.directory) ? data.directory : 'services';
			var extension = (data.extension) ? data.extension : 'php';
			//
			var url = 'http://' + DOCUMENTDOMAIN + '/' + directory + '/' + data.mdl + '.' + extension;
			// var j = request.jar();

			if ( socket.PHPSESSID !== undefined ) {

				request.get ({
					url     : url,
					method  : 'GET',
					headers : build_header (data),
					qs      : vars
				}, function (err, res, body) {
					// console.log(err,body, options)
					fn (body, options)
				});
			} else {
				//  console.log('socket.cookie_string !!! ',socket.cookie_string,'socket.PHPSESSID ',socket.PHPSESSID)
				//
				/*request.get({
				 url: url,
				 method: 'GET',
				 headers: {'content-type': 'application/x-www-form-urlencoded'},
				 qs: vars
				 }, function (err, res, body) {
				 fn(body, options)
				 });*/
			}

		});

		var runningRunModule = {};

		socket.on ('runModule', function (data) {

			data.vars          = data.vars || ''
			data.options       = data.options || {}
			data.vars.defer    = '';
			var SESSID         = data.SESSID || '',
			    DOCUMENTDOMAIN = data.DOCUMENTDOMAIN || 'appgem.destinationsreve.com';

			//
			var url = 'http://' + DOCUMENTDOMAIN + '/' + data.file + '.php';
			// ssid + file + vars
			key = SESSID + data.file + data.vars;
			//
			// console.log('rumModule => ', url);
			request.post ({
				url     : url,
				method  : 'POST',
				headers : build_header (data),
				body    : data.vars
			}, function (err, res, body) {
				// console.log('rumModule <= ', url, body);
			});

		});
	})

}

module.exports = {

	init_app     : function (port) {

	},
	socket_start : function (port) {
		//
		console.log ('socket_start on port ' + port)

		app.listen (port);
		init_app ();

	},
	io           : io,
	bar          : function () {
		// whatever
	}

}