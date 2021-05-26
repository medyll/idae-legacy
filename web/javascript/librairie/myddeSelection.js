myddeSelection = {};
myddeSelection = Class.create();
myddeSelection.prototype = {
	initialize: function (element, options) {
		this.options = Object.extend({
			only: null,
			selectedClassName: 'selected'
		}, options || {});
		this.only = this.options.only;
		this.selectedClassName = this.options.selectedClassName;
		this.element = $(element);
		this.selecting = false;
		this.selectableElements = {};
		Event.observe(this.element, 'mousedown', this.onMouseDown.bindAsEventListener(this, this.element), false);
		Event.observe(document, 'mouseup', this.onMouseUp.bindAsEventListener(this, this.element), false);
		Event.observe(document, 'mousemove', this.onMouseMove.bindAsEventListener(this, this.element), false);
	},
	checkSelect: function () {
		this.selectableElements.each(function (node, index) {
			rect1 = node.getBoundingClientRect();
			rect2 = $('drag_selection').getBoundingClientRect();
			overlap = !(rect1.right < rect2.left ||
			rect1.left > rect2.right ||
			rect1.bottom < rect2.top ||
			rect1.top > rect2.bottom)
			if (overlap) {
				node.addClassName('selected');
				node.select('input[type=checkbox]').invoke('doCheck');
			} else {
				node.removeClassName('selected');
				node.select('input[type=checkbox]').invoke('doUnCheck');
			}
		}.bind(this));
	},
	onMouseMove: function (event) {
		var div = $('drag_selection');
		if (!div || !this.selecting) return;
		if (this.initSelect) {
			this.selectableElements.invoke('removeClassName', 'selected')
		}
		var mouse_pos = [Event.pointerX(event), Event.pointerY(event)];
		if (this.startX > mouse_pos[0] + 4 && this.startX > mouse_pos[0] + 4) {
			//code
		}
		if (this._last_pos && (this._last_pos.inspect() == mouse_pos.inspect())) return;
		this._last_pos = mouse_pos;
		if (mouse_pos[0] > div.startPos[0]) {
			div.setStyle({
				width: (mouse_pos[0] - div.startPos[0]) + 'px'
			});
		} else {
			div.setStyle({
				left: mouse_pos[0] + 'px',
				width: (div.startPos[0] - mouse_pos[0]) + 'px'
			});
		}
		if (mouse_pos[1] > div.startPos[1]) {
			div.setStyle({height: (mouse_pos[1] - div.startPos[1]) + 'px'});
		} else {
			div.setStyle({
				top: mouse_pos[1] + 'px',
				height: (div.startPos[1] - mouse_pos[1]) + 'px'
			});
		}
		var vp_pos = document.viewport.getScrollOffsets();
		var vp_size = document.viewport.getDimensions();
		if (mouse_pos[1] >= vp_pos[1] + vp_size[1] - 10) {
			window.scrollTo(0, vp_pos[1] + 15);
		} else if (mouse_pos[1] <= vp_pos[1] + 10) {
			window.scrollTo(0, vp_pos[1] - 15);
		}
		this.checkSelect();
	},
	onMouseDown: function (event) {
		el = Event.element(event);
		if($(el)!=this.element) return;
		if ($(el).tagName) {
			if ($(el).tagName == 'A') {
				// el.preventDefault();
				// Event.stop(el);
				return;
			}
			if ($(el).readAttribute('draggable') ) {
				// el.preventDefault();
				// Event.stop(el);
				return;
			}
		}
		if ($(el).match(this.only)) return;
		if ($(el).up().match(this.only)) return;
		if (event.findElement('input')) return;
		event.preventDefault();
		this.selecting = true;
		this.selectableElements = $$(this.only);
		console.log(this.only);
		console.log(this.selectableElements);
		this.startX = Event.pointerX;
		this.startY = Event.pointerY;
		var div = new Element('div', {id: 'drag_selection', 'className': 'drag_selection border4 fond_noir'});
		div.startPos = [Event.pointerX(event), Event.pointerY(event)];
		$('body').appendChild(div);
		div = $(div);
		$(div).setOpacity(0.6);
		$(div).setStyle({position: 'absolute', left: Event.pointerX(event) + 'px', top: Event.pointerY(event) + 'px'});
		this.element.fire('dom:unSelectionMade');
	},
	onMouseUp: function (event) {
		this.selecting = false;
		this._last_pos = null;
		this.initSelect = false;
		if (!$('drag_selection')) return;
		$(this.element).fire('dom:click');
		var div = $('drag_selection');
		div.remove();
		console.log(this.element.identify() + ' .' + this.selectedClassName);
		if ($$('#' + this.element.identify() + ' .' + this.selectedClassName).size() > 0) this.element.fire('dom:selectionMade');
	}
}