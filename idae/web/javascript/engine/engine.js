var searchtimer;
quickFind = function (value, where, tag, spy) {
	clearTimeout(searchtimer);
	value = value.toLowerCase();
	searchString = value;
	searchtimer = setTimeout(function () {
		matrixView = null;
		i = 0;
		if (value.empty() || value.length < 2) {
			//return
		}
		if (value.empty() || value.length < 2) {
			$(where).select(tag).invoke('show');
			// $$('#cometspy li').invoke('show');
		} else {
			$(where).select(tag).each(function (node) {
				inn = node.innerHTML.stripTags().toLowerCase();
				if (!inn.include(value)) {
					Element.hide.defer(node)
				} else {
					Element.show.defer(node)
					i++;
					// $('resultStatus').update(i+' resultats pour "'+value+'"');
				}
			})
		}
		if (spy) {
			if ($(spy)) {
				spy = new Element('div', {className: 'inline ededed padding border4'});
			}
			if (!spy.empty()) {
				$(spy).update('<div class="inline ededed padding border4">' + i + "</div")
			}
		}
	}, 250)
	/*$(where).observe('content:loaded',function(){
	 quickFind (value,where,tag,spy)
	 }.bind(this));*/
}

act_chrome_gui = function (file, vars, options) {

	var vars = vars || '',
		filekey = clean_string(file + vars);

	mdlDiv = ('["' + file + '"]').replace('/', '","', 'gi')
	onlyFile = eval(mdlDiv).last();
	valueMdl = onlyFile
	var ajaxOption = {file: file, ajaxLoad: true, parent: 'inBody', inTask: true, sanitizeJSON: false}

	var options = Object.extend({
		ajaxLoad: true,
		parent: 'inBody',
		ident: filekey,
		inTask: true,
		scope: false
	}, options || {});
	// on rajoute le nom du module à pars
	vars += '&module=' + file
	if (options) {

		if (options.value) {
			valueMdl = options.value
		}
		if (options.ident) {
			ajaxOption.ident = options.ident;
		}
	}
	Object.extend(ajaxOption, options || {});

	url = changeCnameTrick() + 'mdl/' + file;
	// on ecrit options ident ( onlyfile par zefault )
	temp = vars.toQueryParams();
	if (temp.table && temp.table_value) {
		ajaxOption.ident = temp.table + temp.table_value;
	}
	var crh_gui = new windowGui(file, '', url, vars, ajaxOption);
	var guiElement = crh_gui.innerDisp;
	if (!guiElement) return;
	$(guiElement).writeAttribute({
		mdl: file,
		vars: vars,
		value: valueMdl
	})

	if (temp.table && temp.table_value) {
		$(guiElement).writeAttribute({table: temp.table, scope: 'id' + temp.table, value: temp.table_value}); // , ident: temp.table + temp.table_value
	} else if (temp.table) {
		$(guiElement).writeAttribute({scope: temp.table, table: temp.table})
	} else {
		if (options.scope) {
			$(guiElement).writeAttribute({scope: options.scope})
		}
	}

	// document.location.href = '#' + file + '#' + vars;
}
ajaxMdl = function (file, title, davars, options) { // utilser act_chrome_gui !!!

	var vars = vars || '';
	file1 = file;
	mdlDiv = ('["' + file1 + '"]').replace('/', '","', 'gi')
	onlyFile = eval(mdlDiv).last();
	valueMdl = onlyFile
	rep = eval(mdlDiv).without(onlyFile);
	vars = davars || '';
	ajaxOption = {ident: clean_string(file + vars), file: file, ajaxLoad: true, parent: 'inBody', inTask: true, sanitizeJSON: false}

	this.options = Object.extend({
		ajaxLoad: true,
		parent: 'inBody',
		inTask: true,
		scope: false
	}, options || {});
	// on rajoute le nom du module à pars
	// vars += '&module=' + file
	if (options) {
		if (options.modalOn) {
			dmp = $(options.modalOn).makeModal().modal().setStyle({'zIndex': 0});
			options.buttonClose = false;
			options.buttonReduce = false;
			options.inTask = false;
			options.onclose = function () {
				$(options.modalOn).undoLoading();
			}.bind(this);
			ajaxOption.parent = $(dmp);
		}
		if (options.value) {
			valueMdl = options.value
		}
		if (options.ident) {
			ajaxOption.ident = options.ident;
		}
	}
	Object.extend(ajaxOption, options || {});

	url = changeCnameTrick() + 'mdl/' + file;

	var guiElement = new windowGui(onlyFile, title, url, vars, ajaxOption);

	if (options) {
		if (options.ident) {
			onlyFile = options.ident
		}
	}
	if($(onlyFile)){
		//$(onlyFile).makeOnTop();
		$(onlyFile).writeAttribute({mdl: file, vars: vars, value: valueMdl})  // ,title:file
		if (this.options.scope) {
			$(onlyFile).writeAttribute({scope: this.options.scope})
		}
		$(onlyFile).value = valueMdl;

	}

	document.location.href = '#' + file + '#' + vars;
}


//
ajaxInMdl = function (file, element, vars, options) {
	// console.log('vars'+ vars);
	//
	var guivars
	// si en module sous repertoire
	var tmpDiv = ('["' + file + '"]').replace('/', '","', 'gi')
	tmpDiv = eval(tmpDiv).last();

	// on rajoute le nom du module à pars

	var vars = vars || '';
	vars += '&module=' + file;
	vars += '&mdl=' + file;

	var mdlOption = {
		value: null,
		seeLoading: true,
		onglet: false,
		tabParent: null,
		taskBar: null,
		reloaded: true,
		single: true,
		fitScreen: true,
		cache: false,
		scope:''
	}

	this.options = Object.extend(mdlOption,options || {});

	if (this.options.onglet != false) {
		var barreTache = this.options.taskBar || $('taskBar');
		var divParent = this.options.tabParent || $('mainApp');

		if (!$(element)) {
		} else {
			$(element).addClassName('flex_v').show();
			if (!$('taskbar_' + $(element).identify())) {
				barreTache = create_element_in('div', $(element), {className: 'taskBar ededed', id: 'taskbar_' + $(element).identify()});
			} else {
				barreTache = $('taskbar_' + $(element).identify());
			}
			divParent = create_element_in('div', $(element), {className: 'flex_main'});
		}
		if (!$(barreTache)) {
			popopen('proxyIndex.php?titre=' + this.options.onglet + '&mdl=' + this.options.file + '&' + vars, '', '', this.options.onglet, true)
			return;
		}

		guivars = {title: this.options.onglet, file: file, vars: vars, taskBar: $(barreTache).identify(), container: divParent, fitScreen: this.options.fitScreen};

		if (this.options.mainIdent) {
			guivars.mainIdent = this.options.mainIdent;
		}

		element = JSGUI.add(guivars);

	}
	// atrributs !!!

	$(element).writeAttribute({
		vars: vars,
		mdl: file,
		value: this.options.value || '',
		scope: this.options.scope || ''
	})
	//.log()
	$(element).cleanWhitespace();

	if (this.options.reloaded == true) {
		$(element).socketModule(file, vars, this.options)
	}

	if (this.options.reloaded == false && !$(element).childElements().last()) {
		$(element).socketModule(file, vars, this.options)
	}
	// document.location.href = '#' + file;
}.bind(this);

ajaxValidation = function (action, path, pars) {
	path = path || '';
	var url = path + 'actions.php' || 'actions.php';
	/*opt.PHPSESSID = Cookies.get('PHPSESSID') || '';
	 opt.SESSID = Cookies.get('SESSID') || '';*/

	new Ajax.Request(
		url, {
			onComplete: function (transport) {
				transport.responseText.evalScripts()
			},
			method: 'post',
			sanitize: true,
			requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8', 'Set-Cookie', 'PHPSESSID=' + localStorage.getItem('PHPSESSID') + ';path=/'],
			postBody: 'F_action=' + action + '&' + pars
		});
	// ,onComplete: function(){div.remove()}.bind(this)
	//requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8','Set-Cookie','PHPSESSID='+localStorage.getItem('PHPSESSID')+';path=/'],
	return true;
}
runSocketModule = function () {

}
ajaxFormValidation = function (form) {
	if (form.readAttribute('auto_close')) {
		$(form).makeLoading();
	}
	setTimeout(function () {
		ajaxFormValidationReal(form)
	}, 300);
}
ajaxFormValidationReal = function (form) {
	var vars
	this.loadingform = $(form);
	/*valid = new Validation (form, {immediate: true, useTitles: true});
	 result = valid.validate ();
	 if (!result) {
	 return;
	 }*/
	if (!$('div_form_validation')) {
		div_form_validation = new Element('div')
		div_form_validation.id = 'div_form_validation';
		$('body').appendChild(div_form_validation);
		$(div_form_validation).hide();
	}
	//
	var options = {
		method: 'post', evalScripts: true,
		requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8']
	}
	if (form.readAttribute('auto_close')) {
		options.onComplete = function () {
			$(form).hide().fire('dom:close')
		}.bind(this)
	}
	if (form.readAttribute('auto_reset')) {
		options.onComplete = function () {
			$(form).reset();
		}.bind(this)
	}
	if (form.action) {
		var url = form.action;
	} else {
		var url = 'actions.php';
	}
	options.postBody = Form.serialize($(form))
	lodaj = new Ajax.Updater($('div_form_validation'), url, options);

	return true;
}

