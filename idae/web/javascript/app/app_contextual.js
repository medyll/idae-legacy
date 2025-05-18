/**
 * Created by lebru_000 on 06/11/2015.
 */
var app_context;
app_context = Class.create();
app_context.prototype = {
	initialize: function (options) {
		this.options = Object.extend({}, options || {});
		this.build();
	},
	build: function () {
		this.element = new Element('div');
		this.element.id = 'app_contextual_menu';
		this.element.addClassName('contextmenu');
		this.element.setAttribute('data-cache','true');
		$(document.body).insert(this.element);
		this.hideMenu();
		$(document.body).on('contextmenu', '[data-contextual]', this.onContextMenu.bind(this));
		this.element.observe('content:loaded', this.repositionMenu.bind(this))
	},
	showMenu: function (x, y) {
		this.element.style.left = x + 'px';
		this.element.style.top = y + 'px';
		this.element.show();
	},

	hideMenu: function () {
		this.element.hide();
	},

	onContextMenu: function (e, node) {
		e.preventDefault();
		document.addEventListener('click', this.onClick.bind(this), false);
		var vars = node.readAttribute('data-contextual');
		var file = 'app/app_contextual/app_contextual';
		node.addClassName('right_clicked');

		this.element.update().socketModule(file, vars);
		this.showMenu(e.pageX, e.pageY);
	},

	onClick: function (e, node) {
		node = e.target;
		if ($(node).readAttribute('data-menu')) return;
		this.hideMenu();
		document.removeEventListener('click', this.onClick.bind(this), false);
		$A(document.querySelectorAll('.right_clicked')).invoke('removeClassName', 'right_clicked');
	},
	repositionMenu: function () {

		this.pageOffset = 10;
		var viewport = document.viewport.getDimensions(),
			offset = document.viewport.getScrollOffsets(),
			containerWidth = this.element.getWidth(),
			containerHeight = this.element.getHeight(),
			positionX = parseInt(this.element.style.left),
			positionY = this.element.offsetTop;

		this.element.setStyle({
			left: ((positionX + containerWidth + this.pageOffset) > viewport.width ? (viewport.width - containerWidth - this.pageOffset) : positionX) + 'px',
			top: ((positionY - offset.top + containerHeight) > viewport.height && (positionY - offset.top) > containerHeight ? (positionY - containerHeight) : positionY) + 'px'
		});

	}
}

new app_context();