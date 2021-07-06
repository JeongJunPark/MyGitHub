<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TourStateModel extends CI_Model {

    public $tablename="tour_state";
    public $pk_fieldname="id";

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->tablename="tour_state";
        $this->pk_fieldname="id";
    }

	/*  
     */
    public function totalcount($search_query = ''){
        $ret = 0;
        $query = " select count(id) as count from ".$this->tablename." where 1 " . $search_query;
        $q = $this->db->query($query);
        if($q->num_rows() == 1) {
            foreach ($q->result() as $row) {
                $ret= $row->count;
            }   
        }   
        return $ret;
    } 

	public function get_list($start, $order, $per_page, $search_query = '' ){
		$rows = null;    

		$limit_sql = " limit ".$start.", ".$per_page;

		$data = array();
		$query ="select * from ".$this->tablename." where 1 " .$search_query. " order by ".$order. $limit_sql ;

		$q = $this->db->query($query);

        return $q->result_array();
	}

	public function save($data) {
	    $ret=false;
	
	    $this->db->set($data);
	    $this->db->set("created_at", "now()", false);
	    $this->db->insert($this->tablename);
	    if ($this->db->affected_rows() > 0) {
	        $ret=true;
	    }
	
	    return $ret;
	
	}

	

	public function update($id, $data){
	    $ret=false;
	
	    $this->db->set($data);
	    $this->db->where($this->pk_fieldname, $id);
	    $proc1= $this->db->update($this->tablename);
	    $affect_count1=$this->db->affected_rows($this->db);
	
	    if($proc1 > 0 ){
	        $ret=true;
	    }
	
	    return $ret;
	}
 
    
}
