<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class LxThemeHelper {

	private static $_customizes = array();

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
		$path = DIR_CATALOG . 'view/theme/' . $theme . '/stylesheet/customize';
		$files = glob( $path . '/*.css' );
		if ( is_readable( $path ) && !empty( $files ) ) {
			foreach ( $files as $file ) {
				$fileInfo = pathinfo( $file );
				if ( ! empty( $fileInfo['filename'] ) ) {
					$cssProfiles[] = $fileInfo['filename'];
				}
			}
		}

		return $cssProfiles;
	}

	public static function getCustomizes( $theme = 'default' ) {
		// setting files
		$files = self::getCustomizeFiles( $theme );
		if ( $files ) {
			foreach ( $files as $file ) {
				$fileInfo = pathinfo( $file );
				self::$_customizes[ $fileInfo['filename'] ] = LxSettingHelper::getSettingFile( $file );
			}
		}

		return self::$_customizes;
	}

	/**
	 * Get customize files
	 */
	public static function getCustomizeFiles( $theme = 'default' ) {
		return glob( DIR_CATALOG . 'view/theme/' . $theme . '/development/customizes/*.xml' );
	}

	/**
	 * Get headers layouts
	 */
	public static function getHeaders( $theme = 'default' ) {
		return self::files2Options( glob( DIR_CATALOG . 'view/theme/' . $theme . '/template/common/header-*.twig' ) );
	}

	/**
	 * Get footers layouts
	 */
	public static function getFooters( $theme = 'default' ) {
		return self::files2Options( glob( DIR_CATALOG . 'view/theme/' . $theme . '/template/common/footer-*.twig' ) );
	}

	/**
	 * Get Product Detail Layouts
	 */
	public static function getProductDefailLayouts( $theme = 'default' ) {
		return self::files2Options( glob( DIR_CATALOG . 'view/theme/' . $theme . '/template/product/product-*.twig' ) );
	}

	/**
	 * Get Product Detail Layouts
	 */
	public static function getProductCategoryLayouts( $theme = 'default' ) {
		return self::files2Options( glob( DIR_CATALOG . 'view/theme/' . $theme . '/template/product/category-*.twig' ) );
	}

	public static function files2Options( $files = array() ) {
		$options = array();
		$options[] = array(
				'text'		=> 'Default',
				'value'		=> ''
			);
		if ( $files ) foreach ( $files as $file ) {
					$options[] = array(
							'text'	=> implode( ' ', array_map( 'ucfirst', explode( '-', str_replace( '.twig', '', basename( $file ) ) ) ) ),
							'value'	=> str_replace( '.twig', '', basename( $file ) )
						);
				}
		return $options;
	}

}
