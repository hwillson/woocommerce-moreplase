<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Assets')):

class MP_Assets {

  public function __construct() {
    add_action('wp_enqueue_scripts', array($this, 'add_styles'));
    //add_action('enqueue_scripts', array($this, 'add_scripts'));
  }

  public function add_styles() {
    wp_enqueue_style(
      'mp-styles',
      MP()->plugin_url() . '/assets/css/main.css',
      array(),
      MP_VERSION
    );
  }

}

endif;

return new MP_Assets();

?>
