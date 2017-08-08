<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class PavoThemerHelperExporter {

	/**
	 * instance class
	 */
	private static $instance = null;

	private $themePath = null;

	/**
	 * get instance class
	 */
	public static function getInstance( $theme = 'default' ) {
		if ( ! isset( self::$instance[$theme] ) ) {
			self::$instance[$theme] = new self( $theme );
		}
		return self::$instance[$theme];
	}

	/**
	 * init constructor
	 */
	public function __construct( $theme = 'default' ) {
		$this->themePath = DIR_CATALOG . 'view/theme/' . $theme . '/';
	}

}