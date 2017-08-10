<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

/**
 * Process download sample data from Pavothemes.com
 */
class PavoThemerDownloadHelper {

	public static $instance = array();

	public $theme = null;

	public static function instance( $theme = 'default' ) {
		if ( empty( self::$instance[$theme] ) ) {
			self::$instance[$theme] = new self( $theme );
		}
		return self::$instance[$theme];
	}

	public function __construct( $theme = 'default' ) {
		$this->theme = $theme;
	}

}