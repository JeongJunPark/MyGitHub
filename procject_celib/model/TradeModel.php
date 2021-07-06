<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TradeModel extends CI_Model {

    public $tablename="trades";
    public $pk_fieldname="id";

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->db2 = $this->load->database('wz', TRUE);
        $this->tablename="trades";
        $this->pk_fieldname="id";
    }

    public function totalcount($search_query = ''){
        $ret = 0;
        $query = " select count(t.id) as count 
                    from ".$this->tablename." as t
                    join virtual_accounts as va on va.holder = 'CELIB' AND t.virtual_account_id = va.id 
                    left join users as u on va.user_id = u.id
                    where 1 " . $search_query;

        $q = $this->db2->query($query);
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

        $query = " select t.id, t.user_id, u.name as user_name, u.phone as user_phone, t.virtual_account_id, va.number,  va.bank_code , tr_il ,tr_si , 
			t.contract_id,  t.iacct_no, t.iacct_nm, t.tr_amt, case when t.trade_type=0 then 'default' when t.trade_type=1 then 'manual' end trade_type, 
			CONCAT (date_format(tr_il , '%Y-%m-%d') ,' ',SUBSTRING(tr_si ,1,2),':',SUBSTRING(tr_si ,3,2),':',SUBSTRING(tr_si ,5,2) ) as deposit_time 
                    from ".$this->tablename." as t
                    join virtual_accounts as va on va.holder = 'CELIB' AND t.virtual_account_id = va.id 
                    left join users as u on va.user_id = u.id
                    where 1 " . $search_query . "
                    order by ".$order. $limit_sql;

		$q = $this->db2->query($query);

        if($q->num_rows() >= 1) {

			$rows = array();

			foreach ($q->result() as $row) {
				$trade = array(
						"id"=> $row->id,
						"iacct_no"=> $row->iacct_no,
						"iacct_nm"=> $row->iacct_nm,
						"tr_amt"=> $row->tr_amt,
						"trade_type"=> $row->trade_type,
						"deposit_time"=> $row->deposit_time,
						"contract_id"=> $row->contract_id,
						"user_id"=> $row->user_id,
						"user_name"=> $row->user_name,
						"user_phone"=> $row->user_phone,
						"number"=>$row->number
						);
				array_push($rows, $trade);
			}

		}else if($q->num_rows() ==1 ) {
			//$rows =$q->result();
			foreach ($q->result() as $row) {
				$trade = array(
						"id"=> $row->id,
						"iacct_no"=> $row->iacct_no,
						"iacct_nm"=> $row->iacct_nm,
						"tr_amt"=> $row->tr_amt,
						"trade_type"=> $row->trade_type,
						"deposit_time"=> $row->deposit_time,
						"contract_id"=> $row->contract_id,
						"user_name"=> $row->user_name,
						"user_phone"=> $row->user_phone,
						"number"=>$row->number
						);
				$rows = $trade;

			}
        }

        return $rows;
	}

	/*  
     * $vanumber : 계좌번호
     * $iacctnm : 입금자명
     * $username : 입주자명
     */
    public function totalcount_search_by_vanumber_iacctnm_username($vanumber, $iacctnm, $username){
        $ret = 0;
        $query = " select count(trades.id) as count from trades join virtual_accounts on virtual_accounts.holder = 'CELIB' AND trades.virtual_account_id = virtual_accounts.id";
        if($username != null && strlen($username) > 0){ 
            $query .= " join users on users.name like '%".$username."%' and trades.user_id = users.id ";
        }   
        if($vanumber != null && strlen($vanumber) > 0){ 
            $query .= " join virtual_accounts on virtual_accounts.number like '%".$vanumber."%' and trades.virtual_account_id = virtual_accounts.id ";
        }   
		$base_where = '';
		if($vanumber != null && strlen($vanumber) > 0){
            $base_where .= " where virtual_accounts.number like '%".$vanumber."%' ";
        }
        if($iacctnm != null && strlen($iacctnm) > 0){
			
            $base_where .= (empty($base_where)?" where ": " and "). " trades.iacct_nm like '%".$iacctnm."%' ";
        }
        $q = $this->db2->query($query.$base_where);
        if($q->num_rows() == 1) {
            foreach ($q->result() as $row) {
                $ret= $row->count;
            }   
        }   
        return $ret;
    } 

	/*
     * $vanumber : 계좌번호
     * $iacctnm : 입금자명
     * $username : 입주자명
     */
    public function search_by_vanumber_iacctnm_username($page, $order , $per_page, $vanumber, $iacctnm, $username){
        $base_table = "(select trades.* from trades join virtual_accounts on virtual_accounts.holder = 'CELIB' AND trades.virtual_account_id = virtual_accounts.id";
        if($order!=null && (strcasecmp($order,"user.name ASC")==0 || strcasecmp($order,"user.name DESC")==0) ){
            $base_table = "(select trades.*,users.name user_name from trades ";
        }
        if($username != null && strlen($username) > 0){
            $base_table .= " join users on users.name like '%".$username."%' and trades.user_id = users.id ";
        }else if($order!=null && (strcasecmp($order,"user.name ASC")==0 || strcasecmp($order,"user.name DESC")==0) ) {
                $base_table .= " left outer join users on trades.user_id = users.id ";
        }

		$base_where = '';
		if($vanumber != null && strlen($vanumber) > 0){
            $base_where .= " where virtual_accounts.number like '%".$vanumber."%' ";
        }
        if($iacctnm != null && strlen($iacctnm) > 0){
			
            $base_where .= (empty($base_where)?" where ": " and "). " trades.iacct_nm like '%".$iacctnm."%' ";
        }
        $base_table .= $base_where . " ) trades ";
        return $this->get_list_exec($page, $order , $per_page , $base_table );
    }

	public function get_list_exec($page, $order , $per_page , $base_table ){
		$rows = null;    

		$orderby_sql=" use index (index_trades_idx1) order by tr_il desc , tr_si desc ";
		$orderby_last=" order by deposit_time desc ";

		if($order!=null && strcasecmp($order,"id ASC")==0 ) { 
			$orderby_sql=" order by trades.id asc";
			$orderby_last=" order by id asc ";
		}   
		if($order!=null && strcasecmp($order,"id DESC")==0 ) { 
			$orderby_sql=" order by trades.id desc";
			$orderby_last=" order by id desc ";
		}   

		if($order!=null && strcasecmp($order,"iacct_no ASC")==0 ) { 
			$orderby_sql=" order by trades.iacct_no asc";
			$orderby_last=" order by iacct_no asc ";
		}   
		if($order!=null && strcasecmp($order,"iacct_no DESC")==0 ) { 
			$orderby_sql=" order by trades.iacct_no desc";
			$orderby_last=" order by iacct_no desc ";
		}   

		if($order!=null && strcasecmp($order,"iacct_nm ASC")==0 ) { 
			$orderby_sql=" order by trades.iacct_nm asc";
			$orderby_last=" order by iacct_nm asc ";
		}   
		if($order!=null && strcasecmp($order,"iacct_nm DESC")==0 ) { 
			$orderby_sql=" order by trades.iacct_nm desc";
			$orderby_last=" order by iacct_nm desc ";
		}   

		if($order!=null && strcasecmp($order,"user.name ASC")==0 ) { 
			$orderby_sql=" order by user_name asc";
			$orderby_last=" order by user_name asc ";
		}   
		if($order!=null && strcasecmp($order,"user.name DESC")==0 ) { 
			$orderby_sql=" order by user_name desc";
			$orderby_last=" order by user_name desc ";
		}   

		if($order!=null && strcasecmp($order,"tr_amt ASC")==0 ) { 
			$orderby_sql=" order by trades.tr_amt asc";
			$orderby_last=" order by tr_amt asc ";
		}   
		if($order!=null && strcasecmp($order,"tr_amt DESC")==0 ) { 
			$orderby_sql=" order by trades.tr_amt desc";
			$orderby_last=" order by tr_amt desc ";
		}

		if($order!=null && strcasecmp($order,"deposit_time ASC")==0 ) {
			$orderby_sql=" order by trades.tr_il asc, trades.tr_si asc";
			$orderby_last=" order by deposit_time asc ";
		}
		if($order!=null && strcasecmp($order,"deposit_time DESC")==0 ) {
			$orderby_sql=" order by trades.tr_il desc, trades.tr_si desc";
			$orderby_last=" order by deposit_time desc ";
		}

		$limit_sql=" limit ".$per_page;
		if($page > 1 ) $limit_sql .= " limit ".(($page-1)*$per_page).", ".$per_page ;

		$data = array();
		$query ="select * from ( ".
			" SELECT trades.id, trades.user_id, users.name as user_name, users.phone as user_phone, trades.virtual_account_id, virtual_accounts.number,  virtual_accounts.bank_code , tr_il ,tr_si , ".
			" trades.contract_id,  trades.iacct_no, trades.iacct_nm, trades.tr_amt, case when trades.trade_type=0 then 'defalt' when trades.trade_type=1 then 'manual' end trade_type, ".
			" CONCAT (date_format(tr_il , '%Y-%m-%d') ,' ',SUBSTRING(tr_si ,1,2),':',SUBSTRING(tr_si ,3,2),':',SUBSTRING(tr_si ,5,2) ) as deposit_time ".
			" FROM ( ".
			" select trades.* from ( SELECT trades.id FROM ".$base_table." ".$orderby_sql."  ".$limit_sql."  ) b ".
			" join trades on b.id= trades.id ".
			"  ) trades ".
			" left outer join users on trades.user_id = users.id ".
			" left outer join virtual_accounts on trades.virtual_account_id = virtual_accounts.id ".
			" ) a ".
			" left outer join bank_code_map on a.bank_code = bank_code_map.code  " ;
		$query .= $orderby_last;

		$q = $this->db2->query($query);

		if($q->num_rows() >= 1) {

			$rows = array();

			foreach ($q->result() as $row) {
				$trade = array(
						"id"=> $row->id,
						"iacct_no"=> $row->iacct_no,
						"iacct_nm"=> $row->iacct_nm,
						"tr_amt"=> $row->tr_amt,
						"trade_type"=> $row->trade_type,
						"deposit_time"=> $row->deposit_time,
						"contract_id"=> $row->contract_id,
						"user_id"=> $row->user_id,
						"user"=> array( "id"=> $row->user_id,"name"=> $row->user_name, "phone" => $row->user_phone ) ,
						"virtual_account"=>array("id"=> $row->virtual_account_id,"number"=> $row->number, "bank"=>$row->bank_name )
						);
				array_push($rows, $trade);
			}

		}else if($q->num_rows() ==1 ) {
			//$rows =$q->result();
			foreach ($q->result() as $row) {
				$trade = array(
						"id"=> $row->id,
						"iacct_no"=> $row->iacct_no,
						"iacct_nm"=> $row->iacct_nm,
						"tr_amt"=> $row->tr_amt,
						"trade_type"=> $row->trade_type,
						"deposit_time"=> $row->deposit_time,
						"contract_id"=> $row->contract_id,
						"user"=> array( "id"=> $row->user_id,"name"=> $row->user_name, "phone" => $row->user_phone ) ,
						"virtual_account"=>array("id"=> $row->virtual_account_id,"number"=> $row->number, "bank"=>$row->bank_name )
						);
				$rows = $trade;

			}
		}
		return $rows;
	}
    
}
