var niceForm = {}
niceForm = Class.create();
niceForm.prototype = {
	initialize: function(element){
		this.element = $(element)
		this.formName = (this.element.form)? this.element.form.id : ''
		if($("surr_"+this.formName+$(this.element).name)) return
		this.newName = "surr_"+ this.formName +$(this.element).name 
		this.build();
	},
	build: function(){ 
		new Insertion.Before(this.element,'<span id="'+this.newName+'" class="zolliformlabel" ></span>'); 
		if(this.element.style.width){ 
			if(this.element.style.width=='100%'){ 
				this.element.style.width = '100%'
				}else{
				this.element.style.width = parseInt(this.element.style.width)- 4 + 'px'
				}
			}
		if(this.element.offsetWidth){
				this.element.style.width = parseInt(this.element.offsetWidth)- 4 + 'px'
				} 
		$(this.newName).appendChild(this.element);
		Element[Element.visible(this.element) ? 'show' : 'hide']($(this.newName)) 
		this.element.observe('DOMAttrModified', function(){ 
					Element[Element.visible(this.element) ? 'show' : 'hide']($(this.newName)) 
 					 }.bind(this,this.newName),true)
	}
}