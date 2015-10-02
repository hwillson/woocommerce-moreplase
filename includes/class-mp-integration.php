<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Integration')) {

  class MP_Integration extends WC_Integration {

    public function __construct() {

      global $woocommerce;
      $this->id = 'mp-integration';
      $this->method_title = __('MorePlease', 'woocommerce-mp');
      $this->method_description =
        __('WooCommerce MorePlease.io plugin settings.', 'woocommerce-mp');

      $this->init_form_fields();
      $this->init_settings();

      $this->api_key = $this->get_option('api_key');

      add_action(
        'woocommerce_update_options_integration_' .  $this->id,
        array($this, 'process_admin_options')
      );

    }

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
      );
    }

  }

}

?>
