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

	/**
	 * download
	 * @param $url url request
	 * @param $destination file destination
	 * @param $data
	 */
	public function download( $url = null, $destination = null, $data = array() ) {
		if ( ! $url || ! $destination || ! file_exists( $destination ) ) return false;

		$fopen = fopen( $destination, 'w+' );
		if ( $fopen ) {
			$ch = curl_init( $url );

			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			//Pass our file handle to cURL.
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			//Timeout if the file doesn't download after 20 seconds.
			curl_setopt( $ch, CURLOPT_TIMEOUT, 50 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			//Execute the request.
			curl_exec( $ch );
			//If there was an error, throw an Exception
			if( curl_errno( $ch ) ){
			    return false;
			}

			//Get the HTTP status code.
			$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			curl_close( $ch );

			return $status === 200 && fclose( $fopen );
		}

		return false;
	}

}