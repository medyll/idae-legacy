/**
 * Created by Mydde on 07/09/2015.
 */

/**
 * Panel Plugin
 *
 * Adds basic demonstration functionality to .ms-Panel components.
 *
 * @param  {jQuery Object}  One or more .ms-Panel components
 * @return {jQuery Object}  The same components (allows for chaining)
 */

//
Panel = {};
Panel = Class.create();
Panel.prototype = {
	initialize:function(element,options){
		this.element = $(element)
		this.element.cleanWhitespace();
		this.element_main = this.element.select(".ms-Panel-main").first();

		/** Hook to open the panel. */
		$(body).on("click",".js-togglePanel", function() {
			// Panel must be set to display "block" in order for animations to render
			this.element_main.setStyle({display: "block"});
			this.element.toggleClassName("is-open");
		}.bind(this));

		this.element.on("animationend", function(event) {

			if (event.animationName === "fadeOut") {
				// Hide and Prevent ms-Panel-main from being interactive
				this.element_main.setStyle({display: "none"});
			}
		}.bind(this));
	}
}
//
insertionQ('.ms-Panel').every(function (element) {
	if ($(element).readAttribute('masked') == null) {
		new Panel(element);
		$(element).writeAttribute('masked', 'true');
	}
});