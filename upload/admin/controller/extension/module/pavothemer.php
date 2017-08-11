<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

require_once dirname( __FILE__ ) . '/pavothemer/pavothemer.php';
require_once dirname( __FILE__ ) . '/pavothemer/helper/sample.php';

/**
 * Theme Control Controller
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

		$tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';
		// setting tabs
		$this->data['settings'] = PavoThemerSettingHelper::getSettings( $this->config->get( 'config_theme' ) );
		$this->data['current_tab'] = isset( $_REQUEST['current_tab'] ) ? $_REQUEST['current_tab'] : current( array_keys( $this->data['settings'] ) );

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
			$this->load->language( 'extension/module/pavothemer' );
			$response = array(
					'status'	=> false
				);

			$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'download';
			$data = ! empty( $_REQUEST['data'] ) ? $_REQUEST['data'] : array();
			$data = array_merge( $data, array( 'date' => false ) );
			$sampleHelper = PavoThemerSampleHelper::instance( $this->config->get( 'config_theme' ) );

			switch ( $action ) {
				case 'download':
					
					break;

				case 'install-modules':
					
					break;

				case 'import-store-settings':
					
					break;

				case 'import-theme-settings':
					
					break;

				case 'import-layout-settings':
					
					break;

				case 'import-sql':
					# code...
					break;

				default:
					# code...
					break;
			}

			$this->response->addHeader('Content-Type: application/json');
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
					'status'	=> false
				);

			$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'create-directory';
			$data = ! empty( $_REQUEST['data'] ) ? $_REQUEST['data'] : array();
			$data = array_merge( array( 'folder' => false, 'theme' => $this->config->get( 'config_theme' ) ), $data );
			$store_id = $this->config->get( 'config_store_id' );
			$sampleHelper = PavoThemerSampleHelper::instance( $data['theme'] );

			switch ( $action ) {
				// first step create new directory
				case 'create-directory':
					// create backup folder
					$folder = $sampleHelper->makeDir();
					$response = array(
							'status'	=> $folder ? true : false,
							'data'		=> array_merge( $data, array( 'folder' => $folder, 'steps' => 5 ) ),
							'text'		=> $folder ? $this->language->get( 'entry_exporting_store_config' ) : $this->language->get( 'entry_error_permission' ) . ': <strong>' . DIR_CATALOG . 'view/theme</strong>',
							'next'		=> str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'action=export-store-settings&user_token=' . $this->session->data['user_token'], true )
										)
						);
					break;

				case 'export-store-settings':
					// get store settings
					$storeSettings = $this->model_extension_pavothemer_sample->getStoreSettings();
					$status = $sampleHelper->makeStoreSettings( $storeSettings, $data['folder'] );
					$response = array(
							'status'	=> $status,
							'data'		=> $data,
							'text'		=> $status ? $this->language->get( 'entry_exporting_theme_config' ) : $this->language->get( 'entry_error_write_file' ) . ': <strong>stores.json</strong> file',
							'next'		=> str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'action=export-theme-settings&user_token=' . $this->session->data['user_token'], true )
										)
						);
					break;

				case 'export-theme-settings':
					// export store settings
					$themeSettings = $this->model_extension_pavothemer_sample->getThemeSettings( $data['folder'] );
					$status = $sampleHelper->makeThemeSettings( $themeSettings, $data['folder'] );
					$response = array(
							'status'	=> $status,
							'data'		=> $data,
							'text'		=> $this->language->get( 'entry_exporting_layout_text' ),
							'next'		=> str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'action=export-layout-settings&user_token=' . $this->session->data['user_token'], true )
										)
						);
					break;

				case 'export-layout-settings':
					$layoutSettings = $this->model_extension_pavothemer_sample->getLayoutSettings( $data['folder'] );
					$status = $sampleHelper->makeLayoutSettings( $layoutSettings, $data['folder'] );
					$response = array(
							'status'	=> $status,
							'data'		=> $data,
							'text'		=> $this->language->get( 'entry_exporting_table_text' ),
							'next'		=> str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'action=export-tables&user_token=' . $this->session->data['user_token'], true )
										)
						);
					break;

				case 'export-tables':
					$response = array(
							'status'	=> true,
							'data'		=> $data,
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
		$sample = ! empty( $_REQUEST['sample'] ) ? $_REQUEST['sample'] : '';
		$theme = ! empty( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : '';
		$this->load->language( 'extension/module/pavothemer' );
		$sampleHelper = PavoThemerSampleHelper::instance( $theme );

		$status = $sampleHelper->delete( $sample );
		$response = array(
				'status'	=> $status,
				'text'		=> $status ? $this->language->get( 'entry_delete_successfully' ) : $this->language->get( 'entry_error_permission' )
			);

		if ( $this->isAjax() ) {
			$response['table'] = $this->sampleTable();
			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $response ) );
		} else {
			if ( $status ) {
				$this->addMessage( $this->language->get( 'entry_delete_successfully' ), 'success' );
			} else {
				$this->addMessage( $this->language->get( 'entry_error_permission' ), 'success' );
			}
			$this->response->redirect( str_replace(
											'&amp;',
											'&',
											$this->url->link('extension/module/pavothemer/export', 'user_token=' . $this->session->data['user_token'], true )
										) ); exit();
		}
	}

	/**
	 * tools
	 */
	public function tools() {
		$this->toolsForm( ! empty( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'import' );
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
			$timestamp = str_replace( 'pavothemer_' . $theme . '_', '', $sample );
			$data['samples'][] = array(
					'name'			=> $sample,
					'created_at' 	=> date( 'Y-m-d H:i:s', $timestamp ),
					'created_by'	=> '',
					'delete'		=> $this->url->link( 'extension/module/pavothemer/deleteProfile', 'profile='.$sample.'&user_token=' . $this->session->data['user_token'], 'SSL' ),
					'download'		=> $this->url->link( 'extension/module/pavothemer/downloadProfile', 'profile='.$sample.'&user_token=' . $this->session->data['user_token'], 'SSL' ),
					'import'		=> $this->url->link( 'extension/module/pavothemer/import', 'profile='.$sample.'&user_token=' . $this->session->data['user_token'], 'SSL' )
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
		// START ADD USER PERMISSION
		$this->load->model('user/user_group');
		// access - modify pavothemer edit
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/edit' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/edit' );
		// access - modify pavothemer customize
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/customize' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/customize' );
		// access - modify pavothemer sampledata
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/import' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/import' );
		// export
		$this->model_user_user_group->addPermission( $this->user->getId(), 'access', 'extension/module/pavothemer/export' );
		$this->model_user_user_group->addPermission( $this->user->getId(), 'modify', 'extension/module/pavothemer/export' );
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
