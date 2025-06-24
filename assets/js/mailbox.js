'use strict';

(function ($) {
  'use strict';

  // Select all

  $('.mailbox .mt-select-all input').on('change', function () {
    var checkboxes = $('.mailbox .m-messages').find('input[type="checkbox"]');
    if ($(this).is(':checked')) {
      checkboxes.prop('checked', true);
    } else {
      checkboxes.prop('checked', false);
    }
  });

  // Messages <-> Detail
  $('.mailbox .m-messages a').click(function () {
    $('.mailbox .m-messages li').removeClass('active');
    $(this).parent().removeClass('unread').addClass('active');
    $('.mailbox .m-toolbar').addClass('mt-detail-mode-turned-on');
    $('.mailbox .m-detail').fadeOut().css('display', 'none');
    $('.mailbox .m-detail').fadeIn().css('display', 'block');
  });

  $('.mailbox .m-toolbar .mt-detail-mode-turn-off a').click(function () {
    $('.mailbox .m-toolbar').removeClass('mt-detail-mode-turned-on');
    $('.mailbox .m-detail').removeAttr('style');
  });
})(jQuery);
