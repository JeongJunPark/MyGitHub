<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BedModel extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

	/*  
     */
    public function totalcount(){
        $ret = 0;
        $query = " select count(beds.id) as count from beds";
        $q = $this->db->query($query);
        if($q->num_rows() == 1) {
            foreach ($q->result() as $row) {
                $ret= $row->count;
            }   
        }   
        return $ret;
    } 

	public function get_list($page, $order, $per_page ){
		$rows = null;    

		$limit_sql=" limit ".$per_page;
		if($page > 1 ) $limit_sql .= " limit ".(($page-1)*$per_page).", ".$per_page ;

		$data = array();
		$query ="select * from beds " ;

		$q = $this->db->query($query);

        return $q->result_array();
	}
    
}
