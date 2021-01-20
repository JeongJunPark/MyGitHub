<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$now_date = date('Y-m-d H:i:s');
$zb_no = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';			// ȸ����ȣ
$zb_id = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// ȸ��ID

// 2������ ��������
$list_sql	= "SELECT * FROM teacher.reserve_config WHERE use_yn ='Y'";
$list_info	= $db->sqlRowArr($list_sql,3);

$able_cnt	 = 0;	// ������ ����
$disable_cnt = 0;	// ������ ����
$html = '';
if(!empty($list_info)){
	foreach($list_info as $list_item){
		// ����Ŭ���� ������������ �������� ���� ���
		if($list_item['class_edate'] < $now_date){
			$disable_cnt++;
			$html .= "
						<li>
							<span class='class_title'>".$list_item['class_title']."</span>
							<span class='btn btn_end'>���ึ��</span>
						</li>
					";

		}else{
			$able_cnt++;
			$html .= "
						<li>
							<span class='class_title'>".$list_item['class_title']."</span>
							<button type='button' class='btn' onclick='go_reserve(".$list_item['no'].");'>���� ��û�ϱ�</button>
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
