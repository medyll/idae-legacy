/**
 * shim-event.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Event.observe/stopObserving/stop/element/pointer, delegation via
 * Element#on(selector...), Element#fire through CustomEvent, dom:loaded.
 *
 * Depends on: shim-core.js, shim-enumerable.js, shim-element.js
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    var docEl = document.documentElement;

    /* ------------------------------------------------------------------ *
     * Handler registry — Prototype stopObserving(el, name) with no        *
     * handler removes every handler for that event name, so we must       *
     * track what observe() attached.                                      *
     * ------------------------------------------------------------------ */
    var registry = [];
    var registryId = 0;

    function findEventRecords(element, eventName, handler) {
        return registry.filter(function (record) {
            if (record.element !== element) return false;
            if (eventName && record.eventName !== eventName) return false;
            if (handler && record.handler !== handler) return false;
            return true;
        });
    }

    function observe(element, eventName, handler) {
        element = typeof element === 'string' ? document.getElementById(element) : element;
        if (!element) return element;
        element.addEventListener(eventName, handler, false);
        registry.push({ id: ++registryId, element: element, eventName: eventName, handler: handler });
        return element;
    }

    function stopObserving(element, eventName, handler) {
        element = typeof element === 'string' ? document.getElementById(element) : element;
        if (!element) return element;
        var records = findEventRecords(element, eventName, handler);
        records.forEach(function (record) {
            element.removeEventListener(record.eventName, record.handler, false);
            registry.splice(registry.indexOf(record), 1);
        });
        return element;
    }

    /* ------------------------------------------------------------------ *
     * Event statics                                                       *
     * ------------------------------------------------------------------ */
    var Event = {
        KEY_BACKSPACE: 8,
        KEY_TAB: 9,
        KEY_RETURN: 13,
        KEY_ESC: 27,
        KEY_LEFT: 37,
        KEY_UP: 38,
        KEY_RIGHT: 39,
        KEY_DOWN: 40,
        KEY_DELETE: 46,
        KEY_HOME: 36,
        KEY_END: 35,
        KEY_PAGEUP: 33,
        KEY_PAGEDOWN: 34,
        KEY_INSERT: 45,

        observe: observe,
        stopObserving: stopObserving,

        element: function (event) {
            event = Event.extend(event);
            var node = event.target, type = event.type,
                currentTarget = event.currentTarget;
            if (currentTarget && currentTarget.tagName) {
                if (type === 'load' ||
                    (type === 'error' && (currentTarget.tagName === 'IMG' || currentTarget.tagName === 'BODY'))) {
                    node = currentTarget;
                }
            }
            return node.nodeType === 3 ? node.parentNode : node;
        },

        findElement: function (event, expression) {
            var element = Event.element(event);
            if (!expression) return element;
            return element.match(expression) ? element : element.up(expression);
        },

        isLeftClick: function (event) { return event.button === 0; },
        isMiddleClick: function (event) { return event.button === 1; },
        isRightClick: function (event) { return event.button === 2; },

        pointer: function (event) {
            var docElement = document.documentElement,
                body = document.body || { scrollLeft: 0, scrollTop: 0 };
            return {
                x: event.pageX || (event.clientX +
                    (docElement.scrollLeft || body.scrollLeft) -
                    (docElement.clientLeft || 0)),
                y: event.pageY || (event.clientY +
                    (docElement.scrollTop || body.scrollTop) -
                    (docElement.clientTop || 0))
            };
        },
        pointerX: function (event) { return Event.pointer(event).x; },
        pointerY: function (event) { return Event.pointer(event).y; },

        stop: function (event) {
            Event.extend(event);
            event.preventDefault();
            event.stopPropagation();
            event.stopped = true;
        },

        // Prototype augmented native events with its methods; CustomEvents
        // we create already carry everything, native ones get the helpers.
        extend: (function () {
            var METHODS = ['stop', 'element', 'findElement', 'pointer', 'pointerX', 'pointerY',
                'isLeftClick', 'isMiddleClick', 'isRightClick'];
            return function (event) {
                if (!event) return false;
                if (event._extendedByShim) return event;
                event._extendedByShim = Prototype.emptyFunction;
                METHODS.forEach(function (name) {
                    if (!(name in event)) {
                        event[name] = Event[name].bind(Event, event);
                    }
                });
                return event;
            };
        })(),

        fire: function (element, eventName, memo, bubble) {
            element = typeof element === 'string' ? document.getElementById(element) : element;
            if (element !== document && document.createEvent && !element.dispatchEvent) {
                element = docEl;
            }
            if (typeof bubble === 'undefined') bubble = true;
            var event;
            if (typeof global.CustomEvent === 'function') {
                event = new CustomEvent(eventName, { bubbles: bubble, cancelable: true });
            } else {
                event = document.createEvent('HTMLEvents');
                event.initEvent(eventName, bubble, true);
            }
            event.memo = memo || {};
            (element || docEl).dispatchEvent(event);
            return Event.extend(event);
        }
    };

    /* ------------------------------------------------------------------ *
     * Delegation — Prototype's Element#on(event, selector, handler)       *
     * ------------------------------------------------------------------ */
    function delegatedHandler(element, selector, handler) {
        return function (event) {
            var target = event.target;
            while (target && target !== element) {
                if (target.nodeType === 1 && (target.matches || target.msMatchesSelector).call(target, selector)) {
                    handler.call(target, event, target);
                    return;
                }
                target = target.parentNode;
            }
        };
    }

    var NATIVE_ELEMENT_PROTO = global.__idaeNativeElementProto ||
        Object.getPrototypeOf(HTMLElement.prototype);

    function delegateOn(eventName, selector, handler) {
        if (handler === undefined) {
            return Event.observe(this, eventName, selector);
        }
        var wrapped = delegatedHandler(this, selector, handler);
        Event.observe(this, eventName, wrapped);
        return this;
    }
    if (NATIVE_ELEMENT_PROTO) {
        NATIVE_ELEMENT_PROTO.on = delegateOn;
    }

    /* ------------------------------------------------------------------ *
     * dom:loaded                                                          *
     * ------------------------------------------------------------------ */
    function fireContentLoadedEvent() {
        if (document.loaded) return;
        document.loaded = true;
        Event.fire(document, 'dom:loaded', null, false);
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        fireContentLoadedEvent.defer ? fireContentLoadedEvent.defer() : fireContentLoadedEvent();
    } else {
        document.addEventListener('DOMContentLoaded', fireContentLoadedEvent, false);
    }

    /* ------------------------------------------------------------------ *
     * document / window observation — Prototype extends document with     *
     * the Event methods too (app_functions.js, app_cache.js, observers.js *
     * all call document.observe / document.on).                           *
     * ------------------------------------------------------------------ */
    [global.Document && Document.prototype, global.HTMLDocument && HTMLDocument.prototype]
        .filter(Boolean)
        .forEach(function (proto) {
            if (proto.__idaeShimEventPatched) return;
            proto.__idaeShimEventPatched = true;
            proto.observe = function (eventName, handler) {
                return Event.observe(this, eventName, handler);
            };
            proto.stopObserving = function (eventName, handler) {
                return Event.stopObserving(this, eventName, handler);
            };
            proto.fire = function (eventName, memo, bubble) {
                return Event.fire(this, eventName, memo, bubble);
            };
            proto.on = delegateOn;
        });

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    global.Event = Object.extend(global.Event || {}, Event);

    if (global.console && global.console.info) {
        console.info('[idae-shim] event loaded');
    }
})(window);
