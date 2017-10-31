<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

require_once DIR_CATALOG . 'controller/extension/module/pavobuilder/pavobuilder.php';
/**
 * Homebuilder Controller
 */
class ControllerExtensionModulePavobuilder extends Controller {

	/**
	 * data pass to view
	 */
	private $data = array();
	private $pavobuilder = null;

	public function __construct( $registry ) {
		parent::__construct( $registry );

		/**
		 * pavobuilder object
		 */
		$this->pavobuilder = PavoBuilder::instance( $registry );
	}

	public function index() {
		$this->load->language( 'extension/module/pavobuilder' );

		if ( ! empty( $this->request->get['module_id'] ) ) {
			$this->edit();
		} else {
			$this->load->model( 'setting/module' );
			$this->load->language( 'extension/module/pavobuilder' );
			/**
			 * breadcrumbs data
			 */
			$this->data['breadcrumbs'] = array();
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get( 'text_home' ),
				'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data['user_token'], true )
			);
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/pavobuilder', 'user_token=' . $this->session->data['user_token'], true)
			);
			$this->data['create_new_module_url'] = $this->url->link( 'extension/module/pavobuilder/add', 'user_token=' . $this->session->data['user_token'], true );
			$this->data['layouts'] = $this->model_setting_module->getModulesByCode( 'pavobuilder' );
			foreach ( $this->data['layouts'] as $k => $layout ) {
				$this->data['layouts'][$k]['edit_link'] = $this->url->link( 'extension/module/pavobuilder', 'module_id=' . $layout['module_id'] . '&user_token=' . $this->session->data['user_token'], true );
			}
			$this->data['delete_url'] = str_replace( '&amp;', '&', $this->url->link( 'extension/module/pavobuilder/delete', 'user_token=' . $this->session->data['user_token'], true ) );
			// set page document title
			if ( $this->language && $this->document ) $this->document->setTitle( $this->language->get( 'heading_title' ) );

			//
			$this->data['errors'] = $this->errors;
			$this->data = array_merge( array(
				'header'		=> $this->load->controller( 'common/header' ),
				'column_left' 	=> $this->load->controller( 'common/column_left' ),
				'footer'		=> $this->load->controller( 'common/footer' )
			), $this->data );
			$this->response->setOutput( $this->load->view( 'extension/module/pavobuilder/pavobuilder', $this->data ) );
		}
	}

	/**
	 * edit module
	 */
	public function edit() {
		$id = ! empty( $this->request->get['module_id'] ) ? $this->request->get['module_id'] : 0;
		$this->form( $id );
	}

	/**
	 * add new module
	 */
	public function add() {
		$this->form();
	}

	/**
	 * creator form
	 */
	private function form( $id = 0 ) {
		$this->load->language( 'extension/module/pavobuilder' );
		$this->load->model( 'setting/extension' );
		$this->load->model( 'setting/module' );
		/**
		 * breadcrumbs data
		 */
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get( 'text_home' ),
			'href' => $this->url->link( 'common/dashboard', 'user_token=' . $this->session->data['user_token'], true )
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/pavobuilder', 'user_token=' . $this->session->data['user_token'], true)
		);

		if ( ! empty( $this->session->data['success'] ) ) {
			$this->data['success'] = $this->session->data['success'];
			unset( $this->session->data['success'] );
		}
		if ( ! empty( $this->session->data['error_warning'] ) ) {

			$this->data['error_warning'] = $this->session->data['error_warning'];
			unset( $this->session->data['error_warning'] );
		}

		if ( $this->request->server['REQUEST_METHOD'] === 'POST' ) {
			$this->saveModule( $id );
		}

		// languages
		$this->load->model( 'localisation/language' );
		$this->data['i18n'] = json_encode( $this->language->all() );
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['layout_id'] = $id;
		$this->data['elements']	= array();
		$this->data['groups']	= array();

		$extensions = $this->model_setting_extension->getInstalled( 'module' );
		foreach ( $extensions as $key => $code ) {
			if ( $code === 'pavobuilder' ) continue;
			$this->load->language( 'extension/module/' . $code );
			$modules = $this->model_setting_module->getModulesByCode( $code );
			if ( $modules ) {
				$this->data['groups'][] = array(
						'name'		=> strip_tags( $this->language->get( 'heading_title' ) ),
						'slug'		=> $code
					);
				foreach ( $modules as $module ) {
					unset( $module['setting'] );
					$module['type']				= 'module';
					$module['icon']				= 'fa fa-opencart';
					$module['group']			= strip_tags( $this->language->get( 'heading_title' ) );
					$module['group_slug']		= $code;
					// $module['settings']			= $module['setting'];
					$this->data['elements'][] 	= $module;
				}
			}
		}

		$file = dirname( __FILE__ ) . '/pavothemer/helper/theme.php';
		if ( ! class_exists( 'PavoThemerHelper' ) && file_exists( $file ) ) {
			require $file;
		}

		// theme helper
		$theme = $this->config->get( 'config_theme' );
		$themeHelper = PavoThemerHelper::instance( $theme );

		/**
		 * 'pavobuilder_animate_effects_groups' animate groups cache key
		 */
		$this->cache->delete( 'pavobuilder_animate_effects_groups' );
		$this->data['animate_groups'] = $this->cache->get( 'pavobuilder_animate_effects_groups', array() );
		if ( ! $this->data['animate_groups'] ) {
			$apiFile = dirname( __FILE__ ) . '/pavothemer/helper/api.php';
			if ( ! class_exists( 'PavothemerApiHelper' ) && file_exists( $apiFile ) ) {
				require_once $apiFile;
				$apiEndpoint = 'https://raw.githubusercontent.com/daneden/animate.css/master/animate-config.json';
				$res = PavothemerApiHelper::get( $apiEndpoint );
				if ( ! empty( $res['response'] ) && ! empty( $res['response']['code'] ) && $res['response']['code'] === 200 ) {
					$body = ! empty( $res['body'] ) ? json_decode( $res['body'], true ) : array();
					foreach ( $body as $group => $effects ) {
						$group_name = implode( ' ', array_map( 'ucfirst', explode( '_', $group ) ) );
						$this->data['animate_groups'][ $group_name ] = array();
						foreach ( $effects as $effect ) {
							$this->data['animate_groups'][ $group_name ][$effect] = ucfirst( $effect );
						}
					}
				}
			}
			$this->cache->set( 'pavobuilder_animate_effects_groups', $this->data['animate_groups'] );
		}

		/**
		 * 'pavobuilder_animate_effects' cache key
		 * animates
		 */
		$this->data['animates']	= $this->cache->get( 'pavobuilder_animate_effects', array() );
		if ( ! $this->data['animates'] ) {
			$this->data['animates']	= $themeHelper->getAnimates();
			$this->cache->set( 'pavobuilder_animate_effects', $this->data['animates'] );
		}

		// fields
		$this->data['element_fields'] = array();
		$this->data['element_mask']  = array();
		$widgets = $this->pavobuilder->widgets->getWidgets();
		$this->data['elements']	= array_merge( $this->data['elements'], $this->pavobuilder->widgets->getWidgets() );
		$this->data['groups'][] = array(
			'name'		=> strip_tags( $this->language->get( 'heading_title' ) ),
			'slug'		=> 'pa-widgets-list'
		);
		$this->data['width_roles'] = array(
			12	=> $this->language->get( 'entry_12_columns_text' ),
			11	=> $this->language->get( 'entry_11_columns_text' ),
			10	=> $this->language->get( 'entry_10_columns_text' ),
			9	=> $this->language->get( 'entry_9_columns_text' ),
			8	=> $this->language->get( 'entry_8_columns_text' ),
			7	=> $this->language->get( 'entry_7_columns_text' ),
			6	=> $this->language->get( 'entry_6_columns_text' ),
			5	=> $this->language->get( 'entry_5_columns_text' ),
			4	=> $this->language->get( 'entry_4_columns_text' ),
			3	=> $this->language->get( 'entry_3_columns_text' ),
			2	=> $this->language->get( 'entry_2_columns_text' ),
			1	=> $this->language->get( 'entry_1_columns_text' )
		);

		foreach ( $widgets as $key => $widget ) {
			$widget = $this->pavobuilder->widgets->getWidget( $key );
			$widgetFields = $widget->fields();
			$this->data['element_mask'][$key] = ! empty( $widgetFields['mask'] ) ? $widgetFields['mask'] : array();
			$this->data['element_fields'][$key] = ! empty( $widgetFields[ 'tabs' ] ) ? $widgetFields[ 'tabs' ] : array();
			$this->data['elements'][$key]['icon'] = $this->data['element_mask'][$key]['icon'];
			$this->data['elements'][$key]['name'] = $this->data['element_mask'][$key]['label'];
		}

		$this->data['user_token'] = $this->session->data['user_token'];
		// layout data
		$layout = $id ? $this->model_setting_module->getModule( $id ) : array();
		$layout = $layout ? $layout : array();
		$this->data['layout'] = $layout;
		if ( empty( $layout['uniqid_id'] ) ) {
			$this->data['layout']['uniqid_id'] = uniqid();
		}
		$this->data['layout']['content'] 		= ! empty( $layout['content'] ) ? $this->validateElementData( $layout['content'] ) : array();

		$this->data['site_url'] 	= $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTPS_CATALOG;
		$this->data['edit_layout_url'] = str_replace( '&amp;', '&', $this->url->link( 'extension/module/pavobuilder/saveModule', 'module_id=' . $id . '&user_token=' . $this->session->data['user_token'], true ) );
		
		// default image
		$this->load->model( 'tool/image' );
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$this->data['underscore_template'] = $this->load->view( 'extension/module/pavobuilder/_template', $this->data );

		// addScripts
		$this->document->addScript( 'view/javascript/pavobuilder/dist/pavobuilder.min.js' );
		$this->document->addScript( 'view/javascript/jquery/jquery-ui/jquery-ui.min.js' );
		if ( $key = $this->config->get( 'pavothemer_google_map_api_key' ) ) {
			$this->document->addScript( '//maps.googleapis.com/maps/api/js?key=' . $this->config->get( 'pavothemer_google_map_api_key' ) . '&libraries=places' );
		}

		// enqueue scripts, stylesheet needed to display editor
		$this->document->addScript( 'view/javascript/summernote/summernote.js' );
		$this->document->addScript( 'view/javascript/summernote/opencart.js' );
		$this->document->addStyle( 'view/javascript/summernote/summernote.css' );

		// addStyles
		$this->document->addStyle( 'view/stylesheet/pavobuilder/dist/pavobuilder.min.css' );
		$this->document->addStyle( 'view/javascript/jquery/jquery-ui/jquery-ui.min.css' );

		$file = '/catalog/view/theme/' . $theme . '/stylesheet/animate.min.css';
		if ( file_exists( dirname( DIR_APPLICATION ) . $file ) ) {
			$this->document->addStyle( HTTP_CATALOG . $file );
		}

		// set page document title
		if ( $this->language && $this->document ) $this->document->setTitle( $this->language->get( 'heading_title' ) );
		$this->data['errors'] = $this->errors;

		$this->data = array_merge( array(
			'header'		=> $this->load->controller( 'common/header' ),
			'column_left' 	=> $this->load->controller( 'common/column_left' ),
			'footer'		=> $this->load->controller( 'common/footer' )
		), $this->data );

		$this->response->setOutput( $this->load->view( 'extension/module/pavobuilder/form', $this->data ) );
	}

	/**
	 * save module
	 */
	public function saveModule( $id = 0 ) {
		if ( ! $this->validate() ) return;
		$this->load->model( 'setting/extension' );
		$this->load->model( 'setting/module' );
		$this->load->language( 'extension/module/pavobuilder' );

		$is_ajax = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';

		$content = $is_ajax ? $this->request->post['content'] : ( ! empty( $this->request->post['content'] ) ? json_decode( html_entity_decode( $this->request->post['content'] ), true ) : array() );
		$this->request->post['content'] = $content;

		$id = ! $id && ! empty( $this->request->get['module_id'] ) ? $this->request->get['module_id'] : $id;

		if ( ! $id ) {
			$this->model_setting_module->addModule( 'pavobuilder', $this->request->post );
			$id = $this->db->getLastId();
		} else {
			$this->model_setting_module->editModule( $id, $this->request->post );
		}

		$uniqid_id = ! empty( $this->request->post['uniqid_id'] ) ? $this->request->post['uniqid_id'] : 0;
		// generate css for layout
		$saved = $this->pavobuilder->css->save( $uniqid_id, $content );

		// ajax save
		if ( $is_ajax ) {
			$this->response->addHeader( 'Content-Type: application/json' );
			$results = array(
				'status'	=> true,
				'id'		=> $id,
				'success'	=> $this->language->get( 'text_success' )
			);
			if ( $saved !== true ) {
				$results = array(
					'status'	=> false,
					'id'		=> $id,
					'success'	=> $saved
				);
			}
			$this->response->setOutput( json_encode( $results ) );
		} else {
			if ( $saved === true ) {
				$this->session->data['success'] = $this->language->get( 'text_success' );
			} else {
				$this->session->data['error_warning'] = $saved;
			}
			// redirect
			$this->response->redirect( $this->url->link( 'extension/module/pavobuilder/edit', 'module_id=' . $id . '&user_token=' . $this->session->data['user_token'], true ) );
		}
	}

	/**
	 * Edit module
	 */
	public function editModule() {
		$module = ! empty( $this->request->get['moduleCode'] ) ? $this->request->get['moduleCode'] : '';
		$file = DIR_APPLICATION . 'controller/extension/module/' . $module . '.php';
		// $file_content = file_get_contents( $file );
		// preg_match( "/this->load->view\([\s+]*[\'|\"{1}]{1}(.+)[\'|\"{1}]{1}/i", $file_content, $matches );
		// $this->load->language( 'extension/module/' . $this->request->post['moduleCode'] );
		// $this->response->setOutput( $this->load->view( $matches[1] ), $this->request->post['settings'] );
		if ( $module && file_exists( $file ) ) {
			$this->load->controller( 'extension/module/' . $module );
		}
	}

	/**
	 * validate element data
	 */
	public function validateElementData( $content = array() ) {
		// $data = arr
		foreach ( $content as $k => $r ) {
			if ( $k === 'settings' && isset( $content['widget'] ) ) {
				$widget = $content['widget'];
				$widget = $this->pavobuilder->widgets->getWidget( $widget );
				if ( method_exists( $widget, 'validate' ) ) {
					$content['settings'] = $widget->validate( $r );
				}
			} else if ( is_array( $r ) ) {
				$content[$k] = $this->validateElementData( $r );
			}
		}

		return $content;
	}

	public function delete() {
		$this->load->language( 'extension/module/pavobuilder' );
		if ( ! $this->user->hasPermission( 'modify', 'extension/module/pavobuilder' ) ) {
			$this->error['warning'] = $this->language->get('error_permission');
		} else {
			if ( empty( $this->request->post['selected'] ) ) {
				$this->error['warning'] = $this->language->get('error_no_selected');
			} else {
				$selected = $this->request->post['selected'];
				$this->load->model( 'setting/module' );
				foreach ( $selected as $id ) {
					$this->model_setting_module->deleteModule( $id );
				}
			}
		}
		$this->response->redirect( $this->url->link( 'extension/module/pavobuilder/index', 'user_token=' . $this->session->data['user_token'], true ) );
	}

	/**
	 * validate method
	 */
	private function validate() {
		if ( ! $this->user->hasPermission('modify', 'extension/module/pavobuilder' ) ) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return ! $this->error;
	}

}