<?php
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$zb_no = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';			// ȸ����ȣ
$zb_id = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// ȸ��ID
$result = false;

if((empty($zb_no)) || (empty($zb_id))){
    $result = false;
    $login_yn = false;
    $msg = '�α��� ���°� �ƴմϴ�.';
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
