<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privacy extends MY_Controller {

	// 개인정보 수집 및 이용동의
	public function index()
	{ 
        $data = array();
		$this->load->view('clients/privacy', $data);
	}
	
	// 개인정보 제3자 제공동의
	public function terms()
	{ 
        $data = array();
		$this->load->view('clients/terms', $data);
	}

}
