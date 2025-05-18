

var app                 = require('http'), 
    fs                  = require('fs'),
    qs                  = require('qs'), 
    request             = require('request'), 
    DOCUMENTDOMAIN 		= 'idaertys.mydde.fr'; 

		  		
 var sender = setInterval(function () {
	
request.post({
    uri:'http://'+DOCUMENTDOMAIN+'/services/json_data_pool.php',
    headers:{'content-type': 'application/x-www-form-urlencoded'},
    body:  'tololo=talala'
},function(err,res,body){
	console.log('end ' , body);
	});        

}, 30000);

 