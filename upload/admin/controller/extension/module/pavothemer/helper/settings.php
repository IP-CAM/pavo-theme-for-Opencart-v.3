<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

/**
 *
 */
class PavoThemerSettingHelper {

	/**
	 * instance instead of new ClassName
	 *
	 * @var PavoThemerSettingHelper
	 */
	private static $_instance = null;

	/**
	 * setting files
	 *
	 * @since 1.0.0
	 */
	private static $_settings = array();

	/**
	 *
	 * Get all xml data settings
	 *
	 * @param $theme - themename
	 * @return array settings page
	 */
	public static function getSettings( $theme = '' ) {
		// setting files
		$files = self::getSettingFiles( $theme );
		if ( $files ) {
			foreach ( $files as $file ) {
				$fileInfo = pathinfo( $file );
				self::$_settings[ $fileInfo['filename'] ] = self::getSettingFile( $file );
			}
		}

		return self::$_settings;
	}

	/**
	 *
	 * Default setting files
	 *
	 * @since 1.0.0
	 * @param $theme string
	 * @return array
	 */
	public static function getSettingFiles( $theme = '' ) {
		return glob( DIR_CATALOG . 'view/theme/' . $theme . '/development/settings/*.xml' );
	}

	/**
	 * get setting in single file
	 *
	 * @param file
	 * @return getXmlDomContent method as array
	 * @since 1.0.0
	 */
	public static function getSettingFile( $file = '' ) {
		if ( ! file_exists( $file ) || ! is_readable( $file ) ) {
			return array();
		}

		$data = array();
		libxml_use_internal_errors( true );
		$xml = simplexml_load_file( $file, 'SimpleXMLElement', LIBXML_NOCDATA );
		if ( $xml === false ) {
			return $data;
		}
		$data = self::getXmlDomContent( $xml );
		if ( ! empty( $data['item'] ) ) {
			$group = array();
			foreach ( $data['item'] as $item ) {
				if ( isset( $item['group'] ) ) {
					$group[ strtolower( str_replace( ' ', '-', $item['group'] ) ) ] = $item['group'];
				}
			}
			$data['group'] = array_unique( $group );
		}
		return $data;
	}

	/**
	 * 
	 * Parse xml content as array
	 *
	 * @since 1.0.0
	 * @param xml
	 * @return array
	 */
	public static function getXmlDomContent( $xml = null ) {
		if ( ! $xml ) return $xml;
		if ( is_string( $xml ) ) {
			return $xml;
		}
		if ( $xml instanceof SimpleXMLElement ) {
			$xml = get_object_vars( $xml );
		}
		$data = array();
		foreach ( $xml as $k => $notes ) {
			$subData = array();
			if ( $notes instanceof SimpleXMLElement ) {
				$notes = self::getXmlDomContent( $notes );
			}
			if ( is_array( $notes ) ) {
				$subData = array();
				foreach ( $notes as $k2 => $note ) {
					if ( $note instanceof SimpleXMLElement ) {
						$subData[$k2] = self::getXmlDomContent( $note );
					} else {
						$subData[$k2] = $note;
					}
				}
			} else {
				$subData = $notes;
			}

			$data[$k] = $subData;
		}
		return $data;
	}

}
