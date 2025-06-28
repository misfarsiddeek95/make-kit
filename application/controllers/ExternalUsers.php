<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExternalUsers extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal");
        $this->load->model("ExternalUser_model");
        $this->load->library("Aayusmain");
    }

    public function index() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_students = $this->Admin_modal->isAccessRightGiven($group_id,112) ? 0 : 1;
            if ($manage_students) {
                throw new Exception("You don't have the permissoin to manage students.");
            }

            $data['student_list']= $this->Admin_modal->isAccessRightGiven($group_id,113) ? 1 : 0;
            $data['add_student']= $this->Admin_modal->isAccessRightGiven($group_id,114) ? 1 : 0;
            $data['edit_student']= $this->Admin_modal->isAccessRightGiven($group_id,115) ? 1 : 0;
            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,116) ? 1 : 0;
            $data['delete_student']= $this->Admin_modal->isAccessRightGiven($group_id,117) ? 1 : 0;

            $data['loadclass'] = $this->Common_modal->getAll('class'); 
            $this->load->view('students',$data);

        } catch (Exception $ex) {
            redirect(base_url());
        }
    }

    public function filterStudents() {
        $class_id = $this->input->post('class_id');
        $status = $this->input->post('status');
        $result = $this->Student_model->filter_students($class_id,$status);
        echo json_encode($result);
    }

    public function addExternalUser() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $_add = $this->Admin_modal->isAccessRightGiven($group_id,114) ? 0 : 1;
            $_edit = $this->Admin_modal->isAccessRightGiven($group_id,115) ? 1 : 0;

            $data['type']='Add';
            if (isset($_POST['user_id'])){
                if ($_edit) {
                    $data['user']= []; //$this->Admin_modal->getUserDetail($this->input->post('user_id'));
                    $data['type']='Update';
                }else{
                    throw new Exception("You don't have the permissoin to update student.");
                }
            }else if ($_add){
                throw new Exception("You don't have the permissoin to add student.");
            }

            $this->load->view('add_external_users',$data);
        } catch (Exception $ex){
            redirect(base_url());
        } 
    }
}