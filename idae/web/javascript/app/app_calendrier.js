window.app_calendrier_fragment = '<div class="containerdisp">' + '<div class="handledisp">' + '<div class="cell buttondisp iconedisp aligncenter">' + '<i class="fa fa-navicon cursor" ></i>' + '</div>' + '<div class="cell">' + '<span data-title="" class="titlefrm"></span>' + '</div>' + '<div class="cell buttondisp buttonreduce aligncenter" >' + '<i class="fa fa-minus cursor"></i>' + '</div>' + '<div class="cell buttondisp popperdisp aligncenter">' + '<i class="fa fa-expand  cursor"></i>' + '</div>' + '<div class="cell buttondisp buttonclose aligncenter">' + '<i class="fa fa-times cursor"></i>' + '</div>' + '</div>' + '<div class="menudisp applink applinkblock" style="display: none;">' + '<a><i class="fa fa-refresh buttonrefresh"></i> Recharger</a><a><i class="fa fa-thumb-tack butonpin"></i> Pin to</a><a><i class="fa fa-share butonshare"></i> Partager</a><a><i class="fa fa-times buttonclose"></i> Fermer</a></div>' + '<div class="entetedisp" style="display: none;"></div>' + '<div class="innerdisp" ></div>' + '<div class="footerdisp" style="display:none"></div>' + '</div>';


var app_calendrier = {};
app_calendrier = Class.create();
app_calendrier.prototype = {
    initialize : function(element, options) {
        //
        this.options = Object.extend({
            className : '',
            parent : document.body,
	        input : null
        }, options || {});
 
        this.element = element;
        this.element_zone = this.element.select('[data-nav_zone]').first();
        this.element_nav = this.element.select('[data-nav_cal]').first();

	    this.element_zone_mdl = this.element_zone.select('.cf_module').first();
	    this.element_nav_mdl = this.element_nav.select('.cf_module').first();

	    this.element_zone_mdl.setAttribute('scope',this.element.identify());
	    this.element_zone_mdl.setAttribute('value',this.element.identify());
	    this.element_nav_mdl.setAttribute('scope',this.element.identify());
	    this.element_nav_mdl.setAttribute('value',this.element.identify());
        this.listen();
    },
    listen : function(){
          
        $(this.element).on('click','.previous_month',function(event,elem){
            vars = elem.readAttribute('vars');
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_day',vars);
            // $(this.element_nav).loadModule('app/app_calendrier/calendrier_nav',vars);
	        reloadScope(this.element.identify(),this.element.identify(),vars)
        }.bind(this));
        $(this.element).on('click','.next_month',function(event,elem){
            vars = elem.readAttribute('vars');
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_day',vars);
            // $(this.element_nav).loadModule('app/app_calendrier/calendrier_nav',vars);
	        reloadScope(this.element.identify(),this.element.identify(),vars)
        }.bind(this));
        $(this.element).on('click','.change_month',function(event,elem){
            vars = elem.readAttribute('vars');
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_month',vars);
            }.bind(this))
        $(this.element).on('click','.change_year',function(event,elem){
            vars = elem.readAttribute('vars');   
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_year',vars);

            }.bind(this))
        $(this.element).on('click','.select_month',function(event,elem){
            vars = elem.readAttribute('vars');
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_day',vars);
	        reloadScope(this.element.identify(),this.element.identify(),vars)
           // $(this.element_nav).loadModule('app/app_calendrier/calendrier_nav',vars);
            }.bind(this))
        $(this.element).on('click','.select_year',function(event,elem){
            vars = elem.readAttribute('vars');   
            // $(this.element_nav).loadModule('app/app_calendrier/calendrier_nav',vars);
            $(this.element_zone).loadModule('app/app_calendrier/calendrier_day',vars);
	        reloadScope(this.element.identify(),this.element.identify(),vars)
	        // console.log(this.element.identify())

            }.bind(this))

	    if(this.element.readAttribute('data-calendar_target')){
		    $(this.element).on('dom:act_click',function(event){
			    if($(this.element.readAttribute('data-calendar_target'))) $(this.element.readAttribute('data-calendar_target')).value = event.memo.value;
			    if($$(this.element.readAttribute('data-calendar_target'))) $$(this.element.readAttribute('data-calendar_target')).invoke('setValue',event.memo.value ) ;
			    $(this.element).fire('dom:act_change',event.memo);
		    }.bind(this))
	    }

    }
}

