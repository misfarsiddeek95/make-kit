'use strict';

(function ($) {
  'use strict';

  // Bootstrap wizard

  $('.form-wizard').bootstrapWizard({ onTabShow: function onTabShow(tab, navigation, index) {
      var $total = navigation.find('li').length;
      var $current = index + 1;
      var $percent = $current / $total * 100;
      $('.form-wizard .progress-bar').css({ width: $percent + '%' });
    } });
})(jQuery);
