/**
 * Javascript code to store data as JSON strings in cookies. 
 * Modernized 2026-08-13: no PrototypeJS dependency.
 * 
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/jsoncookies
 * License: Creative Commons Attribution-ShareAlike 2.5
 *          http://creativecommons.org/licenses/by-sa/2.5/
 * Updated: Apr 12, 2007 8:50pm 
 */

var CookieJar = function (options) {
	this.initialize(options);
};

CookieJar.prototype = {
	/**
	 * Initializes the cookie jar with the options.
	 */
	initialize: function(options) {
		this.options = {
            expires: 3600,		// seconds (1 hr)
			path: '',			// cookie path
			domain: '',			// cookie domain
			secure: ''			// secure ?
		};
		Object.assign(this.options, options || {});

		if (this.options.expires != '') {
			var date = new Date();
			date.setTime(date.getTime() + (this.options.expires * 1000));
			this.options.expires = '; expires=' + date.toGMTString();
		}
		if (this.options.path != '') {
			this.options.path = '; path=' + escape(this.options.path);
		}
		if (this.options.domain != '') {
			this.options.domain = '; domain=' + escape(this.options.domain);
		}
		if (this.options.secure == 'secure') {
			this.options.secure = '; secure';
		} else {
			this.options.secure = '';
		}
	},

	/**
	 * Adds a name values pair.
	 */
	put: function(name, value) {
		var cookie = this.options;
		var type = typeof value;
		switch(type) {
		  case 'undefined':
		  case 'function':
		  case 'unknown': return false;
		  case 'boolean': 
		  case 'string' : 
		  case 'number' : value = String(value.toString());
		}
		var cookie_str = name + "=" + JSON.stringify(value);// escape(Object.toJSON(value));
		try {
			document.cookie = cookie_str + cookie.expires + cookie.path + cookie.domain + cookie.secure;
		} catch (e) {
			return false;
		}
		return true;
	},

	/**
	 * Removes a particular cookie (name value pair) form the Cookie Jar.
	 */
	remove: function(name) {
		var cookie = this.options;
		try {
			var date = new Date();
			date.setTime(date.getTime() - (3600 * 1000));
			var expires = '; expires=' + date.toGMTString();
			document.cookie = name + "=" + expires + cookie.path + cookie.domain + cookie.secure;
		} catch (e) {
			return false;
		}
		return true;
	},

	/**
	 * Return a particular cookie by name;
	 */
	get: function(name) {
		var cookies = document.cookie.match(name + '=(.*?)(;|$)');
		if (cookies) {
			return cookies[1];
			// old way return   (unescape(cookies[1])).evalJSON();
		} else {
			return null;
		}
	},

	/**
	 * Empties the Cookie Jar. Deletes all the cookies.
	 */
	empty: function() {
		var keys = this.getKeys();
		var size = keys.length;
		for(var i=0; i<size; i++) {
			this.remove(keys[i]);
		}
	},

	/**
	 * Returns all cookies as a single object
	 */
	getPack: function() {
		var pack = {};
		var keys = this.getKeys();

		var size = keys.length;
		for(var i=0; i<size; i++) {
			pack[keys[i]] = this.get(keys[i]);
		}
		return pack;
	},

	/**
	 * Returns all keys.
	 */
	getKeys: function() {
		var keys = [];
		var keyRe = /[^=; ]+(?=\=)/g;
		var str = document.cookie;
		var match;
		while((match = keyRe.exec(str)) != undefined) {
			keys.push(match[0].trim());
		}
		return keys;
	}
};
