<?php

class System_login extends CI_Model {
	function getAllWhere($table,$idName,$val){
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($idName, $val);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function get_login_user($username) {
        $this -> db -> select('staff_users.user_id,staff_users.fname,staff_users.lname,staff_users.username,staff_users.password,staff_users.access_group,staff_users.status,photo.photo_path,photo.photo_title'); 
        $this -> db -> from('staff_users');
        //$this->db->where("photo.table='staff_users' OR photo.table is NULL AND photo.status='0' OR photo.status is NULL");
        $this->db->where('username', $username);
        $this->db->join('photo', 'photo.table="staff_users" AND photo.field_id = staff_users.user_id', 'left outer');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
}

