/**
 * Created by Mydde on 22/06/2017.
 */
(function(){

})(this)

class MutateQ {

	constructor(config = {}, options = {}) {
		this.options = { nodeParent : document.documentElement, capture : ['childList', 'attributes', 'characterData'] };
		this.rules   = [];

		Object.assign (this.options, options);

		this.observ ();
	}

	register(str, fct, capture = this.options.capture) {
		this.rules.push ({ str : str, fct : fct, capture : capture });
	}

	observ() {
		var observer = new MutationObserver (function (mutations) {
			// console.time('FIRST')
			for (var rule of this.rules) {
				// console.log(rule);
				for (var i = 0 ; i < mutations.length ; i++) {
					if ( mutations[i].target.matches (rule.str) ) {

						if ( rule.capture.find (x => x = mutations[i].type) ) {
							// console.log (rule.str, rule.fct, mutations[i].type);
							rule.fct (mutations[i].target, mutations[i])
						}
					} else {
						if(mutations[i].target.matches ('[act_target]')){
						console.log('mvfd')
						}
					}
					 /* switch (mutations[i].type) {
					 case 'attributes':
					 if (mutations[i].target.matches(rule)) {
					 console.log(rule, mutations[i]);
					 }

					 break;
					 case 'childList':
					 break;
					 }*/
				}
			}
			// console.timeEnd('FIRST')
		}.bind (this));

		var config = { characterData : true, attributes : true, childList : true, subtree : true }
		observer.observe (document.documentElement, config);
		console.log ('mutate init')
	}

	recol() {

	}
}

var mutateQ = new MutateQ ();
mutateQ.register ('[act_target]', function (node, mutate = {}) {
	var node_elem = $ (node);
	//node_elem.insert('<div>red</div>')
	console.log (node_elem, mutate)

});

