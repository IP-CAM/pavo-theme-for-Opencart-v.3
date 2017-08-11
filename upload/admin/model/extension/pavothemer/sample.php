<?php

class ModelExtensionPavothemerSample extends Model {

	/**
	 * import store configs
	 */
	public function importStoreSettings() {

	}

	/**
	 * import theme settings
	 */
	public function importThemeSettings() {

	}

	/**
	 * import modules
	 */
	public function importModules() {

	}

	/**
	 * install module
	 */
	public function _installModule() {

	}

	/**
	 * get store settings for export
	 */
	public function getStoreSettings() {
		$sql = 'SELECT * FROM ' . DB_PREFIX . 'setting WHERE code = "config"';

		$query = $this->db->query( $sql );

		return $query->rows;
	}

	/**
	 * get theme settings for export
	 */
	public function getThemeSettings( $theme = '' ) {
		if ( ! $theme ) {
			$theme = $this->config->get( 'config_theme' );
		}

		$code = 'theme_' . $theme;
		$sql = 'SELECT * FROM ' . DB_PREFIX . 'setting WHERE code = "'.$this->db->escape( $code ).'"';

		$query = $this->db->query( $sql );

		return $query->rows;
	}

	/**
	 * get layout settings
	 */
	public function getLayoutSettings() {

	}

}
