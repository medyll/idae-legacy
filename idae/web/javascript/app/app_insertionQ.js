/**
 * Created by Mydde on 21/12/2015.
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 *
 * This file is the registry of insertionQ watchers: every selector below is
 * wired the moment a matching node lands in the DOM, which is how
 * server-rendered fragments get behaviour without anyone re-running an init.
 * It is the piece that made the shim necessary in the first place — nodes
 * arrive after page load and still have to be usable.
 *
 * Still calling Element methods on purpose: socketModule, loadModule,
 * toggleContent, doRedim. Those are this app's own API from
 * engine/methods.js, not Prototype's, and they migrate with that file.
 */

/* -------------------------------------------------------------------- *
 * DOM helpers — file-local, same rationale as myddeExplorer.js: hoisting *
 * them into a shared module would mean touching main_bag.js's load       *
 * graph, which phase 5 has no reason to disturb.                         *
 * -------------------------------------------------------------------- */

function iq_qsa(root, selector) {
	if ( !root ) return [];
	return Array.prototype.slice.call (root.querySelectorAll (selector));
}

function iq_identify(node) {
	if ( !node.id ) node.id = uniqid ('anonymous_element');
	return node.id;
}

function iq_show(node) {
	if ( node ) node.style.display = '';
	return node;
}

function iq_hide(node) {
	if ( node ) node.style.display = 'none';
	return node;
}

/** Prototype's Element#visible(). */
function iq_visible(node) {
	return !!node && node.style.display !== 'none';
}

/** Prototype's Element#up(selector): starts at the parent, never at self. */
function iq_up(node, selector) {
	if ( !node || !node.parentElement ) return null;
	return selector ? node.parentElement.closest (selector) : node.parentElement;
}

/** Event delegation, Prototype's Element#on(event, selector, handler). */
function iq_delegate(root, eventName, selector, handler) {
	root.addEventListener (eventName, function (event) {
		var target = event.target;
		while ( target && target !== root ) {
			if ( target.nodeType === 1 && target.matches (selector) ) {
				return handler (event, target);
			}
			target = target.parentNode;
		}
	}, false);
}

/** Prototype's Element#fire: a bubbling CustomEvent carrying `memo`. */
function iq_fire(node, eventName, memo) {
	if ( !node ) return null;
	var event  = new CustomEvent (eventName, { bubbles : true, cancelable : true });
	event.memo = memo || {};
	node.dispatchEvent (event);
	return event;
}

function iq_stripTags(html) {
	return String (html).replace (/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
}

function iq_cleanWhitespace(node) {
	if ( !node ) return node;
	var child = node.firstChild;
	while ( child ) {
		var next = child.nextSibling;
		if ( child.nodeType === 3 && !/\S/.test (child.nodeValue) ) node.removeChild (child);
		child = next;
	}
	return node;
}

/** Sum of offsetTop/offsetLeft up the offsetParent chain (Position.cumulativeOffset). */
function iq_cumulativeOffset(element) {
	var top = 0, left = 0;
	do {
		top += element.offsetTop || 0;
		left += element.offsetLeft || 0;
		element = element.offsetParent;
	} while ( element );
	return { left : left, top : top };
}

/** Sum of scrollTop/scrollLeft up the parent chain (Position.cumulativeScrollOffset). */
function iq_cumulativeScrollOffset(element) {
	var top = 0, left = 0;
	do {
		top += element.scrollTop || 0;
		left += element.scrollLeft || 0;
		element = element.parentNode;
	} while ( element );
	return { left : left, top : top };
}

/**
 * Prototype's Element#clonePosition — place `target` over `source`.
 *
 * Deliberately not idae-be's clonePosition: that one offsets by transform and
 * takes different options, and the only caller here (the leave-planning bar
 * below) depends on the top/left/width/height flavour.
 */
function iq_clonePosition(target, source, options) {
	options = Object.assign ({
		setLeft : true, setTop : true, setWidth : true, setHeight : true,
		offsetTop : 0, offsetLeft : 0
	}, options || {});

	var p     = iq_cumulativeOffset (source);
	var delta = { left : 0, top : 0 };
	if ( window.getComputedStyle (target).position === 'absolute' ) {
		delta = iq_cumulativeOffset (target.offsetParent || document.documentElement);
	}

	if ( options.setLeft ) target.style.left = (p.left - delta.left + options.offsetLeft) + 'px';
	if ( options.setTop ) target.style.top = (p.top - delta.top + options.offsetTop) + 'px';
	if ( options.setWidth ) target.style.width = source.offsetWidth + 'px';
	if ( options.setHeight ) target.style.height = source.offsetHeight + 'px';
	return target;
}

/* -------------------------------------------------------------------- */

function chkDispZone2(frm, event) {
	this.container      = frm
	this.pageOffset     = 10;
	var viewportWidth   = document.documentElement.clientWidth,
	    viewportHeight  = document.documentElement.clientHeight,
	    scrollTop       = window.pageYOffset,
	    containerWidth  = this.container.offsetWidth,
	    containerHeight = this.container.offsetHeight;

	var positionX = (event) ? event.pageX : parseInt (frm.style.left);
	var positionY = (event) ? event.pageY : frm.offsetTop;

	this.container.style.left = ((positionX + containerWidth + this.pageOffset) > viewportWidth ? (viewportWidth - containerWidth - this.pageOffset) : positionX) + 'px';
	this.container.style.top = ((positionY - scrollTop + containerHeight) > viewportHeight && (positionY - scrollTop) > containerHeight ? (positionY - containerHeight) : positionY) + 'px';

	iq_show (this.container);
}

function QsLoad() {
	// alert('red')

	insertionQ.config ({
		strictlyNew : true,
		timeout     : 20
	});
	insertionQ ('[data-reloader]').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			node.setAttribute ('masked', 'true');
			var parent = iq_up (node, '.cf_module');
			var mdl    = node.getAttribute ('mdl');
			var vars   = node.getAttribute ('vars');

			node.innerHTML = '<i class="fa fa-reload"></i>';
			node.addEventListener ('click', function () {

				parent.loadModule (mdl, vars)
			})
		}
	})
	insertionQ ('[data-linker]').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			node.setAttribute ('masked', 'true');
			var linker      = document.getElementById (node.getAttribute ('data-linker'));
			var linker_mdl  = node.getAttribute ('data-linker_mdl');
			var linker_item = node.getAttribute ('data-linker_item');

			iq_delegate (node, 'click', linker_item, function (event, node) {
				var linker_vars = node.getAttribute ('data-vars');
				linker.loadModule (linker_mdl, linker_vars)
			})
		}

	});

	insertionQ ('[data-dsp_liste]').every (function (node) {
		//  console.log(['data-dsp_liste'] ,node)
		if ( node.getAttribute ('masked') == null ) {
			node.setAttribute ('masked', 'true');
			var vars = node.getAttribute ('data-vars');
			load_table_in_zone (vars, node);
		}

	});
	insertionQ ('[swiper]').every (function (node) {

		var mySwiper = new Swiper (node, {
			// Optional parameters
			/*loop: true,*/
			pagination          : '.swiper-pagination',
			slidesPerView       : 'auto',
			/*centeredSlides: true,*/
			paginationClickable : true,
			freeModeSticky      : true,
			grabCursor          : true,
			spaceBetween        : 30,
			slideClass          : 'cool',
			// Navigation arrows
			nextButton          : '.swiper-button-next',
			prevButton          : '.swiper-button-prev',
			// And if we need scrollbar
			scrollbar           : '.swiper-scrollbar',
		})

	});
	insertionQ ('[data-table]').every (function (node) {
		window.data_subscribe[node.getAttribute ('data-table')] = 1;
	});
	insertionQ ('.click_up').every (function (node) {
		if ( node.querySelector ('[data-set_value]') ) {
			var set_value = node.querySelector ('[data-set_value]').getAttribute ('data-set_value');
			// Injected into an onclick attribute, so it runs in page context
			// later — it has to be native too, or this file stops calling the
			// shims while quietly leaving one behind in a string.
			var set_value_str = "this.querySelector('[data-set_value]').getAttribute('data-set_value')";
			var parent = node.parentElement;
			if ( parent ) {
				parent.classList.add ('cursor');
				parent.setAttribute ('onclick', node.getAttribute ('onclick').replace ('set_value', set_value_str));
			}
			node.setAttribute ('onclick', '');
			node.classList.remove ('click_up');
		}
	});
	insertionQ ('[data-quickFind]').every (function (node) {
		var opt = {
			where  : node.getAttribute ('data-quickFind-where') || null,
			tag    : node.getAttribute ('data-quickFind-tag') || null,
			parent : node.getAttribute ('data-quickFind-parent') || false,
			spy    : node.getAttribute ('data-quickFind-spy') || false,
			post   : node.getAttribute ('data-quickFind-post') || false
		}
		var qhF = new QuickFind (node, opt);

	});

	insertionQ ('.hide_on_click').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			node.setAttribute ('masked', 'true');
			node.addEventListener ('click', function (event) {
				// console.log ('.hide_on_click', event.target)
				var renode = event.target;
				if ( renode.classList && renode.classList.contains ('avoid') ) return;
				if ( iq_up (renode, '.avoid') ) {
					console.log ('.avoided ', event.target);
					return;
				}
				node.style.visibility = 'hidden';
				setTimeout (function () {
					iq_hide (node);
					node.style.visibility = 'visible';
				}.bind (this), 450);
			}.bind (this))
		}
	});

	insertionQ ('[data-count]').every (function (node) {
		var vars  = node.getAttribute ('data-vars');
		var table = node.getAttribute ('data-table');
		if ( node.getAttribute ('data-count_auto') ) { //  node.hasChildNodes () == false
			runModule ('services/json_data_table', 'table=' + table + '&' + vars + '&piece=count&count_id=' + iq_identify (node));
		}

	});
	insertionQ ('[data-setting]').every (function (node) {
		var settings_key  = node.getAttribute ('data-setting') || null;
		var value         = node.getAttribute ('data-setting-value') || null;
		var mode          = node.getAttribute ('data-setting-mode') || null;
		var method        = node.getAttribute ('data-setting-method') || null;
		var setting_apply = node.getAttribute ('data-setting-apply') || null;

		//console.log ('settings : ', settings_key, mode, value, method);
		switch (mode) {
			case 'display':
				localforage.getItem (settings_key, function (err, value) {
				//	console.log (err, value)
				}).then (function (finval) {
				// 	console.log ('FIN', finval)
				})
				if ( setting_apply ) {
					node.style.display = value;
				}
				break;

			default:
				break;
		}
		if ( method ) {
			node.addEventListener (method, function () {
				// console.log(method, value);
				ajaxValidation ('set_settings', 'mdl/app/', 'key=' + settings_key + '&value=' + value);
			}.bind (this))
		}
		//	node.socketModule(file, vars);
		// console.log('preload ');
	});
	/*insertionQ('[data-contextual]').every(function (node) {
	 var vars = node.getAttribute('data-contextual');
	 var file = 'app/app_contextual/app_contextual';
	 console.log('preload ');
	 });*/
	//// insertionQ
	insertionQ ('.mastershow:hover .slaveshow').every (function (node) {
		// alert('red');
		if ( window.getComputedStyle (node).position == 'absolute' ) {
			setTimeout (function () {
				chkDispZone2 (node)
			}, 110);
		}
	});
	insertionQ ('[data-idtache]').every (function (node) {
		//
		//
		/*new Resizeable(node,{
		 top: 0,left: 0,right: 0,
		 parent: node.parentElement,
		 resize: function(el) {
		 datedebut   = node.getAttribute('datedebut');
		 heuredebut  = node.getAttribute('heuredebut');
		 idtache  = node.getAttribute('data-idtache');

		 height = eval(node.offsetHeight) / 20 ;
		 height = Math.round(height) * 20;
		 node.style.height = height+'px';
		 ajaxValidation('app_update','mdl/app/','table=tache&table_value='+idtache+'&vars[heureFinTache]='+(height/20))
		 }
		 });*/
	});
	insertionQ ('[data-dragconge]').every (function (node) {
		//
		var data_parent_id = node.getAttribute ('data-parent');

		if ( data_parent_id && document.getElementById (data_parent_id) && node.getAttribute ('data-dragconge') && node.getAttribute ('data-idagent') ) {

			if ( node.getAttribute ('data-dateDebut') && node.getAttribute ('data-dateFin') ) {

				if ( node.getAttribute ('data-heureDebut') && node.getAttribute ('data-heureFin') ) {

					var data_parent = document.getElementById (data_parent_id),
					    dateDebut   = node.getAttribute ('data-datedebut'),
					    heureDebut  = node.getAttribute ('data-heuredebut'),
					    dateFin     = node.getAttribute ('data-datefin'),
					    heureFin    = node.getAttribute ('data-heurefin'),
					    idagent     = node.getAttribute ('data-idagent');

					var selector_zone     = '[data-dropzone="conge"][heureDebut="' + heureDebut + '"][dateDebut="' + dateDebut + '"][data-idagent="' + idagent + '"]';
					var selector_zone_fin = '[data-dropzone="conge"][heureDebut="' + heureFin + '"][dateDebut="' + dateFin + '"][data-idagent="' + idagent + '"]';

					var zones     = iq_qsa (data_parent, selector_zone);
					var zones_fin = iq_qsa (data_parent, selector_zone_fin);

					if ( zones.length == 0 && zones_fin.length == 0 ) { node.remove (); return;}

					var all_zones = iq_qsa (data_parent, '[data-dropzone="conge"][heureDebut][dateDebut][data-idagent="' + idagent + '"]');
					var first_node = zones.length == 0 ? all_zones[0] : zones[0];
					var last_node  = zones_fin.length == 0 ? all_zones[all_zones.length - 1] : zones_fin[0];

					var offsets = iq_cumulativeScrollOffset (first_node);

					iq_clonePosition (node, first_node);

					var tmp_end_clone       = document.createElement ('div');
					tmp_end_clone.className = 'absolute';
					data_parent.appendChild (tmp_end_clone);
					iq_clonePosition (tmp_end_clone, last_node);

					node.style.marginTop = offsets.top + 'px';
					node.style.width     = (offsets.left + tmp_end_clone.offsetLeft + tmp_end_clone.offsetWidth - node.offsetLeft ) + 'px';

				}
			}
		}
	});
	insertionQ ('[data-dyn_datetime]').every (function (element) {
		if ( element.getAttribute ('masked') == null ) {
			element.addEventListener ('click', function () {
				getDuree (element)
			}.bind (this));
			element.addEventListener ('change', function () {
				getDuree (element)
			}.bind (this));
			element.setAttribute ('masked', 'true');
		}
	});
	insertionQ ('.heure').every (function (element) {
		// if (element.matches('.heure')) {
		if ( element.getAttribute ('masked') == null ) {
			var oDateMask = new Mask ("##:##:##");
			oDateMask.attach (element);
			//
			element.setAttribute ('datalist', 'app/app_select_heure');
			element.style.type = 'text';

			new myddeDatalist (element);

			element.setAttribute ('masked', 'true');
		}
	});
	insertionQ ('.inputInline').every (function (node) {
		new resizeInput (node);
	});
	insertionQ ('form').every (function (node) {
		node.setAttribute ('autocomplete', 'off');
	});
	insertionQ ('[autofocus]').every (function (node) {
		node.focus ();
	});
	insertionQ ('[data-icon_select]').every (function (node) {
		node.focus ();
	});
	insertionQ ('.inputDate').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			var oDateMask = new Mask ("##/##/####");
			oDateMask.attach (node);
			node.setAttribute ('masked', 'true');
		}
	});
	insertionQ ('.validate-date-au').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			var id_trig      = uniqid ();
			var trigger      = document.createElement ('i');
			trigger.id        = id_trig;
			trigger.className = 'fa fa-calendar textgris';
			node.after (trigger);// '<i class="fa fa-calendar textgris"></i>'
			var oDateMask = new Mask ("##/##/####");
			oDateMask.attach (node);
			node.setAttribute ('masked', 'true');
			//
			var picker = new Pikaday ({
				field    : node,
				trigger  : trigger,
				format   : 'DD/MM/YYYY',
				onSelect : function () {
					node.value = this.getMoment ().format ('DD/MM/YYYY');
					iq_fire (node, 'dom:act_change')
				}
			});
		}
	});
	insertionQ ('.toggler').every (function (node) {
		new autoToggle (node);
	});
	insertionQ ('table.act_sort').every (function (node) {
		new sortableTable (node); // old way
		node.classList.remove ('act_sort');
	});

	insertionQ ('textarea[ext_mce_textarea]').every (function (node) {
		mce_area ('#' + iq_identify (node));
	});
	insertionQ ('[act_target]').every (function (node) {
		var act_target         = node.getAttribute ('act_target');
		var mdl                = node.getAttribute ('mdl');
		var vars               = node.getAttribute ('vars') || '';
		var boundHandlerMethod = function () {
			var target = document.getElementById (act_target);
			if ( !target ) return;
			iq_show (target);
			if ( node.getAttribute ('mdl') ) {
				target.socketModule (mdl, vars);
			}

			if ( target.getAttribute ('data-act_target_toggle') ) {
				target.toggleContent ();
			}
		};
		node.addEventListener ('click', boundHandlerMethod.bind (this), true);
	});
	insertionQ ('[act_chrome_gui]').every (function (node) {
		var options = '{}';
		var mdl     = node.getAttribute ('act_chrome_gui');
		var vars    = node.getAttribute ('vars') || '';
		var vars    = vars || node.getAttribute ('data-vars');
		if ( node.getAttribute ('options') != 'undefined' ) {
			options = node.getAttribute ('options');
		}
		// data-cache="true"
		var onclick = "act_chrome_gui('" + mdl + "','" + vars + "'," + options + ")";
		node.setAttribute ('onclick', onclick);
	});

	insertionQ ('[act_chrome_ingui]').every (function (node) {
		var options = '{}';
		var mdl     = node.getAttribute ('act_chrome_ingui');
		var vars    = node.getAttribute ('vars') || '';
		var titre   = node.getAttribute ('titre') || '';
		if ( node.getAttribute ('options') != 'undefined' ) {
			options = node.getAttribute ('options');
		}

		var onclick = "ajaxInMdl('" + mdl + "','tmp_" + mdl + "_frame','" + vars + "'," + options + ")";
		node.setAttribute ('onclick', onclick);
	}); //

	insertionQ ('.cf_module ').every (function (node) {
		node.setAttribute ('title', node.getAttribute ('mdl'));
	});

	insertionQ ('[main_auto_tree]').every (function (node) {
		new app_tree (node);
	});
	insertionQ ('[auto_tree]').every (function (node) {
		                                  var ct = '', order = 0;
		                                  iq_cleanWhitespace (node);
		                                  if ( node.getAttribute ('auto_tree_count') ) {
			                                  ct = node.getAttribute ('auto_tree_count');
		                                  }
		                                  if ( node.getAttribute ('right') ) order = 3;
		                                  if ( node.children.length == 1 ) {
			                                  var firstDescendant = node.firstElementChild;
			                                  firstDescendant.style.order = 1;
			                                  firstDescendant.style.width = '100%';
			                                  firstDescendant.classList.add ('flex_main');
		                                  }
		                                  node.insertAdjacentHTML ('beforeend', '<div style="order:' + order + '" class="auto_tree_caret avoid"></div>');
		                                  node.insertAdjacentHTML ('beforeend', '<div auto_tree_count style="order:2" >' + ct + '</div>');
		                                  node.classList.add ('auto_tree');
		                                  node.removeAttribute ('auto_tree');

		                                  /* node.innerHTML = '<div style="order:' + order + '" class="auto_tree_caret avoid"></div><div class="" style="order:1;">' + node.innerHTML + '</div><div auto_tree_count style="order:2" >' + ct + '</div>';
		                                   node.classList.add('auto_tree'); node.removeAttribute('auto_tree');
		                                   node.removeAttribute('auto_tree_count');*/

		                                  if ( node.nextElementSibling ) {
			                                  var inner_node = iq_stripTags (node.nextElementSibling.innerHTML).trim ();
			                                  if ( inner_node.length == 0 ) { node.classList.add ('hidden_caret')}
			                                  node.nextElementSibling.classList.add ('auto_tree_next')
			                                  if ( iq_visible (node.nextElementSibling) ) {
				                                  node.classList.add ('opened')
			                                  } else {
				                                  node.classList.remove ('opened')
			                                  }
		                                  }

	                                  }
	)
	;
	insertionQ ('[data-app_calendrier]').every (function (node) { // => pikaday.js

		node.removeAttribute ('data-app_calendrier');

		new app_calendrier (node);

	});
	insertionQ ('[datalist]').every (function (node) {

		node.setAttribute ('autocomplete', 'off');
		node.setAttribute ('type', 'text');

		new myddeDatalist (node)
	});
	insertionQ ('[act_defer]').every (function (node) {
		if ( node.getAttribute ('masked') == null ) {
			node.setAttribute ('masked', 'true');
			// console.log(node)
			var el      = node;
			var options = {};
			el.removeAttribute ('act_defer');
			act_mdl     = el.getAttribute ('mdl');
			act_vars    = el.getAttribute ('vars') || '';
			value       = el.getAttribute ('value') || iq_identify (el);
			if ( el.getAttribute ('data-json_options') ) {
				options = json_decode (el.getAttribute ('data-json_options'));
				console.log ('json_options', options)
			}
			if ( el.getAttribute ('data-cache') ) {
				options['cache'] = true;
			}
			el.socketModule (act_mdl, act_vars, options);

		}
	});

	// appgui
	insertionQ ('[app_gui_flowdown]').every (function (node) {
		//
		node.doRedim ();
		node.addEventListener ('content:loaded', function () {
			if ( node.timer ) clearTimeout (node.timer);
			node.timer = setTimeout (function () {

			}.bind (this), 0);
		}.bind (this));

		//})
	});

	insertionQ ('[app_gui_explorer]').every (function (node) {
		new myddeExplorer (node);
	});

	return true;
}
