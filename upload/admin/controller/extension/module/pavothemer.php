<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

require_once dirname( __FILE__ ) . '/pavothemer/pavothemer.php';
require_once dirname( __FILE__ ) . '/pavothemer/helper/sample.php';

/**
 * Theme Control Controller
 * Exports:
 * 			+ theme settings
 * 			+ layouts
 * 			+ tables
 * 			+ layout module
 */
class ControllerExtensionModulePavothemer extends PavoThemerController {

	/**
	 * template file
	 *
	 * @var $template string
	 * @since 1.0.0
	 */
	public $template = 'extension/module/pavothemer/themecontrol';

	public function index() {
		$this->edit();
	}

	/**
	 * Render theme control admin layout
	 *
	 * @since 1.0.0
	 */
	public function edit() {
		// load language file
		$this->load->language('extension/module/pavothemer');
		// load setting model
		$this->load->model( 'setting/setting' );
		/**
		 * breadcrumbs data
		 */
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data['user_token'], true )
		);
		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get( 'text_extension' ),
			'href'      => $this->url->link( 'marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', 'SSL' ),
      		'separator' => ' :: '
   		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$tab = isset( $this->request->get['tab'] ) ? $this->request->get['tab'] : '';
		// setting tabs
		$this->data['settings'] = PavoThemerSettingHelper::getSettings( $this->config->get( 'config_theme' ) );
		$this->data['current_tab'] = isset( $this->request->get['current_tab'] ) ? $this->request->get['current_tab'] : current( array_keys( $this->data['settings'] ) );

		// validate and update settings
		if ( $this->request->server['REQUEST_METHOD'] === 'POST' ) {
			$validated = $this->validate();
			if ( $validated ) {
				$this->model_setting_setting->editSetting( 'pavothemer', $this->request->post, $this->config->get( 'config_store_id' ) );
			}
		}

		foreach ( $this->data['settings'] as $k => $fields ) {
			if ( isset( $fields['item'] ) ) foreach( $fields['item'] as $k2 => $item ) {
				if ( isset( $item['id'] ) ) {
					$name = 'pavothemer_' . $item['id'];
					$value = isset( $this->request->post[ $name ] ) ? $this->request->post[ $name ] : $this->config->get( $name );
					// $value = $value ? $value : ( isset( $item['default'] ) ? $item['default'] : '' );

					$label = $this->language->get( $item['label'] );
					$label = $label ? $label : ( isset( $item['label'] ) ? $item['label'] : '' );
					// override item value
					$item['value'] = $value;
					$item['label'] = $label;

					// output html render fieldsx
					$item['output'] = $this->renderFieldControl( $item );
					$this->data['settings'][$k]['item'][$k2] = $item;
				}
			}
		}

		// enqueue scripts, stylesheet needed to display editor
		$this->document->addScript( 'view/javascript/summernote/summernote.js' );
		$this->document->addScript( 'view/javascript/summernote/opencart.js' );
		$this->document->addStyle( 'view/javascript/summernote/summernote.css' );

		// just an other warning
		// $this->addMessage( 'Just an other notice', 'warning' );
		// render admin theme control template
		$this->render();
	}

	/**
	 * Customize
	 *
	 * @since 1.0.0
	 */
	public function customize() {
		// add scripts
		$this->document->addScript( 'view/javascript/pavothemer/customize.js' );
		$this->document->addStyle( 'view/stylesheet/pavothemer/customize.css' );

		$this->data['iframeURI'] = HTTPS_CATALOG;
		$this->data['themeName'] = ucfirst( implode( ' ', explode( '-', implode( ' ', explode( '_', $this->config->get( 'config_theme' ) ) ) ) ) );

		// $this->data['fields'] = $this->parseCustomizeOptions( PavoThemerHelper::getCustomizes() );
		$customizes = PavoThemerHelper::getCustomizes();
		foreach ( $customizes as $file => $customize ) {
			$this->data['fields'][$file] = $this->parseCustomizeOptions( $customize );
		}
		// foreach( $this->data['fields'] as $file => $fields ) {
		// 	foreach ( $fields['item'] as $k => $item ) {
		// 		var_dump($item);
		// 	}
		// } die();

		// load setting model
		$this->load->model( 'setting/setting' );
		$customizeOptions = $this->model_setting_setting->getSetting( 'pavothemer_customize', $this->config->get( 'store_id' ) );

		$this->data['PavoCustomizeParams'] = json_encode( $customizeOptions );
		$this->template = 'extension/module/pavothemer/customize';
		$this->render();
	}

	/**
	 * update customize
	 * @since 1.0.0
	 */
	public function updateCustomize() {

	}

	/**
	 * check is ajax request
	 */
	public function isAjax() {
		return ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';
	}

	/**
	 * Import
	 * @since 1.0.0
	 */
	public function import() {
		if ( $this->isAjax() ) {
			// load model
			$this->load->model( 'extension/pavothemer/sample' );
			$this->load->model( 'setting/extension' );
			$this->load->language( 'extension/module/pavothemer' );
			$response = array(
					'status'	=> false
				);

			$sampleHelper = PavoThemerSampleHelper::instance( $this->config->get( 'config_theme' ) );

			// required modules
			$moduleRequired = $sampleHelper->getModulesRequired();
			$this->load->model( 'setting/extension' );
			$installed_modules = $this->model_setting_extension->getInstalled( 'module' );
			$module_names = array_keys( $moduleRequired );
			$module_needed_install_count = 0;
			$modules_need_to_install = array();
			foreach ( $module_names as $name ) {
				// install required modules if its not installed
				if ( ! in_array( $name, $installed_modules ) ) {
					$module_needed_install_count++;
					$modules_need_to_install[ $name ] = $moduleRequired[ $name ];
				}
			}
			// $this->session->data['installed_module'] = false; die();
			$preInstalled = ! empty( $this->session->data['installed_module'] ) ? $this->session->data['installed_module'] : 0;
			$module = false;
			$lastest = false;
			$next = '';

			$module_names = array_keys( $modules_need_to_install );
			if ( ! $preInstalled ) {
				$module = current( $modules_need_to_install );
				$next = $module_names[1];
			} else {
				$i = $z = 0;
				foreach ( $modules_need_to_install as $key => $value ) {
					$z++;
					if ( $z == count( $modules_need_to_install ) ) {
						$lastest = $value;
					}

					if ( $i == 1 ) {
						$module = $value;
						$i++;
					} else if ( $i == 2 ) {
						$next = $key;
					}
					if ( $preInstalled == $value ) {
						$i++;
					}
				}
			}

			// action need to do
			$action = ! empty( $this->request->get['action'] ) ? $this->request->get['action'] : 'download';
			$folder = ! empty( $this->request->post['folder'] ) ? $this->request->post['folder'] : false;
			$data = ! empty( $this->request->post['data'] ) ? $this->request->post['data'] : array();
			$data = array_merge( array( 'folder' => $folder, 'steps' => 5 + $module_needed_install_count, 'action' => $action ), $data );

			// profile import
			$profile = $data['folder'] ? $sampleHelper->getProfile( $data['folder'] ) : array( 'layouts' => array(), 'themes' => array() );

			switch ( $action ) {
				case 'download':
					$status = true;
					if ( $profile ) {
						$status = true;
					}

					$response = array(
							'status'	=> $status,
							'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/import', 'action=import-theme-settings&user_token=' . $this->session->data['user_token'], true ) ),
							'text'		=> $this->language->get( 'entry_importing_theme_config' ),
							'data'		=> $data
						);
					break;

				case 'import-theme-settings':
					$response = array(
							'status'	=> $this->model_extension_pavothemer_sample->importThemeSettings( $profile ),
							'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/import', 'action=install-modules&user_token=' . $this->session->data['user_token'], true ) ),
							'data'		=> $data,
							'text'		=> $module ? $this->language->get( 'entry_installing_module' ) . ': <strong>' . ( ! empty( $module_names[0] ) ? $module_names[0] : '' ) . '</strong>' : $this->language->get( 'entry_installing_module' ),
						);
					break;

				case 'install-modules':

					$response = array(
								'status'	=> true,
								'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/import', 'action=import-sql&user_token=' . $this->session->data['user_token'], true ) ),
								'data'		=> $data,
								'text'		=> $this->language->get( 'entry_installing_table' )
							);

					$result = $this->model_extension_pavothemer_sample->installModuleRequired( $module );
					if ( $module && $lastest !== $module ) {
						if ( $result === true ) {
							$status = true;
						} else if ( is_array( $result ) ) {
							$response['status'] = ! empty( $result['error'] ) ? false : true;
							$response['status'] = ! empty( $result['success'] ) ? true : $response['status'];
							$response['text']	= $response['status'] ? $result['success'] : $result['error'];
							$response = array_merge( $response, $result );
						}
						$this->session->data['installed_module'] = $module;
						$response['next'] = str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/import', 'action=install-modules&user_token=' . $this->session->data['user_token'], true ) );
						$response['text'] = $module ? $this->language->get( 'entry_installing_module' ) . ': <strong>' . $next . '</strong>' : $this->language->get( 'entry_installing_module' );

					}

					if ( $lastest === $module || $response['status'] === false ) {
						$this->session->data['installed_module'] = false;
					}
					if ( $lastest === $module ) {
						$this->model_extension_pavothemer_sample->importModules( $profile );
					}
					break;

				case 'import-sql':
					$status =  true; //$this->model_extension_pavothemer_sample->installSql();
					$response = array(
							'status'	=> $status,
							'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/import', 'action=import-layout-settings&user_token=' . $this->session->data['user_token'], true ) ),
							'data'		=> $data,
							'text'		=> $this->language->get( 'entry_importing_layout' )
						);
					break;

				case 'import-layout-settings':
					$status = $this->model_extension_pavothemer_sample->importLayouts( $profile );
					$response = array(
							'status'	=> true, //$status,
							'data'		=> $data,
							'text'		=> $this->language->get( 'entry_import_success_text' )
						);
					break;

				default:
					$response = array(
							'status'	=> true,
							'text'		=> $this->language->get( 'entry_import_success_text' ),
							'data'		=> $data
						);
					break;
			}

			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $response ) );

		} else {
			$this->tools( 'import' );
		}
	}

	/**
	 * Export
	 * @since 1.0.0
	 */
	public function export() {
		if ( $this->isAjax() ) {
			// load model
			$this->load->model( 'extension/pavothemer/sample' );
			$this->load->language( 'extension/module/pavothemer' );
			$response = array(
					'status'	=> false,
					'data'		=> array()
				);

			$action = ! empty( $this->request->get['action'] ) ? $this->request->get['action'] : 'create-directory';
			$data = ! empty( $this->request->post['data'] ) ? $this->request->post['data'] : array();
			$theme = $this->config->get( 'config_theme' );
			$data = array_merge( array( 'folder' => false, 'theme' => $theme ), $data );
			$store_id = $this->config->get( 'config_store_id' );

			$sampleHelper = PavoThemerSampleHelper::instance( $data['theme'] );

			try {
				switch ( $action ) {
					// first step create new directory
					case 'create-directory':
						// create backup folder
						$folder = $sampleHelper->makeDir();
						$user_id = $this->session->data['user_id'];
						$this->load->model( 'user/user' );

						$user = $this->model_user_user->getUser( $user_id );
						$infoData = array(
								'email' 			=> $user['email'],
								'theme' 			=> $data['theme'],
								'store_id' 			=> $store_id,
								// 'store_settings' 	=> $store_settings
							);
						$status = $folder ? $sampleHelper->write( $infoData, $folder, 'info' ) : false;
						$response = array(
								'status'	=> $folder ? true : false,
								'data'		=> array_merge( $data, array( 'folder' => $folder, 'steps' => 5 ) ),
								'text'		=> $folder ? $this->language->get( 'entry_exporting_theme_config' ) : $this->language->get( 'entry_error_permission' ) . ': <strong>' . DIR_CATALOG . 'view/theme/'.$theme.'</strong>',
								'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/export', 'action=export-theme-settings&user_token=' . $this->session->data['user_token'], true ) )
							);
						break;

					case 'export-theme-settings':
						// export store settings
						$themeSettings = $this->model_extension_pavothemer_sample->getThemeSettings( $data['theme'] );
						$status = $sampleHelper->write( $themeSettings, $data['folder'], 'theme_settings' );
						$response = array(
								'status'	=> $status,
								'data'		=> $data,
								'text'		=> $this->language->get( 'entry_extension_module_text' ),
								'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/export', 'action=export-extensions&user_token=' . $this->session->data['user_token'], true ) )
							);
						break;

					case 'export-extensions':
						// export store settings
						$extensions = $this->model_extension_pavothemer_sample->getExtensionModules( $data['theme'] );
						$status = $sampleHelper->write( $extensions, $data['folder'], 'extensions' );
						$response = array(
								'status'	=> $status,
								'data'		=> $data,
								'text'		=> $this->language->get( 'entry_exporting_layout_text' ),
								'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/export', 'action=export-layout-settings&user_token=' . $this->session->data['user_token'], true ) )
							);
						break;

					case 'export-layout-settings':
						$layoutSettings = $this->model_extension_pavothemer_sample->getLayoutSettings( $data['folder'] );
						$status = $sampleHelper->write( $layoutSettings, $data['folder'], 'layouts' );
						$response = array(
								'status'	=> $status,
								'data'		=> $data,
								'text'		=> $this->language->get( 'entry_exporting_table_text' ),
								'next'		=> str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/export', 'action=export-tables&user_token=' . $this->session->data['user_token'], true ))
							);
						break;

					case 'export-tables':
						$tables = $sampleHelper->getTablesName();
						$sql = $this->model_extension_pavothemer_sample->exportTables( $tables );
						$response = array(
								'status'	=> true,
								'data'		=> $sampleHelper->exportSql( $sql ),
								'table'		=> $this->sampleTable(),
								'text'		=> $this->language->get( 'entry_export_success_text' )
							);
						break;

					default:
						$response = array(
								'status'	=> true,
								'table'		=> $this->sampleTable(),
								'text'		=> $this->language->get( 'entry_export_success_text' )
							);
						break;
				}

				if ( ! $response['status'] ) {
					throw new Exception();
				}
			} catch( Exception $e ) {
				if ( ! $response['status'] && isset( $response['data']['folder'] ) ) {
					$sampleHelper->delete( $response['data']['folder'] );
				}
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput( json_encode( $response ) );
		} else {
			$this->toolsForm( 'export' );
		}
	}

	/**
	 * delete backup sample
	 */
	public function delete() {
		$sample = ! empty( $this->request->post['sample'] ) ? $this->request->post['sample'] : '';
		$theme = ! empty( $this->request->post['theme'] ) ? $this->request->post['theme'] : '';
		$this->load->language( 'extension/module/pavothemer' );
		$sampleHelper = PavoThemerSampleHelper::instance( $theme );

		$status = $sampleHelper->delete( $sample );
		$response = array(
				'status'	=> $status,
				'text'		=> $status ? $this->language->get( 'entry_delete_text' ) . ' <strong>' . $sample . '</strong> ' . $this->language->get( 'entry_successfully_text' ) : $this->language->get( 'entry_error_permission' ) . ': <strong>' . DIR_CATALOG . 'view/theme/'.$theme.'/sample</strong>'
			);

		if ( $this->isAjax() ) {
			$response['table'] = $this->sampleTable();
			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $response ) );
		} else {
			if ( $status ) {
				$this->addMessage( $this->language->get( 'entry_delete_text' ) . ' <strong>' . $sample . '</strong> ' . $this->language->get( 'entry_successfully_text' ), 'success' );
			} else {
				$this->addMessage( $this->language->get( 'entry_error_permission' ), 'success' );
			}
			$this->response->redirect( str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'user_token=' . $this->session->data['user_token'], true )
										) );
			exit();
		}
	}

	/**
	 * download export
	 */
	public function download() {

		$profile = ! empty( $this->request->get['profile'] ) ? $this->request->get['profile'] : false;
		$file = false;
		if ( $profile ) {
			$sampleHelper = PavoThemerSampleHelper::instance( $this->config->get( 'config_theme' ) );
			$file = $sampleHelper->zipProfile( $profile );
		}

		if ( $this->isAjax() ) {
			$response = array(
					'status' 	=> $file ? true : false,
					'url'		=> str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/download', 'profile=' . $profile . '&user_token=' . $this->session->data['user_token'], true )
										)
				);
			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $response ) );
		} else {

			if ( ! $file ) {
				$this->response->redirect( str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/tools', 'user_token=' . $this->session->data['user_token'], true ) ) );
				exit();
			} else {
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="'. basename( $file ) .'"');
				header('Content-Length: '.filesize( $file ) );
				readfile( $file ); exit();
			}
		}
	}

	/**
	 * tools
	 */
	public function tools() {
		$this->toolsForm( ! empty( $this->request->get['tab'] ) ? $this->request->get['tab'] : 'import' );
	}

	public function toolsForm( $tab = 'import' ) {
		// load language file
		$this->load->language('extension/module/pavothemer');
		// load setting model
		$this->load->model( 'setting/setting' );
		/**
		 * breadcrumbs data
		 */
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data['user_token'], true )
		);
		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get( 'menu_tool_text' ),
			'href'      => $this->url->link( 'extension/module/pavothemer/edit', 'user_token=' . $this->session->data['user_token'], 'SSL' ),
      		'separator' => ' :: '
   		);
		$this->data['current_tab'] = $tab;
		$this->data['import_ajax_url'] = $this->url->link( 'extension/module/pavothemer/import', 'user_token=' . $this->session->data['user_token'], 'SSL' );
		$this->data['export_ajax_url'] = str_replace( '&amp;', '&', $this->url->link( 'extension/module/pavothemer/export', 'user_token=' . $this->session->data['user_token'], true ) ); //, 'user_token=' . $this->session->data['user_token'], 'SSL'
		$this->data['delete_export_url'] = str_replace( '&amp;', '&', $this->url->link('extension/module/pavothemer/delete', 'user_token=' . $this->session->data['user_token'], true ) );
		$this->data['token'] = $this->session->data['user_token'];
		$this->data['sample_histories_table'] = $this->sampleTable();

		$this->template = 'extension/module/pavothemer/tool';
		$this->render();
	}

	/**
	 * print samples table
	 */
	private function sampleTable() {
		$theme = $this->config->get( 'config_theme' );
		$sampleHelper = PavoThemerSampleHelper::instance( $theme );
		$samples = $sampleHelper->getProfiles();
		$data = array();
		$data['samples'] = array();
		foreach ( $samples as $sample ) {
			$name = basename( $sample );
			$timestamp = str_replace( 'pavothemer_' . $theme . '_', '', $name );
			$profile = $sampleHelper->getProfile( $sample );
			$data['samples'][] = array(
					'name'			=> $sample,
					'created_at' 	=> date( 'Y-m-d H:i:s', $timestamp ),
					'created_by'	=> isset( $profile['info'], $profile['info']['email'] ) ? $profile['info']['email'] : '',
					'delete'		=> $this->url->link( 'extension/module/pavothemer/delete', 'profile='.$name.'&user_token=' . $this->session->data['user_token'], 'SSL' ),
					'download'		=> $this->url->link( 'extension/module/pavothemer/download', 'profile='.$name.'&user_token=' . $this->session->data['user_token'], 'SSL' ),
					'import'		=> $this->url->link( 'extension/module/pavothemer/import', 'profile='.$name.'&user_token=' . $this->session->data['user_token'], 'SSL' )
				);
		}
		$data['theme'] = $theme;
		return $this->load->view( 'extension/module/pavothemer/sampletable', $data );
	}

	/**
	 * Validate post form
	 *
	 * @since 1.0.0
	 */
	public function validate() {

		$has_permision = $this->user->hasPermission( 'modify', 'extension/module/pavothemer' );
		if ( ! $has_permision ) {
			$this->errors['warning'] = $this->language->get( 'error_permision' );
		} else {
			foreach ( $this->data['settings'] as $k => $fields ) {
				if ( isset( $fields['item'] ) ) foreach( $fields['item'] as $k2 => $item ) {
					if ( isset( $item['id'] ) ) {
						if ( isset( $item['required'] ) && $item['required'] && empty( $this->request->post[ 'pavothemer_' . $item['id'] ] ) ) {
							$this->errors[ $item['id'] ] = $this->language->get( 'error_' . $item['id'] );
						}
					}
				}
			}
		}
		return ! $this->errors;
	}

	/**
	 * Render html field for input, textarea, ...
	 *
	 * @since 1.0.0
	 * @return mixed html
	 */
	private function renderFieldControl( $item = array() ) {
		if ( empty( $item['type'] ) ) return;
		$theme = $this->config->get( 'config_theme' );
		$type = 'input';
		switch ( $item['type'] ) {
			case 'select_theme':
				# code...
				break;

			case 'select_store':
				# code...
				break;

			case 'select_header':
				$item['option'] = PavoThemerHelper::getHeaders( $theme );
				$type = 'select';
				break;

			case 'select_footer':
				$item['option'] = PavoThemerHelper::getFooters( $theme );
				$type = 'select';
				break;

			case 'select_product_layout':
				$item['option'] = PavoThemerHelper::getProductDefailLayouts( $theme );
				$type = 'select';
				break;

			case 'select_category_layout':
				$item['option']= PavoThemerHelper::getProductCategoryLayouts( $theme );
				$type = 'select';
				break;
			case 'text':
			case 'email':
			case 'tel':
			case 'password':
					$type = 'input';
				break;
			case 'custom_css':
			case 'custom_js':
			case 'textarea':
			case 'summernote':
			case 'editor':
					$type = 'textarea';
				break;
			case 'style_profile':
					$type = 'select';
					$styleProfiles = PavoThemerHelper::getCssProfiles( $theme );
					$item['option'][] = array(
							'text'	=> 'None',
							'value' => ''
						);
					if ( $styleProfiles ) foreach ( $styleProfiles as $profile ) {
						$item['option'][] = array(
								'text'	=> $profile,
								'value'	=> $profile
							);
					}
				break;
			case 'link':
					$type = 'link';
					$url = $this->url->link('extension/module/pavothemer/customize', 'user_token=' . $this->session->data['user_token'], 'SSL');
					$item['url'] = $url;
				break;
			case 'select_font':
					$item['options'] = array(
							array(
									'text'	=> 'xxx',
									'value'		=> 'xxx'
								),
							array(
									'text'	=> 'yyy',
									'value'		=> 'yyy'
								),
							array(
									'text'	=> 'zzz',
									'value'		=> 'zzz'
								)
						);
				break;

			default:
				# code...
					$type = $item['type'];
				break;
		}

		$item['name'] = strpos( $item['id'], 'pavothemer_' ) == false ? 'pavothemer_' . $item['id'] : $item['id'];
		$item['class'] = 'form-control' . ( isset( $item['class'] ) ? ' ' .trim( $item['class'] ) : '' );
		// return html
		return $this->load->view( 'extension/module/pavothemer/fields/' . $type, array( 'item' => $item ) );
	}

	public function parseCustomizeOptions( $fields = array() ) {
		if ( empty( $fields['item'] ) ) {
			$fields['output'] = $this->renderFieldControl( $fields );
		} else {
			foreach ( $fields['item'] as $k => $item ) {
				if ( empty( $item['id'] ) ) continue;
				if ( ! empty( $item['item'] ) ) {
					$item = $this->parseCustomizeOptions( $item );
					$fields['item'][$k] = $item;
				} else {
					$value = $this->config->get( 'pavothemer_customize_' . $item['id'] );
					$item['value'] = $value ? $value : ( isset( $item['default'] ) ? $item['default'] : '' );
					$item['output'] = $this->renderFieldControl( $item );
				}
				$fields['item'][$k] = $item;
			}
		}

		return $fields;
	}

	/**
	 * Insert default pavothemer values to setting table
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function install() {
		$this->load->model('setting/extension');
		$this->model_setting_extension->install('module', $this->request->get['extension']);
		// START ADD USER PERMISSION
		$this->load->model('user/user_group');
		// access - modify pavothemer edit
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/edit' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/edit' );
		// access - modify pavothemer customize
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/customize' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/customize' );
		// access - modify pavothemer sampledata
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/tools' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/tools' );
		// END ADD USER PERMISSION

		$settingFields = PavoThemerSettingHelper::getSettings( $this->config->get('config_theme') );
		$this->load->model( 'setting/setting' );
		$settings = array();

		// get option if it already activated before
		$defaultOptions = array();
		foreach ( $settingFields as $tab => $fields ) {
			if ( empty( $fields['item'] ) ) continue;
			foreach ( $fields['item'] as $item ) {
				if ( ! isset( $item['id'], $item['default'] ) ) continue;
				// get default options
				if ( ! $this->config->get( 'pavothemer_' . $item['id'] ) ) {
					$defaultOptions[ 'pavothemer_' . $item['id'] ] = $item['default'];
				}
			}
		}

		// insert default option values
		$this->model_setting_setting->editSetting( 'pavothemer', $defaultOptions, $this->config->get( 'config_store_id' ) );
	}

	/**
	 * Uninstall action
	 * @since 1.0.0
	 */
	public function uninstall() {
		// START REMOVE USER PERMISSION
		$this->load->model('user/user_group');
		// access - modify pavothemer edit
		$this->model_user_user_group->removePermission( $this->user->getId(), 'access', 'extension/module/pavothemer/edit' );
		$this->model_user_user_group->removePermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/edit' );
		// access - modify pavothemer customize
		$this->model_user_user_group->removePermission( $this->user->getId(), 'access', 'extension/module/pavothemer/customize' );
		$this->model_user_user_group->removePermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/customize' );
		// access - modify pavothemer sampledata
		$this->model_user_user_group->removePermission( $this->user->getId(), 'access', 'extension/module/pavothemer/import' );
		$this->model_user_user_group->removePermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/import' );
		// export
		$this->model_user_user_group->removePermission( $this->user->getId(), 'access', 'extension/module/pavothemer/export' );
		$this->model_user_user_group->removePermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/export' );
		// END REMOVE USER PERMISSION
	}

}
