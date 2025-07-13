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

    public function load_instructors() {
        $this->db->select('s.user_id,s.fname,s.lname,s.company_name,s.nic,s.dob,s.email,s.username,s.status as userStatus,s.gender,a.*,p.pid,p.photo_path,p.extension,p.photo_title,d.phone'); 
        $this->db->from('staff_users s');
        $this->db->where('s.access_group', 2); // only teachers
        $this->db->join('photo p', 'p.table = "staff_users" AND p.field_id = s.user_id', 'left');
        $this->db->join('access_groups a', 'a.group_id = s.access_group', 'left');
        $this->db->join('addresses d', 'd.user_id = s.user_id AND d.add_type = 2', 'left'); // moved condition here
        $this->db->join('cities i', 'i.city_id = d.city_id', 'left');
        $this->db->join('regions r', 'r.reg_id = d.reg_id', 'left');
        
        $query = $this->db->get();
        return $query->result();
    }
}

