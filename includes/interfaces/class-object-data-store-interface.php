<?php
/**
 * Object Data Store Interface
 *
 * @version 1.0.0
 * @package CustomerRewards\Interface
 */

namespace Dornaweb\CustomerRewards\Interfaces;

/**
 * WC Data Store Interface
 *
 * @version  1.0.0
 */
interface Object_Data_Store_Interface {
	/**
	 * Method to create a new record of a Data based object.
	 *
	 * @param Data $data Data object.
	 */
	public function create( &$data );

	/**
	 * Method to read a record. Creates a new Data based object.
	 *
	 * @param Data $data Data object.
	 */
	public function read( &$data );

	/**
	 * Updates a record in the database.
	 *
	 * @param Data $data Data object.
	 */
	public function update( &$data );

	/**
	 * Deletes a record from the database.
	 *
	 * @param  Data $data Data object.
	 * @param  array   $args Array of args to pass to the delete method.
	 * @return bool result
	 */
	public function delete( &$data, $args = array() );

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param  Data $data Data object.
	 * @return array
	 */
	public function read_meta( &$data );

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param  Data $data Data object.
	 * @param  object  $meta Meta object (containing at least ->id).
	 * @return array
	 */
	public function delete_meta( &$data, $meta );

	/**
	 * Add new piece of meta.
	 *
	 * @param  Data $data Data object.
	 * @param  object  $meta Meta object (containing ->key and ->value).
	 * @return int meta ID
	 */
	public function add_meta( &$data, $meta );

	/**
	 * Update meta.
	 *
	 * @param  Data $data Data object.
	 * @param  object  $meta Meta object (containing ->id, ->key and ->value).
	 */
	public function update_meta( &$data, $meta );
}
