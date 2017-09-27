<?php

class PA_Widget_Google_Map extends PA_Widgets {

	public function fields() {
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-map-marker',
				'label'	=> $this->language->get( 'entry_google_map' )
			),
			'tabs'	=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						array(
							'type'	=> 'map-preview',
							'name'	=> 'place_name',
							'label'	=> $this->language->get( 'entry_preview_text' )
						),
						array(
							'type'	=> 'checkbox',
							'name'	=> 'zoomControl',
							'label'	=> $this->language->get( 'entry_enabled_zoom_text' ),
							'default' => 1
						),
						array(
							'type'	=> 'number',
							'name'	=> 'zoom',
							'label'	=> $this->language->get( 'entry_zoom_level_text' ),
							'step' 	=> 1,
							'min'	=> 1,
							'default' => 13
						),
						array(
							'type'	=> 'checkbox',
							'name'	=> 'scrollwheel',
							'label'	=> $this->language->get( 'entry_enabled_scrollwheel_text' ),
							'default' => 1
						),
						array(
							'type'	=> 'checkbox',
							'name'	=> 'mapTypeControl',
							'label'	=> $this->language->get( 'entry_enabled_map_typecontrols_text' ),
							'default' => 1
						),
						array(
							'type'	=> 'checkbox',
							'name'	=> 'draggable',
							'label'	=> $this->language->get( 'entry_enabled_draggable_text' ),
							'default' => 1
						),
						array(
							'type'	=> 'select',
							'name'	=> 'mapTypeId',
							'label'	=> $this->language->get( 'entry_maptype_id_text' ),
							'default' => 'roadmap',
							'options'	=> array(
								array(
									'value'	=> 'roadmap',
									'label'	=> 'Roadmap'
								),
								array(
									'value'	=> 'satellite',
									'label'	=> 'Satellite'
								),
								array(
									'value'	=> 'hybrid',
									'label'	=> 'Hybrid'
								),
								array(
									'value'	=> 'terrain',
									'label'	=> 'Terrain'
								)
							)
						),
						array(
							'type'	=> 'number',
							'name'	=> 'height',
							'label'	=> $this->language->get( 'entry_height_text' ),
							'default' => 500
						),
						array(
							'type'	=> 'radio',
							'name'	=> 'height_unit',
							'label'	=> $this->language->get( 'entry_unit_text' ),
							'default' 	=> 'px',
							'options'	=> array(
								array(
									'value'	=> 'px',
									'label'	=> $this->language->get( 'entry_px_text' )
								),
								array(
									'value'	=> '%',
									'label'	=> $this->language->get( 'entry_percent_text' )
								)
							)
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

	public function render( $settings = array(), $content = '' ){
		return $this->load->view( 'extension/module/pavobuilder/pa_google_map/pa_google_map', array( 'settings' => $settings, 'content' => $content ) );
	}

}