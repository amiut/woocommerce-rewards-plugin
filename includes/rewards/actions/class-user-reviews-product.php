<?php
/**
 * Reward action base class
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Rewards\Actions;
use \Dornaweb\CustomerRewards\Data_Exception;

class User_Reviews_Product extends \Dornaweb\CustomerRewards\Rewards\Reward_Action {
    public $identifier = 'customer_reviews_product';

    /**
     * Constructor
     *
     */
    public function __construct() {
        add_action('comment_post', [$this, 'comment_approved']);
        add_action('edit_comment', [$this, 'comment_approved']);
        add_action('comment_unapproved_to_approved', [$this, 'comment_approved']);
        add_action('comment_approved_to_unapproved', [$this, 'comment_approved']);
        add_action('comment_spam_to_approved', [$this, 'comment_approved']);
        add_action('comment_approved_to_spam', [$this, 'comment_approved']);
        add_action('comment_approved_to_trash', [$this, 'comment_approved']);
        add_action('comment_trash_to_approved', [$this, 'comment_approved']);
    }

    public function comment_approved($comment_id) {
        $comment = get_comment($comment_id);
        $post_id = absint($comment->comment_post_ID);

        if (!is_user_logged_in()) return; // Ignore if guest user

        global $current_user;

        $products = [];

        $user_orders = get_posts([
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $current_user->ID,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys(wc_get_is_paid_statuses())
        ]);

        $is_done = false;

        foreach ($user_orders as $post_object) {
            $order = wc_get_order($post_object->ID);
            $earned_reward_p_ids = array_filter((array) $order->get_meta('_paid_review_reward_product_ids'));
            $pending_reward_p_ids = array_filter((array) $order->get_meta('_pending_review_reward_product_ids'));

            foreach ($order->get_items() as $item) {
                if ($item instanceof \WC_Order_Item_Product) {
                    $product_id = $item->get_product() instanceof \WC_Product_Variation ? $item->get_product()->get_parent_id() : $item->get_product()->get_id();

                    if (in_array($product_id, $earned_reward_p_ids) || $product_id !== $post_id) continue; // Ignore if already paid or product id isn't comment's post id

                    if (absint($comment->comment_approved)) {
                        $this->amount = apply_filters('dornaweb_comment_reward_amount', 2);
                        $this->trigger($order->get_user_id(), '', $comment_id);
                        $earned_reward_p_ids[] = $item->get_product_id();
                        $order->update_meta_data('_paid_review_reward_product_ids', $earned_reward_p_ids);
                    } else {
                        $pending_reward_p_ids[] = $item->get_product_id();
                        $order->update_meta_data('_pending_review_reward_product_ids', $pending_reward_p_ids);
                    }

                    $is_done = true;
                }
            }

            $order->save();

            if ($is_done) {
                break;
            }
        }
    }

    public function get_note() {
        return 'بابت نقد و بررسی محصول ' . get_the_title($this->get_object_id());
    }
}
