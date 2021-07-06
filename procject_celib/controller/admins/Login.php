<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{	
		/*
		Error List:
		0 - No Error
		1 - Too Many Login Attempts
		2 - Bad Credentials
		*/
		$data["error"] = 0;
		if ($this->input->post()){ 
			if ($this->session->userdata("loginattempts")) {
				echo "2";
				$postData = $this->input->post();
				$loginattempts = $this->session->userdata("loginattempts");
				if ($loginattempts > 4) { 
					$data["error"] = 1;
					$this->load->view('admin/login', $data);
				 } else {
				 	$auth = $this->Admin_model->adminLogin($postData);
					if ($auth == true) {
						redirect(base_url()."admin", "auto");
					} else {
						$data["error"] = 2;
						$this->load->view('admin/login', $data);
					}
				 } 
			} else {
				echo "1";
				$this->session->set_userdata("loginattempts", 0);
				$postData = $this->input->post();
				$auth = $this->Admin_model->adminLogin($postData);
				if ($auth == true) {
					redirect(base_url()."admin", "auto");
				} else {
					$data["error"] = 2;
					$this->load->view('admin/login', $data);
				}
			} 
		} else {
			$this->load->view('admin/login', $data);
		}
		
	}
}
