<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class House extends MY_Controller {

	public function index()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              //->set("mgr_actions", $mgr_actions)
              ->build('clients/main',$data);
	}

	// 셀립 순라
	public function soonra()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              ->build('clients/soonra',$data);
	}

	// 셀립 은평
	public function eunpyong()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              ->build('clients/eunpyong',$data);
	}

	// 셀립 여의
	public function yeoui()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              ->build('clients/yeoui',$data);
	}

	// 셀립 용산
	public function yongsan()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              ->build('clients/yongsan',$data);
	}
}
