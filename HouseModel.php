<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HouseModel extends CI_Model {
	
	public $tablename="houses";
    public $pk_fieldname="id";

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

	/*  
     */
    public function totalcount(){
        $ret = 0;
        $query = " select count(id) as count from houses ";
        $q = $this->db->query($query);
        if($q->num_rows() == 1) {
            foreach ($q->result() as $row) {
                $ret= $row->count;
            }   
        }   
		
		//echo $this->db->last_query();
		//exit;
        return $ret;
    } 

	/**
     *
     * @param type $page
     * @param type $order
     * @param type $per_page
     * @return array
     */
	public function get_list($page, $order, $per_page, $status){
		$rows = null;    

		$limit_sql=" limit ".$per_page;
		if($page > 1 ) $limit_sql .= " limit ".(($page-1)*$per_page).", ".$per_page ;
		if($status == 1) $status_sql  = " WHERE status = '1'";

		$data = array();
		$query ="SELECT *
					, (SELECT name FROM admin WHERE id = house.admin_id) AS admin_name
					, JSON_UNQUOTE(json_extract(h_name, '$.kr')) as name_kr
					, JSON_UNQUOTE(json_extract(h_name, '$.en')) as name_en 
				FROM houses AS house" 
				.$status_sql;
		

		$q = $this->db->query($query);
		//echo $this->db->last_query();
		//exit;
        return $q->result_array();
	}

 	/**
     *
     * @param type $data
     * @param type $one_flag 한레코드만 가져오고 싶을때 true
     * @return array
     */
    public function find($where, $one_flag){
        $ret=null;

        if($one_flag)  $where .= " limit 1 ";


        $query ="select *, JSON_UNQUOTE(json_extract(h_name, '$.kr')) as name_kr, JSON_UNQUOTE(json_extract(h_name, '$.en')) as name_en from houses where ".$where  ;

        $q = $this->db->query($query);

        if($q->num_rows() >= 0) {
            if($one_flag){
                foreach( $q->result_array() as $row ){
                   $ret= $row ;
               }
            }else{
                $ret=$q->result_array();
            }
        }

        return $ret;
    }
	
	/**
		저장
	*/
	public function save($data) {
	    $ret=false;
		
	    $this->db->set($data);
	    $this->db->insert($this->tablename);
	    if ($this->db->affected_rows() > 0) {
				$ret=$this->db->insert_id();
	    }
	
	    return $ret;
	}

	/**
		수정
	*/
	public function update($id, $data){
	    $ret=false;
	
	    $this->db->set($data);
	    $this->db->where($this->pk_fieldname, $id);
	    $proc1= $this->db->update($this->tablename);
	    $affect_count1=$this->db->affected_rows($this->db);
	
	    if($proc1 > 0 ){
	        $ret=true;
	    }
		//echo $this->db->last_query();
	
	    return $ret;
	}

	/**
		제거
	*/
    public function remove($id) {
		if($id){
		    $this->db->reset_query();
		    $ret = $this->db->delete($this->tablename, array("id"=>$id));
		}else {
		    $ret = false;
		}
		return $ret;

    }

 	/**
     * key값으로 조회
     */
    public function search_by_list_one($key_id){
        $ret=null;

        $query ="SELECT *, JSON_UNQUOTE(json_extract(h_name, '$.kr')) AS name_kr, JSON_UNQUOTE(json_extract(h_name, '$.en')) AS name_en FROM houses WHERE id=".$key_id  ;
        $q = $this->db->query($query);

		$ret=$q->result_array();

        return $ret;
    }
}
