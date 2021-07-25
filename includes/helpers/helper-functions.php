<?php
/**
 * Helper functions
 *
 */
if (!function_exists('dweb_rewards_get_balance')) {
    function dweb_rewards_get_balance($user_id = 0) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) return 0;

        $ledger = new \Dornaweb\CustomerRewards\Ledger($user_id);
        return $ledger->get_balance();
    }
}
