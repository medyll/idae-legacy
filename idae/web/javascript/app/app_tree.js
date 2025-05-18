app_tree = Class.create ({
	initialize : function (element, options) {
		this.options = {
			duration : 10
		}
		this.element = element;
		this.build ();
		this.listen();
		// data-tree
	},
	build      : function () {
		//
		if ( !$ (this.element).hasClassName ('tree_ready') ) {
			this.element.addClassName ('tree_ready');
			// console.log(this.element)

			this.element.select ('.auto_tree').each (function (node) {
				if ( $ (node).next () ) {
                   $ (node).next ().addClassName ('auto_tree_next')
					if ( $ (node).next ().visible () ) {
						$ (node).addClassName ('opened')
					} else {
						$ (node).removeClassName ('opened')
					}
				}
			}.bind (this))
			this.element.on ('click', '.auto_tree_caret', function (event, node) {
				this.clicked (node);
			}.bind (this));
			this.element.on ('click', '[auto_tree_click]', function (event, node) {
				this.clicked ($ (node).up ('.auto_tree').select ('.auto_tree_caret').first ());
			}.bind (this));
		}
	},
	listen      : function () {
		this.element.on ('content:loaded' ,function (event) {
			
		})
			
		this.element.on ('content:loaded','.auto_tree_next', function (event, node) { 
			this.check(node);
		}.bind(this))
	},
	check      : function (node) {
		// auto_tree_caret 
		
		var caret = node.previous().down('.auto_tree_caret');
		var inner_node = $ (node).innerHTML.stripTags ().strip ();
		if ( inner_node.length == 0 ) { $ (caret).addClassName ('hidden_caret')}else{
			$ (caret).removeClassName ('hidden_caret')
		}
		 
	},
	reposition : function () {
		
	},
	clicked    : function (node) {
		var parent = node.up ('.auto_tree');
		//
		$ (parent).toggleClassName ('opened');
		if ( $ (parent).hasClassName ('opened') ) {
			$ (parent).next ().show ().removeClassName ('none');
		} else {
			$ (parent).next ().hide ().addClassName ('none');
		}
		if ( $ (this.element).readAttribute ('auto_tree_accordeon') ) {
			$ (this.element).select ('.auto_tree_next').without ($ (parent).next ()).invoke ('hide')
			$ (this.element).select ('.auto_tree').without ($ (parent)).invoke ('removeClassName', 'opened')
		}
	}
})