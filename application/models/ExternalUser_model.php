<?php
class ExternalUser_model extends CI_Model {
	
    public function filter_students($class_id,$status){
        $this->db->select('eu.id as user_id,eu.name,eu.role_number,eu.gender,eu.parent_name,eu.parent_phone,eu.parent_email,eu.status,ct.city_name,ct.city_name_hebrew');
        $this->db->from('external_users eu');
        $this->db->where('eu.class_id',$class_id);
        if ($status != '') {
            $this->db->where('eu.status',$status);
        }
        $this->db->join('class c','c.class_id=eu.class_id');
        $this->db->join('cities ct','ct.city_id=eu.city_id', 'left outer');
        $query = $this->db->get();
        return $query->result();
    }
}

