<?php

class ControllerNoticeNotice extends Controller {

	public $data = array();

	public function __construct( $registry, $data = array() ) {
		parent::__construct( $registry, $data );
		$this->data = $data;
	}

	/**
	 * 
	 * Print message
	 *
	 * @param $this->data array( 'type' => 'info', 'message' => '' )
	 * @return mixed html
	 */
	public function index() {
		if ( isset( $this->data['type'], $this->data['message'] ) ) {
			$this->load->view( 'notice/notice', $this->data );
		}
	}

}
