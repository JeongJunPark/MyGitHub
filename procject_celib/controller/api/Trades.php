<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//api 
class Trades  extends MY_Controller{

    public function index()
    { 
        //  parent::index();    // $search_params 을 가져온다.        

        $this->load->model('TradeModel');
        header('Content-Type: application/json; charset=utf-8'); 

        $draw = $_POST['draw'];
		$row = $_POST['start'];
		$row_perpage = $_POST['length']; // Rows display per page
		$column_index = $_POST['order'][0]['column']; // Column index
		$column_name = $_POST['columns'][$column_index]['data']; // Column name
		$column_sort_order = $_POST['order'][0]['dir']; // asc or desc
		$search_value = $_POST['search']['value']; // Search value

        $search_query = " ";
        if($search_value != ''){
           $search_query = " and (t.iacct_nm like '%".$search_value."%' or 
                va.number like '%".$search_value."%' or 
                u.name like '%".$search_value."%' ) ";
        }

        $order = $column_name." ".$column_sort_order;

        $totalcount = $this->TradeModel->totalcount();
        $totalcount_with_filter = $this->TradeModel->totalcount($search_query);

        $data['draw'] = intval($draw);
        $data['iTotalRecords'] = $totalcount;
        $data['iTotalDisplayRecords'] = $totalcount_with_filter;
        $data['aaData'] = $this->TradeModel->get_list($row, $order, $row_perpage, $search_query);

        echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);    

    }

}
