<?php

if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('MP_Cart')):

class MP_Cart {

  public function __construct() {

    add_action(
      'woocommerce_cart_contents',
      array($this, 'show_subscription_options')
    );

    add_action(
      'woocommerce_cart_updated',
      array($this, 'store_subscription_options')
    );

    add_action(
      'template_redirect',
      array($this, 'maybe_remove_discount')
    );

    add_action(
      'template_redirect',
      array($this, 'maybe_remove_ship_with_sub_discount')
    );

  }

  public function show_subscription_options() {
    if (MP_Integration::$api_key) {
      $subscription_options = null;
      if (MP_Subscription::get_subscription_id()) {
        $subscription_options = $this->existing_subscriber_options();
      } else {
        $subscription_options = $this->new_subscriber_options();
      }
      echo $subscription_options;
    }
  }

  public function store_subscription_options() {
    if (MP_Integration::$api_key) {

      if (isset($_REQUEST['mp_is_sub'])) {
        WC()->session->set('mp_is_sub', true);
        $this->apply_discount();
        if (isset($_REQUEST['mp_sub_freq'])) {
          WC()->session->set('mp_sub_freq', $_REQUEST['mp_sub_freq']);
        }
      } else {
        if (isset($_REQUEST['mp_sub_freq'])) {
          WC()->session->set('mp_is_sub', false);
          WC()->session->set('mp_sub_freq', '');
        }
      }

      if (isset($_REQUEST['mp_ship_with_sub'])) {
        WC()->session->set('mp_ship_with_sub', true);
        $this->apply_ship_with_sub_discount();
      } else {
        if (isset($_REQUEST['mp_ship_with_sub_reset'])) {
          WC()->session->set('mp_ship_with_sub', false);
        }
      }

    }
  }

  public function maybe_remove_discount() {
    if (MP_Integration::$api_key) {
      if (!isset($_REQUEST['mp_is_sub']) && isset($_REQUEST['mp_sub_freq'])) {
        $this->remove_discount();
      }
    }
  }

  public function maybe_remove_ship_with_sub_discount() {
    if (MP_Integration::$api_key) {
      if (!isset($_REQUEST['mp_ship_with_sub'])
          && isset($_REQUEST['mp_ship_with_sub_reset'])) {
        $this->remove_ship_with_sub_discount();
      }
    }
  }

  private function new_subscriber_options() {

    $is_subscription = WC()->session->get('mp_is_sub');
    $checked = '';
    if ($is_subscription) {
      $checked = 'checked';
    }

    $freq_options = array(
      'w1' => 'Every Week',
      'w2' => 'Every 2 Weeks',
      'm1' => 'Once a Month',
      'm2' => 'Every 2 Months'
    );
    $select_options = '';
    foreach ($freq_options as $key => $value) {
      $select_options .=
        "<option value=\"$key\" " . $this->set_selected_frequency($key)
        . ">$value</option>";
    }

    $content = <<<CONTENT
      <tr>
        <td colspan="6">
          <div class="mp-sub-options">
            <div class="checkbox form-group">
              <label>
                <input id="mp-is-sub" name="mp_is_sub" type="checkbox"
                  value="1" $checked>
                Make this purchase a subscription
              </label>
            </div>
            <div class="form-group">
              <label for="">Renewal Frequency</label>
              <select id="mp-sub-freq" name="mp_sub_freq" class="form-control">
                <option value=""></option>
                $select_options
              </select>
            </div>
          </div>
        </td>
      </tr>
CONTENT;
    return $content;

  }

  private function existing_subscriber_options() {

    $ship_with_subscription = WC()->session->get('mp_ship_with_sub');
    $checked = '';
    if ($ship_with_subscription) {
      $checked = 'checked';
    }

    $content = <<<CONTENT
      <tr>
        <td colspan="6">
          <div class="mp-sub-options">
            <div class="checkbox form-group">
              <label>
                <input id="mp-ship-with-sub" name="mp_ship_with_sub"
                  type="checkbox" value="1" $checked>
                <input type="hidden" name="mp_ship_with_sub_reset" value="1">
                Ship with next subscription renewal on
                <span class="js-mp-next-ship-date">...</span>
              </label>
            </div>
          </div>
        </td>
      </tr>
CONTENT;
    return $content;

  }

  private function set_selected_frequency($value) {
    $selected_frequency = WC()->session->get('mp_sub_freq');
    $selected = '';
    if ($selected_frequency && $value && ($value == $selected_frequency)) {
      $selected = 'selected';
    }
    return $selected;
  }

  private function apply_discount() {
    $discount_coupon_code = MP_Integration::$discount_coupon_code;
    if (!empty($discount_coupon_code)) {
      if (!WC()->cart->has_discount($discount_coupon_code)) {
        WC()->cart->add_discount($discount_coupon_code);
      }
      wc_clear_notices();
    }
  }

  private function remove_discount() {
    $discount_coupon_code = MP_Integration::$discount_coupon_code;
    if (!empty($discount_coupon_code)) {
      WC()->cart->remove_coupon($discount_coupon_code);
    }
  }

  private function apply_ship_with_sub_discount() {
    $ship_with_sub_coupon_code =
      MP_Integration::$ship_with_sub_discount_coupon_code;
    if (!empty($ship_with_sub_coupon_code)) {
      if (!WC()->cart->has_discount($ship_with_sub_coupon_code)) {
        WC()->cart->add_discount($ship_with_sub_coupon_code);
      }
      wc_clear_notices();
    }
  }

  private function remove_ship_with_sub_discount() {
    $ship_with_sub_coupon_code =
      MP_Integration::$ship_with_sub_discount_coupon_code;
    if (!empty($ship_with_sub_coupon_code)) {
      WC()->cart->remove_coupon($ship_with_sub_coupon_code);
    }
  }

}

endif;

?>
