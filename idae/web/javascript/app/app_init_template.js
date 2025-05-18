app_init_template = function () {
	return new RSVP.Promise (function (resolve, reject) {
		get_data ('app/app/app_component', '', { directory : 'tpl', extension : 'html' }).then (function (file) {

			var file = file.replace (/(\r\n|\n|\r)/gm, "");
			//  console.clear();
			// console.log({file})
			//  console.log(file)
			escapeRegex = function (value) {
				return value.replace (/([-.*+?\\\^=!:${}()|[\]\/\\])/g, '\\$1'); // prototype
			}

			var regex_main = /<!--\sSTART\s([a-z0-9_]+)\s-->(.*)<!--\sEND\s(\1)\s-->/mig;
			var m;

			while ((m = regex_main.exec (file)) !== null) {
				console.log('...')
				// This is necessary to avoid infinite loops with zero-width matches
				if ( m.index === regex_main.lastIndex ) {
					regex_main.lastIndex++;
				}

				if ( m[1] && m[0] ) {
					if ( !window.APP ) window.APP = {}
					if ( !window.APP.APPTPL ) window.APP.APPTPL = {}
					window.APP.APPTPL[m[1]] = m[2];
				}

			} 
			resolve (true)
		})

	})
}
