<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notice_apply extends MY_Controller {

	public function index()
	{ 
        $this->load->model('NoticeApplyModel');
        header('Content-Type: application/json; charset=utf-8'); 

		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$row_perpage = $_POST['length']; // Rows display per page
		$column_index = $_POST['order'][0]['column']; // Column index
		$column_name = $_POST['columns'][$column_index]['data']; // Column name
		$column_sort_order = $_POST['order'][0]['dir']; // asc or desc
		$search_value = $_POST['search']['value']; // Search value
        $columns = $_POST['columns'];

        $search_query = " ";

        foreach($columns as $k => $v) {
            if($v['search']['value']) {
                if($v['data']=='h_name_kr') {
                    $search_query .= ' and h_name like "%'.$v['search']['value'].'%"';
                }else {
                    $search_query .= ' and '.$v['data'].' = "'.$v['search']['value'].'"';
                }
            }
        }
//log_message('debug', '>>>>>>>>>>>>>>>>>');
//log_message('debug', json_encode($_POST));
//echo $row;exit;

        if($search_value != ''){
           $search_query .= " and (name like '%".$search_value."%' or 
                email like '%".$search_value."%' or 
                phone like '%".$search_value."%' ) ";
        }

        $order = $column_name." ".$column_sort_order;

        $totalcount = $this->NoticeApplyModel->totalcount();
        $totalcount_with_filter = $this->NoticeApplyModel->totalcount($search_query);


        $data['draw'] = intval($draw);
        $data['iTotalRecords'] = $totalcount;
        $data['iTotalDisplayRecords'] = $totalcount_with_filter;
        $data['aaData'] = $this->NoticeApplyModel->get_list($row, $order, $row_perpage, $search_query);

        echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);    
	}

    public function apply() {

		$this->load->model('NoticeApplyModel');
		header('Content-Type: application/json; charset=utf-8');

		$data = array();
		$data['name']         	= $this->input->post('name');

		$data['phone']        	= $this->input->post('phone');
		$data['email']        	= $this->input->post('email');
		$data['house_id']    	= $this->input->post('house_id');
		$data['etc']    		= empty($this->input->post('etc')) ? null : $this->input->post('etc');
        try {
            //전화번호 이매일 유효성체크
            if(empty($data['house_id'])) {
                throw new Exception('선택된 지점이 없습니다.', 0);
		    }
		    if(empty($data['name'])) {
                throw new Exception('이름을 입력해주세요.', -1);
		    }
            
            $phone_ptn = '/^(010|011|016|017|018|019)[^0][0-9]{3,4}[0-9]{4}/';
            if(empty($data['phone']) || !preg_match($phone_ptn, $data['phone'])){
                throw new Exception('유효한 핸드폰 번호가 아닙니다. 다시 확인해주세요.', -2);
            }

            $email_ptn = '/^[a-zA-Z]{1}[a-zA-Z0-9.\-_]+@[a-z0-9]{1}[a-z0-9\-]+[a-z0-9]{1}\.(([a-z]{1}[a-z.]+[a-z]{1})|([a-z]+))$/';
            if(empty($data['email']) || !preg_match($email_ptn, $data['email'])){
                throw new Exception('유효한 이메일이 아닙니다. 다시 확인해주세요.', -3);
            }

            //중복체크 phone, house_id
            $condi = 'phone= "'.$data['phone'].'" and house_id = '.$data['house_id'];
		    $duplicate_check =  $this->NoticeApplyModel->find($condi, true);
            if(!empty($duplicate_check)) {
                throw new Exception('이미 신청되었습니다.', -4);
            }
            

		    $ret =  $this->NoticeApplyModel->save($data);
            if(empty($ret)) {
                throw new Exception('저장 에러. 관리자에 문의하세요.', -5);
            }

            $result_data = array('code'=>$ret, 'msg'=>'신청 완료');
		    
        } catch(Exception $e) {
            $result_data = array('code'=>$e->getCode(), 'msg'=> $e->getMessage());
        }

		$this->output->set_status_header(200);
		echo json_encode($result_data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		exit;
    }

    public function update()
    {
		$this->load->model('NoticeApplyModel');
		header('Content-Type: application/json; charset=utf-8');

		$id = $this->input->post('id');
		$data = array();
		$data['etc']    = empty($this->input->post('etc')) ? null : $this->input->post('etc');

		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}


		$result_data =  $this->NoticeApplyModel->update($id, $data);
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

	public function remove()
    {
		$this->load->model('NoticeApplyModel');
		header('Content-Type: application/json; charset=utf-8');

		$id = $this->input->post('id');
		
		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}


		$result_data =  $this->NoticeApplyModel->update($id, array('deleted'=>1));
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

    public function change_state()
    {
		$this->load->model('NoticeApplyModel');
		header('Content-Type: application/json; charset=utf-8');

        $id = $this->input->post('id');
		$data = array();
		$data['state'] 	= $this->input->post('state');

        if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}

		$this->NoticeApplyModel->update($id, $data);

		$this->output->set_status_header(200);

		$ret_data = array(
		        "success"=> true
		);

		echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

}
