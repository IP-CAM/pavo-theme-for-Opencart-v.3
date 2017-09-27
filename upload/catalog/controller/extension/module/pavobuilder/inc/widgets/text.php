<?php

class PA_Widget_Text extends PA_Widgets {

	public function fields(){
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-file-text',
				'label'	=> $this->language->get( 'entry_text_block' )
			),
			'tabs'	=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						array(
							'type'	=> 'text',
							'name'	=> 'extra_class',
							'label'	=> $this->language->get( 'entry_extra_class_text' )
						),
						array(
							'type'		=> 'editor',
							'name'		=> 'content',
							'label'		=> $this->language->get( 'entry_content_text' ),
							'default'	=> ''
						)
					)
				),
				'style'				=> array(
					'label'			=> $this->language->get( 'entry_styles_text' ),
					'fields'		=> array(
						array(
							'type'	=> 'layout-onion',
							'name'	=> 'layout_onion',
							'label'	=> 'entry_box_text'
						),
						array(
							'type'	=> 'colorpicker',
							'name'	=> 'color',
							'label'	=> 'entry_color_text'
						)
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$settings['content'] = ! empty( $settings['content'] ) ? html_entity_decode( htmlspecialchars_decode( $settings['content'] ), ENT_QUOTES, 'UTF-8' ) : '';
		return $this->load->view( 'extension/module/pavobuilder/pa_text/pa_text', array( 'settings' => $settings, 'content' => $content ) );
	}

}