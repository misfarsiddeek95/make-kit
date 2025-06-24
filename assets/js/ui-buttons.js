'use strict';

(function ($) {
  'use strict';

  // Ladda buttons

  Ladda.bind('.ladda-button', {
    timeout: 2000
  });
  Ladda.bind('.ladda-progress .ladda-button', {
    callback: function callback(instance) {
      var progress = 0;
      var interval = setInterval(function () {
        progress = Math.min(progress + Math.random() * 0.1, 1);
        instance.setProgress(progress);
        if (progress === 1) {
          instance.stop();
          clearInterval(interval);
        }
      }, 200);
    }
  });
})(jQuery);
