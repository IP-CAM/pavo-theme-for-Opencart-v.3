<?php

class ControllerExtensionModulePavoBuilder extends Controller {

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
		return ob_get_clean();
	}

	/**
	 * render element
	 */
	private function renderElement( $data = array(), $content = '', $set = 0 ) {
		$settings = ! empty( $data['settings'] ) ? $data['settings'] : array();
		if ( ! empty( $settings['element'] ) ) {
			$this->load->model( 'setting/module' );
			$content = '';
			if ( ! empty( $data['columns'] ) || ! empty( $data['elements'] ) ) {
				$subElements = ! empty( $data['columns'] ) ? $data['columns'] : ( ! empty( $data['elements'] ) ? $data['elements'] : array() );
				ob_start();
				foreach ( $subElements as $element ) {
					$sts = ! empty( $element['settings'] ) ? $element['settings'] : array();
					$subs = ! empty( $data['columns'] ) ? $data['columns'] : ( ! empty( $data['elements'] ) ? $data['elements'] : array() );
					
					if ( ! empty( $element['element_type'] ) && $element['element_type'] === 'module' && ! empty( $element['moduleCode'] ) ) {
						$moduleSettings = ! empty( $element['moduleId'] ) ? $this->model_setting_module->getModule( $element['moduleId'] ) : array();
						echo $this->load->controller( 'extension/module/' . $element['moduleCode'], $moduleSettings ); // $sts
					} else {
						echo $this->renderElement( $element, $subs, 1 );
					}
				}
				$content = ob_get_clean();
			}
			return $this->load->view( 'extension/module/pavobuilder/' . $settings['element'], array( 'settings' => $settings, 'content' => $content ) );
		}
	}

}