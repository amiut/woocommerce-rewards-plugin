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
class Point extends Data {
    /**
	 * Order Data array. This is the core order data exposed in APIs since 1.0.0.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
        'object_id'     => 0,
        'user_id'       => 0,
        'type'          => '',
        'date'          => '',
        'amount'        => 0,
        'note'          => '',
	);


    /**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'points';

    /**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'point';

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'point';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $point ID to load from the DB, or Point object.
	 */
	public function __construct( $point = 0 ) {
		parent::__construct( $point );

		if ( $point instanceof Point ) {
			$this->set_id( $point->get_id() );
		} elseif ( is_numeric( $point ) && $point > 0 ) {
			$this->set_id( $point );
		} elseif ( ! empty( $point->ID ) ) {
			$this->set_id( absint( $point->ID ) );
        }

		$this->data_store = Data_Store::load( 'point' );

		// If we have an ID, load the point from the DB.
		if ( $this->get_id() ) {
			try {
				$this->data_store->read( $this );
			} catch ( \Exception $e ) {
				$this->set_id( 0 );
				$this->set_object_read( true );
			}
		} else {
			$this->set_object_read( true );
		}
	}

	/**
	 * Merge changes with data and clear.
	 * Overrides DATA::apply_changes.
	 * array_replace_recursive does not work well for order items because it merges taxes instead
	 * of replacing them.
	 *
	 * @since 3.2.0
	 */
	public function apply_changes() {
		if ( function_exists( 'array_replace' ) ) {
			$this->data = array_replace( $this->data, $this->changes ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_replaceFound
		} else { // PHP 5.2 compatibility.
			foreach ( $this->changes as $key => $change ) {
				$this->data[ $key ] = $change;
			}
		}
		$this->changes = [];
	}

    public function exists() {
        return $this->get_id() > 0;
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_object_id($context = 'view') {
        return absint($this->get_prop('object_id', $context));
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_user_id($context = 'view') {
        return absint($this->get_prop('user_id', $context));
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_date($context = 'view') {
        return $this->get_prop('date', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_amount($context = 'view') {
        return floatval($this->get_prop('amount', $context));
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_type($context = 'view') {
        return $this->get_prop('type', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_note($context = 'view') {
        return $this->get_prop('note', $context);
    }

    /**
     *
     * @return int
     */
    public function get_product_id() {
        return get_post_type($this->get_object_id()) === 'product' ? $this->get_object_id() : false;
    }

    /**
     *
     * @return WC_Product
     */
    public function get_product() {
        $product_id = wp_get_post_parent_id($this->get_object_id()) ? wp_get_post_parent_id($this->get_object_id()) : $this->get_object_id();
        return get_post_type($product_id) === 'product' && function_exists('wc_get_product') ? wc_get_product($product_id) : false;
    }

    /**
     * Get post object
     *
     * @return WP_Post
     */
    public function get_post() {
        return $this->get_object_id() && get_post($this->get_object_id()) ? get_post($this->get_object_id()) : false;
    }

    /**
     * Set object id
     *
     * @param int $object_id
     */
    public function set_object_id($object_id = 0) {
        $this->set_prop( 'object_id', absint($object_id));
    }

    /**
     * Set product id
     * Alias of set_object_id
     *
     * @param int $product_id
     */
    public function set_product_id($product_id = 0) {
        $this->set_object_id($product_id);
    }

    /**
     * Set list id
     *
     * @param string $user_id
     */
    public function set_user_id($user_id = 0) {
        $this->set_prop( 'user_id', absint($user_id) );
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function set_type($type = '') {
        $this->set_prop( 'type', $type );
    }

    /**
     * Set date
     *
     * @param string $date
     */
    public function set_date($date = '') {
        $this->set_prop( 'date', $date );
    }

    /**
     * Set amount
     *
     * @param string $amount
     */
    public function set_amount($amount = '') {
        $this->set_prop( 'amount', $amount );
    }

    /**
     * Set note
     *
     * @param string $note
     */
    public function set_note($note = '') {
        $this->set_prop( 'note', $note );
    }
}
