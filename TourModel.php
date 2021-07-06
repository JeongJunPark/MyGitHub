<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TourModel extends CI_Model {

    public $tablename="tours";
    public $pk_fieldname="id";

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->tablename="tours";
        $this->pk_fieldname="id";
    }

	/*  
     */
    public function totalcount($search_query = ''){
        $ret = 0;
        $query = " select count(tours.id) as count 
            from ".$this->tablename." 
            inner join houses as h on tours.house_id=h.id
            where 1 and tours.deleted=0  " . $search_query;
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
		$query ="select tours.*,
                        h.h_name, 
                        JSON_UNQUOTE(json_extract(h.h_name, '$.kr')) as h_name_kr
            from ".$this->tablename." 
            inner join houses as h on tours.house_id=h.id
            where 1 and tours.deleted=0 " .$search_query. " order by ".$order. $limit_sql ;
//log_message('debug', $query);

		$q = $this->db->query($query);
        return $q->result_array();
	}

	public function save($data) {
	    $ret=false;
	
	    $this->db->set($data);
        if(empty($data['created_at'])){
	        $this->db->set("created_at", "now()", false);
        }
	    $this->db->set("updated_at", "now()", false);
	    $this->db->insert($this->tablename);
	    if ($this->db->affected_rows() > 0) {
				$ret=$this->db->insert_id();
	    }
	
	    return $ret;
	
	}

	

	public function update($id, $data){
	    $ret=false;
	
	    $this->db->set($data);
	    $this->db->set("updated_at", "now()", false);
	    $this->db->where($this->pk_fieldname, $id);
	    $proc1= $this->db->update($this->tablename);
	    $affect_count1=$this->db->affected_rows($this->db);
	
	    if($proc1 > 0 ){
	        $ret=true;
	    }
	
	    return $ret;
	}

    public function remove($id) {
		if($id){
		    $this->db->reset_query();
		    $ret = $this->db->delete($this->tablename, array("id"=>$id));
		}else {
		    $ret = false;
		}
		return $ret;

    }
 
    
}
