/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Sizes a grid's cells to an even split of its
 * parent, then fades them in. One live caller:
 * `app_planning_mens.php:164` (`new tableGui($('tablePlanningMensuel'),
 * {numRow: N, onlyClass: 'caseMois'})`); the only other call site,
 * mdlCalendrierListYear.php:45, is commented out.
 *
 * Behaviour unchanged, including two pre-existing oddities documented
 * inline: the document-wide getElementsByClassName lookup, and the
 * childNodes branch that no live caller reaches.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function tg_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function tg_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	/* ------------------------------------------------------------------ */

	var tableGui = function () {
		this.initialize.apply(this, arguments);
	};

	tableGui.prototype = {
		initialize: function(element,options){
			this.options = Object.assign({
			numCol: null,
			numRow: null,
			onlyClass: null
			}, options || {});

			this.element = tg_el(element);

			if(this.options.onlyClass==null){
				// Reached only by a caller that passes no `onlyClass` — none
				// does today (the sole such call site is commented out). Note
				// childNodes includes text nodes, which have no .style, so the
				// build() loop below would throw on any whitespace between
				// tags. Kept verbatim rather than "fixed": nothing exercises
				// it, and changing it would be guessing at intent.
				this.allChild = Array.prototype.slice.call(this.element.childNodes)
			}else{
				// document.getElementsByClassName(cls, container): Prototype
				// 1.6 honoured that second argument and scoped the search.
				// The shim only patches Element.prototype, not Document, so
				// this call already resolves to the *native* method, which
				// ignores the second argument and searches the whole
				// document. That divergence arrived with the Phase 3/4 swap,
				// not with this migration — preserved as-is here (the one
				// live caller has a single such grid on screen, so scoped and
				// document-wide return the same nodes).
				this.allChild = Array.prototype.slice.call(document.getElementsByClassName(this.options.onlyClass))
				}

			this.element.style.opacity = 0;
			try{this.element.style.display = '';}catch(e){ }
			if(this.options.numCol){
				this.defaultColWidth = parseInt(this.element.parentNode.offsetWidth / this.options.numCol)
				}
			if(this.options.numRow){
				this.defaultRowHeight = parseInt(this.element.parentNode.offsetHeight / this.options.numRow)
				}

			this.build();
			var timer
			// 'Resize' (capital R) is a custom event name — nothing in the app
			// ever fires it, so this listener has never run. Verbatim.
			this.element.parentNode.addEventListener('Resize',function(){
											if(timer){clearTimeout(timer)}
											 timer =   setTimeout(function(){
																		  /* console.log('resize!!');
																		   if(this.options.numCol){
																				this.defaultColWidth = parseInt(this.element.parentNode.offsetWidth / this.options.numCol)
																				}
																			if(this.options.numRow){
																				this.defaultRowHeight = parseInt(this.element.parentNode.offsetHeight / this.options.numRow)
																				}
																		   this.reBuild()*/
																		   }.bind(this),1000)
											   }.bind(this),true)
		},
		build:function(){
			this.allChild.forEach(function(node,index){
											if(this.options.numCol){
												node.style.width = this.defaultColWidth + 'px'
											}
											if(this.options.numRow){
												node.style.height = this.defaultRowHeight + 'px'
											}
										}.bind(this));
			this.allChild.forEach(function(node,index){
										try{appearElement(node,{duration: 0.2}); }catch(e){}
										});
		 // Was Scriptaculous' Effect.Appear via the shim; appearElement
	 // (engine/methods.js) is the native replacement, same contract.
	 appearElement(this.element,{duration: 0.1})
			/* console.log(this.element ,this.element.up() )*/
			if(this.element.scrollHeight > this.element.parentElement.offsetHeight){
				//this.element.parentElement.style.height = this.element.offsetHeight +'px'
				}
			//this.element.style.opacity = 1;
		},
		reBuild:function(){
			this.allChild.forEach(function(node,index){
											if(this.options.numCol){
												node.style.width = this.defaultColWidth + 'px'
											}
											if(this.options.numRow){
												node.style.height = this.defaultRowHeight + 'px'
											}
										}.bind(this));
		}
	}

	global.tableGui = tableGui;

})(window);
