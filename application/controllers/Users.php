<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Users extends Admin_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal");
        $this->load->model("Access_groups_modal");
        $this->load->model("Group_options_modal");
    }

    public function index(){
        $data['staff_users'] = $this->Admin_modal->get_all_users();
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $data['add_user']= $this->Admin_modal->isAccessRightGiven($group_id,18)?1:0;
        $data['edit_user']= $this->Admin_modal->isAccessRightGiven($group_id,20)?1:0;
        $data['delete_user']= $this->Admin_modal->isAccessRightGiven($group_id,21)?1:0;
        $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,19)?1:0;
        $this->load->view('users',$data);
    }

    public function updateUserStatus()
    {
        try{
            $user_id= $this->input->post('user_id');
            $result = $this->Common_modal->getAllWhere('staff_users','user_id',$user_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $group_id = $this->session->userdata['staff_logged_in']['group_id'];
                    $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,19)?1:0;
                    if ($ChangeStatus) {
                        $data['status']=0;
                    }else{
                        throw new Exception("You don't have the permissoin to change status.");
                    }
                }
                $this->Common_modal->update('user_id',$user_id,'staff_users',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function addUser(){
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_user= $this->Admin_modal->isAccessRightGiven($group_id,18)?0:1;
            $edit_user= $this->Admin_modal->isAccessRightGiven($group_id,20)?1:0;
            $data['type']='Add';
            if (isset($_POST['user_id'])){
                if ($edit_user) {
                    $data['user']= $this->Admin_modal->getUserDetail($this->input->post('user_id'));
                    $data['staff_sites']= $this->Admin_modal->staff_available_sites($this->input->post('user_id'));
                    $data['type']='Update';
                }else{
                    throw new Exception("You don't have the permissoin to update user.");
                }
            }else if ($add_user){
                throw new Exception("You don't have the permissoin to add user.");
            }

            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,19)?0:1;
            $data['countries'] = $this->Common_modal->getCountries();
            $data['websites']= $this->Common_modal->getWebsites();
            $data['role'] = $this->Common_modal->getAll('access_groups');
            $this->load->view('add_user',$data);
        } catch (Exception $ex){
            redirect(base_url());
        }        
    }

    public function checkDBfields(){
        $input = $_GET['input'];
        $result = $this->Common_modal->checkField($_GET['table'],$_GET['data'],$_GET[$input]);
        if($result){ 
            echo http_response_code(418);
        }else{ 
            echo http_response_code(200);
        }
    }

    public function checkDBfieldOpt(){
        $input = $_GET['input'];
        $result = $this->Common_modal->checkField($_GET['table'],$_GET['data'],$_GET[$input]);
        if($result){ 
            echo http_response_code(200);
        }else{ 
            echo http_response_code(418);
        }
    }

    function getRegion()
    {
        $country= $this->input->post('country');
        $result = $this->Common_modal->getRegion($country);
        echo json_encode($result);
    }

    function getCities()
    {
        $region= $this->input->post('region');
        $result = $this->Common_modal->getCities($region);
        echo json_encode($result);
    }

    public function saveUser(){
        try{            
            $user_id= $this->input->post('user_id');
            $add_id= $this->input->post('add_id');
            $fName= $this->input->post('fName');
            $lName= $this->input->post('lName');
            $companyName= $this->input->post('companyName');
            $nic= $this->input->post('nic');
            $dob= $this->input->post('dob');
            $email= $this->input->post('email');
            $mobile= $this->input->post('mobile');
            $address= $this->input->post('address');
            $country= $this->input->post('country');
            $region= $this->input->post('region');
            $city= $this->input->post('city');
            $username= $this->input->post('username');
            $password= trim($this->input->post('password'));
            $access_group= $this->input->post('access_group');
            $status = isset($_POST['status']) ? $_POST['status'] : 1;
            $date = date("Y-m-d H:i:s");
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            if (isset($_POST['visibleSites'])){
                $visibleSites= $this->input->post('visibleSites');
            }else{
                $visibleSites = array();
            }

            $user_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'company_name' => $companyName,
                'nic' => $nic,
                'dob' => $dob,
                'email' => $email,
                'access_group' => $access_group,
                'status' => $status,
                'modified_date' => $date
            );

            $addr_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'address' => $address,
                'city_id' => $city,
                'reg_id' => $region,
                'country_id' => $country,
                'phone' => $mobile,
                'add_type' => 2
            );

            if ($password!='') {
                $user_array['password'] = $this->get_encrypted_password($password);
            }

            if ($user_id==0&&$add_id==0) {
                $add_user= $this->Admin_modal->isAccessRightGiven($group_id,18)?0:1;
                if ($add_user) {
                    throw new Exception("You don't have the permissoin to add users.");
                }else{
                    $checkuser = $this->Admin_modal->user_name_exist_check($username);
                    if ($checkuser) {
                        throw new Exception("Username already exist.");
                    }else{
                        $user_array['username'] = $username;
                        $user_array['create_date'] = $date;
                        $type = 'save';
                    }
                }
            }else if ($user_id!=0&&$add_id!=0) {
                $edit_user= $this->Admin_modal->isAccessRightGiven($group_id,20)?0:1;
                if ($edit_user) {
                    throw new Exception("You don't have the permissoin to update users.");
                }
                $type = 'update';
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
            $this->Admin_modal->saveUser($user_id,$add_id,$visibleSites,$user_array,$addr_array);

            $message = array("status" => "success","message" => $type);

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function deleteUser()
    {
        try{ 
            $user_id= $this->input->post('user_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_user= $this->Admin_modal->isAccessRightGiven($group_id,21)?1:0;
            if ($delete_user) {
                $usercount = $this->Admin_modal->getUserCount();
                if (1<$usercount) {
                    $products = $this->Admin_modal->chechUserUsed('products',$user_id);
                    if ($products) {
                        throw new Exception("Unable to delete, User involved with system");
                    }else{
                        $user_deleted = $this->Common_modal->delete('staff_users','user_id',$user_id);
                        if ($user_deleted) {
                            $this->Admin_modal->deleteUserAddr($user_id);
                            $this->Common_modal->delete('staff_sites','user_id',$user_id);
                            $result = $this->Admin_modal->getUserPhotos($user_id);
                            if ($result) {
                                foreach ($result as $row) {
                                    $this->Common_modal->delete('photo','pid',$row->pid);
                                    $folder = $this->folder."/photos/staff/";
                                    $imgExt = array('org','big','med','std','thu','sma');

                                    foreach ($imgExt as $value) {
                                        $imagename = $row->photo_path.'-'.$value.'.jpg';
                                        unlink( $folder . $imagename);
                                    }
                                }
                            }
                            $message = array("status" => "success","message" => "User deleted successfully.");
                        }else{
                            throw new Exception("Unable to delete this user.");
                        }
                    }
                }else{
                    throw new Exception("Unable to delete last user.");
                }
            }else{
                throw new Exception("You don't have the permissoin to delete users.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function user_profile(){
        try {
            $user_id = $this->session->userdata['staff_logged_in']['user_id'];
            if ($user_id!=''||$user_id!=null||$user_id!=0){
                $data['user']= $this->Admin_modal->getUserDetail($user_id);
                $data['countries'] = $this->Common_modal->getCountries();
            }else{
                throw new Exception("Something went wrong :(");
            }            
            $this->load->view('profile',$data);
        } catch (Exception $ex){
            redirect(base_url());
        }        
    }
    public function UpdateProfile(){

        try{            
            $user_id = $this->session->userdata['staff_logged_in']['user_id'];
            $add_id= $this->input->post('add_id');
            $fName= $this->input->post('fName');
            $lName= $this->input->post('lName');
            $companyName= $this->input->post('companyName');
            //$nic= $this->input->post('nic');
            $dob= $this->input->post('dob');
            //$email= $this->input->post('email');
            $mobile= $this->input->post('mobile');
            $address= $this->input->post('address');
            $country= $this->input->post('country');
            $region= $this->input->post('region');
            $city= $this->input->post('city');
            $password= trim($this->input->post('password'));
            $date = date("Y-m-d H:i:s");

            $user_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'company_name' => $companyName,
                //'nic' => $nic,
                'dob' => $dob,
                //'email' => $email,
                'modified_date' => $date
            );

            $addr_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'address' => $address,
                'city_id' => $city,
                'reg_id' => $region,
                'country_id' => $country,
                'phone' => $mobile
            );

            if ($password!='') {
                $user_array['password'] = $this->get_encrypted_password($password);
            }
            $this->Admin_modal->saveUser($user_id,$add_id,$user_array,$addr_array);

            $log_array = $this->session->userdata('staff_logged_in');
            $log_array['name'] = $fName.' '.$lName;
            $this->session->set_userdata('staff_logged_in', $log_array);

            $message = array("status" => "success","message" => 'Profile updated successfully.');

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}