<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Integration')):

/**
 * MorePlease.io integration class.
 */
class MP_Integration extends WC_Integration {

  public static $api_key;
  public static $discount_coupon_code;
  public static $ship_with_sub_discount_coupon_code;

  /**
   * Default constructor. Intialize integration defaults and settings
   * fields.
   */
  public function __construct() {

    global $woocommerce;
    $this->id = 'mp-integration';
    $this->method_title = __('MorePlease', 'woocommerce-mp');
    $this->method_description =
      __('WooCommerce MorePlease.io plugin settings.', 'woocommerce-mp');

    $this->init_form_fields();
    $this->init_settings();

    self::$api_key = $this->get_option('api_key');
    self::$discount_coupon_code = $this->get_option('discount_coupon_code');
    self::$ship_with_sub_discount_coupon_code =
      $this->get_option('ship_with_sub_discount_coupon_code');

    add_action(
      'woocommerce_update_options_integration_' .  $this->id,
      array($this, 'process_admin_options')
    );

  }

  /**
   * Intiailize settings page form fields.
   */
  public function init_form_fields() {
    $this->form_fields = array(

      'api_key' => array(
        'title' => __('MorePlease API Key', 'woocommerce-mp'),
        'type' => 'text',
        'description' =>
          __('Enter your MorePlease API Key. You can find this in MorePlease '
            . 'admin (https://moreplease.io/admin) under Account > API Keys.',
            'woocommerce-mp'),
        'desc_tip' => true,
        'default' => ''
      ),

      'discount_coupon_code' => array(
        'title' => __('Discount Coupon Code', 'woocommerce-mp'),
        'type' => 'text',
        'description' =>
          __('Interested in giving subscribers a discount? Enter the '
            . 'applicable coupon code here to have it applied when changing a '
            . 'cart to a subscription, and/or when existing subscribers login. '
            . 'Leave this field empty for no discount.',
            'woocommerce-mp'),
        'desc_tip' => true,
        'default' => ''
      ),

      'ship_with_sub_discount_coupon_code' => array(
        'title' => __(
          'Ship With Next Subscription Renewal Discount Coupon Code',
          'woocommerce-mp'
        ),
        'type' => 'text',
        'description' =>
          __('If provided this coupon code will be applied to existing '
            . 'subscriber one-time orders, when selecting to thave their new '
            . 'order shipped with their next subscription renewal.',
            'woocommerce-mp'),
        'desc_tip' => true,
        'default' => ''
      )

    );
  }

}

endif;

?>
