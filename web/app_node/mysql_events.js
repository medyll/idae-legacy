var MySQLEvents = require('mysql-events');
var dsn = {
  host:     '192.168.11.57',
  user:     'guest',
  password: 'ArtisWeb06',
};
var mysqlEventWatcher = MySQLEvents(dsn);
var watcher =mysqlEventWatcher.add(
  'artisdb_leasys_prod',
  function (oldRow, newRow) {
  	console.log(oldRow, newRow);
     //row inserted 
    if (oldRow === null) {
      //insert code goes here 
    }
 
     //row deleted 
    if (newRow === null) {
      //delete code goes here 
    }
 
     //row updated 
    if (oldRow !== null && newRow !== null) {
      //update code goes here 
    }
  }, 
  'match this string or regex'
);