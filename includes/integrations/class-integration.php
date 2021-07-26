<?php
/**
 * Tera Wallet Integration
 *
 * @package CustomerRewards\Integrations
 */

namespace Dornaweb\CustomerRewards\Integrations;
use \Dornaweb\CustomerRewards\Utils\Singleton_Trait;

defined( 'ABSPATH' ) || exit;

class Integration {
    use Singleton_Trait;

    public function init() {
        add_action('dweb_rewards_swap_method', [$this, 'callback']);
    }
}
