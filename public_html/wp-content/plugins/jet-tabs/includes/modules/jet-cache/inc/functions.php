<?php
/**
 * @param $transient
 * @param $value
 * @param int $expiration
 *
 * @return mixed
 */
function jet_set_transient( $key = false, $data = false, $expiration = 0, $source_id = 0, $source = 'default' ) {
	return \Jet_Cache\Manager::get_instance()->db_manager->set_cache( $key, $data, $expiration, $source_id, $source );
}

/**
 * @param $transient
 *
 * @return false|mixed
 */
function jet_get_transient( $key = false, $default = false ) {
	return \Jet_Cache\Manager::get_instance()->db_manager->get_cache( $key, $default );
}