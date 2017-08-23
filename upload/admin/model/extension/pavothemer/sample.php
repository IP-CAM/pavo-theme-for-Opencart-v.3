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
					if ( $this->user->hasPermission( 'modify', 'extension/extension/module/install' ) ) {
						$this->load->controller( 'extension/extension/module/install' );
					}
				}
			}
			// unset extension request
			$this->request->get['extension'] = null;
		}
		// refresh to regenerate ocmod modification
		// $this->load->controller( 'marketplace/modification/refresh' );
	}

	/**
	 * modules required
	 */
	public function installModuleRequired( $modulePath = null ) {
		if ( ! $modulePath ) return true;

		$this->session->data['install'] = token( 10 );
		$destination = DIR_UPLOAD . $this->session->data['install'] . '.tmp';
		if ( copy( $modulePath, $destination ) ) {
			return $this->_installModule();
		}
		return true;
	}

	/**
	 * install module
	 */
	public function _installModule() {
		$steps = array(
				'marketplace/install/unzip',
				'marketplace/install/move',
				'marketplace/install/xml',
				'marketplace/install/remove'
			);

		foreach ( $steps as $step ) {
			$this->load->controller( $step );
			ob_start();
			$output = $this->response->output();
			$result = json_decode( ob_get_clean(), true );

			if ( ! empty( $result['error'] ) ) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * import layouts
	 * #1 import modules to DB_PREFIX . 'module' table
	 * #2 import layouts to DB_PREFIX . 'layout' table
	 * #3 import layout modules to DB_PREFIX . 'layout_module' table
	 * #4 import layout routes to DB_PREFIX . 'layout_route' table
	 * #5 mapping data
	 * old backup layouts, extensions, layout_modules
	 */
	public function importLayouts( $profile = array() ) {
		$this->load->model( 'setting/module' );
		$this->load->model( 'design/layout' );
		$mapping = array(
				'module_ids'	=> array(),
				'layout_ids'	=> array(),
				'layout_modules'=> array()
			);

		#1 Import modules
		$modules = isset( $profile['extensions'], $profile['extensions']['modules'] ) ? $profile['extensions']['modules'] : array();
		// $current_modules = $this->model_setting_module->getModules();
		// var_dump($current_modules); die();

		// each modules
		foreach ( $modules as $extension => $module ) {
			// each current modules
			if ( $module['data'] ) {
				$current_ex_modules = $this->model_setting_module->getModulesByCode( $extension );
				// var_dump($current_ex_modules); die();
				foreach ( $module['data'] as $data ) {
					$module_id = $data['module_id'];
					// module already exists
					// empty modules
					if ( in_array( $data, $current_ex_modules ) ) {
						$mapping['module_ids'][$module_id] = $module_id;
					} else if ( ! $current_ex_modules ) {
						$this->model_setting_module->addModule( $extension, json_decode( $data['setting'], true ) );
						$mapping['module_ids'][$module_id] = $this->db->getLastId();
					} else {
						$module_installed_id = $create_new = false;
						// module name already exists
						foreach ( $current_ex_modules as $c_mod ) {
							$compare_old = array(
									'name'	=> $data['name'],
									'code'	=> $data['code'],
									'setting'	=> $data['setting']
								);
							$compare_new = array(
									'name'		=> $c_mod['name'],
									'code'		=> $c_mod['code'],
									'setting'	=> $c_mod['setting']
								);
							if ( $compare_old === $compare_new ) {
								if ( $module_installed_id ) {
									$create_new = true;
									continue;
								}
								$mapping['module_ids'][$module_id] = $module_installed_id = $c_mod['module_id'];
							}
						}

						if ( $module_installed_id && ! $create_new ) {
							$this->model_setting_module->editModule( $module_installed_id, json_decode( $data['setting'], true ) );
						} else {
							$this->model_setting_module->addModule( $extension, json_decode( $data['setting'], true ) );
							$mapping['module_ids'][$module_id] = $this->db->getLastId();
						}
					}
				}
			}
		}

		#2 Import layouts
		$layouts = ! empty( $profile['layouts'] ) ? $profile['layouts'] : array();
		$current_layouts = $this->model_design_layout->getLayouts();
		// var_dump($layouts, $current_layouts); die();

		if ( $layouts ) {
			$new_layouts = array();
			// each backup layouts
			foreach ( $layouts as $layout_data ) {
				// old id
				$layout_id = $layout_data['layout_id'];

				// current id, current layout modules data
				$installed_layout_id = $create_new = false;
				// $installed_layout_data = array();
				// each current layout
				$excerpt_module = array(
						'layout_id'	=> $layout_id,
						'name'		=> $layout_data['name']
					);

				// import layouts
				if ( in_array( $excerpt_module, $current_layouts ) ) {
					$mapping['layout_ids'][ $layout_id ] = $installed_layout_id = $layout_id;
				} else {
					foreach ( $current_layouts as $c_layout ) {
						if ( $layout_data['name'] === $c_layout['name'] ) {
							// create new layout if their layout name is already exists many times
							if ( $installed_layout_id ) {
								$create_new = true;
								continue;
							}
							$mapping['layout_ids'][ $layout_id ] = $installed_layout_id = $c_layout['layout_id'];
							// $installed_layout_data = $this->model_design_layout->getLayoutModules( $c_layout['layout_id'] );
						}
					}

					$layout_data['layout_module'] = $layout_data['layout_modules'];

					#3. Import Layout Modules
					$layout_modules = array();
					if ( ! empty( $layout_data['layout_modules'] ) ) {
						foreach ( $layout_data['layout_modules'] as $k => $module ) {
							// var_dump($layout_id, $mapping['layout_ids']); die();
							$layout_module = array(
								'layout_id'	=> isset( $mapping['layout_ids'][$layout_id] ) ? $mapping['layout_ids'][$layout_id] : 0,
								'code'		=> 0,
								'position'	=> $module['position'],
								'sort_order'=> $module['sort_order']
							);

							$explode = explode( '.', $module['code'] );
							$module_id = count( $explode ) > 1 ? (int)end( $explode ) : $module['code'];
							if ( is_int( $module_id ) ) {
								if ( ! empty( $mapping['module_ids'][$module_id] ) ) {
									$new_module_id = $mapping['module_ids'][$module_id];
									$layout_module['code'] = str_replace( '.' . $module_id, '.' . $new_module_id, $module['code'] );
								}
							}
							$layout_modules[] = $layout_module;
						}
						// set layout modules
						$layout_data['layout_module'] = $layout_modules;
					}

					// layout routes
					$layout_data['layout_route'] = $layout_data['layout_routes'];

					// create new layout
					if ( $installed_layout_id && ! $create_new ) {
						$this->model_design_layout->editLayout( $installed_layout_id, $layout_data );
					} else {
						$mapping['layout_ids'][ $layout_id ] = $installed_layout_id = $this->model_design_layout->addLayout( $layout_data );
					}
				}

			}
		}
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
	 * layout modules
	 */
	public function getLayoutModules() {
		$sql = 'SELECT * FROM ' . DB_PREFIX . 'layout_module';
		$query = $this->db->query( $sql );
		return $query->rows;
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

				$data['modules'][ $extension ] = array(
					'name'        => $extension,
					'installed'   => in_array( $extension, $extensions ),
					'data' 		  => $modules
				);
			}
		}

		return $data;
	}

	public function exportTables() {

	}

	/**
	 * get pavo extensions paid
	 */
	public function getExtensionsPaid() {

		return array();
	}

	/**
	 * get purchased codes
	 */
	public function getPurchasedCodes() {

	}

}
