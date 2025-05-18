myddeSlide = Class.create({
    initialize: function(element,options) {
       this.options = {
      		duration: 10
    	}
		Object.extend(this.options, options || {});
		this.element = $(element);
		this.childElements = $(this.element).childElements();
		//this.childElements.invoke('hide');
		this.nbChild = this.childElements.size();
		this.childElements.first().show();
		this.activeElement = this.childElements.first(); 
		this.timer = 0 ;
		this.timerOver = 0 ;
		this.moving = '';
		this.effect = '';
		this.goon='';
		this.element.observe('click',this.clicked.bind(this));
		this.element.observe('mouseover',this.mouseover.bind(this));
		//	this.timeNext()	;
		this.direction = 'right';
		//
		$(this.element).setStyle({top:$(this.element).offsetTop+'px'});
		 
    },
	timeNext : function(){
		clearTimeout(this.timer);
		this.timer = setTimeout(function(){
			this.showNext(); 
			}.bind(this),eval(this.options.duration*1000))
	},
	clicked : function(event){
		node = Event.element(event); 
		if(node == document || node ==null) return;
		clearTimeout(this.timer);
		if($(node).hasClassName('next')) {
			this.direction='right';
			this.showNext();
			};
		if($(node).hasClassName('back')) { 
			this.direction='left';
			this.showNext();
			};
	},
	mouseover : function(event){
		node = Event.element(event); 
		if(node == document || node ==null) return;
		clearTimeout(this.timer);
		if($(event).pointerX() > eval(document.body.offsetWidth - 50) ) {
			this.direction='right';
			this.showNext();
			this.goon=true;
			return;
		}
		if($(event).pointerX() < 50 ) {
			this.direction='left';
			this.showNext();
			this.goon=true;
			return;
		} 
		this.goon=false;
		// this.effect.cancel();
	},
	mover: function(){
	//	if (this.moving == true) return;
		if (this.goon == false) return;
		this.moving = true 
		var delta = this.activeElement.offsetLeft/2; 
		//
		clearTimeout(this.timerOver);
		//this.timerOver = setTimeout(function(){
			this.effect = new Effect.Move ($(this.element),{ x: '-'+delta , y: $(this.element).offsetTop, afterFinish : function(){
				 this.moving = true 
				}.bind(this) , mode: 'absolute' , duration:0.2 , queue: 'end'});
		//	}.bind(this),250);
		
	},
	showNext: function(){  
		screenWidth = document.body.offsetWidth; 
		if(this.direction == 'right'){
			this.mover();
			//  &&  
			if(this.activeElement.next() &&  $(this.activeElement)!=this.activeElement.next() ){  
					this.activeElement = this.activeElement.next();
			}else{ 
				this.goon = false;
				return; 
			}
		}
		if(this.direction == 'left'){
			this.mover();
			if(this.activeElement.previous()){ 
				this.activeElement = this.activeElement.previous();
			}else{	 
				this.goon = false;
				return ; 
			}
		} 
		// this.timeNext();
	}
})

