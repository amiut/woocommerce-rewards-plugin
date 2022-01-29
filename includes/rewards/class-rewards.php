<?php
/**
 * Rewards
 *
 * @package CustomerRewards\Rewards
 */

namespace Dornaweb\CustomerRewards\Rewards;
use \Dornaweb\CustomerRewards\Utils\Singleton_Trait;

defined( 'ABSPATH' ) || exit;


/**
 * Rewards Triggers
 */
class Rewards {
	use Singleton_Trait;

	/**
	 * actions
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Hook actions.
	 */
	public function init() {
        // $this->register_actions();
		add_action( 'init', array( $this, 'register_actions' ), 1 );
	}

	/**
	 * Register Reward Actions.
	 */
	public function register_actions() {
		foreach ( $this->get_actions() as $name => $class_name ) {
            $this->actions[$name] = new $class_name();
            $this->actions[$name]->listen();
		}
	}

	/**
	 * List of actions
	 *
	 * @return array
	 */
	protected function get_actions() {
		return apply_filters('dweb_customer_reward_actions', array(
            'customer_purchases_product' => '\\Dornaweb\\CustomerRewards\\Rewards\\Actions\\User_Purchases_Product',
            'customer_reviews_product' => '\\Dornaweb\\CustomerRewards\\Rewards\\Actions\\User_Reviews_Product',
		));
	}
}
