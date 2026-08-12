/**
 * shim-form.js — PrototypeJS Form / Field compatibility layer over
 * @medyll/idae-be.
 *
 * Was shim-ajax.js until 2026-08-11. That file carried three things:
 * Ajax.Request / Ajax.Updater / Ajax.PeriodicalUpdater / Ajax.Responders,
 * PeriodicalExecuter, and Form. The first two are gone — the phase 5
 * migrations replaced every request path with native fetch()-based helpers,
 * leaving no `new Ajax.*` outside vendor/ and flotr/'s own bundled Prototype
 * 1.6, and PeriodicalExecuter never had a caller at all. That was ~390 of the
 * file's 650 lines, dead.
 *
 * Form is the opposite and is why this file still exists: the PHP templates
 * reach it from 52 inline onclick/onsubmit attributes. Those are invisible to
 * any JS-level grep or runtime probe, which is exactly how two of its APIs
 * (the Form.serializeElements static and Form#serialize) stayed broken from
 * the Phase 3/4 swap until 2026-08-11. Migrating the remaining Form callers
 * is a template project, not a JavaScript one.
 *
 * Depends on: shim-core.js, shim-class.js, shim-enumerable.js,
 *             shim-element.js
 *
 * @package idae-be-shim
 * @date 2026-08-06
 * Modified: 2026-08-11
 */
(function (global) {
    'use strict';

    /* ------------------------------------------------------------------ *
     * Form.serialize & friends                                            *
     * ------------------------------------------------------------------ */
    var Form = {
        serialize: function (form, options) {
            form = $(form);
            if (!form) return '';
            return Form.serializeElements(Form.getElements(form), options);
        },

        /**
         * Restored 2026-08-11. Prototype has always provided this static —
         * serialize an arbitrary *collection* of fields rather than a whole
         * form — but the shim only ever installed an element-level
         * `serializeElements` (which just returns getElements, see the
         * Element.addMethods block below). The eight template call sites of
         * the shape `Form.serializeElements($(x).select('.selectable'))`, on
         * the produit_tarif_gamme update screens, have therefore thrown
         * "Form.serializeElements is not a function" ever since the Phase 3/4
         * swap. Confirmed missing at runtime, not just by reading.
         *
         * The body is the loop that used to live inline in Form.serialize,
         * lifted out unchanged, so both paths stay identical by construction.
         */
        serializeElements: function (elements, options) {
            options = Object.extend({ submit: true }, options || {});
            var data = $A(elements).inject([], function (results, element) {
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
        /**
         * Restored 2026-08-11 alongside the static above, and broken since the
         * same swap. Prototype puts `serialize` on forms; thirteen template
         * call sites use it — `$(this).serialize()` inside `onsubmit`,
         * `$('che_form').serialize()` on the migration-check screens. This
         * block installed `serializeElements`, `getInputs`, `disable`… but
         * never `serialize`, so all thirteen threw "serialize is not a
         * function". They live in inline `onclick`/`onsubmit` attributes,
         * which is why no runtime probe of the JS ever caught it.
         */
        serialize: function (element, options) {
            // Prototype installs Form#serialize and Field#serialize as two
            // separate typed method sets. This shim's addMethods is untyped —
            // it lands on every element — so dispatch on the tag to keep both
            // meanings: a whole form serializes its fields, a single field
            // serializes just itself. Without the branch, `input.serialize()`
            // would return '' (getElements finds nothing inside an input)
            // where Prototype returns "name=value".
            element = $(element);
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
