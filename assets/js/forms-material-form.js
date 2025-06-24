'use strict';

(function ($) {
  'use strict';

  // Check material input for value

  function checkValue(element) {
    var hasValue = (element.val() || '').length;
    element.parent().toggleClass('has-value', !!hasValue);
  }

  checkValue($(this));

  $('.form-material .form-control').each(function () {
    checkValue($(this));
    $(this).on('change', function () {
      checkValue($(this));
    });
  });
})(jQuery);
