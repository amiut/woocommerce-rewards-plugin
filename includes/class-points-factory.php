<?php
/**
 * CustomerRewards Point class
 *
 * @package CustomerRewards
 * @since   1.0
 */

namespace Dornaweb\CustomerRewards;

defined('ABSPATH') || exit;

/**
 * CustomerRewards Point class
 */
class Points_Factory {
    public static function tx($args = []) {
        $args = wp_parse_args(
			$args,
			[
				'object_id'     => 0,
                'user_id'       => 0,
                'type'          => 'add',
                'date'          => date('Y-m-d H:i:s', current_time('timestamp')),
                'amount'        => 0,
                'note'          => 0,
            ]
		);

        $data_store = Data_Store::load('point');

        if ($args['amount'] <= 0) {
            throw new Data_Exception('customer_rewards_invalid_amount_deposit', __('Amount must be greater than zero', 'dwrewards'), 400);
        }

        if (!$args['user_id'] || get_userdata($args['user_id']) === false) {
            throw new Data_Exception('customer_rewards_invalid_user', __('Invalid user', 'dwrewards'), 400);
        }

        if (!in_array($args['type'], ['add', 'sub'])) {
            throw new Data_Exception('customer_rewards_invalid_type', __('Type must be `add` or `sub`', 'dwrewards'), 400);
        }

        $point = new Point();
        $point->set_props($args);
        $point->save();
    }

    public static function get_credit_transactions($args = []) {
        $args = wp_parse_args(
			$args,
			[
                'user_id'       => 0,
                'type'          => 'add'
            ]
		);

        if (!$args['user_id'] || get_userdata($args['user_id']) === false) {
            throw new Data_Exception('customer_rewards_invalid_user', __('Invalid user', 'dwrewards'), 400);
        }

        $data_store = Data_Store::load('point');

        return $data_store->get_points_transactions($args);
    }

    public static function get_spend_transactions($args = []) {
        $args = wp_parse_args(
			$args,
			[
                'user_id'       => 0,
                'type'          => 'sub'
            ]
		);

        if (!$args['user_id'] || get_userdata($args['user_id']) === false) {
            throw new Data_Exception('customer_rewards_invalid_user', __('Invalid user', 'dwrewards'), 400);
        }

        $data_store = Data_Store::load('point');

        return $data_store->get_points_transactions($args);
    }
}
