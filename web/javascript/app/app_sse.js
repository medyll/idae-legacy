app_sse = {};
app_sse = Class.create();
app_sse.prototype = {
				initialize: function(){
					this.timer = 0;
					this.run();
				},
				run: function(event,node){

					var source = new EventSource('services/json_data_event.php');

					source.addEventListener('act_upd_data', function(event){
						//
						var data = JSON.parse(event.data);
						if(!data.msg) return;
						if(data.msg.varsData_activity){
							new_data =[];
							new_data.table = data.msg.table;
							new_data.table_value = data.msg.table_value;
							new_data.vars = data.msg.varsData_activity;
							//
							console.log('sse for '+new_data.table+' ' + data.msg.table_value)
							act_upd_data(new_data);
						}
					});
					source.addEventListener('act_add_data', function(event){
						//
						var data = JSON.parse(event.data);
						if(!data.msg) return;
						var msg = data.msg;
						if(msg.dataData_activity){
							new_data =[];
							new_data.table = msg.table;
							new_data.table_value = msg.table_value || '';
							//
							act_add_data(new_data);
						}


					});

					source.addEventListener('message', function(event) {
						console.log(event.data);
						return;
						var data = JSON.parse(event.data);
						var d = new Date(data.msg * 1e3);
						var timeStr = [d.getHours(), d.getMinutes(), d.getSeconds()].join(':');

						console.log('last_event: ' + event.lastEventId +  ', data : ', data);
					}, false);
					//
					source.addEventListener('open', function(event) {
						clearTimeout(this.timer)
						console.log('> Connection was opened');
						//

					}, false);
					source.addEventListener('error', function(event) {
						if (event.eventPhase == 2) { //EventSource.CLOSED
							console.log('> Connection was closed');
							//setTimeout(function(){ new app_sse(); delete this; }.bind(this),1250);
							// new app_sse();
						} 
					}, false);
				} 
}
