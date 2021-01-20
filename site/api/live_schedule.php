<?php
/**
 * @brief   라이브방송 api
 * @param   $_POST
 */
include_once $_SERVER['DOCUMENT_ROOT']."/site/common/dbClass.php";
include_once $_SERVER['DOCUMENT_ROOT']."/site/common/commonClass.php";
$db = new DBCmp();

$data_arr = array();
switch($act){
    case 'name' :
        $lec_num_arr = implode(",",$_POST['data']['code']);
        $description = "강의코드명";

		if(!empty($lec_num_arr)){
			$selectSql = "
						  SELECT 
							no,lec_name 
						  FROM 
							gosivod.lecture 
						  WHERE 
							no IN (".$lec_num_arr.")";

			$select = $db->execute($selectSql);

			while($row = $db->getDataAssoc($select)) {
				$data_arr[] = $row;
			}
		}

        if(!empty($data_arr)){
            $result = 200;
            $message = 'Success';
        } else {
            $result = 202;
            $message = 'Fail';
        }
        itemIconv('euc-kr','utf-8',$data_arr);

        echo json_encode(array ("code"=>$result, 'message'=>$message,'description'=> iconv("euc-kr","utf-8",$description), 'data'=>$data_arr));
        exit;
        break;
}

function itemIconv($in, $out, &$item) {
    if(is_array($item)) {
        foreach($item AS $_key => &$_val) {
            itemIconv($in, $out, $_val);
        }
    } else if(is_numeric($item) || is_bool($item)) {
        $item = $item;
    } else {
        $item = iconv($in, $out,$item);
    }
}