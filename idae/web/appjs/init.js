setInterval(function () {
	get_data('json_ssid', {}).then(function (res) {
		var res_json = JSON.parse(res);
		var cookieJar = new CookieJar();
		var cookie_str = cookieJar.getPack();
		var exit_app = false;

		var arr_verif = ['PHPSESSID', 'SESSID', 'idagent'];
		arr_verif.forEach(function (item) {
			var test = cookieJar.get(item) || null;
			if (test == null) exit_app = true;

			// console.log(exit_app, item , cookieJar.get(item),localStorage.getItem(item))
			// if (test != localStorage.getItem(item)) exit_app = true;
		}.bind(this))
		if (exit_app == true) ajaxValidation('quitter')
		// if (PHPSESSID_TEST.PHPSESSID != localStorage.getItem('PHPSESSID'))
	})
}, 30000);