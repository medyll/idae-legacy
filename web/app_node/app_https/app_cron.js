

var CronJob = require('cron').CronJob;
var request = require('request');


new CronJob('00 * * * * *', function() {
	console.log('You will see this message every minutes');
}, null, true, 'America/Los_Angeles');

new CronJob('00 */2 * * * *', function() {
	console.log('You will see this message at every   02  minutes');
}, null, true, 'America/Los_Angeles');

new CronJob('00 */10 * * * *', function() {

	request.post({
		url: 'https://tac-tac.shop.mydde.fr/',
		method: 'POST',
		headers: {'Cookie': 'PHPSESSID=' + PHPSESSID + '; path=/', 'content-type': 'application/x-www-form-urlencoded'},
		body: qs.stringify(data.vars)
	}, function (err, res, body) {
		// console.log('run ',body,err);
	});

}, null, true, 'America/Los_Angeles');

new CronJob('00 00 * * * *', function() {
	console.log('You will see this message every hour');
}, null, true, 'America/Los_Angeles');