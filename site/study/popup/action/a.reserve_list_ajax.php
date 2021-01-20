<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$now_date = date('Y-m-d H:i:s');
$zb_no = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';			// 회원번호
$zb_id = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// 회원ID

// 2차대비반 개설강좌
$list_sql	= "SELECT * FROM teacher.reserve_config WHERE use_yn ='Y'";
$list_info	= $db->sqlRowArr($list_sql,3);

$able_cnt	 = 0;	// 진행중 예약
$disable_cnt = 0;	// 마감된 예약
$html = '';
if(!empty($list_info)){
	foreach($list_info as $list_item){
		// 예약클래스 오픈종료일이 현재일을 지난 경우
		if($list_item['class_edate'] < $now_date){
			$disable_cnt++;
			$html .= "
						<li>
							<span class='class_title'>".$list_item['class_title']."</span>
							<span class='btn btn_end'>예약마감</span>
						</li>
					";

		}else{
			$able_cnt++;
			$html .= "
						<li>
							<span class='class_title'>".$list_item['class_title']."</span>
							<button type='button' class='btn' onclick='go_reserve(".$list_item['no'].");'>예약 신청하기</button>
						</li>
					";
		}
	}
}

$arr = array(
	'able_cnt'		=> $able_cnt,
	'disable_cnt'	=> $disable_cnt,
	'html'			=> iconv("EUC-KR", "UTF-8", $html)
);
echo json_encode($arr);
exit;
?>
