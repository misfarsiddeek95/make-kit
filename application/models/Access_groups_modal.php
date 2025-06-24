<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Access_groups_modal extends CI_Model {
    public function create_group($group) {
        $this->db->insert('access_groups', $group);
    }
    
    public function check_group_exists($code) {
        $this -> db -> select('*');
        $this -> db -> from('access_groups');
        $this -> db -> where('group_code', $code);
        $this -> db -> limit(1);

        $query = $this -> db -> get();
        $rowCount=$query -> num_rows();
        if($rowCount == 1){
                return true;
        }else{
                return false;
        }
    }
    
    public function get_access_groups() {
        $this -> db -> select('*'); 
        $this -> db -> from('access_groups');
        $query = $this -> db -> get();
        $results=$query -> result();
        return $results;
    }
    
    public function get_access_group_by_id($id) {
        $this -> db -> select('*');
        $this -> db -> from('access_groups');
        $this -> db -> where('group_id', $id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        return $query->row();
    }
    
    function update_access_group($group_id,$data){
        $this->db->where('group_id', $group_id);
        $this->db->update('access_groups', $data);
    }
    
    function delete_access_group($group_id){
        $this -> db -> select('*');
        $this -> db -> from('staff_users');
        $this -> db -> where('access_group', $group_id);
        $query = $this -> db -> get();
        $rowCount=$query -> num_rows();
        if($rowCount > 1){
            throw new Exception('Users Exists for this Access Group');
        }else{
            $this->db->where('group_id', $group_id);
            $this->db->delete('group_progs');
            
            $this->db->where('group_id', $group_id);
            $this->db->delete('access_groups');
        }
    }

    function chechGroupUsed($id){
        $this->db->select('access_group');
        $this->db->from("staff_users");
        $this->db->where("access_group", $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
}
