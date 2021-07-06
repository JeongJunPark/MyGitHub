<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trades extends MY_Controller {

	public function index()
	{ 
        $this->Admin_model->verifyUser();

        $this->load->model('TradeModel');
        //header('Content-Type: application/json; charset=utf-8'); 

        $page= $this->input->get('page');
        $order= empty($this->input->get('order'))?"deposit_time DESC" : $this->input->get('order');
        $per_page= $this->input->get('per_page');

        if( !($page!=null && $page > 0) ) $page=1;
        if( !($per_page!=null && $per_page > 0) ) $per_page=1000;
        if($per_page > 1000 ) $per_page = 1000;
        $data['current_page'] =$page;
        $totalcount = 0;

        $search_params= $this->search_params($_SERVER['QUERY_STRING']);                        

        $vanumber = null;
        $iacctnm = null;
        $username = null;

        if(isset($search_params["virtual_account.number"]) && strlen($search_params["virtual_account.number"]) > 0 ){
            $vanumber = trim($search_params["virtual_account.number"]);   
        }
        if(isset($search_params["user.name"]) && strlen($search_params["user.name"]) > 0 ){  // 입주자로 검색    
            $username = trim($search_params["user.name"]);
        }
        if(isset($search_params["iacct_nm"]) && strlen($search_params["iacct_nm"]) > 0 ){    //입금자로 검색     
            $iacctnm = trim($search_params["iacct_nm"]);
        }

        $totalcount = $this->TradeModel->totalcount_search_by_vanumber_iacctnm_username($vanumber, $iacctnm, $username);
        $total_pages = (int) ($totalcount/$per_page);
        if($total_pages==0) $total_pages =1;
        if( $total_pages <  ($totalcount/$per_page) ) $total_pages++;
        $data['total_pages'] = $total_pages;
        $data['data'] = $this->TradeModel->search_by_vanumber_iacctnm_username($page, $order , $per_page ,$vanumber, $iacctnm, $username);

		$this->load->view('admin/header');
		$this->load->view('admin/trades/list', $data);
		$this->load->view('admin/footer');
	}

	public function list($page=null, $adminid=0) {
		$this->load->view('header');
		$this->load->view('trades/list', $data);
		$this->load->view('footer');
	}

}
