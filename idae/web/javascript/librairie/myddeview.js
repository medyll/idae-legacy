/**
 * myddeview — shift-click / meta-click multi-row selection, delegated over
 * a checkbox class. Instantiated on every list's file zone
 * (`librairie/myddeExplorer.js:680`, `act_file_zone`), and directly on two
 * `produit_tarif_gamme` admin screens.
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 * The 2026-08-09 shim call-site inventory undercounted this file badly —
 * it only measured what the probe's screens actually triggered, and
 * nothing there did a shift-click or meta-click multi-select, so most of
 * this file's real shim surface (`.select`/`.without`/`.up`/`.toggleClassName`/
 * `.identify`/`.fire`/`Event.stop`) never showed up in that count at all.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function mv_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function mv_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function mv_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call (root.querySelectorAll (selector));
	}

	/** Prototype's Element#up(selector): starts at the parent, never at self. */
	function mv_up(node, selector) {
		if (!node || !node.parentElement) return null;
		return selector ? node.parentElement.closest (selector) : node.parentElement;
	}

	function mv_fire(node, eventName, memo) {
		if (!node) return null;
		var event = new CustomEvent (eventName, {bubbles: true, cancelable: true});
		event.memo = memo || {};
		node.dispatchEvent (event);
		return event;
	}

	/** Event delegation, Prototype's Element#on(event, selector, handler). */
	function mv_delegate(root, eventName, selector, handler) {
		root.addEventListener (eventName, function (event) {
			var target = event.target;
			while (target && target !== root) {
				if (target.nodeType === 1 && target.matches (selector)) {
					return handler (event, target);
				}
				target = target.parentNode;
			}
		}, false);
	}

	/* ------------------------------------------------------------------ */

	var myddeview = function () {
		this.initialize.apply (this, arguments);
	};

	myddeview.prototype = {
		initialize: function(element, options){
			// options
			this.options = Object.assign({
				only : null
			}, options || {});
			this.element = mv_el (element);
			// console.log('myddeview',this.element)
			// listener sur composants
			this.className = this.options.only
			mv_delegate (this.element, 'click', this.className, function(event,elem){this.selectableClicked(event,elem)}.bind(this))
			/*mv_qsa(document, this.className).forEach(function(elem){
				elem.addEventListener('click', this.selectableClicked.bind(this))
			}.bind(this))*/
		},
		selectableClicked: function(event,elem){
			// var elem = element;//Event.element(event)
			//if(!elem.classList.contains(this.className)) elem=mv_up(elem, this.className)
			if (event.shiftKey){
				// le 1er elem selectionné
				var checked = mv_qsa (this.element, this.className+'[bugchk=bugchk]').filter (function (n) { return n !== elem; });
				var firstElem = checked[0] || null;
				// console.log(firstElem);
				var allMatching = mv_qsa (this.element, this.className);
				var clickElemIndex = allMatching.indexOf (elem);
				var firstElemIndex = allMatching.indexOf (firstElem) ;
				//


				if(clickElemIndex>firstElemIndex){
					for(var i=firstElemIndex; i<clickElemIndex; i++){
							var index = i
							if(allMatching[index]){
								mv_up (allMatching[index], 'tr').classList.add ('selected')
								var ch = mv_up (allMatching[index], 'tr').querySelector ('input[type=checkbox]');
								ch.setAttribute ('bugchk','bugchk')
								//ch.setAttribute('checked','checked')
								ch.checked = true;
							}
						}
				}

				if(clickElemIndex<firstElemIndex){
					for(var i=clickElemIndex; i<firstElemIndex+1; i++){
						var index = i
						mv_up (allMatching[index], 'tr').classList.add ('selected')
						var ch = mv_up (allMatching[index], 'tr').querySelector ('input[type=checkbox]');
						ch.setAttribute ('bugchk','bugchk')
						//ch.setAttribute('checked','checked')
						ch.checked = true;
						}
				}

				if(clickElemIndex==firstElemIndex){ elem.classList.toggle ('selected')}
				this.hasSelection();
				var selectObj = window.getSelection();
				selectObj.collapseToStart();
				//event.preventDefault();
				return true;
			}
			if (event.metaKey){
				event.preventDefault ();
				event.stopPropagation ();
				elem.classList.toggle ('selected');
				this.hasSelection();
				return true;
			}

			/*mv_qsa(document, this.className).filter(function(n){return n!==elem;}).forEach(function(n){n.classList.remove('selected');});
			elem.classList.toggle('selected');
			this.hasSelection();*/

		},
		hasSelection: function(){
			size = document.querySelectorAll ('#'+mv_identify (this.element)+' .selected').length
			if(size==0){mv_fire (this.element, 'dom:unSelectionMade');}
			if(size!=0){mv_fire (this.element, 'dom:selectionMade');}
		}
	}

	global.myddeview = myddeview;

})(window);
