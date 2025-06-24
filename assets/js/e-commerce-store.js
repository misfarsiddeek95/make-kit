'use strict';

(function ($) {
  'use strict';

  // Bootstrap slider

  $('[data-plugin="bootstrapslider"]').bootstrapSlider();

  // Select2
  $.fn.select2.defaults.set('theme', 'bootstrap');
  $('[data-plugin="select2"]').select2($(this).attr('data-options'));
})(jQuery);
