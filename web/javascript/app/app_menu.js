/**
 * Created by lebru_000 on 06/11/2015.
 */
var app_menu;
app_menu = Class.create();
app_menu.prototype = {
	initialize: function (options) {
		this.options = Object.extend({}, options || {});
		this.build();
	},
	build: function () {
		this.element = new Element('div');
		this.element.id = 'div_app_menu';
		this.element.addClassName('context_app_menu');
		if(this.element.next())this.element.next().addClassName('context_app_menu_after');
		$(document.body).insert(this.element);
		this.hideMenu();
		$(document.body).on('click', '[data-menu]', this.onDataMenu.bind(this));
		this.element.observe('content:loaded', this.repositionMenu.bind(this))
	},
	showMenu: function (x, y) {
		this.element.style.left = x + 'px';
		this.element.style.top = y + 'px';
		this.element.show();
	},
	hideMenu: function () {
		setTimeout(function () {
			this.element.hide();
		}.bind(this), 250)
	},
	onDataMenu: function (e, node) {
		e.preventDefault();
		Event.stop(e);
		$$('.context_app_menu_after').invoke('hide')
		document.addEventListener('click', this.onClickBtn.bind(this), false);
		var clone = $(node).readAttribute('data-clone');
		if (clone) {
			$(this.element).clonePosition(node, {
				setWidth: false,
				setHeight: false,
				offsetTop: node.getHeight()
			}).update(node.next().innerHTML).show();
			this.repositionMenu();
			$(this.element).show();
		} else {
			var next = $(node.next());
			if (!$(node).readAttribute('data-menu_free')) {
				next.setStyle({
					position: 'absolute'
				}).show().addClassName('hide_on_click'); //
				next.clonePosition(node, {
					setWidth: false,
					setHeight: false,
					offsetTop: node.getHeight()
				});
			} else {
				next.addClassName('hide_on_click'); //
			}
			next.show();
			this.repositionMenu(next);
		}
	},
	onClickBtn: function (e, node) {
		node = e.target;
		if ($(node).readAttribute('data-clone')) return;
		this.hideMenu();
		document.removeEventListener('click', this.onClickBtn.bind(this));
		$A(document.querySelectorAll('.right_clicked')).invoke('removeClassName', 'right_clicked');
	},
	repositionMenu: function (element) {
		var element = $(element) || this.element;
		if ($(element).readAttribute('data-menu_free')) return;
		element.makeOnTop();
		var theCSSprop = window.getComputedStyle(element.parentNode, null).getPropertyValue('overflow');
		if (theCSSprop == 'visible' || theCSSprop == 'auto') return;
		this.pageOffset = 5;
		var viewport = document.viewport.getDimensions(),
			offset = document.viewport.getScrollOffsets(),
			containerWidth = element.getWidth(),
			containerHeight = element.getHeight(),
			positionX = parseInt(element.style.left),
			positionY = element.offsetTop;
		element.setStyle({
			left: ((positionX + containerWidth + this.pageOffset) > viewport.width ? (viewport.width - containerWidth - this.pageOffset) : positionX) + 'px',
			top: ((positionY - offset.top + containerHeight) > viewport.height && (positionY - offset.top) > containerHeight ? (positionY - containerHeight) : positionY) + 'px'
		});
	}
}
new app_menu();
