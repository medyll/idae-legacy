/**
 * shim-class.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Class.create (+ $super), Object.extend/clone/keys/values/is*, Template
 *
 * Depends on: shim-core.js ($A, ObjectRange for $super detection)
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    var IS_DONTENUM_BUGGY = (function () {
        for (var p in { toString: 1 }) return p === 'toString' ? false : true;
    })();

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
            return this.template.gsub(this.pattern, function (match) {
                if (object == null) return match[1] + '';
                var before = match[1] || '';
                if (before === '\\') return match[2];
                var ctx = object, expr = match[3];
                var pattern = /^([^.[]+|\[((?:.*?[^\\])?)\])(\.|\[|$)/;
                match = pattern.exec(expr);
                if (match == null) return before;
                while (match != null) {
                    var comp = match[1].charAt(0) === '[' ? match[2].gsub('\\\\]', ']') : match[1];
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
