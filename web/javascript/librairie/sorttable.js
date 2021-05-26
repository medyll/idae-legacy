sortableTable           = {};
sortableTable           = Class.create ();
sortableTable.prototype = {
	initialize    : function (element, options) {
		// console.log('init sortable',element);
		var _this      = this;
		this.timer     = null;
		this.hasHeader = false;
		this.element   = $ ($ (element).identify ())
		this.firstRow  = $ (element).select ('thead tr').first () || null
		if ( !this.firstRow ) {
			return;
		}
		if ( this.firstRow.hasClassName ('avoid') ) {
			return;
		}
		this.firstRow.writeAttribute ({ 'sortDir' : 'asc' }).cleanWhitespace ()
		$ (this.element).setStyle ({ zIndex : '0' }).writeAttribute ({ 'isSortable' : 'true' });

		this.arrowUp   = '<i class="fa fa-angle-down"></i>';
		this.arrowDown = '<i class="fa fa-angle-up"></i>';

		this.resizeTimer = null;
		colGroup         = '';

		this.makeHeader ();
		// this.setSizeHeader();
	},
	makeHeader    : function () {
		if ( this.hasHeader == false ) {
			$ (this.firstRow).select ('td').each (function (node, index) {
				node.writeAttribute ({ 'sortDir' : 'up' });
				var new_name  = 'resizer_' + $ (node).identify ();
				var link      = '<a three class="sortheader explore_thead"> <span class="_flex_main">' + ucfirst ($ (node).innerHTML) + '</span><span class="sortarrow" ></span></a>';
				var inner_old = '<div id="' + new_name + '" style="visibility:hidden">' + ucfirst ($ (node).innerHTML) + "</div>";

				$ (node).update (link + inner_old);
				addResizeListener ($ (new_name), this.setSizeTD.bindAsEventListener (this, $ (new_name)));

				if ( !this.firstRow.hasClassName ('avoid') ) {
					node.observe ('click', this.isClicked.bindAsEventListener (this, node));
				}
			}.bind (this));
			this.hasHeader = true;
		}
	},
	setSizeTD     : function () {
		var node = arguments[1] || arguments[0];
		clearTimeout (this.timer);
		if ( $ (node) ) this.timer = setTimeout ($ (node).previous ().setStyle ({ 'width' : node.up ().offsetWidth + 'px' }), 1);
	},
	setSizeHeader : function () {
		console.log ("nooo setSize")
		return;
		if ( !this.element ) return;
		clearTimeout (this.resizeTimer);
		$ (this.firstRow).select ('.sortheader').each (function (node) {
			if ( node.getWidth != node.up ().offsetWidth && node.up ().offsetWidth != 0 ) {
				node.setStyle ({ 'width' : node.up ().offsetWidth + 'px' });
			}
		})
		this.resizeTimer = setTimeout (function () {
			this.setSizeHeader ();
		}.bind (this), 5000);

	},
	isClicked     : function (event, node) {
		this.setSizeTD (node);
		if ( $ (node).hasClassName ('avoid') ) {
			return false;
		}
		Event.stop (event)
		node.blur ();
		this.removeArrows ();
		this.columnIndex = node.cellIndex;
		this.beginSort (node.childNodes[0], node.cellIndex);
		//this.element.fire('dom:resize');
		return false;
	},
	getSortType   : function () {
		if ( !this.element.rows[1] ) {
			return false;
		}
		// console.log('rows',this.element.rows[1])
		instr = this.element.rows[1].cells[this.columnIndex];
		if ( $ (instr).childElements ().first () ) instr = $ (instr).childElements ().first ();
		str = $ (instr).innerHTML.unescapeHTML ()

		arrSort                   = Array;
		arrSort[this.columnIndex] = 'default'
		stypeSort                 = 'default'
		if ( str.replace (/[-]/g, '0').match (/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/) ) stypeSort = 'date'
		if ( str.match (/^\d\d[\/-]\d\d[\/-]\d\d$/) ) stypeSort = 'date'
		if ( str.search (/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/) !== -1 ) stypeSort = 'date'
		if ( str.match (/^-?[£\x24Û¢´€]?\d+\s*([,\.]\d{0,2})/) ) stypeSort = 'currency'
		if ( str.match (/^-?\d+\s*([,\.]\d{0,2})?[£\x24Û¢´€]/) ) stypeSort = 'currency'
		if ( str.match (/^[\d\.]+$/) ) stypeSort = 'numeric'

		return stypeSort;
	},
	removeArrows  : function () {
		this.firstRow.select ('.sortarrow').each (function (node) {
			$ (node).update ().up ().removeClassName ('sortheaderSorted');
		})
	},
	addArrows     : function () {
		node      = this.firstRow.cells[this.columnIndex];
		$ (node.childNodes[0]).addClassName ('sortheaderSorted');
		spanArrow = this.firstRow.down ('td', this.columnIndex).select ('.sortarrow')[0]
		if ( node.getAttribute ('sortDir') == 'up' ) {
			spanArrow.update (this.arrowDown)
			node.writeAttribute ({ 'sortDir' : 'down' })
		} else {
			spanArrow.update (this.arrowUp)
			node.writeAttribute ({ 'sortDir' : 'up' })
		}
	},
	beginSort     : function (lnk, cIndex) {
		this.columnIndex = cIndex;
		this.arrow       = $ ($ (lnk).querySelector ('.sortarrow'));
		this.sortType    = this.getSortType ();
		console.log ('sortType ', this.sortType);
		try {
			this.activeSort ();
		}
		catch (e) {
		}
	},
	activeSort    : function () {
		var _this         = this
		this.unsortedRows = $A (this.element.rows);
		tempArray         = this.unsortedRows.shift ();
		this.sortedRows   = this.unsortedRows.sortBy (function (node, i) {
			var td = $A (node.getElementsByTagName ("td"))[_this.columnIndex];
			if ( $ (td).childElements ().first () ) td = $ (td).childElements ().first ();
			var tmp_content = td.innerHTML.unescapeHTML ().stripTags ().toLowerCase ();

			switch (_this.sortType) {
				case 'date':
				{
					cellContent = tmp_content.replace (/[-]/g, '1');
					var date = cellContent.replace(/\-/g, '/');
					date = date.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, '$1/$2/$3'); // format before getTime
					console.log('date date ',date);
					cellContent = new Date(date).getTime() || -1;

					console.log('date content ',cellContent);

					/*if ( cellContent.length == 10 ) {
						dt1 = cellContent.substr (6, 4) + cellContent.substr (3, 2) + cellContent.substr (0, 2);
					} else if ( cellContent.length == 8 ) {
						yr = cellContent.substr (6, 2);
						if ( parseInt (yr) < 50 ) {
							yr = '20' + yr;
						} else {
							yr = '19' + yr;
						}
						dt1 = yr + cellContent.substr (3, 2) + cellContent.substr (0, 2);
					}
					cellContent = dt1*/
					break;
				}
				case 'currency':
				{
					cellContent = tmp_content.replace (/[^0-9.]/g, '')
					break;
				}

				case 'default':
				{
					cellContent = tmp_content;
					break;
				}
				case 'numeric':
				{
					cellContent = parseFloat (tmp_content.replace (' ', '').toLowerCase ());
					break;
				}
				default:
				{
					cellContent = tmp_content;
					break;
				}
			}
			return cellContent;
		});

		if ( $A (this.unsortedRows.first ().getElementsByTagName ("td"))[this.columnIndex].innerHTML.stripTags () == $A (this.sortedRows.first ().getElementsByTagName ("td"))[this.columnIndex].innerHTML.stripTags () ) {
			this.sortedRows.reverse ();
			this.firstRow.cells[this.columnIndex].setAttribute ('sortDir', 'down');
		} else {
			this.firstRow.cells[this.columnIndex].setAttribute ('sortDir', 'up');
		}
		sortedRowsHTML = "";
		this.sortedRows.each (function (node, i) {
			var Sortdiv = document.createElement ("div");
			Sortdiv.appendChild (node)
			sortedRowsHTML += Sortdiv.innerHTML;
		});
		// Element.update(this.element.getElementsByTagName("tbody")[0], sortedRowsHTML);
		this.element.getElementsByTagName ("tbody")[0].innerHTML = sortedRowsHTML;
		// this.setSizeHeader();
		this.addArrows ();
	}
}

function ts_sort_currency(a, b) {
	aa = ts_getInnerText (a.cells[SORT_COLUMN_INDEX]).replace (/[^0-9.]/g, '');
	bb = ts_getInnerText (b.cells[SORT_COLUMN_INDEX]).replace (/[^0-9.]/g, '');
	return parseFloat (aa) - parseFloat (bb);
}

function ts_sort_caseinsensitive(a, b) {
	aa = ts_getInnerText (a.cells[SORT_COLUMN_INDEX]).toLowerCase ();
	bb = ts_getInnerText (b.cells[SORT_COLUMN_INDEX]).toLowerCase ();
	if ( aa == bb ) return 0;
	if ( aa < bb ) return -1;
	return 1;
}