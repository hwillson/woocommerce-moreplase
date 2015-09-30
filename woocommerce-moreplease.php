<?php

/*
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

if (in_array('woocommerce/woocommerce.php',
    apply_filters('active_plugins', get_option('active_plugins')))) {
  require_once 'includes/class-moreplease.php';
  new MP_MorePlease();
}

?>
