<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class PavoThemerHelperImporter {

	/**
	 * instance class
	 */
	private static $instance = null;

	/**
	 * get instance class
	 */
	public static function getInstance( $theme = 'default' ) {
		if ( ! isset( self::$instance[$theme] ) ) {
			self::$instance[$theme] = new self();
		}
		return self::$instance[$theme];
	}


}