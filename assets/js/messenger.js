'use strict';

(function ($) {
  'use strict';

  // Chat list <-> Chat window

  $('.messenger .m-chat-list a').click(function () {
    $('.messenger .m-chat-list li').removeClass('active');
    $(this).parent().removeClass('unread').addClass('active');
    $('.messenger .m-right-toolbar').addClass('mrt-chat-window-mode-turned-on');
    $('.messenger .m-right-toolbar').fadeOut().css('display', 'none');
    $('.messenger .m-right-toolbar').fadeIn().css('display', 'block');
    $('.messenger .m-chat-window').fadeOut().css('display', 'none');
    $('.messenger .m-chat-window').fadeIn().css('display', 'block');
    $('.messenger .m-compose').fadeOut().css('display', 'none');
    $('.messenger .m-compose').fadeIn().css('display', 'block');
  });

  $('.messenger .m-right-toolbar .mrt-chat-window-mode-turn-off a').click(function () {
    $('.messenger .m-right-toolbar').removeClass('mrt-chat-window-mode-turned-on');
    $('.messenger .m-right-toolbar').removeAttr('style');
    $('.messenger .m-chat-window').removeAttr('style');
    $('.messenger .m-compose').removeAttr('style');
  });
})(jQuery);
