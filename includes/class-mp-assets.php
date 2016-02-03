<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Assets')):

class MP_Assets {

  public function __construct() {
    add_action('wp_enqueue_scripts', array($this, 'add_styles'));
    add_action('wp_enqueue_scripts', array($this, 'add_scripts'));
  }

  public function add_styles() {
    wp_enqueue_style(
      'mp-styles',
      MP()->plugin_url() . '/assets/css/main.css',
      array(),
      MP_VERSION
    );
  }

  public function add_scripts() {
    wp_enqueue_script(
      'jquery-postmessage',
      MP()->plugin_url() . '/assets/js/jquery.ba-postmessage.min.js',
      array('jquery'),
      MP_VERSION,
      true
    );
    wp_enqueue_script(
      'mp-scripts',
      MP()->plugin_url() . '/assets/js/main.js',
      array('jquery', 'jquery-postmessage'),
      MP_VERSION,
      true
    );
  }

}

endif;

return new MP_Assets();

?>
