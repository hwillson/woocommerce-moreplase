jQuery(function () {
  (function ($) {

    var MP_URL = 'http://moreplease.local:3000';

    // Fetch and display days until next subscription renewal date.
    if ($('.js-mp-days-until-next-ship').length && MP_SUB_ID) {
      $('.js-mp-days-until-next-ship').html('...');
      var postData = {
        subscriptionId: MP_SUB_ID
      };
      $.post(MP_URL + '/api_SubscriptionRenewalDayCount', postData).done(
        function (renewalDays) {
          var msg;
          if (renewalDays == 0) {
            msg = 'today';
          } else {
            msg = 'in ' + renewalDays + ' days';
          }
          $('.js-mp-days-until-next-ship').html(msg);
        }
      );
    }

  }(jQuery));
})
