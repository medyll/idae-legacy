/**
 * Created by Mydde on 16/04/15.
 */

socket.on('socketModule', function (data) {
	 
	// reception module html
	var options = data.out.options || {};
	var data_body   = data.body || '' ;
	this.out = data.out;
	var data_raw = data.out;
	// extraire : file / vars / table / table_value
	var objDta = {
		file : data_raw.file,
		key : filekey = clean_string(data_raw.vars),
		vars : data_raw.vars,
		body: data_body,
		raw_body : data.body
	};
	 
	if(options.request_id){ 
		// console.log(window.app_cache_loader[options.request_id]);
	}
	data_raw.vars = data_raw.vars || '';
	var arr_inspect_vars = parse_str(data_raw.vars) || {};
	if(arr_inspect_vars.table){
		objDta.table = arr_inspect_vars.table;
	}
	if(arr_inspect_vars.table_value){
		objDta.table_value = arr_inspect_vars.table_value;
	}
	// console.log((data_body== window.app_cache_loader[]))
	//
	$(this.out.element).writeAttribute('data-request_id','ya plus bro');

	// on sauvegarde dans indexed_db ; app_data_cache
	window.indexed('app_data_cache').create(function(err,res) {
	// console.log('app_data_cache created ',this.out.file,err,res)
	}.bind(this));
	// delete and insert
	window.indexed('app_data_cache').delete({file:this.out.file,vars:data_raw.vars},function(err,res){
		// console.log('inexedDb delete',res);
		// console.log('data_raw  ',data_raw.vars);
		//
		window.indexed('app_data_cache').insert(objDta,function(res){
			// console.log('inexedDb insert ok => ',res);
		});
	}.bind(this));

	//
	if (!$(this.out.element)) {

		return false;
	}
	// alert('red)')

}.bind(this));