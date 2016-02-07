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

  }

  public function show_subscription_options() {

    if (empty(MP_Integration::$api_key)) {
      return;
    }

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
                <input id="mp-is-sub" name="mp_is_sub" type="checkbox" value="1" $checked>
                <input type="hidden" value="Test">
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
    echo $content;
  }

  public function store_subscription_options() {

    if (empty(MP_Integration::$api_key)) {
      return;
    }

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
  }

  public function maybe_remove_discount() {
    if (!isset($_REQUEST['mp_is_sub']) && isset($_REQUEST['mp_sub_freq'])) {
      $this->remove_discount();
    }
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

}

endif;

?>
