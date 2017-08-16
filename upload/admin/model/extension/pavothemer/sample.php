<?php
/**
 * EXPORTS:
 * - theme settings
 * - layout settings
 * - tables
 * - layout modules
 */
class ModelExtensionPavothemerSample extends Model {

	/**
	 * import theme settings
	 */
	public function importThemeSettings( $profile = array() ) {
		$this->load->model( 'setting/setting' );
		$settings = ! empty( $profile['themes'] ) ? $profile['themes'] : array();
		$infos = ! empty( $profile['info'] ) ? $profile['info'] : '';
		$theme = ! empty( $profile['info']['theme'] ) ? $profile['info']['theme'] : '';

		$code = 'theme_' . $theme;
		$this->model_setting_setting->editSetting( $code, $profile['theme_settings'], $this->config->get( 'config_store_id' ) );

		return true;
	}

	/**
	 * import modules
	 */
	public function importModules( $profile = array() ) {
		$this->load->model( 'setting/extension' );
		$extensions_installed = $this->model_setting_extension->getInstalled( 'module' );
		$importModules = isset( $profile[ 'extensions' ], $profile[ 'extensions' ]['modules'] ) ? $profile['extensions']['modules'] : array();

		if ( ! $importModules ) return;
		$files = glob( DIR_APPLICATION . 'controller/extension/module/*.php' );

		$data = array();
		if ( $files ) {
			foreach ($files as $file) {
				$extension = basename( $file, '.php' );
				// install action if extension is activated in backup profile
				if ( array_key_exists( $extension, $importModules ) && ! isset( $extensions_installed[$extension] ) && $importModules[$extension]['installed'] ) {
					$this->request->get['extension'] = $extension;

					// load controller
					$this->load->controller( 'extension/extension/module/install' );
				}
			}
			// unset extension request
			$this->request->get['extension'] = null;
		}
		// $this->load->controller( 'marketplace/modification/refresh' );
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
	public function installModuleRequired( $modulePath = null ) {
		if ( ! $modulePath ) return true;

		$this->session->data['install'] = token( 10 );
		$destination = DIR_UPLOAD . $this->session->data['install'] . '.tmp';
		if ( copy( $modulePath, $destination ) ) {
			$this->load->controller( 'marketplace/install/unzip' );
			ob_start();
			$output = $this->response->output();
			$result = json_decode( ob_get_clean(), true );
			if ( empty( $result['error'] ) ) {
				ob_start();
				$this->load->controller( 'marketplace/install/move' );
				ob_start();
				$output = $this->response->output();
				$result = json_decode( ob_get_clean(), true );
			}

			if ( empty( $result['error'] ) ) {
				ob_start();
				$this->load->controller( 'marketplace/install/xml' );
				ob_start();
				$output = $this->response->output();
				$result = json_decode( ob_get_clean(), true );
			}

			if ( empty( $result['error'] ) ) {
				ob_start();
				$this->load->controller( 'marketplace/install/remove' );
				ob_start();
				$output = $this->response->output();
				$result = json_decode( ob_get_clean(), true );
			}

			if ( empty( $result['error'] ) ) {
				return true;
			}

			return $result;

		}
		return true;
	}

	/**
	 * unzip modules required
	 * @param $zip file
	 */
	private function unzipModuleRequired( $zip = null ) {
		if ( ! $zip ) return false;


	}

	/**
	 * install sql
	 */
	public function installSql() {

		return true;
	}

	/**
	 * get theme settings for export
	 */
	public function getThemeSettings( $theme = '' ) {
		if ( ! $theme ) {
			$theme = $this->config->get( 'config_theme' );
		}
		$this->load->model( 'setting/setting' );
		$code = 'theme_' . $theme;
		return $this->model_setting_setting->getSetting( $code );
	}

	/**
	 * get layout settings
	 */
	public function getLayoutSettings() {
		$data = array(
				'modules'	=> array(),
				'layouts'	=> array(),
				'layout_modules'	=> array()
			);
		$this->load->model( 'design/layout' );
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$sql = 'SELECT * FROM ' . DB_PREFIX . 'layout_module';
		$query = $this->db->query( $sql );
		$data['layout_module'] = $query->rows ? $query->rows : array();

		return $data;
	}

	/**
	 * export extension modules
	 */
	public function getExtensionModules() {
		$this->load->model( 'setting/extension' );
		$this->load->model( 'setting/module' );

		$extensions = $this->model_setting_extension->getInstalled( 'module' );
		$files = glob( DIR_APPLICATION . 'controller/extension/module/*.php' );

		$data = array();
		if ( $files ) {
			foreach ($files as $file) {
				$extension = basename( $file, '.php' );

				$module_data = array();
				$modules = $this->model_setting_module->getModulesByCode( $extension );

				foreach ( $modules as $module ) {
					if ( $module['setting'] ) {
						$setting_info = json_decode( $module['setting'], true );
					} else {
						$setting_info = array();
					}

					$module_data[] = array(
						'module_id' => $module['module_id'],
						'name'      => $module['name'],
						'status'    => (isset($setting_info['status']) && $setting_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
					);
				}

				$data['modules'][ $extension ] = array(
					'name'        => $extension,
					'installed'   => in_array( $extension, $extensions ),
					'module_data' => $module_data
				);
			}
		}

		return $data;
	}

	public function exportTables() {

	}

}
