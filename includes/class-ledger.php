<?php
/**
 * CustomerRewards Point class
 *
 * @package CustomerRewards
 * @since   1.0
 */

namespace Dornaweb\CustomerRewards;

defined('ABSPATH') || exit;

class Ledger {
    /**
     * User id
     */
    private $user_id = 0;

    /**
     * Constructor
     *
     */
    public function __construct($user_id = 0) {
        if ($user_id) {
            $this->set_user_id($user_id);
        }
    }

    /**
     * Set User id
     */
    public function set_user_id($id) {
        $this->user_id = $id;
    }

    public function get_user_id() {
        return absint($this->user_id);
    }

    public function credit($amount, $note = '', $object_id = 0, $date = '') {
        if (!$this->get_user_id()) return;

        if ($amount <= 0) {
            throw new Data_Exception('customer_rewards_invalid_amount', __('Amount must be greater than zero', 'dwrewards'), 400);
        }

        if (!$date) {
            $date = date('Y-m-d H:i:s', current_time('timestamp'));
        }

        Points_Factory::tx([
            'type'      => 'add',
            'user_id'   => $this->get_user_id(),
            'amount'    => $amount,
            'note'      => $note,
            'date'      => $date,
            'note'      => $note,
        ]);
    }

    public function spend($amount, $note = '', $object_id = 0, $date = '') {
        if (!$this->get_user_id()) return;

        if ($amount <= 0) {
            throw new Data_Exception('customer_rewards_invalid_amount', __('Amount must be greater than zero', 'dwrewards'), 400);
        }

        if ($amount > $this->get_balance()) {
            throw new Data_Exception('customer_rewards_insufficient_balance', __('insufficient balance', 'dwrewards'), 400);
        }

        if (!$date) {
            $date = date('Y-m-d H:i:s', current_time('timestamp'));
        }

        Points_Factory::tx([
            'type'      => 'sub',
            'amount'    => $amount,
            'user_id'   => $this->get_user_id(),
            'note'      => $note,
            'date'      => $date,
            'note'      => $note,
        ]);
    }

    public function sum_credits() {
        $txs = array_map(
            function($point) {
                return $point->get_amount();
            },
            Points_Factory::get_credit_transactions(['user_id' => $this->get_user_id()])
        );

        return array_sum($txs);
    }

    public function sum_spends() {
        $txs = array_map(
            function($point) {
                return $point->get_amount();
            },
            Points_Factory::get_spend_transactions(['user_id' => $this->get_user_id()])
        );

        return array_sum($txs);
    }

    public function get_balance() {
        if (!$this->get_user_id()) return;

        return $this->sum_credits() - $this->sum_spends();
    }
}
