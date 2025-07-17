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

    # Students
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

            $data['loadInstitutes'] = $this->Common_modal->getAll('class');
            $this->load->view('add_external_users',$data);
        } catch (Exception $ex){
            redirect(base_url());
        } 
    }

    # Instructors
    public function instructors(){
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_teachers= $this->Admin_modal->isAccessRightGiven($group_id,128)?0:1;
            if ($manage_teachers) {
                throw new Exception("You don't have the permissoin to manage instructors.");
            }

            $data['instructor_list'] = $this->ExternalUser_model->load_instructors();
            $data['view_teacher_list']= $this->Admin_modal->isAccessRightGiven($group_id,129)?1:0;
            $data['add_teacher']= $this->Admin_modal->isAccessRightGiven($group_id,130)?1:0;
            $data['edit_teacher']= $this->Admin_modal->isAccessRightGiven($group_id,131)?1:0;
            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,132)?1:0;
            $data['delete_teacher']= $this->Admin_modal->isAccessRightGiven($group_id,133)?1:0;
            $this->load->view('teachers',$data);
        } catch (Exception $ex) {
            redirect(base_url());
        }
    }

    function addInstructor() {
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $_add = $this->Admin_modal->isAccessRightGiven($group_id,130)?0:1;
            $_edit = $this->Admin_modal->isAccessRightGiven($group_id,131)?1:0;
            $data['type']='Add';
            if (isset($_POST['user_id'])){
                if ($_edit) {
                    $data['user']= $this->Admin_modal->getUserDetail($this->input->post('user_id'));
                    $data['type']='Update';
                }else{
                    throw new Exception("You don't have the permissoin to update instructor.");
                }
            }else if ($_add){
                throw new Exception("You don't have the permissoin to add instructor.");
            }
            $data['cities'] = $this->Common_modal->getAll('cities');

            $this->load->view('add_instructor', $data);

        } catch (Exception $th) {
            redirect(base_url());
        }
    }

    function saveInstructor() {
        try {
            $user_id = $this->input->post('user_id');
            $add_id= $this->input->post('add_id');
            $fName= $this->input->post('fname');
            $lName= $this->input->post('lname');
            $region= $this->input->post('region');
            $city= $this->input->post('city');
            $country= $this->input->post('country'); 
            $gender= $this->input->post('p_gender'); 
            $email= $this->input->post('email');
            $mobile= $this->input->post('phone');
            $address = $this->input->post('address');

            $password= trim($this->input->post('password'));
            $access_group = 2;
            $date = date("Y-m-d H:i:s");

            $PhotoFileNameMD5='';

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $user_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'gender' => $gender,
                'added_by' => $group_id,
                'email' => $email,
                'access_group' => $access_group,
            );

            $addr_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'address' => $address,
                'reg_id' => $region,
                'city_id' => $city,
                'country_id' => $country,
                'phone' => $mobile,
                'add_type' => 2,
                'status' => 1,
            );

            if ($password!='') {
                $user_array['password'] = $this->get_encrypted_password($password);
            }

            if (isset($_POST['username'])) {
                $username = $this->input->post('username');
                $checkuser = $this->Admin_modal->user_exist_check('username',$username);
                if ($checkuser) {
                    throw new Exception("Username already exists. Please try another.");
                }else{
                    $user_array['username'] = $username;
                }
            }

            if ($user_id == 0 && $add_id==0) {
                $_add = $this->Admin_modal->isAccessRightGiven($group_id,130)?0:1;
                if ($_add) {
                    throw new Exception("You don't have the permissoin to add instructor.");
                }else{
                    $user_array['create_date'] = $date;
                    $type = 'save';
                    $msg = 'Instructor saved successfully.';
                }
            }else if ($user_id != 0 && $add_id!=0) {
                $edit_user= $this->Admin_modal->isAccessRightGiven($group_id,131)?0:1;
                if ($edit_user) {
                    throw new Exception("You don't have the permissoin to update instructor.");
                }
                $type = 'update';
                $msg = 'Instructor updated successfully.';
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
            $returnedUserId = $this->Admin_modal->saveUser($user_id,$add_id,$user_array,$addr_array);
            

            if (isset($_FILES['fileUpload'])) {
                if (!empty($_FILES['fileUpload']["name"])) {
                    $PhotoFileName = $_FILES['fileUpload']['name'];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
                    $folder = $this->folder."/photos/staff/";
                    if(!is_dir($folder)){
                        mkdir($folder, 0777, true);
                    }
                    $filetype = pathinfo($PhotoFileName, PATHINFO_EXTENSION);
                    $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;
                    $img_big = $folder.$PhotoFileNameMD5. '-big.'.$filetype;
                    $img_std = $folder.$PhotoFileNameMD5. '-std.'.$filetype;
                    $img_thu = $folder.$PhotoFileNameMD5. '-thu.'.$filetype;

                    if (!@move_uploaded_file ($_FILES['fileUpload']['tmp_name'],$img_org)) throw new Exception('Can not upload original file...');

                    if (pathinfo($PhotoFileName, PATHINFO_EXTENSION)=='png') {
                        $image = imagecreatefrompng($img_org);
                        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                        imagealphablending($bg, TRUE);
                        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                        imagedestroy($image);
                        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
                        imagejpeg($bg, $img_org, $quality);
                        imagedestroy($bg);
                    }

                    $this->aayusmain->make_thumb($img_org,$img_big,100,1400,1400);
                    $this->aayusmain->make_thumb($img_org,$img_std,100,500,500);
                    $this->aayusmain->make_thumb($img_org,$img_thu,100,100,100);

                    unlink( $img_org );
                }
            }

            if ($PhotoFileNameMD5!='') {
                $data = array(
                    'table' => 'staff_users',
                    'field' => 'user_id',
                    'field_id' => $returnedUserId,
                    'photo_path' => $PhotoFileNameMD5,
                    'extension' => $filetype,
                    'photo_title' => str_replace(array("-","_",".","jpg")," ", $fName),
                    'photo_order' => 0
                );


                if ($user_id!=0) {
                    $photos = $this->Common_modal->getTableSinglePhoto('staff_users',$returnedUserId);
                    if ($photos) {
                        $folder = $this->folder."/photos/staff/";
                        $imgExt = array('big','std','thu'); 
                        foreach ($imgExt as $value) {
                            $imagename = $photos->photo_path.'-'.$value.'.'.$result->extension;
                            unlink( $folder . $imagename );
                        }
                        $message = array("status" => "success","message" => 'Deleted successfully');
                    }
                }
                $inserted_id = $this->Common_modal->insert('photo',$data);
            }
            $message = array("status" => "success","message" => $msg);

        } catch (Exception $ex) {
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}