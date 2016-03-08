jQuery(function () {
  (function ($) {

    var MP_URL = 'http://moreplease.local:3000';

    // Fetch and display days until next subscription renewal date.
    if ($('.js-mp-days-until-next-ship').length && MP_SUB_ID) {
      $('.js-mp-days-until-next-ship').html('...');
      var postData = {
        subscriptionId: MP_SUB_ID
      };
      $.post(MP_URL + '/methods/api_SubscriptionRenewalDayCount', postData).done(
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


    // Fetch and display next ship date for a subscription renewal order.
    if ($('.js-mp-next-ship-date').length && MP_SUB_ID) {
      var postData = {
        subscriptionId: MP_SUB_ID
      };
      $.post(MP_URL + '/methods/api_SubscriptionRenewalDate', postData).done(
        function (renewalDate) {
          $('.js-mp-next-ship-date').html(renewalDate);
        }
      );
    }

    // Dynamicall adjust the size of subscription manager iframe.
    if ($('.mp-iframe').length) {
      var ifHeight;
      jQuery.receiveMessage(function (e) {
        var height = Number(e.data.replace(/.*if_height=(\d+)(?:&|$)/, '$1'));
        if (!isNaN(height) && height > 0 && height !== ifHeight) {
          ifHeight = height;
          jQuery('.mp-iframe').height(height);
        }
      });
    }


// TODO - working on this ...
    // Add product to subscription.
    $('body').on('click', '.js-mp-add-to-sub', function (event) {

      var button, boxContainer, productData, buttonText;

      event.preventDefault();

      if (!$(this).hasClass('adding')) {

        button = $(this).addClass('adding');

        boxContainer = $(this).closest('.js-grid-item, form');

        productData = {
          nonce:  tfAjax.nonce,
          product_id: $(this).attr('data-id'),
          variation_id: $(boxContainer).find('.js-variation').val(),
          quantity: $(boxContainer).find('.js-quantity').val()
        }

        buttonText = $(this).find('.text').text();
        $(this).find('.text').html('Adding...');

        $.post('?wc-ajax=load_box_item', productData).done(function (boxItem) {
          if (boxItem) {
            var boxData = {
              subscriptionId: $('body').attr('data-box-id'),
              productId: boxItem.productId,
              variationId: boxItem.variationId,
              quantity: boxItem.quantity,
              discountPercent: 10,
              companyRole: boxItem.companyRole
            };
            $.ajax({
              url: boxApi + '/api_AddToSubscription',
              method: 'POST',
              data: JSON.stringify(boxData),
              contentType: 'application/json',
              success: function (boxItemId) {
                if (boxItemId) {
                  $.publish('box', boxItem);
                  button.removeClass('adding').find('.text').html('Added!');
                  button.blur();
                  setTimeout(function () {
                    button.find('.text').html(buttonText);
                  }, 2000);
                } else {
                  console.log(
                    'Unable to add product to subscription: ',
                    boxData
                  );
                  button.removeClass('adding').find('.text').html(buttonText);
                }
              }
            });
          }
        });

      }

  }(jQuery));
})
