<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tours extends MY_Controller {

	public function __construct() { 
		parent::__construct(); 
	}

	public function index()
	{ 
		if ($this->Admin_model->verifyUser()) {
            $this->load->model('tourModel');
            //header('Content-Type: application/json; charset=utf-8'); 

			$this->load->model('HouseModel');
			$data['HouseData'] = $this->HouseModel->get_list(0, 'id desc', 10000, 1);

		    $this->load->view('admin/header');
		    $this->load->view('admin/tours/list',$data);
		    $this->load->view('admin/footer');
        }
	}

    public function export() {

		$this->load->model('TourModel');
		//기간선택 추가하자
        $data = $this->TourModel->get_list(0, 'id desc', 10000);

		$spreadsheet = new Spreadsheet(); // instantiate Spreadsheet

        $sheet = $spreadsheet->getActiveSheet();

        // manually set table data value
        $sheet->setCellValue('A1', 'id'); 
        $sheet->setCellValue('B1', '이름'); 
        $sheet->setCellValue('C1', '지점'); 
        $sheet->setCellValue('D1', '상태'); 
        $sheet->setCellValue('E1', '연락처'); 
        $sheet->setCellValue('F1', '이메일'); 
        $sheet->setCellValue('G1', '희망입주'); 
        $sheet->setCellValue('H1', '투어날짜'); 
        $sheet->setCellValue('I1', '투어신청일'); 

		foreach($data as $key => $val){
			$str_num = strval($key+2);
			$sheet->setCellValue('A' . $str_num, $val['id']);
			$sheet->setCellValue('B' . $str_num, $val['name']);
			$sheet->setCellValue('C' . $str_num, $val['h_name_kr']);
			$sheet->setCellValue('D' . $str_num, $val['state']);
			$sheet->setCellValue('E' . $str_num, $val['phone']);
			$sheet->setCellValue('F' . $str_num, $val['email']);
			$sheet->setCellValue('G' . $str_num, $val['hope_movein']);
			$sheet->setCellValue('H' . $str_num, $val['tour_date']);
			$sheet->setCellValue('I' . $str_num, $val['created_at']);
		}
        
        $writer = new Xlsx($spreadsheet); // instantiate Xlsx
 
        $filename = '셀립투어신청리스트-'.date('Ymd'); // set filename for excel file to be exported
 
        header('Content-Type: application/vnd.ms-excel'); // generate excel file
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');	// download file 
    }

	public function import(){

		$this->load->model('TourModel');

	    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    
		try {
	    	if(isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
	    	    $arr_file = explode('.', $_FILES['file']['name']);
	    	    $extension = end($arr_file);
	    	    if('csv' == $extension){
	    	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
	    	    }elseif('xls' == $extension){
	    	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
	    	    }else {
	    	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	    	    }
	    	    $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
	    	    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
				//log_message('debug', json_encode($sheetData));
				$cnt = 0;
	        	if (!empty($sheetData)) {

        	    	$this->db->trans_begin();
	        	    for ($i=4; $i<count($sheetData); $i++) {
						if(empty($sheetData[$i]['B'])) continue;

	        	        $data['house_id']= 4;
	        	        $data['phone']= $sheetData[$i]['B'];
	        	        $data['state']= empty($sheetData[$i]['K']) ? 0 : $sheetData[$i]['K'];
	        	        $data['created_at']= $sheetData[$i]['D'];

			    		$ret = $this->TourModel->save($data);
						if($ret) {
							$cnt++;
						}else {
                			throw new Exception('저장 실패. phone : '.$data['phone'], -2);
						}
	        	    }

        	    	$this->db->trans_commit();
	        	}else {
                	throw new Exception('저장 할 데이터가 없습니다.', -1);
				}

            	$ret_data = array('code'=>true, 'msg'=>'저장 완료('.$cnt.'건)');
	        }else {
            	throw new Exception('저장 할 데이터가 없습니다.', -1);
			}
	        
	    }catch(Exception $e){
            //error_log(print_r($e, true));
            $this->db->trans_rollback();
            //$this->output->set_status_header(500);
        	$ret_data = array('code'=>$e->getCode(), 'msg'=> $e->getMessage());
        }

		$this->output->set_status_header(200);
        echo json_encode($ret_data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	}

}
