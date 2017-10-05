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
			$css[] = implode( '', $this->elementBuilder( $row, '.pa-row-inner' ) );
		}

		$css = implode( '', $css );
		return $css;
	}

	/**
	 * element builder css
	 */
	private function elementBuilder( $element = array(), $inner = '.pa-row-inner' ) {
		$css = array();
		$settings = ! empty( $element['settings'] ) ? $element['settings'] : array();
		$id = false;
		if ( ! empty( $settings['uniqid_id'] ) ) {
			$id = $settings['uniqid_id'];
		} else if ( ! empty( $settings['specifix_id'] ) ) {
			$id = $settings['specifix_id'];
		}

		if ( $id ) {
			$css[] = '#' . $id . ' ' . $inner . ' > div:first{';
			$css[] = ! empty( $settings['color'] ) ? 'color:' . $settings['color'] . ';' : '';
			$base_url = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
			$css[] = ! empty( $settings['background_image'] ) ? 'background-image: url( '. $base_url . 'image/' . $settings['background_image'].' )' . ';' : '';
			$css[] = ! empty( $settings['background_color'] ) ? 'background-color: '.$settings['background_color'] . ';' : '';
			if ( ! isset( $settings['parallax'] ) || ! $settings['parallax'] ) {
				$css[] = ! empty( $settings['background_repeat'] ) ? 'background-repeat: '.$settings['background_repeat'] . ';' : '';
				$css[] = ! empty( $settings['background_position'] ) ? 'background-position: '.$settings['background_position'] . ';' : '';
			}
			if ( ! empty( $settings['styles'] ) ) {
				foreach ( $settings['styles'] as $attr => $value ) {
					$parser = explode( '_', $attr );
					if ( isset( $parser[1] ) && in_array( $parser[1], array( 'top', 'left', 'bottom', 'right' ) ) ) {
						$css[] = $value ? str_replace( '_', '-', $attr ) . ':' . $value . 'px;' : 0;
					} else {
						$css[] = $value ? str_replace( '_', '-', $attr ) . ':' . $value . ';' : '';
					}
				}
			}

			$css[] = '}';

			$responsive = ! empty( $element['responsive'] ) ? $element['responsive'] : array();
			// just columns
			if ( $responsive ) {
				// var_dump($element['columns'][0]['responsive']);
				$responsive = array_reverse( $responsive );
				foreach ( $responsive as $type => $opt ) {
					if ( ! empty( $opt['cols'] ) ) {
						$customWidth = array();
						$write = false;
						switch ( $type ) {
							case 'lg':
								$customWidth[] = '@media (min-width: 1200px){';
								break;

							case 'md':
								$customWidth[] = '@media (min-width: 992px){';
								break;

							case 'sm':
								$customWidth[] = '@media (min-width: 768px){';
								break;
							
							default:
								$customWidth[] = '@media (max-width: 768px){';
								break;
						}

						$customWidth[] = '#' . $id . '.col-' . $type . '-' . $opt['cols'] . '{';

						$styles = ! empty( $opt['styles'] ) ? $opt['styles'] : array();
						if ( ! empty( $styles['width'] ) ) {
							$write = true;
							$customWidth[] = 'width:' . $styles['width'] . '%;';
						}
						$customWidth[] = '}';
						if ( in_array( $type, array( 'lg', 'md', 'sm', 'xs' ) ) ) {
							$customWidth[] = '}';
						}

						if ( $write ) {
							$css[] = implode( ' ', $customWidth );
						}
					}
				}
			}
		}

		$subs = ! empty( $element['columns'] ) ? $element['columns'] : ( ! empty( $element['elements'] ) ? $element['elements'] : array() );
		$inner = ! empty( $element['columns'] ) ? '.pa-column-inner' : '.element-inner';

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