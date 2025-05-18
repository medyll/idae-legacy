/**
 * Created by Mydde on 11/08/15.
 */

var http = require('http');
var sys = require('sys');
var fs = require('fs');

http.createServer(function(req, res) {
    debugHeaders(req);

    if (req.headers.accept && req.headers.accept == 'text/event-stream') {

        if (req.url == '/events') {
            sendSSE(req, res);
        } else {
            res.writeHead(404);
            res.end();
        }
    } else {
        res.writeHead(200, {'Content-Type': 'text/html'});
        res.write(fs.readFileSync(__dirname + '/sse-node.html'));
        res.end();
    }
}).listen(3000);

function sendSSE(req, res) {
    res.writeHead(200, {
        'Content-Type': 'text/event-stream',
        'Cache-Control': 'no-cache',
        'Connection': 'keep-alive'
    });

    var id = (new Date()).toLocaleTimeString();

    setInterval(function() {
        constructSSE(res, id, (new Date()).toLocaleTimeString());
    }, 5000);

    constructSSE(res, id, (new Date()).toLocaleTimeString());
    //res.end();
}

function constructSSE(res, id, data) {
    res.write('id: ' + id + '\n');
    res.write("data: " + data + '\n\n');
}

function debugHeaders(req) {
    console.log('URL: ' + req.url);
    for (var key in req.headers) {
        console.log(key + ': ' + req.headers[key]);
    }
    console.log('\n\n');
}