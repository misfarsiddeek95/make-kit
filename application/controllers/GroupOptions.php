<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GroupOptions extends Admin_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model("Group_Options_Modal");
        $this->load->model("Access_groups_modal");
        $this->load->model("Admin_modal");
        $this->load->model("Common_modal");
    }
    
    public function index(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $pageAccess= $this->Admin_modal->isAccessRightGiven($group_id,16)?1:0;
        if ($pageAccess) {
            $data['access_groups'] = $this->Access_groups_modal->get_access_groups();
            $this->load->view('group_options',$data);
        }else{
            redirect(base_url());
        }
    }
    
    public function update_group_options() {
        try{
            $ids = $this->input->post('ids');
            $checked_arr = explode(',', $ids);
            $group_id = $this->input->post('group_id');
            $this->Group_Options_Modal->update_group_options($checked_arr,$group_id);
            $message = array("status" => "success","message" => "Group Options successfully updated.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    
    public function get_group_options(){
        try{            
            $group_id = $this->input->post('group_id');
            $tree = $this->Group_Options_Modal->get_all_group_options($group_id);
            echo json_encode($tree);
        }catch(Exception $ex){
            $error=  array('error'=>$ex->getMessage());
            echo json_encode($error);
        }
    }

    public function AccessGroups()
    {
        $data['access_groups'] = $this->Access_groups_modal->get_access_groups();
        $this->load->view('access_groups',$data);
    }

    public function addAccessGroups()
    {
        $group_code= $this->input->post('group_code');
        $group_desc= $this->input->post('group_desc');
        $group_id= $this->input->post('group_id');

        $data = array(
            'group_code' => $group_code,
            'group_desc' => $group_desc
        );
        if ($group_id==0) {
            $group_id = $this->Common_modal->insert('access_groups',$data);
            $message = array("status" => "success","message" => "Record added successfully.");
        }else{
            $this->Common_modal->update('group_id',$group_id,'access_groups',$data);
            $message = array("status" => "success","message" => "Record updated successfully.");
        }
        echo json_encode($message);
    }
    public function deleteAccessGroups()
    {
        $group_id= $this->input->post('group_id');

        $GroupUsed = $this->Access_groups_modal->chechGroupUsed($group_id);
        if ($GroupUsed) {
            $message = array("status" => "error","message" => "User already exist in this access group.");
        }else{
            $this->Common_modal->delete('access_groups','group_id',$group_id);
            $this->Common_modal->delete('group_progs','group_id',$group_id);
            $message = array("status" => "success","message" => "Access group deleted successfully.");
        }
        echo json_encode($message);
    }
}