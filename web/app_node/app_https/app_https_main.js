var port = ('/var/www/idaertys_preprod.mydde.fr/web/app_node' == __dirname) ? '3007' : '3007';

var appsocket = require('./app_https_socket.js');
var appmiddleware = require('./app_https_middleware.js');

console.log(port)


appsocket.socket_start(port)

