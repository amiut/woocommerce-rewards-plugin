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
class Conversion_Rate extends Data {
    /**
	 * Order Data array. This is the core order data exposed in APIs since 1.0.0.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
        'currency'      => '',
        'rate'          => 0,
	);


    /**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'conversion-rates';

    /**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'conversion-rate';

    /**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'conversion-rate';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $conversion_rate ID to load from the DB, or Point object.
	 */
	public function __construct( $conversion_rate = 0 ) {
		parent::__construct( $conversion_rate );

		if ( $conversion_rate instanceof Point ) {
			$this->set_id( $conversion_rate->get_id() );
		} elseif ( is_numeric( $conversion_rate ) && $conversion_rate > 0 ) {
			$this->set_id( $conversion_rate );
		} elseif ( ! empty( $conversion_rate->ID ) ) {
			$this->set_id( absint( $conversion_rate->ID ) );
        }

		$this->data_store = Data_Store::load( 'conversion-rate' );

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
    public function get_currency($context = 'view') {
        return $this->get_prop('currency', $context);
    }

    /**
     *
     * @param string $context View or Edit context
     * @return
     */
    public function get_rate($context = 'view') {
        return floatval($this->get_prop('rate', $context));
    }

    /**
     * Set Currency
     *
     * @param int $currency
     */
    public function set_currency($currency = '') {
        $this->set_prop( 'currency', $currency);
    }

    /**
     * Set rate
     *
     * @param string $rate
     */
    public function set_rate($rate = 0) {
        $this->set_prop( 'rate', floatval($rate) );
    }
}
