/**
 * shim-form.js — PrototypeJS Form / Field compatibility layer.
 *
 * Was shim-ajax.js until 2026-08-11. That file carried Ajax.Request /
 * Ajax.Updater / Ajax.PeriodicalUpdater / Ajax.Responders, PeriodicalExecuter,
 * and Form. The first two are gone — the phase 5 migrations replaced every
 * request path with native fetch()-based helpers and PeriodicalExecuter never
 * had a caller at all.
 *
 * Form is the opposite and is why this file exists: the PHP templates reach it
 * from 52 places, nearly all inside inline onclick/onsubmit attributes. Those
 * are invisible to any JS-level grep or runtime probe, which is exactly how
 * two of its APIs (the Form.serializeElements static and Form#serialize)
 * stayed broken from the Phase 3/4 swap until 2026-08-11. Migrating the
 * remaining Form callers is a template project, not a JavaScript one.
 *
 * Modified: 2026-08-11 — made standalone. This file used to depend on all four
 * other shims: $ / $A / $F / $H / String.interpret (shim-core), Object.extend
 * (shim-class), Element.addMethods (shim-element), and .inject / .invoke /
 * .sortBy / .include / .first / .toQueryString (shim-enumerable). Since the
 * cropper migration nothing in the application's JavaScript calls
 * shim-element or shim-enumerable any more — this file was their last
 * consumer, keeping 1,499 lines alive on its own behalf. Every borrowed
 * helper is now a local `fm_` one, so those two can be deleted once the
 * surface specs that still assert their methods are audited.
 *
 * No load-order dependency on any other shim remains.
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    /* ------------------------------------------------------------------ *
     * Local helpers — formerly borrowed from the other shims              *
     * ------------------------------------------------------------------ */

    /** Prototype's `$`: an id resolves to its element, an element passes through. */
    function fm_el(ref) {
        return typeof ref === 'string' ? document.getElementById(ref) : ref;
    }

    /** Array.from for anything array-like, without depending on shim-core's $A. */
    function fm_toArray(collection) {
        return Array.prototype.slice.call(collection);
    }

    /** Prototype's String.interpret: null and undefined become '', not "null". */
    function fm_interpret(value) {
        return value == null ? '' : String(value);
    }

    /**
     * Prototype's `$F`: the *value* of a form control.
     *
     * Checkbox and radio yield their value only when checked, and null
     * otherwise — that null is what makes serializeElements skip them.
     * A multiple-select yields an array, one entry per selected option.
     */
    function fm_value(element) {
        element = fm_el(element);
        if (!element) return null;
        if (element.type === 'checkbox' || element.type === 'radio') {
            return element.checked ? element.value : null;
        }
        if (element.tagName && element.tagName.toLowerCase() === 'select' && element.multiple) {
            return fm_toArray(element.options)
                .filter(function (o) { return o.selected; })
                .map(function (o) { return o.value; });
        }
        return element.value;
    }

    /**
     * Prototype's Element.addMethods semantics: an added method receives the
     * element as its FIRST argument, and is reachable as `node.name(...)`.
     * A local copy so this file does not need shim-element loaded.
     */
    function fm_addMethods(methods) {
        for (var name in methods) {
            if (!Object.prototype.hasOwnProperty.call(methods, name)) continue;
            (function (method) {
                global.HTMLElement.prototype[name] = function () {
                    var args = [this];
                    for (var i = 0; i < arguments.length; i++) args.push(arguments[i]);
                    return method.apply(null, args);
                };
            }(methods[name]));
        }
    }

    function fm_extend(destination, source) {
        for (var property in source) destination[property] = source[property];
        return destination;
    }

    /* ------------------------------------------------------------------ *
     * Form.serialize & friends                                            *
     * ------------------------------------------------------------------ */
    var Form = {
        serialize: function (form, options) {
            form = fm_el(form);
            if (!form) return '';
            return Form.serializeElements(Form.getElements(form), options);
        },

        /**
         * Restored 2026-08-11. Prototype has always provided this static —
         * serialize an arbitrary *collection* of fields rather than a whole
         * form — but the shim only ever installed an element-level
         * `serializeElements` (which just returns getElements, see the
         * fm_addMethods block below). The eight template call sites of the
         * shape `Form.serializeElements($(x).select('.selectable'))`, on the
         * produit_tarif_gamme update screens, therefore threw
         * "Form.serializeElements is not a function" from the Phase 3/4 swap
         * onwards. Confirmed missing at runtime, not just by reading.
         *
         * The body is the loop that used to live inline in Form.serialize,
         * lifted out unchanged, so both paths stay identical by construction.
         */
        serializeElements: function (elements, options) {
            options = fm_extend({ submit: true }, options || {});
            var results = [];
            fm_toArray(elements).forEach(function (element) {
                if (element.disabled || !element.name) return;
                var key = element.name;
                var value = fm_value(element);
                if (value == null || element.type === 'file') return;
                if (element.type === 'submit') return;
                if (Array.isArray(value)) {
                    value.forEach(function (v) {
                        results.push(encodeURIComponent(key) + '=' + encodeURIComponent(fm_interpret(v)));
                    });
                } else {
                    results.push(encodeURIComponent(key) + '=' + encodeURIComponent(fm_interpret(value)));
                }
            });
            return results.join('&');
        },

        getElements: function (form) {
            form = fm_el(form);
            if (!form) return [];
            return fm_toArray(form.querySelectorAll('input, select, textarea, button'))
                .filter(function (el) { return el.type !== 'image'; });
        },

        getInputs: function (form, typeName, name) {
            form = fm_el(form);
            var inputs = form.getElementsByTagName('input');
            if (!typeName && !name) return fm_toArray(inputs);
            var matchingInputs = [];
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                if ((typeName && input.type !== typeName) || (name && input.name !== name)) continue;
                matchingInputs.push(input);
            }
            return matchingInputs;
        },

        disable: function (form) {
            form = fm_el(form);
            Form.getElements(form).forEach(function (el) { el.disabled = true; });
            return form;
        },

        enable: function (form) {
            form = fm_el(form);
            Form.getElements(form).forEach(function (el) { el.disabled = false; });
            return form;
        },

        findFirstElement: function (form) {
            form = fm_el(form);
            var skip = ['hidden', 'submit', 'image', 'button'];
            var elements = Form.getElements(form).filter(function (el) {
                return skip.indexOf(el.type) === -1 && !el.disabled;
            });
            // Prototype's sortBy is a Schwartzian transform; a plain sort on a
            // numeric key is equivalent here and keeps ties in document order,
            // which sortBy also did.
            var byIndex = elements
                .filter(function (el) { return el.hasAttribute('tabIndex') && el.tabIndex >= 0; })
                .sort(function (a, b) { return a.tabIndex - b.tabIndex; });
            return byIndex.length ? byIndex[0] : elements[0];
        },

        focusFirstElement: function (form) {
            form = fm_el(form);
            var el = Form.findFirstElement(form);
            if (el) el.focus();
            return form;
        },

        reset: function (form) {
            fm_el(form).reset();
            return form;
        },

        Element: {
            focus: function (element) { fm_el(element).focus(); return fm_el(element); },
            select: function (element) { fm_el(element).select(); return fm_el(element); },
            getValue: function (element) { return fm_value(element); },
            setValue: function (element, value) {
                element = fm_el(element);
                if (element.type === 'checkbox' || element.type === 'radio') {
                    element.checked = !!value;
                } else {
                    element.value = value;
                }
                return element;
            },
            serialize: function (element) {
                element = fm_el(element);
                if (!element.disabled && element.name) {
                    var value = fm_value(element);
                    if (value !== undefined && value !== null) {
                        // Was $H({name: value}).toQueryString(); one pair needs
                        // no Hash.
                        return encodeURIComponent(element.name) + '=' +
                            encodeURIComponent(fm_interpret(value));
                    }
                }
                return '';
            },
            clear: function (element) {
                fm_el(element).value = '';
                return fm_el(element);
            },
            present: function (element) {
                return fm_el(element).value !== '';
            },
            activate: function (element) {
                element = fm_el(element);
                try {
                    element.focus();
                    if (element.select && (element.type !== 'hidden' || element.type === 'text')) {
                        element.select();
                    }
                } catch (e) {}
                return element;
            },
            disable: function (element) {
                element = fm_el(element);
                element.disabled = true;
                return element;
            },
            enable: function (element) {
                element = fm_el(element);
                element.disabled = false;
                return element;
            }
        }
    };
    Form.Serializers = Form.Element;

    // Form methods as element methods. Methodized: these receive the element
    // as their first argument.
    fm_addMethods({
        /**
         * Restored 2026-08-11 alongside the static above, and broken since the
         * same swap. Prototype puts `serialize` on forms; thirteen template
         * call sites use it — `$(this).serialize()` inside `onsubmit`,
         * `$('che_form').serialize()` on the migration-check screens. The
         * original block installed `serializeElements`, `getInputs`,
         * `disable`… but never `serialize`, so all thirteen threw "serialize
         * is not a function". They live in inline onclick/onsubmit attributes,
         * which is why no runtime probe of the JS ever caught it.
         */
        serialize: function (element, options) {
            // Prototype installs Form#serialize and Field#serialize as two
            // separate typed method sets. This installer is untyped — it lands
            // on every element — so dispatch on the tag to keep both meanings:
            // a whole form serializes its fields, a single field serializes
            // just itself. Without the branch, `input.serialize()` would
            // return '' (getElements finds nothing inside an input) where
            // Prototype returns "name=value".
            element = fm_el(element);
            if (element.tagName && element.tagName.toLowerCase() === 'form') {
                return Form.serialize(element, options);
            }
            return Form.Element.serialize(element);
        },
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
        }
        // `request` dropped 2026-08-11 with the Ajax namespace: its body was
        // `new Ajax.Request(form.action, {parameters: Form.serialize(form)})`
        // and it had no callers, in JavaScript or in the templates.
    });

    // Field-level methods must also work on the elements themselves:
    // $('search').activate() (engine/afterAjaxCall.js) and friends.
    fm_addMethods({
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
    global.Form = Form;
    // Prototype aliases seen in legacy code
    global.Field = Form.Element;

    if (global.console && global.console.info) {
        console.info('[idae-shim] form loaded');
    }

    // Last shim file in main_bag.js's require_hell: if the warn flag was set
    // before load, arm it now. Moved here 2026-08-11 from shim-draggable.js,
    // which used to be last and has been deleted — cropper.js was its only
    // caller and now carries its own Draggable. Without this the flag would
    // silently never arm and shim-warn.spec.ts would pass for the wrong
    // reason.
    if (global.IDAE_SHIM_WARN && global.__idaeShimInstallWarn) {
        global.__idaeShimInstallWarn();
    }
})(window);
