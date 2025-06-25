<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {
    public function __construct(){
        parent::__construct();
    }

    public function index() {
    	if($this->session->userdata('staff_logged_in')==null){
            $this->load->view('login');
        }else {
        	redirect(base_url('back-office'));
        }
        
    }
    public function not_found() {
        $this->load->view('not_found');
    }
}
