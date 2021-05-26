var resizeGui = {};
resizeGui = Class.create();
resizeGui.prototype = {
	initialize : function(element,prevElement,nextElement,options){
			//console.log(element)
			this.element  = $(element);
			
			this.lastValue = 0 ;
			this.index = 0;
			this.firstTimer = 0;;
			this.options = Object.extend({
				layout: 'horizontal' ,
				revert: false,
				defaultStart: '30%',
				visibility: 'visible',
				minwidth:'5',
				cookieName: '["resizeGui"]["'+this.element.id+'"]'
			}, options || {});
			 
			
			this.arrArrow = new Array(); 
			this.init(); 
	},
	makeHidden: function(){
		$(this.element).hide() 
		},
	makeArrowV: function(){
		this.arrow = new Element('div');
		//this.arrow.setStyle(this.arrowVcss);
		this.arrow.className="vResizeArrow"
		this.element.appendChild(this.arrow) 
	},
	init: function(){
		$(this.element).childElements().each(function(node,index){
			this.assignArrow(node,index);			
			}.bind(this));
		var genTimer;
		$('body').observe('content:loaded',function(){
			clearTimeout(genTimer)
			var gentimer = setTimeout(function(){$$('.vResizeArrow').invoke('fire','dom:resize');}.bind(this),500)
			 
			
			});
	},
	assignArrow: function(node,index){
		var arrow = new Element('div',{className:'vResizeArrow'}); 
		var arrowIndex = index;
		var left = eval($(node).offsetLeft) + eval( $(node).offsetWidth) + 'px' 
		  
		if(this.options.layout=='vertical'){
			cursor = 'NS-resize';
		}
		if(this.options.layout=='horizontal'){
			cursor = 'EW-resize';
		}
		$(arrow).setStyle({left:left,cursor:cursor});
		this.element.appendChild(arrow) ;
		this.arrArrow[index]= node;
		$(arrow).observe('dom:resize',function(){
			$(arrow).setStyle({left:left});
			tmp = this.arrArrow[arrowIndex];
			left =  eval($(tmp).offsetLeft) + eval( $(tmp).offsetWidth) + 'px' 
			$(arrow).setStyle({left:left});
			}.bind(this));
		this.associate(node,arrow);
	},
	associate: function(node,arrow){
		var dragObj = new Draggable(arrow,{
									 	constraint: this.options.layout, revert: this.options.revert,
										starteffect: function(){this.start(arrow)}.bind(this),
										change: function(){this.doing(node,arrow)}.bind(this),
										endeffect:function(){this.end(arrow)}.bind(this),
										snap: function(x,y,draggable) {
											  function constrain(n, lower, upper) {
												if (n > upper) return upper;
												else if (n < lower) return lower;
												else return n;
											  }
											 
											  element_dimensions = Element.getDimensions(draggable.element);
											  parent_dimensions = Element.getDimensions(draggable.element.parentNode);
											  return[
												constrain(x, 0, parent_dimensions.width - element_dimensions.width),
												constrain(y, 0, parent_dimensions.height - element_dimensions.height)];
											}
										});
		$$('.vResizeArrow').invoke('fire','dom:resize'); 
	},
	doing: function(node,arrow){
		if(this.options.layout=='vertical'){
			/*newTop =  parseInt(this.element.style.top) - ((this.prevElement.previous())  ?  this.prevElement.previous().offsetTop + this.element.offsetHeight : 0)
			this.prevElement.style.height = newTop  + 'px';
			if(this.nextElement) this.nextElement.style.marginTop =   this.element.offsetHeight + 'px'	 
			if(this.nextElement.next()) this.nextElement.style.height =   parseInt(this.nextElement.next().style.top) - this.element.offsetTop - this.nextElement.next().offsetHeight + 'px'	*/ 
			}
		if(this.options.layout=='horizontal'){
			if($(arrow).previous('.vResizeArrow')){
				if(parseInt(arrow.offsetLeft)<parseInt($(arrow).previous('.vResizeArrow').offsetLeft)+1){
					$(arrow).setStyle({left:parseInt($(arrow).previous('.vResizeArrow').offsetLeft)+this.options.minwidth+'px'});
					}
				}
			if($(arrow).next('.vResizeArrow')){
				if(parseInt(arrow.offsetLeft)>parseInt($(arrow).next('.vResizeArrow').offsetLeft)-1){
					$(arrow).setStyle({left:parseInt($(arrow).next('.vResizeArrow').offsetLeft)-this.options.minwidth+'px'});
					}
				}
			if($(node)){
				newW =  parseInt(arrow.offsetLeft) - parseInt(node.offsetLeft)  + 'px' 
				clearTimeout(this.firstTimer);
				this.firstTimer = setTimeout(function(){$(node).setStyle({width: newW });}.bind(this),50)
				}
			/*if($(node).next()){
				newW =  parseInt(arrow.offsetLeft) - parseInt(node.offsetLeft)  + 'px' 
				clearTimeout(a);
				var a = setTimeout(function(){$(node).setStyle({width: newW });}.bind(this),50)
				}*/
		clearTimeout(secondTimer);
		var secondTimer = setTimeout(function(){$$('.vResizeArrow').without(arrow).invoke('fire','dom:resize'); }.bind(this),200)
			
		}
	}, 
	start: function(arrow){ 
		$(arrow).addClassName('selected')
	},
	end: function(arrow){
		//this.element.makePositioned();
		$(arrow).removeClassName('selected')
		$$('.vResizeArrow').invoke('fire','dom:resize'); 
		
		//this.lastValue = (this.options.layout=='horizontal')? this.element.offsetLeft : parseInt(this.element.style.top) ;
		
		//this.putCookie();
	}, 
	chain: function(){
		this.doing();this.end()
	}
}