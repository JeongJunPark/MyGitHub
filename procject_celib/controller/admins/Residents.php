<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Residents extends MY_Controller {

	public function index()
	{ 
        $this->load->model('ResidentModel');
        //header('Content-Type: application/json; charset=utf-8'); 

        $page= $this->input->get('page');
        $order= empty($this->input->get('order'))?"deposit_time DESC" : $this->input->get('order');
        $per_page= $this->input->get('per_page');

        if( !($page!=null && $page > 0) ) $page=1;
        if( !($per_page!=null && $per_page > 0) ) $per_page=1000;
        if($per_page > 1000 ) $per_page = 1000;
        $data['current_page'] =$page;
        $totalcount = 0;

        $totalcount = $this->ResidentModel->totalcount();
        $total_pages = (int) ($totalcount/$per_page);
        if($total_pages==0) $total_pages =1;
        if( $total_pages <  ($totalcount/$per_page) ) $total_pages++;
        $data['total_pages'] = $total_pages;
        $data['data'] = $this->ResidentModel->get_list($page, $order, $per_page);

		$this->load->view('admin/header');
		$this->load->view('admin/residents/list', $data);
		$this->load->view('admin/footer');
	}

}
