<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

/**
 *
 */
class PavoThemerSampleHelper {

	public static $instance = array();

	public $theme = null;
	public $sampleDir = '';

	public static function instance( $theme = '' ) {
		if ( ! isset( self::$instance[ $theme ] ) ) {
			self::$instance[ $theme ] = new self( $theme );
		}

		return self::$instance[ $theme ];
	}

	public function __construct( $theme = '' ) {
		$this->theme = $theme;
		$this->sampleDir = DIR_CATALOG . 'view/theme/' . $this->theme . '/sample/';
	}

	/**
	 * get samples backup histories inside the theme
	 */
	public function getProfiles() {
		$histories = glob( $this->sampleDir. '*' );

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
		$path = $this->sampleDir . $key . '/';
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
	 * delete backup
	 */
	public function delete( $sample = '' ) {
		if ( ! $sample ) return false;
		$dir = $this->sampleDir . $sample . '/';
		if ( $dir ) {
			return $this->deleteDirectory( $dir );
		}
		return true;
	}

	/**
	 * 
	 */
	public function deleteDirectory( $target = '' ) {
	    if( is_dir( $target ) ){
	        $files = glob( $target . '*', GLOB_MARK );

	        foreach( $files as $file ) {
	            $this->deleteDirectory( $file );
	        }

	        return rmdir( $target );
	    } elseif( is_file( $target ) ) {
	        return unlink( $target );
	    }
	}

	/**
	 * create directory
	 */
	public function makeDir() {
		// clearn folder
		$profiles = $this->getProfiles();
		if ( $profiles ) {
			foreach ( $profiles as $profile ) {
				$dir = $this->sampleDir . $profile . '/';
				if ( ! is_writable( $dir ) ) {
					chmod( $dir, 0777 );
				}
				if ( empty( glob( $dir . '*' ) ) ) {
					rmdir( $dir );
				}
			}
		}

		$folder = 'pavothemer_' . $this->theme . '_' . time();
		$path = $this->sampleDir . $folder . '';
		if ( is_dir( $path ) ) {
			return $folder;
		}
		if ( ! is_writable( dirname( $path ) ) ) return false;
		return mkdir( $path, 0777 ) ? $folder : false;
	}

	/**
	 * download sample data from pavothemes.com
	 */
	public function downloadSample() {
		require_once dirname( __FILE__ ) . '/helper/download.php';
	}

	/**
	 * create store profile
	 */
	public function makeStoreSettings( $settings = array(), $profile = '' ) {
		if ( ! $profile ) return false;

		$file = $this->sampleDir . $profile . '/stores.json';
		if ( $fo = fopen( $file, 'w+' ) ) {
			fwrite( $fo, json_encode( $settings ) );
			return fclose( $fo );
		}

		return true;
	}

	/**
	 * create theme profile
	 */
	public function makeThemeSettings( $settings = array(), $profile = '' ) {
		if ( ! $profile ) return false;

		$file = $this->sampleDir . $profile . '/themes.json';
		if ( $fo = fopen( $file, 'w+' ) ) {
			fwrite( $fo, json_encode( $settings ) );
			return fclose( $fo );
		}
		return true;
	}

	/**
	 * create layout settings
	 */
	public function makeLayoutSettings() {

		return true;
	}

}