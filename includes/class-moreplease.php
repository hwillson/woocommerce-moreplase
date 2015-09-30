<?php

if (!defined('ABSPATH')) {
  exit;
}

class MP_MorePlease {

  public function __construct() {
    add_shortcode(
      'mp-subscription-manager',
      array( __CLASS__, 'show_subscription_manager')
    );
  }

  public function show_subscription_manager() {
    echo '<iframe src="http://moreplease.local:3000/admin"></iframe>';
  }

}

?>
