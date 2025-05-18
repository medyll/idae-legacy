var timerClose;
selfObservers = Class.create();
selfObservers.prototype = {
	initialize: function (element) {
		this.element = element


		//
		document.on('click', function (event) {
		/*	if (longpress) {
				return false;
			};*/
			elementEvent = Event.element(event);
			if (!$(elementEvent)) return false;
			if (elementEvent.match('input[type=checkbox]')) {
				if (elementEvent.checked) {
					if ($(elementEvent).up('tr') && $(elementEvent).up('tr').visible()) {
						$(elementEvent).doCheck();
					} else if (!$(elementEvent).up('tr')) {
						$(elementEvent).doCheck()
					}
					// $(elementEvent).doCheck()
				} else {
					$(elementEvent).doUnCheck()
				}
				return;
			}

			if (elementEvent.hasClassName('autoNext')) {
				$(elementEvent).next().toggle()
				if ($(elementEvent).next().visible()) {
					$(elementEvent).addClassName('active');
				} else {
					$(elementEvent).removeClassName('active');
				}
			}
			if (!elementEvent.hasClassName('avoid') && !elementEvent.hasClassName('hide_on_click')) {
				if (!elementEvent.up().hasClassName('avoid') ) {
					$$('.hide_on_click').invoke('hide');
				/*	if (  !elementEvent.up('.hide_on_click')) {
						$$('.hide_on_click').invoke('hide');
					}*/
				}
			}

		})


		var pressTimer;
		var longpress;
		/*$(document.body).on('mousedown', '[vars] [data-field_name]', function (event, node) {

			longpress = false;
			pressTimer = window.setTimeout(function () {
				// your code here $$('tr[vars] td')
				if (!$('edit_node')) {
					var edit_node = new Element('div', {id: 'edit_node', className: 'absolute blanc border4 edit_node'});
					$('body').appendChild(edit_node);
				}

				var vars = $(node).up().readAttribute('vars') || '';
				vars += '&field_name=' + $(node).readAttribute('field_name') || '';
				vars += '&field_name_raw=' + $(node).readAttribute('field_name_raw') || '';
				$('edit_node').clonePosition($(node)).show();
				//
				$('edit_node').loadModule('app/app_field_update', vars);
				longpress = true;
			}.bind(this), 1000)
		});*/

		/*document.on('mouseup', function () {
			clearTimeout(pressTimer);
		});*/
		this.shots = 0

	}
}

