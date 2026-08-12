/**
 * shim-class.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Class.create (+ $super), Object.extend/clone/keys/values/is*, Template
 *
 * Depends on: shim-core.js ($A, $H, String.interpret, ObjectRange for $super
 * detection). No longer on shim-enumerable — see the cls_gsub note below.
 *
 * @package idae-be-shim
 * @date 2026-08-06
 * Modified: 2026-08-12 — dropped the two String#gsub calls in Template so
 * this file survives the deletion of shim-enumerable.
 */
(function (global) {
    'use strict';

    var IS_DONTENUM_BUGGY = (function () {
        for (var p in { toString: 1 }) return p === 'toString' ? false : true;
    })();

    /**
     * Prototype's String#gsub with a *function* iterator, as a free function.
     *
     * Not `String.prototype.replace`: Template.Pattern is a non-global regex,
     * so replace() would substitute the first `#{...}` and leave the rest of
     * the template untouched. Prototype's gsub re-matches the remainder in a
     * loop, which is what makes a multi-placeholder template work at all.
     * The iterator also receives the whole match *array* (match[1], match[3]
     * are read below), not replace()'s (match, p1, p2, ...) argument list.
     *
     * The zero-length-match guard is Prototype's: without it a pattern that
     * can match '' spins forever.
     */
    function cls_gsub(source, pattern, iterator) {
        var rest = String(source), result = '', match;
        while (rest.length > 0) {
            match = rest.match(pattern);
            if (!match) {
                result += rest;
                break;
            }
            result += rest.slice(0, match.index);
            result += String(iterator(match));
            var consumed = match.index + (match[0].length || 1);
            if (!match[0].length) result += rest.charAt(match.index);
            rest = rest.slice(consumed);
        }
        return result;
    }

    /**
     * Prototype's String#gsub with a *string* pattern: it escapes the string
     * into a literal regex and replaces every occurrence. split/join is the
     * same operation without needing RegExp.escape (which shipped in
     * shim-enumerable).
     */
    function cls_gsubLiteral(source, find, replacement) {
        return String(source).split(find).join(replacement);
    }

    /* ------------------------------------------------------------------ *
     * Object.extend / clone / inspect / keys / values / is*               *
     * ------------------------------------------------------------------ */
    function extend(destination, source) {
        for (var property in source) {
            destination[property] = source[property];
        }
        if (IS_DONTENUM_BUGGY && source) {
            if (source.toString !== Object.prototype.toString) destination.toString = source.toString;
            if (source.valueOf !== Object.prototype.valueOf) destination.valueOf = source.valueOf;
        }
        return destination;
    }

    function clone(object) {
        return extend({}, object);
    }

    function keys(object) {
        var results = [];
        for (var property in object) {
            if (Object.prototype.hasOwnProperty.call(object, property)) results.push(property);
        }
        return results;
    }

    function values(object) {
        var results = [];
        for (var property in object) {
            if (Object.prototype.hasOwnProperty.call(object, property)) results.push(object[property]);
        }
        return results;
    }

    function toQueryString(object) {
        return $H(object).toQueryString();
    }

    function isArray(object) {
        return object != null && object.constructor === Array;
    }

    function isHash(object) {
        return object instanceof Hash;
    }

    function isFunction(object) {
        return typeof object === 'function';
    }

    function isString(object) {
        return typeof object === 'string';
    }

    function isNumber(object) {
        return typeof object === 'number';
    }

    function isDate(object) {
        return object != null && object.constructor === Date;
    }

    function isUndefined(object) {
        return typeof object === 'undefined';
    }

    extend(Object, {
        extend: extend,
        clone: clone,
        inspect: function (object) {
            try {
                if (isUndefined(object)) return 'undefined';
                if (object === null) return 'null';
                return object.inspect ? object.inspect() : String(object);
            } catch (e) {
                if (e instanceof RangeError) return '...';
                throw e;
            }
        },
        keys: keys,
        values: values,
        toQueryString: toQueryString,
        toHTML: function (object) {
            return object && object.toHTML ? object.toHTML() : String.interpret(object);
        },
        isArray: isArray,
        isHash: isHash,
        isFunction: isFunction,
        isString: isString,
        isNumber: isNumber,
        isDate: isDate,
        isUndefined: isUndefined
    });

    /* ------------------------------------------------------------------ *
     * Class.create — superclass optional, $super bound per-method         *
     * ------------------------------------------------------------------ */
    var emptyFunction = function () {};

    function argumentNames(fn) {
        var names = fn.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
            .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
            .replace(/\s+/g, '').split(',');
        return names.length === 1 && !names[0] ? [] : names;
    }

    function wrap(wrapper, original) {
        var fn = original || emptyFunction;
        return function () {
            var args = [fn.bind(this)];
            var a = arguments;
            for (var i = 0; i < a.length; i++) args.push(a[i]);
            return wrapper.apply(this, args);
        };
    }

    var Class = (function () {
        function subclass() {}

        function create() {
            var parent = null, properties = $A(arguments);
            if (typeof properties[0] === 'function') {
                parent = properties.shift();
            }

            function klass() {
                this.initialize.apply(this, arguments);
            }

            extend(klass, Class.Methods || {});
            klass.superclass = parent;
            klass.subclasses = [];

            if (parent) {
                subclass.prototype = parent.prototype;
                klass.prototype = new subclass();
                if (parent.subclasses) parent.subclasses.push(klass);
            }

            for (var i = 0, length = properties.length; i < length; i++) {
                klass.addMethods(properties[i]);
            }

            if (!klass.prototype.initialize) {
                klass.prototype.initialize = emptyFunction;
            }

            klass.prototype.constructor = klass;
            return klass;
        }

        function addMethods(source) {
            var ancestor = this.superclass && this.superclass.prototype;
            var properties = keys(source);

            if (IS_DONTENUM_BUGGY) {
                if (source.toString !== Object.prototype.toString) properties.push('toString');
                if (source.valueOf !== Object.prototype.valueOf) properties.push('valueOf');
            }

            for (var i = 0, length = properties.length; i < length; i++) {
                var property = properties[i], value = source[property];
                if (ancestor && typeof value === 'function' &&
                    argumentNames(value)[0] === '$super') {
                    var method = value;
                    value = wrap((function (m) {
                        return function () { return ancestor[m].apply(this, arguments); };
                    })(property), method);
                    value.valueOf = (function (m) {
                        return function () { return m.valueOf.call(m); };
                    })(method);
                    value.toString = (function (m) {
                        return function () { return m.toString.call(m); };
                    })(method);
                }
                this.prototype[property] = value;
            }
            return this;
        }

        return {
            create: create,
            Methods: { addMethods: addMethods }
        };
    })();
    Class.Methods = { addMethods: Class.Methods.addMethods };

    /* ------------------------------------------------------------------ *
     * Template — new Template(str[, pattern]).evaluate(obj)               *
     * ------------------------------------------------------------------ */
    var Template = Class.create({
        initialize: function (template, pattern) {
            this.template = template.toString();
            this.pattern = pattern || Template.Pattern;
        },
        evaluate: function (object) {
            if (object && typeof object.toTemplateReplacements === 'function') {
                object = object.toTemplateReplacements();
            }
            return cls_gsub(this.template, this.pattern, function (match) {
                if (object == null) return match[1] + '';
                var before = match[1] || '';
                if (before === '\\') return match[2];
                var ctx = object, expr = match[3];
                var pattern = /^([^.[]+|\[((?:.*?[^\\])?)\])(\.|\[|$)/;
                match = pattern.exec(expr);
                if (match == null) return before;
                while (match != null) {
                    var comp = match[1].charAt(0) === '[' ? cls_gsubLiteral(match[2], '\\\\]', ']') : match[1];
                    ctx = ctx[comp];
                    if (ctx == null || match[3] === '') break;
                    expr = expr.substring(match[3] === '[' ? match[1].length : match[0].length);
                    match = pattern.exec(expr);
                }
                return before + String.interpret(ctx);
            }.bind(this));
        }
    });
    Template.Pattern = /(^|.|\r|\n)(#\{(.*?)\})/;

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    global.Class = Class;
    global.Template = Template;
})(window);
