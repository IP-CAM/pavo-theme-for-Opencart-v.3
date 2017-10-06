<?php

class PA_Widget_Reassurance extends PA_Widgets {

	public function fields() {
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-truck',
				'label'	=> $this->language->get( 'entry_reassurance_text' )
			),
			'tabs'	=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ){
		return $this->load->view( 'extension/module/pavobuilder/pa_reassurance/pa_reassurance', array( 'settings' => $settings, 'content' => $content ) );
	}

}