<?php

/**
 * Plugin Name: MorePlease
 * Plugin URI: http://wordpress.org/plugins/moreplease/
 * Description: WooCommerce plugin for moreplease.io.
 * Author: Hugh Willson
 * Version: 0.1
 * Author URI: http://octonary.com
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_MorePlease')) {

  class MP_MorePlease {

    public function __construct() {
      add_action('plugins_loaded', array($this, 'init'));
    }

    public function init() {
      if (class_exists('WC_Integration')) {
        include_once 'includes/class-mp-integration.php';
        add_filter('woocommerce_integrations', array($this, 'add_integration'));
      } else {
        // TODO - throw admin error?
      }
    }

    public function add_integration($integrations) {
      $integrations[] = 'MP_Integration';
      return $integrations;
    }

  }

  $MP_MorePlease = new MP_MorePlease(__FILE__);

}

?>
