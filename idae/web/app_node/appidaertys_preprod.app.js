var port = ('/var/www/idaertys_preprod.mydde.fr/web/app_node' == __dirname) ? '3006' : '3005';
var port = ('D:\\boulot\\UwAmp\\www\\idae.jquery.lan\\web\\app_node' == __dirname) ? '3005' : port;
var port = ('D:\\boulot\\UwAmp\\www\\idae.preprod.lan\\web\\app_node' == __dirname) ? '3005' : port;

var appsocket     = require ('./appmodules/appsocket.app.js');
var appmiddleware = require ('./appmodules/appmiddleware.app.js');
var appcron       = require ('./appmodules/appcron.app.js');

appsocket.socket_start (port);
appcron.cron_start ();