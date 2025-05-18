myddeview = {};
myddeview = Class.create(); 
myddeview.prototype = {
	initialize: function(element, options){
		// options
		this.options = Object.extend({
			only : null
		}, options || {});
		this.element = $(element);
		// console.log('myddeview',$(element))
		// listener sur composants
		this.className = this.options.only 
		this.element.on('click',this.className,function(event,elem){this.selectableClicked(event,elem)}.bind(this))
		/*$$(this.className).each(function(elem){
			elem.observe('click',this.selectableClicked.bindAsEventListener(this))		
		},this)*/
	},
	selectableClicked: function(event,elem){  
		// var elem = element;//Event.element(event)
		//if(!elem.hasClassName(this.className)) elem=elem.up(this.className) 
		if (event.shiftKey){  
			// le 1er elem selectionné  
			var firstElem = this.element.select(this.className+'[bugchk=bugchk]').without(elem).first();//$$(this.className).indexOf($$(this.className+'.selected').first());
			// console.log(firstElem);
			var clickElemIndex = $(this.element).select(this.className).indexOf(elem);
			var firstElemIndex = $(this.element).select(this.className).indexOf(firstElem) ;  
			//  
			 
			
			if(clickElemIndex>firstElemIndex){  
				for(var i=firstElemIndex; i<clickElemIndex; i++){   
						var index = eval(i)  
						if($(this.element).select(this.className)[index]){
							$(this.element).select(this.className)[index].up('tr').addClassName('selected')
							var ch = $(this.element).select(this.className)[index].up('tr').select('input[type=checkbox]').first();
							$(ch).writeAttribute('bugchk','bugchk') 
							//$(ch).writeAttribute('checked','checked') 
							$(ch).checked = true;
						}
					}
			}
			
			if(clickElemIndex<firstElemIndex){  
				for(var i=clickElemIndex; i<firstElemIndex+1; i++){  
					var index = eval(i) 
					$(this.element).select(this.className)[index].up('tr').addClassName('selected')
					var ch = $(this.element).select(this.className)[index].up('tr').select('input[type=checkbox]').first();
					$(ch).writeAttribute('bugchk','bugchk') 
					//$(ch).writeAttribute('checked','checked') 
					$(ch).checked = true;
					}
			}
			 
			if(clickElemIndex==firstElemIndex){ $(elem).toggleClassName('selected')}
			this.hasSelection();
			var selectObj = window.getSelection();
			selectObj.collapseToStart();
			//Event.stop(event);
			return true;	
		} 
		if (event.metaKey){ 
			Event.stop(event);
			$(elem).toggleClassName('selected');
			this.hasSelection();
			return true;
		}
			
		/*$$(this.className).without(elem).invoke('removeClassName','selected');
		elem.toggleClassName('selected');
		this.hasSelection();*/
		
	},
	hasSelection: function(){ 
		size = $$('#'+this.element.identify()+' .selected').size()
		if(size==0){this.element.fire('dom:unSelectionMade');}
		if(size!=0){this.element.fire('dom:selectionMade');}	
	}
}