<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Checkout')):

class MP_Checkout {

  public function __construct() {

    add_action(
      'woocommerce_payment_complete',
      array($this, 'maybe_create_subscription')
    );

    add_action(
      'woocommerce_checkout_init',
      array($this, 'disable_guest_checkout')
    );

  }

  public function maybe_create_subscription($order_id) {

    if (empty(MP_Integration::$api_key)) {
      return;
    }

    $is_subscription = WC()->session->get('mp_is_sub');

// DEBUG
/*
$order_id = 15;
$is_subscription = true;
*/

    if ($is_subscription) {

      update_post_meta($order_id, 'mp_order_type', 'new');
      $renewal_frequency = WC()->session->get('mp_sub_freq');

// DEBUG
/*
$renewal_frequency = 'm1';
*/

      update_post_meta($order_id, 'mp_frequency', $renewal_frequency);

      $order = new WC_Order($order_id);
      $customer = $order->get_user();

      $order_items = $order->get_items();
      $subscription_items = array();
      foreach ($order_items as $item_id => $item) {
        $subscription_items[] = array(
          'productId' => (int) $item['product_id'],
          'variationId' => (int) $item['variation_id'],
          'quantity' => (int) $item['qty'],
          'discountPercent' => 0
        );
      }

      $first_name = $customer->first_name;
      if (empty($first_name)) {
        $first_name = $order->billing_first_name;
      }
      $last_name = $customer->last_name;
      if (empty($last_name)) {
        $last_name = $order->billing_last_name;
      }

      $shipping_methods = $order->get_shipping_methods();
      $shipping_method_id = null;
      $shipping_method_name = null;
      $shipping_cost = 0;
      if (!empty($shipping_methods)) {
        $shipping_method_id = key($shipping_methods);
        $shipping_method = $shipping_methods[$shipping_method_id];
        $shipping_method_name = $shipping_method['name'];
        $shipping_cost = $shipping_method['cost'];
      }

      $data = array(
        'apiKey' => MP_Integration::$api_key,
        'subscription' => array(
          'renewalFrequencyId' => $renewal_frequency,
          'shippingMethodId' => $shipping_method_id,
          'shippingMethodName' => $shipping_method_name,
          'shippingCost' => $shipping_cost
          //'companyRole' => 'mptest'
        ),
        'customer' => array(
          'externalId' => $customer->ID,
          'email' => $customer->user_email,
          'firstName' => $first_name,
          'lastName' => $last_name,
        ),
        'order' => array(
          'orderId' => $order_id,
          'orderTypeId' => 'new',
          'orderDate' => $order->order_date
        ),
        'subscriptionItems' => $subscription_items
      );

      $data_json = json_encode(array($data));

      $ch = curl_init(
        MP_Environment::MP_URL . '/methods/api_CreateNewSubscription'
      );
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
      ));

      $subscription_id = null;
      $response = json_decode(curl_exec($ch));
      if (curl_errno($ch)) {
        // TODO - error handling
        echo curl_error($ch);exit;
      } else if (is_object($response)) {
        // TODO - error handling
        var_dump($response);exit;
      } else {
        $subscription_id = $response;
      }
      curl_close($ch);

      if (!empty($subscription_id)) {
        update_post_meta($order_id, 'mp_subscription_id', $subscription_id);
        update_user_meta($customer->ID, 'mp_subscription_id', $subscription_id);
      }

    }

  }

  /**
   * Creating an account at checkout time is mandatory for subscriptions.
   *
   * @param  Object  $checkout  Checkout object.
   */
  public function disable_guest_checkout($checkout) {

    if (empty(MP_Integration::$api_key)) {
      return;
    }

    $checkout->enable_guest_checkout = false;
    $checkout->must_create_account = true;
  }

}

endif;

?>
