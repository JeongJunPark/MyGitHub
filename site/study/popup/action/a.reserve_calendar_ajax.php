<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$no		= !empty($_POST['no']) ? $_POST['no'] : '';
$zb_no  = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';			// ȸ����ȣ
$zb_id  = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// ȸ��ID
$html	= '';

$sel_current_date = !empty($_POST['current_date']) ? $_POST['current_date'] : '';

$now_date = date("Y-m-d");
$now_day = date('d',strtotime($now_date));

if(empty($sel_current_date)){
	$current_date = $now_date;			// ��������
}else if(!empty($sel_current_date)){
	$current_date = $sel_current_date;	// �� ���ý�(������/���Ŀ�)
}
$current_date_arr = explode("-",$current_date);
$current_year	= $current_date_arr[0]; //�⵵
$current_month	= $current_date_arr[1]; //��
$current_day	= $current_date_arr[2]; //��

$start_month_num = date("t",mktime(0,0,0,$current_month,$current_day,$current_year));	//�ش� ���� �ϼ�
$start_month_day = date("N",mktime(0,0,0,$current_month,1,$current_year));				//�ش� ���� 1�� ����
$weekend_day_line = $start_month_day%7;
$weekend_line = ($start_month_num+$weekend_day_line)/7; $weekend_line = ceil($weekend_line); $weekend_line=$weekend_line-1; // �ش� ���� �� ��
$next_month = date("Y-m-d",mktime(0,0,0,$current_month+1,$current_day,$current_year));	 // ������
$pre_month = date("Y-m-d",mktime(0,0,0,$current_month-1,$current_day,$current_year));	 // ������

$cal_sql="SELECT * FROM teacher.reserve_config WHERE no=".$no." AND use_yn='Y'";
$cal_rs		= $db->execute($cal_sql,3);
$cal_info	= $db->getDataAssoc($cal_rs);

$sdate = substr($cal_info['reserve_sdate'],8,2);
$edate = substr($cal_info['reserve_edate'],8,2);

if(!empty($cal_info)){

$html .="
	<div class='month'>
		<span class='mmonth_prev' onclick='reserveCalendar_list(\"$pre_month\");'>��</span>
		<span class='month_txt'>{$current_year}.{$current_month}</span>
		<span class='mmonth_next' onclick='reserveCalendar_list(\"$next_month\");'>��</span>
	</div>
	<table class='calendar_table'>
		<thead>
		<tr>
			<th class='sun'>��</th>
			<th>��</th>
			<th>ȭ</th>
			<th>��</th>
			<th>��</th>
			<th>��</th>
			<th class='sat'>��</th>
		</tr>
		</thead>
		<tbody>";


for($i=0; $i<=$weekend_line; $i++){

	$html .="<tr>";
	for($j=1; $j<=7; $j++){
		$day_all = 7*$i+$j;
		$day = $day_all-$weekend_day_line;

		$date = date('Y-m',strtotime($current_date))."-".sprintf('%02d',$day);

		if($now_date > $current_date || (($now_date == $current_date) && $now_day > $day)) $date_class = ""; // ���� �� �������� ���� ���ڴ� ��û�Ұ� ó��
		else if($date >= $cal_info['reserve_sdate'] && $date <= $cal_info['reserve_edate']) $date_class = "on_dt";
		else $date_class = "";

		$html .="<td>";
		if($day <= 0 || $day > $start_month_num){
			$html .= '';
		}else{
			if($date_class == ''){
				$html .= "<span class='$date_class'>";
			}else{
				$html .= "<span onclick='reserveTime_list(\"$no\",\"$date\");' id='p_sel_date' class='$date_class'>";
			}
			$html .= $day;
		}

		$html .= "</span>";
	}

	$html .="</tr>";
}


$html .="</tbody>
		</table>";
}

$arr = array(
	'html'			=> iconv("EUC-KR", "UTF-8", $html)
);
echo json_encode($arr);
exit;
?>
