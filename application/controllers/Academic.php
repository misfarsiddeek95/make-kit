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

    # School Institutes
    # ------------------------------------------------------------------------------
    public function school_class() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_class= $this->Admin_modal->isAccessRightGiven($group_id,34)?0:1;
            if ($manage_class) {
                throw new Exception("אין לך הרשאה לנהל מוסדות.");
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
                    throw new Exception("אין לך הרשאה לערוך מוסד.");
                } 
                $type = 'update';
            }else{
                if ($add_class) {
                    throw new Exception("אין לך הרשאה להוסיף מוסד.");
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
                $check_class_used_for_tcrs = $this->Common_modal->checkUsedForDelete('class_id','classsec_for_teacher','class_id',$class_id);
                if ($check_class_used_for_tcrs) {
                    $class_delete = $this->Common_modal->delete('class','class_id',$class_id);
                    if ($class_delete) {
                        $this->Common_modal->delete('class_subjects','class_id',$class_id);
                        $message = array("status" => "success","message" => "מוסד נמחק בהצלחה.");
                    }else{
                        throw new Exception("לא ניתן למחוק מוסד זה.");
                    }
                }else {
                    throw new Exception("מדריכים משוייכים למוסד זה.");
                }
            }else {
                throw new Exception("אין לך הרשאה למחוק מוסד.");
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
                throw new Exception("אין לך הרשאה לנהל חוגים.");
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
                    throw new Exception("אין לך הרשאה לערוך חוג.");
                } 
                $type = 'update';
            }else{
                if ($add_subject) {
                    throw new Exception("אין לך הרשאה להוסיף חוג.");
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
                        $message = array("status" => "success","message" => "חוג נמחק בהצלחה.");
                    }else{
                        throw new Exception("לא ניתן למחוק חוג זה.");
                    }
                }else{
                    throw new Exception("חוג זה משויך לכיתה ומורה.");
                }
            }else {
                throw new Exception("אין לך הרשאה למחוק חוג.");
            }

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    # Assign Subject for Institute
    # ------------------------------------------------------------------------------
    public function subjects_for_class() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_class_subject= $this->Admin_modal->isAccessRightGiven($group_id,123)?0:1;
            if ($manage_class_subject) {
                throw new Exception("אין לך הרשאה לנהל הקצאת חוגים למוסדות.");
            }

            $data['assign_subject_list']= $this->Admin_modal->isAccessRightGiven($group_id,124)?1:0;
            $data['assign_subject']= $this->Admin_modal->isAccessRightGiven($group_id,125)?1:0;
            $data['edit_assigned_subject']= $this->Admin_modal->isAccessRightGiven($group_id,126)?1:0;
            $data['delete_assigned_subject']= $this->Admin_modal->isAccessRightGiven($group_id,127)?1:0;  
            $data['all_classes'] = $this->Academic_model->get_classes(); 
            $data['all_subjects'] = $this->Common_modal->getAll('subjects'); 
            $data['class_subjects'] = $this->Academic_model->get_classes_subjects(); 
            $this->load->view('assign_class_subjects',$data);

        } catch (Exception $ex) {
            redirect(base_url());
        }
    }

    public function saveClassSubjects() { 
        try {
            $clsub_id= $this->input->post('clsub_id');
            $class_id= $this->input->post('class_id'); 
            
            if ($class_id == null || $class_id == '' || $class_id == 0) {
                throw new Exception("אנא בחר את המוסד.");
            }

            if (isset($_POST['subject'])){
                $subject= $this->input->post('subject');
            }else{
                $subject = array();
            }
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $assign_subject= $this->Admin_modal->isAccessRightGiven($group_id,125)?0:1;
            $edit_assigned_subject= $this->Admin_modal->isAccessRightGiven($group_id,126)?0:1; 
    
            if ($clsub_id != 0) {
                if ($edit_assigned_subject) {
                    throw new Exception("אין לך הרשאה לערוך חוג משוייך.");
                } 
                $type = 'update';
            }else{
                if ($assign_subject) {
                    throw new Exception("אין לך הרשאה להקצות חוג.");
                } 
                $type = 'save';
            }
            $clsub_id = $this->Academic_model->assign_class_subjects($clsub_id,$class_id,$subject);
            $message = array("status" => "success","message" => $type,"id" => $clsub_id); 
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function deleteClassSubjects() {
        try {
            $class_id = $this->input->post('class_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_ = $this->Admin_modal->isAccessRightGiven($group_id,63)?1:0;
            if ($delete_) {
                $check_used = $this->Common_modal->checkUsedForDelete('class_id','subject_assign','class_id',$class_id);
                if ($check_used) { 
                    $_delete = $this->Common_modal->delete('class_subjects','class_id',$class_id);
                    if ($_delete) { 
                        $msg = array("status" => "success","message" => "חוג מוסד נמחק בהצלחה.");
                    }else{
                        throw new Exception("לא ניתן למחוק חוג מוסד זה.");
                    }
                }else{
                    throw new Exception("חוג מוסד זה משוייך למורה.");
                }
            }else {
                throw new Exception("אין לך הרשאה למחוק חוג מוסד.");
            }

        } catch (Exception $ex) {
            $msg = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($msg);
    }

    # Assign Institute Instructor
    # ------------------------------------------------------------------------------
    public function assign_class_teacher() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_class_teacher= $this->Admin_modal->isAccessRightGiven($group_id,134)?0:1;
            if ($manage_class_teacher) {
                throw new Exception("אין לך הרשאה לנהל מדריך מוסד.");
            }

            $data['class_tr_list']= $this->Admin_modal->isAccessRightGiven($group_id,135)?1:0;
            $data['add_class_tr']= $this->Admin_modal->isAccessRightGiven($group_id,136)?1:0;
            $data['edit_class_tr']= $this->Admin_modal->isAccessRightGiven($group_id,137)?1:0;
            $data['delete_class_tr']= $this->Admin_modal->isAccessRightGiven($group_id,138)?1:0; 

            $data['teachers'] = $this->Academic_model->get_all_teachers(); 
            $data['all_classes'] = $this->Academic_model->get_classes();  
            $data['assigned_teachers'] = $this->Academic_model->get_class_sections_for_tcrs();
            $this->load->view('assign_class_teacher',$data);
            
        } catch (Exception $ex) {
            redirect(base_url());
        } 
    }

    public function saveAssignedTeacher() { 
        try {
            $tc_id= $this->input->post('tc_id');
            $class_id= $this->input->post('class_id');
            $date = date("Y-m-d H:i:s");

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            if (isset($_POST['teachers'])){
                $teachers= $this->input->post('teachers');
            }else{
                $teachers = array();
            }

            $assign_array = array(
                'class_id' => $class_id, 
                'added_by' => $group_id, 
                'created_date' => $date 
            );

            $add_class_tr= $this->Admin_modal->isAccessRightGiven($group_id,136)?0:1;
            $edit_class_tr= $this->Admin_modal->isAccessRightGiven($group_id,137)?0:1; 

            if ($tc_id != 0) {
                if ($edit_class_tr) {
                    throw new Exception("אין לך הרשאה לערוך מדריך מוסד.");
                } 
                $type = 'update';
            }else{
                if ($add_class_tr) {
                    throw new Exception("אין לך הרשאה להוסיף מדריך מוסד.");
                } 
                $res = $this->Academic_model->check_tr_exist($class_id);
                if ($res) {
                    throw new Exception("מדריך כבר שויך למוסד זה.");
                }
                $type = 'save';
            } 
            $tc_id = $this->Academic_model->save_assigned_teachers($tc_id,$assign_array,$teachers);
            $message = array("status" => "success","message" => $type,"id" => $tc_id);  
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function deleteClassTeacher() {
        try {
            $clsec_id= $this->input->post('clsec_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_class_teacher= $this->Admin_modal->isAccessRightGiven($group_id,138)?1:0;
            if ($delete_class_teacher) {
                    $class_tcr_delete = $this->Common_modal->delete('classsec_for_teacher','clsec_id',$clsec_id);
                    if ($class_tcr_delete) {
                        $this->Common_modal->delete('classec_teacher','clsec_id',$clsec_id); 
                        $message = array("status" => "success","message" => "מדריך כיתה נמחק בהצלחה.");
                    }else{
                        throw new Exception("לא ניתן למחוק מדריך כיתה זה.");
                    } 
            }else {
                throw new Exception("אין לך הרשאה למחוק מדריך כיתה.");
            }

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    # Assign Instructors for Circle
    # ------------------------------------------------------------------------------
    public function assign_subject() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_assign_subject= $this->Admin_modal->isAccessRightGiven($group_id,139)?0:1;
            if ($manage_assign_subject) {
                throw new Exception("אין לך הרשאה לנהל הקצאת חוגים.");
            }

            $data['assign_subject_list']= $this->Admin_modal->isAccessRightGiven($group_id,140)?1:0;
            $data['assign_subject']= $this->Admin_modal->isAccessRightGiven($group_id,141)?1:0;
            $data['edit_assigned_subject']= $this->Admin_modal->isAccessRightGiven($group_id,142)?1:0;
            $data['delete_assigned_subject']= $this->Admin_modal->isAccessRightGiven($group_id,143)?1:0; 

            $data['all_classes'] = $this->Academic_model->get_classes(); 
            $data['all_teachers'] = $this->Academic_model->get_all_teachers(); 
            $data['all_assigned_subjects'] = $this->Academic_model->get_subject_first();
            $this->load->view('assign_subject',$data);

        } catch (Exception $ex) {
            redirect(base_url());
        }
    }

    public function getSectionsandSubs() {
        $class_id = $this->input->post('class_id');
        $results['subjects'] = $this->Academic_model->get_subjects($class_id);
        $results['teachers'] = $this->Academic_model->get_class_subject($class_id);
        echo json_encode($results);
    }

    public function saveAssignedSubject() { 
        try {
            $sa_id= $this->input->post('sa_id');
            $class_id= $this->input->post('class_id');
            $teacher_id= $this->input->post('teacher_id');
            $subject_id= $this->input->post('subject_id');
            $date = date("Y-m-d H:i:s");
            
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $assign_subject= $this->Admin_modal->isAccessRightGiven($group_id,141)?0:1;
            $edit_assigned_subject= $this->Admin_modal->isAccessRightGiven($group_id,142)?0:1;

            $assigning_array = array(
                'class_id' => $class_id,  
                'teacher_id' => $teacher_id, 
                'subject_id' => $subject_id, 
                'added_by' => $group_id, 
                'created_date' => $date 
            );

            $checkExistsValue = $this->Academic_model->check_intructor_circle_exists($assigning_array);

            if ($checkExistsValue) {
                $sa_id = $checkExistsValue;
            }
    
            if ($sa_id != 0) {
                if ($edit_assigned_subject) {
                    throw new Exception("אין לך הרשאה לערוך חוג משוייך.");
                } 
                $type = 'update';
            }else{
                if ($assign_subject) {
                    throw new Exception("אין לך הרשאה להקצות חוג.");
                } 
                $type = 'save';
            }
            $sa_id = $this->Academic_model->assign_subjects($sa_id,$assigning_array);
            $message = array("status" => "success","message" => $type,"id" => $sa_id); 
        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function deleteAssignedSubject() {
        try {
            $sa_id= $this->input->post('sa_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_assigned_subject= $this->Admin_modal->isAccessRightGiven($group_id,143)?1:0;
            if ($delete_assigned_subject) {
                    $assigned_sub_delete = $this->Common_modal->delete('subject_assign','sa_id',$sa_id);
                    if ($assigned_sub_delete) {
                        $message = array("status" => "success","message" => "חוג משוייך נמחק בהצלחה.");
                    }else{
                        throw new Exception("לא ניתן למחוק חוג משוייך זה.");
                    } 
            }else {
                throw new Exception("אין לך הרשאה למחוק חוג משוייך.");
            }

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}