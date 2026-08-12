/**
 * shim-draggable.js — PrototypeJS/Scriptaculous compatibility layer over
 * @medyll/idae-be: Draggable / Draggables.
 *
 * Split out of shim-effects.js on 2026-08-09 when the Effect.* family lost its
 * last caller and was deleted outright — see BE_PLAN.md.
 *
 * Modified: 2026-08-11 — rewritten to Scriptaculous' actual Draggable
 * lifecycle. The 2026-08-09 version implemented a *simplified* drag
 * (mousedown -> startDrag -> updateDrag -> finishDrag, moving the element
 * itself) that happened to satisfy resizeGui.js. It did not satisfy
 * librairie/cropper.js, whose `CropDraggable = Class.create(Draggable, {...})`
 * overrides `initialize` and `draw` and, inside its own initialize, calls
 * `this.currentDelta()` and binds `this.initDrag` — two methods the simplified
 * version never had. `new CropDraggable(...)` therefore threw
 * "this.currentDelta is not a function" on line 151 of cropper.js, during
 * Cropper.Img's own setup and before its setParams(), which means the whole
 * image cropper ("Retailler cette image", mdl/app/app_img/app_img_upload.php)
 * has been dead since the Phase 3/4 swap. Confirmed by constructing a
 * CropDraggable on the live page, not by reading.
 *
 * So the contract implemented here is Scriptaculous', not a convenient subset:
 *
 *   mousedown on handle -> Draggable#initDrag
 *     -> computes this.offset from the pointer and the element's cumulative
 *        offset, then Draggables.activate(this)
 *   document mousemove   -> Draggables.updateDrag
 *     -> first move calls Draggable#startDrag, then Draggable#updateDrag
 *     -> which calls this.draw(pointer)   <- the override point cropper uses
 *   document mouseup     -> Draggables.endDrag -> Draggable#finishDrag
 *
 * The option callbacks the older version supported (starteffect, endeffect,
 * change, onStart/onDrag/onEnd) are kept and fire at the same points, so this
 * is a superset of what was here before.
 *
 * cropper.js is now the only caller. librairie/resizeGui.js was the other and
 * was deleted on 2026-08-11: every `new resizeGui(...)` in the tree was
 * commented out, and the commented calls named `Resizeable`, a class that has
 * never existed anywhere in the codebase.
 *
 * Depends on: shim-core.js, shim-class.js, shim-enumerable.js,
 *             shim-element.js, shim-event.js
 *
 * @package idae-be-shim
 * @date 2026-08-09
 */
(function (global) {
    'use strict';

    function el(element) {
        return typeof element === 'string' ? $(element) : element;
    }

    /* ------------------------------------------------------------------ *
     * Draggables — registry plus the document-level drag pump             *
     * ------------------------------------------------------------------ */
    var Draggables = {
        drags: [],
        observers: [],
        activeDraggable: null,
        _pumpInstalled: false,

        register: function (draggable) {
            if (this.drags.indexOf(draggable) !== -1) return;
            this.drags.push(draggable);
            this._installPump();
        },
        unregister: function (draggable) {
            this.drags = this.drags.filter(function (d) { return d !== draggable; });
        },
        addObserver: function (observer) {
            this.observers.push(observer);
        },
        removeObserver: function (observer) {
            this.observers = this.observers.filter(function (o) { return o !== observer; });
        },
        notify: function (eventName, draggable, event) {
            this.observers.forEach(function (o) {
                if (o[eventName]) o[eventName](eventName, draggable, event);
            });
        },

        /**
         * One pair of document listeners for every draggable, installed on
         * first register — Scriptaculous does the same. Per-instance document
         * listeners would leak: nothing calls destroy() on a CropDraggable
         * when its Cropper is torn down.
         */
        _installPump: function () {
            if (this._pumpInstalled) return;
            this._pumpInstalled = true;
            var self = this;
            document.addEventListener('mousemove', function (event) { self.updateDrag(event); }, false);
            document.addEventListener('mouseup', function (event) { self.endDrag(event); }, false);
        },

        activate: function (draggable) {
            this.activeDraggable = draggable;
        },
        deactivate: function () {
            this.activeDraggable = null;
        },

        updateDrag: function (event) {
            var draggable = this.activeDraggable;
            if (!draggable) return;
            var pointer = [Event.pointerX(event), Event.pointerY(event)];
            // Same-position mousemove events are common; skip them so a click
            // without movement never counts as a drag.
            if (this._lastPointer &&
                this._lastPointer[0] === pointer[0] && this._lastPointer[1] === pointer[1]) return;
            this._lastPointer = pointer;

            if (!draggable.dragging) draggable.startDrag(event);
            draggable.updateDrag(event, pointer);
        },

        endDrag: function (event) {
            var draggable = this.activeDraggable;
            this._lastPointer = null;
            if (!draggable) return;
            this.deactivate();
            if (!draggable.dragging) return;
            draggable.finishDrag(event, true);
        }
    };

    /* ------------------------------------------------------------------ *
     * Draggable                                                           *
     * ------------------------------------------------------------------ */
    var Draggable = function (element) {
        this.initialize(element, arguments[1]);
    };

    Object.extend(Draggable.prototype, {
        /**
         * Subclasses override this wholesale — CropDraggable does — so it must
         * leave the instance usable through the prototype methods alone.
         */
        initialize: function (element, options) {
            this.options = Object.extend({
                handle: false,
                revert: false,
                zindex: 1000,
                starteffect: null,
                endeffect: null,
                change: null
            }, options || {});

            this.element = el(element);
            this.handle = this.options.handle ? el(this.options.handle) : this.element;
            this.delta = this.currentDelta();
            this.dragging = false;

            if (this.element.makePositioned) this.element.makePositioned();

            this.eventMouseDown = this.initDrag.bindAsEventListener(this);
            Event.observe(this.handle, 'mousedown', this.eventMouseDown);
            Draggables.register(this);
        },

        /** [left, top] currently on the element's own style, as numbers. */
        currentDelta: function () {
            return [
                parseInt(Element.getStyle(this.element, 'left') || '0', 10) || 0,
                parseInt(Element.getStyle(this.element, 'top') || '0', 10) || 0
            ];
        },

        /**
         * mousedown. Records where inside the element the pointer grabbed it,
         * which is what draw() subtracts later, then hands over to the pump.
         */
        initDrag: function (event) {
            if (!Event.isLeftClick(event)) return;

            // Never start a drag from a form control: the user is interacting
            // with it, not moving its container.
            var src = Event.element(event);
            var tag = src && src.tagName ? src.tagName.toUpperCase() : '';
            if (tag === 'INPUT' || tag === 'SELECT' || tag === 'OPTION' ||
                tag === 'BUTTON' || tag === 'TEXTAREA') return;

            var pointer = [Event.pointerX(event), Event.pointerY(event)];
            var pos = Element.cumulativeOffset(this.element);
            this.offset = [pointer[0] - pos[0], pointer[1] - pos[1]];

            Draggables.activate(this);
            Event.stop(event);
        },

        startDrag: function (event) {
            this.dragging = true;
            this.delta = this.currentDelta();

            var options = this.options || {};
            if (options.zindex) {
                this.originalZ = parseInt(Element.getStyle(this.element, 'z-index') || '0', 10);
                this.element.style.zIndex = options.zindex;
            }
            if (options.onStart) options.onStart(this, event);
            if (options.starteffect) options.starteffect(this.element);
            Draggables.notify('onStart', this, event);
        },

        updateDrag: function (event, pointer) {
            this.draw(pointer);

            var options = this.options || {};
            if (options.change) options.change(this, event);
            if (options.onDrag) options.onDrag(this, event);
            Draggables.notify('onDrag', this, event);

            Event.stop(event);
        },

        /**
         * Move the element so the grabbed point follows the pointer.
         * CropDraggable replaces this entirely and forwards to its own
         * drawMethod instead of touching the element — which is the whole
         * reason it subclasses rather than configures.
         */
        draw: function (point) {
            var pos = Element.cumulativeOffset(this.element);
            var d = this.currentDelta();
            pos[0] -= d[0];
            pos[1] -= d[1];

            var style = this.element.style;
            style.left = (point[0] - pos[0] - this.offset[0]) + 'px';
            style.top = (point[1] - pos[1] - this.offset[1]) + 'px';
        },

        finishDrag: function (event, success) {
            this.dragging = false;

            var options = this.options || {};
            if (options.zindex && this.originalZ !== undefined) {
                this.element.style.zIndex = this.originalZ;
            }
            if (options.revert) {
                this.element.style.left = this.delta[0] + 'px';
                this.element.style.top = this.delta[1] + 'px';
            }
            if (options.onEnd) options.onEnd(this, event);
            if (options.endeffect) options.endeffect(this.element);
            Draggables.notify('onEnd', this, event);
        },

        destroy: function () {
            if (this.eventMouseDown) {
                Event.stopObserving(this.handle, 'mousedown', this.eventMouseDown);
            }
            Draggables.unregister(this);
        }
    });

    Draggable._dragging = {};

    /* ------------------------------------------------------------------ *
     * Exports                                                             *
     * ------------------------------------------------------------------ */
    global.Draggable = Draggable;
    global.Draggables = Draggables;

    if (global.console && global.console.info) {
        console.info('[idae-shim] draggable loaded');
    }

    // Last shim file (require_hell in main_bag.js): if the warn flag was set
    // before load, arm it now.
    if (global.IDAE_SHIM_WARN && global.__idaeShimInstallWarn) {
        global.__idaeShimInstallWarn();
    }
})(window);
