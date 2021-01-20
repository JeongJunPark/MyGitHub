<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$zb_no = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';			// 회원번호
$zb_id = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// 회원ID
$result = false;

if((empty($zb_no)) || (empty($zb_id))){
    $result = false;
    $login_yn = false;
    $msg = '로그인 상태가 아닙니다.';
}else{
	$login_yn = true;
    $result = true;
}

$arr = array(
    "result" => $result,
    "msg" => iconv("EUC-KR","UTF-8",$msg),
    "login_yn" => $login_yn
);

$return = json_encode($arr);
echo $return;
exit;
