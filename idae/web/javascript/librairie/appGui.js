/**
 * Scriptaculous' Effect.Move (mode: 'absolute'), reduced to what the one
 * caller below needs: animate an already-positioned element's `left`/`top`
 * to the given absolute pixel values over `duration` seconds. Effect.Move
 * defaulted unspecified x/y to 0, so the caller passing only `x` also slid
 * `top` back to 0 — kept here explicitly rather than reproduced implicitly.
 */
function moveElementTo(node, x, y, duration) {
	if ( !node ) return node;
	x = x || 0;
	y = y || 0;
	duration = (duration || 1.0) * 1000;
	if ( window.getComputedStyle (node).position === 'static' ) node.style.position = 'relative';
	var previousTransition = node.style.transition;
	node.style.transition = 'left ' + duration + 'ms ease, top ' + duration + 'ms ease';
	node.style.left = x + 'px';
	node.style.top = y + 'px';
	setTimeout (function () {
		node.style.transition = previousTransition;
	}, duration);
	return node;
}

var appGui       = Class.create ();
appGui.prototype = {
	initialize    : function (element, options) {
		this.element = $ (element);
		this.options = Object.extend ({
			title     : 'app ... ',
			mode      : 'slide',
			fitScreen : false
		}, options || {});
		// console.log(this.element)
		this.element.cleanWhitespace ();
		/*document.body.observe('resize',function(){
		 $$('.inArea').each(function(node){
		 $(node).setStyle('maxWidth:' + eval((document.body.offsetWidth)-120) + 'px')
		 })
		 }.bind(this))*/
		return this;
	},
	add           : function (options) {

		if ( $ (options.element_id) ) {
			this.activate ($ (options.element_id).options);
			return;
		}

		this.addHistButton (options);

		options.element_id = 'frm' + options.title.gsub (' ', '_').toLowerCase ();
		options.onglet_id  = 'ong' + options.title.gsub (' ', '_').toLowerCase ();
		options.container  = options.container || this.element;
		options.fitScreen  = options.fitScreen || this.options.fitScreen;

		// 
		if ( $ (options.element_id) ) {
			if ( $ (options.element_id).options ) this.activate ($ (options.element_id).options);
			return $ (options.element_id);
		}
		var wrap = new Element ('div', { className : 'inArea' });
		$ (wrap).setStyle ({ position : 'relative' })
		document.body.appendChild (wrap);
		if ( options.mainIdent ) {
			$ (wrap).id = options.mainIdent;
		}
		var element = new Element ('div', { id : options.element_id, style : 'position:relative;height:100%;' });
		$ (element).setStyle ({ height : '100%' })

		if ( options.fitScreen == true ) {
			//$(wrap).setStyle({'width' :eval((document.body.offsetWidth))+'px'}) 
		}
		
		element.options = options
		//	 
		$ (options.container).insert ($ (wrap));
		
		$ (wrap).insert ($ (element));
		/**/
		if ( options.taskBar && options.title ) {
			this.addButton (options);
		}
		
		this.activate (options);
		if ( options.fitScreen != true ) {
			$ (element).on ('click', function () {
				if ( $ (element).offsetLeft < ($ (options.container).offsetLeft * (-1)) ) this.activate (options)
			}.bind (this))
		}
		return element
	},
	addButton     : function (options) {
		
		if ( $ (options.onglet_id) ) return true;

		//
		/*if(options.vars){
		 var az = '<div class="slaveshow absolute boxshadow" style="left:0%;min-width:250px;bottom:0;z-index:20000">'
		 + '<div class="blanc" act_defer mdl="app/app/app_fiche_mini" vars="'+options.vars+'"></div>'
		 +'</div>';
		 cell2.insert(az);
		 }*/

		var frag_taskBarButton = window.APP.APPTPL['taskBarButton']
		var elem_taskBarButton = create_element_of (frag_taskBarButton);
		elem_taskBarButton.id  = options.onglet_id;

		var buttonbody  = $(elem_taskBarButton.querySelector ('.buttonbody'));
		var inbody      = $(elem_taskBarButton.querySelector ('.inbody'));
		var buttonmax   = $(elem_taskBarButton.querySelector ('.buttonmax'));
		var buttonclose = $(elem_taskBarButton.querySelector ('.buttonclose'));

		$ (inbody).insert (options.title);

		$ (options.taskBar).appendChild ($ (elem_taskBarButton));

		$ (buttonmax).observe ('click', function (event) {
			Event.stop (event);
			davars = { titre : options.title, mdl : options.file, vars : options.vars };
			localStorage.setItem ('popup', JSON.stringify (davars));
			localStorage.setItem ('popup_master', JSON.stringify (davars));
			popopen ('index.php?titre=' + options.title + '&mdl=' + options.file + '&' + options.vars, '', '', options.title, true)
		}.bind (this))
		$ (buttonclose).observe ('click', function (event) {
			Event.stop (event);
			this.close (options);
		}.bind (this))
		$ (inbody).observe ('dblclick', function (event) {
			Event.stop (event);
			this.close (options);
		}.bind (this))
		$ (inbody).observe ('click', function (event) {
			if ( $ (options.onglet_id).hasClassName ('active') ) {
				Event.stop (event);
				$ (options.container).hide ();
				$ (options.onglet_id).removeClassName ('active');
				return;
			}
			this.activate (options);
		}.bind (this))
		//
		this.addHistButton (options);
	},
	addHistButton : function (options) {
		if ( $ (options.taskBar).select ('.back').size () == 0 && $ (options.taskBar).select ('.taskBarButton').size () > 1 ) {
			var appback = new Element ('div', { className : 'back', id : 'back' + options.element_id, 'style' : 'position:relative;' });
			$ (options.taskBar).select ('.taskBarButton').first ().insert ({ before : $ (appback) });
		}
	},
	activate      : function (options) {
		$ (options.container).show ();
		daParent = $ (options.element_id).up () || $ (options.element_id);
		//
		if ( options.taskBar && options.title ) {
			if($(options.onglet_id)) $(options.onglet_id).addClassName ('active').siblings ().invoke ('removeClassName', 'active');
		}
		if ( options.mode == 'slide' ) {
			delta = eval (daParent.offsetLeft);
			if ( options.fitScreen != true ) {
				delta = eval (parent.offsetLeft) - eval (eval (document.body.offsetWidth) / 5)
			}
			moveElementTo ($ (options.container), eval (-1) * delta, 0, 0.1);
			$ (options.onglet_id).show ();
		} else {
			$ (options.onglet_id).show ();
			$ (daParent).siblings ().invoke ('hide')
			setTimeout (function () {$ (daParent).show (); }.bind (this), 50)
		}
		
	},
	close         : function (options) {
		
		if ( options.taskBar && options.title ) {
			$ (options.onglet_id).remove ();
		}
		if ( $ (options.element_id) ) {
			$ (options.element_id).fire ('dom:close');
			if ( $ (options.element_id) != $ (options.container) ) {
				$ (options.element_id).up ().remove ();
			}
		}
		
		$ (options.container).hide ();
	},
	next          : function (options) {
		
	},
	previous      : function (options) {

	}
}