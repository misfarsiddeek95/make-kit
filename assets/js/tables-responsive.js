'use strict';

(function ($) {
  'use strict';

  // Peity chart

  $('[data-chart="peity"]').each(function () {
    var type = $(this).attr('data-type');
    $(this).peity(type);
  });
})(jQuery);
