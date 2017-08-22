<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class PavothemerApiHelper {

	public static $error = '';
	public static $errno = '';

	/**
	 * get request
	 */
	public static function get( $url = '', $data = array() ) {
		$data['method'] = 'GET';
		return self::request( $url, $data );
	}

	/**
	 * post data
	 */
	public static function post( $data = array() ) {
		$data['method'] = 'POST';
		return self::request( $url, $data );
	}

	/**
	 * make request to api host
	 */
	public static function request( $url = '', $data = array() ) {

		$data = array_merge( array(
				'method'		=> 'GET',
				'headers'		=> array(),
				'body'			=> array(),
				'timeout'		=> 30,
				'user-agent'	=> '',
				'httpversion'	=> '1.0'
			), $data );

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_TIMEOUT, $data['timeout'] );
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $data['timeout'] );

		if ( ! empty( $data['user-agent'] ) ) {
			curl_setopt( $curl, CURLOPT_USERAGENT, $data['user-agent'] );
		}
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

		$method = strtolower( $data['method'] );
		switch ( $method ) {
			case 'post':
					$data = http_build_query( $data['body'] );
					curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
					curl_setopt( $curl, CURLOPT_POST, count( $data ) );
				break;

			case 'put':
					curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
					curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				break;

			case 'delete':
				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
			break;

			default:
					curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $data['method'] );
					if ( ! is_null( $data['body'] ) ) {
						curl_setopt( $curl, CURLOPT_POSTFIELDS, $data['body'] );
					}
				break;
		}

		if ( $data['httpversion'] == '1.0' )
			curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		else
			curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		if ( ! empty( $data['headers'] ) ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $data['headers'] );
		}

		$output = curl_exec( $curl );
		self::$error = curl_error( $curl );
		self::$errno = curl_errno( $curl );

		$results = array(
				'response'	=> array(
						'code'		=> curl_getinfo( $curl, CURLINFO_HTTP_CODE ),
						'message'	=> self::$errno ? self::$error : ''
					),
				'body'		=> $output
			);
echo $output; die();
		return $results;
	}

}