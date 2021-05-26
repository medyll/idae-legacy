<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/09/2015
	 * Time: 16:20
	 */
	require_once('../conf.inc.php');
	ini_set('display_errors',55);

	$client   = new Hoa\Websocket\Client(
		new Hoa\Socket\Client('tcp://idaertys.mydde.fr:3002')
	);
	$client->setHost('idaertys.mydde.fr');
	$client->connect();


	$client->send('cool super cool');
?>
<html>
<head></head>
<body>
<input type="text" id="input" placeholder="Message…" />
<hr />
<pre id="output"></pre>

<script>
	var host   = 'ws://idaertys.mydde.fr:3002';
	var socket = null;
	var input  = document.getElementById('input');
	var output = document.getElementById('output');
	var print  = function (message) {
		var samp       = document.createElement('samp');
		samp.innerHTML = message + '\n';
		console.log(document.getElementById('output'));
		document.getElementById('output').appendChild(samp);

		return;
	};

	input.addEventListener('keyup', function (evt) {
		if (13 === evt.keyCode) {
			var msg = input.value;

			if (!msg) {
				return;
			}

			try {
				socket.send(msg);
				input.value = '';
				input.focus();
			} catch (e) {
				console.log(e);
			}

			return;
		}
	});

	try {
		socket = new WebSocket(host);
		socket.onopen = function () {
			print('connection is opened');
			input.focus();

			return;
		};
		socket.onmessage = function (msg) {
			print(msg.data);

			return;
		};
		socket.onclose = function () {
			print('connection is closed');

			return;
		};
	} catch (e) {
		console.log(e);
	}
</script>
</body>
</html>