<?
include $_SERVER['DOCUMENT_ROOT']."/_config/set_session_start.php";		//���ǻ��� ��������;
include $_SERVER['DOCUMENT_ROOT']."/site/common/common.php";
$db = new DBCmp();

$zb_name = !empty($_SESSION['zb_logged_name']) ? $_SESSION['zb_logged_name'] : '';			// ȸ����
$zb_id	 = !empty($_SESSION['zb_logged_user_id']) ? $_SESSION['zb_logged_user_id'] : '';	// ȸ��ID
$zb_no	 = !empty($_SESSION['zb_logged_no']) ? $_SESSION['zb_logged_no'] : '';				// ȸ����ȣ
$no		 = !empty($_GET['rno']) ? $_GET['rno'] : '';										// �����ȣ(key)

if((empty($zb_no)) || (empty($zb_id)) || (empty($zb_name)) || (empty($no))){
	echo "<script>alert('���������� �����Դϴ�.'); window.close();</script>";
    exit;
}

if((!empty($zb_no)) && (!empty($no))){
	$user_chk_sql	= "SELECT 
							rm.reserve_date,rm.reserve_time,rc.class_title
						FROM 
							teacher.reserve_member AS rm, teacher.reserve_config AS rc 
						WHERE 
							rm.pid = rc.no
						AND
							rm.user_no ='".$zb_no."' 
						AND 
							rm.del_yn ='N'
						AND
							rm.pid='".$no."' 
						ORDER BY rm.no DESC LIMIT 1";
	$user_chk_rs	= $db->execute($user_chk_sql,3);
	$user_rs_data	= $db->getDataAssoc($user_chk_rs);
}
?>
<script src='/js/jquery-1.12.0.min.js'></script>
<script text="javascript">
	// ���� ��û�ϱ� Ŭ����
	function go_list(div){
		var rno = <?=$no?>;
		if(div == 1){
			location.href='reserve_pop02.html?rno='+rno;
		}else{
			location.href='reserve_pop03.html?rno='+rno;
		}
	}
</script>
<!-- 2�� ���� ���� ��û : ���� ��û �˾�_���� ȭ�� -->
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=1050">
    <meta name="robots" content="ALL" />
    <meta name="google-site-verification" content="TtBy8SIAbYqjK8BOg-ggw86aqc7CfzMtbEQXIZHQPic" />
    <meta name="naver-site-verification" content="94cdca090dc4ac3ab96b35828cbf28d046fdd607"/>

    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/site/contents/study/css/popup/popup.css" type="text/css">
</head>
<body scroll="auto">

<div class="reserve_wrap">
    <div class="reserve_content">
        <h1 class="reserve_top_hd"><?=$user_rs_data['class_title']?></h1>
        <div class="reserve_my_area">
            <ul class="reserve_button">
                <li><button class="button" onclick="go_list(1);">���� �Ͻ� ����</button></li>
                <li><button class="button on" onclick="go_list(2);">���� ���� Ȯ��</button></li>
            </ul>
            <div class="class_box">
                <div class="class_top">
                    <h2><em>&bull;</em>���� Ȯ��</h2>
                </div>
                <div class="select_list">
					<?if(!empty($user_rs_data)){?>
						<ul class="class_list check">
							<li>
								 <label class="reserve_label">������ ��(ID)</label><strong><?=$zb_name?> <?='('.$zb_id.')'?></strong>
							</li>
							<li>
								<label class="reserve_label">���� ���¸�</label><strong><?=$user_rs_data['class_title']?></strong>
							</li>
							<li>
								<label class="reserve_label">������</label><strong><?=$user_rs_data['reserve_date']?></strong>
							</li>
							<li>
								<label class="reserve_label">���� �ð�</label><strong><?=$user_rs_data['reserve_time']?></strong>
							</li>
						</ul>
					<?}else{?>
	                    <strong class="list_default">�����Ͻ� ������ �����ϴ�.</strong>
					<?}?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

