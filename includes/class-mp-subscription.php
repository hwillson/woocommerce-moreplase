<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Subscription')):

class MP_Subscription {

  const SUBSCRIPTION_ID_KEY = 'mp_subscription_id';

  public function __construct() {

    add_action(
      'wp_head',
      array($this, 'load_subscription_id')
    );

    add_shortcode(
      'mp-subscription-manager',
      array($this, 'subscription_manager')
    );

  }

  public function load_subscription_id() {
    $subscription_id = self::get_subscription_id();
    if ($subscription_id) {
      echo "<script>var MP_SUB_ID='$subscription_id';</script>";
    }
  }

  public function subscription_manager() {

    $managerContent = null;
    $user_id = get_current_user_id();
    if ($user_id) {
      $subscription_id =
        get_user_meta($user_id, self::SUBSCRIPTION_ID_KEY, true);
      if ($subscription_id) {
        $url = MP_Environment::MP_URL . '/subscription?id='
             . $subscription_id . '&token=' . MP_Integration::$api_key;
        $managerContent =
          "<iframe class=\"mp-iframe\" scrolling=\"no\" src=\"$url\">"
            . "</iframe>";
      } else {
        $managerContent =
          'You do not have an active box subscription.';
      }
      return $managerContent;
    }

  }

  public static function get_subscription_id() {
    $subscription_id = null;
    if (get_current_user_id()) {
      $subscription_id =
        get_user_meta(get_current_user_id(), self::SUBSCRIPTION_ID_KEY, true);
    }
    return $subscription_id;
  }

}

endif;

?>
