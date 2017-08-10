<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

/**
 *
 */
class PavoThemerSampleHelper {

	public static $instance = array();

	public $theme = null;

	public static function instance( $theme = '' ) {
		if ( ! isset( self::$instance[ $theme ] ) ) {
			self::$instance[ $theme ] = new self( $theme );
		}

		return self::$instance[ $theme ];
	}

	public function __construct( $theme = '' ) {
		$this->theme = $theme;
	}

	/**
	 * get samples backup histories inside the theme
	 */
	public function getProfiles() {
		$histories = glob( DIR_CATALOG . 'view/' . $this->theme . '/sample/*' );
		$sampleHistories = array();
		foreach ( $histories as $history ) {
			$history = basename( $history );
			if ( strpos( $history, '.' ) === false ) {
				$sampleHistories[] = $history;
			}
		}

		return $sampleHistories;
	}

	/**
	 * get single sample profile
	 */
	public function getProfile( $key = '' ) {
		$path = DIR_CATALOG . 'view/theme/' . $this->theme . '/sample/' . $key . '/';
		$infoFile = $path . 'data.json';
		$modulesPath = $path . 'modules/';

		if ( ! is_dir( $path ) || ! file_exists( $infoFile ) ) return false;

		$data = array(
				'modules'	=> array(),
				'settings'	=> array(),
				'sql'		=> array()
			);
		$data['info'] = file_get_contents( $infoFile );
		$data['modules'] = array();
		$modules = array();
		if ( is_dir( $modulesPath ) ) {
			$modules = glob( $modulesPath . '*' );

		}

		$data['modules'] = $modules;
	}

	/**
	 * create directory
	 */
	public function makeDir() {
		$date = date( 'Y-m-d_H:i:s' );
		$path = DIR_CATALOG . 'view/theme/' . $this->theme . '/sample/' . $date . '';
		if ( is_dir( $path ) ) {
			return $date;
		}
		if ( ! is_writable( dirname( $path ) ) ) return false;
		return mkdir( $path, 0777 );
	}

	/**
	 * create store profile
	 */
	public function makeStoreSettings() {

		return true;
	}

	/**
	 * create theme profile
	 */
	public function makeThemeSettings() {

		return true;
	}

	/**
	 * create layout settings
	 */
	public function makeLayoutSettings() {

		return true;
	}

}