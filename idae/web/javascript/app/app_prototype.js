/**
 * Created by Mydde on 24/06/2017.
 */

class app_prototype {

}

function app_addMethods(source) {
	var ancestor   = this.superclass && this.superclass.prototype,
	    properties = Object.keys(source);

	if (IS_DONTENUM_BUGGY) {
		if (source.toString != Object.prototype.toString)
			properties.push("toString");
		if (source.valueOf != Object.prototype.valueOf)
			properties.push("valueOf");
	}

	for (var i = 0, length = properties.length; i < length; i++) {
		var property = properties[i], value = source[property];
		if (ancestor && Object.isFunction(value) &&
		    value.argumentNames()[0] == "$super") {
			var method = value;
			value = (function(m) {
				return function() { return ancestor[m].apply(this, arguments); };
			})(property).wrap(method);

			value.valueOf = (function(method) {
				return function() { return method.valueOf.call(method); };
			})(method);

			value.toString = (function(method) {
				return function() { return method.toString.call(method); };
			})(method);
		}
		this.prototype[property] = value;
	}

	return this;
}

(function(GLOBAL) {
	
	var UNDEFINED;
	var SLICE = Array.prototype.slice;
	
	var DIV = document.createElement('div');
	
	
	function $app(element) {
		if (arguments.length > 1) {
			for (var i = 0, elements = [], length = arguments.length; i < length; i++)
				elements.push($app(arguments[i]));
			return elements;
		}
		
		if (Object.isString(element))
			element = document.getElementById(element);
		return Element.extend(element);
	}
	
	GLOBAL.$ = $;
	
	
	if (!GLOBAL.Node) GLOBAL.Node = {};
	
	if (!GLOBAL.Node.ELEMENT_NODE) {
		Object.extend(GLOBAL.Node, {
			ELEMENT_NODE:                1,
			ATTRIBUTE_NODE:              2,
			TEXT_NODE:                   3,
			CDATA_SECTION_NODE:          4,
			ENTITY_REFERENCE_NODE:       5,
			ENTITY_NODE:                 6,
			PROCESSING_INSTRUCTION_NODE: 7,
			COMMENT_NODE:                8,
			DOCUMENT_NODE:               9,
			DOCUMENT_TYPE_NODE:         10,
			DOCUMENT_FRAGMENT_NODE:     11,
			NOTATION_NODE:              12
		});
	}
	
	var ELEMENT_CACHE = {};
	
	function shouldUseCreationCache(tagName, attributes) {
		if (tagName === 'select') return false;
		if ('type' in attributes) return false;
		return true;
	}
	
	var HAS_EXTENDED_CREATE_ELEMENT_SYNTAX = (function(){
		try {
			var el = document.createElement('<input name="x">');
			return el.tagName.toLowerCase() === 'input' && el.name === 'x';
		}
		catch(err) {
			return false;
		}
	})();
	
	
	var oldElement = GLOBAL.Element;
	function Element(tagName, attributes) {
		attributes = attributes || {};
		tagName = tagName.toLowerCase();
		
		if (HAS_EXTENDED_CREATE_ELEMENT_SYNTAX && attributes.name) {
			tagName = '<' + tagName + ' name="' + attributes.name + '">';
			delete attributes.name;
			return Element.writeAttribute(document.createElement(tagName), attributes);
		}
		
		if (!ELEMENT_CACHE[tagName])
			ELEMENT_CACHE[tagName] = Element.extend(document.createElement(tagName));
		
		var node = shouldUseCreationCache(tagName, attributes) ?
		           ELEMENT_CACHE[tagName].cloneNode(false) : document.createElement(tagName);
		
		return Element.writeAttribute(node, attributes);
	}
	
	GLOBAL.Element = Element;
	
	Object.extend(GLOBAL.Element, oldElement || {});
	if (oldElement) GLOBAL.Element.prototype = oldElement.prototype;
	
	Element.Methods = { ByTag: {}, Simulated: {} };
	
	var methods = {};
	
	var INSPECT_ATTRIBUTES = { id: 'id', className: 'class' };
	function inspect(element) {
		element = $app(element);
		var result = '<' + element.tagName.toLowerCase();
		
		var attribute, value;
		for (var property in INSPECT_ATTRIBUTES) {
			attribute = INSPECT_ATTRIBUTES[property];
			value = (element[property] || '').toString();
			if (value) result += ' ' + attribute + '=' + value.inspect(true);
		}
		
		return result + '>';
	}
	
	methods.inspect = inspect;
	
	
	function visible(element) {
		return $app(element).getStyle('display') !== 'none';
	}
	
	function toggle(element, bool) {
		element = $app(element);
		if (typeof bool !== 'boolean')
			bool = !Element.visible(element);
		Element[bool ? 'show' : 'hide'](element);
		
		return element;
	}
	
	function hide(element) {
		element = $app(element);
		element.style.display = 'none';
		return element;
	}
	
	function show(element) {
		element = $app(element);
		element.style.display = '';
		return element;
	}
	
	
	Object.extend(methods, {
		visible: visible,
		toggle:  toggle,
		hide:    hide,
		show:    show
	});
	
	
	function remove(element) {
		element = $app(element);
		element.parentNode.removeChild(element);
		return element;
	}
	
	var SELECT_ELEMENT_INNERHTML_BUGGY = (function(){
		var el = document.createElement("select"),
		    isBuggy = true;
		el.innerHTML = "<option value=\"test\">test</option>";
		if (el.options && el.options[0]) {
			isBuggy = el.options[0].nodeName.toUpperCase() !== "OPTION";
		}
		el = null;
		return isBuggy;
	})();
	
	var TABLE_ELEMENT_INNERHTML_BUGGY = (function(){
		try {
			var el = document.createElement("table");
			if (el && el.tBodies) {
				el.innerHTML = "<tbody><tr><td>test</td></tr></tbody>";
				var isBuggy = typeof el.tBodies[0] == "undefined";
				el = null;
				return isBuggy;
			}
		} catch (e) {
			return true;
		}
	})();
	
	var LINK_ELEMENT_INNERHTML_BUGGY = (function() {
		try {
			var el = document.createElement('div');
			el.innerHTML = "<link />";
			var isBuggy = (el.childNodes.length === 0);
			el = null;
			return isBuggy;
		} catch(e) {
			return true;
		}
	})();
	
	var ANY_INNERHTML_BUGGY = SELECT_ELEMENT_INNERHTML_BUGGY ||
	                          TABLE_ELEMENT_INNERHTML_BUGGY || LINK_ELEMENT_INNERHTML_BUGGY;
	
	var SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING = (function () {
		var s = document.createElement("script"),
		    isBuggy = false;
		try {
			s.appendChild(document.createTextNode(""));
			isBuggy = !s.firstChild ||
			          s.firstChild && s.firstChild.nodeType !== 3;
		} catch (e) {
			isBuggy = true;
		}
		s = null;
		return isBuggy;
	})();
	
	function update(element, content) {
		element = $app(element);
		
		var descendants = element.getElementsByTagName('*'),
		    i = descendants.length;
		while (i--) purgeElement(descendants[i]);
		
		if (content && content.toElement)
			content = content.toElement();
		
		if (Object.isElement(content))
			return element.update().insert(content);
		
		
		content = Object.toHTML(content);
		var tagName = element.tagName.toUpperCase();
		
		if (tagName === 'SCRIPT' && SCRIPT_ELEMENT_REJECTS_TEXTNODE_APPENDING) {
			element.text = content;
			return element;
		}
		
		if (ANY_INNERHTML_BUGGY) {
			if (tagName in INSERTION_TRANSLATIONS.tags) {
				while (element.firstChild)
					element.removeChild(element.firstChild);
				
				var nodes = getContentFromAnonymousElement(tagName, content.stripScripts());
				for (var i = 0, node; node = nodes[i]; i++)
					element.appendChild(node);
				
			} else if (LINK_ELEMENT_INNERHTML_BUGGY && Object.isString(content) && content.indexOf('<link') > -1) {
				while (element.firstChild)
					element.removeChild(element.firstChild);
				
				var nodes = getContentFromAnonymousElement(tagName,
				                                           content.stripScripts(), true);
				
				for (var i = 0, node; node = nodes[i]; i++)
					element.appendChild(node);
			} else {
				element.innerHTML = content.stripScripts();
			}
		} else {
			element.innerHTML = content.stripScripts();
		}
		
		content.evalScripts.bind(content).defer();
		return element;
	}
	
	function replace(element, content) {
		element = $app(element);
		
		if (content && content.toElement) {
			content = content.toElement();
		} else if (!Object.isElement(content)) {
			content = Object.toHTML(content);
			var range = element.ownerDocument.createRange();
			range.selectNode(element);
			content.evalScripts.bind(content).defer();
			content = range.createContextualFragment(content.stripScripts());
		}
		
		element.parentNode.replaceChild(content, element);
		return element;
	}
	
	var INSERTION_TRANSLATIONS = {
		before: function(element, node) {
			element.parentNode.insertBefore(node, element);
		},
		top: function(element, node) {
			element.insertBefore(node, element.firstChild);
		},
		bottom: function(element, node) {
			element.appendChild(node);
		},
		after: function(element, node) {
			element.parentNode.insertBefore(node, element.nextSibling);
		},
		
		tags: {
			TABLE:  ['<table>',                '</table>',                   1],
			TBODY:  ['<table><tbody>',         '</tbody></table>',           2],
			TR:     ['<table><tbody><tr>',     '</tr></tbody></table>',      3],
			TD:     ['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
			SELECT: ['<select>',               '</select>',                  1]
		}
	};
	
	var tags = INSERTION_TRANSLATIONS.tags;
	
	Object.extend(tags, {
		THEAD: tags.TBODY,
		TFOOT: tags.TBODY,
		TH:    tags.TD
	});
	
	function replace_IE(element, content) {
		element = $app(element);
		if (content && content.toElement)
			content = content.toElement();
		if (Object.isElement(content)) {
			element.parentNode.replaceChild(content, element);
			return element;
		}
		
		content = Object.toHTML(content);
		var parent = element.parentNode, tagName = parent.tagName.toUpperCase();
		
		if (tagName in INSERTION_TRANSLATIONS.tags) {
			var nextSibling = Element.next(element);
			var fragments = getContentFromAnonymousElement(
				tagName, content.stripScripts());
			
			parent.removeChild(element);
			
			var iterator;
			if (nextSibling)
				iterator = function(node) { parent.insertBefore(node, nextSibling) };
			else
				iterator = function(node) { parent.appendChild(node); }
			
			fragments.each(iterator);
		} else {
			element.outerHTML = content.stripScripts();
		}
		
		content.evalScripts.bind(content).defer();
		return element;
	}
	
	if ('outerHTML' in document.documentElement)
		replace = replace_IE;
	
	function isContent(content) {
		if (Object.isUndefined(content) || content === null) return false;
		
		if (Object.isString(content) || Object.isNumber(content)) return true;
		if (Object.isElement(content)) return true;
		if (content.toElement || content.toHTML) return true;
		
		return false;
	}
	
	function insertContentAt(element, content, position) {
		position   = position.toLowerCase();
		var method = INSERTION_TRANSLATIONS[position];
		
		if (content && content.toElement) content = content.toElement();
		if (Object.isElement(content)) {
			method(element, content);
			return element;
		}
		
		content = Object.toHTML(content);
		var tagName = ((position === 'before' || position === 'after') ?
		               element.parentNode : element).tagName.toUpperCase();
		
		var childNodes = getContentFromAnonymousElement(tagName, content.stripScripts());
		
		if (position === 'top' || position === 'after') childNodes.reverse();
		
		for (var i = 0, node; node = childNodes[i]; i++)
			method(element, node);
		
		content.evalScripts.bind(content).defer();
	}
	
	function insert(element, insertions) {
		element = $app(element);
		
		if (isContent(insertions))
			insertions = { bottom: insertions };
		
		for (var position in insertions)
			insertContentAt(element, insertions[position], position);
		
		return element;
	}
	
	function wrap(element, wrapper, attributes) {
		element = $app(element);
		
		if (Object.isElement(wrapper)) {
			$app(wrapper).writeAttribute(attributes || {});
		} else if (Object.isString(wrapper)) {
			wrapper = new Element(wrapper, attributes);
		} else {
			wrapper = new Element('div', wrapper);
		}
		
		if (element.parentNode)
			element.parentNode.replaceChild(wrapper, element);
		
		wrapper.appendChild(element);
		
		return wrapper;
	}
	
	function cleanWhitespace(element) {
		element = $app(element);
		var node = element.firstChild;
		
		while (node) {
			var nextNode = node.nextSibling;
			if (node.nodeType === Node.TEXT_NODE && !/\S/.test(node.nodeValue))
				element.removeChild(node);
			node = nextNode;
		}
		return element;
	}
	
	function empty(element) {
		return $app(element).innerHTML.blank();
	}
	
	function getContentFromAnonymousElement(tagName, html, force) {
		var t = INSERTION_TRANSLATIONS.tags[tagName], div = DIV;
		
		var workaround = !!t;
		if (!workaround && force) {
			workaround = true;
			t = ['', '', 0];
		}
		
		if (workaround) {
			div.innerHTML = '&#160;' + t[0] + html + t[1];
			div.removeChild(div.firstChild);
			for (var i = t[2]; i--; )
				div = div.firstChild;
		} else {
			div.innerHTML = html;
		}
		
		return $A(div.childNodes);
	}
	
	function clone(element, deep) {
		if (!(element = $app(element))) return;
		var clone = element.cloneNode(deep);
		if (!HAS_UNIQUE_ID_PROPERTY) {
			clone._prototypeUID = UNDEFINED;
			if (deep) {
				var descendants = Element.select(clone, '*'),
				    i = descendants.length;
				while (i--)
					descendants[i]._prototypeUID = UNDEFINED;
			}
		}
		return Element.extend(clone);
	}
	
	function purgeElement(element) {
		var uid = getUniqueElementID(element);
		if (uid) {
			Element.stopObserving(element);
			if (!HAS_UNIQUE_ID_PROPERTY)
				element._prototypeUID = UNDEFINED;
			delete Element.Storage[uid];
		}
	}
	
	function purgeCollection(elements) {
		var i = elements.length;
		while (i--)
			purgeElement(elements[i]);
	}
	
	function purgeCollection_IE(elements) {
		var i = elements.length, element, uid;
		while (i--) {
			element = elements[i];
			uid = getUniqueElementID(element);
			delete Element.Storage[uid];
			delete Event.cache[uid];
		}
	}
	
	if (HAS_UNIQUE_ID_PROPERTY) {
		purgeCollection = purgeCollection_IE;
	}
	
	
	function purge(element) {
		if (!(element = $app(element))) return;
		purgeElement(element);
		
		var descendants = element.getElementsByTagName('*'),
		    i = descendants.length;
		
		while (i--) purgeElement(descendants[i]);
		
		return null;
	}
	
	Object.assign(methods, {
		remove:  remove,
		update:  update,
		replace: replace,
		insert:  insert,
		wrap:    wrap,
		cleanWhitespace: cleanWhitespace,
		empty:   empty,
		clone:   clone,
		purge:   purge
	});
	
	
	
	function recursivelyCollect(element, property, maximumLength) {
		element = $app(element);
		maximumLength = maximumLength || -1;
		var elements = [];
		
		while (element = element[property]) {
			if (element.nodeType === Node.ELEMENT_NODE)
				elements.push(Element.extend(element));
			
			if (elements.length === maximumLength) break;
		}
		
		return elements;
	}
	
	
	function ancestors(element) {
		return recursivelyCollect(element, 'parentNode');
	}
	
	function descendants(element) {
		return Element.select(element, '*');
	}
	
	function firstDescendant(element) {
		element = $app(element).firstChild;
		while (element && element.nodeType !== Node.ELEMENT_NODE)
			element = element.nextSibling;
		
		return $app(element);
	}
	
	function immediateDescendants(element) {
		var results = [], child = $app(element).firstChild;
		
		while (child) {
			if (child.nodeType === Node.ELEMENT_NODE)
				results.push(Element.extend(child));
			
			child = child.nextSibling;
		}
		
		return results;
	}
	
	function previousSiblings(element) {
		return recursivelyCollect(element, 'previousSibling');
	}
	
	function nextSiblings(element) {
		return recursivelyCollect(element, 'nextSibling');
	}
	
	function siblings(element) {
		element = $app(element);
		var previous = previousSiblings(element),
		    next = nextSiblings(element);
		return previous.reverse().concat(next);
	}
	
	function match(element, selector) {
		element = $app(element);
		
		if (Object.isString(selector))
			return Prototype.Selector.match(element, selector);
		
		return selector.match(element);
	}
	
	
	function _recursivelyFind(element, property, expression, index) {
		element = $app(element), expression = expression || 0, index = index || 0;
		if (Object.isNumber(expression)) {
			index = expression, expression = null;
		}
		
		while (element = element[property]) {
			if (element.nodeType !== 1) continue;
			if (expression && !Prototype.Selector.match(element, expression))
				continue;
			if (--index >= 0) continue;
			
			return Element.extend(element);
		}
	}
	
	
	function up(element, expression, index) {
		element = $app(element);
		
		if (arguments.length === 1) return $app(element.parentNode);
		return _recursivelyFind(element, 'parentNode', expression, index);
	}
	
	function down(element, expression, index) {
		if (arguments.length === 1) return firstDescendant(element);
		element = $app(element), expression = expression || 0, index = index || 0;
		
		if (Object.isNumber(expression))
			index = expression, expression = '*';
		
		var node = Prototype.Selector.select(expression, element)[index];
		return Element.extend(node);
	}
	
	function previous(element, expression, index) {
		return _recursivelyFind(element, 'previousSibling', expression, index);
	}
	
	function next(element, expression, index) {
		return _recursivelyFind(element, 'nextSibling', expression, index);
	}
	
	function select(element) {
		element = $app(element);
		var expressions = SLICE.call(arguments, 1).join(', ');
		return Prototype.Selector.select(expressions, element);
	}
	
	function adjacent(element) {
		element = $app(element);
		var expressions = SLICE.call(arguments, 1).join(', ');
		var siblings = Element.siblings(element), results = [];
		for (var i = 0, sibling; sibling = siblings[i]; i++) {
			if (Prototype.Selector.match(sibling, expressions))
				results.push(sibling);
		}
		
		return results;
	}
	
	function descendantOf_DOM(element, ancestor) {
		element = $app(element), ancestor = $app(ancestor);
		if (!element || !ancestor) return false;
		while (element = element.parentNode)
			if (element === ancestor) return true;
		return false;
	}
	
	function descendantOf_contains(element, ancestor) {
		element = $app(element), ancestor = $app(ancestor);
		if (!element || !ancestor) return false;
		if (!ancestor.contains) return descendantOf_DOM(element, ancestor);
		return ancestor.contains(element) && ancestor !== element;
	}
	
	function descendantOf_compareDocumentPosition(element, ancestor) {
		element = $app(element), ancestor = $app(ancestor);
		if (!element || !ancestor) return false;
		return (element.compareDocumentPosition(ancestor) & 8) === 8;
	}
	
	var descendantOf;
	if (DIV.compareDocumentPosition) {
		descendantOf = descendantOf_compareDocumentPosition;
	} else if (DIV.contains) {
		descendantOf = descendantOf_contains;
	} else {
		descendantOf = descendantOf_DOM;
	}
	
	
	Object.assign(methods, {
		recursivelyCollect:   recursivelyCollect,
		ancestors:            ancestors,
		descendants:          descendants,
		firstDescendant:      firstDescendant,
		immediateDescendants: immediateDescendants,
		previousSiblings:     previousSiblings,
		nextSiblings:         nextSiblings,
		siblings:             siblings,
		match:                match,
		up:                   up,
		down:                 down,
		previous:             previous,
		next:                 next,
		select:               select,
		adjacent:             adjacent,
		descendantOf:         descendantOf,
		
		getElementsBySelector: select,
		
		childElements:         immediateDescendants
	});
	
	
	var idCounter = 1;
	function identify(element) {
		element = $app(element);
		var id = Element.readAttribute(element, 'id');
		if (id) return id;
		
		do { id = 'anonymous_element_' + idCounter++ } while ($app(id));
		
		Element.writeAttribute(element, 'id', id);
		return id;
	}
	
	
	function readAttribute(element, name) {
		return $app(element).getAttribute(name);
	}
	
	function readAttribute_IE(element, name) {
		element = $app(element);
		
		var table = ATTRIBUTE_TRANSLATIONS.read;
		if (table.values[name])
			return table.values[name](element, name);
		
		if (table.names[name]) name = table.names[name];
		
		if (name.include(':')) {
			if (!element.attributes || !element.attributes[name]) return null;
			return element.attributes[name].value;
		}
		
		return element.getAttribute(name);
	}
	
	function readAttribute_Opera(element, name) {
		if (name === 'title') return element.title;
		return element.getAttribute(name);
	}
	
	var PROBLEMATIC_ATTRIBUTE_READING = (function() {
		DIV.setAttribute('onclick', []);
		var value = DIV.getAttribute('onclick');
		var isFunction = Object.isArray(value);
		DIV.removeAttribute('onclick');
		return isFunction;
	})();
	
	if (PROBLEMATIC_ATTRIBUTE_READING) {
		readAttribute = readAttribute_IE;
	} else if (Prototype.Browser.Opera) {
		readAttribute = readAttribute_Opera;
	}
	
	
	function writeAttribute(element, name, value) {
		element = $app(element);
		var attributes = {}, table = ATTRIBUTE_TRANSLATIONS.write;
		
		if (typeof name === 'object') {
			attributes = name;
		} else {
			attributes[name] = Object.isUndefined(value) ? true : value;
		}
		
		for (var attr in attributes) {
			name = table.names[attr] || attr;
			value = attributes[attr];
			if (table.values[attr]) {
				value = table.values[attr](element, value);
				if (Object.isUndefined(value)) continue;
			}
			if (value === false || value === null)
				element.removeAttribute(name);
			else if (value === true)
				element.setAttribute(name, name);
			else element.setAttribute(name, value);
		}
		
		return element;
	}
	
	var PROBLEMATIC_HAS_ATTRIBUTE_WITH_CHECKBOXES = (function () {
		if (!HAS_EXTENDED_CREATE_ELEMENT_SYNTAX) {
			return false;
		}
		var checkbox = document.createElement('<input type="checkbox">');
		checkbox.checked = true;
		var node = checkbox.getAttributeNode('checked');
		return !node || !node.specified;
	})();
	
	function hasAttribute(element, attribute) {
		attribute = ATTRIBUTE_TRANSLATIONS.has[attribute] || attribute;
		var node = $app(element).getAttributeNode(attribute);
		return !!(node && node.specified);
	}
	
	function hasAttribute_IE(element, attribute) {
		if (attribute === 'checked') {
			return element.checked;
		}
		return hasAttribute(element, attribute);
	}
	
	GLOBAL.Element.Methods.Simulated.hasAttribute =
		PROBLEMATIC_HAS_ATTRIBUTE_WITH_CHECKBOXES ?
		hasAttribute_IE : hasAttribute;
	
	function classNames(element) {
		return new Element.ClassNames(element);
	}
	
	var regExpCache = {};
	function getRegExpForClassName(className) {
		if (regExpCache[className]) return regExpCache[className];
		
		var re = new RegExp("(^|\\s+)" + className + "(\\s+|$)");
		regExpCache[className] = re;
		return re;
	}
	
	function hasClassName(element, className) {
		if (!(element = $app(element))) return;
		
		var elementClassName = element.className;
		
		if (elementClassName.length === 0) return false;
		if (elementClassName === className) return true;
		
		return getRegExpForClassName(className).test(elementClassName);
	}
	
	function addClassName(element, className) {
		if (!(element = $app(element))) return;
		
		if (!hasClassName(element, className))
			element.className += (element.className ? ' ' : '') + className;
		
		return element;
	}
	
	function removeClassName(element, className) {
		if (!(element = $app(element))) return;
		
		element.className = element.className.replace(
			getRegExpForClassName(className), ' ').strip();
		
		return element;
	}
	
	function toggleClassName(element, className, bool) {
		if (!(element = $app(element))) return;
		
		if (Object.isUndefined(bool))
			bool = !hasClassName(element, className);
		
		var method = Element[bool ? 'addClassName' : 'removeClassName'];
		return method(element, className);
	}
	
	var ATTRIBUTE_TRANSLATIONS = {};
	
	var classProp = 'className', forProp = 'for';
	
	DIV.setAttribute(classProp, 'x');
	if (DIV.className !== 'x') {
		DIV.setAttribute('class', 'x');
		if (DIV.className === 'x')
			classProp = 'class';
	}
	
	var LABEL = document.createElement('label');
	LABEL.setAttribute(forProp, 'x');
	if (LABEL.htmlFor !== 'x') {
		LABEL.setAttribute('htmlFor', 'x');
		if (LABEL.htmlFor === 'x')
			forProp = 'htmlFor';
	}
	LABEL = null;
	
	function _getAttr(element, attribute) {
		return element.getAttribute(attribute);
	}
	
	function _getAttr2(element, attribute) {
		return element.getAttribute(attribute, 2);
	}
	
	function _getAttrNode(element, attribute) {
		var node = element.getAttributeNode(attribute);
		return node ? node.value : '';
	}
	
	function _getFlag(element, attribute) {
		return $app(element).hasAttribute(attribute) ? attribute : null;
	}
	
	DIV.onclick = Prototype.emptyFunction;
	var onclickValue = DIV.getAttribute('onclick');
	
	var _getEv;
	
	if (String(onclickValue).indexOf('{') > -1) {
		_getEv = function(element, attribute) {
			var value = element.getAttribute(attribute);
			if (!value) return null;
			value = value.toString();
			value = value.split('{')[1];
			value = value.split('}')[0];
			return value.strip();
		};
	}
	else if (onclickValue === '') {
		_getEv = function(element, attribute) {
			var value = element.getAttribute(attribute);
			if (!value) return null;
			return value.strip();
		};
	}
	
	ATTRIBUTE_TRANSLATIONS.read = {
		names: {
			'class':     classProp,
			'className': classProp,
			'for':       forProp,
			'htmlFor':   forProp
		},
		
		values: {
			style: function(element) {
				return element.style.cssText.toLowerCase();
			},
			title: function(element) {
				return element.title;
			}
		}
	};
	
	ATTRIBUTE_TRANSLATIONS.write = {
		names: {
			className:   'class',
			htmlFor:     'for',
			cellpadding: 'cellPadding',
			cellspacing: 'cellSpacing'
		},
		
		values: {
			checked: function(element, value) {
				value = !!value;
				element.checked = value;
				return value ? 'checked' : null;
			},
			
			style: function(element, value) {
				element.style.cssText = value ? value : '';
			}
		}
	};
	
	ATTRIBUTE_TRANSLATIONS.has = { names: {} };
	
	Object.extend(ATTRIBUTE_TRANSLATIONS.write.names,
	              ATTRIBUTE_TRANSLATIONS.read.names);
	
	var CAMEL_CASED_ATTRIBUTE_NAMES = $w('colSpan rowSpan vAlign dateTime ' +
	                                     'accessKey tabIndex encType maxLength readOnly longDesc frameBorder');
	
	for (var i = 0, attr; attr = CAMEL_CASED_ATTRIBUTE_NAMES[i]; i++) {
		ATTRIBUTE_TRANSLATIONS.write.names[attr.toLowerCase()] = attr;
		ATTRIBUTE_TRANSLATIONS.has.names[attr.toLowerCase()]   = attr;
	}
	
	Object.extend(ATTRIBUTE_TRANSLATIONS.read.values, {
		href:        _getAttr2,
		src:         _getAttr2,
		type:        _getAttr,
		action:      _getAttrNode,
		disabled:    _getFlag,
		checked:     _getFlag,
		readonly:    _getFlag,
		multiple:    _getFlag,
		onload:      _getEv,
		onunload:    _getEv,
		onclick:     _getEv,
		ondblclick:  _getEv,
		onmousedown: _getEv,
		onmouseup:   _getEv,
		onmouseover: _getEv,
		onmousemove: _getEv,
		onmouseout:  _getEv,
		onfocus:     _getEv,
		onblur:      _getEv,
		onkeypress:  _getEv,
		onkeydown:   _getEv,
		onkeyup:     _getEv,
		onsubmit:    _getEv,
		onreset:     _getEv,
		onselect:    _getEv,
		onchange:    _getEv
	});
	
	
	Object.extend(methods, {
		identify:        identify,
		readAttribute:   readAttribute,
		writeAttribute:  writeAttribute,
		classNames:      classNames,
		hasClassName:    hasClassName,
		addClassName:    addClassName,
		removeClassName: removeClassName,
		toggleClassName: toggleClassName
	});
	
	
	function normalizeStyleName(style) {
		if (style === 'float' || style === 'styleFloat')
			return 'cssFloat';
		return style.camelize();
	}
	
	function normalizeStyleName_IE(style) {
		if (style === 'float' || style === 'cssFloat')
			return 'styleFloat';
		return style.camelize();
	}
	
	function setStyle(element, styles) {
		element = $app(element);
		var elementStyle = element.style, match;
		
		if (Object.isString(styles)) {
			elementStyle.cssText += ';' + styles;
			if (styles.include('opacity')) {
				var opacity = styles.match(/opacity:\s*(\d?\.?\d*)/)[1];
				Element.setOpacity(element, opacity);
			}
			return element;
		}
		
		for (var property in styles) {
			if (property === 'opacity') {
				Element.setOpacity(element, styles[property]);
			} else {
				var value = styles[property];
				if (property === 'float' || property === 'cssFloat') {
					property = Object.isUndefined(elementStyle.styleFloat) ?
					           'cssFloat' : 'styleFloat';
				}
				elementStyle[property] = value;
			}
		}
		
		return element;
	}
	
	
	function getStyle(element, style) {
		element = $app(element);
		style = normalizeStyleName(style);
		
		var value = element.style[style];
		if (!value || value === 'auto') {
			var css = document.defaultView.getComputedStyle(element, null);
			value = css ? css[style] : null;
		}
		
		if (style === 'opacity') return value ? parseFloat(value) : 1.0;
		return value === 'auto' ? null : value;
	}
	
	function getStyle_Opera(element, style) {
		switch (style) {
			case 'height': case 'width':
			if (!Element.visible(element)) return null;
			
			var dim = parseInt(getStyle(element, style), 10);
			
			if (dim !== element['offset' + style.capitalize()])
				return dim + 'px';
			
			return Element.measure(element, style);
			
			default: return getStyle(element, style);
		}
	}
	
	function getStyle_IE(element, style) {
		element = $app(element);
		style = normalizeStyleName_IE(style);
		
		var value = element.style[style];
		if (!value && element.currentStyle) {
			value = element.currentStyle[style];
		}
		
		if (style === 'opacity') {
			if (!STANDARD_CSS_OPACITY_SUPPORTED)
				return getOpacity_IE(element);
			else return value ? parseFloat(value) : 1.0;
		}
		
		if (value === 'auto') {
			if ((style === 'width' || style === 'height') && Element.visible(element))
				return Element.measure(element, style) + 'px';
			return null;
		}
		
		return value;
	}
	
	function stripAlphaFromFilter_IE(filter) {
		return (filter || '').replace(/alpha\([^\)]*\)/gi, '');
	}
	
	function hasLayout_IE(element) {
		if (!element.currentStyle || !element.currentStyle.hasLayout)
			element.style.zoom = 1;
		return element;
	}
	
	var STANDARD_CSS_OPACITY_SUPPORTED = (function() {
		DIV.style.cssText = "opacity:.55";
		return /^0.55/.test(DIV.style.opacity);
	})();
	
	function setOpacity(element, value) {
		element = $app(element);
		if (value == 1 || value === '') value = '';
		else if (value < 0.00001) value = 0;
		element.style.opacity = value;
		return element;
	}
	
	function setOpacity_IE(element, value) {
		if (STANDARD_CSS_OPACITY_SUPPORTED)
			return setOpacity(element, value);
		
		element = hasLayout_IE($app(element));
		var filter = Element.getStyle(element, 'filter'),
		    style = element.style;
		
		if (value == 1 || value === '') {
			filter = stripAlphaFromFilter_IE(filter);
			if (filter) style.filter = filter;
			else style.removeAttribute('filter');
			return element;
		}
		
		if (value < 0.00001) value = 0;
		
		style.filter = stripAlphaFromFilter_IE(filter) +
		               ' alpha(opacity=' + (value * 100) + ')';
		
		return element;
	}
	
	
	function getOpacity(element) {
		return Element.getStyle(element, 'opacity');
	}
	
	function getOpacity_IE(element) {
		if (STANDARD_CSS_OPACITY_SUPPORTED)
			return getOpacity(element);
		
		var filter = Element.getStyle(element, 'filter');
		if (filter.length === 0) return 1.0;
		var match = (filter || '').match(/alpha\(opacity=(.*)\)/i);
		if (match && match[1]) return parseFloat(match[1]) / 100;
		return 1.0;
	}
	
	
	Object.extend(methods, {
		setStyle:   setStyle,
		getStyle:   getStyle,
		setOpacity: setOpacity,
		getOpacity: getOpacity
	});
	
	if ('styleFloat' in DIV.style) {
		methods.getStyle = getStyle_IE;
		methods.setOpacity = setOpacity_IE;
		methods.getOpacity = getOpacity_IE;
	}
	
	var UID = 0;
	
	GLOBAL.Element.Storage = { UID: 1 };
	
	function getUniqueElementID(element) {
		if (element === window) return 0;
		
		if (typeof element._prototypeUID === 'undefined')
			element._prototypeUID = Element.Storage.UID++;
		return element._prototypeUID;
	}
	
	function getUniqueElementID_IE(element) {
		if (element === window) return 0;
		if (element == document) return 1;
		return element.uniqueID;
	}
	
	var HAS_UNIQUE_ID_PROPERTY = ('uniqueID' in DIV);
	if (HAS_UNIQUE_ID_PROPERTY)
		getUniqueElementID = getUniqueElementID_IE;
	
	function getStorage(element) {
		if (!(element = $app(element))) return;
		
		var uid = getUniqueElementID(element);
		
		if (!Element.Storage[uid])
			Element.Storage[uid] = $H();
		
		return Element.Storage[uid];
	}
	
	function store(element, key, value) {
		if (!(element = $app(element))) return;
		var storage = getStorage(element);
		if (arguments.length === 2) {
			storage.update(key);
		} else {
			storage.set(key, value);
		}
		return element;
	}
	
	function retrieve(element, key, defaultValue) {
		if (!(element = $app(element))) return;
		var storage = getStorage(element), value = storage.get(key);
		
		if (Object.isUndefined(value)) {
			storage.set(key, defaultValue);
			value = defaultValue;
		}
		
		return value;
	}
	
	
	Object.extend(methods, {
		getStorage: getStorage,
		store:      store,
		retrieve:   retrieve
	});
	
	
	var Methods = {}, ByTag = Element.Methods.ByTag,
	    F = Prototype.BrowserFeatures;
	
	if (!F.ElementExtensions && ('__proto__' in DIV)) {
		GLOBAL.HTMLElement = {};
		GLOBAL.HTMLElement.prototype = DIV['__proto__'];
		F.ElementExtensions = true;
	}
	
	function checkElementPrototypeDeficiency(tagName) {
		if (typeof window.Element === 'undefined') return false;
		if (!HAS_EXTENDED_CREATE_ELEMENT_SYNTAX) return false;
		var proto = window.Element.prototype;
		if (proto) {
			var id = '_' + (Math.random() + '').slice(2),
			    el = document.createElement(tagName);
			proto[id] = 'x';
			var isBuggy = (el[id] !== 'x');
			delete proto[id];
			el = null;
			return isBuggy;
		}
		
		return false;
	}
	
	var HTMLOBJECTELEMENT_PROTOTYPE_BUGGY =
		    checkElementPrototypeDeficiency('object');
	
	function extendElementWith(element, methods) {
		for (var property in methods) {
			var value = methods[property];
			if (Object.isFunction(value) && !(property in element))
				element[property] = value.methodize();
		}
	}
	
	var EXTENDED = {};
	function elementIsExtended(element) {
		var uid = getUniqueElementID(element);
		return (uid in EXTENDED);
	}
	
	function extend(element) {
		if (!element || elementIsExtended(element)) return element;
		if (element.nodeType !== Node.ELEMENT_NODE || element == window)
			return element;
		
		var methods = Object.clone(Methods),
		    tagName = element.tagName.toUpperCase();
		
		if (ByTag[tagName]) Object.extend(methods, ByTag[tagName]);
		
		extendElementWith(element, methods);
		EXTENDED[getUniqueElementID(element)] = true;
		return element;
	}
	
	function extend_IE8(element) {
		if (!element || elementIsExtended(element)) return element;
		
		var t = element.tagName;
		if (t && (/^(?:object|applet|embed)$/i.test(t))) {
			extendElementWith(element, Element.Methods);
			extendElementWith(element, Element.Methods.Simulated);
			extendElementWith(element, Element.Methods.ByTag[t.toUpperCase()]);
		}
		
		return element;
	}
	
	if (F.SpecificElementExtensions) {
		extend = HTMLOBJECTELEMENT_PROTOTYPE_BUGGY ? extend_IE8 : Prototype.K;
	}
	
	function addMethodsToTagName(tagName, methods) {
		tagName = tagName.toUpperCase();
		if (!ByTag[tagName]) ByTag[tagName] = {};
		Object.extend(ByTag[tagName], methods);
	}
	
	function mergeMethods(destination, methods, onlyIfAbsent) {
		if (Object.isUndefined(onlyIfAbsent)) onlyIfAbsent = false;
		for (var property in methods) {
			var value = methods[property];
			if (!Object.isFunction(value)) continue;
			if (!onlyIfAbsent || !(property in destination))
				destination[property] = value.methodize();
		}
	}
	
	function findDOMClass(tagName) {
		var klass;
		var trans = {
			"OPTGROUP": "OptGroup", "TEXTAREA": "TextArea", "P": "Paragraph",
			"FIELDSET": "FieldSet", "UL": "UList", "OL": "OList", "DL": "DList",
			"DIR": "Directory", "H1": "Heading", "H2": "Heading", "H3": "Heading",
			"H4": "Heading", "H5": "Heading", "H6": "Heading", "Q": "Quote",
			"INS": "Mod", "DEL": "Mod", "A": "Anchor", "IMG": "Image", "CAPTION":
				"TableCaption", "COL": "TableCol", "COLGROUP": "TableCol", "THEAD":
				"TableSection", "TFOOT": "TableSection", "TBODY": "TableSection", "TR":
				"TableRow", "TH": "TableCell", "TD": "TableCell", "FRAMESET":
				"FrameSet", "IFRAME": "IFrame"
		};
		if (trans[tagName]) klass = 'HTML' + trans[tagName] + 'Element';
		if (window[klass]) return window[klass];
		klass = 'HTML' + tagName + 'Element';
		if (window[klass]) return window[klass];
		klass = 'HTML' + tagName.capitalize() + 'Element';
		if (window[klass]) return window[klass];
		
		var element = document.createElement(tagName),
		    proto = element['__proto__'] || element.constructor.prototype;
		
		element = null;
		return proto;
	}
	
	function addMethods(methods) {
		if (arguments.length === 0) addFormMethods();
		
		if (arguments.length === 2) {
			var tagName = methods;
			methods = arguments[1];
		}
		
		if (!tagName) {
			Object.extend(Element.Methods, methods || {});
		} else {
			if (Object.isArray(tagName)) {
				for (var i = 0, tag; tag = tagName[i]; i++)
					addMethodsToTagName(tag, methods);
			} else {
				addMethodsToTagName(tagName, methods);
			}
		}
		
		var ELEMENT_PROTOTYPE = window.HTMLElement ? HTMLElement.prototype :
		                        Element.prototype;
		
		if (F.ElementExtensions) {
			mergeMethods(ELEMENT_PROTOTYPE, Element.Methods);
			mergeMethods(ELEMENT_PROTOTYPE, Element.Methods.Simulated, true);
		}
		
		if (F.SpecificElementExtensions) {
			for (var tag in Element.Methods.ByTag) {
				var klass = findDOMClass(tag);
				if (Object.isUndefined(klass)) continue;
				mergeMethods(klass.prototype, ByTag[tag]);
			}
		}
		
		Object.extend(Element, Element.Methods);
		Object.extend(Element, Element.Methods.Simulated);
		delete Element.ByTag;
		delete Element.Simulated;
		
		Element.extend.refresh();
		
		ELEMENT_CACHE = {};
	}
	
	Object.extend(GLOBAL.Element, {
		extend:     extend,
		addMethods: addMethods
	});
	
	if (extend === Prototype.K) {
		GLOBAL.Element.extend.refresh = Prototype.emptyFunction;
	} else {
		GLOBAL.Element.extend.refresh = function() {
			if (Prototype.BrowserFeatures.ElementExtensions) return;
			Object.extend(Methods, Element.Methods);
			Object.extend(Methods, Element.Methods.Simulated);
			
			EXTENDED = {};
		};
	}
	
	function addFormMethods() {
		Object.extend(Form, Form.Methods);
		Object.extend(Form.Element, Form.Element.Methods);
		Object.extend(Element.Methods.ByTag, {
			"FORM":     Object.clone(Form.Methods),
			"INPUT":    Object.clone(Form.Element.Methods),
			"SELECT":   Object.clone(Form.Element.Methods),
			"TEXTAREA": Object.clone(Form.Element.Methods),
			"BUTTON":   Object.clone(Form.Element.Methods)
		});
	}
	
	Element.addMethods(methods);
	
	function destroyCache_IE() {
		DIV = null;
		ELEMENT_CACHE = null;
	}
	
	if (window.attachEvent)
		window.attachEvent('onunload', destroyCache_IE);
	
})(this);