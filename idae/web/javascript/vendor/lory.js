!function (e, t) {
	if ("object" == typeof exports && "object" == typeof module)module.exports = t(); else if ("function" == typeof define && define.amd)define([], t); else {
		var n = t();
		for (var i in n)("object" == typeof exports ? exports : e)[i] = n[i]
	}
}(this, function () {
	return function (e) {
		function t(i) {
			if (n[i])return n[i].exports;
			var o = n[i] = {exports: {}, id: i, loaded: !1};
			return e[i].call(o.exports, o, o.exports, t), o.loaded = !0, o.exports
		}

		var n = {};
		return t.m = e, t.c = n, t.p = "", t(0)
	}([function (e, t, n) {
		e.exports = n(1)
	}, function (e, t, n) {
		"use strict";
		function i(e) {
			return e && e.__esModule ? e : {"default": e}
		}

		function o(e, t) {
			function n(e, t) {
				var n = z, i = n.classNameActiveSlide;
				e.forEach(function (e, t) {
					e.classList.contains(i) && e.classList.remove(i)
				}), e[t].classList.add(i)
			}

			function i(e) {
				var t = z, n = t.infinite, i = e.slice(0, n), o = e.slice(e.length - n, e.length);
				return i.forEach(function (e) {
					var t = e.cloneNode(!0);
					B.appendChild(t)
				}), o.reverse().forEach(function (e) {
					var t = e.cloneNode(!0);
					B.insertBefore(t, B.firstChild)
				}), B.addEventListener(O.transitionEnd, y), f.call(B.children)
			}

			function o(t, n, i) {
				(0, l["default"])(e, t + ".lory." + n, i)
			}

			function a(e, t, n) {
				var i = B && B.style;
				i && (i[O.transition + "TimingFunction"] = n, i[O.transition + "Duration"] = t + "ms", O.hasTranslate3d ? i[O.transform] = "translate3d(" + e + "px, 0, 0)" : i[O.transform] = "translate(" + e + "px, 0)")
			}

			function d(e, t) {
				var i = z, r = i.slideSpeed, s = i.slidesToScroll, d = i.infinite, l = i.rewind, c = i.rewindSpeed, u = i.ease, v = i.classNameActiveSlide, m = r, p = t ? P + 1 : P - 1, h = Math.round(M - S);
				o("before", "slide", {index: P, nextSlide: p}), "number" != typeof e && (e = t ? P + s : P - s), e = Math.min(Math.max(e, 0), _.length - 1), d && void 0 === t && (e += d);
				var E = Math.min(Math.max(_[e].offsetLeft * -1, h * -1), 0);
				l && Math.abs(N.x) === h && t && (E = 0, e = 0, m = c), a(E, m, u), N.x = E, _[e].offsetLeft <= h && (P = e), !d || e !== _.length - d && 0 !== e || (t && (P = d), t || (P = _.length - 2 * d), N.x = _[P].offsetLeft * -1, A = function () {
					a(_[P].offsetLeft * -1, 0, void 0)
				}), v && n(f.call(_), P), o("after", "slide", {currentSlide: P})
			}

			function c() {
				o("before", "init"), O = (0, s["default"])(), z = r({}, u["default"], t);
				var a = z, d = a.classNameFrame, l = a.classNameSlideContainer, c = a.classNamePrevCtrl, m = a.classNameNextCtrl, p = a.enableMouseEvents, b = a.classNameActiveSlide;
				j = e.getElementsByClassName(d)[0], B = j.getElementsByClassName(l)[0], k = e.getElementsByClassName(c)[0], T = e.getElementsByClassName(m)[0], N = {
					x: B.offsetLeft,
					y: B.offsetTop
				}, _ = z.infinite ? i(f.call(B.children)) : f.call(B.children), v(), b && n(_, P), k && T && (k.addEventListener("click", h), T.addEventListener("click", E)), j.addEventListener("touchstart", x), p && (j.addEventListener("mousedown", x), j.addEventListener("click", g)), z.window.addEventListener("resize", C), o("after", "init")
			}

			function v() {
				var e = z, t = e.infinite, i = e.ease, o = e.rewindSpeed, r = e.rewindOnResize, s = e.classNameActiveSlide;
				M = B.getBoundingClientRect().width || B.offsetWidth, S = j.getBoundingClientRect().width || j.offsetWidth, S === M && (M = _.reduce(function (e, t) {
					return e + t.getBoundingClientRect().width || t.offsetWidth
				}, 0)), r ? P = 0 : (i = null, o = 0), t ? (a(_[P + t].offsetLeft * -1, 0, null), P += t, N.x = _[P].offsetLeft * -1) : (a(_[P].offsetLeft * -1, o, i), N.x = _[P].offsetLeft * -1), s && n(f.call(_), P)
			}

			function m(e) {
				d(e)
			}

			function p() {
				return P - z.infinite || 0
			}

			function h() {
				d(!1, !1)
			}

			function E() {
				d(!1, !0)
			}

			function b() {
				o("before", "destroy"), j.removeEventListener(O.transitionEnd, y), j.removeEventListener("touchstart", x), j.removeEventListener("touchmove", L), j.removeEventListener("touchend", w), j.removeEventListener("mousemove", L), j.removeEventListener("mousedown", x), j.removeEventListener("mouseup", w), j.removeEventListener("mouseleave", w), j.removeEventListener("click", g), z.window.removeEventListener("resize", C), k && k.removeEventListener("click", h), T && T.removeEventListener("click", E), z.infinite && Array.apply(null, Array(z.infinite)).forEach(function () {
					B.removeChild(B.firstChild), B.removeChild(B.lastChild)
				}), o("after", "destroy")
			}

			function y() {
				A && (A(), A = void 0)
			}

			function x(e) {
				var t = z, n = t.enableMouseEvents, i = e.touches ? e.touches[0] : e;
				n && (j.addEventListener("mousemove", L), j.addEventListener("mouseup", w), j.addEventListener("mouseleave", w)), j.addEventListener("touchmove", L), j.addEventListener("touchend", w);
				var r = i.pageX, a = i.pageY;
				D = {x: r, y: a, time: Date.now()}, F = void 0, R = {}, o("on", "touchstart", {event: e})
			}

			function L(e) {
				var t = e.touches ? e.touches[0] : e, n = t.pageX, i = t.pageY;
				R = {x: n - D.x, y: i - D.y}, "undefined" == typeof F && (F = !!(F || Math.abs(R.x) < Math.abs(R.y))), !F && D && (e.preventDefault(), a(N.x + R.x, 0, null)), o("on", "touchmove", {event: e})
			}

			function w(e) {
				var t = D ? Date.now() - D.time : void 0, n = Number(t) < 300 && Math.abs(R.x) > 25 || Math.abs(R.x) > S / 3, i = !P && R.x > 0 || P === _.length - 1 && R.x < 0, r = R.x < 0;
				F || (n && !i ? d(!1, r) : a(N.x, z.snapBackSpeed)), D = void 0, j.removeEventListener("touchmove", L), j.removeEventListener("touchend", w), j.removeEventListener("mousemove", L), j.removeEventListener("mouseup", w), j.removeEventListener("mouseleave", w), o("on", "touchend", {event: e})
			}

			function g(e) {
				R.x && e.preventDefault()
			}

			function C(e) {
				v(), o("on", "resize", {event: e})
			}

			var N = void 0, M = void 0, S = void 0, _ = void 0, j = void 0, B = void 0, k = void 0, T = void 0, O = void 0, A = void 0, P = 0, z = {};
			"undefined" != typeof jQuery && e instanceof jQuery && (e = e[0]);
			var D = void 0, R = void 0, F = void 0;
			return c(), {setup: c, reset: v, slideTo: m, returnIndex: p, prev: h, next: E, destroy: b}
		}

		Object.defineProperty(t, "__esModule", {value: !0});
		var r = Object.assign || function (e) {
				for (var t = 1; t < arguments.length; t++) {
					var n = arguments[t];
					for (var i in n)Object.prototype.hasOwnProperty.call(n, i) && (e[i] = n[i])
				}
				return e
			};
		t.lory = o;
		var a = n(2), s = i(a), d = n(3), l = i(d), c = n(5), u = i(c), f = Array.prototype.slice
	}, function (e, t) {
		(function (e) {
			"use strict";
			function n() {
				var t = void 0, n = void 0, i = void 0, o = void 0;
				return function () {
					var r = document.createElement("_"), a = r.style, s = void 0;
					"" === a[s = "webkitTransition"] && (i = "webkitTransitionEnd", n = s), "" === a[s = "transition"] && (i = "transitionend", n = s), "" === a[s = "webkitTransform"] && (t = s), "" === a[s = "msTransform"] && (t = s), "" === a[s = "transform"] && (t = s), document.body.insertBefore(r, null), a[t] = "translate3d(0, 0, 0)", o = !!e.getComputedStyle(r).getPropertyValue(t), document.body.removeChild(r)
				}(), {transform: t, transition: n, transitionEnd: i, hasTranslate3d: o}
			}

			Object.defineProperty(t, "__esModule", {value: !0}), t["default"] = n
		}).call(t, function () {
			return this
		}())
	}, function (e, t, n) {
		"use strict";
		function i(e) {
			return e && e.__esModule ? e : {"default": e}
		}

		function o(e, t, n) {
			var i = new a["default"](t, {bubbles: !0, cancelable: !0, detail: n});
			e.dispatchEvent(i)
		}

		Object.defineProperty(t, "__esModule", {value: !0}), t["default"] = o;
		var r = n(4), a = i(r)
	}, function (e, t) {
		(function (t) {
			function n() {
				try {
					var e = new i("cat", {detail: {foo: "bar"}});
					return "cat" === e.type && "bar" === e.detail.foo
				} catch (t) {
				}
				return !1
			}

			var i = t.CustomEvent;
			e.exports = n() ? i : "function" == typeof document.createEvent ? function (e, t) {
				var n = document.createEvent("CustomEvent");
				return t ? n.initCustomEvent(e, t.bubbles, t.cancelable, t.detail) : n.initCustomEvent(e, !1, !1, void 0), n
			} : function (e, t) {
				var n = document.createEventObject();
				return n.type = e, t ? (n.bubbles = Boolean(t.bubbles), n.cancelable = Boolean(t.cancelable), n.detail = t.detail) : (n.bubbles = !1, n.cancelable = !1, n.detail = void 0), n
			}
		}).call(t, function () {
			return this
		}())
	}, function (e, t) {
		"use strict";
		Object.defineProperty(t, "__esModule", {value: !0}), t["default"] = {
			slidesToScroll: 1,
			slideSpeed: 300,
			rewindSpeed: 600,
			snapBackSpeed: 200,
			ease: "ease",
			rewind: !1,
			infinite: !1,
			classNameFrame: "js_frame",
			classNameSlideContainer: "js_slides",
			classNamePrevCtrl: "js_prev",
			classNameNextCtrl: "js_next",
			classNameActiveSlide: "active",
			enableMouseEvents: !1,
			window: window,
			rewindOnResize: !0
		}
	}])
});
//# sourceMappingURL=lory.min.js.map