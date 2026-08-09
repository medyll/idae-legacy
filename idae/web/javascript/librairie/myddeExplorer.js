/**
 * myddeExplorer — the list/explorer shell: toolbar buttons, sort zones,
 * drag & drop, preview pane, client-side search.
 *
 * Modified: 2026-08-09 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour is unchanged; only the DOM layer moved to
 * native APIs. The file-local helpers below replace the handful of Prototype
 * idioms this class relied on — event delegation above all, which idae-be
 * does not provide either.
 *
 * Still deliberately shim-shaped: `socketModule`, `doCheck`/`doUnCheck` and
 * `unToggleContent` are Element methods this app defines itself in
 * engine/methods.js. They are app API, not Prototype API, and migrate with
 * that file.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local on purpose. They are small, and hoisting   *
	 * them into a shared module would mean touching main_bag.js's load    *
	 * graph, which phase 5 has no reason to disturb.                      *
	 * ------------------------------------------------------------------ */

	function qsa(root, selector) {
		if ( !root ) return [];
		return Array.prototype.slice.call (root.querySelectorAll (selector));
	}

	function first(root, selector) {
		return root ? root.querySelector (selector) : null;
	}

	/**
	 * Event delegation, Prototype's Element#on(event, selector, handler)
	 * signature: the handler is called with (event, matchedNode), `this`
	 * bound by the caller.
	 */
	function delegate(root, eventName, selector, handler) {
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
	function fire(node, eventName, memo) {
		if ( !node ) return null;
		var event  = new CustomEvent (eventName, { bubbles : true, cancelable : true });
		event.memo = memo || {};
		node.dispatchEvent (event);
		return event;
	}

	function identify(node) {
		if ( !node.id ) node.id = uniqid ('anonymous_element');
		return node.id;
	}

	function show(node) {
		if ( node ) node.style.display = '';
		return node;
	}

	function hide(node) {
		if ( node ) node.style.display = 'none';
		return node;
	}

	/**
	 * Prototype's Element#cleanWhitespace: drops whitespace-only text nodes so
	 * childElements()/next()/previous() walks are not thrown off by the
	 * formatting in the server-rendered markup.
	 */
	function cleanWhitespace(node) {
		if ( !node ) return node;
		var child = node.firstChild;
		while ( child ) {
			var next = child.nextSibling;
			if ( child.nodeType === 3 && !/\S/.test (child.nodeValue) ) node.removeChild (child);
			child = next;
		}
		return node;
	}

	function stripTags(html) {
		return String (html).replace (/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
	}

	function queryToObject(query) {
		return Object.fromEntries (new URLSearchParams (String (query || '')));
	}

	function objectToQuery(obj) {
		return new URLSearchParams (obj || {}).toString ();
	}

	/**
	 * Prototype's Form.serialize, which — unlike FormData — accepts any
	 * container, not just a <form>. Several call sites here serialize a
	 * `[expl_file_list]` div rather than a form, so this has to walk fields
	 * itself.
	 */
	function serializeFields(root) {
		if ( !root ) return '';
		var pairs = [];
		qsa (root, 'input, select, textarea').forEach (function (field) {
			if ( field.disabled || !field.name || field.type === 'file' || field.type === 'image' ) return;
			if ( field.type === 'submit' || field.type === 'button' || field.type === 'reset' ) return;
			if ( (field.type === 'checkbox' || field.type === 'radio') && !field.checked ) return;

			if ( field.tagName.toLowerCase () === 'select' && field.multiple ) {
				Array.prototype.slice.call (field.options).forEach (function (option) {
					if ( option.selected ) pairs.push (encodeURIComponent (field.name) + '=' + encodeURIComponent (option.value));
				});
				return;
			}
			pairs.push (encodeURIComponent (field.name) + '=' + encodeURIComponent (field.value));
		});
		return pairs.join ('&');
	}

	/** Prototype's Element#wrap: put `wrapper` where `node` is, node inside. */
	function wrap(node, wrapper) {
		if ( node.parentNode ) node.parentNode.insertBefore (wrapper, node);
		wrapper.appendChild (node);
		return wrapper;
	}

	/* ------------------------------------------------------------------ */

	var myddeExplorer = function () {
		this.initialize.apply (this, arguments);
	};

	myddeExplorer.prototype = {
		initialize                     : function (element, options) {
			this.element = element;
			identify (this.element);
			this.options = Object.assign ({
				expl_html_title          : false,
				expl_nav_zone            : false,
				expl_left_zone           : false,
				expl_preview_zone        : false,
				expl_file_zone           : false,
				expl_bottom_zone         : false,
				expl_drag_selection_zone : false,
				expl_search_button       : false,
				onChange                 : function () {},
				onEndUpload              : function () {}
			}, options || {});

			cleanWhitespace (this.element);

			this.build_all ();

			this.act_click_zone ();

			this.where_search = 'local'
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
			if ( this.options.expl_html_title || first (this.element, '[expl_html_title]') ) {
				this.expl_html_title = this.options.expl_html_title || first (this.element, '[expl_html_title]');
			}
		},
		build_expl_left_zone           : function () {
			if ( this.options.expl_left_zone || first (this.element, '[expl_left_zone]') ) {
				this.expl_left_zone = this.options.expl_left_zone || first (this.element, '[expl_left_zone]');
				this.set_list_dragdrop_zone ();
			}
		},
		build_expl_preview_zone        : function () {
			if ( this.options.expl_preview_zone || first (this.element, '[expl_preview_zone]') ) {
				this.expl_preview_zone = this.options.expl_preview_zone || first (this.element, '[expl_preview_zone]');
				this.build_preview_zone ();
			}
		},
		build_expl_file_zone           : function () {
			if ( this.options.expl_file_zone || first (this.element, '[expl_file_zone]') ) {
				this.expl_file_zone = this.options.expl_file_zone || first (this.element, '[expl_file_zone]');
				this.act_file_zone ();
			}
		},
		build_expl_bottom_zone         : function () {
			if ( this.options.expl_bottom_zone || first (this.element, '[expl_bottom_zone]') ) {
				this.expl_bottom_zone = this.options.expl_bottom_zone || first (this.element, '[expl_bottom_zone]');
			}
		},
		build_expl_drag_selection_zone : function () {
			if ( this.options.expl_drag_selection_zone || first (this.element, '[expl_drag_selection_zone]') ) {
				this.expl_drag_selection_zone = this.options.expl_drag_selection_zone || first (this.element, '[expl_drag_selection_zone]');
				this.act_drag_selection_zone ();
			}
		},
		build_expl_search_button       : function () {
			if ( first (this.element, '[expl_search_button]') ) {
				this.expl_search_button = this.options.expl_search_button || first (this.element, '[expl_search_button]');
				this.act_expl_search_input ();
			}
		},
		build_preview_zone             : function () {

			if ( !document.getElementById ('auto_expl_preview_zone') ) {
				var frag_app_left_panel = window.APP.APPTPL['app_left_panel']
				var elem_left_panel     = create_element_of (frag_app_left_panel);
				document.body.appendChild (elem_left_panel);
				this.expl_preview_zone  = elem_left_panel;
			} else {
				this.expl_preview_zone = document.getElementById ('auto_expl_preview_zone');
			}
			return this.expl_preview_zone;

		},
		act_click_zone                 : function () {
			// zone centrale fonction centrale
			delegate (this.element, 'submit', '[expl_form]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var elem_expl = first (this.element, '[expl_file_list]'),
					    form_vars = serializeFields (node),
					    vars      = elem_expl.getAttribute ('vars') || '',
					    mdl       = elem_expl.getAttribute ('mdl'),
					    mdl_value = elem_expl.getAttribute ('value'),
					    mdl_scope = elem_expl.getAttribute ('scope');

					var data_form_vars = queryToObject (form_vars);
					var data_vars      = queryToObject (vars);
					var new_vars       = array_merge (data_form_vars, data_vars);
					new_vars           = array_unique (new_vars);
					var send_vars      = objectToQuery (new_vars);
					// console.log('expl_form',node,data_form_vars,new_vars,send_vars)
					load_table_in_zone (send_vars, elem_expl);
					//reloadScope(mdl_scope, mdl_value, form_vars);
					// elem_expl.socketModule(mdl, vars + '&' + form_vars);
				}
			}.bind (this));
			delegate (this.element, 'click', '[expl_save_liste_button]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var uiop     = first (this.element, '[expl_file_list]');
					console.log (uiop)
					var add_vars = uiop.getAttribute ('vars');

					var vars = serializeFields (first (this.element, '[expl_file_list]'));
					act_chrome_gui ('app/app/app_save_liste_multi', vars + '&' + add_vars);
				}
			}.bind (this));
			delegate (this.element, 'click', '[expl_multi_button]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var uiop     = first (this.element, '[expl_file_list]');
					var add_vars = uiop.getAttribute ('vars');

					var vars = serializeFields (first (this.element, '[expl_file_list]'));
					act_chrome_gui ('app/app/app_update_multi', vars + '&' + add_vars);
				}
			}.bind (this));
			delegate (this.element, 'click', '[expl_multi_delete_button]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var uiop     = first (this.element, '[expl_file_list]');
					var add_vars = uiop.getAttribute ('vars');

					var vars = serializeFields (first (this.element, '[expl_file_list]'));
					act_chrome_gui ('app/app_delete_multi', vars + '&' + add_vars);
				}
			}.bind (this));

			delegate (this.element, 'click', '[act_preview_mdl]', function (event, node) {
				//
				// return;
				/*this.build_preview_zone();
				 var act_target = this.get_zone('[expl_preview_zone_file]');

				 if (!first(this.element, '[expl_view_button].active'))
				 return;
				 var mdl = node.getAttribute('act_preview_mdl');
				 var vars = node.getAttribute('vars') || '';
				 var value = node.getAttribute('value') || '';
				 if (node.getAttribute('value'))
				 act_target.setAttribute('value', node.getAttribute('value'));
				 if (node.getAttribute('scope')) act_target.setAttribute('scope', node.getAttribute('scope'));
				 show(this.expl_preview_zone);
				 show(act_target).socketModule(mdl, vars, value);*/
			}.bind (this))
			// PREVIEW
			delegate (this.element, 'dblclick', '[act_preview_mdl]', function (event, node) {
				//
				this.build_preview_zone ();
				var act_target = this.get_zone ('[expl_preview_zone_file]');

				/*if (first(this.element, '[expl_view_button]')) {
				 if (first(this.element, '[expl_view_button].active') && node.classList.contains('active')) {
				 first(this.element, '[expl_view_button].active').classList.remove('active');
				 hide(this.expl_preview_zone);
				 return;
				 }
				 first(this.element, '[expl_view_button]').classList.add('active');
				 first(this.element, '[expl_view_button]').oldText = first(this.element, '[expl_view_button]').innerHTML;
				 first(this.element, '[expl_view_button]').innerHTML = '<i class="fa fa-eye-slash"></i> Fermer';
				 show(act_target);
				 show(this.expl_preview_zone);
				 }*/
				show (this.expl_preview_zone);
				var mdl        = node.getAttribute ('act_preview_mdl');
				var vars       = node.getAttribute ('vars') || '';
				var value      = node.getAttribute ('value') || '';
				if ( act_target ) act_target.socketModule (mdl, vars, value);

			}.bind (this));

			delegate (this.element, 'click', '.sortnext', function (event, node) {
				var parent = node.parentElement && node.parentElement.closest ('[sort_zone]');

				if ( parent ) {
					if ( parent.nextElementSibling ) {
						parent.nextElementSibling.after (parent)
					}
				}
				fire (node, 'dom:act_sort')
			}.bind (this))
			delegate (this.element, 'click', '.sortprevious', function (event, node) {
				var parent = node.parentElement && node.parentElement.closest ('[sort_zone]');
				if ( parent ) {
					if ( parent.previousElementSibling ) {
						parent.previousElementSibling.before (parent)
					}
				}
				fire (node, 'dom:act_sort')
			}.bind (this))
			delegate (this.element, 'click', '[expl_view_button]', function (event, node) {
				if ( node.classList.contains ('active') ) {
					qsa (document, '[expl_preview_zone]').forEach (hide);
					node.classList.remove ('active');
					node.innerHTML = node.oldText;
				} else {
					qsa (document, '[expl_preview_zone]').forEach (show);
					node.classList.add ('active');
					node.oldText = node.innerHTML;
					node.innerHTML = '<i class="fa fa-eye-slash"></i> Fermer';
				}
			}.bind (this));
			delegate (this.element, 'click', '[expl_act_target]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel     = first (this.element, '[expl_file_list]'),
					    target   = node.getAttribute ('expl_act_target'),
					    mdl      = node.getAttribute ('mdl'),
					    receiver = first (this.element, '[expl_act_target_receiver=' + target + ']'),
					    vars     = (node.getAttribute ('vars') || '') + '&' + flel.getAttribute ('vars');

					if ( receiver ) receiver.socketModule (mdl, vars);
				}
			}.bind (this));
			delegate (this.element, 'click', '[data-button-data_model]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel = first (this.element, '[expl_file_list]');
					flel.setAttribute ('data-data_model', node.getAttribute ('data-button-data_model'));

					load_table_in_zone (flel.getAttribute ('vars'), flel);
				}
			}.bind (this));
			delegate (this.element, 'click', '[data-button-dsp]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel = first (this.element, '[expl_file_list]');
					flel.setAttribute ('data-dsp', node.getAttribute ('data-button-dsp'));
					if ( node.getAttribute ('data-button-dsp') == "mdl" ) {
						flel.setAttribute ('data-dsp-mdl', node.getAttribute ('data-dsp-mdl'));
						flel.setAttribute ('data-dsp', 'mdl');
					} else {
						flel.removeAttribute ('data-dsp-mdl')
					}
					load_table_in_zone (flel.getAttribute ('vars'), flel);
				} else {

				}
			}.bind (this));
			delegate (this.element, 'click', '[data-button-className]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel = first (this.element, '[expl_file_list]');
					flel.setAttribute ('data-className', node.getAttribute ('data-button-className'));

					load_table_in_zone (flel.getAttribute ('vars'), flel);
				}
			}.bind (this));
			delegate (this.element, 'click', '[data-button_chk]', function (event, node) {

				var flel = first (this.element, '[expl_file_list]');
				if ( node.classList.contains ('active') ) {
					flel.setAttribute ('data-show_chk', true);
				} else {
					flel.setAttribute ('data-show_chk', 'false');
				}

			}.bind (this));
			delegate (this.element, 'click', '[data-button-export]', function (event, node) {
				var flel = first (this.element, '[expl_file_list]');
				var vars = flel.getAttribute ('vars');
				runModule ('services/json_data_table', vars + '&csv_export=1');
			}.bind (this));
			delegate (this.element, 'click', '[app_button]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel = first (this.element, '[expl_file_list]');
					flel.setAttribute ('vars', node.getAttribute ('vars'));
					this.on_content_requested (event);
					event.preventDefault ();
					event.stopPropagation ();
					//
					if (node.getAttribute ('data-button_group') ) {

					}
					if (node.getAttribute ('data-button_sort') ) {

					}
					if (node.getAttribute ('data-button_sort_order') ) {

					}
					if ( !node.getAttribute ('-data-dsp') ) {
						load_table_in_zone (node.getAttribute ('vars'), flel);
					} else {
						switch (node.getAttribute ('data-dsp')) {
							case 'thumb' :

								break;
						}

					}
				}
			}.bind (this));
			delegate (this.element, 'click', '[app_button_scope]', function (event, node) {
				if ( first (this.element, '[expl_file_list]') ) {
					var flel = first (this.element, '[expl_file_list]');
					//flel.setAttribute('vars', node.getAttribute('vars'));
					this.on_content_requested (event);
					//

					var data_form_vars = queryToObject (flel.getAttribute ('vars'));
					var data_vars      = queryToObject (node.getAttribute ('vars'));
					var new_vars       = array_merge (data_form_vars, data_vars);

					new_vars      = array_unique (new_vars);
					var send_vars = objectToQuery (new_vars);
					//
					fire (flel, 'dom:load_data', { url_data : send_vars })
					//flel.setAttribute('vars', send_vars);
					//load_table_in_zone(send_vars, flel);

				}
			}.bind (this));

			delegate (this.element, 'click', 'thead input[type=checkbox]', function (event, node) {
				var ch = node.checked ? 'doCheck' : 'doUnCheck';
				qsa (this.expl_file_zone, 'tbody input[type=checkbox]').forEach (function (box) {
					box[ch] ();
				});

			}.bind (this));

			delegate (this.element, 'keyup', '[expl_search_button]', function (event, node) {
				if ( this.timerSearch )  clearTimeout (this.timerSearch)
				this.timerSearch = setTimeout (function () {
					this.act_search (node)
				}.bind (this), 250);
			}.bind (this));
			this.element.addEventListener ('mouseup', function (event) {
				this.act_disinput ();
			}.bind (this));
			this.element.addEventListener ('dom:click', function (event) {
				this.act_disinput ();
			}.bind (this));
			this.element.addEventListener ('dom:selectionMade', function (event) {
				this.act_disinput ();
			}.bind (this));
			this.element.addEventListener ('content:loaded', function (event) {
				cleanWhitespace (this.element);
				this.act_disinput ();
				// expl_search_button rebuild ?
				this.build_expl_search_button ();
				// cout !!!
				if ( first (this.element, '[expl_count_report]') ) {
					if ( first (this.element, '[expl_count]') ) {
						first (this.element, '[expl_count_report]').innerHTML = first (this.element, '[expl_count]').innerHTML
					}
				}
			}.bind (this));
		},
		act_search                     : function (node) {
			var valuesearch = node.value.toLowerCase (),
			    i           = 0;

			if ( this.where_search == 'world' ) {

				if ( first (this.element, '[expl_file_list]') ) {
					//
					var uiop      = first (this.element, '[expl_file_list]');
					var file_vars = uiop.getAttribute ('vars');
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
					var body = first (this.element, '.div_tbody');
					if ( !body ) return;
					Array.prototype.slice.call (body.children).forEach (function (node) {
						var inn = stripTags (node.innerHTML).toLowerCase ();
						if ( inn.indexOf (valuesearch) === -1 ) {
							setTimeout (function () { hide (node); }, 10)
						} else {
							setTimeout (function () { show (node); }, 10)
							i++;
						}
					}.bind (this))
				}.bind (this), 500)
			}
		},
		act_disinput                   : function () {
			setTimeout (function () {
				var enabled = qsa (this.element, '[bugchk]').length != 0;
				qsa (this.element, '.disinput').forEach (function (node) {
					node.classList[enabled ? 'add' : 'remove'] ('enabled');
				});
			}.bind (this), 125)
		},
		act_sort_zone                  : function () { //   attr[sort_zone_drag]
			// return;
			//console.log('act_sort_zone')
			delegate (this.element, 'dragstart', '[sort_zone_drag] [data-sort_element]', function (event, node) {

				event.dataTransfer.effectAllowed = "move";
				event.dataTransfer.setData ('dragid', identify (node));
				node.setAttribute ('dragged', 'dragged');
				//
				var node_up = node.parentElement && node.parentElement.closest ('[sort_zone_drag]');
				//
				if ( qsa (node_up, '.django').length == 0 ) {
					var django_tag = node.tagName;
					if ( django_tag.toLowerCase () == 'tr' ) {
						var django_add = '<td class="padding" colspan="' + node.children.length + '">&nbsp;</td>'
					} else { var django_add = ''}
					node.insertAdjacentHTML ('beforebegin', '<' + django_tag + '  id="django" class="django padding fond_noir rounded">' + django_add + '</' + django_tag + '>');
				}
				var django = first (node_up, '.django');

				delegate (node_up, 'dragover', '[data-sort_element]', function (event, node) {
					if ( event ) {
						event.stopPropagation ();
						event.preventDefault ();
					}
					node.before (show (django))
				}.bind (this))

				delegate (this.element, 'dragend', '[data-sort_element]', function (event, node) {
					if ( django ) hide (django);
				})

				delegate (node_up, 'drop', '[data-sort_element]', function (event, node) {
					if ( !event.dataTransfer.getData ('dragid') ) return;
					this.dnd_successful = true;
					var dropped_elem    = document.getElementById (event.dataTransfer.getData ('dragid'));
					event.preventDefault ();

					django.before (dropped_elem);
					dropped_elem.removeAttribute ('dragged')
					hide (django);
					fire (dropped_elem, 'dom:act_sort');
					// console.log('attr', dropped_elem, dropped_elem.getAttribute('data-table'))
					if ( !dropped_elem.getAttribute ('data-table') ) return;
					var table = dropped_elem.getAttribute ('data-table');
					var Table = ucfirst (table);
					//
					var pair = {};
					qsa (node_up, '[data-sort_element]').forEach (function (node, index) {
						pair['ordre' + Table + '[' + index + ']'] = node.getAttribute ('data-table_value');
					}.bind (this));
					var vars = objectToQuery (pair);
					var url  = vars + '&table=' + table;

					ajaxValidation ('app_sort', 'mdl/app/', url);
				}.bind (this))

			}.bind (this));

		},
		set_list_dragdrop_zone         : function () {
			qsa (this.element, '[expl_left_zone]').forEach (function (node) {
				this.act_left_zone (node);
			}.bind (this))
		},
		act_left_zone                  : function (node) { // drop avec data
			var dragdrop_zone = node;
			//  delegate(document.body, "dragstart", '[draggable]', function (event, node) {
			delegate (dragdrop_zone, "dragstart", '[draggable]', function (event, node) {
				console.log ('act_left_zone drag start')
				//node.style.opacity = '0.4'
				event.dataTransfer.effectAllowed = "move";
				event.dataTransfer.setData ('dragid', identify (node));

			}.bind (this));
			delegate (dragdrop_zone, 'dragleave', '[dropzone]', function (event, node) {
				node.style.background = "";
			});
			delegate (dragdrop_zone, 'dragenter', '[dropzone]', function (event, node) {

				event.dataTransfer.dropEffect = "move";
				event.preventDefault ();
				node.style.background         = "#FC3";
				return false;
			});
			delegate (dragdrop_zone, 'dragover', '[dropzone]', function (event, node) {
				event.dataTransfer.dropEffect = "move";
				event.preventDefault ();
				node.style.background         = "#FC3";
				return false;
			});

			delegate (dragdrop_zone, 'drop', '[dropzone]', function (event, node) {

				if ( !event.dataTransfer.getData ('dragid') ) return;
				if ( node.getAttribute ('noF_action') ) return;

				this.dnd_successful   = true;
				var val                   = event.dataTransfer.getData ('dragid');
				var dragged               = document.getElementById (val);
				event.preventDefault ();
				node.style.background = "";
				if ( dragged ) dragged.style.opacity = '1'
				var varstarget        = node.getAttribute ('data-vars') || '';
				var F_action          = node.getAttribute ('F_action') || 'app_update';
				var path              = node.getAttribute ('path') || 'mdl/app/';
				console.log('dropzone',node,F_action)
				if ( event.dataTransfer.getData ('multiple') ) {
					qsa (this.element, '.selected').forEach (function (elem) {
						var varstomove = elem.getAttribute ('data-vars') || '';
						if ( node.getAttribute ('data-append') ) {
							node.appendChild (elem);
						}
						ajaxValidation (F_action, path, varstarget + '&' + varstomove);
					}.bind (this))
				} else {
					var varstomove = dragged ? (dragged.getAttribute ('data-vars') || '') : '';
					if ( node.getAttribute ('data-append') && dragged ) {
						node.appendChild (dragged);
					}
					if ( F_action == 'form_up' ) {
						console.log({form: dragged && dragged.closest ('form')})
						ajaxFormValidation (dragged && dragged.closest ('form'));
					} else {

						ajaxValidation (F_action, path, varstarget + '&' + varstomove);
					}
				}
				fire (node, 'dom:act_drop', { drop_node : event.dataTransfer.getData ('dragid') });
			}.bind (this));
		},
		act_file_zone                  : function () {

			new myddeview (this.expl_file_zone, {
				only : 'input[type=checkbox]'
			});
			this.dnd_successful = false;
			delegate (this.expl_file_zone, "dragstart", '[draggable]', function (event, node) {

				node.style.opacity = '0.4'
				if ( qsa (this.element, '[bugchk]').length > 1 ) {
					event.dataTransfer.setData ('multiple', true);
					qsa (this.element, '[bugchk]').forEach (function (elem) {
						elem.style.opacity = '0.4';
					});
				}
				event.dataTransfer.effectAllowed = "move";
				event.dataTransfer.setData ('dragid', identify (event.target));
				this.dnd_successful              = false;
			}.bind (this));
			delegate (this.expl_file_zone, "dragend", '[draggable]', function (event, node) {
				console.log ('expl_file_zone drag end')
				if ( this.dnd_successful ) {
					event.target.parentNode.removeChild (event.target);
				} else {
					node.style.opacity = '1';
					qsa (this.element, '[bugchk]').forEach (function (elem) {
						elem.style.opacity = '1';
					});
				}
			}.bind (this));
			this.expl_file_zone.addEventListener ('dom:vars_changed', this.on_content_requested.bind (this));
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
			this.expl_search_button.setAttribute ('placeholder', 'Rechercher');
			this.expl_search_button.setAttribute ('data-menu', 'data-menu');
			// this.where_search
			var loc       = document.createElement ('a');
			loc.className = 'autoToggle active avoid';
			loc.innerHTML = 'Parmi les éléments visibles';
			loc.addEventListener ('click', function () {
				this.where_search = 'local'
			}.bind (this));
			var wrld       = document.createElement ('a');
			wrld.className = 'autoToggle  avoid';
			wrld.innerHTML = 'Partout';
			wrld.addEventListener ('click', function () {
				this.where_search = 'world'
				this.act_search (this.expl_search_button);
			}.bind (this));
			var str       = document.createElement ('div');
			str.className = 'applink applinkblock absolute contextmenu toggler';
			str.appendChild (loc);
			str.appendChild (wrld);
			// var str = '<div class="applink applinkblock absolute contextmenu"><a class="titre_entete">Parmi les éléments visibles</a><label class="titre_entete"><input type="checkbox" name="where" value="world" />Partout</label></div>';
			this.expl_search_menu = hide (document.createElement ('div'));
			this.expl_search_menu.appendChild (str);
			this.expl_search_wrapper              = document.createElement ('div');
			wrap (this.expl_search_button, this.expl_search_wrapper);
			this.expl_search_wrapper.appendChild (this.expl_search_menu);
			//this.expl_search_menu.addEventListener('click', function (event) {
			// event.preventDefault(); event.stopPropagation();
			// this.expl_search_button.focus();
			// }.bind(this));
			this.expl_search_button.addEventListener ('focus', function (event) {
				event.preventDefault ();
				event.stopPropagation ();
				// show(this.expl_search_menu);
			}.bind (this));
			this.expl_search_button.act_processed = true;
		},
		on_content_requested           : function (event) {

			if ( first (this.element, 'progress.auto_prog') ) {
				// id: auto_progress_
				var elem = event.target;
				if ( !elem.getAttribute ('vars') )
					return;
				var vars = queryToObject (elem.getAttribute ('vars'));
				if ( vars.table ) {
					first (this.element, 'progress.auto_prog').setAttribute ('data-table', vars.table);
					first (this.element, 'progress.auto_prog').setAttribute ('id', 'auto_progress_' + vars.table);
				}
			}
			qsa (this.element, '[expl_file_reload]').forEach (function (node) {

				if ( first (this.element, '[expl_file_list]') ) {
					var uiop      = first (this.element, '[expl_file_list]');
					var node_vars = node.getAttribute ('vars');
					var vars      = uiop.getAttribute ('vars');
					if ( node_vars == vars )
						return;
					var mdl = node.getAttribute ('mdl');
					node.socketModule (mdl, vars);
				}
			}.bind (this));

		},
		oncontentrequested             : function () {

		}
	}

	global.myddeExplorer = myddeExplorer;

})(window);
