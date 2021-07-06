<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function index()
	{ 
        $data = array();
		$this->template
              ->set_layout('client')
              //->set("mgr_actions", $mgr_actions)
              ->build('clients/main',$data);
	}
}
