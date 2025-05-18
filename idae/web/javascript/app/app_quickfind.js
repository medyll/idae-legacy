// TABLE search
QuickFind = Class.create();
QuickFind.prototype = {
	search_icon: '<i search_icon="search_icon"  class="fa fa-search"></i>',
	spy_element: '<div spy_element="spy_element" class="inline ededed padding border4 boxshadow"></div>',
	initialize: function (element, options) {
		this.element = $(element)
		this.options = Object.extend({
			where: null,
			tag: 'div',
			spy: false,
			parent: false,
			post: false
		}, options || {});
		//

		this.element.identify();
		this.counter = 0;
		if (!this.options.where) {
			// console.log(this.element);
			var where_elem = this.element.next() || this.element.up().next();
			this.options.where = where_elem.identify()
		}
		this.element.insert({after: this.search_icon});

		this.element.on('keyup', function (event) {
			if (this.timer) clearTimeout(this.timer);
			this.timer = setTimeout(this.perform_search.bind(this), 500);
		}.bind(this));
	},
	perform_search: function () {

		var value = this.element.value.toLowerCase();
		// post
		if (this.options.post) {
			$(this.options.where).select('[data-table_count][data-table_count_max]').each(function (node) {
				// enlever vars[search]
				var tmp_vars = [];
				parse_str(node.readAttribute('vars'),tmp_vars);
				tmp_vars['search']
				console.log(node.readAttribute('vars'),tmp_vars);
				// console.log(node.readAttribute('data-table_count'),node.readAttribute('data-table_count_max'));
				if (node.readAttribute('data-table_count_max') > node.readAttribute('data-table_count') || tmp_vars['search'] ) {
					console.log('red')
					node.fire('dom:load_data',{url_data:node.readAttribute('vars')+'&search='+value});
				}
			}.bind(this))

		}

		i = 0;
		if (value.empty() || value.length < 2) {
			//return
		}
		if (value.empty() || value.length < 1) {
			$(this.options.where).select(this.options.tag).invoke('show');
			if (this.options.parent) $(this.options.where).select(this.options.parent).invoke('show');
		} else {
			$(this.options.where).select(this.options.tag).each(function (node) {
				
				var inn = node.innerHTML.stripTags().toLowerCase();
				if (!inn.include(value)) {
					Element.hide.defer(node);
				} else {
					Element.show.defer(node);
					this.counter++;
				}
			}.bind(this))

			if (this.options.parent) {
				$(this.options.where).select(this.options.parent).each(function (node) {
					if (this.options.post) {
						var tmp_vars = [];
						parse_str(node.readAttribute('vars'),tmp_vars);
						tmp_vars['search']
						// console.log(node.readAttribute('vars'),tmp_vars);
						// console.log(node.readAttribute('data-table_count'),node.readAttribute('data-table_count_max'));
						if (node.readAttribute('data-table_count_max') > node.readAttribute('data-table_count') || tmp_vars['search'] ) {
							// console.log('red')
							node.fire('dom:load_data',{url_data:node.readAttribute('vars')+'&search='+value});

						}
					}
					var inn = node.innerHTML.stripTags().toLowerCase();
					if (!inn.include(value)) {
						Element.hide.defer(node);
					} else {
						Element.show.defer(node);
					}
				}.bind(this))

			}
		}

		if (this.options.spy) {
			this.get_count();
		}
	},
	perform_data_search : function(){
		
	},
	get_count: function () {
		if (!$(this.options.spy)) {
			this.element.insert({after: this.spy_element});
			this.options.spy = $(this.element.querySelector('[spy_element]'))
		}

		this.options.spy.update(this.counter);
	},
	get_search: function (element, value) {

	}

}

