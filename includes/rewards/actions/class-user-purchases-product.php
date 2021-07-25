<?php
/**
 * Reward action base class
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Rewards\Actions;
use \Dornaweb\CustomerRewards\Data_Exception;

class User_Purchases_Product extends \Dornaweb\CustomerRewards\Rewards\Reward_Action {
    public $identifier = 'customer_purchases_product';
    public $amount = 10;

    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action('woocommerce_order_status_completed', [$this, 'order_completed']);
    }

    public function order_completed($order_id) {
        $order = wc_get_order($order_id);
        $items = array_filter($order->get_items(), function($item) {
            return $item instanceof \WC_Order_Item_Product;
        });

        foreach ($items as $item) {
            $this->trigger($order->get_user_id(), '', $item->get_product_id());
        }
    }

    public function get_note() {
        return 'بابت خرید ' . get_the_title($this->get_object_id());
    }
}
