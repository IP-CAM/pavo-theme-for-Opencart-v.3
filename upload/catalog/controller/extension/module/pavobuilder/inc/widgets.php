<?php

class PA_Widgets extends Controller {

	public static $instance = null;
	private $widgets = array();

	public static function instance( $registry ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $registry );
		}
		return self::$instance;
	}

	public function __construct( $registry ) {
		parent::__construct( $registry );
	}

	/**
	 * load all widgets
	 */
	public function registerWidgets() {
		if ( $this->widgets ) return $this->widgets;
		$files = glob( dirname( DIR_SYSTEM ) . '/catalog/controller/extension/module/pavobuilder/inc/widgets/*.php' );
		foreach ( $files as $file ) {
			$file_name = basename( $file, '.php' );
			if ( strpos( $file_name, 'pa_' ) == 0 ) {
				require_once $file;
				$name = implode( '_', array_map( 'ucfirst', explode( '_', $file_name ) ) );
				$class_name = 'PA_Widget_' . $name;
				$widget = str_replace( 'widget_', '', strtolower( $class_name ) );
				$this->widgets[ $widget ]= new $class_name( $this->registry );
			}
		}
		return $this;
	}

	public function getWidgets() {
		if ( ! $this->widgets ) {
			$this->registerWidgets();
		}

		$widgets = array();
		$this->load->language( 'extension/module/pavobuilder' );
		foreach ( $this->widgets as $key => $widget ) {
			$widgets[$key] = array(
					'type' 		=> 'widget',
					'widget' 	=> str_replace( 'widget_', '', strtolower( get_class( $widget ) ) ),
					'group'		=> strip_tags( $this->language->get( 'heading_title' ) ),
					'group_slug'=> 'pa-widgets-list',
					'icon'		=> '',
					'label'		=> ''
				);
		}

		return $widgets;
	}

	public function getWidget( $widget = '' ) {
		if ( ! $this->widgets ) {
			$this->registerWidgets();
		}
		
		return ! empty( $this->widgets[$widget] ) ? $this->widgets[$widget] : null;
	}

	/**
	 * render widget
	 */
	public function renderWidget( $widget = '', $settings = array(), $content = '' ) {
		$widget = $this->getWidget( $widget );
		return $widget->render( $settings, $content );
	}

}