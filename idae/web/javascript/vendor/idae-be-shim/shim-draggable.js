/**
 * shim-draggable.js — PrototypeJS/Scriptaculous compatibility layer over
 * @medyll/idae-be: Draggable / Draggables, pointer-based, minimal
 * Scriptaculous API (handle, revert, zindex, starteffect/endeffect,
 * onStart/onDrag/onEnd, change).
 *
 * Split out of shim-effects.js on 2026-08-09 when the Effect.* family
 * (Fade/Appear/Opacity/Move/SlideUp/SlideDown/Parallel and the
 * requestAnimationFrame timing loop they shared) lost its last caller and
 * was deleted outright — see BE_PLAN.md. Draggable itself still has two live
 * callers (librairie/cropper.js's `CropDraggable`, which subclasses it via
 * `Class.create(Draggable, {...})` and overrides several of its methods,
 * and librairie/resizeGui.js), so it could not go with the rest: migrating
 * it means either reimplementing Draggable's exact method surface (so the
 * subclass's overrides keep working) or rewriting cropper.js's drag-select
 * behavior natively and blind — both riskier than carving this out.
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
     * Draggable / Draggables — pointer-based, minimal Scriptaculous API   *
     * ------------------------------------------------------------------ */
    var Draggables = {
        drags: [],
        observers: [],
        register: function (draggable) {
            if (this.drags.include(draggable)) return;
            this.drags.push(draggable);
        },
        unregister: function (draggable) {
            this.drags = this.drags.reject(function (d) { return d === draggable; });
        },
        addObserver: function (observer) {
            this.observers.push(observer);
        },
        removeObserver: function (observer) {
            this.observers = this.observers.without(observer);
        },
        notify: function (eventName, draggable, event) {
            this.observers.each(function (o) {
                if (o[eventName]) o[eventName](eventName, draggable, event);
            });
        }
    };

    var Draggable = function (element) {
        var options = Object.extend({
            handle: false,
            revert: false,
            zindex: 1000,
            scroll: false,
            starteffect: null,
            endeffect: null,
            change: null
        }, arguments[1] || {});

        this.element = el(element);
        this.handle = options.handle ? el(options.handle) : this.element;
        this.options = options;
        this.dragging = false;

        this.element.makePositioned && this.element.makePositioned();

        var self = this;
        this._downHandler = function (event) { self.startDrag(event); };
        this._moveHandler = function (event) { self.updateDrag(event); };
        this._upHandler = function (event) { self.finishDrag(event); };
        Event.observe(this.handle, 'mousedown', this._downHandler);
        Draggables.register(this);
    };
    Object.extend(Draggable.prototype, {
        startDrag: function (event) {
            if (event.button !== 0) return;
            this.dragging = true;
            this.originalLeft = parseFloat(this.element.getStyle('left') || '0') || 0;
            this.originalTop = parseFloat(this.element.getStyle('top') || '0') || 0;
            var pointer = Event.pointer(event);
            this.startX = pointer.x;
            this.startY = pointer.y;
            this.originalZ = this.element.getStyle('z-index');
            this.element.setStyle({ zIndex: this.options.zindex });

            Event.observe(document, 'mousemove', this._moveHandler);
            Event.observe(document, 'mouseup', this._upHandler);

            if (this.options.onStart) this.options.onStart(this, event);
            if (this.options.starteffect) this.options.starteffect(this.element);
            Draggables.notify('onStart', this, event);
            Event.stop(event);
        },
        updateDrag: function (event) {
            if (!this.dragging) return;
            var pointer = Event.pointer(event);
            var dx = pointer.x - this.startX;
            var dy = pointer.y - this.startY;
            this.element.setStyle({
                left: (this.originalLeft + dx) + 'px',
                top: (this.originalTop + dy) + 'px'
            });
            if (this.options.change) this.options.change(this, event);
            if (this.options.onDrag) this.options.onDrag(this, event);
            Draggables.notify('onDrag', this, event);
            Event.stop(event);
        },
        finishDrag: function (event) {
            if (!this.dragging) return;
            this.dragging = false;
            Event.stopObserving(document, 'mousemove', this._moveHandler);
            Event.stopObserving(document, 'mouseup', this._upHandler);

            this.element.setStyle({ zIndex: this.originalZ });
            if (this.options.revert) {
                this.element.setStyle({ left: this.originalLeft + 'px', top: this.originalTop + 'px' });
            }
            if (this.options.onEnd) this.options.onEnd(this, event);
            if (this.options.endeffect) this.options.endeffect(this.element);
            Draggables.notify('onEnd', this, event);
        },
        destroy: function () {
            Event.stopObserving(this.handle, 'mousedown', this._downHandler);
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
