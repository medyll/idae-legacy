/**
 * shim-enumerable.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Array / String / Number / Function prototype extensions
 *
 * Assigned plainly (enumerable), exactly like Prototype 1.7.3 did — any
 * code pattern that would break on enumerable prototype members would
 * already be broken today under Prototype.
 *
 * Depends on: shim-core.js ($A, $R, Hash), shim-class.js (Object.extend)
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    var $break = { __proto__: null };

    /* ------------------------------------------------------------------ *
     * Function.prototype                                                  *
     * ------------------------------------------------------------------ */
    function argumentNames(fn) {
        var names = fn.toString().match(/^[\s\(]*function[^(]*\(([^)]*)\)/)[1]
            .replace(/\/\/.*?[\r\n]|\/\*(?:.|[\r\n])*?\*\//g, '')
            .replace(/\s+/g, '').split(',');
        return names.length === 1 && !names[0] ? [] : names;
    }

    Object.extend(Function.prototype, (function () {
        var slice = Array.prototype.slice;

        function update(array, args) {
            var arrayLength = array.length, length = args.length;
            while (length--) array[arrayLength + length] = args[length];
            return array;
        }

        function merge(array, args) {
            array = slice.call(array, 0);
            return update(array, args);
        }

        // Prototype's bind curries leading arguments; same shape as native
        // bind, kept here for argument-shape parity.
        function bind(context) {
            if (arguments.length < 2 && typeof arguments[0] === 'undefined') return this;
            if (typeof this !== 'function') throw new TypeError('The object is not callable.');
            var nop = function () {};
            var __method = this, args = slice.call(arguments, 1);
            var bound = function () {
                var a = merge(args, arguments);
                return __method.apply(context, a);
            };
            nop.prototype = this.prototype;
            bound.prototype = new nop();
            return bound;
        }

        function bindAsEventListener(context) {
            var __method = this, args = slice.call(arguments, 1);
            return function (event) {
                var a = update([event || window.event], args);
                return __method.apply(context, a);
            };
        }

        function curry() {
            if (!arguments.length) return this;
            var __method = this, args = slice.call(arguments, 0);
            return function () {
                return __method.apply(this, merge(args, arguments));
            };
        }

        function delay(timeout) {
            var __method = this, args = slice.call(arguments, 1);
            timeout = timeout * 1000;
            return window.setTimeout(function () {
                return __method.apply(__method, args);
            }, timeout);
        }

        function defer() {
            var args = update([0.01], arguments);
            return this.delay.apply(this, args);
        }

        function wrap(wrapper) {
            var __method = this;
            return function () {
                var a = update([__method.bind(this)], arguments);
                return wrapper.apply(this, a);
            };
        }

        function methodize() {
            if (this._methodized) return this._methodized;
            var __method = this;
            return this._methodized = function () {
                var a = update([this], arguments);
                return __method.apply(null, a);
            };
        }

        return {
            argumentNames: function () { return argumentNames(this); },
            bind: bind,
            bindAsEventListener: bindAsEventListener,
            curry: curry,
            delay: delay,
            defer: defer,
            wrap: wrap,
            methodize: methodize
        };
    })());

    /* ------------------------------------------------------------------ *
     * Enumerable core, shared by Array.prototype                          *
     * ------------------------------------------------------------------ */
    function eachSlice(array, number, iterator, context) {
        var index = -number, slices = [];
        while ((index += number) < array.length) {
            slices.push(array.slice(index, index + number));
        }
        return slices.map(function (slice) { iterator.call(context, slice); });
    }

    var Enumerable = {
        each: function (iterator, context) {
            try {
                this._each(iterator, context);
            } catch (e) {
                if (e !== $break) throw e;
            }
            return this;
        },
        eachSlice: eachSlice,
        all: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var result = true;
            this.each(function (value, index) {
                if (!(result = result && !!iterator.call(context, value, index, this))) throw $break;
            }, this);
            return result;
        },
        any: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var result = false;
            this.each(function (value, index) {
                if ((result = !!iterator.call(context, value, index, this))) throw $break;
            }, this);
            return result;
        },
        collect: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var results = [];
            this.each(function (value, index) {
                results.push(iterator.call(context, value, index, this));
            }, this);
            return results;
        },
        detect: function (iterator, context) {
            var result;
            this.each(function (value, index) {
                if (iterator.call(context, value, index, this)) {
                    result = value;
                    throw $break;
                }
            }, this);
            return result;
        },
        findAll: function (iterator, context) {
            var results = [];
            this.each(function (value, index) {
                if (iterator.call(context, value, index, this)) results.push(value);
            }, this);
            return results;
        },
        grep: function (filter, iterator, context) {
            iterator = iterator || Prototype.K;
            var results = [];
            if (typeof filter === 'string') filter = new RegExp(RegExp.escape(filter));
            this.each(function (value, index) {
                if (filter.match(value)) results.push(iterator.call(context, value, index, this));
            }, this);
            return results;
        },
        include: function (object) {
            if (typeof this.indexOf === 'function') return this.indexOf(object) !== -1;
            var found = false;
            this.each(function (value) {
                if (value === object) {
                    found = true;
                    throw $break;
                }
            });
            return found;
        },
        inGroupsOf: function (number, fillWith) {
            fillWith = typeof fillWith === 'undefined' ? null : fillWith;
            return this.eachSlice(number, function (slice) {
                while (slice.length < number) slice.push(fillWith);
                return slice;
            });
        },
        inject: function (memo, iterator, context) {
            this.each(function (value, index) {
                memo = iterator.call(context, memo, value, index, this);
            }, this);
            return memo;
        },
        invoke: function (method) {
            var args = $A(arguments).slice(1);
            return this.map(function (value) {
                return value[method].apply(value, args);
            });
        },
        max: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var result;
            this.each(function (value, index) {
                value = iterator.call(context, value, index, this);
                if (result == null || value >= result) result = value;
            }, this);
            return result;
        },
        min: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var result;
            this.each(function (value, index) {
                value = iterator.call(context, value, index, this);
                if (result == null || value < result) result = value;
            }, this);
            return result;
        },
        partition: function (iterator, context) {
            iterator = iterator || Prototype.K;
            var trues = [], falses = [];
            this.each(function (value, index) {
                (iterator.call(context, value, index, this) ? trues : falses).push(value);
            }, this);
            return [trues, falses];
        },
        pluck: function (property) {
            var results = [];
            this.each(function (value) {
                results.push(value[property]);
            });
            return results;
        },
        reject: function (iterator, context) {
            var results = [];
            this.each(function (value, index) {
                if (!iterator.call(context, value, index, this)) results.push(value);
            }, this);
            return results;
        },
        sortBy: function (iterator, context) {
            return this.map(function (value, index) {
                return { value: value, criteria: iterator.call(context, value, index, this) };
            }, this).sort(function (left, right) {
                var a = left.criteria, b = right.criteria;
                return a < b ? -1 : a > b ? 1 : 0;
            }).pluck('value');
        },
        toArray: function () {
            return this.map();
        },
        zip: function () {
            var iterator = Prototype.K, args = $A(arguments);
            if (typeof args.last() === 'function') iterator = args.pop();
            var collections = [this].concat(args).map($A);
            return this.map(function (value, index) {
                return iterator(collections.pluck(index));
            });
        },
        size: function () {
            return this.length;
        },
        inspect: function () {
            return '[' + this.map(Object.inspect).join(', ') + ']';
        }
    };

    /* ------------------------------------------------------------------ *
     * Array.prototype                                                     *
     * ------------------------------------------------------------------ */
    // Capture natives before the Enumerable mixin lands, so the standard
    // aliases win over the Enumerable versions (Prototype does the same).
    var nativeMap = Array.prototype.map,
        nativeFilter = Array.prototype.filter,
        nativeEvery = Array.prototype.every,
        nativeSome = Array.prototype.some;

    Object.extend(Array.prototype, Enumerable);

    Array.prototype._reverse = Array.prototype.reverse;

    Object.extend(Array.prototype, {
        _each: function (iterator, context) {
            // Native-forEach semantics (what Prototype uses when available):
            // holes in sparse arrays are skipped.
            for (var i = 0, length = this.length >>> 0; i < length; i++) {
                if (i in this) iterator.call(context, this[i], i, this);
            }
        },
        clear: function () {
            this.length = 0;
            return this;
        },
        first: function () {
            return this[0];
        },
        last: function () {
            return this[this.length - 1];
        },
        compact: function () {
            return this.filter(function (value) { return value != null; });
        },
        flatten: function () {
            return this.inject([], function (array, value) {
                return array.concat(Array.isArray(value) ? value.flatten() : [value]);
            });
        },
        without: function () {
            var values = $A(arguments);
            return this.filter(function (value) { return !values.include(value); });
        },
        reverse: function (inline) {
            return (inline === false ? this.toArray() : this)._reverse();
        },
        uniq: function (sorted) {
            return this.inject([], function (array, value, index) {
                if (index === 0 || (sorted ? array.last() !== value : !array.include(value))) {
                    array.push(value);
                }
                return array;
            });
        },
        intersect: function (array) {
            return this.uniq().findAll(function (item) { return array.indexOf(item) !== -1; });
        },
        clone: function () {
            return this.slice(0);
        },
        inspect: function () {
            return '[' + this.map(Object.inspect).join(', ') + ']';
        }
    });

    /* ------------------------------------------------------------------ *
     * String.prototype                                                    *
     * ------------------------------------------------------------------ */
    function substituteTemplate(str, object, pattern) {
        return new Template(str, pattern).evaluate(object);
    }

    Object.extend(String.prototype, {
        gsub: function (pattern, replacement) {
            var result = '', source = this.toString(), match;
            replacement = arguments.length < 2 ? '' : replacement;
            if (typeof pattern === 'string') pattern = new RegExp(RegExp.escape(pattern));

            // Reset lastIndex so a reused /g regexp behaves like Prototype's.
            pattern = new RegExp(pattern.source, pattern.global ? 'g' : 'g');

            while (source.length > 0) {
                match = source.match(pattern);
                if (match && match[0].length > 0) {
                    result += source.slice(0, match.index);
                    if (typeof replacement === 'function') {
                        result += String.interpret(replacement(match));
                    } else {
                        result += new Template(replacement).evaluate(match);
                    }
                    source = source.slice(match.index + match[0].length);
                } else {
                    result += source;
                    break;
                }
            }
            return result;
        },
        sub: function (pattern, replacement, count) {
            count = typeof count === 'undefined' ? 1 : count;
            return this.gsub(pattern, function (match) {
                if (--count < 0) return match[0];
                return typeof replacement === 'function' ? replacement(match) : new Template(replacement).evaluate(match);
            });
        },
        scan: function (pattern, iterator) {
            this.gsub(pattern, iterator);
            return String(this);
        },
        truncate: function (length, truncation) {
            length = length || 30;
            truncation = typeof truncation === 'undefined' ? '...' : truncation;
            return this.length > length ?
                this.slice(0, length - truncation.length) + truncation : String(this);
        },
        strip: String.prototype.trim ? String.prototype.trim : function () {
            return this.replace(/^\s+/, '').replace(/\s+$/, '');
        },
        stripTags: function () {
            return this.replace(/<\w+(\s+("[^"]*"|'[^']*'|[^>])+)?(\/)?>|<\/\w+>/gi, '');
        },
        stripScripts: function () {
            return this.replace(new RegExp(Prototype.ScriptFragment, 'img'), '');
        },
        extractScripts: function () {
            var matchAll = new RegExp(Prototype.ScriptFragment, 'img');
            var matchOne = new RegExp(Prototype.ScriptFragment, 'im');
            return (this.match(matchAll) || []).map(function (scriptTag) {
                return (scriptTag.match(matchOne) || ['', ''])[1];
            });
        },
        evalScripts: function () {
            // Indirect eval: scripts land in GLOBAL scope, like Prototype's
            // (app code declares globals this way — main_item_search_gui & co).
            return this.extractScripts().map(function (script) { return (1, eval)(script); });
        },
        escapeHTML: function () {
            return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        },
        unescapeHTML: function () {
            return this.stripTags().replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
        },
        toQueryParams: function (separator) {
            var match = this.strip().match(/([^?#]*)(#.*)?$/);
            if (!match) return {};
            return match[1].split(separator || '&').inject({}, function (hash, pair) {
                if ((pair = pair.split('='))[0]) {
                    var key = decodeURIComponent(pair.shift());
                    var value = pair.length > 1 ? pair.join('=') : pair[0];
                    if (value !== undefined) value = decodeURIComponent(value);
                    if (key in hash) {
                        if (!Array.isArray(hash[key])) hash[key] = [hash[key]];
                        hash[key].push(value);
                    } else {
                        hash[key] = value;
                    }
                }
                return hash;
            });
        },
        toArray: function () {
            return this.split('');
        },
        succ: function () {
            return this.slice(0, this.length - 1) +
                String.fromCharCode(this.charCodeAt(this.length - 1) + 1);
        },
        times: function (count) {
            return count < 1 ? '' : new Array(count + 1).join(this);
        },
        camelize: function () {
            var parts = this.split('-'), len = parts.length;
            if (len === 1) return parts[0];
            var camelized = this.charAt(0) === '-'
                ? parts[0].charAt(0).toUpperCase() + parts[0].substring(1)
                : parts[0];
            for (var i = 1; i < len; i++) {
                camelized += parts[i].charAt(0).toUpperCase() + parts[i].substring(1);
            }
            return camelized;
        },
        capitalize: function () {
            return this.charAt(0).toUpperCase() + this.substring(1).toLowerCase();
        },
        underscore: function () {
            return this.replace(/::/g, '/').replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
                .replace(/([a-z\d])([A-Z])/g, '$1_$2').replace(/-/g, '_').toLowerCase();
        },
        dasherize: function () {
            return this.replace(/_/g, '-');
        },
        inspect: function (useDoubleQuotes) {
            var escapedString = this.replace(/[\x00-\x1f\\]/g, function (character) {
                if (character in String.prototype.inspect.specialChar) {
                    return String.prototype.inspect.specialChar[character];
                }
                return '\\u00' + character.charCodeAt(0).toPaddedString(2, 16);
            });
            if (useDoubleQuotes) return '"' + escapedString.replace(/"/g, '\\"') + '"';
            return "'" + escapedString.replace(/'/g, "\\'") + "'";
        },
        unfilterJSON: function (filter) {
            return this.replace(filter || Prototype.JSONFilter, '$1');
        },
        isJSON: function () {
            var str = this;
            if (str.blank()) return false;
            str = str.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@');
            str = str.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
            str = str.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
            return (/^[\],:{}\s]*$/).test(str);
        },
        evalJSON: function (sanitize) {
            var json = this.unfilterJSON();
            var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
            if (cx.test(json)) {
                json = json.replace(cx, function (a) { return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4); });
            }
            try {
                if (!sanitize || json.isJSON()) return eval('(' + json + ')');
            } catch (e) {}
            throw new SyntaxError('Badly formed JSON string: ' + this.inspect());
        },
        include: function (pattern) {
            return this.indexOf(pattern) > -1;
        },
        startsWith: function (pattern, position) {
            position = position || 0;
            return this.lastIndexOf(pattern, position) === position;
        },
        endsWith: function (pattern, position) {
            var d = this.length - pattern.length;
            if (position !== undefined) d = position;
            return d >= 0 && this.indexOf(pattern, d) === d;
        },
        empty: function () {
            return this === '';
        },
        blank: function () {
            return /^\s*$/.test(this);
        },
        interpolate: function (object, pattern) {
            return new Template(this, pattern).evaluate(object);
        }
    });

    String.prototype.inspect.specialChar = {
        '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '\\': '\\\\'
    };

    /* ------------------------------------------------------------------ *
     * Number.prototype                                                    *
     * ------------------------------------------------------------------ */
    Object.extend(Number.prototype, (function () {
        function toColorPart() {
            return this.toPaddedString(2, 16);
        }
        function succ() {
            return this + 1;
        }
        function times(iterator, context) {
            $R(0, this, true).each(iterator, context);
            return this;
        }
        function toPaddedString(length, radix) {
            var string = this.toString(radix || 10);
            return '0'.times(length - string.length) + string;
        }
        function abs() { return Math.abs(this); }
        function round() { return Math.round(this); }
        function ceil() { return Math.ceil(this); }
        function floor() { return Math.floor(this); }
        return {
            toColorPart: toColorPart,
            succ: succ,
            times: times,
            toPaddedString: toPaddedString,
            abs: abs,
            round: round,
            ceil: ceil,
            floor: floor
        };
    })());

    /* ------------------------------------------------------------------ *
     * RegExp.escape                                                       *
     * ------------------------------------------------------------------ */
    RegExp.escape = function (str) {
        return String(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
    };

    /* ------------------------------------------------------------------ *
     * Hash gets the full Enumerable mixin (map, collect, inject, ...)     *
     * — Prototype mixes it into Hash.prototype as well. Hash is defined   *
     * by shim-core, already loaded at this point.                         *
     * ------------------------------------------------------------------ */
    if (global.Hash) {
        Object.extend(global.Hash.prototype, Enumerable);
    }
    global.Enumerable = Enumerable;

    // Prototype aliases (map→collect, find→detect, ...) on Enumerable
    // consumers that lack a native version (Hash, ObjectRange).
    Enumerable.map = Enumerable.collect;
    Enumerable.find = Enumerable.detect;
    Enumerable.select = Enumerable.findAll;
    Enumerable.member = Enumerable.include;
    Enumerable.entries = Enumerable.toArray;
    if (global.Hash) {
        Object.extend(global.Hash.prototype, {
            map: Enumerable.map,
            find: Enumerable.find,
            select: Enumerable.select,
            member: Enumerable.member,
            entries: Enumerable.entries
        });
    }
    if (global.ObjectRange) {
        // Preserve the range's own include/toArray: _each relies on them,
        // and Enumerable.include would recurse through each().
        var rangeInclude = global.ObjectRange.prototype.include;
        var rangeToArray = global.ObjectRange.prototype.toArray;
        Object.extend(global.ObjectRange.prototype, Enumerable, {
            map: Enumerable.map,
            find: Enumerable.find,
            select: Enumerable.select,
            member: Enumerable.member,
            entries: Enumerable.entries
        });
        global.ObjectRange.prototype.include = rangeInclude;
        global.ObjectRange.prototype.toArray = rangeToArray;
        global.ObjectRange.prototype.entries = rangeToArray;
    }

    // Native aliases win on real Arrays — wrapped so zero-argument calls
    // default to Prototype.K, exactly like Prototype's wrapNative (this is
    // what lets Enumerable.toArray call this.map() with no callback).
    function wrapNative(method) {
        return function () {
            if (arguments.length === 0) return method.call(this, Prototype.K);
            if (arguments[0] === undefined) {
                var args = Array.prototype.slice.call(arguments, 1);
                args.unshift(Prototype.K);
                return method.apply(this, args);
            }
            return method.apply(this, arguments);
        };
    }
    if (nativeMap) Array.prototype.map = wrapNative(nativeMap);
    if (nativeFilter) Array.prototype.filter = nativeFilter;
    if (nativeEvery) Array.prototype.every = wrapNative(nativeEvery);
    if (nativeSome) Array.prototype.some = wrapNative(nativeSome);
    // Prototype alias names that have no native counterpart.
    Array.prototype.find = Array.prototype.find || Enumerable.detect;
    Array.prototype.select = nativeFilter;
    Array.prototype.member = Array.prototype.member || Enumerable.include;
    // Prototype's entries returns a plain array (toArray), unlike the
    // native iterator-returning entries — Prototype semantics win here.
    Array.prototype.entries = Enumerable.toArray;

    /* ------------------------------------------------------------------ *
     * String.from — kept for parity                                       *
     * ------------------------------------------------------------------ */
    if (!String.from) {
        String.from = function (object) { return String(object); };
    }

    if (global.console && global.console.info) {
        console.info('[idae-shim] enumerable loaded');
    }
})(window);
