<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class LxThemeHelper {

	/**
	 * Get Skins
	 *
	 * @param $theme string
	 * @return array skins
	 */
	public static function getSkins( $theme = 'default' ) {
		$skins = array();
		$path = DIR_CATALOG . 'view/theme/'.$theme.'/stylesheet/skins';
		$files = glob( $path . '/*.css' );

		if( is_readable( $path ) && ! empty( $files ) ){
			foreach( $directories as $dir ){
				$fileInfo = pathinfo( $file );
				if ( ! empty( $fileInfo['filename'] ) ) {
					$skins[] = $fileInfo['filename'];
				}
			}
		}

		return $skins;
	}

	/**
	 *
	 * Get Css Profiles
	 *
	 * @param $theme string 'default'
	 * @return array css files 
	 * @since 1.0.0
	 */
	public static function getCssProfiles( $theme = 'default' ) {
		$cssProfiles = array();
		$path = DIR_CATALOG . 'view/theme/'.$theme.'/stylesheet/customize';
		$files = glob( $path . '/*.css' );
		if ( is_readable( $path ) &&! empty( $files ) ) {
			foreach ( $files as $file ) {
				$fileInfo = pathinfo( $file );
				if ( ! empty( $fileInfo['filename'] ) ) {
					$cssProfiles[] = $fileInfo['filename'];
				}
			}
		}

		return $cssProfiles;
	}

}
