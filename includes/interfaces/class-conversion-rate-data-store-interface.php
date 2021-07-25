<?php
/**
 * Conversion Rate Data Store Interface
 *
 * @version 1.0.0
 * @package CustomerRewards\Interface
 */

namespace Dornaweb\CustomerRewards\Interfaces;

/**
 * Conversion Rate Data Store Interface
 *
 * Functions that must be defined by the Conversion Rate data store (for functions).
 *
 * @version  1.0.0
 */
interface Conversion_Rate_Data_Store_Interface {

	/**
	 * Add a Conversion Rate
	 *
	 * @param  array $conversion_rate Order Data.
	 * @return int   Conversion Rate ID
	 */
	public function create( &$conversion_rate );

	/**
	 * Read a Conversion Rate
	 *
	 * @param  array $conversion_rate Order Data.
	 * @return int   Conversion Rate ID
	 */
	public function read( &$conversion_rate );

	/**
	 * Delete a Conversion Rate
	 *
	 * @param  array $conversion_rate Order Data.
	 * @return int   Conversion Rate ID
	 */
	public function delete( &$conversion_rate );
}
