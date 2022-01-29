<?php
/**
 * Reward action base class
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Rewards;
use \Dornaweb\CustomerRewards\Ledger;
use \Dornaweb\CustomerRewards\Data_Exception;

class Reward_Action {
    /**
     * Identifier
     *
     */
    public $identifier = '';

    /**
     * amount
     */
    public $amount = 0;

    /**
     * Object
     */
    private $object = 0;

    /**
     * Note
     */
    private $note = '';

    /**
     * Listen to action
     */
    public function listen() {
        if (!$this->identifier) {
            throw new Data_Exception('action_identifier_not_defined', __('Identifier is not defined', 'dwrewards'), 400);
        }

        add_action("dweb_rewards_action_{$this->identifier}", [$this, 'pay'], 10, 4);
    }

    /**
     * Pay credit
     */
    public function pay($user_id, $note = false, $object_id = 0, $date = '') {
        if (!$this->get_amount()) return;

        if ($object_id) {
            $this->object_id = $object_id;
        }

        if ($note) {
            $this->note = $note;
        }

        try {
            $ledger = new Ledger($user_id);
            $ledger->credit($this->get_amount(), $this->get_note(), $this->get_object_id(), $date);
        } catch (Data_Exception $e) {
            return false;
        }
    }

    /**
     * Trigger the action
     */
    public function trigger($user_id, $note = false, $object_id = 0, $date = '') {
        do_action("dweb_rewards_action_{$this->identifier}", $user_id, $note, $object_id, $date);
    }

    public function get_note() {
        return $this->note;
    }

    public function get_object_id() {
        return absint($this->object_id);
    }

    public function get_amount() {
        return $this->amount;
    }
}
