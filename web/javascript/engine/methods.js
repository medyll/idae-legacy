/*var $Element;

 function $_$(node){

 return $Element(document.getElementById(node));
 }
 $Element.have_method()({

 })
 $Element.add_method()({

 })*/
function prefixedCalc() {
	var prefixes = ["", "-webkit-", "-moz-", "-o-"], el
	for (var i = 0; i < prefixes.length; i++) {
		el = document.createElement('div')
		el.style.cssText = "width:" + prefixes[i] + "calc(9px)"
		if (el.style.length) return prefixes[i]
	}
}
Element.addMethods({
	kill: function (node) {
		try {
			$(node).purge()
		} catch (e) {
		}
		try {
			$(node).remove()
		} catch (e) {
		}
		try {
			$(node).purge()
		} catch (e) {
		}
	},
	doCheck: function (node) {
		node.checked = true;
		$(node).writeAttribute('bugchk', 'bugchk');
		$(node).writeAttribute('checked', true);
		if ($(node).up('tr')) $(node).up('tr').addClassName('selected')
	},
	doUnCheck: function (node) {
		node.checked = true;
		$(node).writeAttribute('bugchk', 'bugchk');
		$(node).writeAttribute('checked', false);
		$(node).writeAttribute('bugchk', false);
		if ($(node).up('tr')) $(node).up('tr').removeClassName('selected')
	},
	doRedim: function (node) {
		var element = node;
		if ($(element).up() == document)return element;
		rect = $(node).getBoundingClientRect();
		$(element).makePositioned();
		var daRedimParent = $($(element).parentNode); //up();
		$(daRedimParent).relativize();
		var parentHeight = $(daRedimParent).getHeight();
		$(daRedimParent).relativize();
		if (parentHeight != 0) {
			var newHeight = parentHeight - $(element).offsetTop
			// console.log(newHeight,rect.top);
			if (element.next('.stayDown')) {
				newHeight = newHeight - element.next('.stayDown').getHeight();
			}
			$(element).setStyle({height: newHeight + 'px'})
			$(element).childElements().each(function (node) { //select('.table')
				if ($(node).hasClassName('table')) {
					$(node).setStyle({height: newHeight + 'px'});
					$(node).select('>.cell').each(function (cell) {
						$(cell).setStyle({height: newHeight + 'px'})
					}.bind(this))
				}
			}.bind(this));
		}
		return element;
	},
	socketModule: function (node, file, davars, options) {
		switch (window.document.location.hostname) {
			case 'localhost':
				var DOCUMENTDOMAIN = window.document.location.href.replace(location.hash, "").replace('http://', "");
				break;
			case '192.168.11.44':
				var DOCUMENTDOMAIN = window.document.location.href.replace(location.hash, "").replace('http://', "");
				break;
			default:
				var DOCUMENTDOMAIN = window.document.location.host;
				break;
		}
		var request_id = uniqid('app_cache_load');
		if (!$(node)) {
			console.log('pas node', node);
			return;
		}
		if ($(node).requesting) {
			// $(node).makeLoading();
			// return;
		}

		var key_name = build_cache_key(file, davars);

		var element = $(node),
			vars = davars || '',
			ajaxOption = {request_id: request_id, cache: false, key_name: key_name};

		var id_l = 'loading_loader_' + $(element).identify();

		var options = Object.extend(ajaxOption, options || {});

		$(element).writeAttribute('vars', vars);
		$(element).writeAttribute('mdl', file);
		$(element).writeAttribute('data-request_id', request_id);

		// console.log('options cache =>  ',file , options);

		// CACHE : detection de l'attribut
		if ($(element).readAttribute('data-cache')) {
			options.cache = true;
		}
		if (options.cache  && !$(element).readAttribute('data-cache')) {
			$(element).writeAttribute('data-cache', 'true');
		} //  && $(element).readAttribute('data-cache')=='true'
		//

		if (!$(element)) {
			console.log('pas element', element);
			return;
		}

		$(element).insert({top: '<div class="none noneinfinite_loader absolute" id="' + id_l + '"></div>'});

		if ($(element).tagName.toUpperCase() == 'DIV' && (options.seeLoading)) {
			// $(element).insert({bottom:'<div class="loading" style="height:100%;width:'+$(element).getWidth()+'px;top:0;left:0;z-index:4000;position:absolute;"></div>'});
		}

		var opt = {
			DOCUMENTDOMAIN: DOCUMENTDOMAIN,
			element: element.identify(),
			file: file,
			vars: vars,
			options: options
		}
		if ($('SESSID')) opt.SESSID = $('SESSID').value;
		/*if (localStorage.getItem('SSSAVEPATH')) {
			opt.SSSAVEPATH = localStorage.getItem('SSSAVEPATH');
		}*/
		if (localStorage.getItem('PHPSESSID')) {
			opt.PHPSESSID = localStorage.getItem('PHPSESSID');
		}
		if (localStorage.getItem('SESSID')) {
			opt.SESSID = localStorage.getItem('SESSID');
		}

		// CACHE

		if ((localStorage.getItem('cache_mode') == 'on')) {
			// console.log('cache ', key_name, options.cache);

			if (options.cache == true) { // && file.indexOf("select")  == -1 &&  file.indexOf("update")  == -1
				app_cache.getItem(key_name, function (err, value) {
					// console.log( 'localforage cache_mode' , 'key_name',key_name,'value',value);
					if (err) {
						console.error('Oh noes!');
					} else {
						// console.log('test from cache');
						if (!empty(value) && !empty(value.data_body)) {
							//  console.log(file+' from cache');
							element.update(value.data_body);
							// element.insert({top: '<div class="fond_noir color_fond_noir padding absolute"><i class="fa fa-cog"></i></div>'});
							$(element).writeAttribute('data-from_cache', 'true');
							afterAjaxCall($(element));
							$(element).fire('content:loaded');
							socket.emit('socketModule', opt);
							return $(element);
						} else {
							// console.log('please do from cache');
							$(element).writeAttribute('data-need_cache', 'true');
						}
					}
				});

			}

		} else {

		}
		//
		$(node).requesting = true;
		socket.emit('socketModule', opt, function (result) {
			if (options.onComplete) {
				var onComplete = options.onComplete
				if (Object.isFunction(onComplete)) onComplete(result);
			}
			$(node).requesting = false;
			$(node).undoLoading();
		}.bind(this));
		//
		/*  if (typeof socket != 'object') {

		 }
		 if (typeof Element.socketModule == 'undefined') {
		 console.log('undefined Element.socketModule ',key_name);
		 setTimeout(function () {
		 element.socketModule(file, vars, options);
		 }.bind(this), 10)
		 return $(element);
		 }
		 if (typeof socket != 'object') {
		 console.log('undefined socketModule ',key_name);
		 setTimeout(function () {
		 element.socketModule(file, vars, options);
		 }.bind(this), 100)
		 return $(element);
		 }*/

		//


		/* var ajaxOption = {
		 method: 'post',
		 evalScripts: true,
		 sanitizeJSON: false,
		 requestHeaders: ['Content-type', 'application/x-www-form-urlencoded','charset','UTF-8'],
		 parameters: vars
		 }
		 myAjax = new Ajax.Updater( element.identify(),'http://'+window.document.location.host+'/mdl/'+ file+'.php', ajaxOption );*/

		return $(element);
	},
	loadFragment: function (node, fragment, vars, options) {
		options = options || {}
		vars = vars || ''
		options = Object.extend({fragment: fragment}, options || {});
		var out = $($(node).readAttribute('data-app_fragment-target')) || $(node).up('[mdl]').select('[data-app_fragment=' + fragment + ']').first()
		var file = $(node).up('[mdl]').readAttribute('mdl')

		return $(out).loadModule(file, vars, options);
		;
	},
	loadModule: function (node, file, vars, options) {
		//ajaxOption = {}
		//options = Object.extend(ajaxOption, options || {});
		// ajaxInMdl => onglet ... => socketModule
		ajaxInMdl(file, node, vars, options);
		return $(node);
	},
	animateCss: function (node, animationName, fn,args) {
		var animationEnd = 'animationend';
		args = args || {};
		node.addClassName('animated ' + animationName).on(animationEnd, function () {
			$(node).removeClassName('animated').removeClassName(animationName);
			if (fn) {
				switch (fn) {
					case 'remove':
						//fn = Element.remove;
						break;
					case 'hide':
						//fn = Element.hide;
						break;
					case 'show':
						//fn = Element.show;
						break;
				}
				if (Object.isFunction(Element.Methods[fn])) Element[fn](node)(args);
				//if (Object.isFunction(fn)) fn(node);
			}

		}.bind(this));
		return $(node);
	},
	toggleContent: function (element) {
		frm = $(element);
		test = $(element).getStyle('position')
		$(element).show().setStyle({position: 'absolute'})
		Element.cleanWhitespace(frm.parentNode);
		toSwitch = $A(frm.parentNode.childNodes);
		$A(toSwitch).without(frm).each(function (node) {
			if (!$(node).hasClassName('avoid')) {
				$(node).hide();
			}
		})
		$(element).show().setStyle({position: test})
		return $(element);
	},
	unToggleContent: function (element) {
		frm = $(element);
		Element.cleanWhitespace(frm.parentNode);
		toSwitch = frm.parentNode.childNodes;
		$(element).hide();
		for (i = 0; i < toSwitch.length; i++) {
			if (!Element.hasClassName(toSwitch[i], 'avoid') && toSwitch[i] != frm)Element.show(toSwitch[i]);
		}
		return $(element);
	},
	cloneCopy: function (source, target, options) {
		options = Object.extend({offsetLeft: ''}, options || {});
		target.oldValue = target.value;
		var append = options.append || null;
		if (options.spy) {
			$(options.spy).observe('click', function () {
				if ($F(options.spy) == 'on') {
					target.oldValue = target.value;
					target.value = source.value
				} else {
					target.value = target.oldValue
				}
			})
		}
		$(source).observe('keyup', function () {
			if (options.spy) {
				if ($F(options.spy)) {
					target.value = append + source.value
				}
			}
		}.bind(this), true)
		$(source).observe('change', function () {
			if ($F(options.spy)) {
				target.value = append + source.value
			}
		}.bind(this), true)
		$(source).observe('click', function () {
			if ($F(options.spy)) {
				target.value = append + source.value
			}
		}.bind(this), true)
	},
	unCloneCopy: function (source, target) {
		$(source).stopObserving('keyup', function () {
			target.value = source.value
		}.bind(this), true)
		$(source).stopObserving('change', function () {
			target.value = source.value
		}.bind(this), true)
	},
	makeLoading: function (frm) {
		$(frm).identify();
		$(frm).insert({top: '<div id="load' + $(frm).id + '" style="width:100%;height:100%;position:absolute;overflow:hidden;display:table-cell;vertical-align: middle;z-index:500;" class="transpblanc"><div class="loading"></div> </div>'})
		$('load' + $(frm).id).show();
		$('load' + $(frm).id).makeOnTop();
		$(frm).loader = function () {
			return $('load' + $(frm).id);
		}
		return $(frm);
	},
	makeModal: function (frm) {
		new Insertion.Top($(frm), '<div id="load' + $(frm).id + '" style="width:100%height:100%;display:block;position:absolute;overflow:hidden;opacity:0.1;z-index:500;" class="noir"><div class="isModal"></div> </div>');
		$('load' + $(frm).id).show();
		$('load' + $(frm).id).makeOnTop();
		$(frm).modal = function () {
			return $('load' + $(frm).id);
		}
		return $(frm);
	},
	undoLoading: function (element) {
		if (!$('load' + element.id)) {
			return false;
		}
		else {
			$('load' + element.id).remove()
		}
	},
	makeOnTop: function (element) {
		tempZindex = $(element).siblings().invoke("getStyle", "z-index").max();
		tempZindex = parseInt(tempZindex) || 1;
		$(element).setStyle('z-index:' + eval(tempZindex + 1));
		//console.log(element);
		return $(element);
	},
	Fade: function (element, speed) {
		var s = element.style;
		if (!speed) var speed = 100;
		s.opacity = 1;
		(function do_fade() {
			(s.opacity -= .1) < .1 ? s.display = "none" : setTimeout(do_fade, speed)
		})();

		return $(element);
	},
	Appear: function (element) {
		if (element.visible() != true) {
			new Effect.Appear(element);
		}
		return $(element);
	},
	SlideDown: function (element) {
		if (element.visible() != true) {
			new Effect.SlideDown(element, {duration: 0.2});
		}
		return $(element);
	},
	SlideUp: function (element) {
		if (element.visible() == true) {
			new Effect.SlideUp(element, {duration: 0.2});
		}
		return $(element);
	},
	Print: function (element, options) {
		options = Object.extend({cssSheet: true}, options || {});

		var frame = document.createElement('iframe');
		$(frame).setStyle({positon: 'absolute', visibility: 'hidden'})
		$('body').appendChild(frame)
		$(frame).observe('load', function (element, event) {
			$(frame).focus()
			// 	console.log(frame.contentWindow.document.body)
			$(frame).contentWindow.document.body.innerHTML = $(element).innerHTML
			// $(frame).CopyStyle($H($(frame)).contentWindow.document.body)
			$(frame).contentWindow.document.title = 'Impressionencours'
			$(frame).focus()
			$H($(frame)).contentWindow.window.print()
			$(frame).remove
		}.bind(this))
		$(frame).src = "blank.html"
		$(frame).target = "_self"
	},
	toggleSrc: function (element, firstSrc, secondSrc) {
		element = $(element);
		test = element.src.replace(secondSrc, firstSrc);
		if (test == element.src) {
			element.src = element.src.replace(firstSrc, secondSrc);
			element.src = element.src.replace(firstSrc, secondSrc);
		}
		else {
			element.src = element.src.replace(secondSrc, firstSrc);
			element.src = element.src.replace(secondSrc, firstSrc);
		}
		Element.show(element)
		return $(element);
	}
});
Array.prototype.chunk = function(j)  { return this.reduce((a,b,i,g) => !(i % j) ? a.concat([g.slice(i,i+j)]) : a, []);}

window.getInnerWidth = function () {
	if (window.innerWidth) {
		return window.innerWidth;
	} else if (document.body.clientWidth) {
		return document.body.clientWidth;
	} else if (document.documentElement.clientWidth) {
		return document.documentElement.clientWidth;
	}
}

window.getInnerHeight = function () {
	if (window.innerHeight) {
		return window.innerHeight;
	} else if (document.body.clientHeight) {
		return document.body.clientHeight;
	} else if (document.documentElement.clientHeight) {
		return document.documentElement.clientHeight;
	}
}


