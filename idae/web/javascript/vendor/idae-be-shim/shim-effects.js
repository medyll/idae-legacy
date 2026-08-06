/**
 * shim-effects.js — PrototypeJS compatibility layer over @medyll/idae-be
 * Effect.* (Appear/Fade/Opacity/Move/Scale/SlideUp/SlideDown/Morph/
 * Parallel/Highlight) and Draggable(s) — requestAnimationFrame based,
 * API-compatible with Scriptaculous options (duration, from, to,
 * afterFinish, afterUpdate).
 *
 * Depends on: shim-core.js, shim-class.js, shim-enumerable.js,
 *             shim-element.js
 *
 * @package idae-be-shim
 * @date 2026-08-06
 */
(function (global) {
    'use strict';

    function el(element) {
        return typeof element === 'string' ? $(element) : element;
    }

    /* ------------------------------------------------------------------ *
     * Effect.Base — the timing loop                                       *
     * ------------------------------------------------------------------ */
    var Effect = {};

    Effect.Base = function () {};
    Effect.Base.prototype = {
        start: function (options) {
            this.options = Object.extend({
                duration: 1.0,
                from: 0.0,
                to: 1.0,
                fps: 60,
                queue: null
            }, options || {});
            this.element = el(this.options.element || this.element);
            this.currentFrame = 0;
            this.state = 'idle';
            this.startOn = Date.now();
            this.finishOn = this.startOn + this.options.duration * 1000;
            this.from = this.options.from;
            this.to = this.options.to;
            if (this.options.beforeStart) this.options.beforeStart(this);
            this.setup && this.setup();
            this.state = 'running';
            var self = this;
            this.event('beforeStart');
            this.loop();
        },
        loop: function () {
            var self = this;
            var now = Date.now();
            if (now >= this.finishOn) {
                this.render(1.0);
                this.finalize && this.finalize();
                this.state = 'finished';
                this.event('afterFinish');
                if (this.options.afterFinish) this.options.afterFinish(this);
                return;
            }
            var position = (now - this.startOn) / (this.options.duration * 1000);
            var pos = this.from + (this.to - this.from) * position;
            this.render(pos);
            if (this.state === 'running') {
                this._frame = requestAnimationFrame(function () { self.loop(); });
            }
        },
        render: function (position) {
            if (this.state === 'idle') this.state = 'running';
            if (this.options.afterUpdate) this.options.afterUpdate(this);
            this.update && this.update(position);
        },
        cancel: function () {
            if (this._frame) cancelAnimationFrame(this._frame);
            this.state = 'finished';
        },
        event: function (eventName) {
            if (this.options[eventName]) this.options[eventName](this);
        },
        inspect: function () {
            var data = $H();
            for (var property in this) {
                if (typeof this[property] !== 'function') data.set(property, this[property]);
            }
            return '#<Effect:' + data.inspect() + ',options:' + $H(this.options).inspect() + '>';
        }
    };

    function makeEffect(name, methods) {
        var ctor = function (element) {
            this.element = el(element);
            var options = Object.clone(arguments[1] || {});
            options.element = this.element;
            this.start(options);
        };
        ctor.prototype = Object.extend(Object.create(Effect.Base.prototype), methods);
        Effect[name] = ctor;
        return ctor;
    }

    /* ------------------------------------------------------------------ *
     * Effect.Opacity / Appear / Fade                                      *
     * ------------------------------------------------------------------ */
    makeEffect('Opacity', {
        setup: function () {
            this.from = this.options.from !== undefined ? this.options.from : this.element.getOpacity();
            this.to = this.options.to !== undefined ? this.options.to : 1.0;
        },
        update: function (position) {
            this.element.setOpacity(position);
        }
    });

    Effect.Appear = function (element) {
        var options = Object.extend({
            from: (el(element).getStyle('display') === 'none' ? 0.0 : el(element).getOpacity()) || 0.0,
            to: 1.0,
            afterFinish: function (effect) {
                effect.element.setOpacity(1.0);
            }
        }, arguments[1] || {});
        return new Effect.Opacity(element, options);
    };

    Effect.Fade = function (element) {
        var oldOpacity;
        var options = Object.extend({
            from: el(element).getOpacity() || 1.0,
            to: 0.0
        }, arguments[1] || {});
        oldOpacity = options.from;
        var userAfterFinish = options.afterFinish;
        options.afterFinish = function (effect) {
            effect.element.hide().setOpacity(oldOpacity);
            if (userAfterFinish) userAfterFinish(effect);
        };
        return new Effect.Opacity(element, options);
    };

    /* ------------------------------------------------------------------ *
     * Effect.Move                                                         *
     * ------------------------------------------------------------------ */
    makeEffect('Move', {
        setup: function () {
            this.mode = this.options.mode || 'relative';
            this.deltaX = this.options.x || 0;
            this.deltaY = this.options.y || 0;
            this.originalLeft = parseFloat(this.element.getStyle('left') || '0') || 0;
            this.originalTop = parseFloat(this.element.getStyle('top') || '0') || 0;
            if (this.mode === 'absolute') {
                this.deltaX = this.deltaX - this.originalLeft;
                this.deltaY = this.deltaY - this.originalTop;
            }
            this.element.makePositioned();
        },
        update: function (position) {
            this.element.setStyle({
                left: Math.round(this.deltaX * position + this.originalLeft) + 'px',
                top: Math.round(this.deltaY * position + this.originalTop) + 'px'
            });
        }
    });

    /* ------------------------------------------------------------------ *
     * Effect.Scale                                                        *
     * ------------------------------------------------------------------ */
    makeEffect('Scale', {
        setup: function () {
            this.originalWidth = this.element.offsetWidth;
            this.originalHeight = this.element.offsetHeight;
            this.factor = (this.options.scaleTo !== undefined ? this.options.scaleTo : 100.0) / 100.0;
            this.targetWidth = this.originalWidth * this.factor;
            this.targetHeight = this.originalHeight * this.factor;
            this.element.makePositioned && this.element.makePositioned();
        },
        update: function (position) {
            this.element.setStyle({
                width: Math.round(this.originalWidth + (this.targetWidth - this.originalWidth) * position) + 'px',
                height: Math.round(this.originalHeight + (this.targetHeight - this.originalHeight) * position) + 'px'
            });
        }
    });

    /* ------------------------------------------------------------------ *
     * Effect.SlideUp / SlideDown                                          *
     * ------------------------------------------------------------------ */
    function slideSetup(effect, opening) {
        var element = effect.element;
        element.makeClipping && element.makeClipping();
        if (opening) {
            element.show();
            effect.startHeight = 0;
            effect.endHeight = element.getHeight();
        } else {
            effect.startHeight = element.getHeight();
            effect.endHeight = 0;
        }
    }

    Effect.SlideUp = function (element) {
        var userAfterFinish = (arguments[1] || {}).afterFinish;
        var options = Object.extend({
            afterFinish: function (effect) {
                effect.element.hide().undoClipping && effect.element.undoClipping();
                effect.element.setStyle({ height: '' });
                if (userAfterFinish) userAfterFinish(effect);
            }
        }, arguments[1] || {});
        return new (makeEffect('SlideUpImpl', {
            setup: function () { slideSetup(this, false); },
            update: function (position) {
                var h = this.startHeight + (this.endHeight - this.startHeight) * position;
                this.element.setStyle({ height: Math.round(h) + 'px' });
            }
        }))(element, options);
    };

    Effect.SlideDown = function (element) {
        var userAfterFinish = (arguments[1] || {}).afterFinish;
        var options = Object.extend({
            afterFinish: function (effect) {
                effect.element.undoClipping && effect.element.undoClipping();
                effect.element.setStyle({ height: '' });
                if (userAfterFinish) userAfterFinish(effect);
            }
        }, arguments[1] || {});
        return new (makeEffect('SlideDownImpl', {
            setup: function () { slideSetup(this, true); },
            update: function (position) {
                var h = this.startHeight + (this.endHeight - this.startHeight) * position;
                this.element.setStyle({ height: Math.round(h) + 'px' });
            }
        }))(element, options);
    };

    /* ------------------------------------------------------------------ *
     * Effect.Morph — animate numeric style props to target values         *
     * ------------------------------------------------------------------ */
    makeEffect('Morph', {
        setup: function () {
            var style = this.options.style || {};
            if (Object.isString(style)) {
                this.targetStyle = style.toQueryParams ? {} : {};
                var rules = style.split(';');
                var parsed = {};
                rules.each(function (rule) {
                    var pair = rule.split(':');
                    if (pair.length === 2) parsed[pair[0].strip()] = pair[1].strip();
                });
                this.targetStyle = parsed;
            } else {
                this.targetStyle = style;
            }
            this.startValues = {};
            this.endValues = {};
            var self = this;
            $H(this.targetStyle).each(function (pair) {
                var prop = pair.key.camelize();
                var end = parseFloat(pair.value);
                if (isNaN(end)) return; // non-numeric: applied at the end
                self.startValues[prop] = parseFloat(self.element.getStyle(prop)) || 0;
                self.endValues[prop] = end;
            });
        },
        update: function (position) {
            var styles = {};
            var self = this;
            $H(this.startValues).each(function (pair) {
                var start = pair.value, end = self.endValues[pair.key];
                styles[pair.key] = (start + (end - start) * position) + 'px';
            });
            this.element.setStyle(styles);
        },
        finalize: function () {
            // Non-numeric declarations land in one shot at the end.
            var styles = {};
            var self = this;
            $H(this.targetStyle).each(function (pair) {
                if (!(pair.key.camelize() in self.endValues)) styles[pair.key] = pair.value;
            });
            if ($H(styles).keys().length) this.element.setStyle(styles);
        }
    });

    /* ------------------------------------------------------------------ *
     * Effect.Highlight                                                    *
     * ------------------------------------------------------------------ */
    makeEffect('Highlight', {
        setup: function () {
            this.originalColor = this.element.getStyle('background-color') || 'transparent';
            this.highlightColor = this.options.startcolor || '#ffff99';
            this.keepOriginal = this.options.keepBackgroundImage;
        },
        update: function (position) {
            this.element.setStyle({ backgroundColor: position < 1 ? this.highlightColor : this.originalColor });
        },
        finalize: function () {
            this.element.setStyle({ backgroundColor: this.originalColor });
        }
    });

    /* ------------------------------------------------------------------ *
     * Effect.Parallel                                                     *
     * ------------------------------------------------------------------ */
    Effect.Parallel = function (effects) {
        this.effects = effects || [];
        var options = arguments[1] || {};
        var userAfterFinish = options.afterFinish;
        var remaining = this.effects.length;
        this.effects.forEach(function (effect) {
            var oldFinish = effect.options.afterFinish;
            effect.options.afterFinish = function (fx) {
                if (oldFinish) oldFinish(fx);
                if (--remaining === 0 && userAfterFinish) userAfterFinish(fx);
            };
        });
        return this;
    };

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
    global.Effect = Effect;
    global.Draggable = Draggable;
    global.Draggables = Draggables;

    if (global.console && global.console.info) {
        console.info('[idae-shim] effects loaded');
    }

    // Last shim file: if the warn flag was set before load, arm it now.
    if (global.IDAE_SHIM_WARN && global.__idaeShimInstallWarn) {
        global.__idaeShimInstallWarn();
    }
})(window);
