<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SystemLogin extends MY_Controller {
    public function __construct(){
        parent::__construct();
        $this->clear_cache();
        $this->load->model("System_login");

    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }
    public function index() {
        $this->load->view('login');
    }
    
    public function signin(){
        $userName= ($this->input->post('username'));
        $password= $this->input->post('password');
        try{
            $result = $this->System_login->get_login_user($userName);
            if($result){
                if ($result->access_group==0||$result->access_group==''||$result->access_group==null) {
                    $message = array("status" => "error","message" => "משתמש לא אושר. אנא צור קשר עם המנהל.");
                }else{
                    if($result->status==1 && password_verify($password, $result->password)){
                        $img = 'user_default.jpg';
                        if ($result->photo_path!=null) {
                          $img = 'staff/'.$result->photo_path.'-thu.'.$result->extension;
                        }
                        $log_array = array(
                            'user_id' => $result->user_id,
                            'name' => $result->fname.' '.$result->lname,
                            'image' => $img,
                            'username' => $result->username,
                            'group_id' => $result->access_group
                        );
                        $this->session->set_userdata('staff_logged_in', $log_array);
                        $message = array("status" => "success","message" => "back-office");
                    }else if(!password_verify($password, $result->password)){
                        $message = array("status" => "error","message" => "סיסמה שגויה. נסה שוב.");
                    }else{
                        $message = array("status" => "error","message" => "משתמש חסום. אנא צור קשר עם המנהל.");
                    }
                }
            }else{
                $message = array("status"=>"error","message"=>"שם משתמש לא חוקי. המשתמש אינו קיים.");
            }
            echo json_encode($message);
        }catch(Exception $ex){
            $error=  array('error'=>$ex->getMessage());
            echo json_encode($error);
        }
    }
    
    public function logout()
    {
        if($this->session->userdata('staff_logged_in')!=null){
            $sess_array = array(
                'user_id' => '',
                'name' => '',
                'image' => '',
                'username' => '',
                'group_id' => ''
            );
            $this->session->unset_userdata($sess_array);
            $this->session->sess_destroy();
            $this->clear_cache();
            redirect(base_url());
        }
    }
}

