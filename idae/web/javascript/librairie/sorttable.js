/**
 * sortableTable — click-to-sort table headers (`table.act_sort`, wired by
 * app_insertionQ.js's `table.act_sort` watcher).
 *
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Behaviour unchanged; only the DOM layer is native.
 * All HTML built or moved here is client-generated (header markup, row
 * re-ordering) — none of it is a server-fetched fragment, so this file does
 * not need the stripScripts/evalScripts handling app_socket.js's migration
 * needed for injected `<script>` tags.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function st_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function st_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	/** Prototype's Element#cleanWhitespace(): drop whitespace-only text-node children. */
	function st_cleanWhitespace(node) {
		if (!node) return node;
		var child = node.firstChild;
		while (child) {
			var next = child.nextSibling;
			if (child.nodeType === 3 && !/\S/.test(child.nodeValue)) node.removeChild(child);
			child = next;
		}
		return node;
	}

	/** Prototype's Element#down(tagName, index): the Nth descendant with that tag. */
	function st_down(node, tagName, index) {
		var matches = node.getElementsByTagName(tagName);
		return matches[index || 0] || null;
	}

	/** Prototype's Element#update(html): empty when called with no content. */
	function st_update(node, html) {
		if (!node) return node;
		node.innerHTML = html == null ? '' : String(html);
		return node;
	}

	function st_setStyle(node, styles) {
		Object.keys(styles).forEach(function (key) {
			node.style[key] = styles[key];
		});
		return node;
	}

	function st_stripTags(html) {
		return String(html).replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?>|<\/\w+>/gi, '');
	}

	/**
	 * Prototype's String#unescapeHTML(): strips tags, then decodes exactly
	 * the three entities escapeHTML produces — not a general HTML-entity
	 * decode (deliberately not `div.textContent`, which would also unescape
	 * e.g. `&nbsp;`, something Prototype's version never did).
	 */
	function st_unescapeHTML(html) {
		return st_stripTags(html).replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
	}

	/* ------------------------------------------------------------------ */

	var sortableTable = function () {
		this.initialize.apply(this, arguments);
	};

	sortableTable.prototype = {
		initialize    : function (element, options) {
			// console.log('init sortable',element);
			var _this      = this;
			this.timer     = null;
			this.hasHeader = false;
			this.element   = element;
			st_identify (this.element);
			this.firstRow  = this.element.querySelector ('thead tr') || null
			if ( !this.firstRow ) {
				return;
			}
			if ( this.firstRow.classList.contains ('avoid') ) {
				return;
			}
			this.firstRow.setAttribute ('sortDir', 'asc');
			st_cleanWhitespace (this.firstRow);
			st_setStyle (this.element, { zIndex : '0' });
			this.element.setAttribute ('isSortable', 'true');

			this.arrowUp   = '<i class="fa fa-angle-down"></i>';
			this.arrowDown = '<i class="fa fa-angle-up"></i>';

			this.resizeTimer = null;
			colGroup         = '';

			this.makeHeader ();
			// this.setSizeHeader();
		},
		makeHeader    : function () {
			if ( this.hasHeader == false ) {
				st_qsa (this.firstRow, 'td').forEach (function (node, index) {
					node.setAttribute ('sortDir', 'up');
					var new_name  = 'resizer_' + st_identify (node);
					var link      = '<a three class="sortheader explore_thead"> <span class="_flex_main">' + ucfirst (node.innerHTML) + '</span><span class="sortarrow" ></span></a>';
					var inner_old = '<div id="' + new_name + '" style="visibility:hidden">' + ucfirst (node.innerHTML) + "</div>";

					st_update (node, link + inner_old);
					// bindAsEventListener puts the real event first and bound
					// args after (event, node) — the opposite order from plain
					// .bind(this, node), which would prepend node before the
					// event and hand isClicked/setSizeTD their arguments
					// swapped. Wired explicitly here instead.
					var resizedNode = document.getElementById (new_name);
					addResizeListener (resizedNode, function (event) {
						this.setSizeTD (event, resizedNode);
					}.bind (this));

					if ( !this.firstRow.classList.contains ('avoid') ) {
						node.addEventListener ('click', function (event) {
							this.isClicked (event, node);
						}.bind (this));
					}
				}.bind (this));
				this.hasHeader = true;
			}
		},
		setSizeTD     : function () {
			var node = arguments[1] || arguments[0];
			clearTimeout (this.timer);
			if ( node ) this.timer = setTimeout (function () {
				// Guards two things that would throw identically under the old
				// Prototype call ($(node).previous() on a sibling-less node
				// returns undefined, and .setStyle() on that throws just the
				// same): the resize callback can fire after the row has been
				// removed from the DOM (detect-element-resize is async), and
				// node.previousElementSibling can be null on its own.
				if ( !node.parentElement || !node.previousElementSibling ) return;
				st_setStyle (node.previousElementSibling, { width : node.parentElement.offsetWidth + 'px' });
			}, 1);
		},
		// Dead since before this migration: the early `return` below means
		// nothing past it — including its own setTimeout(this.setSizeHeader)
		// recursion — has ever run. Its only non-recursive caller
		// (initialize()) has been calling it commented-out since at least
		// the shim era. Left as dead code rather than migrated, same policy
		// as the other unreachable blocks found this phase (myddeAttach.js).
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
			if ( node.classList.contains ('avoid') ) {
				return false;
			}
			event.preventDefault ();
			event.stopPropagation ();
			node.blur ();
			this.removeArrows ();
			this.columnIndex = node.cellIndex;
			this.beginSort (node.childNodes[0], node.cellIndex);
			//this.element.dispatchEvent(new CustomEvent('dom:resize', {bubbles: true}));
			return false;
		},
		getSortType   : function () {
			if ( !this.element.rows[1] ) {
				return false;
			}
			// console.log('rows',this.element.rows[1])
			instr = this.element.rows[1].cells[this.columnIndex];
			if ( instr.children[0] ) instr = instr.children[0];
			str = st_unescapeHTML (instr.innerHTML)

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
			st_qsa (this.firstRow, '.sortarrow').forEach (function (node) {
				st_update (node);
				node.parentElement.classList.remove ('sortheaderSorted');
			})
		},
		addArrows     : function () {
			node      = this.firstRow.cells[this.columnIndex];
			node.childNodes[0].classList.add ('sortheaderSorted');
			spanArrow = st_qsa (st_down (this.firstRow, 'td', this.columnIndex), '.sortarrow')[0]
			if ( node.getAttribute ('sortDir') == 'up' ) {
				st_update (spanArrow, this.arrowDown)
				node.setAttribute ('sortDir', 'down')
			} else {
				st_update (spanArrow, this.arrowUp)
				node.setAttribute ('sortDir', 'up')
			}
		},
		beginSort     : function (lnk, cIndex) {
			this.columnIndex = cIndex;
			this.arrow       = lnk.querySelector ('.sortarrow');
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
			this.unsortedRows = Array.prototype.slice.call (this.element.rows);
			tempArray         = this.unsortedRows.shift ();
			this.sortedRows   = this.unsortedRows.slice ().sort (function (a, b) {
				var contentOf = function (node) {
					var td = node.getElementsByTagName ("td")[_this.columnIndex];
					if ( td.children[0] ) td = td.children[0];
					var tmp_content = st_stripTags (st_unescapeHTML (td.innerHTML)).toLowerCase ();

					switch (_this.sortType) {
						case 'date':
						{
							var cellContent = tmp_content.replace (/[-]/g, '1');
							var date = cellContent.replace(/\-/g, '/');
							date = date.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/, '$1/$2/$3'); // format before getTime
							console.log('date date ',date);
							cellContent = new Date(date).getTime() || -1;

							console.log('date content ',cellContent);
							return cellContent;
						}
						case 'currency':
							return tmp_content.replace (/[^0-9.]/g, '');
						case 'numeric':
							return parseFloat (tmp_content.replace (' ', '').toLowerCase ());
						case 'default':
						default:
							return tmp_content;
					}
				};
				var ca = contentOf (a), cb = contentOf (b);
				if ( ca < cb ) return -1;
				if ( ca > cb ) return 1;
				return 0;
			});

			if ( st_stripTags (this.unsortedRows[0].getElementsByTagName ("td")[this.columnIndex].innerHTML) == st_stripTags (this.sortedRows[0].getElementsByTagName ("td")[this.columnIndex].innerHTML) ) {
				this.sortedRows.reverse ();
				this.firstRow.cells[this.columnIndex].setAttribute ('sortDir', 'down');
			} else {
				this.firstRow.cells[this.columnIndex].setAttribute ('sortDir', 'up');
			}
			sortedRowsHTML = "";
			this.sortedRows.forEach (function (node, i) {
				var Sortdiv = document.createElement ("div");
				Sortdiv.appendChild (node)
				sortedRowsHTML += Sortdiv.innerHTML;
			});
			// this.element.getElementsByTagName("tbody")[0] is populated entirely
			// from `node`s already in the live DOM (moved into a detached div
			// above, never fetched), so no script-eval handling is needed here —
			// unlike app_socket.js's injected server fragments.
			this.element.getElementsByTagName ("tbody")[0].innerHTML = sortedRowsHTML;
			// this.setSizeHeader();
			this.addArrows ();
		}
	}

	global.sortableTable = sortableTable;

})(window);

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
