<?php
/**
 *
 * @package ThemeLexux Framework for OpenCart 3.x
 * @version 1.0.0
 * 
 */

if ( ! defined( 'DIR_SYSTEM' ) ) exit();

// define some key
defined( 'PAVOTHEMER_DIR' ) || define( 'PAVOTHEMER_DIR', dirname( __FILE__ ) );
defined( 'PAVOTHEMER_API' ) || define( 'PAVOTHEMER_API', 'http://localhost/opencart/api/index.php' );

require_once PAVOTHEMER_DIR . '/helper/settings.php';
require_once PAVOTHEMER_DIR . '/helper/theme.php';

if ( ! class_exists( 'PavoThemerController' ) ) :

	class PavoThemerController extends Controller {

		/**
		 * static $_instance insteadof PavoThemerController class
		 *
		 * @var PavoThemerController
		 * @since 1.0.0
		 */
		private static $_instance = null;

		/**
		 * Data array, pass it when setOutput
		 *
		 * @var $data array
		 * @since 1.0.0
		 */
		public $data = array(
				'notices'	=> array()
			);

		/**
		 * Template path, pass it to render template
		 *
		 * @var template string or null
		 * @since 1.0.0
		 */
		public $template = null;

		/**
		 * errors storge
		 * 
		 * @var $errors array
		 */
		protected $errors = array();

		/**
		 * Constructor Framework Controller
		 * @since 1.0.0
		 */
		public function __construct( $registry ) {
			parent::__construct( $registry );
		}

		/**
		 * Index method render layout
		 * 
		 * @since 1.0.0
		 */
		public function index() {
			$this->render();
		}

		/**
		 * Render Layout template
		 *
		 * @since 1.0.0
		 */
		public function render() {
			if ( $this->template ) {
				// set page document title
				if ( $this->language && $this->document ) $this->document->setTitle( $this->language->get( 'heading_title' ) );
				$this->data['errors'] = $this->errors;
				$this->data = array_merge( array(
					'header'		=> $this->load->controller( 'common/header' ),
					'column_left' 	=> $this->load->controller( 'common/column_left' ),
					'footer'		=> $this->load->controller( 'common/footer' )
				), $this->data );
				$this->response->setOutput( $this->load->view( $this->template, $this->data ) );
			} else {
				trigger_error( 'Template not found' ); die();
			}
		}

		/**
		 * Add message notice
		 * @since 1.0.0
		 * @return mixed
		 */
		public function addMessage( $message = '', $type = 'warning' ) {
			$this->data['notices'][] = $this->load->view( 'extension/module/pavothemer/notice', array( 'type' => $type, 'message' => $message ) );
		}

	}

endif;
