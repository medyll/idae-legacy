var tableGui = {}
tableGui = Class.create();
tableGui.prototype = {
	initialize: function(element,options){
		this.options = Object.extend({
		numCol: null,
		numRow: null,
		onlyClass: null
		}, options || {}); 
		
		this.element = $(element);
		
		if(this.options.onlyClass==null){
			this.allChild = $A(this.element.childNodes)
		}else{ 
			this.allChild = $A( document.getElementsByClassName(this.options.onlyClass,this.element))
			}
			
		$(this.element).setOpacity(0);	
		try{$(this.element).show();}catch(e){ } 	
		if(this.options.numCol){
			this.defaultColWidth = parseInt($(this.element).parentNode.offsetWidth / this.options.numCol)
			}
		if(this.options.numRow){
			this.defaultRowHeight = parseInt($(this.element).parentNode.offsetHeight / this.options.numRow)
			}
		
		this.build();
		var timer 
		this.element.parentNode.observe('Resize',function(){
										if(timer){clearTimeout(timer)}
										 timer =   setTimeout(function(){
																	  /* console.log('resize!!');
																	   if(this.options.numCol){
																			this.defaultColWidth = parseInt($(this.element).parentNode.offsetWidth / this.options.numCol)
																			}
																		if(this.options.numRow){
																			this.defaultRowHeight = parseInt($(this.element).parentNode.offsetHeight / this.options.numRow)
																			}
																	   this.reBuild()*/
																	   }.bind(this),1000)
										   }.bind(this),true)
	},
	build:function(){
		this.allChild.each(function(node,index){
										if(this.options.numCol){
											$(node).style.width = this.defaultColWidth + 'px'
										}
										if(this.options.numRow){
											$(node).style.height = this.defaultRowHeight + 'px' 
										}
									}.bind(this));
		this.allChild.each(function(node,index){
									try{Effect.Appear($(node),{duration: 0.2,position: 'front'}); }catch(e){} 
									});
		 Effect.Appear($(this.element),{duration: 0.1})
		/* console.log(this.element ,this.element.up() )*/
		if(this.element.scrollHeight > this.element.up().offsetHeight){
			//this.element.up().style.height = this.element.offsetHeight +'px'
			}
		//$(this.element).setOpacity(1);
	},
	reBuild:function(){
		this.allChild.each(function(node,index){
										if(this.options.numCol){
											$(node).style.width = this.defaultColWidth + 'px'
										}
										if(this.options.numRow){
											$(node).style.height = this.defaultRowHeight + 'px' 
										}
									}.bind(this));
	}
}