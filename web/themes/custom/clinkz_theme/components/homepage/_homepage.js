(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.homepage = {
    attach: function (context, settings) {
      once('homepage', '.homepage', context).forEach(function (element) {
        // Initialize Lottie
        const animation = lottie.loadAnimation({
          container: document.getElementById('lottie-container'),
          renderer: 'svg',
          loop: true,
          autoplay: true,
          path: '/themes/custom/clinkz_theme/components/homepage/assets/animation.json',
        });

        // Add loading state
        animation.addEventListener('DOMLoaded', function () {
          element.classList.add('is-loaded');
        });
      });
    },
  };
})(jQuery, Drupal);
