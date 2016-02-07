<?php

/**
 * Plugin Name: MorePlease
 * Plugin URI: http://wordpress.org/plugins/moreplease/
 * Description: WooCommerce plugin for moreplease.io.
 * Author: Hugh Willson
 * Version: 0.0.1
 * Author URI: http://octonary.com
 */

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MorePlease')):

/**
 * MorePlease plugin loader.
 */
final class MorePlease {

  public $version = '0.0.1';
  public $cart;
  public $checkout;
  public $web_service;

  protected static $instance = null;

  public static function instance() {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __construct() {
    $this->define_constants();
    $this->init_hooks();
  }

  public function includes() {
    include_once 'includes/class-mp-integration.php';
    if ($this->is_request('frontend')) {
      include_once 'includes/class-mp-environment.php';
      include_once 'includes/class-mp-assets.php';
      include_once 'includes/class-mp-cart.php';
      include_once 'includes/class-mp-checkout.php';
      include_once 'includes/class-mp-web-service.php';
      include_once 'includes/class-mp-subscription.php';
      include_once 'includes/class-mp-product.php';
      include_once 'includes/class-mp-discount.php';
    }
  }

  /**
   * Initialize the integration class.
   */
  public function init() {
    $this->includes();
    add_filter('woocommerce_integrations', array($this, 'add_integration'));
    if ($this->is_request('frontend')) {
      $this->cart = new MP_Cart();
      $this->checkout = new MP_Checkout();
      $this->web_service = new MP_Web_Service();
      $this->subscription = new MP_Subscription();
      $this->product = new MP_Product();
      $this->discount = new MP_Discount();
    }
  }

  /**
   * Add available integrations.
   *
   * @param  Array  $integrations  Integrations.
   * @return  Array  Integrations.
   */
  public function add_integration($integrations) {
    $integrations[] = 'MP_Integration';
    return $integrations;
  }

  public function plugin_url() {
    return untrailingslashit(plugins_url('/', __FILE__));
  }

  private function define_constants() {
    $this->define('MP_VERSION', $this->version);
  }

  private function define($name, $value) {
    if (!defined($name)) {
      define($name, $value);
    }
  }

  /**
   * What type of request is this? string $type ajax, frontend or admin.
   *
   * @param  String  $type  Type of request.
   * @return  Boolean  True if request type match, false otherwise.
   */
  private function is_request($type) {
    switch ( $type ) {
      case 'admin' :
        return is_admin();
      case 'ajax' :
        return defined( 'DOING_AJAX' );
      case 'cron' :
        return defined( 'DOING_CRON' );
      case 'frontend' :
        return (!is_admin() || defined('DOING_AJAX'))
          && !defined('DOING_CRON');
    }
  }

  private function init_hooks() {
    add_action('plugins_loaded', array($this, 'init'), 0);
  }

}

endif;

function MP() {
  return MorePlease::instance();
}

$GLOBALS['moreplease'] = new MorePlease();

?>
