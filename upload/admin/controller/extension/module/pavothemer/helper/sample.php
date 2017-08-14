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
		$file = $this->sampleDir . $key . '/profile.json';
		return file_exists( $file ) ? json_decode( $file, true ) : array();
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
	 * write file
	 */
	public function write( $settings = array(), $profile = '', $type = '' ) {
		if ( ! $profile ) return false;
		$file = $this->sampleDir . $profile . '/profile.json';
		$content = file_exists( $file ) ? file_get_contents( $file ) : array();
		$content = $content ? json_decode( $content, true ) : array();

		$content[$type] = $settings;
		if ( $fo = fopen( $file, 'w+' ) ) {
			fwrite( $fo, json_encode( $content ) );
			return fclose( $fo );
		}
		return true;
	}

	/**
	 * tables need to export
	 */
	public function getTablesName() {

		return array();
	}

	/**
	 * export sql file
	 */
	public function exportSql( $sqlString = '', $profile = '' ) {
		if ( ! $profile ) return false;
		$file = $this->sampleDir . $profile . '/' . $this->theme . '.sql';

		return true;
	}

	/**
	 * load modules requireds
	 */
	public function getModulesRequired() {
		$dir = dirname( $this->sampleDir ) . '/modules';
		$modules = glob( $dir . '/*.ocmod.zip' );
		$data = array();
		if ( is_dir( $dir ) && ! empty( $modules ) ) {
			foreach ( $modules as $module ) {
				$name = basename( $module, '.ocmod.zip' );
				$data[$name] = $module;
			}
		}
		return $data;
	}

	/**
	 * zip profile to download
	 */
	public function zipProfile( $profile = '' ) {
		$folder = $this->sampleDir . $profile;
		$filename = $profile . '.zip';
		// backup before
		if ( file_exists( DIR_DOWNLOAD . $filename ) ) {
			// return DIR_DOWNLOAD . $filename;
		}
		$zip = $this->zip( $folder, DIR_DOWNLOAD . $filename );
		if ( $zip ) {
			return DIR_DOWNLOAD . $filename;
		}

		return false;
	}

	/**
	 * create zip file
	 */
	public function zip( $source = '', $destination = '' ) {
	    if ( ! extension_loaded( 'zip' ) || ! file_exists( $source ) ) {
	        return false;
	    }

	    $zip = new ZipArchive();
	    if ( ! $zip->open( $destination, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
	        return false;
	    }

	    $source = str_replace( '\\', '/', realpath( $source ) );

	    if ( is_dir( $source ) === true ) {
	        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );

	        foreach ( $files as $file ) {
	            $file = str_replace('\\', '/', $file);

	            // Ignore "." and ".." folders
	            if ( in_array( substr( $file, strrpos( $file, '/' ) + 1 ), array( '.', '..' ) ) ) {
	                continue;
	            }

	            $file = realpath( $file );

	            if ( is_dir( $file ) === true ) {
	                $zip->addEmptyDir( str_replace( $source . '/', '', $file . '/' ) );
	            } elseif ( is_file( $file ) === true ) {
	                $zip->addFromString( str_replace( $source . '/', '', $file ), file_get_contents( $file ) );
	            }
	        }
	    } elseif ( is_file( $source ) === true ) {
	        $zip->addFromString( basename( $source ), file_get_contents( $source ) );
	    }

	    return $zip->close();
	}

}