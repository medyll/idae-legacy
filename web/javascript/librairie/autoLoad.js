autoLoad = {};
autoLoad = Class.create();
autoLoad.prototype = {

			initialize: function(element , file , options){
				
				this.options = options; 
				this.element = $(element) ; 
				this.element.cleanWhitespace();
				this.file = file;
				this.isLoading = false;  
				this.tbody = $(this.element).select('tbody').first() || $(this.element) ;
				
				this.recordCount = 0;
				this.page = 0;
				this.rowHeight=0;
				this.nbPageLoaded = 0;
				this.rppage = options.rppage ?  options.rppage : 30 ;
				this.fragmentPage = options.fragmentPage ?  options.fragmentPage : 3 ;
				
				this.realrppage = this.rppage; 
				// this.rppage = eval(this.rppage)/2
				this.onScrollFunction = options.onScrollFunction ?  options.onScrollFunction :  null;
				this.onFirstLoad = options.onFirstLoad ?  options.onFirstLoad :  null;
				this.spinner =   this.options.spinner  || null ; 
				this.body = options.body ||  '';

				this.ajaxOptions = null;
				this.nbPage = null
				
				this.spinner_bar = new Element('div',{style:'width:1%;border-bottom:1px solid #ccc;background-color:#999;height:5px;position:absolute;top:0px;left:0;z-index:500'}) 
				this.element.up().appendChild(this.spinner_bar);
				//
				this.buildBottom(); 
				//
				$(this.element).setStyle({position:'relative'});
				// $(this.element).setStyle({'padding-bottom':'1000px'});
				// RUN
				$(this.element).observe('content:loaded',function(){ 
					this.pageLoaded(); 
					this.getCount();   
				}.bind(this))
				//
				this.appendPage();  
			}, 
			buildBottom: function(){
				this.bottom_bar = new Element('div',{className:'stayDown table applink',style:'vertical-align:middle;margin:0 auto;width:100%;border-top:1px solid #ccc;background-color:#fff;height:30px;position:absolute;bottom:0px;left:0;z-index:500'}) 
				this.element.insert({after:this.bottom_bar});
				var cell1 =  new Element('div',{className: 'cell cellmiddle'} );
				var cell2 =  new Element('div',{className: 'cell cellmiddle'} ); 
				var cell3 =  new Element('div',{className: 'cell cellmiddle'} ); 
				
				this.btntest =  new Element('div',{className:' '}); 
				this.reportcount =  new Element('div',{className:' '}); 
				this.pager =  new Element('span',{className:'inline padding'}); 
				this.selectPage =  new Element('select',{className:'inputTiny noborder'});
				this.pagerNext =  new Element('a',{className:'inline padding cursor'});
				this.pagerPrevious =  new Element('a',{className:'inline padding cursor'});
				//
				this.pagerPrevious.update('<i class="fa fa-lg fa-chevron-left"></i>');
				this.pagerNext.update('<i class="fa fa-lg fa-chevron-right"></i>');
				
				$(cell1).insert(this.reportcount); 
				$(cell2).insert(this.btntest); 
				$(cell3).insert(this.pagerPrevious).setStyle({'width':'120px'});
				$(cell3).insert(this.pager);
				$(cell3).insert(this.pagerNext);
				// 
				this.pager.update(this.selectPage); 
				//
				$(this.bottom_bar).appendChild(cell1); 
				$(this.bottom_bar).appendChild(cell2);
				$(this.bottom_bar).appendChild(cell3);
		
				this.element.setStyle({'height':(this.element.getHeight()-30)+'px'})
				 
				this.pagerPrevious.on('click',function(){
						this.page = this.page - (this.fragmentPage*2)
						this.nbPageLoaded=0; 
						this.loadNextPage();
						}.bind(this));
				this.pagerNext.on('click',function(){
						this.nbPageLoaded=0; 
						this.loadNextPage();
						}.bind(this));
				this.selectPage.observe('change',function(){
						this.page = this.selectPage.getValue()  ; 
						this.nbPageLoaded=0; 
						this.loadNextPage();
						}.bind(this));
			}, 
			appendPage: function(){     
				post =  'page='+this.page+'&rppage='+this.rppage +'&'+this.body; 
				this.page++; 
				this.nbPageLoaded++; 
				 
				$(this.tbody).loadModule(this.file, post,{ 
					append: true					
				}) 
			},
			appendNextPage: function(){  
				this.appendPage();  
			},
			loadNextPage: function(){ 
				$(this.tbody).update();
				this.appendPage();  
				return;
			}, 
			pageLoaded: function(){  
				if(this.rowHeight==0)this.rowHeight = $(this.tbody).select('td').first().offsetHeight; 
			}, 
			getCount:function(){ 
				//
				this.countElement = $(this.tbody).select('.autoLoad-recordcount').first()
				this.recordCount = this.countElement.getAttribute('recordCount');
				//
 				this.nbPage = parseInt(this.recordCount / this.rppage) ; 
				if(this.selectPage.empty()){
					for (key=0; key < this.nbPage; key++){
						za =  new Element('option',{value:key});
						za.update(key);
						this.selectPage.appendChild(za);  
						}
				}
				//
				this.reportcount.update(' '+this.recordCount+' lignes');
				this.btntest.update(Math.round(this.nbPage/this.fragmentPage)+' page(s) ');
				
				if(this.spinner!=null){ $(this.spinner).update(this.page + ' / ' + parseInt(this.nbPage) +' ' +parseInt( (this.page/this.nbPage)*100 )); }  
				this.removeExtraTages(); 
				// 
				var percentage = Math.round(( eval(this.tbody.childElements().size()) / eval(this.recordCount))*100 );
				this.spinner_bar.setStyle({width:(percentage) + "%"});   
				//	 				
				if( (this.page <= this.nbPage)   ) {
					if(this.options.maxloadpages){
						if( (this.options.maxloadpages) == (this.nbPageLoaded)  ){return;}
						}
					this.appendNextPage();
					return;
				} 
				if(this.spinner!=null){ $(this.spinner).update( this.recordCount ); } 
			}, 
			removeExtraTages: function(){
				$(this.tbody).select('.autoLoad-recordcount').invoke('remove');
			}, 
			rang: function(){ 
			},
			tsScroll: function(){ 
			},
			needReload: function(){ 
			} 
				
}