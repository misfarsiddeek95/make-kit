'use strict';

(function ($) {
  'use strict';

  // Toastr

  var i = -1;
  var toastCount = 0;
  var $toastlast;

  var getMessage = function getMessage() {
    var msgs = ['Are you the six fingered man?', 'Inconceivable!', 'I do not think that means what you think it means.', 'Have fun storming the castle!'];

    i++;

    if (i === msgs.length) {
      i = 0;
    }

    return msgs[i];
  };

  $('#showtoast').click(function () {
    var shortCutFunction = $("#toastTypeGroup").val();
    var msg = $('#message').val();
    var title = $('#title').val() || '';
    var toastIndex = toastCount++;

    toastr.options = {
      closeButton: $('#closeButton').prop('checked'),
      progressBar: $('#progressBar').prop('checked'),
      positionClass: $('#positionGroup').val() || 'toast-top-right',
      onclick: null
    };

    if (!msg) {
      msg = getMessage();
    }

    var $toast = toastr[shortCutFunction](msg, title);

    if (typeof $toast === 'undefined') {
      return;
    }
  });

  $('#cleartoasts').click(function () {
    toastr.clear();
  });
})(jQuery);
