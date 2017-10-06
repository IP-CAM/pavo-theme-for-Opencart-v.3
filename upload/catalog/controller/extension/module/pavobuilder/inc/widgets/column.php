<?php

class PA_Widget_Column extends PA_Widgets {

	public function fields(){
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-columns',
				'label'	=> $this->language->get( 'entry_text_block' )
			),
			'tabs'		=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						array(
							'type'	=> 'text',
							'name'	=> 'uniqid_id',
							'label'	=> $this->language->get( 'entry_column_id_text' ),
							'desc'	=> $this->language->get( 'entry_column_desc_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'extra_class',
							'label'	=> $this->language->get( 'entry_extra_class_text' ),
							'desc'	=> $this->language->get( 'entry_extra_class_desc_text' )
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
				// ,
				// 'responsive'		=> array(
				// 	'label'			=> $this->language->get( 'entry_responsive_text' ),
				// 	'fields'		=> array(
				// 		array(
				// 			'type'	=> 'responsive',
				// 			'name'	=> 'responsive'
				// 		)
				// 	)
				// )
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$class = $bootstrap_class = array();
		if ( ! empty( $settings['extra_class'] ) ) {
			$class[] = $settings['extra_class'];
		}
		if ( ! empty( $settings['parallax'] ) ) {
			$class[] = $settings['parallax'];
		}
		if ( ! empty( $settings['effect'] ) ) {
			$class[] = $settings['effect'];
		}
		if ( $settings['responsive'] ) {
			foreach ( $settings['responsive'] as $type => $opt ) {
				if ( ! empty( $opt['cols'] ) ) {
					$bootstrap_class[] = 'col-' . $type . '-' . $opt['cols'];
				}
			}
		}
		$settings['class'] = implode( ' ', $class );
		$settings['bootstrap_class'] = implode( ' ', $bootstrap_class );
		$settings['id'] = ! empty( $settings['uniqid_id'] ) ? $settings['uniqid_id'] : '';
		$settings['id'] = ! $settings['id'] && ! empty( $settings['specifix_id'] ) ? $settings['specifix_id'] : $settings['id'];
		if ( ! $settings['id'] ) {
			$settings['id'] = uniqid();
		}

		return $this->load->view( 'extension/module/pavobuilder/pa_column/pa_column', array( 'settings' => $settings, 'content' => $content ) );
	}

}