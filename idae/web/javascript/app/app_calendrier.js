window.app_calendrier_fragment = '<div class="containerdisp">' + '<div class="handledisp">' + '<div class="cell buttondisp iconedisp aligncenter">' + '<i class="fa fa-navicon cursor" ></i>' + '</div>' + '<div class="cell">' + '<span data-title="" class="titlefrm"></span>' + '</div>' + '<div class="cell buttondisp buttonreduce aligncenter" >' + '<i class="fa fa-minus cursor"></i>' + '</div>' + '<div class="cell buttondisp popperdisp aligncenter">' + '<i class="fa fa-expand  cursor"></i>' + '</div>' + '<div class="cell buttondisp buttonclose aligncenter">' + '<i class="fa fa-times cursor"></i>' + '</div>' + '</div>' + '<div class="menudisp applink applinkblock" style="display: none;">' + '<a><i class="fa fa-refresh buttonrefresh"></i> Recharger</a><a><i class="fa fa-thumb-tack butonpin"></i> Pin to</a><a><i class="fa fa-share butonshare"></i> Partager</a><a><i class="fa fa-times buttonclose"></i> Fermer</a></div>' + '<div class="entetedisp" style="display: none;"></div>' + '<div class="innerdisp" ></div>' + '<div class="footerdisp" style="display:none"></div>' + '</div>';

/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Calendar widget nav (prev/next month, month/year
 * pickers) plus an optional "act as an input's calendar popup" mode
 * (`data-calendar_target`). Behaviour unchanged, including the
 * `$$(...).invoke('setValue', ...)` branch below, which was already
 * unreachable before this migration: `data-calendar_target` holds a bare
 * element id (set server-side from `$_POST['calendar_target']`,
 * app_calendrier.php:16), never a CSS selector, so treating it as one
 * (`$$`/querySelectorAll) never matches anything — and no `setValue`
 * method exists anywhere else in this app even if it did.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function cal_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	function cal_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function cal_delegate(root, eventName, selector, handler) {
		root.addEventListener(eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches(selector)) {
					return handler(event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	/* ------------------------------------------------------------------ */

	var app_calendrier = function () {
		this.initialize.apply(this, arguments);
	};

	app_calendrier.prototype = {
	    initialize : function(element, options) {
	        //
	        this.options = Object.assign({
	            className : '',
	            parent : document.body,
		        input : null
	        }, options || {});

	        this.element = element;
	        this.element_zone = cal_qsa(this.element, '[data-nav_zone]')[0];
	        this.element_nav = cal_qsa(this.element, '[data-nav_cal]')[0];

		    this.element_zone_mdl = cal_qsa(this.element_zone, '.cf_module')[0];
		    this.element_nav_mdl = cal_qsa(this.element_nav, '.cf_module')[0];

		    this.element_zone_mdl.setAttribute('scope',cal_identify(this.element));
		    this.element_zone_mdl.setAttribute('value',cal_identify(this.element));
		    this.element_nav_mdl.setAttribute('scope',cal_identify(this.element));
		    this.element_nav_mdl.setAttribute('value',cal_identify(this.element));
	        this.listen();
	    },
	    listen : function(){

	        cal_delegate(this.element,'click','.previous_month',function(event,elem){
	            vars = elem.getAttribute('vars');
	            this.element_zone.loadModule('app/app_calendrier/calendrier_day',vars);
	            // this.element_nav.loadModule('app/app_calendrier/calendrier_nav',vars);
		        reloadScope(cal_identify(this.element),cal_identify(this.element),vars)
	        }.bind(this));
	        cal_delegate(this.element,'click','.next_month',function(event,elem){
	            vars = elem.getAttribute('vars');
	            this.element_zone.loadModule('app/app_calendrier/calendrier_day',vars);
	            // this.element_nav.loadModule('app/app_calendrier/calendrier_nav',vars);
		        reloadScope(cal_identify(this.element),cal_identify(this.element),vars)
	        }.bind(this));
	        cal_delegate(this.element,'click','.change_month',function(event,elem){
	            vars = elem.getAttribute('vars');
	            this.element_zone.loadModule('app/app_calendrier/calendrier_month',vars);
	            }.bind(this))
	        cal_delegate(this.element,'click','.change_year',function(event,elem){
	            vars = elem.getAttribute('vars');
	            this.element_zone.loadModule('app/app_calendrier/calendrier_year',vars);

	            }.bind(this))
	        cal_delegate(this.element,'click','.select_month',function(event,elem){
	            vars = elem.getAttribute('vars');
	            this.element_zone.loadModule('app/app_calendrier/calendrier_day',vars);
		        reloadScope(cal_identify(this.element),cal_identify(this.element),vars)
	           // this.element_nav.loadModule('app/app_calendrier/calendrier_nav',vars);
	            }.bind(this))
	        cal_delegate(this.element,'click','.select_year',function(event,elem){
	            vars = elem.getAttribute('vars');
	            // this.element_nav.loadModule('app/app_calendrier/calendrier_nav',vars);
	            this.element_zone.loadModule('app/app_calendrier/calendrier_day',vars);
		        reloadScope(cal_identify(this.element),cal_identify(this.element),vars)
		        // console.log(cal_identify(this.element))

	            }.bind(this))

		    if(this.element.getAttribute('data-calendar_target')){
			    this.element.addEventListener('dom:act_click',function(event){
				    var targetId = this.element.getAttribute('data-calendar_target');
				    if(document.getElementById(targetId)) document.getElementById(targetId).value = event.memo.value;
				    if(cal_qsa(document, targetId).length) cal_qsa(document, targetId).forEach(function (n) { n.setValue(event.memo.value); });
				    var changeEvent = new CustomEvent('dom:act_change', {bubbles: true, cancelable: true});
				    changeEvent.memo = event.memo;
				    this.element.dispatchEvent(changeEvent);
			    }.bind(this))
		    }

	    }
	}

	global.app_calendrier = app_calendrier;

})(window);
