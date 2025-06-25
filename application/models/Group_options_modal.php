<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Group_options_modal extends CI_Model {

    public function get_access_group_options($user_id) {
        $this -> db -> select('*');
        $this -> db -> from('staff_users');
        $this -> db -> where('user_id', $user_id);
        $this -> db -> limit(1);
        $query_user = $this -> db -> get();
        $group_id = $query_user->row()->access_group;
        
        $this -> db -> select('prg_id'); 
        $this -> db -> from('group_progs');
        $this -> db -> where('group_id', $group_id);
        $query = $this -> db -> get();
        $available=$query -> result();

        $available = array_map(function($o) use ($available) {return (integer)$o->prg_id;}, $available);
        
        if (!empty($available)) {
            $this -> db -> select('*'); 
            $this -> db -> from('system_options');
            $this -> db -> where('ismain', 1);
            $this -> db -> where('depth_level', 1);
            $this -> db -> where('is_event', 0);
            $this -> db -> where_in('optid', $available);
            $this->db->order_by("prg_order", "asc");
            $query = $this -> db -> get();
            $main_progs = $query -> result();
                
            foreach ($main_progs as $main_prog) {
                $child = $this->get_opt_child_options($main_prog->optid, $available, $main_prog->depth_level+1);
                $main_prog->children=$child;
            }
        }
        return $main_progs;
    }

    private function get_opt_child_options($parent_id, $available_opt, $depth_level) {
        $this -> db -> select('*'); 
        $this -> db -> from('system_options');
        $this -> db -> where('parent_id', $parent_id);
        $this -> db -> where('depth_level', $depth_level);
        $this -> db -> where('is_event', 0);
        $this -> db -> where_in('optid', $available_opt);
        $this->db->order_by("prg_order", "asc");
        $query = $this -> db -> get();
        $child_progs = $query -> result();
        
        foreach ($child_progs as $prog) {
            $child_2 = $this->get_opt_child_options($prog->optid, $available_opt, $prog->depth_level+1);
            $prog->children=$child_2;
        }
        return $child_progs;
    }

    public function get_all_group_options($group_id) {
        $this -> db -> select('prg_id'); 
        $this -> db -> from('group_progs');
        $this -> db -> where('group_id', $group_id);
        $query = $this -> db -> get();
        $available=$query -> result();
        $available = array_map(function($o) use ($available) {return (integer)$o->prg_id;}, $available);
        
        $this -> db -> select('*'); 
        $this -> db -> from('system_options');
        $this -> db -> where('ismain', 1);
        $this -> db -> where('depth_level', 1);
        $this->db->order_by("prg_order", "asc");
        $query = $this -> db -> get();
        $main_progs = $query -> result();
        
        $return_array = array();
        foreach ($main_progs as $main_prog) {
            $child = $this->get_child_options($main_prog->optid, $available, $main_prog->depth_level+1);
            
            $is_checked = FALSE;
            if(in_array($main_prog->optid, $available)){
                if(count($child)==0){
                    $is_checked = TRUE;
                }
            }
            $row = array(
                'optid'=>$main_prog->optid,
                'text' =>$main_prog->description,
                'checked'=> $is_checked,
                'children'=>$child
            );
            array_push($return_array,$row);
        }
        return $return_array;
    }

    private function get_child_options($parent_id, $available_opt, $depth_level) {
        $this -> db -> select('*'); 
        $this -> db -> from('system_options');
        $this -> db -> where('parent_id', $parent_id);
        $this -> db -> where('depth_level', $depth_level);
        $this->db->order_by("optid", "asc");
        $query = $this -> db -> get();
        $child_progs = $query -> result();
        
        $return_array = array();
        $all_child_checked = TRUE;
        foreach ($child_progs as $prog) {
            $child_2 = $this->get_child_options($prog->optid, $available_opt, $prog->depth_level+1);
            
            $is_checked = FALSE;
            if(in_array($prog->optid, $available_opt)){
                if(count($child_2)==0){
                    $is_checked = TRUE;
                }
            }
            
            $row = array(
                'optid'=>$prog->optid,
                'text' =>$prog->description,
                'checked'=> $is_checked,
                'children'=>$child_2
            );
            array_push($return_array,$row);
        }
        return $return_array;
    }

    public function update_group_options(array $options, $group_id) {
        $this->db->trans_start();
        $this->db->where('group_id', $group_id);
        $this->db->delete('group_progs');
        
        $option_2 = array();
        foreach ($options as $value) {
            $this -> db -> select('*');
            $this -> db -> from('system_options');
            $this -> db -> where('optid', $value);
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            $grp_option = $query->row();  
            
            $all_parents = explode("/",$grp_option->tree_path);
            
            foreach ($all_parents as $prg) {
                if(!in_array($prg, $option_2)){
                    array_push($option_2, (integer)$prg);
                }
            }            
        }
        
        foreach ($option_2 as $optids) {
            $data =   array('group_id'  => $group_id,
                            'prg_id'  => $optids
                     );
            $this->db->insert('group_progs', $data);
        }
        $this->db->trans_complete();
    }
    
    public function get_prg_by_id($prg_id){
        $this -> db -> select('*'); 
        $this -> db -> from('system_options');
        $this -> db -> where('optid', $prg_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        return $query->row();
    }
}