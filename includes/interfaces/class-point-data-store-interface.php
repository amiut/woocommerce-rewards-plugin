<?php
/**
 * Point Data Store Interface
 *
 * @version 1.0.0
 * @package CustomerRewards\Interface
 */

namespace Dornaweb\CustomerRewards\Interfaces;

/**
 * WC Point Data Store Interface
 *
 * Functions that must be defined by the Point data store (for functions).
 *
 * @version  1.0.0
 */
interface Point_Data_Store_Interface {

	/**
	 * Add a Point
	 *
	 * @param  array $point Order Data.
	 * @return int   Point ID
	 */
	public function create( &$point );

	/**
	 * Read a Point
	 *
	 * @param  array $point Order Data.
	 * @return int   Point ID
	 */
	public function read( &$point );

	/**
	 * Delete a Point
	 *
	 * @param  array $point Order Data.
	 * @return int   Point ID
	 */
	public function delete( &$point );

    /**
     * Query Points
     *
     * @param array $args
     * @return mixed
     */
    public function get_points_transactions($args = []);
}
