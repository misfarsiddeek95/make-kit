<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Academic extends Admin_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal");
        $this->load->model("Academic_model");
        $this->load->library("Aayusmain");
    }

    # School Classes
    # ------------------------------------------------------------------------------
    public function school_class() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_class= $this->Admin_modal->isAccessRightGiven($group_id,34)?0:1;
            if ($manage_class) {
                throw new Exception("You don't have the permissoin to manage class.");
            }

            $data['class_list']= $this->Admin_modal->isAccessRightGiven($group_id,103)?1:0;
            $data['add_class']= $this->Admin_modal->isAccessRightGiven($group_id,104)?1:0;
            $data['edit_class']= $this->Admin_modal->isAccessRightGiven($group_id,105)?1:0;
            $data['delete_class']= $this->Admin_modal->isAccessRightGiven($group_id,106)?1:0; 

            $data['all_classes'] = $this->Academic_model->get_all_class(); 

            $this->load->view('class',$data);
            
        } catch (Exception $ex) {
            redirect(base_url());
        } 
    }

    public function saveClass() { 
        try {
            $class_id= $this->input->post('class_id');
            $class_name= $this->input->post('class_name');
            $class_numeric= $this->input->post('class_numeric');
            $date = date("Y-m-d H:i:s");
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $add_class= $this->Admin_modal->isAccessRightGiven($group_id,104)?0:1;
            $edit_class= $this->Admin_modal->isAccessRightGiven($group_id,105)?0:1;
    
            $class_array = array(
                'class_name' => $class_name, 
                'class_numeric' => $class_numeric ? $class_numeric : null, 
                'added_by' => $group_id, 
                'created_date' => $date 
            );

            if ($class_id != 0) {
                if ($edit_class) {
                    throw new Exception("You don't have the permission to edit class.");
                } 
                $type = 'update';
            }else{
                if ($add_class) {
                    throw new Exception("You don't have the permission to add class.");
                } 
                $type = 'save';
            }
            $class_id = $this->Academic_model->save_class($class_id,$class_array);
            
            $message = array("status" => "success","message" => $type,"id" => $class_id); 
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function deleteClass() {
        try {
            $class_id= $this->input->post('class_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_class= $this->Admin_modal->isAccessRightGiven($group_id,37)?1:0;
            if ($delete_class) {
                $check_class_used = $this->Common_modal->checkUsedForDelete('class_id','student_class','class_id',$class_id);
                $check_class_used_for_tcrs = $this->Common_modal->checkUsedForDelete('class_id','classsec_for_teacher','class_id',$class_id);
                if ($check_class_used) {
                    if ($check_class_used_for_tcrs) {
                        $class_delete = $this->Common_modal->delete('class','class_id',$class_id);
                        if ($class_delete) {
                            $this->Common_modal->delete('class_section','class_id',$class_id);
                            $this->Common_modal->delete('class_subjects','class_id',$class_id);
                            $message = array("status" => "success","message" => "Class deleted successfully.");
                        }else{
                            throw new Exception("Unable to delete this class.");
                        }
                    }else {
                        throw new Exception("Teachers are assigned to this class.");
                    }
                }else{
                    throw new Exception("Students are in this class.");
                }
            }else {
                throw new Exception("You don't have the permission to delete class.");
            }

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    # School Subjects
    # ------------------------------------------------------------------------------
    public function subject() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $_manage = $this->Admin_modal->isAccessRightGiven($group_id,118)?0:1;
            if ($_manage) {
                throw new Exception("You don't have the permissoin to manage subjects.");
            }

            $data['subject_list']= $this->Admin_modal->isAccessRightGiven($group_id,119) ? 1 : 0;
            $data['add_subject']= $this->Admin_modal->isAccessRightGiven($group_id,120) ? 1 : 0;
            $data['edit_subject']= $this->Admin_modal->isAccessRightGiven($group_id,121) ? 1 : 0;
            $data['delete_subject']= $this->Admin_modal->isAccessRightGiven($group_id,122) ? 1 : 0;

            $data['all_subjects'] = $this->Common_modal->getAll('subjects');
            $this->load->view('subjects',$data);
            
        } catch (Exception $ex) {
            redirect(base_url());
        } 
    }

    public function saveSubject() { 
        try {
            $subject_id= $this->input->post('subject_id');
            $subject_name= $this->input->post('subject_name');
            $subject_code= $this->input->post('subject_code');
            $subject_type= $this->input->post('subject_type');
            $date = date("Y-m-d H:i:s");
            
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $add_subject= $this->Admin_modal->isAccessRightGiven($group_id,120)?0:1;
            $edit_subject= $this->Admin_modal->isAccessRightGiven($group_id,121)?0:1;
    
            $subject_array = array(
                'subject_name' => $subject_name, 
                'subject_code' => $subject_code, 
                'subject_type' => $subject_type, 
                'added_by' => $group_id, 
                'created_date' => $date 
            );
    
            if ($subject_id != 0) {
                if ($edit_subject) {
                    throw new Exception("You don't have the permission to edit subject.");
                } 
                $type = 'update';
            }else{
                if ($add_subject) {
                    throw new Exception("You don't have the permission to add subject.");
                } 
                $type = 'save';
            }
            $subject_id = $this->Common_modal->insert_me($subject_id,$subject_array,'subjects','sub_id');
            $message = array("status" => "success","message" => $type,"id" => $subject_id); 
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function deleteSubject() {
        try {
            $sub_id= $this->input->post('sub_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_subject= $this->Admin_modal->isAccessRightGiven($group_id,122)?1:0;
            if ($delete_subject) {
                $check_sbj_used = $this->Common_modal->checkUsedForDelete('subject_id','subject_assign','subject_id',$sub_id);
                if ($check_sbj_used) { 
                    $sbj_delete = $this->Common_modal->delete('subjects','sub_id',$sub_id);
                    if ($sbj_delete) { 
                        $message = array("status" => "success","message" => "Subject deleted successfully.");
                    }else{
                        throw new Exception("Unable to delete this Subject.");
                    }
                }else{
                    throw new Exception("This subject is assigned to class and teacher.");
                }
            }else {
                throw new Exception("You don't have the permission to delete Subject.");
            }

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}