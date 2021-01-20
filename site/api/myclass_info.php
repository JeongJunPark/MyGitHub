<style>
    .lecture_room .bxslider-default {width:872px;}
</style>
<?php

/***
 * @Params
 * ä�ξ��̵� : ChannelPoolID
 * ä�θ� : ChannelName
 * ��۾��̵� : VideoChatIdx
 * ���Ÿ��Ʋ : BroadTitle
 * ������ : TeacherName
 * �����پ��̵� : ScheduleIdx
 * ��۽��۽ð� : BeginDatetime
 * �������ð� : EndDatetime
 * ���� (1 : ����, 3: �ް�, 5 : ����) : ScheduleStatus
 * ���´��ð�(��) : OpenWaitMIN
 * ä�� ���� ���� (1 :���, 0 : ����) : IsUse
 */

$site_num = 3040;	//������

//���̺갭�� API
$url = 'http://livetv.hackers.com/api/schedule/'.$site_num;
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$data = json_decode(trim(curl_exec($curl)));
curl_close($curl);
$channel_info = $data->data->ChannelInfo;
$yoil = array("��","��","ȭ","��","��","��","��");

//���� ���� count
$schedule_arr = array();
$schedule_val = array();
$lec_arr = array();
$list = array();
foreach($channel_info AS $key => $d) {
    if(date('Y-m-d')==date("Y-m-d", strtotime($d->BeginDatetime)) && ($d->IsUse == 1) ) {
        if(!empty($d->PurchaseInfo)){
			if($d->PurchaseInfo) {
				foreach($d->PurchaseInfo AS $lec_k => $lec_dd) {
					if($lec_no == $lec_dd->LectureCode){
						$list[] = $d;
					}
				}
			}
        }
    }
}

$sdi_arr = array();
foreach($list AS $key => $data) {
    if($data->PurchaseInfo) {
        array_push($sdi_arr,$data->ScheduleIdx);
        //���� ���ſ���
        foreach($data->PurchaseInfo AS $k => $dd) {
            $lec_arr[] = $dd->LectureCode;
        }
        $lec_in = implode(',',$lec_arr);
        $sql = "SELECT count(*) AS cnt FROM gosivod.lecture_mem WHERE mem_user_id = '{$_SESSION['user_id']}' AND lec_state = 2 AND lec_no IN ({$lec_in})";
        $lec_cnt =$db->getDataAssoc($db->execute($sql));
        if($lec_cnt['cnt']==0) {
            unset($list[$key]);
        }
    }
}
here_setcookie("LiveUser",json_encode($sdi_arr), 0);

//��Ű ����
function here_setcookie($name, $value, $expire, $path='/') {
    if (headers_sent()) {
        $cookie = $name.'='.urlencode($value).';';
        if ($expire) $cookie .= ' expires='.gmdate('D, d M Y H:i:s', $expire).' GMT';
        echo '<script language="javascript">document.cookie="'.$cookie.';path=/;domain=hackers.com;";</script>';
    } else {
        setcookie($name, $value, $expire, $path);
    }

}

//���̺갭�� ���
if((!empty($list)) && in_array($lec_no,$lec_arr)) { ?>
	<? foreach($list AS $key => $d) { ?>
		<?
		$url = "javascript:open_popup('http://livetv.hackers.com/".iconv('utf-8','euc-kr',$d->ChannelPoolID)."')";

		switch($d->ScheduleStatus) {
			case 1 :
				$images = '<img src="/img/mypage/pty_live_200826_2.jpg">';
				//��� �ð�
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime("-".$d->OpenWaitMIN." minutes",strtotime($d->BeginDatetime)))) {
					$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
					$url = "javascript:alert('��� ���� �� �Դϴ�.');";
				}
				break;
			case 3 :
				$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
				$url = "javascript:alert('���� ������".iconv('utf-8','euc-kr',$d->BroadTitle)." ������ �ް��Դϴ�.');";
				break;
			case 5 :
				$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
				$url = "javascript:alert('��۽ð��� �ƴմϴ�.');";
				break;
		}
		?>
		<a href="<?=$url?>" class="mgb60 pty_live">
			<?=$images?>
		</a>
	<? } ?>

    <script type="text/javascript">
        function open_popup(url){
            var option = "left=100, top=100, width=2400, height=1000, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, status=no";
            window.open(url,'_blank',option);
        }
    </script>
<?
}