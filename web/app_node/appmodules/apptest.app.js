/**
 * Created by Mydde on 27/09/2016.
 */

var http = require("http");
var url = require('url');
var fs = require('fs');
var io = require('socket.io');

var server = http.createServer(function (request, response) {
	var path = url.parse(request.url).pathname;

	switch (path) {
		case '/':
			response.writeHead(200, {'Content-Type': 'text/html'});
			response.write('hello world');
			response.end();
			break;
		case '/socket.html':
			fs.readFile(__dirname + path, function (error, data) {
				if (error) {
					response.writeHead(404);
					response.write("opps this doesn't exist - 404");
					response.end();
				}
				else {
					response.writeHead(200, {"Content-Type": "text/html"});
					response.write(data, "utf8");
					response.end();
				}
			});
			break;
		default:
			response.writeHead(404);
			response.write("opps this doesn't exist - 404");
			response.end();
			break;
	}
});

listener = server.listen(3006);

io.listen(server);

listener.on('connection',function(res,req){

})
/*
io.use(function (socket, next) {
	console.log('auth')
	if (socket.request.headers.cookie) {
		// if there is, parse the cookie
		/!*handshakeData.cookie = cookie.parse(handshakeData.headers.cookie);
		 cookie_obj = cookie.parse(handshakeData.headers.cookie);

		 if (cookie_obj.PHPSESSID == 'undefined') {
		 console.log('Bad transmitted.');
		 next(new Error('not authorized'));
		 }
		 console.log('auth ok');
		 next();*!/

	} else {
		next(new Error('not authorized'));
	}
});*/
