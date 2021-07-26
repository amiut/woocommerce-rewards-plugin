<?php
/**
 * Bookmarks REST Controller
 *
 * @package WooCommerceWishlist
 * @since   1.0.0
 * @version 1.0.0
 */

namespace Dornaweb\CustomerRewards\Rest_API\Controllers\V1;
use Dornaweb\CustomerRewards\Data_Exception;
use Dornaweb\CustomerRewards\REST_Exception;
use Dornaweb\CustomerRewards\Ledger;
use Dornaweb\CustomerRewards\Conversion_Helper;

defined('ABSPATH') || exit;

class Swap_REST_Controller extends \Dornaweb\CustomerRewards\Rest_API\REST_Controller {
    /**
     * REST Route
     */
    public $path = 'swap';

    public $methods = ['get', 'post'];

    public function get() {
        global $current_user;
        $ledger = new Ledger($current_user->ID);

        wp_send_json_success([
            'price_format'      => get_woocommerce_price_format(),
            'user_id'           => $current_user->ID,
            'user_balance'      => $ledger->get_balance(),
            'current_currency'  => get_woocommerce_currency(),
            'current_rate'      => Conversion_Helper::get_rate(),
            'rates'             => Conversion_Helper::get_rates(),
        ]);
    }

    public function post($request) {
        global $current_user;
        $amount = floatval($request->get_param('amount'));
        $swap_method = apply_filters('dweb_rewards_swap_method', '', $current_user->ID, $request);
        $ledger = new Ledger($current_user->ID);

        try {
            if (!$amount) {
                throw new REST_Exception('swap_amount_empty', __('Amount is not set', 'dwebcr'), 400);
            }

            if ($amount > $ledger->get_balance()) {
                throw new REST_Exception('swap_insufficient_balance', __('insufficient balance', 'dwebcr'), 400);
            }

            if (!$swap_method || !is_callable($swap_method)) {
                throw new REST_Exception('swap_method_not_callable', __('There is a problem with swapping, please contact support', 'dwebcr'), 500);
            }

            call_user_func_array($swap_method, [$amount, $current_user->ID, $request]);

        } catch (Data_Exception $e) {
            wp_send_json_error($e->getErrorData(), $e->getCode());
        }
    }

    public function permission_get() {
        return is_user_logged_in();
    }

    public function permission_post() {
        return is_user_logged_in();
    }
}
