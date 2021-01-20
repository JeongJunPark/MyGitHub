<?php
	include_once $_SERVER['DOCUMENT_ROOT'].'/site/common/dbClass.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/site/common/commonClass.php';
	include_once $_SERVER['DOCUMENT_ROOT']."/site/common/commonFunction.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/site/common/freebookClass.php";



	$db = new DBCmp();
	$freebookCls = new FreebookClass($db);

	$method = trim($_POST['m']);
	$input = $_POST;
	$input['member_no'] = $_SESSION['zb_logged_no'];
	$return_data = $freebookCls->{$method}($input);
	responseJson($return_data);
?>