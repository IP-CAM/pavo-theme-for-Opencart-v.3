<?php

class PA_Widget_Single_Image extends PA_Widgets {

	public function fields() {
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-picture-o',
				'label'	=> $this->language->get( 'entry_single_image_text' )
			),
			'tabs'	=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						array(
							'type'	=> 'image',
							'name'	=> 'src',
							'label'	=> $this->language->get( 'entry_image_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'image_size',
							'label'	=> $this->language->get( 'entry_image_size_text' ),
							'desc'	=> $this->language->get( 'entry_image_size_desc' ),
							'default'		=> 'full',
							'placeholder'	=> '200x400'
						),
						array(
							'type'	=> 'text',
							'name'	=> 'link',
							'label'	=> $this->language->get( 'entry_link_text' ),
							'default'		=> '#',
							'desc'	=> $this->language->get( 'entry_link_desc_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'alt',
							'label'	=> $this->language->get( 'entry_alt_text' ),
							'default' => '',
							'desc'	=> $this->language->get( 'entry_alt_desc_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'extra_class',
							'label'	=> $this->language->get( 'entry_extra_class_text' ),
							'default' => '',
							'desc'	=> $this->language->get( 'entry_extra_class_desc_text' )
						)
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$class = array();
		if ( ! empty( $settings['extra_class'] ) ) {
			$class[] = $settings['extra_class'];
		}
		if ( ! empty( $settings['effect'] ) ) {
			$class[] = $settings['effect'];
		}

		$settings['class'] = implode( ' ', $class );
		$server = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;
		if ( ! empty( $settings['src'] ) ) {
			$settings['image_size'] = strtolower( $settings['image_size'] );
			$src = empty( $settings['image_size'] ) || $settings['image_size'] == 'full' ? $server . 'image/' . $settings['src'] : false;

			if ( $src === false && strpos( $settings['image_size'], 'x' ) ) {
				$this->load->model( 'tool/image' );
				$sizes = explode( 'x', $settings['image_size'] );
				if ( ! empty( $sizes[0] ) && ! empty( $sizes[1] ) ) {
					$src = $this->model_tool_image->resize( $settings['src'], $sizes[0], $sizes[1] );
				}
			}

			$settings['src'] = $src ? $src : '';
		}
		return $this->load->view( 'extension/module/pavobuilder/pa_single_image/pa_single_image', array( 'settings' => $settings, 'content' => $content ) );
	}

}