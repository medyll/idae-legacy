myddeExplorer           = {};
myddeExplorer           = Class.create ();
myddeExplorer.prototype = {
	initialize                     : function (element, options) {
		this.element = $ ($ (element).identify ());
		this.options = Object.extend ({
			expl_html_title          : false,
			expl_nav_zone            : false,
			expl_left_zone           : false,
			expl_preview_zone        : false,
			expl_file_zone           : false,
			expl_bottom_zone         : false,
			expl_drag_selection_zone : false,
			expl_search_button       : false,
			onChange                 : Prototype.emptyFunction,
			onEndUpload              : Prototype.emptyFunction
		}, options || {});

		this.element.cleanWhitespace ();

		this.build_all ();

		this.act_click_zone ();

		this.where_search = 'local'
		return $ (this.element);
	},
	get_zone                       : function (zone) {
		if ( !this.expl_preview_zone ) return false;
		//
		return this.expl_preview_zone.querySelector (zone)
	},
	build_all                      : function () {
		this.act_sort_zone (); // zone de tri
		this.build_expl_html_title ();
		this.build_expl_left_zone (); // drag drop de gauche
		this.build_preview_zone (); // vue auto
		this.build_expl_file_zone ();
		this.build_expl_bottom_zone ();
		this.build_expl_drag_selection_zone ();
		this.build_expl_search_button ();
	},
	build_expl_html_title          : function () {
		if ( this.options.expl_html_title || this.element.select ('[expl_html_title]').first () ) {
			this.expl_html_title = $ (this.options.expl_html_title || this.element.select ('[expl_html_title]').first ());
		}
	},
	build_expl_left_zone           : function () {
		if ( this.options.expl_left_zone || this.element.select ('[expl_left_zone]').first () ) {
			this.expl_left_zone = $ (this.options.expl_left_zone || this.element.select ('[expl_left_zone]').first ());
			this.set_list_dragdrop_zone ();
		}
	},
	build_expl_preview_zone        : function () {
		if ( this.options.expl_preview_zone || this.element.select ('[expl_preview_zone]').first () ) {
			this.expl_preview_zone = $ (this.options.expl_preview_zone || this.element.select ('[expl_preview_zone]').first ());
			this.build_preview_zone ();
		}
	},
	build_expl_file_zone           : function () {
		if ( this.options.expl_file_zone || this.element.select ('[expl_file_zone]').first () ) {
			this.expl_file_zone = $ (this.options.expl_file_zone || this.element.select ('[expl_file_zone]').first ());
			this.act_file_zone ();
		}
	},
	build_expl_bottom_zone         : function () {
		if ( this.options.expl_bottom_zone || this.element.select ('[expl_bottom_zone]').first () ) {
			this.expl_bottom_zone = $ (this.options.expl_bottom_zone || this.element.select ('[expl_bottom_zone]').first ());
		}
	},
	build_expl_drag_selection_zone : function () {
		if ( this.options.expl_drag_selection_zone || this.element.select ('[expl_drag_selection_zone]').first () ) {
			this.expl_drag_selection_zone = $ (this.options.expl_drag_selection_zone || this.element.select ('[expl_drag_selection_zone]').first ());
			this.act_drag_selection_zone ();
		}
	},
	build_expl_search_button       : function () {
		if ( this.element.querySelector ('[expl_search_button]') ) {
			this.expl_search_button = $ (this.options.expl_search_button || this.element.select ('[expl_search_button]').first ());
			this.act_expl_search_input ();
		}
	},
	build_preview_zone             : function () {

		if ( !$ ('auto_expl_preview_zone') ) {
			var frag_app_left_panel = window.APP.APPTPL['app_left_panel']
			var elem_left_panel     = create_element_of (frag_app_left_panel);
			document.body.appendChild (elem_left_panel);
			this.expl_preview_zone  = elem_left_panel;
		} else {
			this.expl_preview_zone = $ ('auto_expl_preview_zone');
		}
		return this.expl_preview_zone;

	},
	act_click_zone                 : function () {
		// zone centrale fonction centrale
		$ (this.element).on ('submit', '[expl_form]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				var elem_expl = $ (this.element.querySelector ('[expl_file_list]')),
				    form_vars = $ (node).serialize (),
				    vars      = elem_expl.readAttribute ('vars') || '',
				    mdl       = elem_expl.readAttribute ('mdl'),
				    mdl_value = elem_expl.readAttribute ('value'),
				    mdl_scope = elem_expl.readAttribute ('scope');

				var data_form_vars = form_vars.toQueryParams ();
				var data_vars      = vars.toQueryParams ();
				var new_vars       = array_merge (data_form_vars, data_vars);
				new_vars           = array_unique (new_vars);
				var send_vars      = Object.toQueryString (new_vars);
				// console.log('expl_form',$(node),data_form_vars,new_vars,send_vars)
				load_table_in_zone (send_vars, elem_expl);
				//reloadScope(mdl_scope, mdl_value, form_vars);
				// $(elem_expl).socketModule(mdl, vars + '&' + form_vars);
			}
		}.bind (this));
		$ (this.element).on ('click', '[expl_save_liste_button]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				var uiop     = $ (this.element.querySelector ('[expl_file_list]'));
				console.log (uiop)
				var add_vars = uiop.readAttribute ('vars');

				var vars = Form.serialize ($ (this.element.querySelector ('[expl_file_list]')));
				act_chrome_gui ('app/app/app_save_liste_multi', vars + '&' + add_vars);
			}
		}.bind (this));
		$ (this.element).on ('click', '[expl_multi_button]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				var uiop     = $ (this.element.querySelector ('[expl_file_list]'));
				var add_vars = uiop.readAttribute ('vars');

				var vars = Form.serialize ($ (this.element.querySelector ('[expl_file_list]')));
				act_chrome_gui ('app/app/app_update_multi', vars + '&' + add_vars);
			}
		}.bind (this));
		$ (this.element).on ('click', '[expl_multi_delete_button]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				var uiop     = $ (this.element.querySelector ('[expl_file_list]'));
				var add_vars = uiop.readAttribute ('vars');

				var vars = Form.serialize ($ (this.element.querySelector ('[expl_file_list]')));
				act_chrome_gui ('app/app_delete_multi', vars + '&' + add_vars);
			}
		}.bind (this));

		$ (this.element).on ('click', '[act_preview_mdl]', function (event, node) {
			//
			// return;
			/*this.build_preview_zone();
			 var act_target = this.get_zone('[expl_preview_zone_file]');

			 if (!this.element.querySelector('[expl_view_button].active'))
			 return;
			 var mdl = node.readAttribute('act_preview_mdl');
			 var vars = node.readAttribute('vars') || '';
			 var value = node.readAttribute('value') || '';
			 if (node.readAttribute('value'))
			 $(act_target).setAttribute('value', node.readAttribute('value'));
			 if (node.readAttribute('scope'))$(act_target).setAttribute('scope', node.readAttribute('scope'));
			 this.expl_preview_zone.show();
			 $(act_target).show().socketModule(mdl, vars, value);*/
		}.bind (this))
		// PREVIEW
		$ (this.element).on ('dblclick', '[act_preview_mdl]', function (event, node) {
			//
			this.build_preview_zone ();
			var node       = node;
			var act_target = this.get_zone ('[expl_preview_zone_file]');

			/*if (this.element.querySelector('[expl_view_button]')) {
			 if (this.element.querySelector('[expl_view_button].active') && node.hasClassName('active')) {
			 this.element.querySelector('[expl_view_button].active').removeClassName('active');
			 this.expl_preview_zone.hide();
			 return;
			 }
			 this.element.querySelector('[expl_view_button]').addClassName('active');
			 this.element.querySelector('[expl_view_button]').oldText = this.element.querySelector('[expl_view_button]').innerHTML;
			 $(this.element.querySelector('[expl_view_button]')).update('<i class="fa fa-eye-slash"></i> Fermer');
			 act_target.show();
			 this.expl_preview_zone.show();
			 }*/
			this.expl_preview_zone.show ();
			var mdl        = node.readAttribute ('act_preview_mdl');
			var vars       = node.readAttribute ('vars') || '';
			var value      = node.readAttribute ('value') || '';
			$ (act_target).socketModule (mdl, vars, value);

		}.bind (this));

		$ (this.element).on ('click', '.sortnext', function (event, node) {
			var parent = $ (node).up ('[sort_zone]');
			
			if ( parent ) {
				if ( $ (parent).next () ) {
					$ (parent).next ().insert ({ after : $ (parent) })
				}
			}
			$ (node).fire ('dom:act_sort')
		}.bind (this))
		$ (this.element).on ('click', '.sortprevious', function (event, node) {
			var parent = $ (node).up ('[sort_zone]');
			if ( parent ) {
				if ( $ (parent).previous () ) {
					$ (parent).previous ().insert ({ before : $ (parent) })
				}
			}
			$ (node).fire ('dom:act_sort')
		}.bind (this))
		$ (this.element).on ('click', '[expl_view_button]', function (event, node) {
			var node = node;
			if ( $ (node).hasClassName ('active') ) {
				$$ ('[expl_preview_zone]').invoke ('hide');
				$ (node).removeClassName ('active');
				$ (node).update ($ (node).oldText);
			} else {
				$$ ('[expl_preview_zone]').invoke ('show');
				$ (node).addClassName ('active');
				$ (node).oldText = $ (node).innerHTML;
				$ (node).update ('<i class="fa fa-eye-slash"></i> Fermer');
			}
		}.bind (this));
		$ (this.element).on ('click', '[expl_act_target]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				var flel     = $ (this.element.querySelector ('[expl_file_list]')),
				    target   = node.readAttribute ('expl_act_target'),
				    mdl      = node.readAttribute ('mdl'),
				    receiver = this.element.querySelector ('[expl_act_target_receiver=' + target + ']'),
				    vars     = (node.readAttribute ('vars') || '') + '&' + flel.readAttribute ('vars');

				$ (receiver).socketModule (mdl, vars);
			}
		}.bind (this));
		$ (this.element).on ('click', '[data-button-data_model]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				flel = $ (this.element.querySelector ('[expl_file_list]'));
				flel.setAttribute ('data-data_model', node.readAttribute ('data-button-data_model'));

				load_table_in_zone (flel.readAttribute ('vars'), flel);
			}
		}.bind (this));
		$ (this.element).on ('click', '[data-button-dsp]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				flel = $ (this.element.querySelector ('[expl_file_list]'));
				flel.setAttribute ('data-dsp', node.readAttribute ('data-button-dsp'));
				if ( node.readAttribute ('data-button-dsp') == "mdl" ) {
					flel.setAttribute ('data-dsp-mdl', node.readAttribute ('data-dsp-mdl'));
					flel.setAttribute ('data-dsp', 'mdl');
				} else {
					flel.removeAttribute ('data-dsp-mdl')
				}
				load_table_in_zone (flel.readAttribute ('vars'), flel);
			} else {

			}
		}.bind (this));
		$ (this.element).on ('click', '[data-button-className]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				flel = $ (this.element.querySelector ('[expl_file_list]'));
				flel.setAttribute ('data-className', node.readAttribute ('data-button-className'));

				load_table_in_zone (flel.readAttribute ('vars'), flel);
			}
		}.bind (this));
		$ (this.element).on ('click', '[data-button_chk]', function (event, node) {

			flel = $ (this.element.querySelector ('[expl_file_list]'));
			if ( node.hasClassName ('active') ) {
				flel.setAttribute ('data-show_chk', true);
			} else {
				flel.setAttribute ('data-show_chk', 'false');
			}

		}.bind (this));
		$ (this.element).on ('click', '[data-button-export]', function (event, node) {
			flel = $ (this.element.querySelector ('[expl_file_list]'));
			vars = flel.readAttribute ('vars');
			runModule ('services/json_data_table', vars + '&csv_export=1');
		}.bind (this));
		$ (this.element).on ('click', '[app_button]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				flel = $ (this.element.querySelector ('[expl_file_list]'));
				flel.setAttribute ('vars', node.readAttribute ('vars'));
				this.on_content_requested (event);
				event.preventDefault ();
				Event.stop (event)
				//
				if (node.readAttribute ('data-button_group') ) {

				}
				if (node.readAttribute ('data-button_sort') ) {

				}
				if (node.readAttribute ('data-button_sort_order') ) {

				}
				if ( !node.readAttribute ('-data-dsp') ) {
					load_table_in_zone (node.readAttribute ('vars'), flel);
				} else {
					switch (node.readAttribute ('data-dsp')) {
						case 'thumb' :

							break;
					}

				}
			}
		}.bind (this));
		$ (this.element).on ('click', '[app_button_scope]', function (event, node) {
			if ( this.element.querySelector ('[expl_file_list]') ) {
				flel = $ (this.element.querySelector ('[expl_file_list]'));
				//flel.setAttribute('vars', node.readAttribute('vars'));
				this.on_content_requested (event);
				//

				var data_form_vars = flel.readAttribute ('vars').toQueryParams ();
				var data_vars      = node.readAttribute ('vars').toQueryParams ();
				var new_vars       = array_merge (data_form_vars, data_vars);

				new_vars      = array_unique (new_vars);
				var send_vars = Object.toQueryString (new_vars);
				//
				flel.fire ('dom:load_data', { url_data : send_vars })
				//flel.setAttribute('vars', send_vars);
				//load_table_in_zone(send_vars, flel);

			}
		}.bind (this));

		$ (this.element).on ('click', 'thead input[type=checkbox]', function (event, node) {
			var ch = node.checked ? 'doCheck' : 'doUnCheck';
			$A (this.expl_file_zone.querySelectorAll ('tbody input[type=checkbox]')).invoke (ch);

		}.bind (this));

		$ (this.element).on ('keyup', '[expl_search_button]', function (event, node) {
			if ( this.timerSearch )  clearTimeout (this.timerSearch)
			this.timerSearch = setTimeout (function () {
				this.act_search (node)
			}.bind (this), 250);
		}.bind (this));
		$ (this.element).observe ('mouseup', function (event) {
			this.act_disinput ();
		}.bind (this));
		$ (this.element).observe ('dom:click', function (event) {
			this.act_disinput ();
		}.bind (this));
		$ (this.element).observe ('dom:selectionMade', function (event) {
			this.act_disinput ();
		}.bind (this));
		$ (this.element).observe ('content:loaded', function (event) {
			$ (this.element).cleanWhitespace ();
			this.act_disinput ();
			// expl_search_button rebuild ?
			this.build_expl_search_button ();
			// cout !!!
			if ( this.element.select ('[expl_count_report]').first () ) {
				if ( this.element.select ('[expl_count]').first () ) {
					this.element.select ('[expl_count_report]').first ().update (this.element.select ('[expl_count]').first ().innerHTML)
				}
			}
		}.bind (this));
	},
	act_search                     : function (node) {
		var valuesearch = node.value.toLowerCase (),
		    i           = 0;

		if ( this.where_search == 'world' ) {

			if ( this.element.querySelector ('[expl_file_list]') ) {
				//
				var uiop      = $ (this.element.querySelector ('[expl_file_list]'));
				var file_vars = uiop.readAttribute ('vars');
				//
				if ( this.timer ) {
					clearTimeout (this.timer);
				}
				//
				this.timer = setTimeout (function () {
					load_table_in_zone (file_vars + '&search=' + node.value, uiop);
				}.bind (this), 500)
			}
		} else {
			if ( this.timer ) {
				clearTimeout (this.timer);
			}
			this.timer = setTimeout (function () {
				$ (this.element).select ('.div_tbody').first ().childElements ().each (function (node) {
					inn = node.innerHTML.stripTags ().toLowerCase ();
					if ( !inn.include (valuesearch) ) {
						Element.hide.defer (node)
					} else {
						Element.show.defer (node)
						i++;
					}
				}.bind (this))
			}.bind (this), 500)
		}
	},
	act_disinput                   : function () {
		setTimeout (function () {
			if ( $ (this.element).select ('[bugchk]').size () != 0 ) {
				$ (this.element).select ('.disinput').invoke ('addClassName', 'enabled')
			} else {
				$ (this.element).select ('.disinput').invoke ('removeClassName', 'enabled')
			}
		}.bind (this), 125)
	},
	act_sort_zone                  : function () { //   attr[sort_zone_drag]
		// return;
		//console.log('act_sort_zone')
		$ (this.element).on ("dragstart", '[sort_zone_drag] [data-sort_element]', function (event, node) {

			event.dataTransfer.effectAllowed = "move";
			event.dataTransfer.setData ('dragid', $ (node).identify ());
			node.setAttribute ('dragged', 'dragged');
			//
			var node_up = $ (node).up ('[sort_zone_drag]');
			//
			if ( $ (node_up).select ('.django').size () == 0 ) {
				var django_tag = $ (node).tagName;
				if ( django_tag.toLowerCase () == 'tr' ) {
					var django_add = '<td class="padding" colspan="' + $ (node).childElements ().length + '">&nbsp;</td>'
				} else { var django_add = ''}
				node.insert ({ before : '<' + django_tag + '  id="django" class="django padding fond_noir rounded">' + django_add + '</' + django_tag + '>' });
			}
			var django = $ (node_up).select ('.django').first ();

			$ (node_up).on ('dragover', '[data-sort_element]', function (event, node) {
				if ( event ) {
					event.stopPropagation ();
					event.preventDefault ();
				}
				node.insert ({ before : django.show () })
			}.bind (this))

			$ (this.element).on ('dragend', '[data-sort_element]', function (event, node) {
				if ( django ) django.hide ();
			})

			$ (node_up).on ('drop', '[data-sort_element]', function (event, node) {
				if ( !event.dataTransfer.getData ('dragid') ) return;
				this.dnd_successful = true;
				var dropped_elem    = $ (event.dataTransfer.getData ('dragid'));
				event.preventDefault ();

				django.insert ({ before : dropped_elem });
				dropped_elem.removeAttribute ('dragged')
				django.hide ();
				$ (dropped_elem).fire ('dom:act_sort');
				// console.log('attr', dropped_elem, dropped_elem.readAttribute('data-table'))
				if ( !dropped_elem.readAttribute ('data-table') ) return;
				var table = dropped_elem.readAttribute ('data-table');
				var Table = ucfirst (table);
				//
				var pair = {};
				node_up.select ('[data-sort_element]').collect (function (node, index) {
					pair['ordre' + Table + '[' + index + ']'] = node.readAttribute ('data-table_value');
				}.bind (this));
				vars     = Object.toQueryString (pair);
				url      = vars + '&table=' + table;

				ajaxValidation ('app_sort', 'mdl/app/', url);
			})

		}.bind (this));

	},
	set_list_dragdrop_zone         : function () {
		this.element.select ('[expl_left_zone]').each (function (node) {
			this.act_left_zone (node);
		}.bind (this))
	},
	act_left_zone                  : function (node) { // drop avec data
		var dragdrop_zone = node;
		//  $('body').on("dragstart", '[draggable]', function (event, node) {
		dragdrop_zone.on ("dragstart", '[draggable]', function (event, node) {
			console.log ('act_left_zone drag start')
			//$(node).style.opacity = '0.4'
			event.dataTransfer.effectAllowed = "move";
			event.dataTransfer.setData ('dragid', $ (node).identify ());

		}.bind (this));
		dragdrop_zone.on ('dragleave', '[dropzone]', function (event, node) {
			node.style.background = "";
		});
		dragdrop_zone.on ('dragenter', '[dropzone]', function (event, node) {

			event.dataTransfer.dropEffect = "move";
			event.preventDefault ();
			node.style.background         = "#FC3";
			return false;
		});
		dragdrop_zone.on ('dragover', '[dropzone]', function (event, node) {
			event.dataTransfer.dropEffect = "move";
			event.preventDefault ();
			node.style.background         = "#FC3";
			return false;
		});

		dragdrop_zone.on ('drop', '[dropzone]', function (event, node) {

			if ( !event.dataTransfer.getData ('dragid') ) return;
			if ( node.readAttribute ('noF_action') ) return;

			this.dnd_successful   = true;
			var val                   = event.dataTransfer.getData ('dragid');
			event.preventDefault ();
			node.style.background = "";
			$ (val).style.opacity = '1'
			var varstarget        = node.readAttribute ('data-vars') || '';
			var F_action          = node.readAttribute ('F_action') || 'app_update';
			var path              = node.readAttribute ('path') || 'mdl/app/';
			console.log('dropzone',node,F_action)
			if ( event.dataTransfer.getData ('multiple') ) {
				$ (this.element).select ('.selected').each (function (elem) {
					var varstomove = $ (elem).readAttribute ('data-vars') || '';
					if ( node.readAttribute ('data-append') ) {
						node.appendChild ($ (elem));
					}
					ajaxValidation (F_action, path, varstarget + '&' + varstomove);
				}.bind (this))
			} else {
				var varstomove = $ (val).readAttribute ('data-vars') || '';
				if ( node.readAttribute ('data-append') ) {
					node.appendChild ($ (val));
				}
				if ( F_action == 'form_up' ) {
					console.log({form:$ (val).up('form')})
					ajaxFormValidation ($ (val).up('form'));
				} else {

					ajaxValidation (F_action, path, varstarget + '&' + varstomove);
				}
			}
			$ (node).fire ('dom:act_drop', { drop_node : event.dataTransfer.getData ('dragid') });
		}.bind (this));
	},
	act_file_zone                  : function () {

		new myddeview (this.expl_file_zone, {
			only : 'input[type=checkbox]'
		});
		this.dnd_successful = false;
		this.expl_file_zone.on ("dragstart", '[draggable]', function (event, node) {

			$ (node).style.opacity = '0.4'
			if ( $ (this.element).select ('[bugchk]').size () > 1 ) {
				event.dataTransfer.setData ('multiple', true);
				$ (this.element).select ('[bugchk]').invoke ('setOpacity', '0.4')
			}
			event.dataTransfer.effectAllowed = "move";
			event.dataTransfer.setData ('dragid', $ (event.target).identify ());
			this.dnd_successful              = false;
		}.bind (this));
		this.expl_file_zone.on ("dragend", '[draggable]', function (event, node) {
			console.log ('expl_file_zone drag end')
			if ( this.dnd_successful ) {
				event.target.parentNode.removeChild (event.target);
			} else {
				$ (node).style.opacity = '1';
				$ (this.element).select ('[bugchk]').invoke ('setOpacity', '1');
			}
		}.bind (this));
		this.expl_file_zone.observe ('dom:vars_changed', this.on_content_requested.bind (this));
	},
	act_drag_selection_zone        : function () {
		new myddeSelection (this.expl_file_zone, {
			/* only: '[draggable]'*/
			only : '[data-table][data-table_value]'
		});
	},
	// n'est pas un bouton //
	act_expl_search_multi_input    : function () {

	},
	// n'est pas un bouton //
	act_expl_search_input          : function () {
		if ( !this.expl_search_button )
			return;
		if ( this.expl_search_button.act_processed )
			return;
		this.expl_search_button.writeAttribute ({ placeholder : 'Rechercher', 'data-menu' : 'data-menu' });
		// this.where_search
		var loc  = new Element ('a').addClassName ('autoToggle active avoid').update ('Parmi les éléments visibles');
		loc.on ('click', function () {
			this.where_search = 'local'
		}.bind (this));
		var wrld = new Element ('a').addClassName ('autoToggle  avoid').update ('Partout');
		wrld.on ('click', function () {
			this.where_search = 'world'
			this.act_search (this.expl_search_button);
		}.bind (this));
		var str  = new Element ('div').addClassName ('applink applinkblock absolute contextmenu toggler').insert (loc).insert (wrld);
		// var str = '<div class="applink applinkblock absolute contextmenu"><a class="titre_entete">Parmi les éléments visibles</a><label class="titre_entete"><input type="checkbox" name="where" value="world" />Partout</label></div>';
		this.expl_search_menu                 = new Element ('div').hide ();
		this.expl_search_menu.update (str);
		this.expl_search_wrapper              = new Element ('div');
		this.expl_search_button.wrap (this.expl_search_wrapper);
		this.expl_search_wrapper.insert (this.expl_search_menu);
		//this.expl_search_menu.observe('click', function (event) {
		// Event.stop(event);
		// this.expl_search_button.activate();
		// }.bind(this));
		this.expl_search_button.observe ('focus', function (event) {
			Event.stop (event);
			// this.expl_search_menu.show();
		}.bind (this));
		this.expl_search_button.act_processed = true;
	},
	on_content_requested           : function (event) {

		if ( this.element.querySelector ('progress.auto_prog') ) {
			// id: auto_progress_
			var elem = Event.element (event);
			if ( !elem.readAttribute ('vars') )
				return;
			var vars = (elem.readAttribute ('vars')).toQueryParams ();
			if ( vars.table ) {
				$ (this.element.querySelector ('progress.auto_prog')).setAttribute ('data-table', vars.table);
				$ (this.element.querySelector ('progress.auto_prog')).setAttribute ('id', 'auto_progress_' + vars.table);
			}
		}
		$A (this.element.querySelectorAll ('[expl_file_reload]')).each (function (node) {

			if ( this.element.querySelector ('[expl_file_list]') ) {
				var uiop      = $ (this.element.querySelector ('[expl_file_list]'));
				var node_vars = node.readAttribute ('vars');
				var vars      = uiop.readAttribute ('vars');
				if ( node_vars == vars )
					return;
				var mdl = node.readAttribute ('mdl');
				$ (node).socketModule (mdl, vars);
			}
		}.bind (this));

	},
	oncontentrequested             : function () {

	}
}