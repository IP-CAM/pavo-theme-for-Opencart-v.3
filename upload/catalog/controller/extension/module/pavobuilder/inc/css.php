<?php

class PA_Css extends Controller {

	private static $instance = null;

	public static function instance( $registry ) {
		if ( ! self::$instance ) {
			self::$instance = new self( $registry );
		}
		return self::$instance;
	}

	public function __construct( $registry ) {
		parent::__construct( $registry );
	}

	public function build( $content ) {
		$css = array();
		foreach ( $content as $key => $row ) {
			$css[] = implode( '', $this->elementBuilder( $row, '.row-inner' ) );
		}

		$css = implode( '', $css );
		return $css;
	}

	/**
	 * element builder css
	 */
	private function elementBuilder( $element = array(), $inner = '.row-inner' ) {
		$css = array();
		$settings = ! empty( $element['settings'] ) ? $element['settings'] : array();
		$id = false;
		if ( ! empty( $settings['uniqid_id'] ) ) {
			$id = $settings['uniqid_id'];
			// $css[] = '#'.$settings['uniqid_id'].' ' .$inner. '{';
		} else if ( ! empty( $settings['specifix_id'] ) ) {
			$id = $settings['specifix_id'];
			// $css[] = '#'.$settings['specifix_id'].' ' .$inner. '{';
		}

		if ( $id ) {
			$css[] = '#'. $id .' ' .$inner. '{';
			$css[] = ! empty( $settings['color'] ) ? 'color:' . $settings['color'] . ';' : '';
			$css[] = ! empty( $settings['background-image'] ) ? 'background-image: url( '.$settings['background-image'].' )' . ';' : '';
			$css[] = ! empty( $settings['background-color'] ) ? 'background-color: '.$settings['background-color'] . ';' : '';
			if ( ! empty( $settings['styles'] ) ) {
				foreach ( $settings['styles'] as $attr => $value ) {
					if ( $value ) {
						$css[] = str_replace( '_', '-', $attr ) . ':' . $value . 'px;';
					}
				}
			}

			$css[] = '}';

			$responsive = ! empty( $element['responsive'] ) ? $element['responsive'] : array();
			// just columns
			if ( $responsive ) {
				$responsive = array_reverse( $responsive );
				foreach ( $responsive as $type => $opt ) {
					if ( ! empty( $opt['cols'] ) ) {
						switch ( $type ) {
							case 'lg':
								$css[] = '@media (min-width: 1200px){';
								break;

							case 'md':
								$css[] = '@media (min-width: 992px){';
								break;

							case 'sm':
								$css[] = '@media (min-width: 768px){';
								break;
							
							default:
								# code...
								break;
						}
						$css[] = '#' . $id . '.col-' . $type . '-' . $opt['cols'] . '{';

						$styles = ! empty( $opt['styles'] ) ? $opt['styles'] : array();
						if ( ! empty( $styles['width'] ) ) {
							$css[] = 'width:' . $styles['width'] . '%;';
						}
						$css[] = '}';
						if ( in_array( $type, array( 'lg', 'md', 'sm' ) ) ) {
							$css[] = '}';
						}
					}
				}
			}
		}// die();

		$subs = ! empty( $element['columns'] ) ? $element['columns'] : ( ! empty( $element['elements'] ) ? $element['elements'] : array() );
		$inner = ! empty( $element['columns'] ) ? '.column-inner' : '.element-inner';

		if ( $subs ) {
			foreach ( $subs as $sub ) {
				$css[] = implode( '', $this->elementBuilder( $sub, $inner ) );
			}
		}
		return $css;
	}

	/**
	 * create css profile for layout
	 */
	public function save( $id = 0, $content = array() ) {
		if ( ! $id ) return true;
		if ( ! $content ) return true;

		$content = $this->build( $content );
		$file = dirname( dirname( __FILE__ ) ) . '/stylesheet/' . $id . '.css';

		try {
			if ( ! is_writable( dirname( $file ) ) ) {
				throw new Exception( $this->language->get( 'text_warning' ) . ' <strong>' . dirname( $file ) . '</strong>' );
			}
			$fo = fopen( $file, 'w+' );
			if ( $fo ) {
				fwrite( $fo, $content );
				return fclose( $fo );
			} else {
				throw new Exception( $this->language->get( 'text_warning' ) . ' <strong>' . $file . '</strong>' );
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}

}