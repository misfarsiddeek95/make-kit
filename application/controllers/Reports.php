<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal"); 
        $this->load->model("Reports_model");
    }

    public function studentReport() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_main = $this->Admin_modal->isAccessRightGiven($group_id,156) ? 0 : 1;
            if ($manage_main) {
                throw new Exception("אין לך הרשאה לנהל דוח תלמידים.");
            }

            $data['loadInstitutes'] = $this->Common_modal->getAll('class');
            $data['loadCities'] = $this->Common_modal->getAll('cities');
            $data['loadSubjects'] = $this->Common_modal->getAll('subjects');

            $instructorConditions = array('su.access_group' => 2, 'su.status' => 1);
            $data['loadInstructors'] = $this->Common_modal->get_all_selected_fields('su.user_id as teacher_id,CONCAT_WS(" ", su.fname, su.lname) AS teacher_name','staff_users su',$instructorConditions);

            $this->load->view('report_students',$data);

        } catch (Exception $ex) {
            redirect(base_url());
        }
    }

    public function filterStudents() {
        $class_id = $this->input->post('class_id');
        $city_id = $this->input->post('city_id');
        $teacher_id = $this->input->post('teacher_id');
        $subject_id = $this->input->post('subject_id');

        $data = array(
            'class_id' => $class_id,
            'city_id' => $city_id,
            'teacher_id' => $teacher_id,
            'subject_id' => $subject_id
        );

        $result = $this->Reports_model->filter_students($data);
        echo json_encode($result);
    }
}