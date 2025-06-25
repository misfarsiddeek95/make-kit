<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Customers_Modal extends CI_Model {
    public function get_all_cust() {
        $this -> db -> select('c.cust_id,c.fname,c.lname,c.mobile,c.email,c.status as custStatus,c.added_date,p.pid,p.photo_path,p.photo_title'); 
        $this -> db -> from('customers c');
        $this->db->join('photo p', 'p.table="customers" AND p.field_id = c.cust_id', 'left outer');
        $query = $this -> db -> get();
        $results=$query -> result();
        return $results;
    }

    function deleteCustomerAddr($id){
        $this->db->where('user_id', $id);
        $this->db->where("user_id",$id)->where("(add_type=0 OR add_type=1)");
        $this->db->delete('addresses');
        if($this->db->affected_rows()>0){
            return true;
        }else{
            return false;
        }
    }

    function getCustPhotos($id){
        $this->db->select('*');
        $this->db->from('photo');
        $this->db->where('table', 'customers');
        $this->db->where('field_id', $id);
        $query = $this->db->get();
        if(0<$query->num_rows()){
          return $query->result();
        }else{
          return false;
        }
    }

    public function getCustDetail($id) {
        $this -> db -> select('c.cust_id,c.fname,c.lname,c.mobile,c.email,c.status,a.add_id,a.address,a.city_id,a.reg_id,a.country_id,a.phone');
        $this -> db -> from('customers c');
        $this->db->where('c.cust_id', $id);
        $this->db->where('a.add_type', 0);
        $this->db->join('addresses a', 'a.user_id = c.cust_id');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function cust_email_exist_check($email){
        $this -> db -> select('*');
        $this -> db -> from('customers');
        $this -> db -> where('email', $email);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $rowCount=$query -> num_rows();
        if($rowCount == 1){
            return true;
        }else{
            return false;
        }
    }

    function saveCust($cust_id,$add_id,$user_array,$addr_array){
        $this->db->trans_start();

        if ($cust_id==0) {
            $this->db->insert('customers',$user_array);
            $addr_array['user_id'] =  $this->db->insert_id();

            $this->db->insert('addresses',$addr_array);
        }else{
            $this->db->where('cust_id', $cust_id);
            $this->db->update('customers', $user_array);

            $this->db->where('add_id', $add_id);
            $this->db->update('addresses', $addr_array);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
}

