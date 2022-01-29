<?php
/**
 * Tera Wallet Integration
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Integrations;
use Dornaweb\CustomerRewards\Data_Exception;
use Dornaweb\CustomerRewards\Conversion_Helper;

defined( 'ABSPATH' ) || exit;

class Tera_Wallet_Integration extends Integration {
    public function swap_callback() {
        return [$this, 'top_up_wallet'];
    }

    /**
     *
     * @return bool true if successful | false if failed
     */
    public function top_up_wallet($amount, $user_id, $ledger, $request) {
        if (!class_exists('Woo_Wallet_Wallet')) {
            throw new Data_Exception('swap_tera_wallet_class_not_defined', __('There is a problem with swapping, please contact support', 'dwebcr'), 500);
        }

        $wallet = new \Woo_Wallet_Wallet();
        $wallet->credit('', Conversion_Helper::get_swap_amount($amount), __('Swapping earned points', 'dwebcr'));
        $ledger->spend($amount, __('Swapping earned points', 'dwebcr'));

        wp_send_json_success([
            'message' => sprintf(__('You have successfully spent %d coins, you wallet was credited with %s', 'dwebcr'), $amount, wc_price(Conversion_Helper::get_swap_amount($amount)))
        ]);
    }
}
