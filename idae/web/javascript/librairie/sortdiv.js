
sortDiv = {};
sortDiv = Class.create();
sortDiv.prototype = {
				initialize: function(element , options){
					var _this = this;
					this.options = options ;
					this.element=$(element)
					this.hasHeader = false;
					Element.cleanWhitespace(this.element); 
					$(this.options.control).cleanWhitespace(); 
					this.childList = function(){return document.getElementsByClassName('autoSort',$(this.element));}.bind(this)
					 
					this.element.setAttribute('isSortable','true');
 
					this.arrowUp = '<img src="images/tri_desc.gif" width="11">' ;
					this.arrowDown = '<img src="images/tri_asc.gif" width="11">' ;
					this.resizeTimer = null; 
					$A($(this.options.control).childNodes).each(function(node){
																	this.makeHeader(node) 
																	 }.bind(this))
				}, 
				isClicked: function(event,node){
					Event.stop(event)
					node.blur(); 
					this.removeArrows();
					this.sortBy = node.getAttribute('sortBy'); 
					this.activeSorter = node
					this.beginSort(node.childNodes[0],this.sortBy) ;
					return false;
				},
				setHeaderHTML: function(node){
				 			node.innerHTML = '<a href=""  class="'+node.className+' sortheader"> '+node.innerHTML.unescapeHTML()+'<span class="sortarrow" > </span></a>'; 
				},				
				makeHeader: function(node){  
					if(this.hasHeader == false){ node.setAttribute('sortDir','up'); 
							node.observe('click',this.isClicked.bindAsEventListener(this,node),false); 
							this.setHeaderHTML(node); 
					}
				},
				getSortType: function(){ 
					str = $(this.childList()[0]).getAttribute(this.sortBy)  || ''
					arrSort = Array;
					arrSort[this.columnIndex] = 'default'
					stypeSort = 'default'
					if (str.replace(/[-]/g, '0').match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)) stypeSort = 'date'  
					if (str.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)) stypeSort = 'date' 
					if (str.match(/^[$]/)) stypeSort = 'currency' 
					if (str.match(/^[\d\.]+$/)) stypeSort = 'numeric' 
					
					return stypeSort;
				},
				removeArrows: function(){
					$A(document.getElementsByClassName('sortarrow',this.options.control)).each(function(node){
																					node.innerHTML='';		 
																					Element.removeClassName(node.parentNode,'sortheaderSorted');
																							 })
				},
				addArrows: function(){
					node = this.activeSorter;
					Element.addClassName(node.childNodes[0],'sortheaderSorted');
					spanArrow = $A(document.getElementsByClassName('sortarrow',node))[0]
					if(node.getAttribute('sortDir') == 'up' )
					{spanArrow.innerHTML = this.arrowDown 
					node.setAttribute('sortDir','down')
					}else{ 
					spanArrow.innerHTML= this.arrowUp
					node.setAttribute('sortDir','up')
					}	
					
				},
				beginSort: function(lnk,cIndex){
					this.columnIndex = cIndex;
					this.arrow = document.getElementsByClassName('sortarrow',$(lnk))[0];
					this.sortType = this.getSortType(); 
				 this.activeSort();
				},
				activeSort: function(){
					var _this = this 
					this.unsortedRows =   $A(this.childList());
					tempArray = this.unsortedRows//.shift(); 
					this.sortedRows = this.unsortedRows.sortBy(function(node, i){
						switch ( _this.sortType) {
									case 'date':
									{
										 cellContent =  node.getAttribute(this.sortBy).unescapeHTML().toLowerCase().replace(/[-]/g, '1'); 
										 if (cellContent.length == 10) {
												dt1 = cellContent.substr(6,4)+cellContent.substr(3,2)+cellContent.substr(0,2);
											} else if (cellContent.length == 8) {
												yr = cellContent.substr(6,2);
												if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
												dt1 = yr+cellContent.substr(3,2)+cellContent.substr(0,2);
											}
										cellContent = dt1
										break;
									}
									case 'currency':
									{
										cellContent =   node.getAttribute(this.sortBy).unescapeHTML().toLowerCase().replace(/[^0-9.]/g,'')
									break;	
									}
									
									case 'default':
									{
										//console.log(this.sortBy);
										cellContent = node.getAttribute(this.sortBy).stripTags().toLowerCase();
										break;
									}
									case 'numeric':
									{
										cellContent =  parseFloat(node.getAttribute(this.sortBy).stripTags().toLowerCase());
										break;
									}
									default:
									{
										cellContent =  node.getAttribute(this.sortBy).stripTags().toLowerCase();
										break;
										}
								}
								return cellContent;
					}.bind(this));
					
					if($A(this.unsortedRows).first().getAttribute(this.sortBy) == $A(this.sortedRows).first().getAttribute(this.sortBy)){
						this.sortedRows.reverse();
						this.activeSorter.setAttribute('sortDir','down'); 
					}else{
						this.activeSorter.setAttribute('sortDir','up');
						}
					sortedRowsHTML = "";
					this.sortedRows.each(function(node, i){
						var Sortdiv = document.createElement("div"); 
						Sortdiv.appendChild(node)
						sortedRowsHTML += Sortdiv.innerHTML;
					});
				
					Element.update(this.element, sortedRowsHTML);
					
					this.addArrows();
				}
}





