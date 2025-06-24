'use strict';

(function ($) {
  'use strict';

  // List <-> Detail

  $('.contacts .c-list a').click(function () {
    $('.contacts .c-list li').removeClass('active');
    $(this).parent().addClass('active');
    $('.contacts .c-right-toolbar').addClass('crt-detail-mode-turned-on');
    $('.contacts .c-right-toolbar').fadeOut().css('display', 'none');
    $('.contacts .c-right-toolbar').fadeIn().css('display', 'block');
    $('.contacts .c-detail').fadeOut().css('display', 'none');
    $('.contacts .c-detail').fadeIn().css('display', 'block');
  });

  $('.contacts .c-right-toolbar .crt-detail-mode-turn-off a').click(function () {
    $('.contacts .c-right-toolbar').removeClass('crt-detail-mode-turned-on');
    $('.contacts .c-right-toolbar').removeAttr('style');
    $('.contacts .c-detail').removeAttr('style');
  });
})(jQuery);
