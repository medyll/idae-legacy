/**
 * Created by Mydde on 04/11/2016.
 */

var require_app = [
	'app',
	'app_php',
	'app_cache',
	'app_socket',
	'app_functions',
	'app_live_data',
	'app_datatable',
	'app_keepon',
	'app_chat',
	'app_planning',
	'app_conge',
	'app_window',
	'app_calendrier',
	'app_contextual',
	'app_menu',
	'app_quickfind',
	'app_tree',
	'app_init_template',
	'app_insertionQ'];

window.require_obj = {};
window.require_paths = {};

require_app.forEach(function (thread) {
	// require_obj[thread] = {exports:''}
	require_paths[thread] = 'app/'+thread
})

requirejs.config({
	baseUrl: 'javascript/app',
	paths : require_paths,
	onNodeCreated: function (node, config, moduleName, url) {
		console.log('module ' + moduleName + ' is about to be loaded');

		node.addEventListener('load', function () {
			console.log('module ' + moduleName + ' has been loaded');
		});

		node.addEventListener('error', function () {
			console.log('module ' + moduleName + ' could not be loaded');
		});
	}
});
console.log(require_paths)
require(require_obj,function(){
	alert('red)')
});