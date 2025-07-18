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
        $this->db->join('addresses d', 'd.user_id = s.user_id AND a.user_type = 1 AND d.add_type = 2', 'left'); // moved condition here
        $this->db->join('cities i', 'i.city_id = d.city_id', 'left');
        $this->db->join('regions r', 'r.reg_id = d.reg_id', 'left');
        
        $query = $this->db->get();
        return $query->result();
    }

    function load_institute_circles($instituteId) {
        $this->db->select('s.sub_id,s.subject_name');
        $this->db->from('class_subjects cs');
        $this->db->where('cs.class_id', $instituteId);
        $this->db->join('subjects s', 's.sub_id=cs.subject_id', 'left');
        $q = $this->db->get();
        return $q->result();
    }

    function load_subject_instructor($instituteId, $subjectId) {
        $this->db->select('su.user_id as teacher_id,CONCAT_WS(" ", su.fname, su.lname) AS teacher_name');
        $this->db->from('subject_assign sa');
        $this->db->where('sa.class_id', $instituteId);
        $this->db->where('sa.subject_id', $subjectId);
        $this->db->join('staff_users su', 'su.user_id=sa.teacher_id');
        $q = $this->db->get();
        return $q->result();
    }

    function register_external_user($user_id,$add_id,$user_array,$addr_array) {
        $this->db->trans_start();
        if ($user_id==0) {
            $this->db->insert('staff_users',$user_array);
            $user_id =  $this->db->insert_id();

            $addr_array['user_id'] = $user_id;
            $this->db->insert('addresses',$addr_array);
        }else{
            $this->db->where('user_id', $user_id);
            $this->db->update('staff_users', $user_array);

            $this->db->where('add_id', $add_id);
            $this->db->update('addresses', $addr_array);
        }
        $this->db->trans_complete();
        return $user_id; 
    }
}

