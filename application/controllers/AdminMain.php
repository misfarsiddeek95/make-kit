<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class AdminMain extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model("Admin_modal");
        $this->clear_cache();
    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }
    public function index(){
        $data['usercount'] = $this->Admin_modal->getnewuser();
        $data['totaluserlist'] = $this->Admin_modal->newUserList();
        $data['totalusercount'] = $this->Admin_modal->totalUserCount();
        $data['productcount'] = $this->Admin_modal->getnewProducts();
        $data['procountlist'] = $this->Admin_modal->productprivuse();
        $data['totalprolist'] = $this->Admin_modal->totalProList();
        $data['totalorders'] = $this->Admin_modal->totalSuccessOrders();
        $data['outOfStocks'] = $this->Admin_modal->outOfStocks();
        $data['newordercount'] = $this->Admin_modal->getnewOrders();
        $data['priviousOrders'] = $this->Admin_modal->previousOrders();
        $this->load->view('dashboard',$data);
    }
}

