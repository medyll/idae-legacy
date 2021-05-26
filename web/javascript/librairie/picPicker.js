picPicker = {};
picPicker = Class.create();
picPicker.prototype = {
				initialize:function(element,options){ 
					this.element = $(element)
					this.timer = 0;
					this.timerSaisie = 0;
					this.state = 0;
					this.firstLoad = false;
					this.oldValue = '';
					var mdlOption = { classButton:'default',module:'calendrier/calendrier',scope:'',vars:'',paramName:false,populate:true}
					this.options = Object.extend(mdlOption, options || {});  
					 
					$(this.element).writeAttribute({autocomplete:'off'})
					$(this.element).autocomplete = false;
					if($(this.element).readAttribute('vars')){
						this.options.vars = $(this.element).readAttribute('vars');
						}
					if($(this.element).readAttribute('scope')){
						this.options.scope = $(this.element).readAttribute('scope');
						}
					if($(this.element).readAttribute('paramName')){
						this.options.paramName = $(this.element).readAttribute('paramName');
						}else{
						this.options.paramName = $(this.element).readAttribute('name');	
						}
					if($(this.element).readAttribute('classButton')){
						this.options.classButton = $(this.element).readAttribute('classButton');
						}
					if($(this.element).readAttribute('target')){
						this.options.target = $(this.element).readAttribute('target');
						}
					if($(this.element).readAttribute('populate')){
						this.options.populate = true;
						}
					try{$(this.element).form.autoComplete = "off";}catch(e){}
					this.prepare();
				},
				prepare: function(){ 
					this.wrapper = new Element('div',{'class':'wrapper_holder','style':'display:inline-block;position:relative;'})
					this.button = new Element('input',{'tabindex':'-1','type':'button','class':this.options.classButton+' pickPickerButton','style':'display:inline-block;'})

					$(this.element).wrap(this.wrapper).insert({bottom: this.button});
					if(this.options.populate){ 
						//$(this.element).observe('mouseover',function(event){
							if(this.firstLoad==false)	this.emit();
							this.firstLoad = true;
							//}.bind(this)); 
						}
					$(this.element).observe('click',this.openCal.bind(this),false); 
					$(this.element).observe('keydown',this.validate.bind(this),true);
					$(this.element).observe('keyup',this.ajaxEventCal.bind(this),true);
					$(this.button).observe('click',this.openCal.bindAsEventListener(this),false);
					$(this.element).observe('blur',function(event){
											clearTimeout(this.timer);
										 	this.timer = setTimeout(function(){try{$(this.holder).hide();}catch(e){}}.bind(this),500) 
							}.bind(this));  
					return true;
				}, 
				buildElements:function(){
					if(!this.options.target){
						this.holder = new Element('div',{'class':'pickPicker','style':'display:none;position:absolute;min-width:'+$(this.element).getWidth()+'px'}) 
						$('body').appendChild(this.holder)
						$(this.holder).identify() ;
					}else{
						 this.holder = $(this.options.target)
					}
					$(this.holder).observe('mouseover',function(event){  
									if($(this.element).offsetHeight==0){$(this.holder).hide();} 
									}.bind(this));
					$(this.holder).observe('click',function(event){  
											$(this.element).focus();
											clearTimeout(this.timer);
											this.timer = setTimeout(function(){$(this.element).focus()}.bind(this),250) 
											$(this.element).focus(); 
											}.bind(this));
					$(this.holder).observe('dom:datechoosen',function(event){ 
											clearTimeout(this.timer);	  
											$(this.element).value=event.memo.value;
											$(this.holder).hide() 
												
												 
											// $(this.element).fire('dom:datechoosen',event.memo) 
											if($(this.wrapper).previous('.toPick')){
													if(event.memo.id){
														$(this.wrapper).previous('.toPick').value = event.memo.id 
													}
												}
											$(this.element).fire('dom:datechoosen',event.memo)
											}.bind(this));
					return true;
				},
				validate:function(event){  
					if (event.keyCode == Event.KEY_LEFT || event.keyCode == 37){ Event.stop(event);return false;}// Left Arrow 
					if (event.keyCode == Event.KEY_UP || event.keyCode == 38){ // Up Arrow 
					if(this.timer){ clearTimeout(this.timer) }
					if(this.timerSaisie){ clearTimeout(this.timerSaisie) }
						if($(this.holder).select('.autoToggle.active').size() ==0 ){
							$(this.holder).select('.autoToggle').last().addClassName('active');
						}else{
							previous = $(this.holder).select('.active').first().previous('.autoToggle');
							$(this.holder).select('.autoToggle.active').invoke('removeClassName','active')
							$(previous).addClassName('active');
						}
					Event.stop(event);return false;
					$(this.element).scrollTo();
					}
					if (event.keyCode == Event.KEY_RIGHT || event.keyCode == 39){ Event.stop(event);return false;} // Right Arrow 
					if (event.keyCode == Event.KEY_DOWN || event.keyCode == 40){ // Down Arrow 
					if(this.timer){ clearTimeout(this.timer) }
					if(this.timerSaisie){ clearTimeout(this.timerSaisie) }
						if($(this.holder).select('a.active').size() ==0 ){
							$(this.holder).select('a').first().addClassName('active')
						}else{
							next = $(this.holder).select('.active').first().next('.autoToggle'); 
							$(this.holder).select('.autoToggle.active').invoke('removeClassName','active')
							$(next).addClassName('active');
						}
					Event.stop(event);return false;
					$(this.element).scrollTo();
					} if (event.keyCode == 13) { // Enter (Open Item)
					func = $(this.holder).select('.active[onclick]').first().readAttribute('onclick') ;
					identi = $(this.holder).select('.active[onclick]').first().identify();
					eval(func.replace("this",identi));
 					Event.stop(event);return false;
					}
					return true;
				},
				openCal:function(){ 
					$(this.element).focus(); 
					if(this.timer){ clearTimeout(this.timer) }
					if (!$(this.holder)){ 
						 this.buildElements();
					}
					if(!this.options.target){
					this.holder.clonePosition(this.wrapper,{setWidth:false,setHeight:false}) 
					offset = this.element.viewportOffset()
					
					this.holder.setStyle({top: (offset.top +25)+'px'}) 
					}
					//Event.stop(event);
					this.holder.show().makeOnTop(); 
					this.timer = setTimeout(function(){$(this.element).focus()}.bind(this),500) 
					if($(this.holder).empty()){ this.ajaxCal(); }
					return true;
					 
					}, 
				ajaxEventCal:function(event){ 
					this.timer = setTimeout(function(){$(this.element).focus()}.bind(this),500) ; 
					 if (  event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40 || event.keyCode == 13) return false;
					this.ajaxCal(); 
					},
				ajaxCal:function(){ 
					if (!$(this.holder)){ 
						 this.buildElements();
					}
					this.state = 1;
					if($(this.element).readAttribute('vars')){
						this.options.vars = $(this.element).readAttribute('vars');
						}
					this.timer = setTimeout(function(){$(this.element).focus()}.bind(this),500)  
					//if($(this.holder).empty()){  
					if(this.oldValue!=$(this.element).value){
						this.emit();
						this.oldValue==$(this.element).value
						}
					},
				emit: function(){
					if (!$(this.holder)){ 
						 this.buildElements();
					}
					vars = this.options.vars;
					if(this.options.paramName){vars = vars + '&'+this.options.paramName+'='+$(this.element).value}
					// console.log(vars)
					if(this.timer){ clearTimeout(this.timer) }
					if(this.timerSaisie){ clearTimeout(this.timerSaisie) }
					if( typeof socket  ==  'object'){
						this.timerSaisie = setTimeout(function(){
							vars = 'date='+$(this.element).value+'&calendarId='+$(this.holder).id+'&'+vars;
							$(this.holder).loadModule(this.options.module,vars,{scope:this.options.scope,value:$(this.holder).id})
						}.bind(this),500) 
					}else{
						this.timerSaisie = setTimeout(function(){
							ajaxInMdl(this.options.module,$(this.holder),'date='+$(this.element).value+'&calendarId='+$(this.holder).id+'&'+vars,{scope:this.options.scope,value:$(this.holder).id})
						}.bind(this),500) 
					}					
				}
}