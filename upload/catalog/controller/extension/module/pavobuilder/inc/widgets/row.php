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
							'name'	=> 'background-color',
							'label'	=> $this->language->get( 'entry_background_color_text' )
						),
						array(
							'type'	=> 'image',
							'name'	=> 'background-image',
							'label'	=> $this->language->get( 'entry_background_image_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'background-video',
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
							'label'	=> 'entry_box_text'
						),
						array(
							'type'	=> 'colorpicker',
							'name'	=> 'color',
							'label'	=> 'entry_color_text'
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
		return $this->load->view( 'extension/module/pavobuilder/pa_row/pa_row', array( 'settings' => $settings, 'content' => $content ) );
	}

}