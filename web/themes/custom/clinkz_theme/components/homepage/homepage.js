/******/ (function() { // webpackBootstrap
/*!******************************************!*\
  !*** ./components/homepage/_homepage.js ***!
  \******************************************/
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.homepage = {
    attach: function attach(context, settings) {
      // Initialize Lottie animations with improved performance
      var elements = document.querySelectorAll('.lottie-animation');

      // Intersection Observer for lazy loading
      var observerCallback = function observerCallback(entries) {
        entries.forEach(function (entry) {
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
                  preserveAspectRatio: 'xMidYMid cover',
                  // Changed to cover
                  clearCanvas: true
                }
              });

              // Performance optimizations
              animation.setSubframe(false);

              // Add smooth loading transition
              animation.addEventListener('DOMLoaded', function () {
                element.style.opacity = '0';
                setTimeout(function () {
                  element.style.transition = 'opacity 0.5s ease-in';
                  element.style.opacity = '1';
                }, 100);
              });

              // Error handling
              animation.addEventListener('error', function (error) {
                console.error('Lottie animation error:', error);
                element.innerHTML = 'Animation failed to load';
              });
              element.setAttribute('data-processed', 'true');
            } catch (error) {
              console.error('Failed to initialize Lottie animation:', error);
            }
          }
        });
      };

      // Create and configure the observer with improved options
      var observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
      };
      var observer = new IntersectionObserver(observerCallback, observerOptions);
      elements.forEach(function (element) {
        observer.observe(element);
      });

      // Add smooth scroll behavior
      document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          var target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });

      // Add cursor effects (optional)
      var cursor = document.createElement('div');
      cursor.className = 'custom-cursor';
      document.body.appendChild(cursor);
      document.addEventListener('mousemove', function (e) {
        cursor.style.transform = "translate(".concat(e.clientX, "px, ").concat(e.clientY, "px)");
      });
      return function () {
        elements.forEach(function (element) {
          if (element.hasAttribute('data-processed')) {
            observer.unobserve(element);
          }
        });
        document.body.removeChild(cursor);
      };
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=homepage.js.map