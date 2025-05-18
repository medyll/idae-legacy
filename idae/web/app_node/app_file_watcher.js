/**
 * Created by Mydde on 13/10/2015.
 */


var filewatcher = require('filewatcher');

var watcher = filewatcher();
// ... or a directory
watcher.add('');

watcher.on('change', function(file, stat) {
	console.log('File modified: %s', file);
	if (!stat) console.log('deleted');
});
