<?php

class FreebookClass {

	var $db;
	var $_YOIL_KOR_ARR = Array('��','��','ȭ','��','��','��','��');
	var $work_ip_arr = Array(
		'222\.122\.234\.27',    // ���� ���߼���
		'222\.122\.229\.55',    // PASS ���߼���
		'14\.49\.30\.111',    // PASS �Ǽ���
		'14\.49\.30\.79',    // ���� �Ǽ���

		'192\.168\.56\.',       // ���� VM
		'192\.168\.99\.',       // ���� VM - docker

		'172\.20\.',            // �系IP
		'172\.16\.',            // �系IP
		'10\.10\.',             // �系IP
		'10\.11\.',             // �系IP
		'10\.12\.',             // �系IP
		'10\.0\.',              // �系IP
		'172\.30\.',            // VPN

		'211\.222\.90\.194',    // ����AP - Wi-Fi 6
		'121\.135\.246\.180',   // ����AP - Wi-Fi 8
		'121\.135\.246\.164',   // ����AP - Wi-Fi 9
		'211\.222\.90\.184',    // ����AP - Wi-Fi 10
		'121\.135\.246\.137',	// ����AP - Wi-Fi 11
		'211\.219\.65\.10',     // ����AP - Wi-Fi 4F2 �������

	);

	function FreebookClass($db) {
		$this->db = $db;
	}

	public function setDB($db) {
		$this->db = $db;
	}

	public function isInsideIp() {
		global $_DEV, $_SERVER;

        if($_DEV != 'l' && $_DEV != 't' && $_DEV != 'q' && !preg_match('/'.implode('|', $this->work_ip_arr).'/', $_SERVER['REMOTE_ADDR'])) {
			return false;
        }

		return true;
	}

	#������� ���� ��������
	public function getFreebook($param) {
		$freebook_idx = $param['freebook_idx'];

		$sql = "SELECT * FROM gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$return_data['msg']  = '';
		$return_data['data'] = $freebook_info;
		$return_data['error'] = 0;
		return $return_data;
	}

	#������� ���� ����Ʈ ��������
	public function getCalendarList($param) {
		$freebook_idx = $param['freebook_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx)) {
			$return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select * from gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and use_flag='Y' order by started_at asc";
		$calendar_list = $this->db->sqlRowArr($sql);

		for($i=0,$cnt=count($calendar_list);$i<$cnt;$i++) {
			$yoil_num = date('w',strtotime($calendar_list[$i]['started_at']));
			$calendar_list[$i]['started_date'] = date('m/d', strtotime($calendar_list[$i]['started_at'])).'('.$this->_YOIL_KOR_ARR[$yoil_num].')';
			$calendar_list[$i]['is_end'] = false;
			if($calendar_list[$i]['ended_at'] <= $now_date) {
				$calendar_list[$i]['is_end'] = true;
			}
		}

		$return_data['msg']  = '';
		$return_data['data'] = $calendar_list;
		$return_data['error'] = 0;
		return $return_data;
	}

	#��÷�ڼ� üũ�Լ�
	public function getSuccessMember($param) {
		$freebook_idx = $param['freebook_idx'];
		$calendar_info = $param['calendar_info'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx)) {
			$return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

        $cnt = 0;
		if($calendar_info['idx']) {
            $sql = "SELECT COUNT(*) FROM gosivod.freebook_member WHERE freebook_calendar_idx=" . $calendar_info['idx'] . " AND result_state='Y'";
            $cnt = $this->db->sqlRowOne($sql);
        }

		$return_data['msg']  = '';
		$return_data['data'] = $cnt;
		$return_data['error'] = 0;
		return $return_data;
	}
    #���� �̺�Ʈ ��÷�ڼ� üũ�Լ�
    public function getSuccessMemberToday($param) {
        $freebook_idx = $param['freebook_idx'];
        $item_list = $param['item_list'];

        if(empty($freebook_idx)) {
            $return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
            $return_data['error'] = 1;
            return $return_data;
        }

        $cnt = array();
        foreach($item_list as $key => $value){
            $sql = "SELECT COUNT(*) FROM gosivod.freebook_member WHERE freebook_calendar_idx=".$value['idx']." AND result_state='Y'";
            $cnt[$key] = $this->db->sqlRowOne($sql);
        }

        $return_data['msg']  = '';
        $return_data['cnt'] = $cnt;
        $return_data['error'] = 0;
        return $return_data;
    }

	#���� �������� ������� ���� ���� ��������
	public function getProceedingCalendar($param) {
		$freebook_idx = $param['freebook_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx)) {
			$return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($now_date)) {
			$return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "SELECT count(*) FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and ended_at > '".$now_date."' and use_flag='Y' order by ended_at asc";
		$cnt = $this->db->sqlRowOne($sql);

		if($cnt > 0) {
			$sql = "SELECT * FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and ended_at > '".$now_date."' and use_flag='Y' order by ended_at asc limit 1";
		}
		else {
			$sql = "SELECT * FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and use_flag='Y' order by ended_at desc limit 1";
		}
		$calendar_info = $this->db->sqlRow($sql);

		if($calendar_info['idx']) {
            $sql = "SELECT lec_no FROM gosivod.freebook_good where freebook_calendar_idx =" . $calendar_info['idx'];
            $calendar_info['lec_no'] = $this->db->sqlRowOne($sql);
        }

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����
		$grap_timestmap = strtotime($start_date) - strtotime($now_date);

		if($now_date < $start_date) {
			if(substr($now_date, 0, 10) == substr($start_date, 0, 10)) {
				$calendar_info['quiz_question'] = '����� ���� '.date('H�� i��', strtotime($start_date)).'�� �����˴ϴ� :)';
			}
			#�̺�Ʈ�� ������ �������̰� �������� ���
			else if(date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$calendar_info['quiz_question'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$calendar_info['quiz_question'] = '�̺�Ʈ ���� �ð��� �ƴմϴ�.';
			}
		}

		if($now_date >= $calendar_info['ended_at']) {
			$calendar_info['quiz_question'] = '�̺�Ʈ�� ����Ǿ����ϴ�.';
		}

		$return_data['msg']  = '';
		$return_data['data'] = $calendar_info;
		$return_data['error'] = 0;
		return $return_data;
	}

    #���� �������� ������� ���� ���� �������� (��� ����)
    public function getProceedingCalendarNoQuiz($param) {
        $freebook_idx = $param['freebook_idx'];
        $now_date = date('Y-m-d H:i:s');

        #���ξ������̰� now_date�� ���� ��� ����
        if($this->isInsideIp() && !empty($param['now_date'])) {
            $now_date = $param['now_date'];
        }

        if(empty($freebook_idx)) {
            $return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
            $return_data['error'] = 1;
            return $return_data;
        }

        if(empty($now_date)) {
            $return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
            $return_data['error'] = 1;
            return $return_data;
        }

        $sql = "SELECT count(*) FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and ended_at > '".$now_date."' and use_flag='Y' order by ended_at asc";
        $cnt = $this->db->sqlRowOne($sql);

        if($cnt > 0) {
            $sql = "SELECT * FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and ended_at > '".$now_date."' and use_flag='Y' order by ended_at asc limit 1";
        }
        else {
            $sql = "SELECT * FROM gosivod.freebook_calendar where freebook_idx=".$freebook_idx." and use_flag='Y' order by ended_at desc limit 1";
        }
        $calendar_info = $this->db->sqlRow($sql);

        if($calendar_info['idx']) {
            $sql = "SELECT lec_no FROM gosivod.freebook_good where freebook_calendar_idx =" . $calendar_info['idx'];
            $calendar_info['lec_no'] = $this->db->sqlRowOne($sql);
        }

        #10�������� �����Է°���
        $start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at'])); //�����Է½ð� ����
        $grap_timestmap = strtotime($start_date) - strtotime($now_date);

        if($now_date < $start_date) {
            #�̺�Ʈ�� ������ �������̰� �������� ���
            if(date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
                $calendar_info['quiz_question'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
            }
            else {
                $calendar_info['quiz_question'] = '�̺�Ʈ ���� �ð��� �ƴմϴ�.';
            }
        }

        if($now_date >= $calendar_info['ended_at']) {
            $calendar_info['quiz_question'] = '�̺�Ʈ�� ����Ǿ����ϴ�.';
        }

        $return_data['msg']  = '';
        $return_data['data'] = $calendar_info;
        $return_data['error'] = 0;
        return $return_data;
    }

	#����Ʈ�� ǥ�� �� ���ް� ��
	public function getRemainViewCount($param) {
		$calendar_info = $param['calendar_info'];
		$now_date = date('Y-m-d H:i:s');
		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($calendar_info)) {
			$return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}


		$now_s = 0;
		if($now_date >= $calendar_info['started_at']) {
			$now_s = strtotime($now_date) - strtotime($calendar_info['started_at']);
		}


        $cnt_give = 0;
		if($calendar_info['idx']) {
            $sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=" . $calendar_info['idx'] . " and result_state='Y'";
            $cnt_give = $this->db->sqlRowOne($sql);
        }


		$cnt_minus = $calendar_info['cnt_view']/(strtotime($calendar_info['ended_at']) - strtotime($calendar_info['started_at']));
		$cnt_deduct = (int)($now_s * $cnt_minus);

		$give_rate = $calendar_info['cnt_view'];
		if($calendar_info['cnt_real'] > 0) {
			$give_rate = ceil($calendar_info['cnt_view'] / $calendar_info['cnt_real']);
		}
		#��������
		$cnt_remain1 = $calendar_info['cnt_view'] - ($cnt_give * $give_rate);
		#���̴¼���
		$cnt_remain2 = $calendar_info['cnt_view'] - $cnt_deduct;

		if($now_s == 0) {
			$cnt_remain = $cnt_remain2;
		}
		else if($cnt_remain1 < $cnt_remain2) {
			$cnt_remain = $cnt_remain1;
		}
		else {
			$cnt_remain = $cnt_remain2;
		}

		if($cnt_remain < 0) $cnt_remain = 0;

		if(substr($now_date, 0, 10) != substr($calendar_info['started_at'], 0, 10)) {
			$cnt_remain = 0;
		}

		$return_data['msg']  = '';
		$return_data['data'] = $cnt_remain;
		$return_data['error'] = 0;
		return $return_data;
	}


    #����Ʈ�� ǥ�� �� ���� ���ް� ��
    public function getRealRemainViewCount($param) {
        $calendar_info = $param['calendar_info'];
        $now_date = date('Y-m-d H:i:s');
        #���ξ������̰� now_date�� ���� ��� ����
        if($this->isInsideIp() && !empty($param['now_date'])) {
            $now_date = $param['now_date'];
        }

        if(empty($calendar_info)) {
            $return_data['msg']  = '������ �߻��Ͽ����ϴ�.';
            $return_data['error'] = 1;
            return $return_data;
        }


        $now_s = 0;
        if($now_date >= $calendar_info['started_at']) {
         //   $now_s = strtotime($now_date) - strtotime($calendar_info['started_at']);
        }


        $sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=".$calendar_info['idx']." and result_state='Y'";
        $cnt_give = $this->db->sqlRowOne($sql);


/*        $cnt_minus = $calendar_info['cnt_view']/(strtotime($calendar_info['ended_at']) - strtotime($calendar_info['started_at']));
        $cnt_deduct = (int)($now_s * $cnt_minus);*/

        $give_rate = $calendar_info['cnt_view'];
        if($calendar_info['cnt_real'] > 0) {
            $give_rate = ceil($calendar_info['cnt_view'] / $calendar_info['cnt_real']);
        }
        #��������
        $cnt_remain1 = $calendar_info['cnt_view'] - ($cnt_give * $give_rate);
        #���̴¼���
        $cnt_remain2 = $calendar_info['cnt_view'] - $cnt_give;

        if($now_s == 0) {
            $cnt_remain = $cnt_remain2;
        }
        else if($cnt_remain1 < $cnt_remain2) {
            $cnt_remain = $cnt_remain1;
        }
        else {
            $cnt_remain = $cnt_remain2;
        }

        if($cnt_remain < 0) $cnt_remain = 0;

        if(substr($now_date, 0, 10) != substr($calendar_info['started_at'], 0, 10)) {
            $cnt_remain = 0;
        }

        $return_data['msg']  = '';
        $return_data['data'] = $cnt_remain;
        $return_data['error'] = 0;
        return $return_data;
    }
	#�����Է�
	public function inputCorrectAnser($param) {
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$quiz_answer = iconv("UTF-8", "EUC-KR", trim($param['quiz_answer']));
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է½ð����� üũ
		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�̺�Ʈ����ð��� ������ ���
		if($now_date > $freebook_info['event_ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� ����Ǿ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է½ð� ���� ���
		if($now_date < $start_date) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($start_date, 0, 10)) {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�! ����� ���� '.date('H�� i��', strtotime($start_date)).'�� �����˴ϴ� :)';
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		if($now_date >= $freebook_info['event_ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� ����Ǿ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '���� �̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		if($_SESSION['freebook_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '�̹� ������ �Է� �ϼ̽��ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($quiz_answer)) {
			$return_data['msg'] = '������ �Է����ּ���.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($quiz_answer != $calendar_info['quiz_answer']) {
			$return_data['msg'] = '������ �ƴմϴ�. ������ �ٽ� �Է� ���ּ���!  :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#������ ������ ���
		$_SESSION['freebook_'.$freebook_calendar_idx] = 1;

		$return_data['msg'] = '�����Է��� �Ϸ�Ǿ����ϴ� :)';
		$return_data['error'] = 0;
		return $return_data;
	}

	#�ѹ��� ���������� ����ޱ�
	public function takeFreebook($param) {
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y'";
		$cnt = $this->db->sqlRowOne($sql);

		if($cnt>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10) && $now_date < $start_date) {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�! ����� ���� '.date('H�� i��', strtotime($start_date)).'�� �����˴ϴ� :)';
			}
			else if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�! ���� '.date('H', strtotime($calendar_info['started_at'])).'�ÿ� ������:)';
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if($_SESSION['freebook_'.$freebook_calendar_idx] != 1) {
			$return_data['msg'] = '������ �Է����ּ���!';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 0;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "SELECT COUNT(*) FROM gosivod.freebook_member WHERE freebook_calendar_idx={$freebook_calendar_idx} AND member_no = {$member_no}";
        $free_member_chk = $this->db->sqlRowOne($sql);

		if(empty($free_member_chk)) {

            #��÷�ڰ� �����Ǵ� ������ �־� ������
            $sql = "UPDATE gosivod.freebook_success_member SET member_no=" . $member_no . " WHERE member_no<0 AND freebook_calendar_idx=" . $freebook_calendar_idx . " ORDER BY member_no desc LIMIT 1";
            $this->db->sqlExe($sql);
            $cnt_remain = $this->db->sqlAffectedRows();

            #��÷����
            if ($cnt_remain <= 0) {
                $_SESSION['freebook_result_n_' . $freebook_calendar_idx] = 1;
                $sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					" . $freebook_idx . ",
					" . $freebook_calendar_idx . ",
					" . $member_no . ",
					'N',
					NOW()
				)";
                $this->db->sqlExe($sql);

                $return_data['msg'] = '��÷����!';
                $return_data['error'] = 2;
                return $return_data;
            }

            #����
            $_SESSION['freebook_result_y_' . $freebook_calendar_idx] = 1;

            $sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					" . $freebook_idx . ",
					" . $freebook_calendar_idx . ",
					" . $member_no . ",
					'Y',
					NOW()
				)";
            $this->db->sqlExe($sql);

            $sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=" . $freebook_calendar_idx;
            $lec_no_list = $this->db->sqlRowArr($sql);

            if ($lec_no_list[0]['lec_no'] > 50000) {
                $sql = "select book_name from gosivod.book_info where book_id=" . ($lec_no_list[0]['lec_no'] - 50000);
                $lec_name = $this->db->sqlRowOne($sql);
            } else {
                if ((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                    $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                    $lec_name = $this->db->sqlRowOne($sql);
                }
            }

            /*
            foreach($lec_no_list as $item) {
                $sql = "INSERT INTO gosivod.lecture_cart SET
                        site_cart = '1',
                        mem_no = '".$member_no."',
                        lec_no = '".$item['lec_no']."',
                        reg_date = now(),
                        reg_ip = '".$_SERVER['REMOTE_ADDR']."'";
                $this->db->sqlExe($sql);
            }
            */

            #��÷�˾�â
            $return_data['msg'] = '��÷����!';
            $return_data['lec_no'] = $lec_no_list[0]['lec_no'];
            $return_data['lec_name'] = $lec_name;
            $return_data['error'] = 0;
            return $return_data;
        }else{
            $return_data['error'] = 3;
            $return_data['msg'] = '�̹� �̺�Ʈ�� ���� �ϼ̽��ϴ�.';
        }
	}

	#��ǰ���� ������ ���������� ����ޱ�
	public function takeGoodFreebook($param) {
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date("H", strtotime($calendar_info["started_at"]))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if($_SESSION['freebook_'.$freebook_calendar_idx] != 1) {
			$return_data['msg'] = '������ �Է����ּ���!';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 0;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (24,26,28,35,37)";
		$cnt1 = $this->db->sqlRowOne($sql);

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (25,27,34,36,38)";
		$cnt2 = $this->db->sqlRowOne($sql);

		if(($freebook_calendar_idx == 24 || $freebook_calendar_idx == 26 || $freebook_calendar_idx == 28 || $freebook_calendar_idx == 35 || $freebook_calendar_idx == 37) && $cnt1>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		if(($freebook_calendar_idx == 25 || $freebook_calendar_idx == 27 || $freebook_calendar_idx == 34 || $freebook_calendar_idx == 36 || $freebook_calendar_idx == 38) && $cnt2>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=".$freebook_calendar_idx." and result_state='Y'";
		$cnt_give = $this->db->sqlRowOne($sql);
		$cnt_remain = $calendar_info['cnt_real'] - $cnt_give;

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		/*
		foreach($lec_no_list as $item) {
			$sql = "INSERT INTO gosivod.lecture_cart SET
					site_cart = '1',
					mem_no = '".$member_no."',
					lec_no = '".$item['lec_no']."',
					reg_date = now(),
					reg_ip = '".$_SERVER['REMOTE_ADDR']."'";
			$this->db->sqlExe($sql);
		}
		*/

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['lec_name'] = $lec_name;
		$return_data['error'] = 0;
		return $return_data;
	}


	#���� ��ǰ���� ������ ���������� ����ޱ�
	public function takeOneGoodFreebook($param) {
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date("H", strtotime($calendar_info["started_at"]))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if($_SESSION['freebook_'.$freebook_calendar_idx] != 1) {
			$return_data['msg'] = '������ �Է����ּ���!';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 0;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (40,42,44,46,48,50,52,54,56,58,62,64,66,68) and created_at BETWEEN '".substr($now_date,0,10)." 00:00:00' and '".substr($now_date,0,10)." 23:59:59'";
		$cnt0 = $this->db->sqlRowOne($sql);

		#DB���� �� ����ó��
		if(!isset($cnt0)) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$this->setResultFail($freebook_idx, $freebook_calendar_idx, $member_no);

			$return_data['msg'] = '��÷����!!!';
			$return_data['error'] = 2;
			return $return_data;
		}

		if(($freebook_calendar_idx == 41 || $freebook_calendar_idx == 43 || $freebook_calendar_idx == 45 || $freebook_calendar_idx == 47 || $freebook_calendar_idx == 49 || $freebook_calendar_idx == 51 || $freebook_calendar_idx == 53 || $freebook_calendar_idx == 55 || $freebook_calendar_idx == 57 || $freebook_calendar_idx == 59 || $freebook_calendar_idx == 63 || $freebook_calendar_idx == 65 || $freebook_calendar_idx == 67 || $freebook_calendar_idx == 69 ) && $cnt0>0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;

			$return_data['msg'] = '��÷����!!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (40,42,44,46,48,50,52,54,56,58,62,64,66,68)";
		$cnt1 = $this->db->sqlRowOne($sql);

		#DB���� �� ����ó��
		if(!isset($cnt1)) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$this->setResultFail($freebook_idx, $freebook_calendar_idx, $member_no);

			$return_data['msg'] = '��÷����!!!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (41,43,45,47,49,51,53,55,57,59,63,65,67,69)";
		$cnt2 = $this->db->sqlRowOne($sql);

		#DB���� �� ����ó��
		if(!isset($cnt2)) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$this->setResultFail($freebook_idx, $freebook_calendar_idx, $member_no);

			$return_data['msg'] = '��÷����!!!';
			$return_data['error'] = 2;
			return $return_data;
		}

		if(($freebook_calendar_idx == 40 || $freebook_calendar_idx == 42 || $freebook_calendar_idx == 44 || $freebook_calendar_idx == 46 || $freebook_calendar_idx == 48 || $freebook_calendar_idx == 50 || $freebook_calendar_idx == 52 || $freebook_calendar_idx == 54 || $freebook_calendar_idx == 56 || $freebook_calendar_idx == 58 || $freebook_calendar_idx == 62 || $freebook_calendar_idx == 64 || $freebook_calendar_idx == 66 || $freebook_calendar_idx == 68 ) && $cnt1>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		if(($freebook_calendar_idx == 41 || $freebook_calendar_idx == 43 || $freebook_calendar_idx == 45 || $freebook_calendar_idx == 47 || $freebook_calendar_idx == 49 || $freebook_calendar_idx == 51 || $freebook_calendar_idx == 53 || $freebook_calendar_idx == 55 || $freebook_calendar_idx == 57 || $freebook_calendar_idx == 59 || $freebook_calendar_idx == 63 || $freebook_calendar_idx == 65 || $freebook_calendar_idx == 67 || $freebook_calendar_idx == 69) && $cnt2>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=".$freebook_calendar_idx." and result_state='Y'";
		$cnt_give = $this->db->sqlRowOne($sql);

		#DB���� �� ����ó��
		if(!isset($cnt_give)) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$this->setResultFail($freebook_idx, $freebook_calendar_idx, $member_no);

			$return_data['msg'] = '��÷����!!!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$cnt_remain = $calendar_info['cnt_real'] - $cnt_give;

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
			$return_data['error'] = 2;
			return $return_data;
		}

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		$benefitCls = new benefitCmp();

		if($lec_no_list[0]['lec_no']==50875){
			$benefit_arr['user_id']	= $_SESSION['zb_logged_user_id'];
			$benefit_arr['cp_no']	= "8D48FAE727E7EFA7";
			$benefit_arr['site']		= "gosi";
			$benefitCls->coupon_insert($benefit_arr);
		}else if($lec_no_list[0]['lec_no']==50876){
			$benefit_arr['user_id']	= $_SESSION['zb_logged_user_id'];
			$benefit_arr['cp_no']	= "99F83F7C82BA8FML";
			$benefit_arr['site']		= "gosi";
			$benefitCls->coupon_insert($benefit_arr);
		}

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['lec_name'] = $lec_name;
		$return_data['error'] = 0;
		return $return_data;
	}

	#Egosi�� ����ޱ�
	public function takeEgosiFreebook($param) {

		/*
		#�׽�Ʈ��
		$param['member_no'] = rand();
		$param['freebook_idx'] = 12;
		$param['freebook_calendar_idx'] = 71;
		$param['now_date'] = '2018-10-02 21:00:00';
		*/

		$lec_term = $param['lec_term'];
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date("H", strtotime($calendar_info["started_at"]))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if($_SESSION['freebook_'.$freebook_calendar_idx] != 1) {
			$return_data['msg'] = '������ �Է����ּ���!';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 8;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		/**
			���� ��ǰ�� ��÷�Ǿ� �ִ��� üũ�ϴ� ����
		**/
		$sql = "select freebook_calendar_idx from gosivod.freebook_good where freebook_idx in (".$freebook_idx_sql.") and lec_no='".$lec_no_list[0]['lec_no']."'";
		$freebook_calendar_idx_list = $this->db->sqlRowArr($sql);
		$freebook_calendar_idx_arr = array();
		foreach($freebook_calendar_idx_list as $item) {
			$freebook_calendar_idx_arr[] = $item['freebook_calendar_idx'];
		}
		$freebook_calendar_idx_qry = implode(",", $freebook_calendar_idx_arr);

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (0, ".$freebook_calendar_idx_qry.")";
		$cnt = $this->db->sqlRowOne($sql);

		if((in_array($freebook_calendar_idx, $freebook_calendar_idx_arr)) && $cnt>0) {
			$return_data['msg'] = '�̹� �ش� ���簡 ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		#��÷�ڰ� �����Ǵ� ������ �־� ������
		$sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no<0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";

		$this->db->sqlExe($sql);
		$cnt_remain = $this->db->sqlAffectedRows();

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
			$return_data['error'] = 2;
			return $return_data;
		}

		$success_sql = "select count(*) from gosivod.freebook_member where freebook_idx = ".$freebook_idx." AND member_no = ".$member_no." AND result_state = 'Y'";
		$success_cnt = $this->db->sqlRowOne($success_sql);

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		//��ǰ����
		if($lec_no_list[0]['lec_no'] < 50000) {

			$benefitCls = new benefitCmp();

			$benefit_arr = array(
				'user_id' => $_SESSION['zb_logged_user_id'],
				'lec_no' => $lec_no_list[0]['lec_no'],
				'mobile' => 'Y',
				'lec_term' => 30
			);

			$benefitCls->free_lecture_insert($benefit_arr);

		}
		else {
			# ���޹�� - ��ٱ���
			# site_cart = '1' -- ������
			# site_cart = '2' -- ����
			foreach($lec_no_list as $item) {
				$sql = "INSERT INTO gosivod.lecture_cart SET
						site_cart = '1',
						mem_no = '".$member_no."',
						lec_no = '".$item['lec_no']."',
						reg_date = now(),
						reg_ip = '".$_SERVER['REMOTE_ADDR']."'";
				$this->db->sqlExe($sql);
			}
		}

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['lec_name'] = $lec_name;
		$return_data['length'] = $success_cnt;
		$return_data['error'] = 0;
		return $return_data;
	}

	#Egosi�� ����ޱ� ��ٱ��� ����X
	public function takeEgosiFreebookCart($param) {

		/*
		#�׽�Ʈ��
		$param['member_no'] = rand();
		$param['freebook_idx'] = 12;
		$param['freebook_calendar_idx'] = 71;
		$param['now_date'] = '2018-10-02 21:00:00';
		*/


		$lec_term = $param['lec_term'];
		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$freebook_calendar_quiz_flag = $param['freebook_calendar_quiz_flag'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
			    $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date("H", strtotime($calendar_info["started_at"]))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		# ��� �Է����� �ʴ� ���� �̺�Ʈ �� ���
		if($freebook_calendar_quiz_flag != 'N'){
            #�����Է��� ���� �ʾ��� ���
            if($_SESSION['freebook_'.$freebook_calendar_idx] != 1 ) {
                $return_data['msg'] = '������ �Է����ּ���!';
                $return_data['error'] = 1;
                return $return_data;
            }
        }

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 8;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		/**
			���� ��ǰ�� ��÷�Ǿ� �ִ��� üũ�ϴ� ����
		**/
		$sql = "select freebook_calendar_idx from gosivod.freebook_good where freebook_idx in (".$freebook_idx_sql.") and lec_no='".$lec_no_list[0]['lec_no']."'";
		$freebook_calendar_idx_list = $this->db->sqlRowArr($sql);
		$freebook_calendar_idx_arr = array();
		foreach($freebook_calendar_idx_list as $item) {
			$freebook_calendar_idx_arr[] = $item['freebook_calendar_idx'];
		}
		$freebook_calendar_idx_qry = implode(",", $freebook_calendar_idx_arr);

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no."
		 and result_state='Y' and freebook_calendar_idx in (0, ".$freebook_calendar_idx_qry.")";
		$cnt = $this->db->sqlRowOne($sql);

		if((in_array($freebook_calendar_idx, $freebook_calendar_idx_arr)) && $cnt>0) {
			$return_data['msg'] = '�̹� �ش� ���簡 ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		#��÷�ڰ� �����Ǵ� ������ �־� ������
		$sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no < 0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";

		$this->db->sqlExe($sql);
		$cnt_remain = $this->db->sqlAffectedRows();

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
			$return_data['error'] = 2;
			return $return_data;
		}

		$success_sql = "select count(*) from gosivod.freebook_member where freebook_idx = ".$freebook_idx." AND member_no = ".$member_no." AND result_state = 'Y'";
		$success_cnt = $this->db->sqlRowOne($success_sql);

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		foreach($lec_no_list as $item) {
			$benefitCls = new benefitCmp();
			$benefit_arr = array(
				'user_id' => $_SESSION['zb_logged_user_id'],
				'lec_no' => $item['lec_no'],
				'site' => 'gosi',
			);
			$benefitCls->freepass_insert($benefit_arr);
		}

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['lec_name'] = $lec_name;
		$return_data['length'] = $success_cnt;
		$return_data['error'] = 0;
		return $return_data;
	}


	#Egosi�� ������� ����ޱ�
	public function takeEgosiNoQuiz($param) {

		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#�̺�Ʈ ���۽ð�
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']));

		#�̺�Ʈ �ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "���� ".date('H', strtotime($calendar_info['started_at']))."��! ������ 100�� �����ϼ���!";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		# �̹� ��÷�Ǿ����� Ȯ��(�ѹ��� ���� ������ ��� ���)
		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y'";
		$cnt = $this->db->sqlRowOne($sql);

		if($cnt>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		$sql = "select lec_no,good_type from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['error'] = 8;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��Ÿ������ �����ϼ̽��ϴ�. ���� �ٽ� ������ �ּ���!';
			$return_data['error'] = 2;
			return $return_data;
		}


		#��÷�ڰ� �����Ǵ� ������ �־� ������
		$sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no<0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";
		$this->db->sqlExe($sql);
		$cnt_remain = $this->db->sqlAffectedRows();

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��Ÿ������ �����ϼ̽��ϴ�. ���� �ٽ� ������ �ּ���!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
			$return_data['error'] = 2;
			return $return_data;
		}

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		$benefitCls = new benefitCmp();

        if ($lec_no_list[0]['lec_no'] < 50000) {

            # ���޹�� - ��������
            $benefitCls = new benefitCmp();
            $benefit_arr = array(
                'user_id' => $_SESSION['zb_logged_user_id'],
                'lec_no' => $lec_no_list[0]['lec_no'],
                'mobile' => 'Y',
                'site' => 'gosi',
                'term' => $lec_term
            );
            $benefitCls->lecture_insert($benefit_arr);

        } else {
            # ���޹�� - ��ٱ���
            # site_cart = '1' -- ������
            # site_cart = '2' -- ����
            foreach ($lec_no_list as $item) {
                $sql = "INSERT INTO gosivod.lecture_cart SET
                    site_cart = '1',
                    mem_no = '" . $member_no . "',
                    lec_no = '" . $item['lec_no'] . "',
                    lec_opt = '1',
                    reg_date = now(),
                    reg_ip = '" . $_SERVER['REMOTE_ADDR'] . "'";
                $this->db->sqlExe($sql);
            }
        }

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['error'] = 0;
		return $return_data;
	}

	#Egosi�� ����� ���� ����ޱ�
	public function takeEgosiDirect($param) {

		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$lec_term = !empty($param['lec_term']) ? $param['lec_term'] : '';
		$now_date = date('Y-m-d H:i:s');
        $quiz_answer = iconv("UTF-8", "EUC-KR", trim($param['quiz_answer']));
		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date('H', strtotime($calendar_info['started_at']))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                //$sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                //$lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if(empty($quiz_answer)) {
			$return_data['msg'] = '������ �Է����ּ���.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($quiz_answer != $calendar_info['quiz_answer']) {
			$return_data['msg'] = '������ �ƴմϴ�. ������ �ٽ� �Է� ���ּ���!  :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 8;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		/**
			���� ��ǰ�� ��÷�Ǿ� �ִ��� üũ�ϴ� ����
		**/
		$sql = "select freebook_calendar_idx from gosivod.freebook_good where freebook_idx in (".$freebook_idx_sql.") and lec_no='".$lec_no_list[0]['lec_no']."'";
		$freebook_calendar_idx_list = $this->db->sqlRowArr($sql);
		$freebook_calendar_idx_arr = array();
		foreach($freebook_calendar_idx_list as $item) {
			$freebook_calendar_idx_arr[] = $item['freebook_calendar_idx'];
		}
		$freebook_calendar_idx_qry = implode(",", $freebook_calendar_idx_arr);

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (0, ".$freebook_calendar_idx_qry.")";
		$cnt = $this->db->sqlRowOne($sql);

		if((in_array($freebook_calendar_idx, $freebook_calendar_idx_arr)) && $cnt>0) {
			$return_data['msg'] = '�̹� �ش� ���簡 ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		/*
		$sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=".$freebook_calendar_idx." and result_state='Y'";
		$cnt_give = $this->db->sqlRowOne($sql);
		$cnt_remain = $calendar_info['cnt_real'] - $cnt_give;
		*/

//        $fail_sql = "SELECT idx FROM gosivod.freebook_member WHERE freebook_idx IN (".$freebook_idx_sql.") AND member_no=".$member_no." AND result_state='N' AND freebook_calendar_idx IN (0, ".$freebook_calendar_idx_qry.")";
//        $fail_cnt = $this->db->getCount($this->db->execute($fail_sql,3));
//
//        if(in_array($freebook_calendar_idx, $freebook_calendar_idx_arr) && $fail_cnt > 0){
//            $return_data['msg'] = '��÷����!';
//            $return_data['error'] = 2;
//            return $return_data;
//        }


		$scc_chk_sql = "SELECT 
						count(*) AS cnt FROM gosivod.freebook_success_member 
					WHERE 
						member_no= '".$member_no."' 
					AND 
						freebook_calendar_idx= '".$freebook_calendar_idx."' ";
		$scc_cnt_chk = $this->db->sqlRowOne($scc_chk_sql);

		if($scc_cnt_chk['cnt'] < 1){
			#��÷�ڰ� �����Ǵ� ������ �־� ������
			$sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no<0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";
			$this->db->sqlExe($sql);
			$cnt_remain = $this->db->sqlAffectedRows();

			#��÷����
			if($cnt_remain <= 0) {
				$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;

				$fail_chk_sql = "SELECT 
                                    COUNT(*) 
                                FROM gosivod.freebook_member 
                                WHERE 
                                    member_no= '".$member_no."' 
                                AND 
                                    freebook_idx= '".$freebook_idx."'
                                AND 
                                    freebook_calendar_idx= '".$freebook_calendar_idx."'";
				$fail_cnt_chk = $this->db->sqlRowOne($fail_chk_sql);

				if($fail_cnt_chk < 1){
					$sql = "insert into gosivod.freebook_member (
							freebook_idx,
							freebook_calendar_idx,
							member_no,
							result_state,
							created_at
						) values (
							".$freebook_idx.",
							".$freebook_calendar_idx.",
							".$member_no.",
							'N',
							NOW()
						)";

					$this->db->sqlExe($sql);

					$return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
					$return_data['error'] = 2;
					return $return_data;
				}
			}
		}

        #����
        $_SESSION['freebook_result_y_' . $freebook_calendar_idx] = 1;

        $chk_sql = "SELECT 
						COUNT(*) 
                    FROM gosivod.freebook_member 
					WHERE 
						member_no= '" . $member_no . "' 
					AND 
						freebook_idx= '" . $freebook_idx . "'
					AND 
						freebook_calendar_idx= '" . $freebook_calendar_idx . "'";
        $cnt_chk = $this->db->sqlRowOne($chk_sql);

        if ($cnt_chk < 1) {
            $sql = "insert into gosivod.freebook_member (
						freebook_idx,
						freebook_calendar_idx,
						member_no,
						result_state,
						created_at
					) values (
						" . $freebook_idx . ",
						" . $freebook_calendar_idx . ",
						" . $member_no . ",
						'Y',
						NOW()
					)";
            $this->db->sqlExe($sql);
        }

        if ($lec_no_list[0]['lec_no'] < 50000) {

            # ���޹�� - ��������
            $benefitCls = new benefitCmp();
            $benefit_arr = array(
                'user_id' => $_SESSION['zb_logged_user_id'],
                'lec_no' => $lec_no_list[0]['lec_no'],
                'mobile' => 'Y',
                'site' => 'gosi',
                'term' => $lec_term
            );
            $benefitCls->lecture_insert($benefit_arr);

        } else {
            # ���޹�� - ��ٱ���
            # site_cart = '1' -- ������
            # site_cart = '2' -- ����
            foreach ($lec_no_list as $item) {
                $sql = "INSERT INTO gosivod.lecture_cart SET
                    site_cart = '1',
                    mem_no = '" . $member_no . "',
                    lec_no = '" . $item['lec_no'] . "',
                    lec_opt = '1',
                    reg_date = now(),
                    reg_ip = '" . $_SERVER['REMOTE_ADDR'] . "'";
                $this->db->sqlExe($sql);
            }
        }
        #��÷�˾�â
        $return_data['msg'] = '��÷����!';
        $return_data['lec_no'] = $lec_no_list[0]['lec_no'];
        $return_data['lec_name'] = $lec_name;
        $return_data['error'] = 0;
        return $return_data;

    }


	#��ǰ���� ������ ���������� ����ޱ�
	public function takePoliceFreebook($param) {
		/*
		#�׽�Ʈ��
		$param['member_no'] = rand();
		$param['freebook_idx'] = 12;
		$param['freebook_calendar_idx'] = 71;
		$param['now_date'] = '2018-10-02 21:00:00';
		*/

		$member_no = $param['member_no'];
		$freebook_idx = $param['freebook_idx'];
		$freebook_calendar_idx = $param['freebook_calendar_idx'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
			$return_data['msg'] = '�߸��� �����Դϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		$sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
		$calendar_info = $this->db->sqlRow($sql);

		#10�������� �����Է°���
		$start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

		#�����Է½ð� ���� ���
		if($now_date < $calendar_info['started_at']) {
			$grap_timestmap = strtotime($start_date) - strtotime($now_date);

			if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
				$return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date("H", strtotime($calendar_info["started_at"]))."��{$minute_alert}�� ������:)";
			}
			#�̺�Ʈ�� ������ ������ ���
			else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
				$return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
			}
			else {
				$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			}

			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}

		$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
		$lec_no_list = $this->db->sqlRowArr($sql);

		if($lec_no_list[0]['lec_no'] > 50000) {
			$sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
			$lec_name = $this->db->sqlRowOne($sql);
		}
		else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
		}

		#���� �̺�Ʈ�� ���� �Ǿ��� ���
		if($now_date >= $calendar_info['ended_at']) {
			$return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
			$return_data['error'] = 1;
			return $return_data;
		}

		#�����Է��� ���� �ʾ��� ���
		if($_SESSION['freebook_'.$freebook_calendar_idx] != 1) {
			$return_data['msg'] = '������ �Է����ּ���!';
			$return_data['error'] = 1;
			return $return_data;
		}

		if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
			$return_data['lec_name'] = $lec_name;
			$return_data['error'] = 8;
			return $return_data;
		}

		if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
			$return_data['msg'] = '��÷����!';
			$return_data['error'] = 2;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y'";
		$cnt1 = $this->db->sqlRowOne($sql);

		if($cnt1>0) {
			$return_data['msg'] = '�̹� ��÷�Ǿ� ���� �� �� �����ϴ�!';
			$return_data['error'] = 8;
			return $return_data;
		}

		/*
		$sql = "select count(*) from gosivod.freebook_member where freebook_calendar_idx=".$freebook_calendar_idx." and result_state='Y'";
		$cnt_give = $this->db->sqlRowOne($sql);
		$cnt_remain = $calendar_info['cnt_real'] - $cnt_give;
		*/
		if(!empty($member_no)){
			#��÷�ڰ� �����Ǵ� ������ �־� ������
			$sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no<0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";
			$this->db->sqlExe($sql);
			$cnt_remain = $this->db->sqlAffectedRows();
		}

		#��÷����
		if($cnt_remain <= 0) {
			$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
			$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
			$this->db->sqlExe($sql);

			$return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
			$return_data['error'] = 2;
			return $return_data;
		}

		#����
		$_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;

		$sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'Y',
					NOW()
				)";
		$this->db->sqlExe($sql);

		foreach($lec_no_list as $item) {
				$sql = "INSERT INTO gosivod.lecture_cart SET
						site_cart = '2',
						mem_no = '".$member_no."',
						lec_no = '".$item['lec_no']."',
						term = 90,
						mobile = 'Y',
						reg_date = now(),
						reg_ip = '".$_SERVER['REMOTE_ADDR']."'";
				$this->db->sqlExe($sql);
		}

		#��÷�˾�â
		$return_data['msg'] = '��÷����!';
		$return_data['lec_no'] = $lec_no_list[0]['lec_no'];
		$return_data['lec_name'] = $lec_name;
		$return_data['error'] = 0;
		return $return_data;
	}

	public function setResultFail($freebook_idx, $freebook_calendar_idx, $member_no) {
		$_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
		$sql = "insert into gosivod.freebook_member (
				freebook_idx,
				freebook_calendar_idx,
				member_no,
				result_state,
				created_at
			) values (
				".$freebook_idx.",
				".$freebook_calendar_idx.",
				".$member_no.",
				'N',
				NOW()
			)";
		$this->db->sqlExe($sql);
	}

	public function getResultFreebook($param) {
		$freebook_idx = $param['freebook_idx'];
		$member_no = $param['member_no'];
		$now_date = date('Y-m-d H:i:s');

		#���ξ������̰� now_date�� ���� ��� ����
		if($this->isInsideIp() && !empty($param['now_date'])) {
			$now_date = $param['now_date'];
		}

		if(empty($freebook_idx)) {
			$return_data['msg'] = '������ �߻��Ͽ����ϴ�.';
			$return_data['error'] = 1;
			return $return_data;
		}

		if(empty($member_no)) {
			$return_data['msg'] = '�α����� �ʿ��մϴ�.';
			$return_data['error'] = 9;
			return $return_data;
		}


		$sql = "select * from gosivod.freebook where idx=".$freebook_idx;
		$freebook_info = $this->db->sqlRow($sql);

		#�����Է½ð� ���� ���
		/*if($now_date < $freebook_info['event_started_at']) {
			$return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
			$return_data['error'] = 1;
			return $return_data;
		}*/

		$sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
		$freebook_default_idx = $this->db->sqlRowOne($sql);

		$sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
		$freebook_idx_list = $this->db->sqlRowArr($sql);
		$freebook_idx_arr = array();
		foreach($freebook_idx_list as $item) {
			$freebook_idx_arr[] = $item['idx'];
		}
		$freebook_idx_sql = implode(",", $freebook_idx_arr);

		$sql = "select freebook_calendar_idx from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y'";
		$freebook_member_list = $this->db->sqlRowArr($sql);
		$i=0;
		$freebook_good = array();
		foreach($freebook_member_list as $item) {
			$sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$item['freebook_calendar_idx'];
			$freebook_good[$i]['lec_no'] = $lec_no = $this->db->sqlRowOne($sql);
			if($lec_no > 50000) {
				$sql = "select book_name from gosivod.book_info where book_id=".($lec_no-50000);
				$freebook_good[$i]['lec_name'] = $this->db->sqlRowOne($sql);
			}
			else {
                if((!empty($lec_no)) && ($lec_no > 0)) {
                    $sql = "select lec_name from gosivod.lecture where no=" . $lec_no;
                    $freebook_good[$i]['lec_name'] = $this->db->sqlRowOne($sql);
                }
			}
			$i++;
		}

		if(count($freebook_good) > 0) {
			$return_data['msg'] = '��÷�� �������� �����մϴ�!';
			$return_data['goods'] = $freebook_good;
			$return_data['error'] = 0;
			return $return_data;
		}

		$sql = "select count(*) from gosivod.freebook_member where freebook_idx=".$freebook_idx." and member_no=".$member_no." and result_state='N'";
		$cnt = $this->db->sqlRowOne($sql);

		if($cnt > 0) {
			$return_data['msg'] = '�ƽ����� ��÷���� �����̽��ϴ�.';
			$return_data['error'] = 2;
			return $return_data;
		}

		#�̺�Ʈ ���� ����� ���� ���
		$return_data['msg'] = '��÷ ������ �����ϴ�.';
		$return_data['error'] = 1;
		$return_data['member_no'] = $member_no;
		return $return_data;
	}

    #Egosi�� ���� ���� Ȯ�� ��ٱ��� ���� X , �������� 0
    public function takeEgosiQuizConfirm($param) {
        $benefitCls = new benefitCmp();
        $lec_name = "";
        $member_no = $param['member_no'];
        $freebook_idx = $param['freebook_idx'];
        $freebook_calendar_idx = $param['freebook_calendar_idx'];
        $now_date = date('Y-m-d H:i:s');

        $quiz_answer = iconv("UTF-8", "EUC-KR", trim($param['quiz_answer']));
        #���ξ������̰� now_date�� ���� ��� ����
        if($this->isInsideIp() && !empty($param['now_date'])) {
            $now_date = $param['now_date'];
        }

        if(empty($freebook_idx) || empty($freebook_calendar_idx)) {
            $return_data['msg'] = '�߸��� �����Դϴ�.';
            $return_data['error'] = 1;
            return $return_data;
        }

        $sql = "select freebook_default_idx from gosivod.freebook where idx=".$freebook_idx;
        $freebook_default_idx = $this->db->sqlRowOne($sql);

        $sql = "select idx from gosivod.freebook where freebook_default_idx=".$freebook_default_idx;
        $freebook_idx_list = $this->db->sqlRowArr($sql);
        $freebook_idx_arr = array();
        foreach($freebook_idx_list as $item) {
            $freebook_idx_arr[] = $item['idx'];
        }
        $freebook_idx_sql = implode(",", $freebook_idx_arr);

        $sql = "select * from gosivod.freebook where idx=".$freebook_idx;
        $freebook_info = $this->db->sqlRow($sql);

        $sql = "select * from gosivod.freebook_calendar where idx=".$freebook_calendar_idx;
        $calendar_info = $this->db->sqlRow($sql);

        #10�������� �����Է°���
        $start_date = date('Y-m-d H:i:s', strtotime($calendar_info['started_at']) - 60*10); //�����Է½ð� ����

        #�����Է½ð� ���� ���
        if($now_date < $calendar_info['started_at']) {
            $grap_timestmap = strtotime($start_date) - strtotime($now_date);

            if(substr($now_date, 0, 10) == substr($calendar_info['started_at'], 0, 10)) {
                $minute_alert = (date('i',strtotime($calendar_info['started_at'])) > 0) ? ' '.date('i',strtotime($calendar_info['started_at'])).'��' : '';
                $return_data['msg'] = "�̺�Ʈ ����ð��� �ƴմϴ�! ���� ".date('H', strtotime($calendar_info['started_at']))."��{$minute_alert}�� ������:)";
            }
            #�̺�Ʈ�� ������ ������ ���
            else if($now_date > $freebook_info['event_started_at'] && date('w', strtotime($start_date)) == 1 && $grap_timestmap < 60*60*24*7) {
                $return_data['msg'] = '�̺�Ʈ ���� �Ⱓ�� �ƴմϴ�. �����ֿ� ������ :)';
            }
            else {
                $return_data['msg'] = '�̺�Ʈ ����ð��� �ƴմϴ�!';
            }

            $return_data['error'] = 1;
            return $return_data;
        }

        if(empty($member_no)) {
            $return_data['msg'] = '�α����� �ʿ��մϴ�.';
            $return_data['error'] = 9;
            return $return_data;
        }

        #���� �̺�Ʈ�� ���� �Ǿ��� ���
        if($now_date >= $calendar_info['ended_at']) {
            $return_data['msg'] = '�̺�Ʈ�� �����Ǿ����ϴ� :)';
            $return_data['error'] = 1;
            return $return_data;
        }

        $sql = "select lec_no from gosivod.freebook_good where freebook_calendar_idx=".$freebook_calendar_idx;
        $lec_no_list = $this->db->sqlRowArr($sql);

        if($lec_no_list[0]['lec_no'] > 50000) {
            $sql = "select book_name from gosivod.book_info where book_id=".($lec_no_list[0]['lec_no']-50000);
            $lec_name = $this->db->sqlRowOne($sql);
        }
        else {
            if((!empty($lec_no_list[0]['lec_no'])) && ($lec_no_list[0]['lec_no'] > 0)) {
                $sql = "select lec_name from gosivod.lecture where no=" . $lec_no_list[0]['lec_no'];
                $lec_name = $this->db->sqlRowOne($sql);
            }
        }

        #�����Է��� ���� �ʾ��� ���
        if(empty($quiz_answer)) {
            $return_data['msg'] = '������ �Է����ּ���.';
            $return_data['error'] = 1;
            return $return_data;
        }

        if($quiz_answer != $calendar_info['quiz_answer']) {
            $return_data['msg'] = '������ �ƴմϴ�. ������ �ٽ� �Է� ���ּ���!  :)';
            $return_data['error'] = 1;
            return $return_data;
        }

        if($_SESSION['freebook_result_y_'.$freebook_calendar_idx] == 1) {
            $return_data['msg'] = '��÷����!';
            $return_data['lec_name'] = $lec_name;
            $return_data['error'] = 8;
            return $return_data;
        }

        if($_SESSION['freebook_result_n_'.$freebook_calendar_idx] == 1) {
            $return_data['msg'] = '��÷����!';
            $return_data['error'] = 2;
            return $return_data;
        }

        /**
        ���� ��ǰ�� ��÷�Ǿ� �ִ��� üũ�ϴ� ����
         **/
        $sql = "select freebook_calendar_idx from gosivod.freebook_good where freebook_idx in (".$freebook_idx_sql.") and lec_no='".$lec_no_list[0]['lec_no']."'";
        $freebook_calendar_idx_list = $this->db->sqlRowArr($sql);
        $freebook_calendar_idx_arr = array();
        foreach($freebook_calendar_idx_list as $item) {
            $freebook_calendar_idx_arr[] = $item['freebook_calendar_idx'];
        }
        $freebook_calendar_idx_qry = implode(",", $freebook_calendar_idx_arr);

        $sql = "select count(*) from gosivod.freebook_member where freebook_idx in (".$freebook_idx_sql.") and member_no=".$member_no." and result_state='Y' and freebook_calendar_idx in (0, ".$freebook_calendar_idx_qry.")";
        $cnt = $this->db->sqlRowOne($sql);

        if((in_array($freebook_calendar_idx, $freebook_calendar_idx_arr)) && $cnt>0) {
            $return_data['msg'] = '�̹� �ش� ��ǰ�� ��÷�Ǿ� ���� �� �� �����ϴ�!';
            $return_data['error'] = 8;
            return $return_data;
        }

        $fail_sql = "SELECT idx FROM gosivod.freebook_member WHERE freebook_idx IN (".$freebook_idx_sql.") AND member_no=".$member_no." AND result_state='N' AND freebook_calendar_idx IN (0, ".$freebook_calendar_idx_qry.")";
        $fail_cnt = $this->db->getCount($this->db->execute($fail_sql,3));

        if(in_array($freebook_calendar_idx, $freebook_calendar_idx_arr) && $fail_cnt > 0){
            $return_data['msg'] = '��÷����!';
            $return_data['error'] = 2;
            return $return_data;
        }

        #��÷�ڰ� �����Ǵ� ������ �־� ������
        $sql = "UPDATE gosivod.freebook_success_member SET member_no=".$member_no." WHERE member_no<0 AND freebook_calendar_idx=".$freebook_calendar_idx." ORDER BY member_no desc LIMIT 1";
        $this->db->sqlExe($sql);
        $cnt_remain = $this->db->sqlAffectedRows();

        #��÷����
        if($cnt_remain <= 0 && $fail_cnt <= 0) {
            $_SESSION['freebook_result_n_'.$freebook_calendar_idx] = 1;
            $sql = "insert into gosivod.freebook_member (
					freebook_idx,
					freebook_calendar_idx,
					member_no,
					result_state,
					created_at
				) values (
					".$freebook_idx.",
					".$freebook_calendar_idx.",
					".$member_no.",
					'N',
					NOW()
				)";
            $this->db->sqlExe($sql);

            $return_data['msg'] = '��÷����!!!';    // $cnt_remain �� 0 �� ��� ��÷���� �ʱ�ȭ�� ���� �ʾҴ��� üũ!!
            $return_data['error'] = 2;
            return $return_data;
        }

        #����
        $_SESSION['freebook_result_y_'.$freebook_calendar_idx] = 1;
		if(!empty($member_no)){
			$sql = "insert into gosivod.freebook_member (
						freebook_idx,
						freebook_calendar_idx,
						member_no,
						result_state,
						created_at
					) values (
						".$freebook_idx.",
						".$freebook_calendar_idx.",
						".$member_no.",
						'Y',
						NOW()
					)";
			$this->db->sqlExe($sql);
		}
        $auth_sql = "SELECT count(*) FROM gosivod.auth_list WHERE user_id ='".$_SESSION['zb_logged_user_id']."' AND cupone_number ='9F2D28A784AE5E3R'";
        $auth_cnt = $this->db->sqlRowOne($auth_sql);

        //���� 1ȸ�� ����
        if($auth_cnt == 0){
            $benefit_arr['user_id']	= $_SESSION['zb_logged_user_id'];
            $benefit_arr['cp_no']	= "9F2D28A784AE5E3R";
            $benefit_arr['site']	= "gosi";
            $benefitCls->coupon_insert($benefit_arr);
        }

        #��÷�˾�â
        $return_data['msg'] = '��÷����!';
        $return_data['lec_no'] = $lec_no_list[0]['lec_no'];
        $return_data['lec_name'] = $lec_name;
        $return_data['error'] = 0;
        return $return_data;
    }
}
?>