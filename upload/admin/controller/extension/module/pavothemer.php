<?php

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

require_once dirname( __FILE__ ) . '/pavothemer/pavothemer.php';

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

	/**
	 * Render theme control admin layout
	 *
	 * @since 1.0.0
	 */
	public function index() {
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
			'href' => $this->url->link( 'common/dashboard', 'token=' . $this->session->data['user_token'], true )
		);
		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get( 'text_extension' ),
			'href'      => $this->url->link( 'marketplace/extension', 'token=' . $this->session->data['user_token'].'&type=module', 'SSL' ),
      		'separator' => ' :: '
   		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['user_token'], true)
		);

		$tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : '';
		// setting tabs
		$this->data['settings'] = LxSettingHelper::getSettings( $this->config->get( 'config_theme' ) );
		$this->data['current_tab'] = isset( $_REQUEST['current_tab'] ) ? $_REQUEST['current_tab'] : current( array_keys( $this->data['settings'] ) );

		// validate and update settings
		if ( $this->request->server['REQUEST_METHOD'] === 'POST' ) {
			$validated = $this->validate();
			if ( $validated ) {
				$this->model_setting_setting->editSetting( 'lx_config', $this->request->post, $this->config->get( 'config_store_id' ) );
			}
		}

		foreach ( $this->data['settings'] as $k => $fields ) {
			if ( isset( $fields['item'] ) ) foreach( $fields['item'] as $k2 => $item ) {
				if ( isset( $item['id'] ) ) {
					$name = 'lx_config_' . $item['id'];
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
		parent::index();
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
		$this->template = 'extension/module/pavothemer/customize';
		parent::index();
	}

	/**
	 * Sample data
	 *
	 * @since 1.0.0
	 */
	public function sampledata() {

	}

	/**
	 * Validate post form
	 *
	 * @since 1.0.0
	 */
	public function validate() {

		$has_permision = $this->hasPermission();
		if ( ! $has_permision ) {
			$this->errors['warning'] = $this->language->get( 'error_permision' );
		} else {
			foreach ( $this->data['settings'] as $k => $fields ) {
				if ( isset( $fields['item'] ) ) foreach( $fields['item'] as $k2 => $item ) {
					if ( isset( $item['id'] ) ) {
						if ( isset( $item['required'] ) && $item['required'] && empty( $this->request->post[ $item['id'] ] ) ) {
							$this->errors[ $item['id'] ] = $this->language->get( 'error_' . $item['id'] );
						}
					}
				}
			}
		}
		return ! $this->errors;
	}

	/**
	 *
	 * Check current user has permision modify controller
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	private function hasPermission() {
		return $this->user->hasPermission( 'modify', 'extension/module/pavothemer' );
	}

	/**
	 * Render html field for input, textarea, ...
	 *
	 * @since 1.0.0
	 * @return mixed html
	 */
	private function renderFieldControl( $item = array() ) {
		if ( empty( $item['type'] ) ) return;

		$type = 'input';
		switch ( $item['type'] ) {
			case 'select-theme':
				# code...
				break;

			case 'select-store':
				# code...
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
					$styleProfiles = LxThemeHelper::getCssProfiles( $this->config->get( 'config_theme' ) );
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
			default:
				# code...
					$type = $item['type'];
				break;
		}

		$item['name'] = strpos( $item['id'], 'lx_config_' ) == false ? 'lx_config_' . $item['id'] : $item['id'];
		$item['class'] = 'form-control' . ( isset( $item['class'] ) ? ' ' .trim( $item['class'] ) : '' );
		// return html
		return $this->load->view( 'extension/module/pavothemer/fields/' . $type, array( 'item' => $item ) );
	}

	/**
	 *
	 * Insert default pavothemer values to setting table
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function install() {
		// START ADD USER PERMISSION
		$this->load->model('user/user_group');
		// access - modify pavothemer edit
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/pavothemer/edit');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/pavothemer/edit');
		// access - modify pavothemer customize
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/pavothemer/customize');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/pavothemer/customize');
		// access - modify pavothemer sampledata
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'extension/module/sampledata');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'extension/module/sampledata');
		// END ADD USER PERMISSION

		$settingFields = LxSettingHelper::getSettings( $this->config->get('config_theme') );
		$this->load->model( 'setting/setting' );
		$settings = array();

		// get option if it already activated before
		$defaultOptions = array();
		foreach ( $settingFields as $tab => $fields ) {
			if ( empty( $fields['item'] ) ) continue;
			foreach ( $fields['item'] as $item ) {
				if ( ! isset( $item['id'], $item['default'] ) ) continue;
				// get default options
				if ( ! $this->config->get( 'lx_config_' . $item['id'] ) ) {
					$defaultOptions[ 'lx_config_' . $item['id'] ] = $item['default'];
				}
			}
		}
		// $settings['lx_config'] = $defaultOptions;

		// insert default option values
		// $this->model_setting_setting->editSetting( 'lx_config', $settings, $this->config->get( 'config_store_id' ) );
		$this->model_setting_setting->editSetting( 'lx_config', $defaultOptions, $this->config->get( 'config_store_id' ) );
	}

}
