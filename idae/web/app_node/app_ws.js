/**
 * Created by Mydde on 24/08/2015.
 */
var WebSocketServer = require('ws').Server
	, wss = new WebSocketServer({port: 9000});
wss.on('connection', function(ws) {
	ws.on('message', function(message) {
		console.log('received: %s', message);
	});
	ws.send('something');
});