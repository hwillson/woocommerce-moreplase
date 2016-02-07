<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Discount')):

class MP_Discount {

  public function __construct() {

    add_action(
      'template_redirect',
      array($this, 'maybe_apply_subscriber_discount')
    );

  }

  public function maybe_apply_subscriber_discount() {
    $user_id = get_current_user_id();
    if ($user_id) {
      $discount_coupon_code = MP_Integration::$discount_coupon_code;
      if (!empty($discount_coupon_code)) {
        $sub_id = get_user_meta($user_id, 'mp_subscription_id', true);
        if ($sub_id) {
          if (!WC()->cart->has_discount($discount_coupon_code)) {
            WC()->cart->add_discount($discount_coupon_code);
          }
          wc_clear_notices();
        } else {
          WC()->cart->remove_coupon($discount_coupon_code);
        }
      }
    }
  }

}

endif;

?>
