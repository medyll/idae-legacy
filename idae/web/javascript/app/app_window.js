window.windowsGui_fragment = '<div class="containerdisp appgui_windowcolor">'
	+ '<div class="handledisp">'
	+ '<div class="cell buttondisp iconedisp aligncenter">'
	+ '<i class="fa fa-th cursor" ></i>'
	+ '</div>'
	+ '<div class="cell">'
	+ '<span data-title="" class="titlefrm"></span>'
	+ '</div>'
	+ '<div class="cell buttondisp buttonreduce aligncenter" >'
	+ '<i class="fa fa-minus cursor"></i>'
	+ '</div>'
	+ '<div class="cell buttondisp popperdisp aligncenter">'
	+ '<i class="fa fa-expand  cursor"></i>'
	+ '</div>'
	+ '<div class="cell buttondisp buttonclose aligncenter">'
	+ '<i class="fa fa-times cursor"></i>'
	+ '</div>'
	+ '</div>'
	+ '<div class="menudisp applink applinkblock" style="display: none;">'
	+ '<a class="buttonrefresh"><i class="fa fa-refresh"></i> Recharger</a><a><i class="fa fa-thumb-tack butonpin"></i> Pin to</a><a><i class="fa fa-share butonshare"></i> Partager</a><a class="buttonclose"><i class="fa fa-times"></i> Fermer</a></div>'
	+ '<div class="entetedisp" style="display: none;"></div>'
	+ '<div class="innerdisp" ></div>'
	+ '<div class="footerdisp" style="display:none"></div>'
	+ '</div>';


var windowGui = {};
windowGui = Class.create();
windowGui.prototype = {
	initialize: function (frm, title, rep, vars, options) {
		// console.log(frm,title,rep,vars , options)
		this.options = Object.extend({
			ident: clean_string(frm),
			className: '',
			parent: document.body,
			buttonClose: true,
			buttonReduce: true,
			hasHandle: true,
			inTask: true,
			content: false,
			icone: true,
			runonce: false,
			cookieArrayName: 'windowGui',
			onclose: '',
			startPosition: this.setLastPos.bind(this)
		}, options || {});
		this.options.scope = this.options.scope || this.options.parent

		this.container_id = 'container' + this.options.ident;

		this.parent = this.options.parent;

		this.frm = frm;
		this.title = title;
		this.rep = rep;
		this.vars = vars || '';

		if ($(this.options.ident)) {
			this.give_focus();
			if (this.options.runonce == true) {
				$(this.options.ident).fire('dom:appreload', {vars: this.vars});
				return $(this.options.ident);
			}
			if (this.options.ajaxLoad) {
				if (typeof socket == 'object') {
					setTimeout(function () {
						$(this.options.ident).socketModule(this.options.file, this.vars);
					}.bind(this), 1)

					return $(this.options.ident);
				}

			}

			return $(this.options.ident);
		}

		this.buildDivs();
		this.centerGuiFrm();
		setTimeout(function () {
			this.give_focus();
		}.bind(this), 0)
		setTimeout(function () {
			$(this.container).makeOnTop();
			this.makeDraggable();
		}.bind(this), 0)
		//
		$(this.entete).hide();

		$(this.options.ident).close = this.close.bind(this);

		$(this.innerDisp).observe('content:loaded', this.content_loaded.bind(this));

		if (this.options.ajaxLoad) {
			this.ajaxLoad();
		} else {
			this.ready()
		}
		//
		if (this.options.startPosition == 'cascade') {
			this.cascadeGuiFrm()
		}

		//
		$(this.container).observe('dom:close', function (event) {
			Event.stop(event);
			this.close();
		}.bind(this));
		//
		//
		if (this.options.content != false) {
			$(this.innerDisp).update(this.options.content);
			$(this.innerDisp).fire('content:loaded');
		}
		$(this.container).observe('click', function (event) {
			this.chkDispZone();
			this.give_focus();
		}.bind(this));

		// buttonrefresh
		$(this.container).on('click', '.buttonrefresh', function (event, node) {
			this.ajaxLoad.bind(this)
		}.bind(this))
		// buttonclose
		$(this.container).on('click', '.buttonclose', function (event, node) {
			this.close.bind(this)
		}.bind(this))

		$(this.innerDisp).observe('click', function (event) {
			this.menuDisp.hide();
		}.bind(this));
		$(this.innerDisp).observe('click', function (event) {
			this.menuDisp.hide();
		}.bind(this));
		//
		return $(this.options.ident);
	},
	content_loaded: function () {
		$(this.innerDisp).stopObserving('content:loaded', this.content_loaded.bind(this));
		if (this.options.startPosition != 'cascade') {
			this.setLastPos();
		}
		// console.log($(this.innerDisp).select('.enteteFor'))
		if ($(this.innerDisp).select('.enteteFor').size() != 0) {
			ent = $(this.innerDisp).select('.enteteFor').first();
			$(this.entete).update($(ent).innerHTML).show()
			$(ent).remove();
		}
		if ($(this.innerDisp).select('.titreFor').size() != 0) {
			ent = $(this.innerDisp).select('.titreFor').first();
			this.title = $(ent).innerHTML
			$(this.titre).update($(ent).innerHTML).show()
			$(ent).remove();
		}
		if ($(this.innerDisp).select('.footerFor').size() != 0) {
			var ent = $(this.innerDisp).select('.footerFor').first();
			$(this.footerDisp).update($(ent).innerHTML).show()
			$(ent).remove();
		}
		this.chkDispZone();
		this.give_focus();

		setTimeout(function(){
			// addResizeListener($(this.container), this.resize.bind(this));
		}.bind(this),1000)
	},
	resize:function(){
		var debug = {innerdisp:[this.innerDisp.getWidth(),this.innerDisp.getStyle('width')]};
		debug['first child'] = this.innerDisp.firstDescendant()
		debug['first child size'] = this.innerDisp.firstDescendant().getWidth()
		// console.log('resize .. ',debug)
	},
	get_key: function (key) {
		value = localStorage.getItem('app_window_pos_' + key);

		return value && JSON.parse(value);
	},
	store_key: function (key, value) {
		localStorage.setItem('app_window_pos_' + key, JSON.stringify(value));
	},
	give_focus: function () {
		// console.log(this.container_id);
		$(this.container_id).makeOnTop();
		if (!$(this.container_id).hasClassName('active')) {
			$(this.container_id).siblings().invoke('removeClassName', 'active');
			$(this.container_id).addClassName('active');
		}

	},
	ident: function () {
		adate = new Date();
		ident = adate.getMilliseconds();
		ident += adate.getSeconds();
		ident += adate.getMinutes();
		return 'ident' + ident;
	},
	// construction des divs
	buildDivs: function () {
		tmp_elem = new Element('div');
		tmp_elem.update(window.windowsGui_fragment);
		// ASSIGNATION
		this.container = tmp_elem.select('.containerdisp').first();
		this.innerDisp = tmp_elem.select('.innerdisp').first();
		this.handleDisp = tmp_elem.select('.handledisp').first();
		this.titre = tmp_elem.select('.titlefrm').first();
		this.entete = tmp_elem.select('.entetedisp').first();
		this.menuDisp = tmp_elem.select('.menudisp').first();
		this.footerDisp = tmp_elem.select('.footerdisp').first();
		this.iconeDisp = tmp_elem.select('.iconedisp').first();
		this.buttonClose = tmp_elem.select('.buttonclose').first();
		this.buttonReduce = tmp_elem.select('.buttonreduce').first();
		this.buttonPopper = tmp_elem.select('.popperdisp').first();
		// ASSIGN
		this.container.id = this.container_id;
		this.innerDisp.id = this.options.ident;

		// MENAGE
		if (this.options.icone != true) {
			this.iconeDisp.remove();
		}
		;
		if (this.options.buttonClose != true) {
			this.buttonClose.remove();
		}
		if (this.options.buttonReduce != true) {
			this.buttonReduce.remove();
		}
		// TPL
		if (this.options.hasHandle == true) {
			this.titre.update(this.title || '');
		}
		// APPEND
		// if(this.options.hasHandle != true){this.handleDisp.appendChild(this.entete); }


		// handle
		//if(this.options.hasHandle == true){
		// this.makeHandleDisp();
		//}

		if (this.options.className != '') {
			$(this.container).addClassName(this.options.className);
		}
		if (this.options.hasHandle == true) {
			this.titre.update(this.title || '');
		}

		$(this.container).loaded = true;
		$(this.container).setStyle({visibility: 'visible', top: 0, left: '0', 'max-width': screen.width + 'px'});

		// DOM
		$(this.parent).appendChild($(this.container));
		// ACTIVATE
		this.clickOdrome();
	},
	clickOdrome: function () {

		$(this.container).on('click', '.buttonclose', function (event) {
			Event.stop(event);
			this.close();
		}.bind(this));
		$(this.container).on('click', '.buttonreduce', function (event) {
			Event.stop(event);
			this.isReduced();
		}.bind(this));
		$(this.container).on('click', '.iconedisp', function () {
			this.menuDisp.toggle()
		}.bind(this));
		$(this.container).on('click', '.popperdisp', function (event) {
			davars = {titre: this.title, mdl: this.options.file, vars: this.vars};
			localforage.setItem('app_popup', JSON.stringify(davars)).then(function () {
				//popopen('index.php?titre=' + this.title + '&mdl=' + this.options.file + '&' + this.vars, $(this.innerDisp).getWidth(), $(this.innerDisp).getHeight(), this.title, true);

				// this.close();
			})
			popopen('index.php?', $(this.innerDisp).getWidth(), $(this.innerDisp).getHeight(), this.title.stripTags(), true);
			localStorage.set('popup', JSON.stringify(davars));
			Event.stop(event);

		}.bind(this))//alert(this.options.file,this.vars);

		$(this.container).on('click', '.buttonrefresh', function () {
			this.ajaxLoad();
			this.menuDisp.hide()
		}.bind(this)); // ;
		$(this.container).on('click', '.buttonshare', function () {
			this.ajaxLoad()
		}.bind(this)); // ;
		$(this.container).on('click', '.buttonpin', function () {
			this.ajaxLoad()
		}.bind(this)); // ;
	},
	makeDraggable: function () {
		this.mydrag = new Draggabilly(this.container, {
			// options...
			handle: ('.handledisp')
		});
		this.mydrag.on('dragStart', this.startDrag.bind(this));
		this.mydrag.on('dragEnd', this.endDrag.bind(this));

	},
	startDrag: function () {
		this.innerDisp.setStyle({visibility: 'hidden'});
	},
	endDrag: function () {
		// this.mydrag.destroy();
		this.chkDispZone();
		this.innerDisp.show();
		this.innerDisp.setStyle({visibility: 'visible'});

		position_store = {top: this.container.offsetTop, left: this.container.offsetLeft, width: this.container.offsetWidth, height: this.container.offsetHeight};
		this.store_key(this.options.ident, position_store);
	},
	ajaxLoad: function () {
		//this.centerGuiFrm();
		if (typeof socket == 'object') {
			$(this.options.ident).socketModule(this.options.file, this.vars);
			return;
		} else {
			this.myAjax = new Ajax.Updater(this.options.ident, this.rep + '.php',
				{
					method: 'post',
					onlyLatestOfClass: this.options.ident,
					postBody: this.vars,
					/*onComplete: this.onComplete.bind(this), */
					evalScripts: true,
					requestHeaders: ['Content-type', 'application/x-www-form-urlencoded', 'charset', 'UTF-8']
				});

		}

	},
	onComplete: function (transport) {
		// this.innerDisp.fire('dom:resize');

		if (this.options.startPosition == 'cascade') {
			this.cascadeGuiFrm.bind(this)
		} else {
			this.centerGuiFrm();
		}
		//afterAjaxCall($(this.innerDisp));
	},
	hasCookie: function () {
		if (this.get_key(this.options.ident) != null) {
			return true;
		} else {
			return false;
		}
	},
	setLastPos: function () {
		position_store = this.get_key(this.options.ident);
		//console.log(position_store)
		if (position_store) {
			$(this.container).setStyle({top: position_store.top + 'px', left: position_store.left + 'px', visibility: 'visible'});
		} else {
			this.centerGuiFrm()
		}

	},
	centerGuiFrm: function () {
		if (!$(this.container)) {
			return false;
		}
		LeftPosition = (($(this.container).up().offsetWidth) / 2) - ($(this.container).getDimensions()['width'] / 2)
		TopPosition = (($(this.container).up().getHeight()) / 3) - ($(this.container).getHeight() / 2)

		$(this.container).setStyle({top: TopPosition + 'px', left: LeftPosition + 'px', visibility: 'visible'});
		this.ready();
	},
	cascadeGuiFrm: function () {
		if (!$(this.container.id)) {
			return false;
		}
		if (this.hasCookie()) {
			this.setLastPos();
			return
		}
		LeftPosition = ($(document.body).getWidth() / 4) + $(this.container.id).siblings().size() * 5;
		TopPosition = $(this.container.id).siblings().size() * 25;
		Element.setStyle($(this.container), {visibility: 'visible', left: LeftPosition + 'px'});
		this.ready();
	},
	ready: function () {
		$(this.container).setStyle({visibility: 'visible'});
		$(this.container).show();
		// this.setLastPos();
		if (this.options.onComplete) {
			this.options.onComplete();
		}
	},
	chkDispZone: function () {
		this.options.pageOffset = 100;
		var viewport = document.viewport.getDimensions(),
			offset = document.viewport.getScrollOffsets(),
			containerWidth = this.container.getWidth(),
			containerHeight = this.container.getHeight();

		positionX = parseInt($(this.container).style.left);
		positionY = parseInt($(this.container).offsetTop);

		if (eval(positionX + this.options.pageOffset) > viewport.width) {
			this.container.setStyle({
				left: (viewport.width - this.options.pageOffset) + 'px'
			});
		}
		if (eval(positionX + containerWidth - this.options.pageOffset) < 0) {
			this.container.setStyle({
				left: (this.options.pageOffset - containerWidth) + 'px'
			});
		}
		// if (eval(positionY + containerHeight - this.options.pageOffset) > viewport.height) {
		if (eval(positionY + this.options.pageOffset) > viewport.height) {
			this.container.setStyle({
				top: (viewport.height - this.options.pageOffset) + 'px'
			});
		}
		if (eval(positionY) < 0) {
			this.container.setStyle({
				top: '0px'
			});
		}
		if (eval(containerHeight) > viewport.height) {
			this.container.setStyle({
				height: (viewport.height) + 'px',
				top: '0px'
			});
		}
	},
	reOpen: function () {
		this.setLastPos();
		$(this.container).setStyle({visibility: 'visible', position: 'absolute'}).show();
		$(this.options.ident).show();
		$(this.frm).setStyle({visibility: 'visible'});
		if (this.options.ajaxLoad) {
			this.ajaxLoad();
		}
	},
	close: function (event) {
		if (event)Event.stop(event);

		this.removeFinal();
		if (this.options.onclose) {
			this.options.onclose();
		}
		if ($('ongl' + this.container.id)) $('ongl' + this.container.id).kill();
	},
	removeFinal: function () {
		if ($(this.container.id)) {
			$(this.container).kill();
			if (this.butonInTask) {
				if ($(this.butonInTask.id)) {
					$(this.butonInTask).kill();
				}
			}
		}
	},
	isReduced: function (event) {
		if (event) {
			Event.stop(event)
		}
		//this.container.className= 'containerdisp';
		new Effect.Fade($(this.container), {duration: 0.3})
		JSGUI.addButton({vars: this.vars, container: $(this.container), element_id: $(this.container).identify(), taskBar: 'taskBar', onglet_id: 'ongl' + this.container.id, title: this.title});
	}


}

