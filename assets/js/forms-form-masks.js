'use strict';

(function ($) {
  'use strict';

  // Iputmask

  $('#inputmasks').find(':input').each(function () {
    $(this).inputmask();
  });
})(jQuery);
