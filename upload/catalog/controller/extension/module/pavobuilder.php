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
					echo $this->load->controller( 'extension/module/' . $element['moduleCode'], $moduleSettings );
				} else if ( $subs ) {
					echo $this->renderElement( $element, $subs );
				}
			}
			$content = ob_get_clean();
		}

		$settings = $this->unescapeData( $settings );
		return $this->load->view( 'extension/module/pavobuilder/' . $data['shortcode'] . '/' . $data['shortcode'], array( 'settings' => $settings, 'content' => $content ) );
	}

	private function unescapeData( $settings = array() ) {
		$data = array();
		foreach ( $settings as $k => $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				$data[ $k ] = $this->unescapeData( $value );
			} else if ( is_string( $value ) ) {
				$data[ $k ] = html_entity_decode( htmlspecialchars_decode( $value ), ENT_QUOTES, 'UTF-8' );
			} else {
				$data[$k] = $value;
			}
		}
		return $data;
	}
}