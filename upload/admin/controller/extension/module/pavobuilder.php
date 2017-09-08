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

	/**
	 * edit module
	 */
	public function edit() {
		$id = ! empty( $this->request->get['id'] ) ? $this->request->get['id'] : 0;
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

		// languages
		$this->data['languages'] = json_encode( $this->language->all() );
		$this->data['layout_id'] = $id;
		$this->data['elements']	= array();
		$this->data['groups']	= array();

		$extensions = $this->model_setting_extension->getInstalled( 'module' );
		foreach ( $extensions as $key => $code ) {
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
		$themeHelper = PavoThemerHelper::instance( $this->config->get( 'config_theme' ) );
		$shortcodes = $themeHelper->getShortcodes();
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
		$this->data['underscore_template'] = $this->load->view( 'extension/module/pavobuilder/_template', $this->data );

		// addScripts
		$this->document->addScript( 'view/javascript/pavobuilder/dist/pavobuilder.min.js' );
		$this->document->addScript( 'view/javascript/jquery/jquery-ui/jquery-ui.min.js' );
		// addStyles
		$this->document->addStyle( 'view/stylesheet/pavobuilder/dist/pavobuilder.min.css' );
		$this->document->addStyle( 'view/javascript/jquery/jquery-ui/jquery-ui.min.css' );
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
	 * validate method
	 */
	private function validate() {

	}

}