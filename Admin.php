<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function index()
	{ 
		if ($this->Admin_model->verifyUser()) {
            $this->load->model('tourModel');

			$dt = date('Y-m-d'); 
			$week = $this->sunToSat($dt);
			$tmp_dt = $week['Sun_ymd'];


			$data = array();
  			while ( $week['Sat_ymd'] > $tmp_dt ) {
        		$data[$tmp_dt] = array();
				$query = " select count(id) as count from tours where  date_format(created_at, '%Y-%m-%d') = '".$tmp_dt."'";
				$q = $this->db->query($query);
        		if($q->num_rows() >= 1) {
        		    foreach ($q->result() as $row) {
        		        $data[$tmp_dt][0] = $row->count;
        		    }   
        		}
				
        		$query = " select ts.state, count(ts.id) as count from tour_state as ts
						left join tours as t on ts.tour_id = t.id
						where date_format(ts.created_at, '%Y-%m-%d') = '".$tmp_dt."' group by ts.state";
				$q1 = $this->db->query($query);
        		if($q1->num_rows() >= 1) {
        		    foreach ($q1->result() as $row) {
        		        $data[$tmp_dt][$row->state] = $row->count;
        		    }
        		} 
				$tmp_dt = date('Y-m-d', strtotime("+1 day", strtotime($tmp_dt)));
  			}

//log_message('debug', json_encode($data));
			

			$this->load->view('admin/header');
			$this->load->view('admin/dashboard', array('data'=>$data));
			$this->load->view('admin/footer');
		} 
		
	}

	public function sunToSat($ymd) {
	    $time = strtotime($ymd);
	    $today = date("Y-m-d", $time);
	
	    $tday = date("w", $time);
	
	    if($tday) $Sun = -1;
	    else $Sun = 0;
	
	    $last['Sun'] = strtotime("{$Sun} Sunday", $time);
	    $last['Sat'] = strtotime("0 Saturday", $time);
	
	    $last['Sun_ymd'] = date("Y-m-d", $last['Sun']);
	    $last['Sat_ymd'] = date("Y-m-d", $last['Sat']);
	
	    return $last;
	}

}
