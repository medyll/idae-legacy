myddeDatalist = {};
myddeDatalist = Class.create();
myddeDatalist.prototype = {
	initialize: function (element, options) {
		this.element = $(element)
		if (this.element.readAttribute('act_job_done')) return this.element;
		// done :
		this.element.setAttribute('act_job_done', true);
		var mdlOption = {scope: '', vars: '', paramName: false, populate: true, autoclose: false}
		this.options = Object.extend(mdlOption, options || {});
		//
		this.timer = 0;
		this.timerSaisie = 0;
		this.state = 0;
		this.firstLoad = false;
		this.oldValue = '';


		if ($(this.element).readAttribute('vars')) {
			this.options.vars = $(this.element).readAttribute('vars');
			this.element.http_vars = $(this.element).readAttribute('vars');
		}
		if ($(this.element).readAttribute('target')) {
			this.options.target = $(this.element).readAttribute('target');
		}
		if ($(this.element).readAttribute('dsp')) {
			new resizeInput($(this.element));
		}
		if ($(this.element).readAttribute('scope')) {
			this.options.scope = $(this.element).readAttribute('scope');
		}
		if ($(this.element).readAttribute('paramName')) {
			this.options.paramName = $(this.element).readAttribute('paramName');
		} else {
			this.options.paramName = $(this.element).readAttribute('name');
		}

		if ($(this.element).readAttribute('datalist')) {
			this.options.datalist = $(this.element).readAttribute('datalist');
		}
		if ($(this.element).readAttribute('populate')) {
			this.options.populate = true;
		}
		if ($(this.element).readAttribute('autoclose')) {
			this.options.autoclose = true;
		}

		this.prepare();
		if (this.options.populate == true) {
			this.emit_populate();
		}
		this.still_here();
		return this.element;
	},
	prepare: function () {
		this.element.identify();
		if ($(this.element).readAttribute('datalist_input_name')) {
			var name = $(this.element).readAttribute('datalist_input_name');
			this.toPick = new Element('input', {type: 'hidden', name: name});
			this.element.insert({after: this.toPick});
			if ($(this.element).readAttribute('datalist_input_value')) {
				this.toPick.value = $(this.element).readAttribute('datalist_input_value');
			}
		}
		if (!this.wrapper) {
			this.wrapper_holder = new Element('div', {'class': 'wrapper_holder'});
			this.wrapper_holder.style.display = 'inline-block';
			this.wrapper_holder.style.position = 'relative';
			this.element.wrap(this.wrapper_holder);
		}

		if (this.options.target) {
			this.datalist_element = $(this.options.target);
			//
		} else {
			//
			this.datalist_element = $('dtl_' + this.element.id) || new Element('div', {'id': 'dtl_' + this.element.id, className: 'act_dropdown applink applinkblock'})
			this.datalist_element.style.position = 'absolute';
			this.datalist_element.style.maxHeight = '300px';
			this.datalist_element.style.minWidth = this.element.getWidth() + 'px';
			this.datalist_element.style.overflow = 'auto';
			//
			this.btn = new Element('i', {className: 'fa fa-caret-down textgrisfonce'});
			this.btn.style.position = 'absolute';
			this.btn.style.right = '5px';
			this.btn.style.top = '7px';

			// this.element.insert({after: this.datalist_element.hide()});
			$(document.body).appendChild(this.datalist_element.hide())
			this.datalist_element.clonePosition(this.element, {setWidth: false, setHeight: false})
			offset = this.element.viewportOffset()
			this.datalist_element.setStyle({top: (offset.top + 25) + 'px'})

			this.element.insert({after: this.btn});

		}
		this.element.setAttribute('list', this.datalist_element.id);

		$(this.element).observe('click', this.onclick.bind(this));
		$(this.element).observe('focus', this.onfocus.bind(this));
		$(this.element).observe('blur', this.onblur.bind(this));
		$(this.element).observe('keydown', this.onkeydown.bind(this), false);
		$(this.element).observe('keyup', this.emit.bind(this));
		$(this.datalist_element).on('click', '.avoid', this.reset_close.bindAsEventListener(this));
		$(this.datalist_element).observe('dom:act_click', this.act_fire.bindAsEventListener(this));
		if (this.element.form) {
			$(this.element.form).observe('submit', this.prevent_submit.bind(this), true);
		}

		return true;
	},
	onfocus: function () {
		clearTimeout(this.timer);
		if (!this.options.target) {
			this.datalist_element.clonePosition(this.element, {setWidth: false, setHeight: false})
			var offset = this.element.viewportOffset()
			this.datalist_element.setStyle({top: (offset.top + 25) + 'px'});
			this.datalist_element.makeOnTop();
		}

		if ($(this.datalist_element).empty()) {
			this.emit();
		}
		if (this.options.populate) {
			this.datalist_element.show();
		}
		return true;

	},
	reset_close: function (event) {
		clearTimeout(this.timer);
		this.element.focus();
		clearTimeout(this.timer);
	},
	onclick: function () {
		if (this.options.populate) {
			this.datalist_element.show();
		}
	},
	onblur: function () {
		this.timer = setTimeout(function () {
			if (!this.options.target || this.options.autoclose == true) this.datalist_element.hide();
		}.bind(this), 150);
	},
	onkeyup: function () {
		this.emit();
	},
	onkeydown: function (event) {

		if (event.keyCode == Event.KEY_RIGHT || event.keyCode == 39) {
			Event.stop(event);
			return false;
		} // Right Arrow
		if (event.keyCode == Event.KEY_LEFT || event.keyCode == 37) {
			Event.stop(event);
			return false;
		}// Left Arrow

		if (event.keyCode == Event.KEY_UP || event.keyCode == 38) {
			Event.stop(event);
			if (this.timerSaisie) {
				clearTimeout(this.timerSaisie)
			}
			if ($(this.datalist_element).select('.autoToggle.active').size() == 0) {
				$(this.datalist_element).select('.autoToggle').last().addClassName('active');
			} else {
				previous = $(this.datalist_element).select('.active').first().previous('.autoToggle');
				$(this.datalist_element).select('.autoToggle.active').invoke('removeClassName', 'active')
				$(previous).addClassName('active');
			}
			$(this.element).scrollTo();
			return false;
		}
		if (event.keyCode == Event.KEY_DOWN || event.keyCode == 40) { // Down Arrow
			Event.stop(event);
			if (this.timerSaisie) {
				clearTimeout(this.timerSaisie)
			}
			if ($(this.datalist_element).select('a.active').size() == 0) {
				$(this.datalist_element).select('a').first().addClassName('active')
			} else {
				next = $(this.datalist_element).select('.active').first().next('.autoToggle');
				$(this.datalist_element).select('.autoToggle.active').invoke('removeClassName', 'active')
				$(next).addClassName('active');
			}
			$(this.element).scrollTo();
			return false;
		}
		if (event.keyCode == 13) { // Enter (Open Item)
			event.preventDefault();
			event.stopPropagation();
			Event.stop(event);
			func = $(this.datalist_element).select('.active[onclick]').first().readAttribute('onclick');
			identi = $(this.datalist_element).select('.active[onclick]').first().identify();
			eval(func.replace("this", identi));
			Event.stop(event);
			return false;
		}
		return true;
	},
	prevent_submit: function (event) {
		if ($(this.datalist_element).visible()) {
			event.preventDefault();
			event.stopPropagation();
			Event.stop(event);
			console.log('yeah')
		}
	},

	act_fire: function (event) {
		var act_event;
		act_event = event;

		Event.stop(event);
		event.preventDefault();
		if (this.toPick && act_event.memo.id) {
			$(this.toPick).value = act_event.memo.id
		} else if (this.toPick && act_event.memo.value) {
			$(this.toPick).value = act_event.memo.value
		}
		$(this.element).value = act_event.memo.value;
		$(this.element).fire('dom:act_change', act_event.memo);
		$(this.datalist_element).hide();
	},
	emit_populate: function () {
		var vars;
		vars = this.options.vars;
		vars = vars + '&' + this.options.paramName
		// clearTimeout(this.timerSaisie);
		this.timerSaisie = setTimeout(function () {
			$(this.datalist_element).loadModule(this.options.datalist, vars, {
				scope: this.options.scope, value: this.options.scope, onComplete:this.oncomplete.bind(this)
			})
		}.bind(this), 250);

		this.oldValue = $(this.element).value;
	},
	emit: function () {
		
		//console.log(this.oldValue, ' old new ', $(this.element).value)
		if (this.oldValue == $(this.element).value) {
			// clearTimeout(this.timerSaisie);
			return;
		}
		// str_len
		/*if ($(this.datalist_element).select('[data-need_more]').first()) {
			var need_more = $(this.datalist_element).select('[data-need_more]').first().readAttribute('data-need_more');
			// console.log(this.oldLen, $(this.element).value.length);
			if (eval(need_more) == 0) {
				if ((this.oldLen || 0) > $(this.element).value.length) {

					this.oldLen = $(this.element).value.length;
				} else {
					this.oldLen = $(this.element).value.length;
					this.search_here();
					return;
				}
			}

		}*/
		if ((this.oldLen || 0) > $(this.element).value.length) {

			this.oldLen = $(this.element).value.length;
		} else {
			this.oldLen = $(this.element).value.length;
			// this.search_here();
			//return;
		}

		var vars;
		vars = this.options.vars;
		vars = vars + '&' + this.options.paramName + '=' + $(this.element).value

		if (this.timerSaisie) clearTimeout(this.timerSaisie);
		this.timerSaisie = setTimeout(function () {
		 
			$(this.datalist_element).loadModule(this.options.datalist, vars, {
				scope: this.options.scope, value: this.options.scope, onComplete:this.oncomplete.bind(this)
			})
		}.bind(this), 250);

		this.oldValue = $(this.element).value;
	},
	still_here: function () {
		if (!document.getElementById($(this.element).identify())) {
			this.datalist_element.remove();
		} else {
			setTimeout(function () {
				this.still_here()
			}.bind(this), 5000);
		}
	},
	search_here: function () {

		$(this.datalist_element).select('.app_select').each(function (node) {
			var inn = node.innerHTML.stripTags().toLowerCase();

			if (!inn.include($(this.element).value.toLowerCase())) {
				Element.hide.defer(node);
			} else {
				Element.show.defer(node);
			}
		}.bind(this))
	},
	oncomplete: function(red){
		// console.log(red)
	}
}