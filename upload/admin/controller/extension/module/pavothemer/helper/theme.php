<?php
if ( ! defined( 'DIR_SYSTEM' ) ) exit();

class PavoThemerHelper {

	private $_customizes = array();

	private static $instance = array();

	public function __construct( $theme = 'default' ) {
		$this->theme = $theme;
	}

	public static function instance( $theme = 'default' ) {
		if ( empty( self::$instance[$theme] ) ) {
			self::$instance[$theme] = new self( $theme );
		}

		return self::$instance[$theme];
	}

	/**
	 * write file
	 */
	public function writeFile( $file = '', $content = '' ) {
		if ( ! is_writable( dirname( $file ) ) ) return false;
		$fopen = fopen( $file, 'w+' );
		if ( $fopen ) {
			fwrite( $fopen, $content );
			return fclose( $fopen );
		}

		return false;
	}

	/**
	 * Get Skins
	 *
	 * @param $theme string
	 * @return array skins
	 */
	public function getSkins() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/stylesheet/skins/*.css' ), '', '.min.css' );
	}

	/**
	 *
	 * Get Css Profiles
	 *
	 * @param $theme string 'default'
	 * @return array css files 
	 * @since 1.0.0
	 */
	public function getCssProfiles() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/stylesheet/customizes/*.css' ), '', '.css' );
	}

	public function getCustomizes() {
		// setting files
		$files = $this->getCustomizeFiles( $this->theme );
		$settingHelder = PavoThemerSettingHelper::instance( $this->theme );
		if ( $files ) {
			foreach ( $files as $file ) {
				$fileInfo = pathinfo( $file );
				$this->_customizes[ $fileInfo['filename'] ] = $settingHelder->getSettingFile( $file );
			}
		}

		return $this->_customizes;
	}

	/**
	 * Get customize files
	 */
	public function getCustomizeFiles() {
		return glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/development/customizes/*.xml' );
	}

	/**
	 * Get headers layouts
	 */
	public function getHeaders() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/template/common/header*.twig' ), 'header' );
	}

	/**
	 * Get footers layouts
	 */
	public function getFooters() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/template/common/footer*.twig' ), 'footer' );
	}

	/**
	 * Get Product Detail Layouts
	 */
	public function getProductDefailLayouts() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/template/product/product*.twig' ), 'product' );
	}

	/**
	 * Get Product Detail Layouts
	 */
	public function getProductCategoryLayouts() {
		return $this->files2Options( glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/template/product/category*.twig' ), 'category' );
	}

	/**
	 * file to select options
	 */
	public function files2Options( $files = array(), $prefix = '', $ext = '.twig' ) {
		$options = array();
		if ( $files ) {
			foreach ( $files as $file ) {
				$options[] = array(
						'text'	=> implode( ' ', array_map( 'ucfirst', array_merge( array( $prefix ), array( str_replace( $prefix, '', str_replace( $ext, '', basename( $file ) ) ) ) ) ) ),
						'value'	=> str_replace( $ext, '', basename( $file ) )
					);
			}
		}
		return $options;
	}

	/**
	 * shortcodes list supported by Pavotheme
	 */
	public function getShortcodes() {
		$files = glob( DIR_CATALOG . 'view/theme/' . $this->theme . '/template/pavobuilder/*.twig' );
		$results = array();
		foreach ( $files as $file ) {
			$results[] = basename( $file, '.twig' );
		}

		return $results;
	}

}
