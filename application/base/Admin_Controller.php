<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Admin_Controller extends MY_Controller {
    function __construct()
    {
        $this->data = array();
        parent::__construct();
        if($this->session->userdata('staff_logged_in')==null){
            redirect(base_url());
        }
        $this->load->model("Group_options_modal");
        $user_id = $this->session->userdata['staff_logged_in']['user_id'];
        $this->data['tree']= $this->Group_options_modal->get_access_group_options($user_id);
        $this->data['curr'] = '₪';
        $this->load->vars($this->data);
        $this->folder = $_SERVER['DOCUMENT_ROOT'] .  "/make-kit";
    }
    
    
}
