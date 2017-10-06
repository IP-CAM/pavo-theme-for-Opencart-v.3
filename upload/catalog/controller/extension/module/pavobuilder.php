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
		if ( ! empty( $settings['background_video'] ) ) {
			$url = $settings['background_video'];
			// $url = 'https://youtu.be/2WRz96r9axM';
			// $url = 'https://www.youtube.com/watch?v=2WRz96r9axM';
			// validate youtube url
			preg_match( '/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', $url, $match );
			$video_id = ! empty( $match[2] ) ? $match[2] : false;
			$settings['background_video'] = false;
			if ( $video_id ) {
				$query = array(
					'playlist'		=> $video_id,
					'enablejsapi' 	=> 1,
					'iv_load_policy'	=> 3,
					'disablekb'		=> 1,
					'autoplay'		=> 1,
					'controls'		=> 0,
					'showinfo'		=> 0,
					'rel'			=> 0,
					'loop'			=> 1,
					'mute'			=> 1,
					'wmode'			=> 'transparent'
				);
				$settings['background_video'] = 'https://youtube.com/embed/' . $video_id . '?' . http_build_query( $query );
			}
		}
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

		if ( ! in_array( $data['widget'], array( 'pa_row', 'pa_column' ) ) ) {
			ob_start();
			echo $this->pavobuilder->widgets->renderWidget( $data['widget'], $settings, $content );
			return $this->load->view( 'extension/module/pavobuilder/pa_element_wrapper', array( 'data' => $data, 'settings' => $settings, 'content' => ob_get_clean() ) );
		}
		return $this->pavobuilder->widgets->renderWidget( $data['widget'], $settings, $content );
	}

}