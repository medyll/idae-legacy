/**
 * Created by Mydde on 27/09/2016.
 */
process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";

var http        = require ('http'),
    url         = require ('url'),
    path        = require ('path'),
    mongoClient = require ('mongodb').MongoClient
var CronJob     = require ('cron').CronJob;
var request     = require ('request');

var time_zone = 'Europe/Paris';
var tab_cron  = [];

var tab_host = [];

var get_host = function () {
	switch (__dirname) {
		case '/var/www/idaertys_preprod.mydde.fr/web/app_node/app_modules' :
			tab_host = ['appmaw-idaertys-preprod.mydde.fr', 'appblog.idaertys-preprod.mydde.fr', 'appcrfr.idaertys-preprod.mydde.fr', 'idaertys-preprod.mydde.fr'];
			break;
		case 'D:\\boulot\\UwAmp\\www\\idae.jquery.lan\\web\\app_node\\app_modules' :
			tab_host = ['tactac.idae.jquery.lan', 'maw.idae.jquery.lan', 'leasys.idae.jquery.lan'];
			break;
		case 'D:\\boulot\\UwAmp\\www\\idae.preprod.lan\\web\\app_node\\app_modules' :
			tab_host = ['tactac.idae.preprod.lan', 'maw.idae.preprod.lan', 'leasys.idae.preprod.lan', 'idae.io.idae.preprod.lan', 'blog.idae.preprod.lan'];
			break;
		default :
			tab_host = ['tactac.idae.preprod.lan', 'maw.idae.preprod.lan', 'leasys.idae.preprod.lan', 'idae.io.idae.preprod.lan', 'blog.idae.preprod.lan'];
			break;
	}
	return tab_host;
}

var job_group = function () {
	var crons = {
		'secondes_30'         : '30 * * * * *',
		'minute'              : '00 * * * * *',
		'minutes_5'           : '00 */5 * * * *',
		'minutes_10'          : '00 */10 * * * *',
		'minutes_15'          : '00 */15 * * * *',
		'midday'              : '00 00 12 * * *',
		'hourly_double'       : '00 00 */2 * * *',
		'hourly_mid'          : '00 30 * * * *',
		'hourly'              : '00 00 * * * *',
		'midnight'            : '00 00 00 * * *',
		'midnight_hour_1'     : '00 00 01 * * *',
		'midnight_hour_1_mid' : '00 30 01 * * *',
		'midnight_hour_2'     : '00 00 02 * * *',
		'midnight_hour_2_mid' : '00 30 02 * * *'
	}

	for (const cron_act in crons) {
		tab_cron [cron_act] = new CronJob (crons[cron_act], function () {
			launch_job (cron_act);
		}, null, true, time_zone);
	}
}

var launch_job = function (type) {
	var type = type;

	for (const MAINHOST of tab_host) {
		request.get ({
			url     : 'http://' + MAINHOST + '/bin/cron/cron_dispatch.php?type_cron=' + type + '&host=' + MAINHOST,
			headers : { 'X-CRON' : 'tactac' }
		}, function (err, res, body) {  });
	}
}
var check_job  = function () {

	for (var i in tab_cron) {
		test = tab_cron[i].running;
		if ( test != true ) {
			tab_cron[i].start ();
		}
	}
}

module.exports = {
	tab_host   : tab_host,
	cron_start : function () {
		get_host ();
		job_group ();
		check_job ();
		console.log ('cron_started for ', tab_host);
	}
}