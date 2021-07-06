<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tour_state extends MY_Controller {

	public function index()
	{ 
        $this->load->model('TourStateModel');
        header('Content-Type: application/json; charset=utf-8'); 

		$tour_id = $this->input->post('tour_id');

        $data['data'] = $this->TourStateModel->get_list(0, 'created_at desc', 20, ' and tour_id = '.$tour_id);

        echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);    
	}

    // 투어날짜 등록
	public function add() {

		$this->load->model('TourModel');
		$this->load->model('TourStateModel');
		header('Content-Type: application/json; charset=utf-8');

		$data = array();

		$id = $this->input->post('id');

        if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}

		$data['tour_id']	  = empty($this->input->post('id')) ? null : $this->input->post('id');	// tours 테이블 key = tour_state 테이블 tour_id
		$data['state']   	  = '3';			// 투어예정으로 변경
		//memo column
		$data['memo']   	  = (empty($this->input->post('tour_date')) || empty($this->input->post('tour_time'))) ? null : '{"tour_date":"'.$this->input->post('tour_date').'", "tour_time":"'.$this->input->post('tour_time').'"}'; 
		
		$tourModel_data['tour_date']    = empty($this->input->post('tour_date')) ? null : $this->input->post('tour_date');
		$tourModel_data['tour_time']    = empty($this->input->post('tour_time')) ? null : $this->input->post('tour_time');
		$tourModel_data['state']		= '3';	// 투어예정으로 변경

        try {
            $this->db->trans_begin();

			$this->TourModel->update($id, $tourModel_data);	// tours => tour_date,tour_time UPADATE

		    $this->TourStateModel->save($data);
		    
            $this->db->trans_commit();

		    $this->output->set_status_header(200);

			$ret_data = array(
			        "success"=> true
			);

		    echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }catch(Exception $e){
            error_log(print_r($e, true));
            $this->db->trans_rollback();
            $this->output->set_status_header(500);
            $ret_data = array(
                            "success"=> false,
                            "errors"=> array()
                    );
            echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

    }

    // 투어날짜 삭제
	public function remove() {
		
		$this->load->model('TourModel');
		$this->load->model('TourStateModel');
		header('Content-Type: application/json; charset=utf-8');

		$data = array();

		$id = $this->input->post('id');

		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}

		$data['tour_id']	  = empty($this->input->post('id')) ? null : $this->input->post('id');	// tours 테이블 key = tour_state 테이블 tour_id
		$data['state']   	  = empty($this->input->post('state')) ? 0 : $this->input->post('state');	
		//memo column
		$data['memo']   	  = null;
		$tourModel_data['tour_date']    = null;
		$tourModel_data['tour_time']    = null;
		
        try {
            $this->db->trans_begin();

			$this->TourModel->update($id, $tourModel_data);	// tours => tour_date,tour_time UPADATE

		    $this->TourStateModel->save($data);
		    
            $this->db->trans_commit();

		    $this->output->set_status_header(200);

			$ret_data = array(
			        "success"=> true
			);

		    echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        }catch(Exception $e){
            error_log(print_r($e, true));
            $this->db->trans_rollback();
            $this->output->set_status_header(500);
            $ret_data = array(
                            "success"=> false,
                            "errors"=> array()
                    );
            echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

	}

}
