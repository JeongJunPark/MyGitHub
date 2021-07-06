<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Houses extends MY_Controller {

	public function index()
	{ 
        $this->Admin_model->verifyUser();

        $this->load->model('HouseModel');
		
        $data['data'] = $this->HouseModel->get_list(0, 'id desc', 10000, 0);
		
		$data['admin_data'] = $this->Admin_model->getAdmins();

		$this->load->view('admin/header');
		$this->load->view('admin/houses/list', $data);
		$this->load->view('admin/footer');

	}

	public function save()
	{ 
	
		header('Content-Type: application/json; charset=utf-8');
        $this->Admin_model->verifyUser();
        $this->load->model('HouseModel');
		
		$name_jsonData	= array($_POST['h_name'],$_POST['e_name']);

		$data['h_name'] = json_encode($name_jsonData);
		
		$data['admin_id']	= (!empty($_POST['admin_id']))	? $_POST['admin_id'] : '';
		$data['h_name']		= ((!empty($_POST['h_name'])) && (!empty($_POST['e_name']))) ? '{"kr":"'.$_POST['h_name'].'", "en":"'.$_POST['e_name'].'"}' : '';
		$data['flagship']	= (!empty($_POST['flagship']))	? $_POST['flagship'] : '';
		$data['airbnb']		= (!empty($_POST['airbnb']))	? $_POST['airbnb'] : '';
		$data['kakao']		= (!empty($_POST['kakao']))		? $_POST['kakao'] : '';
		$data['info_addr']	= (!empty($_POST['info_addr'])) ? $_POST['info_addr'] : '';
		$data['address']	= (!empty($_POST['address']))	? $_POST['address'] : '';
		$data['type']		= (!empty($_POST['type']))		? $_POST['type'] : '';
		$data['status']		= (!empty($_POST['status']))	? $_POST['status'] : '';

        try {
			if(empty($data['h_name'])) {
                throw new Exception('지점명을 입력해주세요.', -1);
		    }

			$ret_data = $this->HouseModel->save($data);

			if(empty($ret_data)) {
                throw new Exception('저장 에러. 관리자에 문의하세요.', -5);
            }

            $result_data = array('code'=>$ret_data, 'msg'=>'신청 완료');

        } catch(Exception $e) {
            $result_data = array('code'=>$e->getCode(), 'msg'=> $e->getMessage());
        }
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		exit;
	}

	public function update()
	{ 
		header('Content-Type: application/json; charset=utf-8');
        $this->Admin_model->verifyUser();
        $this->load->model('HouseModel');
		
		$id					= (!empty($_POST['id']))		? $_POST['id'] : null;	// key
		$data['admin_id']	= (!empty($_POST['admin_id']))	? $_POST['admin_id'] : null;
		$data['h_name']		= ((!empty($_POST['h_name'])) && (!empty($_POST['e_name']))) ? '{"kr":"'.$_POST['h_name'].'", "en":"'.$_POST['e_name'].'"}' : null;
		$data['flagship']	= (!empty($_POST['flagship']))	? $_POST['flagship'] : null;
		$data['airbnb']		= (!empty($_POST['airbnb']))	? $_POST['airbnb'] : null;
		$data['kakao']		= (!empty($_POST['kakao']))		? $_POST['kakao'] : null;
		$data['info_addr']	= (!empty($_POST['info_addr'])) ? $_POST['info_addr'] : null;
		$data['address']	= (!empty($_POST['address']))	? $_POST['address'] : null;
		$data['type']		= (!empty($_POST['type']))		? $_POST['type'] : null;
		$data['status']		= (isset($_POST['status']))		? $_POST['status'] : null;

		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}
		
		$result_data =  $this->HouseModel->update($id, $data);
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	}

	public function remove()
    {
		header('Content-Type: application/json; charset=utf-8');
        $this->Admin_model->verifyUser();
        $this->load->model('HouseModel');
		$id	= (!empty($_POST['id'])) ? $_POST['id'] : null;	// key
		
		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}


		$result_data =  $this->HouseModel->remove($id);
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

	// key값으로 1건 조회 함수
	public function search_one()
	{ 
		header('Content-Type: application/json; charset=utf-8');
        $this->Admin_model->verifyUser();
        $this->load->model('HouseModel');
		
		$key_id = (!empty($_POST['key_id'])) ? $_POST['key_id'] : null;

		if(empty($key_id)){
			
			$result_data = array('data'=>999, 'msg'=>'오류발생');
			echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
			exit;
		}

        $list_data = $this->HouseModel->search_by_list_one($key_id);

		$data['id']			= $list_data[0]['id'];			// key
		$data['admin_id']	= $list_data[0]['admin_id'];	// 담당매니저
		$data['name_kr']	= $list_data[0]['name_kr'];		// 한글명
		$data['name_en']	= $list_data[0]['name_en'];		// 영문명
		$data['flagship']	= $list_data[0]['flagship'];	// 플래그쉽 이름
		$data['airbnb']		= $list_data[0]['airbnb'];		// airbnb 주소
		$data['kakao']		= $list_data[0]['kakao'];		// 카카오톡 아이디 주소
		$data['info_addr']	= $list_data[0]['info_addr'];	// 웹 소개페이지 주소
		$data['address']	= $list_data[0]['address'];		// 실제 주소
		$data['type']		= $list_data[0]['type'];		// 지점 타임
		$data['status']		= $list_data[0]['status'];		// 상태
		
		$result_data = array('data'=>$data, 'msg'=>'조회 완료');
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		exit;

	}

}
