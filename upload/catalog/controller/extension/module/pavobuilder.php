<?php

require_once dirname( __FILE__ ) . '/pavobuilder/pavobuilder.php';

class ControllerExtensionModulePavoBuilder extends Controller {

	public function __construct( $registry ) {
		parent::__construct( $registry );
		$this->pavobuilder = PavoBuilder::instance( $registry );
	}

	/**
	 * render layout
	 */
	public function index( $setting ) {
		$builder = ! empty( $setting['content'] ) ? $setting['content'] : array();
		ob_start();
		if ( ! empty( $builder ) ) {
			foreach ( $builder as $row ) {
				echo $this->renderElement( $row );
			}
		}
		$uniqid_id = ! empty( $setting['uniqid_id'] ) ? $setting['uniqid_id'] : '';
		$file = DIR_APPLICATION . 'controller/extension/module/pavobuilder/stylesheet/' . $uniqid_id . '.css';
		if ( file_exists( $file ) ) {
			$this->document->addStyle('catalog/controller/extension/module/pavobuilder/stylesheet/' . $uniqid_id . '.css' );
		}
		return ob_get_clean();
	}

	/**
	 * render element
	 */
	private function renderElement( $data = array(), $content = '' ) {
		$settings = ! empty( $data['settings'] ) ? $data['settings'] : array();
		$this->load->model( 'setting/module' );
		$content = '';

		if ( ! empty( $data['row'] ) ) {
			return $this->renderElement( $data['row'] );
		}

		if ( ! empty( $data['columns'] ) || ! empty( $data['elements'] ) ) {
			ob_start();
			$subElements = ! empty( $data['columns'] ) ? $data['columns'] : ( ! empty( $data['elements'] ) ? $data['elements'] : array() );
			foreach ( $subElements as $element ) {
				$subs = ! empty( $data['columns'] ) ? $data['columns'] : ( ! empty( $data['elements'] ) ? $data['elements'] : array() );
				if ( ! empty( $element['element_type'] ) && $element['element_type'] === 'module' && ! empty( $element['moduleCode'] ) ) {
					$moduleSettings = ! empty( $element['moduleId'] ) ? $this->model_setting_module->getModule( $element['moduleId'] ) : array();
					ob_start();
					echo $this->load->controller( 'extension/module/' . $element['moduleCode'], $moduleSettings );
					echo $this->load->view( 'extension/module/pavobuilder/pa_element_wrapper', array( 'content' => ob_get_clean() ) );
				} else if ( $subs ) {
					echo $this->renderElement( $element, $subs );
				}
			}
			$content = ob_get_clean();
		}
		if ( isset( $data['widget'] ) && $data['widget'] == 'pa_column' && ! empty( $data['responsive'] ) ) {
			$settings['responsive'] = $data['responsive'];
		}
		return $this->pavobuilder->widgets->renderWidget( $data['widget'], $settings, $content );
	}

}