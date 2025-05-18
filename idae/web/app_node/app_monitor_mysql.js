// t_site : c_nom_site 
// t_siteorg
// t_solserv_clt : c_code_solservclt
// 
// client : t_partie => N_ID 19118 vars table t_cptcpta => N_COMPTABILISETRANSACTIONS_PARTIE_ID
// 
// C0000322




var app                 = require('http').createServer(handler),
    io                  = require('socket.io').listen(app),
    fs                  = require('fs'),
    qs                  = require('qs'),
    mysql               = require('mysql'),
    request             = require('request'), 
    connectionsArray    = [],
    connection          = mysql.createConnection({
        host        : '192.168.11.57', 
        user        : 'guest',
        password    : 'ArtisWeb06',
        database    : 'artisdb_leasys_prod', 
        port        : 3306,
        insecureAuth : false
    }),
    POLLING_SYS_INTERVAL 	= 60000,
    POLLING_INTERVAL 	= 30000,
    pollingTimer,
    last_date 			= 'string',
    DOCUMENTDOMAIN 		= 'idaertys.mydde.fr';



 
connection.connect(function(err) {

});

// 
// app.listen(8000);

// 
function handler ( req, res ) {
    fs.readFile( __dirname + '/client.html' , function ( err, data ) {
        if ( err ) {
            console.log( err );
            res.writeHead(500);
            return res.end( 'Error loading client.html' );
        }
        res.writeHead( 200 );
        res.end( data );
    });
}

// t_site
// t_partie : attributions client
// verifier table : t_partie.N_ID  =  t_cptcpta.N_COMPTABILISETRANSACTIONS_PARTIE_ID  avec B_PRINCIPAL_CPTCOMPTABILITEAUXILIAIRE_IDX = 'Oui'  monitor T_COMPUTED_UPDATE_DATE
// t_partie.C_CODE_ORG = C0000322  => donnéees clients
// interventions techniques : t_interinfoprincipales
// sys_auditlogtable => tout est loggué ici ?  


var arr_date = [];
var vars  		= [];
var index ;

var query_loop = function(table){
	// should i take the last 10 ones ?
	var query = connection.query("SELECT *, DATE_FORMAT(T_UPDATE_DATE,'%d %b %Y %T:%f') as DADATE  FROM  " +table+ " order by T_UPDATE_DATE desc limit 80", function(err, rows) {
		if(!rows) return;		
		if(rows.length==0) return;		 
		  result_date = rows[0].DADATE;  
		  if( arr_date[table] != result_date ){
	         	console.log('send post for table '+table+' taille '+rows.length); 
		  		arr_date[table] = result_date;  

		  		vars['table'] = table;
		  		vars['rows']  = rows;
		  		 
		  		request.post({
				    uri:'http://'+DOCUMENTDOMAIN+'/services/json_data_monitor.php',
				    headers:{'content-type': 'application/x-www-form-urlencoded'},
				    body:  qs.stringify(vars)
			    },function(err,res,body){
				   	console.log('monitor mysql res => ',body); 
					//
			    });
	         	} 
			});
}
var pollingLoop = function () { 
    //  
   	for(var index_table in tables){
   		var nom_table  = tables[index_table];
   		index = index_table;
 		//
 		query_loop(nom_table);	
   	}

   	pollingTimer = setTimeout( function(){pollingLoop()}, POLLING_INTERVAL ); 
};

// pollingLoop();

var pollingSysLoop = function () { 
   //    
   	
   var query = connection.query("SELECT *, DATE_FORMAT(T_DATE_EVENT,'%d %b %Y %T:%f') as DADATE, DATE_FORMAT(T_DATE_EVENT,'%d %b %Y') as DA_DATE, DATE_FORMAT(T_DATE_EVENT,'%T') as DA_TIME  FROM sys_auditlogtable order by T_DATE_EVENT desc limit 1");
 
    query.on('error', function(err) { 
         
     })
     .on('result', function( row ) {  
    	 result_date = row.DADATE;   
         
         if( last_date != result_date ){
         	 console.log('Sys changesssss !!!',row.C_TYPE_IDX_PARENT); 
         	 last_date = result_date;  
         }        
     })
     .on('end',function(){       
         pollingTimer = setTimeout( function(){pollingSysLoop()}, POLLING_SYS_INTERVAL );        
     });
};


// pollingSysLoop();

request.post({
    uri:'http://'+DOCUMENTDOMAIN+'/services/json_data_artis_sync_list.php',
    headers:{'content-type': 'application/x-www-form-urlencoded'},
    body:  qs.stringify(vars)
},function(err,res,body){
    tables = JSON.parse(body);
    console.log(tables)
    pollingLoop();
    //
});