/**
 * Created by Mydde on 21/12/2015.
 */
function chkDispZone2(frm, event) {
	this.container      = frm
	this.pageOffset     = 10;
	var viewport        = document.viewport.getDimensions (),
	    offset          = document.viewport.getScrollOffsets (),
	    containerWidth  = this.container.getWidth (),
	    containerHeight = this.container.getHeight ();

	var positionX = (event) ? event.pageX : parseInt ($ (frm).style.left);
	var positionY = (event) ? event.pageY : $ (frm).offsetTop;
	;

	this.container.setStyle ({
		left : ((positionX + containerWidth + this.pageOffset) > viewport.width ? (viewport.width - containerWidth - this.pageOffset) : positionX) + 'px',
		top  : ((positionY - offset.top + containerHeight) > viewport.height && (positionY - offset.top) > containerHeight ? (positionY - containerHeight) : positionY) + 'px'
	});

	this.container.show ();
}

function QsLoad() {
	// alert('red')

	insertionQ.config ({
		strictlyNew : true,
		timeout     : 20
	});
	insertionQ ('[data-reloader]').every (function (node) {
		var node_elem = $ (node);
		if ( $ (node_elem).readAttribute ('masked') == null ) {
			$ (node_elem).writeAttribute ('masked', 'true');
			var parent = node_elem.up ('.cf_module');
			var mdl    = node_elem.readAttribute ('mdl');
			var vars   = node.readAttribute ('vars');

			node_elem.update ('<i class="fa fa-reload"></i>');
			$ (node_elem).on ('click', function (node) {

				parent.loadModule (mdl, vars)
			})
		}
	})
	insertionQ ('[data-linker]').every (function (node) {
		var node_elem = $ (node);
		if ( $ (node_elem).readAttribute ('masked') == null ) {
			$ (node_elem).writeAttribute ('masked', 'true');
			var linker      = $ (node_elem.readAttribute ('data-linker'));
			var linker_mdl  = node_elem.readAttribute ('data-linker_mdl');
			var linker_item = node_elem.readAttribute ('data-linker_item');

			$ (node_elem).on ('click', linker_item, function (event, node) {
				var linker_vars = node.readAttribute ('data-vars');
				linker.loadModule (linker_mdl, linker_vars)
			})
		}

	});

	insertionQ ('[data-dsp_liste]').every (function (node) {
		//  console.log(['data-dsp_liste'] ,node)
		var node_elem = $ (node);
		if ( $ (node_elem).readAttribute ('masked') == null ) {
			$ (node_elem).writeAttribute ('masked', 'true');
			var vars = node_elem.readAttribute ('data-vars');
			load_table_in_zone (vars, node_elem);
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
		window.data_subscribe[node.readAttribute ('data-table')] = 1;
	});
	insertionQ ('.click_up').every (function (node) {
        if($(node).down('[data-set_value]')){
            var set_value = $(node).down('[data-set_value]').readAttribute('data-set_value');
            var set_value_str = "$(this).down('[data-set_value]').readAttribute('data-set_value')";
            $(node).up().addClassName('cursor').setAttribute('onclick',$(node).readAttribute('onclick').replace('set_value',set_value_str))
            $(node).setAttribute('onclick','');
            $(node).removeClassName('click_up');
        }
	});
	insertionQ ('[data-quickFind]').every (function (node) {
		var opt = {
			where  : $ (node).readAttribute ('data-quickFind-where') || null,
			tag    : $ (node).readAttribute ('data-quickFind-tag') || null,
			parent : $ (node).readAttribute ('data-quickFind-parent') || false,
			spy    : $ (node).readAttribute ('data-quickFind-spy') || false,
			post   : $ (node).readAttribute ('data-quickFind-post') || false
		}
		var qhF = new QuickFind (node, opt);

	});

	insertionQ ('.hide_on_click').every (function (node) {
		if ( $ (node).readAttribute ('masked') == null ) {
			$ (node).writeAttribute ('masked', 'true');
			$ (node).on ('click', function (renode) {
				// console.log ('.hide_on_click', renode.target)
				var renode = $ (renode.target);
				if ( $ (renode).hasClassName ('avoid') ) return;
				if ( $ (renode).up ('.avoid') ) {
					console.log ('.avoided ', renode.target);
					return;
				}
				$ (node).setStyle ({ visibility : 'hidden' })
				setTimeout (function () {
					$ (node).hide ().setStyle ({ visibility : 'visible' });
				}.bind (this), 450);
			}.bind (this))
		}
	});

	insertionQ ('[data-count]').every (function (node) {
		var vars  = $ (node).readAttribute ('data-vars');
		var table = $ (node).readAttribute ('data-table');
		if ( $ (node).readAttribute ('data-count_auto') ) { //  $ (node).hasChildNodes () == false
			runModule ('services/json_data_table', 'table=' + table + '&' + vars + '&piece=count&count_id=' + node.identify ());
		}

	});
	insertionQ ('[data-setting]').every (function (node) {
		var settings_key  = node.readAttribute ('data-setting') || null;
		var value         = node.readAttribute ('data-setting-value') || null;
		var mode          = node.readAttribute ('data-setting-mode') || null;
		var method        = node.readAttribute ('data-setting-method') || null;
		var setting_apply = node.readAttribute ('data-setting-apply') || null;

		//console.log ('settings : ', settings_key, mode, value, method);
		switch (mode) {
			case 'display':
				localforage.getItem (settings_key, function (err, value) {
				//	console.log (err, value)
				}).then (function (finval) {
				// 	console.log ('FIN', finval)
				})
				if ( setting_apply ) {
					node.setStyle ({ display : value });
				}
				break;

			default:
				break;
		}
		if ( method ) {
			node.on (method, function () {
				// console.log(method, value);
				ajaxValidation ('set_settings', 'mdl/app/', 'key=' + settings_key + '&value=' + value);
			}.bind (this))
		}
		//	$(node).socketModule(file, vars);
		// console.log('preload ');
	});
	/*insertionQ('[data-contextual]').every(function (node) {
	 var vars = node.readAttribute('data-contextual');
	 var file = 'app/app_contextual/app_contextual';
	 console.log('preload ');
	 });*/
	//// insertionQ
	insertionQ ('.mastershow:hover .slaveshow').every (function (node) {
		// alert('red');
		var node_elem = $ (node);
		if ( $ (node).getStyle ('position') == 'absolute' ) {
			setTimeout (function () {
				chkDispZone2 (node_elem)
			}, 110);
		}
	});
	insertionQ ('[data-idtache]').every (function (node) {
		//
		//
		/*new Resizeable($(node_elem),{
		 top: 0,left: 0,right: 0,
		 parent: $(node_elem).up(),
		 resize: function(el) {
		 datedebut   = $(node_elem).readAttribute('datedebut');
		 heuredebut  = $(node_elem).readAttribute('heuredebut');
		 idtache  = $(node_elem).readAttribute('data-idtache');

		 height = eval($(node_elem).getHeight()) / 20 ;
		 height = Math.round(height) * 20;
		 $(node_elem).setStyle({'height':height+'px'});
		 ajaxValidation('app_update','mdl/app/','table=tache&table_value='+idtache+'&vars[heureFinTache]='+(height/20))
		 }
		 });*/
	});
	insertionQ ('[data-dragconge]').every (function (node) {
		//
		var node = $ (node);

		if ( $ (node).readAttribute ('data-parent') && $ ($ (node).readAttribute ('data-parent')) && $ (node).readAttribute ('data-dragconge') && $ (node).readAttribute ('data-idagent') ) {

			if ( $ (node).readAttribute ('data-dateDebut') && $ (node).readAttribute ('data-dateFin') ) {

				if ( $ (node).readAttribute ('data-heureDebut') && $ (node).readAttribute ('data-heureFin') ) {

					var data_parent = $ ($ (node).readAttribute ('data-parent')),
					    dateDebut   = $ (node).readAttribute ('data-datedebut'),
					    heureDebut  = $ (node).readAttribute ('data-heuredebut'),
					    dateFin     = $ (node).readAttribute ('data-datefin'),
					    heureFin    = $ (node).readAttribute ('data-heurefin'),
					    idagent     = $ (node).readAttribute ('data-idagent');

					var selector_zone     = '[data-dropzone="conge"][heureDebut="' + heureDebut + '"][dateDebut="' + dateDebut + '"][data-idagent="' + idagent + '"]';
					var selector_zone_fin = '[data-dropzone="conge"][heureDebut="' + heureFin + '"][dateDebut="' + dateFin + '"][data-idagent="' + idagent + '"]';

					if( $ (data_parent).select (selector_zone).size () == 0  &&  $ (data_parent).select (selector_zone_fin).size () == 0 ) { $ (node).remove();return;}
					if ( $ (data_parent).select (selector_zone).size () == 0 ) {
						var first_node = $ (data_parent).select ('[data-dropzone="conge"][heureDebut][dateDebut][data-idagent="' + idagent + '"]').first ();
					}else {
						var first_node = $ (data_parent).select (selector_zone).first ();
					}

					if ( $ (data_parent).select (selector_zone_fin).size () == 0 ) {
						var last_node = $ (data_parent).select ('[data-dropzone="conge"][heureDebut][dateDebut][data-idagent="' + idagent + '"]').last ();
					} else { var last_node = $ (data_parent).select (selector_zone_fin).first ();}

					var offsets = first_node.cumulativeScrollOffset ();

					node.clonePosition (first_node);

					var tmp_end_clone = new Element ('div', { className : 'absolute' });
					$ (data_parent).appendChild (tmp_end_clone);
					$ (tmp_end_clone).clonePosition (last_node);

					$ (node).setStyle ({ marginTop : offsets.top + 'px', width : (offsets.left + tmp_end_clone.offsetLeft + tmp_end_clone.offsetWidth - node.offsetLeft ) + 'px' });

				}
			}
		}
	});
	insertionQ ('[data-dyn_datetime]').every (function (element) {
		if ( $ (element).readAttribute ('masked') == null ) {
			$ (element).on ('click', function () {
				getDuree (element)
			}.bind (this));
			$ (element).on ('change', function () {
				getDuree (element)
			}.bind (this));
			$ (element).writeAttribute ('masked', 'true');
		}
	});
	insertionQ ('.heure').every (function (element) {
		// if ($(element).match('.heure')) {
		if ( $ (element).readAttribute ('masked') == null ) {
			var oDateMask = new Mask ("##:##:##");
			oDateMask.attach ($ (element));
			//
			$ (element).writeAttribute ('datalist', 'app/app_select_heure');
			$ (element).setStyle ({ type : 'text' });

			new myddeDatalist (element);

			$ (element).writeAttribute ('masked', 'true');
		}
	});
	insertionQ ('.inputInline').every (function (node) {
		new resizeInput (node);
	});
	insertionQ ('form').every (function (node) {
		$ (node).writeAttribute ({ 'autocomplete' : 'off' });
	});
	insertionQ ('[autofocus]').every (function (node) {
		$ (node).focus ();
	});
	insertionQ ('[data-icon_select]').every (function (node) {
		$ (node).focus ();
	});
	insertionQ ('.inputDate').every (function (node) {
		if ( $ (node).readAttribute ('masked') == null ) {
			var oDateMask = new Mask ("##/##/####");
			oDateMask.attach ($ (node));
			$ (node).writeAttribute ('masked', 'true');
		}
	});
	insertionQ ('.validate-date-au').every (function (node) {
		if ( $ (node).readAttribute ('masked') == null ) {
			var id_trig = uniqid ();
			$ (node).insert ({ after : new Element ('i', { id : id_trig, className : 'fa fa-calendar textgris' }) })// '<i class="fa fa-calendar textgris"></i>'
			var oDateMask = new Mask ("##/##/####");
			oDateMask.attach ($ (node));
			$ (node).writeAttribute ('masked', 'true');
			//
			var picker = new Pikaday ({
				field    : $ (node),
				trigger  : $ (id_trig),
				format   : 'DD/MM/YYYY',
				onSelect : function () {
					$ (node).value = this.getMoment ().format ('DD/MM/YYYY');
					$ (node).fire ('dom:act_change')
				}
			});
		}
	});
	insertionQ ('.toggler').every (function (node) {
		new autoToggle (node);
	});
	insertionQ ('table.act_sort').every (function (node) {
		new sortableTable (node); // old way
		$ (node).removeClassName ('act_sort');
	});

	insertionQ ('textarea[ext_mce_textarea]').every (function (node) {
		mce_area ('#' + $ (node).identify ());
	});
	insertionQ ('[act_target]').every (function (node) {
		var act_target         = node.readAttribute ('act_target');
		var mdl                = node.readAttribute ('mdl');
		var vars               = node.readAttribute ('vars') || '';
		var boundHandlerMethod = function () {
			$ (act_target).show ();
			if ( node.readAttribute ('mdl') ) {
				$ (act_target).socketModule (mdl, vars);
			}

			if ( $ (act_target).readAttribute ('data-act_target_toggle') ) {
				$ (act_target).toggleContent ();
			}
		};
		$ (node).observe ('click', boundHandlerMethod.bind (this), true);
	});
	insertionQ ('[act_chrome_gui]').every (function (node) {
		var options = '{}';
		var mdl     = node.readAttribute ('act_chrome_gui');
		var vars    = node.readAttribute ('vars') || '';
		var vars    = vars || node.readAttribute ('data-vars');
		if ( node.readAttribute ('options') != 'undefined' ) {
			options = node.readAttribute ('options');
		}
		// data-cache="true"
		var onclick = "act_chrome_gui('" + mdl + "','" + vars + "'," + options + ")";
		node.writeAttribute ('onclick', onclick);
	});

	insertionQ ('[act_chrome_ingui]').every (function (node) {
		var options = '{}';
		var mdl     = node.readAttribute ('act_chrome_ingui');
		var vars    = node.readAttribute ('vars') || '';
		var titre   = node.readAttribute ('titre') || '';
		if ( node.readAttribute ('options') != 'undefined' ) {
			options = node.readAttribute ('options');
		}

		var onclick = "ajaxInMdl('" + mdl + "','tmp_" + mdl + "_frame','" + vars + "'," + options + ")";
		node.writeAttribute ('onclick', onclick);
	}); //

	insertionQ ('.cf_module ').every (function (node) {
		$ (node).setAttribute ('title', $ (node).readAttribute ('mdl'));
	});

	insertionQ ('[main_auto_tree]').every (function (node) {
		new app_tree (node);
	});
	insertionQ ('[auto_tree]').every (function (node) {
		                                  var ct = '', order = 0;
		                                  $ (node).cleanWhitespace ();
		                                  if ( node.readAttribute ('auto_tree_count') ) {
			                                  ct = node.readAttribute ('auto_tree_count');
		                                  }
		                                  if ( node.readAttribute ('right') ) order = 3;
		                                  if ( $ (node).childElements ().size () == 1 ) {
			                                  $ (node).firstDescendant ().setStyle ({ order : 1, width : '100%' }).addClassName ('flex_main')
		                                  }
		                                  $ (node).insert ('<div style="order:' + order + '" class="auto_tree_caret avoid"></div>');
		                                  $ (node).insert ('<div auto_tree_count style="order:2" >' + ct + '</div>');
		                                  $ (node).addClassName ('auto_tree').removeAttribute ('auto_tree');

		                                  /* $ (node).update ('<div style="order:' + order + '" class="auto_tree_caret avoid"></div><div class="" style="order:1;">' + node.innerHTML + '</div><div auto_tree_count style="order:2" >' + ct + '</div>')
		                                   .addClassName ('auto_tree').removeAttribute ('auto_tree');
		                                   node.removeAttribute ('auto_tree_count');*/

		                                  if ( $ (node).next () ) {
			                                  var inner_node = $ (node).next ().innerHTML.stripTags ().strip ();
			                                  if ( inner_node.length == 0 ) { $ (node).addClassName ('hidden_caret')}
			                                  $ (node).next ().addClassName ('auto_tree_next')
			                                  if ( $ (node).next ().visible () ) {
				                                  $ (node).addClassName ('opened')
			                                  } else {
				                                  $ (node).removeClassName ('opened')
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
		if ( $ (node).readAttribute ('masked') == null ) {
			$ (node).writeAttribute ('masked', 'true');
			// console.log($(node))
			var el      = $ (node);
			var options = {};
			el.removeAttribute ('act_defer');
			act_mdl     = el.readAttribute ('mdl');
			act_vars    = el.readAttribute ('vars') || '';
			value       = el.readAttribute ('value') || el.identify ();
			if ( el.readAttribute ('data-json_options') ) {
				options = json_decode (el.readAttribute ('data-json_options'));
				console.log ('json_options', options)
			}
			if ( el.readAttribute ('data-cache') ) {
				options['cache'] = true;
			}
			el.socketModule (act_mdl, act_vars, options);

		}
	});

	// appgui
	insertionQ ('[app_gui_flowdown]').every (function (node) {
		//
		var node = $ (node);
		node.doRedim ();
		node.observe ('content:loaded', function () {
			if ( node.timer ) clearTimeout (node.timer);
			node.timer = setTimeout (function () {

			}.bind (this), 0);
		}.bind (this));

		//})
	});

	insertionQ ('[app_gui_explorer]').every (function (node) {
		new myddeExplorer ($ (node));
	});

	return true;
}