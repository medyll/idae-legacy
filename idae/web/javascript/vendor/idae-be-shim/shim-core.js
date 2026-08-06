/**
 * shim-core.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Globals: $, $$, $A, $H, $w, $F, $R, Prototype.*, Try.these, IDAE_SHIM_WARN hook
 *
 * Loaded in place of prototype-1.7.3 (require_hell group, main_bag.js).
 * Only implements what the application actually calls — the contract is
 * playwright/tests/prototype-surface.spec.ts.
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    /* ------------------------------------------------------------------ *
     * IDAE_SHIM_WARN — dev flag. When window.IDAE_SHIM_WARN is truthy,    *
     * every shimmed call logs family/name + a stack, so Phase 5 can       *
     * collect the real call-sites by navigating the app instead of        *
     * grepping. Centralised here; every other shim file uses it.          *
     * ------------------------------------------------------------------ */
    function shimWarn(family, name) {
        if (!global.IDAE_SHIM_WARN) return;
        try {
            throw new Error('shim-trace');
        } catch (e) {
            global.console.warn('[idae-shim] ' + family + '.' + name + '\n' + (e.stack || ''));
        }
    }
    global.__idaeShimWarn = shimWarn;

    /* ------------------------------------------------------------------ *
     * installWarnWraps — wraps every function the shim patched onto the   *
     * native prototypes (Element/Array/String/Number/Function) so each    *
     * call logs family.name + a stack. Activated either by setting        *
     * window.IDAE_SHIM_WARN before the shims load, or at runtime:         *
     *   window.IDAE_SHIM_WARN = 1; window.__idaeShimInstallWarn();        *
     * ------------------------------------------------------------------ */
    function installWarnWraps() {
        if (installWarnWraps.done) return;
        installWarnWraps.done = true;
        [
            ['Element', global.__idaeNativeElementProto || Element.prototype],
            ['Array', Array.prototype],
            ['String', String.prototype],
            ['Number', Number.prototype],
            ['Function', Function.prototype]
        ].forEach(function (pair) {
            var family = pair[0], proto = pair[1];
            Object.keys(proto).forEach(function (name) {
                var descriptor = Object.getOwnPropertyDescriptor(proto, name);
                if (!descriptor || typeof descriptor.value !== 'function') return;
                if (descriptor.value.__idaeShimWrapped) return;
                // Only wrap our own additions — never native members.
                if (!SHIM_OWNED[family] || !SHIM_OWNED[family].include(name)) return;
                var original = descriptor.value;
                var wrapped = function () {
                    shimWarn(family, name);
                    return original.apply(this, arguments);
                };
                wrapped.__idaeShimWrapped = true;
                try {
                    Object.defineProperty(proto, name, {
                        value: wrapped,
                        writable: true,
                        enumerable: descriptor.enumerable,
                        configurable: true
                    });
                } catch (e) { /* non-configurable: leave unwrapped */ }
            });
        });
        console.info('[idae-shim] warn wraps installed');
    }

    // The members each shim file adds to native prototypes. Kept explicit
    // so the warn wraps can never touch a native method.
    var SHIM_OWNED = {
        Element: null, // filled below, after shim-element has run
        Array: ['each', 'eachSlice', 'all', 'any', 'collect', 'detect', 'findAll', 'grep',
            'include', 'inGroupsOf', 'inject', 'invoke', 'max', 'min', 'partition', 'pluck',
            'reject', 'sortBy', 'toArray', 'zip', 'size', 'inspect', 'clear', 'first', 'last',
            'compact', 'flatten', 'without', 'uniq', 'intersect', 'clone'],
        String: ['gsub', 'sub', 'scan', 'truncate', 'strip', 'stripTags', 'stripScripts',
            'extractScripts', 'evalScripts', 'escapeHTML', 'unescapeHTML', 'toQueryParams',
            'toArray', 'succ', 'times', 'camelize', 'capitalize', 'underscore', 'dasherize',
            'inspect', 'unfilterJSON', 'isJSON', 'evalJSON', 'include', 'startsWith',
            'endsWith', 'empty', 'blank', 'interpolate'],
        Number: ['toColorPart', 'succ', 'times', 'toPaddedString', 'abs', 'round', 'ceil', 'floor'],
        Function: ['argumentNames', 'bind', 'bindAsEventListener', 'curry', 'delay', 'defer',
            'wrap', 'methodize']
    };

    // Element methods are registered by shim-element through addMethods;
    // rather than duplicating the list here, wrap whatever shim-element
    // marked. It stamps its contributions on Element.__shimMethods.
    installWarnWraps.lateResolveElement = function () {
        SHIM_OWNED.Element = (global.Element && global.Element.__shimMethods) || [];
    };

    global.__idaeShimInstallWarn = function () {
        installWarnWraps.lateResolveElement();
        installWarnWraps();
    };

    /* ------------------------------------------------------------------ *
     * $ — id or element → element (Prototype returned it "extended"; our  *
     * methods live on Element.prototype, so this is an identity/lookup).  *
     * ------------------------------------------------------------------ */
    function $(element) {
        if (arguments.length > 1) {
            for (var i = 0, elements = [], length = arguments.length; i < length; i++) {
                elements.push($(arguments[i]));
            }
            return elements;
        }
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        return element || null;
    }

    /* ------------------------------------------------------------------ *
     * Tolerant qSA — Prototype's selector engine accepted unquoted        *
     * attribute values that CSS forbids ([data-uniqid=6a74...] when the   *
     * value starts with a digit). Retry with quoted attribute values.     *
     * ------------------------------------------------------------------ */
    function tolerantQueryAll(root, selector) {
        try {
            return root.querySelectorAll(selector);
        } catch (e) {
            var quoted = String(selector).replace(
                /\[([a-zA-Z_][\w-]*)=([^'"\]\s][^\]\s]*)\]/g,
                '[$1="$2"]'
            );
            if (quoted === selector) throw e;
            return root.querySelectorAll(quoted);
        }
    }
    global.__idaeQSA = tolerantQueryAll;

    /* ------------------------------------------------------------------ *
     * $$ — CSS selector → real Array                                      *
     * ------------------------------------------------------------------ */
    function $$(selector) {
        return $A(tolerantQueryAll(document, selector));
    }

    /* ------------------------------------------------------------------ *
     * $A — iterable / array-like / arguments → real Array                 *
     * ------------------------------------------------------------------ */
    function $A(iterable) {
        if (!iterable) return [];
        if ('toArray' in Object(iterable)) return iterable.toArray();
        var length = iterable.length || 0, results = new Array(length);
        while (length--) results[length] = iterable[length];
        return results;
    }

    /* ------------------------------------------------------------------ *
     * $w — "a b c" → ['a','b','c']                                        *
     * ------------------------------------------------------------------ */
    function $w(string) {
        if (!string || typeof string !== 'string') return [];
        string = string.strip();
        return string ? string.split(/\s+/) : [];
    }

    // Local extend — Object.extend only lands with shim-class.js, which
    // loads after this file.
    function extendOwn(destination, source) {
        for (var property in source) {
            destination[property] = source[property];
        }
        return destination;
    }

    /* ------------------------------------------------------------------ *
     * Hash — minimal ordered map with Prototype semantics                 *
     * ------------------------------------------------------------------ */
    function Hash(object) {
        this._object = extendOwn({}, object || {});
    }
    extendOwn(Hash.prototype, (function () {
        function toQueryPair(key, value) {
            if (value == null) return key;
            return encodeURIComponent(key) + '=' + encodeURIComponent(value);
        }
        return {
            _each: function (iterator, context) {
                var i = 0;
                for (var key in this._object) {
                    if (Object.prototype.hasOwnProperty.call(this._object, key)) {
                        var value = this._object[key], pair = [key, value];
                        pair.key = key; pair.value = value;
                        iterator.call(context, pair, i++);
                    }
                }
            },
            each: function (iterator, context) {
                this._each(iterator, context);
                return this;
            },
            set: function (key, value) {
                this._object[key] = value;
                return this;
            },
            get: function (key) {
                return this._object[key];
            },
            unset: function (key) {
                var value = this._object[key];
                delete this._object[key];
                return value;
            },
            keys: function () {
                return this.pluck('key');
            },
            values: function () {
                return this.pluck('value');
            },
            pluck: function (property) {
                var results = [];
                this._each(function (pair) { results.push(pair[property]); });
                return results;
            },
            merge: function (object) {
                return this.clone().update(object);
            },
            update: function (object) {
                new Hash(object)._each(function (pair) { this.set(pair.key, pair.value); }, this);
                return this;
            },
            toQueryString: function () {
                var results = [];
                this._each(function (pair) {
                    var key = pair.key, value = pair.value;
                    if (value && typeof value === 'object') {
                        if (Array.isArray(value)) {
                            for (var i = 0; i < value.length; i++) results.push(toQueryPair(key, value[i]));
                            return;
                        }
                        value = String.interpret ? String.interpret(value) : String(value);
                    }
                    results.push(toQueryPair(key, value));
                });
                return results.join('&');
            },
            inspect: function () {
                return '#<Hash:{' + this.map(function (pair) {
                    return pair.key.inspect ? pair.key.inspect() : JSON.stringify(pair.key) + ': ' +
                        (pair.value && pair.value.inspect ? pair.value.inspect() : JSON.stringify(pair.value));
                }).join(', ') + '}>';
            },
            clone: function () {
                return new Hash(this);
            },
            toObject: function () {
                return Object.clone(this._object);
            }
        };
    })());
    Hash.from = function (object) { return new Hash(object); };

    function $H(object) {
        return new Hash(object);
    }

    /* ------------------------------------------------------------------ *
     * ObjectRange + $R(start, end)                                        *
     * ------------------------------------------------------------------ */
    function ObjectRange(start, end, exclusive) {
        this.start = start;
        this.end = end;
        this.exclusive = exclusive;
    }
    extendOwn(ObjectRange.prototype, {
        _each: function (iterator, context) {
            var value = this.start;
            while (this.include(value)) {
                iterator.call(context, value);
                value = value.succ ? value.succ() : value + 1;
            }
        },
        each: function (iterator, context) {
            this._each(iterator, context);
            return this;
        },
        include: function (value) {
            if (value < this.start) return false;
            if (this.exclusive) return value < this.end;
            return value <= this.end;
        },
        toArray: function () {
            var results = [];
            this._each(function (v) { results.push(v); });
            return results;
        }
    });

    function $R(start, end, exclusive) {
        return new ObjectRange(start, end, exclusive);
    }

    /* ------------------------------------------------------------------ *
     * $F — form field value by id/element/name                            *
     * ------------------------------------------------------------------ */
    function $F(element) {
        element = $(element);
        if (!element) return null;
        if (!('value' in element)) {
            // Form control lookup by name — Prototype resolves through
            // Form.findFirstElement semantics; the app only ever passes a
            // control or an id, so this branch is just a safety net.
            var found = document.getElementsByName(element && element.id ? element.id : '')[0];
            element = found || element;
        }
        if (element.type === 'checkbox' || element.type === 'radio') {
            return element.checked ? element.value : null;
        }
        if (element.tagName && element.tagName.toLowerCase() === 'select' && element.multiple) {
            return $A(element.options).filter(function (o) { return o.selected; })
                .map(function (o) { return o.value; });
        }
        return element.value;
    }

    /* ------------------------------------------------------------------ *
     * Prototype.* — version markers and tiny helpers the app touches      *
     * ------------------------------------------------------------------ */
    global.Prototype = {
        Version: '1.7.3-shim',
        ScriptFragment: '<script[^>]*>([\\S\\s]*?)<\/script\\s*>',
        JSONFilter: /^\/\*-secure-([\s\S]*)\*\/\s*$/,
        emptyFunction: function () {},
        K: function (x) { return x; },
        Browser: (function () {
            var ua = navigator.userAgent;
            return {
                IE: !!window.attachEvent && !window.opera,
                Opera: !!window.opera,
                WebKit: ua.indexOf('AppleWebKit/') > -1,
                Gecko: ua.indexOf('Gecko') > -1 && ua.indexOf('KHTML') === -1,
                MobileSafari: /Apple.*Mobile.*Safari/.test(ua),
                OldWebKit: false
            };
        })(),
        BrowserFeatures: {
            XPath: !!document.evaluate,
            SelectorsAPI: !!document.querySelector,
            ElementExtensions: true,
            SpecificElementExtensions: true
        }
    };

    /* ------------------------------------------------------------------ *
     * Try.these(fn, ...) — first function that doesn't throw              *
     * ------------------------------------------------------------------ */
    var Try = {
        these: function () {
            var returnValue;
            for (var i = 0, length = arguments.length; i < length; i++) {
                var lambda = arguments[i];
                try {
                    returnValue = lambda();
                    break;
                } catch (e) {}
            }
            return returnValue;
        }
    };

    /* ------------------------------------------------------------------ *
     * String.interpret — used by Hash/toQueryString paths                 *
     * ------------------------------------------------------------------ */
    if (!String.interpret) {
        String.interpret = function (value) {
            return value == null ? '' : String(value);
        };
    }

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    global.$ = $;
    global.$$ = $$;
    global.$A = $A;
    global.$w = $w;
    global.$F = $F;
    global.$R = $R;
    global.$H = $H;
    global.Hash = Hash;
    global.ObjectRange = ObjectRange;
    global.Try = Try;

    if (global.console && global.console.info) {
        console.info('[idae-shim] core loaded (Prototype ' + global.Prototype.Version + ')');
    }
})(window);
