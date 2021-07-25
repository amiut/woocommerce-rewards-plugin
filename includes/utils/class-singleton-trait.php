<?php
/**
 * Singleton class trait.
 *
 * @package WooCommerce\Utilities
 */

namespace Dornaweb\CustomerRewards\Utils;

/**
 * Singleton trait.
 */
trait Singleton_Trait {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	final public function __wakeup() {
		die();
	}
}
