<?php
/**
 * Tera Wallet Integration
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Integrations;

defined( 'ABSPATH' ) || exit;

class Tera_Wallet_Integration extends Integration {
    public function callback() {
        return [$this, 'top_up_wallet'];
    }

    public function top_up_wallet() {
        wp_send_json_success([
            'Salam' => true
        ]);
    }
}
