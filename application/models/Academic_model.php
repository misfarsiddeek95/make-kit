<?php

class Academic_model extends CI_Model{ 
    
    public function __construct(){
        parent::__construct(); 
        $this->load->model('Common_modal', 'Common_modal');
    }

    public function save_class($class_id,$class_array) {
        $this->db->trans_start();
        if ($class_id == 0) {
            $this->db->insert('class',$class_array);
            $class_id =  $this->db->insert_id();
        }else{
            $this->db->where('class_id', $class_id);
            $this->db->update('class', $class_array);
        }
        $this->db->trans_complete();
        return $class_id; 
    }

    public function get_all_class() {
        $this->db->select('c.*');
        $this->db->from('class c');
        $this->db->order_by('c.class_id','asc');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function get_classes() {
        $this->db->select('c.*');
        $this->db->from('class c');
        $query = $this->db->get();
        return $query->result();
    }
}

?>