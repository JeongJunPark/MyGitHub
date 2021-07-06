<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tours extends MY_Controller {

	public function index()
	{ 
        $this->load->model('TourModel');
        header('Content-Type: application/json; charset=utf-8'); 

		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$row_perpage = $_POST['length']; // Rows display per page
		$column_index = $_POST['order'][0]['column']; // Column index
		$column_name = $_POST['columns'][$column_index]['data']; // Column name
		$column_sort_order = $_POST['order'][0]['dir']; // asc or desc
		$search_value = $_POST['search']['value']; // Search value
        $columns = $_POST['columns'];
//log_message('debug', '>>>>>>>>>>>>>>>>>');
//log_message('debug', json_encode($_POST));
//echo $row;exit;

        $search_query = " ";
        foreach($columns as $k => $v) {
            if($v['search']['value']!="") {
                if($v['data']=='h_name_kr') {
                    $search_query .= ' and h_name like "%'.$v['search']['value'].'%"';
                }else {
                    $search_query .= ' and '.$v['data'].' = "'.$v['search']['value'].'"';
                }
            }
        }

        if($search_value != ''){
           $search_query = " and (name like '%".$search_value."%' or 
                email like '%".$search_value."%' or 
                phone like '%".$search_value."%' ) ";
        }

        $order = $column_name." ".$column_sort_order;

        $totalcount = $this->TourModel->totalcount();
        $totalcount_with_filter = $this->TourModel->totalcount($search_query);


        $data['draw'] = intval($draw);
        $data['iTotalRecords'] = $totalcount;
        $data['iTotalDisplayRecords'] = $totalcount_with_filter;
        $data['aaData'] = $this->TourModel->get_list($row, $order, $row_perpage, $search_query);

        echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);    
	}

    public function apply() {

		$this->load->model('TourModel');
		header('Content-Type: application/json; charset=utf-8');

		$data = array();
		$data['name']         	= $this->input->post('name');

		$data['house_id']    	= empty($this->input->post('house_id')) ? 1 : $this->input->post('house_id');
		$data['phone']        	= empty($this->input->post('phone')) ? null : $this->input->post('phone');
		$data['email']        	= empty($this->input->post('email')) ? null : $this->input->post('email');
		$data['tour_date']    	= empty($this->input->post('tour_date')) ? null : $this->input->post('tour_date');
		$data['tour_time']    	= empty($this->input->post('tour_time')) ? null : $this->input->post('tour_time');
		$data['hope_movein']  	= empty($this->input->post('hope_movein')) ? null : $this->input->post('hope_movein');
		$data['hope_period']  	= empty($this->input->post('period')) ? null : $this->input->post('period');
		$data['current_residence'] 	= empty($this->input->post('current_residence')) ? null : $this->input->post('current_residence');
		$data['current_residence_status'] 	= empty($this->input->post('current_residence_status')) ? null : $this->input->post('current_residence_status');
		$data['current_residence_type'] 	= empty($this->input->post('current_residence_type')) ? null : $this->input->post('current_residence_type');
		$data['age']    		= empty($this->input->post('age')) ? null : $this->input->post('age');
		$data['gender'] 		= empty($this->input->post('gender')) ? null : $this->input->post('gender');
		$data['job']    		= empty($this->input->post('job')) ? null : $this->input->post('job');
		$data['path']   		= empty($this->input->post('path')) ? 0 : $this->input->post('path');
		$data['etc']    		= empty($this->input->post('etc')) ? null : $this->input->post('etc');


        try {

			/*
			if(empty($data['name'])) {
                throw new Exception('이름을 입력해주세요.', -1);
		    }
			*/
			
			if(empty($data['phone'])) {
                throw new Exception('휴대폰번호를 입력해주세요.', -1);
		    }


			$phone_ptn = '/^(010|011|016|017|018|019)[^0][0-9]{3,4}[0-9]{4}/';
            if(empty($data['phone']) || !preg_match($phone_ptn, $data['phone'])){
                throw new Exception('유효한 핸드폰 번호가 아닙니다. 다시 확인해주세요.', -2);
            }

			if(empty($data['house_id'])) {
				throw new Exception('지점을 선택해주세요.', -3);
			}

			$email_ptn = '/^[a-zA-Z]{1}[a-zA-Z0-9.\-_]+@[a-z0-9]{1}[a-z0-9\-]+[a-z0-9]{1}\.(([a-z]{1}[a-z.]+[a-z]{1})|([a-z]+))$/';
            if(!empty($data['email']) && !preg_match($email_ptn, $data['email'])){
                throw new Exception('유효한 이메일이 아닙니다. 다시 확인해주세요.', -4);
            }

			$ret_data =  $this->TourModel->save($data);

			if(empty($ret_data)) {
                throw new Exception('저장 에러. 관리자에 문의하세요.', -5);
            }

            //알림톡 전송

			/*
			$this->load->model('MessageTemplatesModel');
			$template = $this->MessageTemplatesModel->find_by_code("movein");
			$locals = array(
                    "%{house_name}"	=>$house["name_kr"],
                    "%{address}"	=>$row["full_address"],
                    "%{username}"	=>$row["user_name"],
                    "%{pws}"		=>$row["pws"],
                    "%{life_info}"	=>$row["life_info"]
            );
            $content = str_ireplace(array_keys($locals), array_values($locals), $template["content"]);
            $content = $template["content"];
            $this->load->library('Sms');
            $this->sms->send_alarmtalk($template["subject"], $data["phone"], $template["tmpl_cd"], $content);
			*/

        	if($ret_data && $data['email']) {
        	    //이메일 전송
				$data['tour_id'] = $ret_data;
				$data['language'] = empty($this->input->post('language')) ? 'kr' : $this->input->post('language');
        	    $ret_email = $this->send_email($data);
				if(!$ret_email) {
                	throw new Exception('이메일 전송 실패.', -6);
				}
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
		$this->load->model('TourModel');
		header('Content-Type: application/json; charset=utf-8');

		$id = $this->input->post('id');
		$data = array();
		$data['name']    = empty($this->input->post('name')) ? null : $this->input->post('name');
		$data['current_residence'] 	= empty($this->input->post('current_residence')) ? null : $this->input->post('current_residence');
		$data['current_residence_status'] 	= empty($this->input->post('current_residence_status')) ? null : $this->input->post('current_residence_status');
		$data['current_residence_type'] 	= empty($this->input->post('current_residence_type')) ? null : $this->input->post('current_residence_type');
		$data['age']    = empty($this->input->post('age')) ? null : $this->input->post('age');
		$data['gender'] = empty($this->input->post('gender')) ? null : $this->input->post('gender');
		$data['job']    = empty($this->input->post('job')) ? null : $this->input->post('job');
		$data['path']   = empty($this->input->post('path')) ? null : $this->input->post('path');
		$data['etc']    = empty($this->input->post('etc')) ? null : $this->input->post('etc');

        if($data['current_residence_status'] == '기타') {
		    $data['current_residence_status'] 	= empty($this->input->post('current_residence_status_etc')) ? null : $this->input->post('current_residence_status_etc');
        }
        if($data['current_residence_type'] == '기타') {
		    $data['current_residence_type'] 	= empty($this->input->post('current_residence_type_etc')) ? null : $this->input->post('current_residence_type_etc');
        }
		
		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}


		$result_data =  $this->TourModel->update($id, $data);
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

	public function remove()
    {
		$this->load->model('TourModel');
		header('Content-Type: application/json; charset=utf-8');

		$id = $this->input->post('id');
		
		if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}


		$result_data =  $this->TourModel->update($id, array('deleted'=>1));
		
		$this->output->set_status_header(200);
		echo json_encode($result_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    }

    public function change_state()
    {
		$this->load->model('TourModel');
		$this->load->model('TourStateModel');
		header('Content-Type: application/json; charset=utf-8');

        $id = $this->input->post('id');
		$data = array();
		$data['state'] 	= $this->input->post('state');

        if(empty($id)) {
		    echo json_encode(array('error'=>-1, 'msg'=>'error'),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		    exit;
		}

        try {
            $this->db->trans_begin();

		    $this->TourModel->update($id, $data);

            $data['tour_id'] = $id;
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

    public function send_email($params)
    {
        $this->load->model('HouseModel');
        $this->load->library('CommonLib');
        $commomlib = new CommonLib();

		$house_idx = $params['house_id'];
		$house_info = $this->HouseModel->find('id='.$house_idx, true);

		$lang = $params['language'];
    	$house_name = $house_info['name_'.$lang];
		$email_addr = 'celibsoonra@celib.kr';
    	$kakao = array(
    	        'id'    => 'celiblifeandstay',
    	        'addr'  => 'https://pf.kakao.com/_ESsbs'
    	        );
    	//$preview = 'https://www.airbnb.co.kr/users/show/318904959?_set_bev_on_new_domain=1591682830_NDU5NDE5YTU2ZDRk';
		if($house_idx == 1){	// 셀립순라
			$email_addr = 'celibsoonra@celib.kr';

		}else if($house_idx == 2){	// 셀립여의
			$email_addr = 'celibyeoui@celib.kr';

		}else if($house_idx == 3){	// 셀립은평
			$email_addr = 'celibeunpyong@celib.kr';

		}else if($house_idx == 4){	// 셀립용산
			$email_addr = 'celibyongsan@celib.kr';

		}

/*
		if($house_idx == 4){
    	    $email_addr = 'celibyongsan@celib.kr';
    	    $kakao = array(
    	        'id'    => 'celibyongsan',
    	        'addr'  => 'https://pf.kakao.com/_TmfxoK'
    	        );
    	    //$preview = 'https://www.notion.so/6a8a576e9c5a4673b262d5af0d228f21';
    	}
*/
		$data['from_email'] = $email_addr;
		$data['to_email'] = $params['email'];
		$data['subject'] = "안녕하세요! ".$house_name."입니다.";

        $data['body'] = $this->load->view('emails/tour_apply', array('house_info'=>$house_info, 'params'=>$params, 'house_name'=>$house_name, 'kakao'=> $kakao, 'email_addr'=> $email_addr, 'preview'=>$preview) , TRUE);

        /*
		if($lang == 'en') {
                    $subject = "Hello from ".$house_name."!";
                    $body = "Dear ".$params['name'].",
<br>Thank you for booking a tour at ".$house_name.".
<br>
<br>We will be preparing a tour for you to get a taste of living at .".$house_name.".
<br>Upon your arrival at ".$params['tour_time'].":00 ".date('d. M. Y', strtotime($params['tour_date'])).", our manager will walk you through all types of living spaces and inform you about our services in detail so that you can better envision your potential life at célib.
<br>
<br>Please expect the tour to take about 30-60 minutes of your time, and it is a chance for you to ask any questions you have about living at célib, and discuss any concerns freely.
<br>
<br>If you’d like to take a look at célib room types before visiting in person, you can find us on Airbnb <a href='".$house_info['airbnb']."' target='_blank'>here</a>. Many of our residents go for a 1-3 day trial run of living at célib via Airbnb, our official partner, before making the big decision on where to live.
<br>
<br>To find your way to ".$house_name." double-check our location, you can view to exact address <a href='https://".HOST.$house_info['info_addr']."' target='_blank'>here</a>.
<br>
<br>If you have any lingering questions related to the tour, please don’t hesitate reaching out to us via email or <a href='http://pf.kakao.com/_xcxejLxb' target='_blank'>Kakao</a>!
<br>
<br>We look forward to meeting you!
<br>".$house_name."
<br>
<br>phone: <a href='tel:+82-02-743-1013'>+82-02-743-1013</a>
<br>kakao ID: <a href='http://pf.kakao.com/_xcxejLxb' target='_blank'>celibsoonra</a>
<br>email: <a href='mailto:reservations@celib.kr'>reservations@celib.kr</a>
<br>instagram: @celib_lifeandstay";
		}
        */

        $ret = $commomlib->send_email( $data );


		//매니저 알림메일
		if(ENVIRONMENT == 'production') {
			$data1['from_email'] = 'woozoo@woozoo.kr';
			$data1['to_email'] = $email_addr;
            $data1['subject'] = "[".$house_name."투어신청]".$params['phone'];
            $data1['body'] = $params['phone']." 번호로 ".$house_name."에 투어를 신청하였습니다.";

        	$commomlib->send_email( $data1 );
        }
		return $ret;
    }
	
}
