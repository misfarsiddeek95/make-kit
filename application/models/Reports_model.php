<?php

class Reports_model extends CI_Model{ 

    public function filter_students($data){
        $this->db->select('eu.id as user_id,eu.name,eu.points_earned,eu.points_spent,eu.points_earned_medalian,ct.city_name,ct.city_name,ct.city_name_hebrew,p.photo_path,p.extension,c.class_name,CONCAT_WS(" ", su.fname, su.lname) AS instructor_name,s.subject_name');
        $this->db->from('external_users eu');
        if ($data['class_id'] != '') {
            $this->db->where('eu.class_id',$data['class_id']);
        }
        if ($data['city_id'] != '') {
            $this->db->where('eu.city_id',$data['city_id']);
        }
        if ($data['teacher_id'] != '') {
            $this->db->where('eu.instructor_id',$data['teacher_id']);
        }
        if ($data['subject_id'] != '') {
            $this->db->where('eu.subject_id',$data['subject_id']);
        }
        $this->db->join('class c','c.class_id=eu.class_id');
        $this->db->join('cities ct','ct.city_id=eu.city_id', 'left outer');
        $this->db->join('subjects s', 's.sub_id=eu.subject_id', 'left outer');
        $this->db->join('photo p', 'p.table = "external_users" AND p.field_id = eu.id', 'left outer');
        $this->db->join('staff_users su', 'su.user_id=eu.instructor_id', 'left outer');
        $query = $this->db->get();
        return $query->result();
    }
}