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

    public function get_classes_subjects() {
        $this->db->select('c.*');
        $this->db->from('class c');
        $this->db->order_by('c.class_id','asc');
        $this->db->join('class_subjects cs','cs.class_id=c.class_id');
        $this->db->group_by('c.class_id');
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $row) {
            $row->subjects = $this->get_classed_subs($row->class_id);
        }
        return $result;
    }

    public function get_classed_subs($class_id) {
        $this->db->select('s.sub_id,s.subject_name');
        $this->db->from('class_subjects cs');
        $this->db->where('cs.class_id', $class_id);  
        $this->db->order_by('s.sub_id','asc');
        $this->db->join('subjects s','s.sub_id=cs.subject_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function assign_class_subjects($clsub_id,$class_id,$subject) {
        $this->db->trans_start();
        $type = FALSE;
        if ($clsub_id == 0) { 
            if (!(empty($subject))) {
                $sub_array = array();
                foreach ($subject as $key => $value) {
                    $sb_arr = array(
                        'class_id' => $class_id, 
                        'subject_id' => $value 
                    );
                    $sub_array[] = $sb_arr;
                }
                if (!(empty($sub_array))) {
                    $this->db->insert_batch('class_subjects', $sub_array);
                    $clsub_id =  $this->db->insert_id();
                }
            } 
        }else{
            $type = TRUE; 
            $sub_array = array();
            $exist_clsub_ids = array();
            if (!(empty($subject))) {
                foreach ($subject as $key => $value) {
                    $sb_arr = array(
                        'class_id' => $class_id, 
                        'subject_id' => $value 
                    );
                    $result = $this->Common_modal->checkExistForUpdate('clsub_id','class_subjects',$sb_arr);
                    if ($result) {
                        $exist_clsub_ids[] = $result->clsub_id;
                    }else{
                        $sub_array[] = $sb_arr;
                    } 
                }
            } 
            if ($type) {
                $this->db->where('class_id', $class_id); 
                if (!(empty($exist_clsub_ids))) {
                    $this->db->where_not_in('clsub_id', $exist_clsub_ids);
                }
                $this->db->delete('class_subjects');
            }

            if (!(empty($sub_array))) {
                $this->db->insert_batch('class_subjects', $sub_array);       
            }
        }
        $this->db->trans_complete();
        return $clsub_id; 
    }
}

?>