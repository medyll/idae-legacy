/**
 * Created by lebru_000 on 17/01/16.
 */
var app_draggable = {};
app_draggable = Class.create();
app_draggable.prototype = {
	initialize   : function (element,options) {
		this.options = Object.extend({}, options || {});
		this.element = $(element);
		$(this.element).writeAttribute('draggable','true');
		var draggable = $(this.element);
		var handle = this.element.select('.handle').first() || this.element;
		var target = false;
		draggable.onmousedown = function(e) { target = e.target; };
		draggable.ondragstart = function(e) {
			if (handle.contains(target)) {
				e.dataTransfer.setData('text/plain', 'handle');
			} else {
				e.preventDefault();
			}
		};
	}
}
 