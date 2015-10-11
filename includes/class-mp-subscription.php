<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Subscription')):

class MP_Subscription {

  public function __construct() {

    add_action(
      'wp_head',
      array($this, 'load_subscription_id')
    );

  }

  public function load_subscription_id() {
    if (get_current_user_id()) {
      $subscription_id =
        get_user_meta(get_current_user_id(), 'mp_subscription_id', true);
      if ($subscription_id) {
        echo "<script>var MP_SUB_ID='$subscription_id';</script>";
      }
    }
  }

}

endif;

?>
