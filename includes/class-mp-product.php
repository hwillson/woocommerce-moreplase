<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Product')):

class MP_Product {

  public function __construct() {


// TODO - maybe this doesn't make sense; maybe add to subscription button
// should always be a shortcode, that takes product/variation ID params, with
// quantity option, and option to pull quantity from jquery selector...

    // add_action(
    //   'woocommerce_after_add_to_cart_button',
    //   array($this, 'show_add_to_subscription_button')
    // );

    add_shortcode(
      'mp-add-to-sub-button',
      array($this, 'show_add_to_subscription_button')
    );

  }

  public function show_add_to_subscription_button($attributes, $content) {
// TODO - left off here ...
    $subscription_id = MP_Subscription::get_subscription_id();
    if ($subscription_id) {
      $buttonLabel = null;
      if ($content) {
        $buttonLabel = $content;
      } else {
        $buttonLabel = __('Add to Subscription', 'woocommerce-mp');
      }
      echo
        '<div class="mp-add-to-sub">'
        .   '<button type="button" class="js-mp-add-to-sub">'
        .     $buttonLabel
        .   '</button>'
        . '</div>';
    }
  }

}

endif;

?>
