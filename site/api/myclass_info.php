<style>
    .lecture_room .bxslider-default {width:872px;}
</style>
<?php

/***
 * @Params
 * 채널아이디 : ChannelPoolID
 * 채널명 : ChannelName
 * 방송아이디 : VideoChatIdx
 * 방송타이틀 : BroadTitle
 * 강연자 : TeacherName
 * 스케줄아이디 : ScheduleIdx
 * 방송시작시간 : BeginDatetime
 * 방송종료시간 : EndDatetime
 * 상태 (1 : 정상, 3: 휴강, 5 : 종료) : ScheduleStatus
 * 오픈대기시간(분) : OpenWaitMIN
 * 채널 삭제 유무 (1 :사용, 0 : 삭제) : IsUse
 */

$site_num = 3040;	//공무원

//라이브강의 API
$url = 'http://livetv.hackers.com/api/schedule/'.$site_num;
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$data = json_decode(trim(curl_exec($curl)));
curl_close($curl);
$channel_info = $data->data->ChannelInfo;
$yoil = array("일","월","화","수","목","금","토");

//진행 강의 count
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
        //강의 구매여부
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

//쿠키 생성
function here_setcookie($name, $value, $expire, $path='/') {
    if (headers_sent()) {
        $cookie = $name.'='.urlencode($value).';';
        if ($expire) $cookie .= ' expires='.gmdate('D, d M Y H:i:s', $expire).' GMT';
        echo '<script language="javascript">document.cookie="'.$cookie.';path=/;domain=hackers.com;";</script>';
    } else {
        setcookie($name, $value, $expire, $path);
    }

}

//라이브강의 출력
if((!empty($list)) && in_array($lec_no,$lec_arr)) { ?>
	<? foreach($list AS $key => $d) { ?>
		<?
		$url = "javascript:open_popup('http://livetv.hackers.com/".iconv('utf-8','euc-kr',$d->ChannelPoolID)."')";

		switch($d->ScheduleStatus) {
			case 1 :
				$images = '<img src="/img/mypage/pty_live_200826_2.jpg">';
				//방송 시간
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s", strtotime("-".$d->OpenWaitMIN." minutes",strtotime($d->BeginDatetime)))) {
					$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
					$url = "javascript:alert('방송 시작 전 입니다.');";
				}
				break;
			case 3 :
				$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
				$url = "javascript:alert('금일 예정된".iconv('utf-8','euc-kr',$d->BroadTitle)." 수업은 휴강입니다.');";
				break;
			case 5 :
				$images = '<img src="/img/mypage/pty_live_200826_1.jpg">';
				$url = "javascript:alert('방송시간이 아닙니다.');";
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