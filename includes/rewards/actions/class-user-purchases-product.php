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

        $already_paid_for_products = array_filter((array) $order->get_meta('_paid_reward_product_ids'));

        foreach ($items as $item) {
            if (in_array($item->get_product_id(), $already_paid_for_products)) continue; // Skip if already paid

            $product_id = $item->get_product() instanceof \WC_Product_Variation ? $item->get_product()->get_parent_id() : $item->get_product_id();
            $this->amount = absint(get_post_meta($product_id, 'cccoin_after_buy', true));

            $this->trigger($order->get_user_id(), '', $product_id);
            $already_paid_for_products[] = $item->get_product_id();
            $order->update_meta_data('_paid_reward_product_ids', $already_paid_for_products);
        }

        $order->save();
    }

    public function get_note() {
        return 'بابت خرید ' . get_the_title($this->get_object_id());
    }
}
