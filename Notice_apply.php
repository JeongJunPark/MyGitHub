<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notice_apply extends MY_Controller {

	public function index()
	{ 
		if ($this->Admin_model->verifyUser()) {
            //$this->load->model('tourModel');
            //header('Content-Type: application/json; charset=utf-8'); 
			$this->load->model('HouseModel');
			$data['HouseData'] = $this->HouseModel->get_list(0, 'id desc', 10000, 1);

		    $this->load->view('admin/header');
		    $this->load->view('admin/notice_apply/list',$data);
		    $this->load->view('admin/footer');
        }
	}

}
