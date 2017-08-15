<?php

class ModelExtensionPavothemerSample extends Model {

	/**
	 * import store configs
	 */
	public function importStoreSettings( $profile = array() ) {
		$this->load->model( 'setting/setting' );
		// var_dump( $this->model_setting_setting->getSetting('config')); die();
		$stores = ! empty( $profile['stores'] ) ? $profile['stores'] : array();
		$newStores = array();
		if ( $stores ) {
			foreach ( $stores as $store ) {
				var_dump($store); die();
			}
		}
	}

	/**
	 * import theme settings
	 */
	public function importThemeSettings( $themes = array() ) {
		$this->load->model( 'setting/setting' );
		// $this->model_setting_setting->editSetting();
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
	 * import layouts
	 */
	public function importLayouts() {

	}

	/**
	 * modules required
	 */
	public function installModulesRequired( $modules = array() ) {
		$this->load->model( 'setting/extension' );
		return true;
	}

	/**
	 * install sql
	 */
	public function installSql() {

		return true;
	}

	/**
	 * get store settings for export
	 */
	public function getStoreSettings() {
		$data = array(
				'stores'			=> array(),
				'stores_settings'	=> array()
			);
		$this->load->model( 'setting/store' );
		$data['stores'] = $this->model_setting_store->getStores();

		if ( $data['stores'] ) {
			$data['stores_settings'] = array(
					$this->model_setting_setting->getSetting( 'config' )
				);
			foreach ( $data['stores'] as $store ) {
				$data['stores_settings'][$store['store_id']] = $this->model_setting_setting->getSetting( 'config', $store['store_id'] );
			}
		}

		return $data;
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
		$data = array(
				'modules'	=> array(),
				'layouts'	=> array(),
				'layout_module'	=> array()
			);
		$this->load->model( 'design/layout' );
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$sql = 'SELECT * FROM ' . DB_PREFIX . 'layout_module';
		$query = $this->db->query( $sql );
		$data['layout_module'] = $query->rows ? $query->rows : array();

		$sql = 'SELECT * FROM ' . DB_PREFIX . 'module';
		$query = $this->db->query( $sql );
		$data['modules'] = $query->rows ? $query->rows : array();

		return $data;
	}

	public function exportTables() {

	}

}
