<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Other_modal extends CI_Model {
    function getAllRates(){
    	$this->db->select('cc.*,co.nicename,co.iso,cu.*');
        $this->db->from('country_currency cc');
        //$this->db->where('cc.status', 0);
        $this->db->join('country as co', 'co.country_id = cc.country_id', 'left');
        $this->db->join('currency as cu', 'cu.currency_id = cc.currency_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function check_rate_exist($country_id){
        $this -> db -> select('*');
        $this -> db -> from('country_currency');
        $this -> db -> where('country_id', $country_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          	return true;
        }else{
            return false;
        }
    }
    function saveRate($rate_id,$rate_array){
        $this->db->trans_start();

        if ($rate_id==0) {
            $this->db->insert('country_currency',$rate_array);
        }else{
            $this->db->where('cc_id', $rate_id);
            $this->db->update('country_currency', $rate_array);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
    function updateType(){
    	$q = "UPDATE country_currency SET type='0'";
        $query = $this->db->query($q);
	}

    function getCoupons($search,$status,$fdate,$tdate,$limit,$offset){
        $this->db->select('*');
        $this->db->from('coupons');

        if ($search!=''&&$search!=null) {
            $this->db->like('coupon_code', $search);
        }
        if ($status!=''||$status!=null) {
            $this->db->where('status', $status);
        }
        
        if ($fdate!=''&&$fdate!=null) {
            $fdate = date( 'Y-m-d', strtotime( $fdate ) );
            $this->db->where('valid_from >= "'.$fdate.'"');
        }

        if ($tdate!=''&&$tdate!=null) {
            $tdate = date( 'Y-m-d', strtotime( $tdate ) );
            $this->db->where('valid_to <= "'.$tdate.'"');
        }

        $this->db->order_by('create_date', "desc");

        $tempdb = clone $this->db;
        $ret['rowcount'] = $tempdb->count_all_results();
        
        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['coupons']=$query->result();

        return $ret;
    }
}

