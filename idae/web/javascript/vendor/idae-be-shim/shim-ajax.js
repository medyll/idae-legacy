/**
 * shim-ajax.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Ajax.Request / Ajax.Updater / Ajax.PeriodicalUpdater / Ajax.Responders,
 * PeriodicalExecuter, Form.serialize — backed by fetch().
 *
 * Depends on: shim-core.js, shim-class.js, shim-enumerable.js,
 *             shim-element.js, shim-event.js
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    /* ------------------------------------------------------------------ *
     * PeriodicalExecuter                                                  *
     * ------------------------------------------------------------------ */
    var PeriodicalExecuter = function (callback, frequency) {
        this.callback = callback;
        this.frequency = frequency;
        this.currentlyExecuting = false;
        this.registerCallback();
    };
    Object.extend(PeriodicalExecuter.prototype, {
        registerCallback: function () {
            var self = this;
            this.timer = setInterval(function () { self.onTimerEvent(); }, this.frequency * 1000);
        },
        execute: function () {
            this.callback(this);
        },
        stop: function () {
            if (!this.timer) return;
            clearInterval(this.timer);
            this.timer = null;
        },
        onTimerEvent: function () {
            if (!this.currentlyExecuting) {
                try {
                    this.currentlyExecuting = true;
                    this.execute();
                } catch (e) {
                    global.console && console.error('[idae-shim] PeriodicalExecuter error', e);
                } finally {
                    this.currentlyExecuting = false;
                }
            }
        }
    });

    /* ------------------------------------------------------------------ *
     * Ajax.Responders — global onCreate/onComplete dispatch               *
     * ------------------------------------------------------------------ */
    var Ajax = {
        activeRequestCount: 0,
        Responders: {
            responders: [],
            each: function (iterator, context) {
                for (var i = 0; i < this.responders.length; i++) {
                    iterator.call(context, this.responders[i], i, this.responders);
                }
                return this;
            },
            register: function (responder) {
                if (!this.include(responder)) this.responders.push(responder);
            },
            unregister: function (responder) {
                this.responders = this.responders.without(responder);
            },
            include: function (responder) {
                return this.responders.include(responder);
            },
            dispatch: function (callback, request, transport, json) {
                this.each(function (responder) {
                    if (typeof responder[callback] === 'function') {
                        try {
                            responder[callback].apply(responder, [request, transport, json]);
                        } catch (e) {
                            global.console && console.error('[idae-shim] Ajax.Responders error', e);
                        }
                    }
                });
            }
        }
    };

    /* ------------------------------------------------------------------ *
     * Ajax.Base — shared option plumbing                                  *
     * ------------------------------------------------------------------ */
    var AJAX_DEFAULTS = {
        method: 'post',
        asynchronous: true,
        contentType: 'application/x-www-form-urlencoded',
        encoding: 'UTF-8',
        parameters: '',
        evalJSON: true,
        evalJS: true
    };

    function normalizeOptions(options) {
        options = Object.extend(Object.clone(AJAX_DEFAULTS), options || {});
        options.method = options.method.toLowerCase();
        if (Object.isString(options.parameters)) {
            options.parameters = options.parameters.toQueryParams();
        }
        return options;
    }

    function buildHeaders(options) {
        var headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-Prototype-Version': Prototype.Version,
            'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
        };
        if (options.method === 'post') {
            headers['Content-type'] = options.contentType +
                (options.encoding ? '; charset=' + options.encoding : '');
        }
        if (options.requestHeaders) {
            if (Array.isArray(options.requestHeaders)) {
                for (var i = 0; i < options.requestHeaders.length; i += 2) {
                    headers[options.requestHeaders[i]] = options.requestHeaders[i + 1];
                }
            } else {
                Object.extend(headers, options.requestHeaders);
            }
        }
        return headers;
    }

    /* ------------------------------------------------------------------ *
     * Ajax.Request                                                        *
     * ------------------------------------------------------------------ */
    Ajax.Request = function (url, options) {
        this.options = normalizeOptions(options);
        this.url = url;
        this.transport = null;
        this._complete = false;
        this._controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
        Ajax.Responders.dispatch('onCreate', this, null);
        this.request(url);
    };
    Ajax.Request.Events = ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'];

    Object.extend(Ajax.Request.prototype, {
        request: function (url) {
            var options = this.options;
            var params = Object.extend({}, options.parameters);

            var method = options.method;
            if (!['get', 'post'].include(method)) {
                params['_method'] = method;
                method = 'post';
            }

            var body;
            if (method === 'get') {
                var qs = $H(params).toQueryString();
                if (qs) this.url += (this.url.include('?') ? '&' : '?') + qs;
            } else {
                body = Object.isString(options.parameters) && options.postBody === undefined ?
                    options.parameters :
                    (options.postBody !== undefined ? options.postBody : $H(params).toQueryString());
            }

            var self = this;
            Ajax.activeRequestCount++;

            var fetchOptions = {
                method: method.toUpperCase(),
                headers: buildHeaders(options),
                credentials: 'same-origin'
            };
            if (body !== undefined) fetchOptions.body = body;
            if (this._controller) fetchOptions.signal = this._controller.signal;

            this.respondToReadyState && this.respondToReadyState(1);

            global.fetch(this.url, fetchOptions).then(function (response) {
                return response.text().then(function (text) {
                    var transport = self.buildTransport(response, text);
                    self.transport = transport;
                    var json = self.evalJSON(transport);
                    self.dispatchCallbacks(transport, json, response.ok);
                });
            }).catch(function (error) {
                Ajax.activeRequestCount--;
                if (error && error.name === 'AbortError') return;
                var transport = { status: 0, statusText: String(error), responseText: '', request: self };
                self.transport = transport;
                if (typeof self.options.onException === 'function') {
                    self.options.onException(self, error);
                } else if (typeof self.options.onFailure === 'function') {
                    self.options.onFailure(transport);
                }
                Ajax.Responders.dispatch('onComplete', self, transport, null);
            });
        },

        buildTransport: function (response, text) {
            var self = this;
            var transport = {
                status: response.status,
                statusText: response.statusText,
                responseText: text,
                responseXML: null,
                request: this,
                aborted: false,
                abort: function () {
                    transport.aborted = true;
                    if (self._controller) self._controller.abort();
                },
                getHeader: function (name) { return response.headers.get(name); },
                getAllHeaders: function () {
                    var str = '';
                    response.headers.forEach(function (v, k) { str += k + ': ' + v + '\n'; });
                    return str;
                },
                getAllResponseHeaders: function () { return transport.getAllHeaders(); }
            };
            Object.defineProperty(transport, 'responseJSON', {
                get: function () { return self.evalJSON(transport); }
            });
            Object.defineProperty(transport, 'readyState', {
                get: function () { return 4; }
            });
            return transport;
        },

        evalJSON: function (transport) {
            if (!transport) return null;
            var options = this.options;
            if (!options.evalJSON) return null;
            var text = transport.responseText;
            if (!text) return null;
            var contentType = (transport.getHeader && transport.getHeader('Content-type')) || '';
            if (!(options.evalJSON === 'force' || contentType.include('application/json') || text.isJSON())) {
                return null;
            }
            try {
                return text.evalJSON(true);
            } catch (e) {
                return null;
            }
        },

        evalScripts: function (transport) {
            if (!this.options.evalJS) return;
            var text = transport && transport.responseText;
            if (!text || !/<script/i.test(text)) return;
            try {
                text.evalScripts();
            } catch (e) {
                global.console && console.error('[idae-shim] evalScripts error', e);
            }
        },

        dispatchCallbacks: function (transport, json, success) {
            this._complete = true;
            Ajax.activeRequestCount--;
            var options = this.options;

            if (this.options.evalJS) {
                // Updater overrides where scripts run relative to insertion.
                if (!this._skipEvalScripts) this.evalScripts(transport);
            }

            var statusName = this.getStatusName(transport.status);
            var callback = success ? options.onSuccess : options.onFailure;
            if (typeof options['on' + transport.status] === 'function') {
                options['on' + transport.status](transport, json);
            } else if (typeof callback === 'function') {
                callback(transport, json);
            }
            if (typeof options[statusName] === 'function') {
                options[statusName](transport, json);
            }
            Ajax.Responders.dispatch(success ? 'onSuccess' : 'onFailure', this, transport, json);

            if (typeof options.onComplete === 'function') {
                options.onComplete(transport, json);
            }
            Ajax.Responders.dispatch('onComplete', this, transport, json);
        },

        getStatusName: function (status) {
            if (status >= 200 && status < 300) return 'onSuccess';
            return 'onFailure';
        },

        isSuccess: function () {
            var status = this.transport ? this.transport.status : 0;
            return (status >= 200 && status < 300) || status === 304;
        },

        respondToReadyState: function (readyState) {
            var event = Ajax.Request.Events[readyState];
            var f = this.options['on' + event];
            if (typeof f === 'function') f(this.transport, this.transport ? this.transport.responseJSON : null);
        }
    });

    /* ------------------------------------------------------------------ *
     * Ajax.Updater(container, url, options)                               *
     * ------------------------------------------------------------------ */
    Ajax.Updater = function (container, url, options) {
        options = options || {};
        this.container = {
            success: (container && container.success) ? container.success : $(container),
            failure: (container && container.failure) ? container.failure :
                (container && container.success ? null : $(container))
        };

        options = Object.extend({
            method: 'post',
            insertion: null,
            evalScripts: false
        }, options);

        this.options = options;
        var originalOnComplete = options.onComplete;
        var originalOnFailure = options.onFailure;
        var self = this;

        options.onComplete = function (transport, json) {
            self.updateContent(transport.responseText);
            if (originalOnComplete) originalOnComplete(transport, json);
        };
        options.onFailure = function (transport, json) {
            var receiver = self.container.failure;
            if (receiver) self.updateContent(transport.responseText, true);
            if (originalOnFailure) originalOnFailure(transport, json);
        };

        Ajax.Request.call(this, url, options);
    };
    Ajax.Updater.prototype = Object.extend(Object.create(Ajax.Request.prototype), {
        _skipEvalScripts: true,

        updateContent: function (responseText, useFailure) {
            var receiver = this.container[useFailure ? 'failure' : 'success'];
            if (!receiver) return;

            var insertion = this.options.insertion;
            if (insertion) {
                if (Object.isString(insertion)) {
                    insertion = insertion.camelize().capitalize();
                }
                new Insertion[insertion](receiver, responseText);
            } else {
                receiver.update(responseText.stripScripts());
            }

            if (this.options.evalScripts && /<script/i.test(responseText)) {
                responseText.evalScripts.bind(responseText).defer();
            }
        },

        evalScripts: function () { /* handled by updateContent when evalScripts: true */ }
    });

    /* ------------------------------------------------------------------ *
     * Ajax.PeriodicalUpdater                                              *
     * ------------------------------------------------------------------ */
    Ajax.PeriodicalUpdater = function (container, url, options) {
        options = Object.extend({
            frequency: 2,
            decay: 1
        }, options || {});
        this.updater = null;
        this.container = container;
        this.url = url;
        this.options = options;
        this.frequency = options.frequency;
        this.decay = options.decay;
        this.timer = null;
        this.onTimerEvent();
    };
    Object.extend(Ajax.PeriodicalUpdater.prototype, {
        start: function () {
            var self = this;
            this.options.onComplete = this.updateComplete.bind(this);
            this.timer = setInterval(function () { self.onTimerEvent(); }, this.frequency * 1000);
        },
        stop: function () {
            if (this.timer) clearInterval(this.timer);
            this.timer = null;
        },
        updateComplete: function (transport) {
            var original = this.options.originalOnComplete;
            if (original) original(transport);
            if (this.decay !== 1) {
                this.frequency = this.frequency * this.decay;
                this.stop();
                this.start();
            }
        },
        onTimerEvent: function () {
            this.updater = new Ajax.Updater(this.container, this.url, this.options);
        }
    });

    /* ------------------------------------------------------------------ *
     * Form.serialize & friends                                            *
     * ------------------------------------------------------------------ */
    var Form = {
        serialize: function (form, options) {
            form = $(form);
            if (!form) return '';
            options = Object.extend({ submit: true }, options || {});
            var elements = Form.getElements(form);
            var data = elements.inject([], function (results, element) {
                if (!element.disabled && element.name) {
                    var key = element.name, value;
                    if (element.tagName.toLowerCase() === 'select' && element.multiple) {
                        value = $A(element.options).filter(function (o) { return o.selected; })
                            .map(function (o) { return o.value; });
                    } else {
                        value = $F(element);
                    }
                    if (value != null && element.type !== 'file' &&
                        (element.type !== 'submit' || (!options.submit && options.submit !== element.name))) {
                        if (element.type === 'submit') return results;
                        if (Array.isArray(value)) {
                            value.each(function (v) {
                                results.push(encodeURIComponent(key) + '=' + encodeURIComponent(String.interpret(v)));
                            });
                        } else {
                            results.push(encodeURIComponent(key) + '=' + encodeURIComponent(String.interpret(value)));
                        }
                    }
                }
                return results;
            });
            return data.join('&');
        },

        getElements: function (form) {
            form = $(form);
            var elements = $A(form.querySelectorAll('input, select, textarea, button'));
            return elements.filter(function (el) { return el.type !== 'image'; });
        },

        getInputs: function (form, typeName, name) {
            form = $(form);
            var inputs = form.getElementsByTagName('input');
            if (!typeName && !name) return $A(inputs);
            var matchingInputs = [];
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                if ((typeName && input.type !== typeName) || (name && input.name !== name)) continue;
                matchingInputs.push(input);
            }
            return matchingInputs;
        },

        disable: function (form) {
            form = $(form);
            Form.getElements(form).invoke('disable');
            return form;
        },

        enable: function (form) {
            form = $(form);
            Form.getElements(form).invoke('enable');
            return form;
        },

        findFirstElement: function (form) {
            form = $(form);
            var elements = Form.getElements(form).filter(function (el) {
                return !['hidden', 'submit', 'image', 'button'].include(el.type) && !el.disabled;
            });
            var firstByIndex = elements.filter(function (el) { return el.hasAttribute('tabIndex') && el.tabIndex >= 0; })
                .sortBy(function (el) { return el.tabIndex; }).first();
            return firstByIndex ? firstByIndex : elements.first();
        },

        focusFirstElement: function (form) {
            form = $(form);
            var el = Form.findFirstElement(form);
            if (el) el.focus();
            return form;
        },

        reset: function (form) {
            $(form).reset();
            return form;
        },

        Element: {
            focus: function (element) { $(element).focus(); return $(element); },
            select: function (element) { $(element).select(); return $(element); },
            getValue: function (element) { return $F(element); },
            setValue: function (element, value) {
                element = $(element);
                if (element.type === 'checkbox' || element.type === 'radio') {
                    element.checked = !!value;
                } else {
                    element.value = value;
                }
                return element;
            },
            serialize: function (element) {
                element = $(element);
                if (!element.disabled && element.name) {
                    var value = $F(element);
                    if (value !== undefined && value !== null) {
                        var pair = {};
                        pair[element.name] = value;
                        return $H(pair).toQueryString();
                    }
                }
                return '';
            },
            clear: function (element) {
                $(element).value = '';
                return $(element);
            },
            present: function (element) {
                return $(element).value !== '';
            },
            activate: function (element) {
                element = $(element);
                try {
                    element.focus();
                    if (element.select && (element.type !== 'hidden' || element.type === 'text')) {
                        element.select();
                    }
                } catch (e) {}
                return element;
            },
            disable: function (element) {
                element = $(element);
                element.disabled = true;
                return element;
            },
            enable: function (element) {
                element = $(element);
                element.disabled = false;
                return element;
            }
        }
    };
    Form.Serializers = Form.Element;

    // Form methods as form-element methods. Element.addMethods methodizes:
    // these receive the element as their first argument.
    Element.addMethods({
        serializeElements: function (element) {
            return Form.getElements(element);
        },
        getInputs: function (element, typeName, name) {
            return Form.getInputs(element, typeName, name);
        },
        disable: function (element) {
            element.disabled = true;
            return element;
        },
        enable: function (element) {
            element.disabled = false;
            return element;
        },
        focusFirstElement: function (element) {
            return Form.focusFirstElement(element);
        },
        request: function (element, options) {
            options = Object.extend({
                method: element.method || 'post',
                parameters: Form.serialize(element)
            }, options || {});
            return new Ajax.Request(element.readAttribute('action') || '', options);
        }
    });

    // Field-level methods must also work on the elements themselves:
    // $('search').activate() (engine/afterAjaxCall.js) and friends.
    Element.addMethods({
        activate: function (element) {
            return Form.Element.activate(element);
        },
        clear: function (element) {
            return Form.Element.clear(element);
        },
        present: function (element) {
            return Form.Element.present(element);
        },
        getValue: function (element) {
            return Form.Element.getValue(element);
        },
        setValue: function (element, value) {
            return Form.Element.setValue(element, value);
        }
    });

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    Ajax.Form = Form;
    global.Ajax = Ajax;
    global.Form = Form;
    global.PeriodicalExecuter = PeriodicalExecuter;
    // Prototype aliases seen in legacy code
    global.Field = Form.Element;

    if (global.console && global.console.info) {
        console.info('[idae-shim] ajax loaded');
    }
})(window);
