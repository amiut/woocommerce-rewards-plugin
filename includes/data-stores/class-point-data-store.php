<?php
/**
 * CustomerRewards Point Data Store class
 *
 * @package CustomerRewards
 * @since   1.0
 */

namespace Dornaweb\CustomerRewards\Data_Stores;

defined('ABSPATH') || exit;

class Point_Data_Store implements \Dornaweb\CustomerRewards\Interfaces\Point_Data_Store_Interface {
    private $valid_fields = ['object_id', 'user_id', 'type', 'date', 'amount', 'note'];

    public function create(&$point) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'points_transactions',
            [
                'object_id'     => $point->get_object_id(),
                'user_id'       => $point->get_user_id(),
                'type'          => $point->get_type(),
                'date'          => $point->get_date(),
                'amount'        => $point->get_amount(),
                'note'          => $point->get_note(),
            ],
            [
                '%d',
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
            ]
        );

        $point_id = absint( $wpdb->insert_id );
		return $point_id;
    }

    /**
	 * Read a point item from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param Point $point point object.
	 *
	 * @throws \Exception If invalid point.
	 */
	public function read( &$point ) {
		global $wpdb;

		$point->set_defaults();

		$data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ". implode(', ', $this->valid_fields) ." FROM {$wpdb->prefix}points_transactions WHERE ID = %d LIMIT 1;",
                $point->get_id()
            )
        );

		if ( ! $data ) {
			throw new \Exception( __( 'Invalid Point.', 'dwebcr' ) );
		}

		$point->set_props(
            array_combine(
                $this->valid_fields,
                array_map(function($field) use($data) {
                    return $data->$field;
                }, $this->valid_fields)
            )
		);
        $point->set_object_read( true );
	}

	/**
	 * Remove an point from the database.
	 *
	 * @since 1.0.0
	 * @param Point $point      point instance.
	 */
	public function delete( &$point ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'points_transactions',
			array(
				'ID' => $point->get_id(),
			),
			array( '%d' )
		); // WPCS: cache ok, DB call ok.
	}

    /**
	 * Get a point object.
	 *
	 * @param  array $data From the DB.
	 * @return \Dornaweb\CustomerRewards\Point
	 */
	private function get_point( $data ) {
        if ($data->ID) {
            $data->id = $data->ID;
        }

		return new \Dornaweb\CustomerRewards\Point( $data );
	}

    /**
     * Query Points
     *
     * @param array $args
     * @return mixed
     */
    public function get_points_transactions($args = []) {
        global $wpdb;

        $args = wp_parse_args(
			$args,
            array_merge(
                array_combine($this->valid_fields, [0, 0, '', '', 0, '']),
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
        $query[] = "SELECT {$fields} FROM {$wpdb->prefix}points_transactions WHERE 1=1";

        if ($args['object_id']) {
            $query[] = $wpdb->prepare( 'AND object_id = %d', absint( $args['object_id'] ) );
        }

        if ($args['user_id']) {
            $query[] = $wpdb->prepare( 'AND user_id = %d', absint( $args['user_id'] ) );
        }

        if ($args['type']) {
            $query[] = $wpdb->prepare( 'AND type = %s', $args['type'] );
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( implode( ' ', $query ), $get_results_output );

        switch ( $args['return'] ) {
			case 'ids':
				return wp_list_pluck( $results, 'ID' );
			case 'objects':
				return array_map( [ $this, 'get_point' ], $results );
			default:
				return $results;
		}
    }
}
