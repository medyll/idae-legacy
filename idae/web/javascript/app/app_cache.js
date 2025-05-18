/**
 * Created by lebru_000 on 22/03/15.
 */

if(!window.app_cache_loader) window.app_cache_loader = {};

document.addEventListener('DOMContentLoaded', function() {
	// code

})
window.document.observe('dom:loaded', function () {

})

window.app_cache  = localforage.createInstance({
	name: "app_cache"
});

function app_cache_reset  (){
	if(confirm('Vider le cache d\'application ?')){
		var SESSID = localStorage.getItem('SESSID');
		var IDAGENT = localStorage.getItem('IDAGENT');
		var APPID = localStorage.getItem('APPID');
		var PHPSESSID = localStorage.getItem('PHPSESSID');
		var SSSAVEPATH = localStorage.getItem('SSSAVEPATH');

		localStorage.clear();
		window.localforage.clear();
		window.app_cache.clear();

		localStorage.setItem('SESSID',SESSID);
		localStorage.setItem('IDAGENT',IDAGENT);
		localStorage.setItem('APPID',APPID);
		localStorage.setItem('PHPSESSID',PHPSESSID);
		localStorage.setItem('SSSAVEPATH',SSSAVEPATH);
		//
	}

}
function  build_cache_key(file, davars) {
    if(file=='') return '';
	var filekey = clean_string(file|| 'undefined'),
		vars = davars || '',
		key_vars = 'vars_' + vars;

	var key_name = 'app_cache.' + filekey + '.' + key_vars;
  // console.log(md5(key_name))
	return key_name;
}
function   unstream_from_store  (table,table_value){
    indexed(table).create(function(err,res) {});
    indexed(table).delete({table_value:table_value},function(err,res){})
}