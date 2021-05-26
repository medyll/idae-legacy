autoToggle = {};
autoToggle = Class.create();
autoToggle.prototype = {
				initialize: function(element , options){ 
					this.options = Object.extend({
						activeClass: ' ',
						inactiveClass:  ' ',
						activeWay: 'click'
					}, options || {}); 
					
					this.element=$(element);
                    this.element.cleanWhitespace();
					$(this.element).on('click','.autoToggle',this.isclicked.bind(this))
					return $(element);
				},
				isclicked: function(event,node){
					if(!Event.isLeftClick(event)) return;
					$(this.element).select('.autoToggle').invoke('removeClassName','active');
					if(this.element.hasClassName('toggler_visible'))$(node).unToggleContent()
					$(node).addClassName('active')
				} 
}
autoPush = {};
autoPush = Class.create();
autoPush.prototype = {
				initialize: function(element , options){ 
					this.options = Object.extend({
						activeClass: ' ',
						inactiveClass:  ' ',
						activeWay: 'click'
					}, options || {}); 
					
					this.element=$(element).cleanWhitespace();
					this.lastElemActive = null;
					this.activeElement = this.element.select('.active')[0] || null;
					this.activeClass = this.options.activeClass;
					this.inactiveClass =  this.options.inactiveClass;
					this.activeWay =this.options.activeWay ;
					this.childList = $(this.element).select('.autoToggle');
					this.setLastElement();
					this.parseElement();
					if(this.activeElement!=null){
							this.lastElemActive = this.activeElement 
					} 
					return element;
				},
				parseElement: function(){
					for(i=0;i <this.childList.length;i++){
						zz = this.childList[i]
						$(zz).observe(this.activeWay,function(element,event){ 
															  this.setClass(element)
															  }.bind(this,zz));
					}
				},
				setLastElement: function(){
					this.lastElemList = document.getElementsByClassName(this.activeClass,$(this.element));
					for(i=0;i <this.lastElemList.length;i++){
						this.lastElemActive = this.lastElemList[i]
					}
				},
				setClass: function(activeElement){  
					this.activeElement = activeElement;
					if(!$(activeElement).hasClassName('active')) { 
					$(activeElement).addClassName('active');
					if($(activeElement).select('input[type=checkbox]').first()){
						$(activeElement).select('input[type=checkbox]').first().checked= true
						$(activeElement).select('input[type=checkbox]').first().setAttribute({checked:'checked'})
						}
					return true;}
					if($(activeElement).hasClassName('active')) { $(activeElement).removeClassName('active');
					if($(activeElement).select('input[type=checkbox]').first()){
						$(activeElement).select('input[type=checkbox]').first().checked= false
						}
					return true;}
				}
}
