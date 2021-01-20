<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$method			= !empty($_POST['method']) ? $_POST['method'] : '';
$no				= !empty($_POST['no']) ? $_POST['no'] : '';											// 신청강의 PK
$sel_date		= !empty($_POST['sel_date']) ? $_POST['sel_date'] : '';								// 선택 일자
$sel_schedule	= !empty($_POST['sel_schedule']) ? $_POST['sel_schedule'] : '';						// 선택 시간표
$zb_no			= !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';				// 회원번호
$zb_id			= !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';		// 회원ID
$zb_name		= !empty($_SESSION['zb_logged_name']) ? $_SESSION['zb_logged_name'] : '';			// 회원명
$zb_tel			= !empty($_SESSION['zb_logged_handphone']) ? $_SESSION['zb_logged_handphone'] : '';		// 회원 휴대폰

$html	= '';

if((empty($method)) || (empty($sel_date)) || (empty($sel_schedule))){ exit; }

if($method == 'insert'){

	if(!empty($no) && (!empty($sel_date)) && (!empty($sel_schedule))){
		$cnt_sql	= "SELECT count(*) AS cnt FROM teacher.reserve_member WHERE user_no ='".$zb_no."' AND del_yn ='N' AND pid= '".$no."'";	 // 동일한 강좌의 신청이력이 존재할경우 신청불가
		$cnt_rs		= $db->execute($cnt_sql,3);
		$chk_cnt	= $db->getData($cnt_rs);
	}
	
	if($chk_cnt['cnt'] > 0){
		$msg = 'already';

	}else{

		#관리자단 중복신청여부 'N'으로 등록된 강의는 중복예약 불가 START#
		if(!empty($no)){
			$config_chk_sql		= "SELECT * FROM teacher.reserve_config WHERE no= ".$no."";
			$config_chk_rs		= $db->execute($config_chk_sql,3);
			$config_chk_data	= $db->getData($config_chk_rs);
		}

		if($config_chk_data['dupl_yn'] == 'N'){

			$dupl_sql	= "SELECT * FROM teacher.reserve_config WHERE dupl_yn='N'";
			$dupl_info = $db->sqlRowArr($dupl_sql,3);

            $duple_lec_no_arr = array();
			if(!empty($dupl_info)){
				foreach($dupl_info AS $item){
					$duple_lec_no_arr[] = $item['no'];
				}
			}

			$duple_lec_no_arr = array_unique($duple_lec_no_arr);
			$duple_lec_no_chk = implode(',',$duple_lec_no_arr);

			$member_chk_cnt_sql = "SELECT count(*) AS cnt FROM teacher.reserve_member WHERE user_no ='".$zb_no."' AND pid IN (".$duple_lec_no_chk.") AND del_yn ='N'";
			$mem_chk_cnt_rs		= $db->execute($member_chk_cnt_sql,3);
			$mem_chk_cnt_data	= $db->getData($mem_chk_cnt_rs);

			if($mem_chk_cnt_data['cnt'] > 0){
				$msg = 'duplication';

				echo json_encode(array('msg' => iconv("EUC-KR", "UTF-8", $msg)));
				exit;
			}
		}
		#관리자단 중복신청여부 'N'으로 등록된 강의는 중복예약 불가 END#
		if(!empty($no)){
			$config_chk_sql		= "SELECT * FROM teacher.reserve_config WHERE no= ".$no."";
			$config_chk_rs		= $db->execute($config_chk_sql,3);
			$config_data		= $db->getData($config_chk_rs);
		}
		
		if((!empty($no)) && (!empty($sel_schedule)) && (!empty($sel_date))){
			$user_data_sql	= "SELECT * FROM teacher.reserve_member WHERE reserve_date= '".$sel_date."' AND reserve_time= '".$sel_schedule."' AND del_yn ='N' AND pid= '".$no."'";
			$chk_rs			= $db->execute($user_data_sql,3);
			$data_info		= $db->getData($chk_rs);
		}
		
		if(!empty($data_info)){
			$time_cnt_sql	= "SELECT count(*) AS cnt FROM teacher.reserve_member WHERE pid=".$data_info['pid']." AND reserve_date='".$data_info['reserve_date']."' AND reserve_time= '".$data_info['reserve_time']."'";
			$time_cnt_rs	= $db->execute($time_cnt_sql,3);
			$time_cnt_info	= $db->getData($time_cnt_rs);
		}

		if($time_cnt_info['cnt'] <= $config_data['max_cnt']){
			$insert_sql = "INSERT INTO 
								teacher.reserve_member
							SET
								pid				= '".$no."'
								,user_no		= '".$zb_no."'
								,reserve_date	= '".$sel_date."'
								,reserve_time	= '".$sel_schedule."'
								,reg_date		= NOW()
								,del_yn			= 'N'
							";

		    $rs = $db->execute($insert_sql);

			if($rs){
				$msg = 'success';
			}

		}else{
			$msg = 'no_data';
		}
	}
}

$arr = array(
	'msg'		=> iconv("EUC-KR", "UTF-8", $msg),
);
echo json_encode($arr);
exit;
?>
