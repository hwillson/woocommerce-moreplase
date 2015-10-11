<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Web_Service')):

class MP_Web_Service {

  public function __construct() {

    add_action(
      'wc_ajax_get_product_variations',
      array($this, 'get_product_variations')
    );

  }

  public function get_product_variations() {

    global $wpdb;

    $sql = "SELECT     p.id AS variationId, p.post_parent AS productId, "
         . "           t.name AS variationName "
         . "FROM       {$wpdb->prefix}posts p "
         . "INNER JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.id "
         . "INNER JOIN {$wpdb->prefix}terms t ON t.slug = pm.meta_value "
         . "WHERE      p.post_parent != 0 "
         . "AND        p.post_status = 'publish' "
         . "AND        pm.meta_key LIKE 'attribute_pa_%' "
         . "UNION "
         . "SELECT     0 AS variationId, p.id AS productId, "
         . "           '' AS variationName "
         . "FROM       {$wpdb->prefix}posts p "
         . "INNER JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = p.id "
         . "WHERE      p.post_parent = 0 "
         . "AND        p.post_type = 'product' "
         . "AND        p.post_status = 'publish' ";
    $results = $wpdb->get_results($sql, OBJECT);

    $product_variations = array();
    if (!empty($results)) {
      foreach ($results as $result) {

        $product_variation = new stdClass();

        $product_variation->productId = (int) $result->productId;
        $product = wc_get_product($product_variation->productId);
        if ($product) {

          $product_variation->productName = $product->get_title();
          $product_variation->productUrl = $product->get_permalink();

          // Extract image URL from returned <img /> tag
          preg_match('/"(.*?)"/', $product->get_image(), $match);
          if (isset($match[1])) {
            $product_variation->productImage = $match[1];
          }

          $product_variation->variationPrice = (float) $product->get_price();

          if ((int) $result->variationId !== 0) {
            $product_variation->variationId = (int) $result->variationId;
            $product_variation->variationName = $result->variationName;
            $variation = wc_get_product($product_variation->variationId);
            if ($variation->get_image()) {
              // Extract image URL from returned <img /> tag
              preg_match('/"(.*?)"/', $variation->get_image(), $match);
              if (isset($match[1])) {
                $product_variation->productImage = $match[1];
              }
            }
            $product_variation->variationPrice = (float) $variation->get_price();
          }

          $product_variations[] = $product_variation;

        }

      }

      $product_variations_json = json_encode($product_variations);
      wp_send_json_success($product_variations_json);

    } else {
      wp_send_json_error();
    }

    exit;

  }

}

endif;

?>
