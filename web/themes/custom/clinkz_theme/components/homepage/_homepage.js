(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.homepage = {
    attach: (context, settings) => {
      // Initialize Lottie animations with improved performance
      const elements = document.querySelectorAll('.lottie-animation');

      // Intersection Observer for lazy loading
      const observerCallback = (entries) => {
        entries.forEach(entry => {
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
                  preserveAspectRatio: 'xMidYMid cover', // Changed to cover
                  clearCanvas: true,
                }
              });

              // Performance optimizations
              animation.setSubframe(false);

              // Add smooth loading transition
              animation.addEventListener('DOMLoaded', () => {
                element.style.opacity = '0';
                setTimeout(() => {
                  element.style.transition = 'opacity 0.5s ease-in';
                  element.style.opacity = '1';
                }, 100);
              });

              // Error handling
              animation.addEventListener('error', error => {
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
      const observerOptions = {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
      };

      const observer = new IntersectionObserver(observerCallback, observerOptions);

      elements.forEach(element => {
        observer.observe(element);
      });

      // Add smooth scroll behavior
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });

      // Add cursor effects (optional)
      const cursor = document.createElement('div');
      cursor.className = 'custom-cursor';
      document.body.appendChild(cursor);

      document.addEventListener('mousemove', e => {
        cursor.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
      });

      return () => {
        elements.forEach(element => {
          if (element.hasAttribute('data-processed')) {
            observer.unobserve(element);
          }
        });
        document.body.removeChild(cursor);
      };
    }
  };
})(jQuery, Drupal);