/**
 * shim-element.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Element.prototype methods, Element.extend/addMethods, Insertion.*,
 * Position.*, plus fade/appear/morph element shortcuts (Effect.Methods
 * surface; the heavy lifting lives in shim-effects.js).
 *
 * Depends on: shim-core.js, shim-class.js, shim-enumerable.js
 * Optional (loaded later): shim-event.js (observe/fire), shim-effects.js
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    var docEl = document.documentElement;

    if (!Object.isElement) {
        Object.isElement = function (object) {
            return !!(object && object.nodeType === 1);
        };
    }

    function elementOf(element) {
        return typeof element === 'string' ? document.getElementById(element) : element;
    }

    function matches(el, selector) {
        return (el.matches || el.msMatchesSelector || el.webkitMatchesSelector).call(el, selector);
    }

    /* ------------------------------------------------------------------ *
     * Insertion positions                                                 *
     * ------------------------------------------------------------------ */
    var INSERTION_TRANSLATIONS = {
        before: function (element, node) { element.parentNode.insertBefore(node, element); },
        top: function (element, node) { element.insertBefore(node, element.firstChild); },
        bottom: function (element, node) { element.appendChild(node); },
        after: function (element, node) { element.parentNode.insertBefore(node, element.nextSibling); },
        'before-end': 'beforeend'
    };

    function insertContentAt(element, content, position) {
        element = elementOf(element);
        if (!element) return null;

        var insert = INSERTION_TRANSLATIONS[position || 'bottom'];
        var isString = Object.isString(content) || Object.isNumber(content);

        if (isString) {
            var html = String(content);
            // Scripts inside inserted HTML are evaluated deferred, like
            // Prototype's Element.insert/update.
            if (/<script/i.test(html)) {
                content.evalScripts ? content.evalScripts.bind(content).defer() : null;
            }
            element.insertAdjacentHTML(
                position === 'before' ? 'beforebegin' :
                position === 'top' ? 'afterbegin' :
                position === 'after' ? 'afterend' : 'beforeend',
                html.stripScripts()
            );
        } else {
            var node = content.nodeType ? content : (content._element || null);
            if (node) insert(element, node);
        }
        return element;
    }

    /* ------------------------------------------------------------------ *
     * Attribute tables (Prototype quirks)                                 *
     * ------------------------------------------------------------------ */
    var ATTRIBUTE_TRANSLATIONS_READ = {
        'class': 'className',
        'for': 'htmlFor'
    };
    var BOOLEAN_ATTRIBUTES = {
        checked: 1, selected: 1, disabled: 1, readonly: 1, multiple: 1, noresize: 1, compact: 1
    };

    function readAttribute(element, name) {
        element = elementOf(element);
        if (!element) return null;
        if (name in ATTRIBUTE_TRANSLATIONS_READ) {
            var v = element[ATTRIBUTE_TRANSLATIONS_READ[name]];
            return v === '' || v == null ? null : v;
        }
        if (name in BOOLEAN_ATTRIBUTES) {
            return element.hasAttribute(name) ? name : null;
        }
        var value = element.getAttribute(name);
        return value === null ? null : value;
    }

    function writeAttribute(element, name, value) {
        element = elementOf(element);
        if (!element) return element;
        var attributes = {};
        if (Object.isString(name)) {
            attributes[name] = value;
        } else {
            attributes = name;
        }
        for (var attr in attributes) {
            value = attributes[attr];
            if (attr === 'class' || attr === 'className') {
                element.className = value;
            } else if (attr === 'for' || attr === 'htmlFor') {
                element.htmlFor = value;
            } else if (attr === 'style') {
                element.style.cssText = value;
            } else if (attr in BOOLEAN_ATTRIBUTES) {
                if (value) element.setAttribute(attr, attr);
                else element.removeAttribute(attr);
            } else {
                if (value === false || value === null) element.removeAttribute(attr);
                else element.setAttribute(attr, value);
            }
        }
        return element;
    }

    /* ------------------------------------------------------------------ *
     * Styles                                                              *
     * ------------------------------------------------------------------ */
    var STYLE_TRANSLATIONS = { 'float': 'cssFloat', 'styleFloat': 'cssFloat' };
    var CSS_NUMBER_PROPERTIES = {
        zIndex: true, fontWeight: true, opacity: true, zoom: true,
        lineHeight: true, counterIncrement: true, counterReset: true,
        orphans: true, widows: true, columnCount: true, fillOpacity: true,
        stopOpacity: true, strokeDashoffset: true, strokeOpacity: true, strokeWidth: true
    };

    function setStyle(element, styles) {
        element = elementOf(element);
        var elementStyle = element.style, match;
        if (Object.isString(styles)) {
            element.style.cssText += ';' + styles;
            return styles.include('opacity') ?
                element.setOpacity(styles.match(/opacity:\s*(\d?\.?\d*)/)[1]) : element;
        }
        for (var property in styles) {
            if (property === 'opacity') {
                element.setOpacity(styles[property]);
                continue;
            }
            var value = styles[property];
            // Prototype appends 'px' to bare numbers for dimensional
            // properties (app code calls setStyle({top: 800}) and expects px).
            if (typeof value === 'number' && !CSS_NUMBER_PROPERTIES[property]) {
                value = value + 'px';
            }
            var prop = (property in STYLE_TRANSLATIONS) ? STYLE_TRANSLATIONS[property] : property.camelize();
            elementStyle[prop] = value;
        }
        return element;
    }

    function getStyle(element, style) {
        element = elementOf(element);
        style = style === 'float' ? 'cssFloat' : style.camelize();
        var value = element.style[style];
        if (!value || value === 'auto') {
            var css = global.getComputedStyle(element, null);
            value = css ? css[style] : null;
        }
        if (style === 'opacity') return value ? parseFloat(value) : 1.0;
        return value === 'auto' ? null : value;
    }

    function setOpacity(element, value) {
        element = elementOf(element);
        element.style.opacity = (value === 1 || value === '') ? '' : (value < 0.00001) ? 0 : value;
        return element;
    }

    /* ------------------------------------------------------------------ *
     * Traversal                                                           *
     * ------------------------------------------------------------------ */
    function recursivelyCollect(element, property, maximumLength) {
        element = elementOf(element);
        maximumLength = maximumLength || -1;
        var elements = [];
        while ((element = element[property])) {
            if (element.nodeType === 1) elements.push(element);
            if (elements.length === maximumLength) break;
        }
        return elements;
    }

    function matchSelector(el, selector) {
        return selector ? matches(el, selector) : true;
    }

    /* ------------------------------------------------------------------ *
     * The methods                                                         *
     * ------------------------------------------------------------------ */
    var Methods = {
        /* ---- attributes ---- */
        readAttribute: function (name) { return readAttribute(this, name); },
        writeAttribute: function (name, value) { return writeAttribute(this, name, value); },
        hasAttribute: function (name) { return this.hasAttribute(name); },
        identify: (function () {
            var counter = 0;
            return function () {
                if (this.id) return this.id;
                this.id = 'anonymous_element_' + (++counter);
                return this.id;
            };
        })(),

        /* ---- classes ---- */
        hasClassName: function (className) {
            return this.classList.contains(className);
        },
        addClassName: function (className) {
            // Prototype accepted multi-class strings ('animated bounce').
            var el = this;
            $w(className).each(function (token) {
                if (!el.classList.contains(token)) el.classList.add(token);
            });
            return this;
        },
        removeClassName: function (className) {
            var el = this;
            $w(className).each(function (token) { el.classList.remove(token); });
            return this;
        },
        toggleClassName: function (className) {
            this.classList.toggle(className);
            return this;
        },
        classNames: function () {
            return new Element.ClassNames(this);
        },

        /* ---- styles ---- */
        setStyle: function (styles) { return setStyle(this, styles); },
        getStyle: function (style) { return getStyle(this, style); },
        setOpacity: function (value) { return setOpacity(this, value); },
        getOpacity: function () { return getStyle(this, 'opacity'); },

        /* ---- visibility ---- */
        visible: function () { return this.style.display !== 'none'; },
        toggle: function () { return this[this.visible() ? 'hide' : 'show'](); },
        hide: function () {
            this.style.display = 'none';
            return this;
        },
        show: function () {
            this.style.display = '';
            return this;
        },
        remove: function () {
            if (this.parentNode) this.parentNode.removeChild(this);
            return this;
        },

        /* ---- content ---- */
        update: function (content) {
            if (content === undefined) content = '';
            if (content && content.toElement) content = content.toElement();
            if (Object.isElement(content)) {
                this.innerHTML = '';
                this.appendChild(content);
                return this;
            }
            content = String.interpret(content);
            this.innerHTML = content.stripScripts();
            if (/<script/i.test(content)) {
                content.evalScripts.bind(content).defer();
            }
            return this;
        },
        replace: function (content) {
            if (content && content.toElement) content = content.toElement();
            if (Object.isElement(content)) {
                this.parentNode.replaceChild(content, this);
                return content;
            }
            content = String.interpret(content);
            if (/<script/i.test(content)) {
                content.evalScripts.bind(content).defer();
            }
            this.insertAdjacentHTML('beforebegin', content.stripScripts());
            this.remove();
            return this;
        },
        insert: function (content) {
            if (Object.isString(content) || Object.isNumber(content) || Object.isElement(content) ||
                (content && (content.toElement || content.toHTML))) {
                insertContentAt(this, content.toElement ? content.toElement() : content, 'bottom');
            } else {
                // Hash form: { top: ..., bottom: ..., before: ..., after: ... }
                for (var position in content) {
                    insertContentAt(this, content[position], position);
                }
            }
            return this;
        },
        wrap: function (wrapper, attributes) {
            wrapper = Object.isElement(wrapper) ? wrapper : document.createElement(wrapper);
            if (attributes) writeAttribute(wrapper, attributes);
            if (this.parentNode) this.parentNode.replaceChild(wrapper, this);
            wrapper.appendChild(this);
            return wrapper;
        },
        purge: function () {
            // Prototype's purge detaches every observer on the element and its
            // descendants (so listeners removed mid-dispatch never fire, e.g.
            // app_window kill() during a click). Event lives in shim-event.js,
            // which loads after this file — resolve it lazily at call time.
            var E = global.Event;
            if (E && E.stopObserving) {
                E.stopObserving(this);
                var descendants = this.getElementsByTagName('*');
                for (var i = 0; i < descendants.length; i++) {
                    E.stopObserving(descendants[i]);
                }
            }
            while (this.firstChild) this.removeChild(this.firstChild);
            return this;
        },
        empty: function () {
            return this.innerHTML.blank();
        },
        cleanWhitespace: function () {
            var node = this.firstChild;
            while (node) {
                var nextNode = node.nextSibling;
                if (node.nodeType === 3 && !/\S/.test(node.nodeValue)) this.removeChild(node);
                node = nextNode;
            }
            return this;
        },

        /* ---- traversal ---- */
        up: function (expression, index) {
            if (arguments.length === 1 && typeof expression === 'number') {
                index = expression; expression = undefined;
            }
            index = index || 0;
            var ancestors = recursivelyCollect(this, 'parentNode');
            var matched = expression ? ancestors.filter(function (el) { return matchSelector(el, expression); }) : ancestors;
            return matched[index] || null;
        },
        down: function (expression, index) {
            if (arguments.length === 1 && typeof expression === 'number') {
                index = expression; expression = undefined;
            }
            if (arguments.length === 0) {
                var first = this.firstElementChild;
                return first || null;
            }
            index = index || 0;
            var matchesAll = $A(global.__idaeQSA(this, expression || '*'));
            return matchesAll[index] || null;
        },
        previous: function (expression, index) {
            if (arguments.length === 1 && typeof expression === 'number') {
                index = expression; expression = undefined;
            }
            index = index || 0;
            var siblings = recursivelyCollect(this, 'previousElementSibling');
            var matched = expression ? siblings.filter(function (el) { return matchSelector(el, expression); }) : siblings;
            return matched[index] || null;
        },
        next: function (expression, index) {
            if (arguments.length === 1 && typeof expression === 'number') {
                index = expression; expression = undefined;
            }
            index = index || 0;
            var siblings = recursivelyCollect(this, 'nextElementSibling');
            var matched = expression ? siblings.filter(function (el) { return matchSelector(el, expression); }) : siblings;
            return matched[index] || null;
        },
        select: function () {
            var selectors = $A(arguments).join(', ');
            return $A(global.__idaeQSA(this, selectors));
        },
        adjacent: function () {
            var selectors = $A(arguments).join(', ');
            return $A(global.__idaeQSA(this.parentNode, selectors)).filter(function (el) {
                return el !== this;
            }, this);
        },
        descendants: function () {
            return $A(global.__idaeQSA(this, '*'));
        },
        firstDescendant: function () {
            return this.firstElementChild;
        },
        childElements: function () {
            return $A(this.children);
        },
        immediateDescendants: function () {
            return $A(this.children);
        },
        previousSiblings: function () { return recursivelyCollect(this, 'previousElementSibling'); },
        nextSiblings: function () { return recursivelyCollect(this, 'nextElementSibling'); },
        siblings: function () {
            var self = this;
            return $A(this.parentNode ? this.parentNode.children : []).filter(function (el) { return el !== self; });
        },
        match: function (selector) { return matches(this, selector); },
        descendantOf: function (ancestor) {
            ancestor = elementOf(ancestor);
            var el = this;
            while ((el = el.parentNode)) {
                if (el === ancestor) return true;
            }
            return false;
        },
        getElementsBySelector: function () {
            return this.select.apply(this, arguments);
        },
        getElementsByClassName: function (className) {
            // Native method is shadowed by this one; route through qSA.
            return $A(global.__idaeQSA(this, '.' + className));
        },

        /* ---- events (delegate to shim-event) ---- */
        observe: function (eventName, handler) {
            return Event.observe(this, eventName, handler);
        },
        stopObserving: function (eventName, handler) {
            return Event.stopObserving(this, eventName, handler);
        },
        fire: function (eventName, memo, bubble) {
            return Event.fire(this, eventName, memo, bubble);
        },

        /* ---- geometry ---- */
        getDimensions: function () {
            var display = this.style.display;
            if (display !== 'none' && display !== null) {
                return { width: this.offsetWidth, height: this.offsetHeight };
            }
            // Hidden element: measure with visibility trick (Prototype semantics)
            var els = this.style;
            var originalVisibility = els.visibility;
            var originalPosition = els.position;
            var originalDisplay = els.display;
            els.visibility = 'hidden';
            if (originalPosition !== 'fixed') els.position = 'absolute';
            els.display = 'block';
            var originalWidth = this.clientWidth, originalHeight = this.clientHeight;
            els.display = originalDisplay;
            els.position = originalPosition;
            els.visibility = originalVisibility;
            return { width: originalWidth, height: originalHeight };
        },
        getHeight: function () { return this.getDimensions().height; },
        getWidth: function () { return this.getDimensions().width; },
        cumulativeOffset: function () {
            return Position.cumulativeOffset(this);
        },
        positionedOffset: function () {
            return Position.positionedOffset(this);
        },
        absolutize: function () {
            return Position.absolutize(this);
        },
        relativize: function () {
            return Position.relativize(this);
        },
        cumulativeScrollOffset: function () {
            var valueT = 0, valueL = 0, element = this;
            do {
                valueT += element.scrollTop || 0;
                valueL += element.scrollLeft || 0;
                element = element.parentNode;
            } while (element);
            return Element._returnOffset(valueL, valueT);
        },
        viewportOffset: function (forDimensions) {
            var valueT = 0, valueL = 0, element = this;
            var docBody = document.body;
            do {
                valueT += element.offsetTop || 0;
                valueL += element.offsetLeft || 0;
                if (element.offsetParent === docBody && element.style.position === 'absolute') break;
            } while ((element = element.offsetParent));
            element = this;
            do {
                if (element !== docBody) {
                    valueT -= element.scrollTop || 0;
                    valueL -= element.scrollLeft || 0;
                }
            } while ((element = element.parentNode));
            return Element._returnOffset(valueL, valueT);
        },
        makePositioned: function () {
            if (getStyle(this, 'position') === 'static') {
                this.style.position = 'relative';
                if (global.opera) {
                    this.style.top = 0;
                    this.style.left = 0;
                }
            }
            return this;
        },
        undoPositioned: function () {
            if (getStyle(this, 'position') === 'relative') {
                this.style.position = '';
                this.style.top = this.style.left = '';
            }
            return this;
        },
        makeClipping: function () {
            if (this._overflow) return this;
            this._overflow = getStyle(this, 'overflow') || 'auto';
            if (this._overflow !== 'hidden') this.style.overflow = 'hidden';
            return this;
        },
        undoClipping: function () {
            if (!this._overflow) return this;
            this.style.overflow = this._overflow === 'auto' ? '' : this._overflow;
            this._overflow = null;
            return this;
        },
        clonePosition: function (source, options) {
            return Position.clone(elementOf(source), this, options || {});
        },
        scrollTo: function () {
            var pos = Position.cumulativeOffset(this);
            global.scrollTo(pos[0], pos[1]);
            return this;
        },

        /* ---- debug ---- */
        inspect: function () {
            var id = this.id ? ' id="' + this.id + '"' : '';
            var cls = this.className ? ' class="' + this.className + '"' : '';
            return '<' + this.tagName.toLowerCase() + id + cls + '>';
        },

        /* ---- Scriptaculous shortcuts (Effect.Methods) ---- */
        visualEffect: function (name, options) {
            name = name.camelize().capitalize();
            if (Effect[name]) return new Effect[name](this, options || {});
        },
        fade: function (options) { return new Effect.Fade(this, options || {}); },
        appear: function (options) { return new Effect.Appear(this, options || {}); },
        morph: function (style, options) { return new Effect.Morph(this, Object.extend({ style: style }, options || {})); },
        highlight: function (options) {
            if (Effect.Highlight) return new Effect.Highlight(this, options || {});
        }
    };

    /* ------------------------------------------------------------------ *
     * Element statics                                                     *
     * ------------------------------------------------------------------ */
    // The native constructor stays reachable for prototype patching; the
    // global Element becomes Prototype's factory function — the app calls
    // `new Element('div', { className: ... })` (app_menu, app_contextual).
    var NativeElement = global.Element;
    global.__idaeNativeElementProto = NativeElement.prototype;

    var Element = function (tagName, attributes) {
        var element = document.createElement(tagName);
        if (attributes) writeAttribute(element, attributes);
        return element;
    };

    Element._returnOffset = function (l, t) {
        var result = [l, t];
        result.left = l;
        result.top = t;
        return result;
    };

    // Methods live on the native Element.prototype: "extending" an element
    // is a no-op. Prototype's Element.extend returned the same element;
    // code assigning its return value keeps working.
    Element.extend = function (element) {
        return elementOf(element);
    };
    Element.extend.cache = { afterMethodized: null };

    // The shim's own Methods are written this-style and assigned directly.
    // Element.addMethods — the API app code uses (engine/methods.js and
    // friends) — follows Prototype semantics: added methods receive the
    // element as their FIRST argument (methodized).
    Element.__shimMethods = Object.keys(Methods);
    Object.extend(NativeElement.prototype, Methods);

    // Prototype also exposes every method as an Element static in
    // element-first form: Element.cleanWhitespace(node), Element.hide(node).
    Object.keys(Methods).forEach(function (name) {
        if (name in Element) return;
        var method = Methods[name];
        Element[name] = function (element) {
            var args = Array.prototype.slice.call(arguments, 1);
            var el = elementOf(element);
            return method.apply(el, args);
        };
    });

    Element.addMethods = function (methods) {
        Element.__shimMethods = Element.__shimMethods.concat(Object.keys(methods));
        for (var name in methods) {
            if (!Object.prototype.hasOwnProperty.call(methods, name)) continue;
            NativeElement.prototype[name] = methodizeForElement(methods[name]);
        }
    };

    function methodizeForElement(method) {
        return function () {
            var args = [this];
            for (var i = 0, length = arguments.length; i < length; i++) args.push(arguments[i]);
            return method.apply(null, args);
        };
    }

    Element.ClassNames = function (element) {
        this.element = elementOf(element);
    };
    Element.ClassNames.prototype = {
        _each: function (iterator, context) {
            $w(this.element.className).each(iterator, context);
        },
        set: function (className) {
            this.element.className = className;
        },
        add: function (classNameToAdd) {
            this.include(classNameToAdd) || this.set($A(this).concat(classNameToAdd).join(' '));
        },
        remove: function (classNameToRemove) {
            this.include(classNameToRemove) && this.set($A(this).without(classNameToRemove).join(' '));
        },
        include: function (className) {
            return $w(this.element.className).include(className);
        },
        toString: function () {
            return $A(this).join(' ');
        }
    };
    Object.extend(Element.ClassNames.prototype, global.Enumerable || {});

    /* ------------------------------------------------------------------ *
     * Insertion.*                                                         *
     * ------------------------------------------------------------------ */
    var Insertion = {};
    ['Before', 'Top', 'Bottom', 'After'].forEach(function (kind) {
        Insertion[kind] = function (element, content) {
            insertContentAt(element, content, kind.toLowerCase());
        };
    });

    /* ------------------------------------------------------------------ *
     * Position.*                                                          *
     * ------------------------------------------------------------------ */
    var Position = {
        _absolutizePrep: function (element) {
            var el = elementOf(element);
            if (el._madePositioned) return true;
            return false;
        },
        absolutize: function (element) {
            element = elementOf(element);
            if (element._madePositioned) return element;
            element._madePositioned = true;
            var originalStyles = {
                position: element.style.position,
                top: element.style.top,
                bottom: element.style.bottom,
                left: element.style.left,
                width: element.style.width,
                height: element.style.height
            };
            element._originalStyles = originalStyles;
            var pos = Position.cumulativeOffset(element);
            element.style.position = 'absolute';
            element.style.top = pos[1] + 'px';
            element.style.left = pos[0] + 'px';
            element.style.width = element.offsetWidth + 'px';
            element.style.height = element.offsetHeight + 'px';
            return element;
        },
        relativize: function (element) {
            element = elementOf(element);
            if (!element._madePositioned) return element;
            element._madePositioned = false;
            var originalStyles = element._originalStyles || {};
            element.style.position = originalStyles.position || '';
            element.style.top = originalStyles.top || '';
            element.style.bottom = originalStyles.bottom || '';
            element.style.left = originalStyles.left || '';
            element.style.width = originalStyles.width || '';
            element.style.height = originalStyles.height || '';
            return element;
        },
        cumulativeOffset: function (element) {
            var valueT = 0, valueL = 0;
            element = elementOf(element);
            if (element.parentNode) {
                do {
                    valueT += element.offsetTop || 0;
                    valueL += element.offsetLeft || 0;
                    element = element.offsetParent;
                } while (element);
            }
            return Element._returnOffset(valueL, valueT);
        },
        positionedOffset: function (element) {
            element = elementOf(element);
            var valueT = 0, valueL = 0;
            do {
                valueT += element.offsetTop || 0;
                valueL += element.offsetLeft || 0;
                element = element.offsetParent;
                if (element) {
                    if (element.tagName === 'BODY') break;
                    var p = getStyle(element, 'position');
                    if (p !== 'static') break;
                }
            } while (element);
            return Element._returnOffset(valueL, valueT);
        },
        realOffset: function (element) {
            var cumulative = Position.cumulativeOffset(elementOf(element));
            var scroll = { top: global.pageYOffset || docEl.scrollTop, left: global.pageXOffset || docEl.scrollLeft };
            return Element._returnOffset(cumulative[0] + scroll.left, cumulative[1] + scroll.top);
        },
        within: function (element, x, y) {
            element = elementOf(element);
            var offset = Position.cumulativeOffset(element);
            return (y >= offset[1] &&
                y < offset[1] + element.offsetHeight &&
                x >= offset[0] &&
                x < offset[0] + element.offsetWidth);
        },
        withinIncludingScrolloffsets: function (element, x, y) {
            element = elementOf(element);
            var offsetcache = Position.cumulativeScrollOffset(element);
            var offset = Position.cumulativeOffset(element);
            return (y >= offset[1] + offsetcache[1] &&
                y < offset[1] + offsetcache[1] + element.offsetHeight &&
                x >= offset[0] + offsetcache[0] &&
                x < offset[0] + offsetcache[0] + element.offsetWidth);
        },
        overlap: function (mode, element) {
            if (!mode) return 0;
            element = elementOf(element);
            var offset = Position.cumulativeOffset(element);
            if (mode === 'vertical') {
                return (offset[1] + element.offsetHeight) / element.offsetHeight;
            }
            if (mode === 'horizontal') {
                return (offset[0] + element.offsetWidth) / element.offsetWidth;
            }
        },
        prepare: function () {
            var docBody = document.body;
            this.deltaX = global.pageXOffset || docEl.scrollLeft || docBody.scrollLeft || 0;
            this.deltaY = global.pageYOffset || docEl.scrollTop || docBody.scrollTop || 0;
        },
        clone: function (source, target, options) {
            source = elementOf(source);
            target = elementOf(target);
            options = Object.extend({
                setLeft: true,
                setTop: true,
                setWidth: true,
                setHeight: true,
                offsetTop: 0,
                offsetLeft: 0
            }, options);
            var p = Position.cumulativeOffset(source);

            var delta = [0, 0];
            if (getStyle(target, 'position') === 'absolute') {
                var parent = Position.cumulativeOffset(target.offsetParent || docEl);
                delta[0] = parent[0];
                delta[1] = parent[1];
            }

            if (options.setLeft) target.style.left = (p[0] - delta[0] + options.offsetLeft) + 'px';
            if (options.setTop) target.style.top = (p[1] - delta[1] + options.offsetTop) + 'px';
            if (options.setWidth) target.style.width = source.offsetWidth + 'px';
            if (options.setHeight) target.style.height = source.offsetHeight + 'px';
            return target;
        },
        page: function (forElement) {
            forElement = elementOf(forElement);
            var valueT = 0, valueL = 0;
            var element = forElement;
            do {
                valueT += element.offsetTop || 0;
                valueL += element.offsetLeft || 0;
                if (element.offsetParent === document.body && element.style.position === 'absolute') break;
            } while ((element = element.offsetParent));
            element = forElement;
            do {
                if (!global.opera || element.tagName === 'BODY') {
                    valueT -= element.scrollTop || 0;
                    valueL -= element.scrollLeft || 0;
                }
            } while ((element = element.parentNode));
            return Element._returnOffset(valueL, valueT);
        }
    };

    /* ------------------------------------------------------------------ *
     * document.viewport — Prototype dom.js helpers                        *
     * ------------------------------------------------------------------ */
    document.viewport = {
        getDimensions: function () {
            return { width: global.innerWidth, height: global.innerHeight };
        },
        getWidth: function () { return global.innerWidth; },
        getHeight: function () { return global.innerHeight; },
        getScrollOffsets: function () {
            return Element._returnOffset(
                global.pageXOffset || docEl.scrollLeft || 0,
                global.pageYOffset || docEl.scrollTop || 0
            );
        }
    };

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    global.Element = Element;
    global.Insertion = Insertion;
    global.Position = Position;

    // Hash often depends on Enumerable on Array — re-assert availability.
    if (!global.Enumerable) {
        // Enumerable is assigned inside shim-enumerable as a local; expose it
        // via Array.prototype for ClassNames' benefit.
        global.Enumerable = {};
    }

    if (global.console && global.console.info) {
        console.info('[idae-shim] element loaded');
    }
})(window);
