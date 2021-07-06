<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NoticeApplyModel extends CI_Model {

    public $tablename="notice_apply";
    public $pk_fieldname="id";

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->tablename="notice_apply";
        $this->pk_fieldname="id";
    }

	/*  
     */
    public function totalcount($search_query = ''){
        $ret = 0;
        $query = " select count(na.id) as count 
            from ".$this->tablename." as na 
            inner join houses as h on na.house_id=h.id
            where 1 and na.deleted=0 " .$search_query ;
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
		$query ="select na.*, 
                        h.h_name, 
                        JSON_UNQUOTE(json_extract(h.h_name, '$.kr')) as h_name_kr
            from ".$this->tablename." as na 
            inner join houses as h on na.house_id=h.id
            where 1 and na.deleted=0 " .$search_query. " order by ".$order. $limit_sql ;

		$q = $this->db->query($query);

        return $q->result_array();
	}
	
	/**
     *
     * @param type $data
     * @param type $one_flag 한레코드만 가져오고 싶을때 true
     * @return type
     */
    public function find($where, $one_flag){
        $ret=null;

        if($one_flag)  $where .= " limit 1 ";


        $query ="select * from ".$this->tablename." where ".$where  ;

        $q = $this->db->query($query);

        if($q->num_rows() >= 0) {
            if($one_flag){
                foreach( $q->result() as $row ){
                   $ret= $row ;
               }
            }else{
                $ret=$q->result();
            }
        }

        return $ret;
    }

	public function save($data) {
	    $ret=false;
	
	    $this->db->set($data);
	    $this->db->set("created_at", "now()", false);
	    $this->db->set("updated_at", "now()", false);
	    $this->db->insert($this->tablename);
	    if ($this->db->affected_rows() > 0) {
	        $ret=true;
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
