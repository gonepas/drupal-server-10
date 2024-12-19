/******/ (function() { // webpackBootstrap
/*!******************************************!*\
  !*** ./components/homepage/_homepage.js ***!
  \******************************************/
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t["return"] || t["return"](); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.homepage = {
    attach: function attach(context, settings) {
      // Initialize Lottie animations
      var elements = document.querySelectorAll('.lottie-animation');
      console.log("asdhkaghsdgasjhd");

      // Intersection Observer for lazy loading
      var observerCallback = function observerCallback(entries) {
        var _iterator = _createForOfIteratorHelper(entries),
          _step;
        try {
          var _loop = function _loop() {
            var entry = _step.value;
            if (entry.isIntersecting && !entry.target.hasAttribute('data-processed')) {
              var element = entry.target;
              var animationPath = element.getAttribute('data-animation-path');
              var loop = element.getAttribute('data-loop') !== 'false';
              var autoplay = element.getAttribute('data-autoplay') !== 'false';
              try {
                var animation = lottie.loadAnimation({
                  container: element,
                  renderer: 'svg',
                  loop: loop,
                  autoplay: autoplay,
                  path: animationPath,
                  rendererSettings: {
                    progressiveLoad: true,
                    preserveAspectRatio: 'xMidYMid slice'
                  }
                });

                // Error handling
                animation.addEventListener('error', function (error) {
                  console.error('Lottie animation error:', error);
                  element.innerHTML = 'Animation failed to load';
                });

                // Loading handling
                animation.addEventListener('DOMLoaded', function () {
                  element.classList.add('animation-loaded');
                });

                // Add data-processed attribute to prevent re-initialization
                element.setAttribute('data-processed', 'true');
              } catch (error) {
                console.error('Failed to initialize Lottie animation:', error);
                element.innerHTML = 'Animation initialization failed';
              }
            }
          };
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            _loop();
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      };

      // Create and configure the observer
      var observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
      };
      var observer = new IntersectionObserver(observerCallback, observerOptions);

      // Observe all Lottie animation elements
      var _iterator2 = _createForOfIteratorHelper(elements),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var element = _step2.value;
          observer.observe(element);
        }

        // Cleanup function for when elements are removed
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
      var cleanup = function cleanup() {
        var _iterator3 = _createForOfIteratorHelper(elements),
          _step3;
        try {
          for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
            var element = _step3.value;
            if (element.hasAttribute('data-processed')) {
              observer.unobserve(element);
            }
          }
        } catch (err) {
          _iterator3.e(err);
        } finally {
          _iterator3.f();
        }
      };

      // Add cleanup to Drupal's behaviors detach
      return cleanup;
    },
    detach: function detach(context, settings, trigger) {
      if (trigger === 'unload') {
        var elements = context.querySelectorAll('.lottie-animation');
        var _iterator4 = _createForOfIteratorHelper(elements),
          _step4;
        try {
          for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
            var element = _step4.value;
            if (element.hasAttribute('data-processed')) {
              element.removeAttribute('data-processed');
            }
          }
        } catch (err) {
          _iterator4.e(err);
        } finally {
          _iterator4.f();
        }
      }
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=homepage.js.map