/**
 * Created by Mydde on 12/10/2015.
 */
window.data_subscribe = [];
/*if( typeof (insertionQ)!='object'){
 setTimeout(function () {
 init_q_data_live()
 }.bind(this),1000)
 }else{
 init_q_data_live()
 }
 init_q_data_live = function(){
 insertionQ('[data-table]').every(function (node) {
 window.data_subscribe[node.readAttribute('data-table')]=1;

 });

 console.log('init_q_data_live '  )
 }*/
var timer_upd_data = [];
var throttle_delay = 100, throttle_queue = [], throttle_timer = false, throttle_elem, throttle_trigger_size = 10;

throttling         = function (data, fn) {
	// console.log('throttling ', data, throttle_queue, throttle_queue.size());

	if ( data ) {
		throttle_queue.push (data);
		// console.log('throttle_queue.push',throttle_queue.size())
	}
	if ( !throttling_reached () && data ) {
		act_upd_data (data, true);
		return true;
	}
	;
	throttling_run ();

}
throttling_reached = function () {
	return (throttle_queue.size () >= throttle_trigger_size)
}
throttling_run     = function () {

	if ( !throttle_timer ) {
		//console.clear();
		//console.log('ini timer')
		throttle_timer = setInterval (function () {
			throttling_shift ()
		}.bind (this), throttle_delay);
	}

}
throttling_shift   = function () {

	throttle_elem = throttle_queue[0];
	// console.log('size before shift',throttle_queue.size())
	throttle_queue.shift ();
	// console.log('size after shift',throttle_queue.size())
	act_upd_data (throttle_elem, true);

	// console.log(throttle_timer)
	if ( throttle_queue.size () == 0 ) {
		clearInterval (throttle_timer);
		throttle_timer = false;
	}
}

act_upd_data = function (data, from_throttle) {
	var table_value = data.table_value;
	var table       = data.table;
	var Table       = ucfirst (table);
	var vars        = data.vars || [];

	// console.log(' => act_upd_data ', table, vars, data);

	if ( !from_throttle ) {
		throttling (data);
		return false;
	}

	if ( !window.data_subscribe[table] ) {
		// console.log ('refus data_live ' + table);
		return;
	}
	if ( $$ ('[data-table=' + table + ']') ) {
		// console.log('mise à jour data_live '+table,vars);
		/*clearTimeout(timer_upd_data[table + table_value]);
		 timer_upd_data[table + table_value] = setTimeout(function () {
		 for (var property in vars) {
		 if (vars.hasOwnProperty(property)) {
		 $$('[data-table=' + table + '][data-table_value=' + table_value + '] [data-field_name=' + property + ']').invoke('update', vars[property]);
		 }
		 }
		 }.bind(this), 5)*/
		for (var property in vars) {
			if ( vars.hasOwnProperty (property) ) {
				$$ ('[data-table=' + table + '][data-table_value=' + table_value + '] [data-field_name=' + property + ']').invoke ('update', vars[property]);
			}
		}

		clearTimeout (timer_upd_data[table]);
		timer_upd_data[table] = setTimeout (function () {
			$$ ('[data-table=' + table + '][data-uniqid]').invoke ('fire', 'dom:data_reload', vars);
		}.bind (this), 100);

		switch (table) {
			case 'tache':
				if ( vars['dateDebut' + Table] || vars['heureDebut' + table] ) {
					var dateDebut  = vars['dateDebut' + Table],
					    heureDebut = vars['heureDebut' + Table],
					    heureFin   = vars['heureFin' + Table];

					// console.log(Table,table_value,$$('[data-table=' + table + '][data-table_value=' + table_value + ']'))
					$$ ('[data-table=' + table + '][data-table_value=' + table_value + ']').each (function (tache_node) {

						if ( $ (tache_node).readAttribute ('data-parent') ) {
							tache_node.setAttribute ('data-dragtache', 'tache');
							tache_node.setAttribute ('data-dateDebut', dateDebut);
							tache_node.setAttribute ('data-heureDebut', heureDebut);
							var tache_parent = $ ($ (tache_node).readAttribute ('data-parent'));
							if ( $ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="' + heureDebut + '"]').size () != 0 ) {
								$ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="' + heureDebut + '"]').each (function (node_parent) {
									$ (node_parent).appendChild (tache_node);
								}.bind (this));
							} else if ( $ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="AM"]').size () != 0 ) {
								$ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"][heuredebut="AM"]').each (function (node_parent) {
									$ (node_parent).appendChild (tache_node);
								}.bind (this))
							} else if ( $ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]').size () != 0 ) {
								$ (tache_parent).select ('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]').each (function (node_parent) {
									$ (node_parent).appendChild (tache_node);
								}.bind (this));
							} else {
								$ (tache_node).remove ();
							}
							$ (tache_node).fire ('dom:resizetache');
						}
					}.bind (this))
				}
				break;
			case 'conge':

				if ( vars['dateDebut' + Table] || vars['heureDebut' + table] || vars['dateFin' + Table] || vars['heureFin' + table] ) {

					$$ ('[data-table=' + table + '][data-table_value=' + table_value + ']').each (function (tache_node) {
						if ( $ (tache_node).readAttribute ('data-parent') ) {
							var data_parent = $ ($ (tache_node).readAttribute ('data-parent'));
							if ( !$ (data_parent) ) return;
							var attr = {}
							if ( vars['dateDebut'+ Table] ) attr['data-datedebut'] = vars['dateDebut' + Table];
							if ( vars['heureDebut'+ Table] ) attr['data-heuredebut'] = vars['heureDebut' + Table];
							if ( vars['dateFin'+ Table] ) attr['data-datefin'] = vars['dateFin' + Table];
							if ( vars['heureFin'+ Table] ) attr['data-heurefin'] = vars['heureFin' + Table];
							if ( vars['duree'+ Table] ) attr['data-duree'] = vars['duree' + Table];

							tache_node.writeAttribute (attr);

							$(data_parent).appendChild(tache_node);


						}
					})
				}
				break;
		}

	} else {
		console.log ('desinscription data_live ' + table);
		delete window.data_subscribe[table];
	}
}

act_update_mdl = function (data) {
	// mdl,value,data
	var mdl = data.mdl, value = data.value, html = data.html;

	if ( !window.timer_stream_upd_mdl ) window.timer_stream_upd_mdl = [];
	if ( $$ ('[mdl="' + mdl + '"][value="' + value + '"]') ) {
		console.log (data);
		//
		if ( window.timer_stream_upd_mdl[mdl + '_' + value] ) clearTimeout (window.timer_stream_upd_mdl[mdl + '_' + value])
		window.timer_stream_upd_mdl[mdl + '_' + value] = setTimeout (function () {
			//
			$$ ('[mdl="' + mdl + '"][value="' + value + '"]').invoke ('update', html);
		}.bind (this), 0)
	}
}
act_add_data   = function (data) {
	var table = data.table;

	if ( !window.timer_stream_add_data ) window.timer_stream_add_data = [];
	if ( $$ ('[data-table=' + table + '][data-uniqid]') ) {
		//
		if ( window.timer_stream_add_data[table] ) clearTimeout (window.timer_stream_add_data[table])
		window.timer_stream_add_data[table] = setTimeout (function () {
			//
			$$ ('[data-table=' + table + '][data-uniqid]').invoke ('fire', 'dom:data_reload', data);
		}.bind (this), 0)
	}
}

act_close_mdl = function (data) {
	var vars = data.vars;
	var id   = 'id' + vars.table;
	if ( document.body.querySelector ('[scope=' + id + ']') ) {
		$$ ('[scope=' + id + '][value=' + vars.table_value + ']').invoke ('fire', 'dom:close');
		$$ ('[scope=' + id + '][value=' + vars.table_value + ']').invoke ('remove');
	}
	$$ ('[data-table=' + vars.table + '] [data-table_value=' + vars.table_value + ']').invoke ('remove');
}