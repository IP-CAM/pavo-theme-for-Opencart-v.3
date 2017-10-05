<?php

class PA_Widget_Row extends PA_Widgets {

	public function fields(){
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-columns',
				'label'	=> $this->language->get( 'entry_row_text' )
			),
			'tabs'	=> array(
				'general'		=> array(
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
								array(
									'label'	=> $this->language->get( 'entry_wide_text' ),
									'value'	=> 'wide'
								),
								array(
									'label'	=> $this->language->get( 'entry_boxed_text' ),
									'value'	=> 'boxed'
								)
							)
						)
					)
				),
				'background'		=> array(
					'label'			=> $this->language->get( 'entry_background_text' ),
					'fields'		=> array(
						array(
							'type'	=> 'colorpicker',
							'name'	=> 'background_color',
							'label'	=> $this->language->get( 'entry_background_color_text' )
						),
						array(
							'type'	=> 'image',
							'name'	=> 'background_image',
							'label'	=> $this->language->get( 'entry_background_image_text' )
						),
						array(
							'type'	=> 'select',
							'name'	=> 'background_position',
							'label'	=> $this->language->get( 'entry_background_position' ),
							'options'	=> array(
								array(
									'label'		=> 'Inherit',
									'value'		=> 'inherit'
								),
								array(
									'label'		=> 'Top Left',
									'value'		=> 'top left'
								),
								array(
									'label'		=> 'Top Right',
									'value'		=> 'top right'
								),
								array(
									'label'		=> 'Bottom Left',
									'value'		=> 'bottom left'
								),
								array(
									'label'		=> 'Bottom Right',
									'value'		=> 'bottom right'
								),
								array(
									'label'		=> 'Center Center',
									'value'		=> 'center center'
								)
							)
						),
						array(
							'type'	=> 'select',
							'name'	=> 'background_repeat',
							'label'	=> $this->language->get( 'entry_background_repeat_text' ),
							'options'	=> array(
								array(
									'label'	=> 'No Repeat',
									'value'	=> 'no-repeat'
								),
								array(
									'label'	=> 'Repeat x',
									'value'	=> 'repeat-x'
								),
								array(
									'label'	=> 'Repeat y',
									'value'	=> 'repeat-y'
								)
							)
						),
						array(
							'type'	=> 'text',
							'name'	=> 'background_video',
							'label'	=> $this->language->get( 'entry_video_url_text' )
						),
						array(
							'type'	=> 'checkbox',
							'name'	=> 'parallax',
							'label'	=> $this->language->get( 'entry_parallax_text' )
						)
					)
				),
				'style'				=> array(
					'label'			=> $this->language->get( 'entry_styles_text' ),
					'fields'		=> array(
						array(
							'type'	=> 'layout-onion',
							'name'	=> 'styles',
							'label'	=> $this->language->get( 'entry_box_text' )
						)
					)
				),
				'animate'			=> array(
					'label'			=> $this->language->get( 'entry_effect_text' ),
					'fields'		=> array(
						array(
							'type'	=> 'animate',
							'name'	=> '',
							'label'	=> $this->language->get( 'heading_title' )
						),
						array(
							'type'	=> 'select-animate',
							'name'	=> 'effect',
							'id'	=> 'animate-select',
							'group'	=> true,
							'label'	=> $this->language->get( 'entry_effect_text' )
						)
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$class = array();
		if ( ! empty( $settings['extra_class'] ) ) {
			$class[] = $settings['extra_class'] ? $settings['extra_class'] : '';
		}
		if ( ! empty( $settings['parallax'] ) ) {
			$class[] = $settings['parallax'] ? 'pa-parallax' : '';
		}
		if ( ! empty( $settings['effect'] ) ) {
			$class[] = $settings['effect'];
		}

		$settings['class'] = implode( ' ', $class );
		$settings['id'] = ! empty( $settings['uniqid_id'] ) ? $settings['uniqid_id'] : '';
		$settings['id'] = ! $settings['id'] && ! empty( $settings['specifix_id'] ) ? $settings['specifix_id'] : '';
		if ( ! $settings['id'] ) {
			$settings['id'] = uniqid();
		}
		return $this->load->view( 'extension/module/pavobuilder/pa_row/pa_row', array( 'settings' => $settings, 'content' => $content ) );
	}

}