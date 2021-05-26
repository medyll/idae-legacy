URLToArray = function (url) {
  var request = {};
  var pairs = url.split('&');
  for (var i = 0; i < pairs.length; i++) {
    var pair = pairs[i].split('=');
    request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
  }
  return request;
}
reloadScope = function(scope,value,pars,newValue,options){ 
		var newValue = newValue || value  
		 
		$$('[scope="'+scope+'"]').each(function(theElem){ 
		var node = theElem; 
			if(node.readAttribute('value')==value || value == '*'){  
				//  
				options = Object.extend({
					oneLoad: false,
					seeLoading: true,
					unset_key : [],
					force: false
				}, options || {});
				//

				if(options.oneLoad == true && $(node).childElements().size()!=0) return;
				//
				newvars = node.readAttribute('vars')+'&'+(pars || '') ;
				if(options.force){newvars=options.force;}
				qy = URLToArray(newvars);

				if(options.unset_key.size()!=0) {
					options.unset_key.forEach(function(key_name,i){
						 if(qy[key_name]  ){qy[key_name]=null; delete qy[key_name]; }
						 if(qy.vars && qy.vars[key_name]){console.log('unset trouvé',key_name)}
					});
				}
				 
				qy.defer = null;qy.emptyModule=null; delete qy.defer; delete qy.emptyModule;
				$(node).writeAttribute({vars: $H(qy).toQueryString() }); 
				module =  node.readAttribute('mdl')
				//
				 
				if( typeof socket  ==  'object'){
					$(node).socketModule(module,$H(qy).toQueryString(),options);  
					return $(node);
				}
				// fallback
				new Ajax.Updater($(node), changeCnameTrick()+'mdl/'+node.getAttribute('mdl')+'.php', {
						parameters: qy ,
						method: 'post',  
						evalScripts: true,
						requestHeaders: ['Content-type', 'application/x-www-form-urlencoded','charset','UTF-8']
					});
				 
			}
		})
	}
reloadModule = function(module,value,pars,newValue,options){ 
		// var newValue = newValue || value  
		
		 
		$$('[mdl="'+module+'"]').each(function(theElem){ 
		var node = theElem; 
			if(node.readAttribute('value')==value || value == '*'){  
				//  
				//options.value = newValue || node.readAttribute('value') 
				
				options = Object.extend({
					oneLoad: false,
					seeLoading: true
				}, options || {});
				//
				if(options.oneLoad == true && $(node).childElements().size()!=0) return;
				//	
				var aars = pars || node.readAttribute('vars')
				
				var qy = aars.toQueryParams()
				// var qy = Object.isString(aars) ? aars : Object.toQueryString(aars);
		  
				$(node).cleanWhitespace(); 
				qy.defer = null;qy.emptyModule=null;
				qy.module= module;
				$(node).writeAttribute({vars: $H(qy).toQueryString() }); 
				if(newValue) $(node).writeAttribute({value: newValue }); 
				//
				if( typeof socket  ==  'object'){
					$(node).socketModule(module,aars,options);
				}else{
					new Ajax.Updater($(node), changeCnameTrick()+'mdl/'+node.getAttribute('mdl')+'.php', {
						parameters: qy ,
						method: 'post',
						evalScripts: true,
						requestHeaders: ['Content-type', 'application/x-www-form-urlencoded','charset','UTF-8']
					});

				}
				//
				setTimeout(function(){
					// console.log('reloadModule par ajax !!! ')
				}.bind(this),250)
				 
			}
		})
	}
closeModule = function(module,value,pars,newValue){
		var newValue = newValue || value
		$$('[mdl="'+module+'"]').each(function(node){
			if(node.getAttribute('value')==value){
				aars = pars || node.getAttribute('vars')
				try{$(node).close()}catch(e){$(node).remove()}
			}
		})
	}
newValueModule = function(){
	var aa = new Date();
	newValue = 'moduleValue_'+aa.getTime();
	return newValue;
	}