<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Product')):

class MP_Product {

  public function __construct() {

    add_action(
      'woocommerce_after_add_to_cart_button',
      array($this, 'show_add_to_subscription_button')
    );

  }

  public function show_add_to_subscription_button() {
    $subscription_id = MP_Subscription::get_subscription_id();
    if ($subscription_id) {
      echo '<button type="button" class="mp-add-to-sub">'
        . __('Add to Subscription', 'mp')
        . '</button>';
    }
  }

}

endif;

?>
