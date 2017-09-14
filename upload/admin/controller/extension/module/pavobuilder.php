<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

/**
 * Homebuilder Controller
 */
class ControllerExtensionModulePavobuilder extends Controller {

	/**
	 * data pass to view
	 */
	private $data = array();

	public function index() {
		$this->load->language( 'extension/module/pavobuilder' );

		if ( ! empty( $this->request->get['module_id'] ) ) {
			$this->edit();
		} else {
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
			$this->saveModule();
		}

		// languages
		$this->data['languages'] = json_encode( $this->language->all() );
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
					$module['type']				= 'module';
					$module['icon']				= 'fa fa-opencart';
					$module['group']			= strip_tags( $this->language->get( 'heading_title' ) );
					$module['group_slug']		= $code;
					$module['settings']			= $module['setting'];
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
		$shortcodes = $themeHelper->getShortcodes();

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

		// DEVING
		$this->data['row_edit_fields'] = $this->rowEditFields();

		if ( $shortcodes ) {
			$this->data['groups'][] = array(
					'name'		=> $this->language->get( 'entry_pavo_shortcodes' ),
					'slug'		=> 'pa-shortcodes-list'
				);
			foreach ( $shortcodes as $shortcode ) {
				$this->data['elements'][] = array(
						'type'		=> 'shortcode',
						'settings'	=> '',
						'shortcode'	=> $shortcode,
						'group' 	=> strip_tags( $this->language->get( 'heading_title' ) ),
						'group_slug'=> 'pa-shortcodes-list'
					);
			}
		}

		// layout data
		$this->data['layout'] = $id ? $this->model_setting_module->getModule( $id ) : array();
		$this->data['site_url'] 	= $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTPS_CATALOG;
		$this->data['underscore_template'] = $this->load->view( 'extension/module/pavobuilder/_template', $this->data );

		// addScripts
		$this->document->addScript( 'view/javascript/pavobuilder/dist/pavobuilder.min.js' );
		$this->document->addScript( 'view/javascript/jquery/jquery-ui/jquery-ui.min.js' );
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
	private function saveModule() {
		$this->load->model( 'setting/extension' );
		$this->load->model( 'setting/module' );
		$this->load->language( 'extension/module/pavobuilder' );
		$content = ! empty( $this->request->post['content'] ) ? json_decode( htmlspecialchars_decode( $this->request->post['content'] ), true ) : array();
		$this->request->post['content'] = $content;

		if ( ! $id ) {
			$this->model_setting_module->addModule( 'pavobuilder', $this->request->post );
			$id = $this->db->getLastId();
		} else {
			$this->model_setting_module->editModule( $id, $this->request->post );
		}

		$this->session->data['success'] = $this->language->get('text_success');

		$this->response->redirect( $this->url->link( 'extension/module/pavobuilder/edit', 'module_id=' . $id . '&user_token=' . $this->session->data['user_token'], true ) );
	}

	/**
	 * rows edit fields
	 */
	private function rowEditFields() {
		$fields = array(
				'general'	=> array(
						'label'		=> $this->language->get( 'entry_general_text' ),
						'fields'	=> array(
								array(
										'type'	=> 'text',
										'name'	=> 'uniqid_id',
										'label'	=> $this->language->get( 'entry_row_id_text' )
									),
								array(
										'type'	=> 'text',
										'name'	=> 'extra_class',
										'label'	=> $this->language->get( 'entry_extra_class_text' )
									),
								array(
										'type'	=> 'select',
										'name'	=> 'layout',
										'label'	=> $this->language->get( 'entry_layout_type_text' ),
										'options'	=> array(
												'wide'	=> $this->language->get( 'entry_wide_text' ),
												'boxed'	=> $this->language->get( 'entry_boxed_text' )
											)
									),
							)
					),
				'background'	=> array(
						'label'	=> $this->language->get( 'entry_background_text' ),
						'fields'	=> array(
								array(
										'type'		=> 'colorpicker',
										'name'		=> 'background-color',
										'label'		=> $this->language->get( 'entry_background_color_text' )
									),
								array(
										'type'		=> 'image',
										'name'		=> 'background-image',
										'label'		=> $this->language->get( 'entry_background_image_text' )
									),
								array(
										'type'		=> 'text',
										'name'		=> 'background-video',
										'label'		=> $this->language->get( 'entry_video_url_text' )
									),
								array(
										'type'		=> 'checkbox',
										'name'		=> 'parallax',
										'label'		=> $this->language->get( 'entry_parallax_text' )
									)
							)
					),
				'style'	=> array(
						'label'	=> $this->language->get( 'entry_styles_text' ),
						'fields'	=> array(
								array(
										'type'		=> 'layout-onion',
										'name'		=> 'layout_onion',
										'label'		=> $this->language->get( 'entry_box_text' )
									),
								array(
										'type'		=> 'colorpicker',
										'name'		=> 'color',
										'label'		=> $this->language->get( 'entry_color_text' )
									)
							)
					),
				'animate'	=> array(
						'label'	=> $this->language->get( 'entry_effect_text' ),
						'fields'	=> array(
								array(
										'type'	=> 'animate',
										'name'	=> '',
										'label'	=> $this->language->get( 'heading_title' )
									),
								array(
										'type'		=> 'select',
										'name'		=> 'effect',
										'id'		=> 'animate-select',
										'label'		=> $this->language->get( 'entry_effect_text' ),
										'groups'	=> $this->data['animate_groups'] ? true : false,
										'options'	=> $this->data['animate_groups'] ? $this->data['animate_groups'] : $this->data['animates']
									)
							)
					)
			);
		return $fields;
	}

	/**
	 * validate method
	 */
	private function validate() {

	}

}