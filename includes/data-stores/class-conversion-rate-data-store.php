<?php
/**
 * CustomerRewards Point Data Store class
 *
 * @package CustomerRewards
 * @since   1.0
 */

namespace Dornaweb\CustomerRewards\Data_Stores;

defined('ABSPATH') || exit;

class Conversion_Rate_Data_Store implements \Dornaweb\CustomerRewards\Interfaces\Conversion_Rate_Data_Store_Interface {
    private $valid_fields = ['currency', 'rate'];

    public function create(&$conversion_rate) {
        global $wpdb;

        $conversion_rate->apply_changes();

        $wpdb->insert(
            $wpdb->prefix . 'points_rates',
            [
                'currency'      => $conversion_rate->get_currency(),
                'rate'          => $conversion_rate->get_rate()
            ],
            [
                '%s',
                '%f',
            ]
        );

        $conversion_rate_id = absint( $wpdb->insert_id );
		return $conversion_rate_id;
    }

    /**
	 * Read a Conversion rate item from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param Point $conversion_rate Conversion rate object.
	 *
	 * @throws \Exception If invalid Conversion rate.
	 */
	public function read( &$conversion_rate ) {
		global $wpdb;

		$conversion_rate->set_defaults();

		$data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ". implode(', ', $this->valid_fields) ." FROM {$wpdb->prefix}points_rates WHERE ID = %d LIMIT 1;",
                $conversion_rate->get_id()
            )
        );

		if ( ! $data ) {
			throw new \Exception( __( 'Invalid Point.', 'dwebcr' ) );
		}

		$conversion_rate->set_props(
            array_combine(
                $this->valid_fields,
                array_map(function($field) use($data) {
                    return $data->$field;
                }, $this->valid_fields)
            )
		);
        $conversion_rate->set_object_read( true );
	}

    /**
	 * Update a conversion rate.
	 *
	 * @since 1.0.0
	 * @param Address $conversion_rate Address instance.
	 */
	public function update( &$conversion_rate ) {
        global $wpdb;

		$changes = $conversion_rate->get_changes();

        $data = [
            'currency'      => $conversion_rate->get_currency(),
            'rate'          => $conversion_rate->get_rate()
        ];

        $wpdb->update(
			$wpdb->prefix . 'points_rates',
			$data,
			array(
				'ID' => $conversion_rate->get_id(),
			)
		); // WPCS: DB call ok.

		$conversion_rate->apply_changes();
    }

	/**
	 * Remove an Conversion rate from the database.
	 *
	 * @since 1.0.0
	 * @param Point $conversion_rate  Conversion rate instance.
	 */
	public function delete( &$conversion_rate ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'points_rates',
			array(
				'ID' => $conversion_rate->get_id(),
			),
			array( '%d' )
		); // WPCS: cache ok, DB call ok.
	}

    /**
	 * Get a rate object.
	 *
	 * @param  array $data From the DB.
	 * @return \Dornaweb\CustomerRewards\Conversion_Rate
	 */
	private function get_rate( $data ) {
        if ($data->ID) {
            $data->id = $data->ID;
        }

		return new \Dornaweb\CustomerRewards\Conversion_Rate( $data );
	}

    /**
     * Get Conversion rate by currency code
     *
     * @param string $currency Currency code
     */
    public function get_rate_by_currency_name($currency) {
        global $wpdb;

		$data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ". implode(', ', array_merge(['ID'], $this->valid_fields)) ." FROM {$wpdb->prefix}points_rates WHERE currency = %s LIMIT 1;",
                $currency
            )
        );

		if ( ! $data ) {
			return false;
        }

        $conversion_rate = $this->get_rate($data);
        return $conversion_rate;
    }

    public function get_rates($args = []) {
        global $wpdb;

        $args = wp_parse_args(
			$args,
            array_merge(
                array_combine($this->valid_fields, ['', 0]),
                [
                    'return' => 'objects'
                ]
            )
		);

        $valid_fields = array_merge($this->valid_fields, ['ID']);
		$get_results_output = ARRAY_A;

        if ( 'ids' === $args['return'] ) {
			$fields = 'ID';
		} elseif ( 'objects' === $args['return'] ) {
			$fields             = '*';
			$get_results_output = OBJECT;
		} else {
			$fields = explode( ',', (string) $args['return'] );
			$fields = implode( ', ', array_intersect( $fields, $valid_fields ) );
		}

        $query = [];
        $query[] = "SELECT {$fields} FROM {$wpdb->prefix}points_rates WHERE 1=1";

        if ($args['currency']) {
            $query[] = $wpdb->prepare( 'AND currency = %s', $args['currency'] );
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( implode( ' ', $query ), $get_results_output );

        switch ( $args['return'] ) {
			case 'ids':
				return wp_list_pluck( $results, 'ID' );
			case 'objects':
				return array_map( [ $this, 'get_rate' ], $results );
			default:
				return $results;
		}
    }
}
