/**
 * Modified: 2026-08-11 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5).
 *
 * Two things live here:
 *
 * 1. `nl2br` — deliberately kept, and deliberately *not* deduplicated.
 *    app_php.js:11 defines a global of the same name with a different
 *    implementation (it replaces the newline with `<br />`, this one keeps
 *    the newline and only inserts `<br>` before it, skipping matches
 *    preceded by `>`). main_bag.js loads app_php.js at line 27 and this
 *    file at line 75, so *this* version is the one the app actually runs —
 *    including for app_socket.js:672. Deleting it would silently swap the
 *    behaviour of every live-data update.
 *
 * 2. `resizeInput` — auto-sizing text input. Live: app_insertionQ.js:419
 *    and myddeDatalist.js:179.
 *
 * Removed: `ResizingTextArea`, which was defined here but never
 * instantiated anywhere in the repo.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function ta_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	/** Prototype's Element#update: strip <script> before innerHTML, eval the original on a 10ms defer. */
	function ta_update(node, content) {
		if (content === undefined) content = '';
		content = String(content);
		node.innerHTML = engine_stripScripts(content);
		if (/<script/i.test(content)) {
			setTimeout(function () { engine_evalScripts(content); }, 10);
		}
		return node;
	}

	/**
	 * Prototype's Element#getWidth (via getDimensions): clientWidth, forcing
	 * the element visible first if it is display:none.
	 */
	function ta_getWidth(node) {
		if (window.getComputedStyle(node).display !== 'none') return node.clientWidth;
		var style = node.style;
		var originalVisibility = style.visibility,
			originalPosition = style.position,
			originalDisplay = style.display;
		style.visibility = 'hidden';
		if (originalPosition !== 'fixed') style.position = 'absolute';
		style.display = 'block';
		var width = node.clientWidth;
		style.display = originalDisplay;
		style.position = originalPosition;
		style.visibility = originalVisibility;
		return width;
	}

	/* ------------------------------------------------------------------ */

	// Shadows app_php.js's nl2br on purpose — see the file header.
	global.nl2br = function nl2br(str, is_xhtml) {

	    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
	}

	var resizeInput = function () {
		this.initialize.apply(this, arguments);
	};

	resizeInput.prototype = {
	    initialize: function (element, options) {

	        this.options = Object.assign({ }, options || {});
	        this.element = ta_el(element);
	        this.wrapper_holder = this.element.parentNode;

	        this.span = document.createElement('span');
	        this.span.setAttribute('style', 'position:absolute;z-index:-1;display:inline-block;right:0;top:-1500px;visibility:hidden;padding:0.5em;padding-right:2.5em;min-width:80px;');
	        ta_update(this.span, this.element.value);
	        // Pre-existing ordering quirk, kept verbatim: the span is measured
	        // here but only appended to the document on the next line, so this
	        // first measurement is always 0 (a detached element has no
	        // clientWidth). The initial width is therefore '0px', rescued by
	        // the minWidth below; subsequent keydown measurements are real.
	        this.element.style.width = ta_getWidth(this.span) + 'px';
	        this.element.style.minWidth =  '80px';
	        this.wrapper_holder.appendChild(this.span);
	        // Two-arg .on(event, handler): a plain listener under the shim,
	        // not delegation.
	        this.element.addEventListener('keydown', function (event) {
	            ta_update(this.span, this.element.value);
	            this.element.style.width = ta_getWidth(this.span) + 'px';
	        }.bind(this));
	    }

	}

	global.resizeInput = resizeInput;

})(window);
