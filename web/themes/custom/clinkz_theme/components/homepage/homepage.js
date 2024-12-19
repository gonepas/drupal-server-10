/******/ (function() { // webpackBootstrap
/*!******************************************!*\
  !*** ./components/homepage/_homepage.js ***!
  \******************************************/
(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.clinkzThemeLottie = {
    attach: (context, settings) => {
      // Initialize Lottie animations
      const elements = document.querySelectorAll('.lottie-animation');
      console.log("asdhkaghsdgasjhd");

      // Intersection Observer for lazy loading
      const observerCallback = (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting && !entry.target.hasAttribute('data-processed')) {
            const element = entry.target;
            const animationPath = element.getAttribute('data-animation-path');
            const loop = element.getAttribute('data-loop') !== 'false';
            const autoplay = element.getAttribute('data-autoplay') !== 'false';

            try {
              const animation = lottie.loadAnimation({
                container: element,
                renderer: 'svg',
                loop,
                autoplay,
                path: animationPath,
                rendererSettings: {
                  progressiveLoad: true,
                  preserveAspectRatio: 'xMidYMid slice'
                }
              });

              // Error handling
              animation.addEventListener('error', (error) => {
                console.error('Lottie animation error:', error);
                element.innerHTML = 'Animation failed to load';
              });

              // Loading handling
              animation.addEventListener('DOMLoaded', () => {
                element.classList.add('animation-loaded');
              });

              // Add data-processed attribute to prevent re-initialization
              element.setAttribute('data-processed', 'true');
            } catch (error) {
              console.error('Failed to initialize Lottie animation:', error);
              element.innerHTML = 'Animation initialization failed';
            }
          }
        }
      };

      // Create and configure the observer
      const observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
      };

      const observer = new IntersectionObserver(observerCallback, observerOptions);

      // Observe all Lottie animation elements
      for (const element of elements) {
        observer.observe(element);
      }

      // Cleanup function for when elements are removed
      const cleanup = () => {
        for (const element of elements) {
          if (element.hasAttribute('data-processed')) {
            observer.unobserve(element);
          }
        }
      };

      // Add cleanup to Drupal's behaviors detach
      return cleanup;
    },

    detach: (context, settings, trigger) => {
      if (trigger === 'unload') {
        const elements = context.querySelectorAll('.lottie-animation');
        for (const element of elements) {
          if (element.hasAttribute('data-processed')) {
            element.removeAttribute('data-processed');
          }
        }
      }
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=homepage.js.map