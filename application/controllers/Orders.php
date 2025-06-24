<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Orders extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->clear_cache();
        $this->load->model("Admin_modal");
        $this->load->model("Order_modal");
        $this->load->model("Common_modal");
    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }

    public function index(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_order= $this->Admin_modal->isAccessRightGiven($group_id,2)?0:1;

        try{
            if ($view_order) {
                throw new Exception("You don't have the permissoin to view orders.");
            }
            $data['all_orders']= $this->Admin_modal->isAccessRightGiven($group_id,57)?1:0;
            $data['add_order']= $this->Admin_modal->isAccessRightGiven($group_id,58)?1:0; 
            $data['view_order']= $this->Admin_modal->isAccessRightGiven($group_id,66)?1:0;
            $data['delete_order']= $this->Admin_modal->isAccessRightGiven($group_id,60)?1:0; 
            $data['order_status']= $this->Admin_modal->isAccessRightGiven($group_id,64)?1:0;
            $data['pay_status']= $this->Admin_modal->isAccessRightGiven($group_id,68)?1:0;

            $data['order_statuses']= $this->Common_modal->getAll('order_statuses');
            $data['payment_status']= array('2'=>'Success' , '0'=>'Pending' , '-1'=>'Canceled' , '-2'=>'Failed' , '-3'=>'Chargedback');
            $this->load->view('view_orders',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function getOrders(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $allOderAccess= $this->Admin_modal->isAccessRightGiven($group_id,57)?0:1;
        $visibleAllSites= $this->Admin_modal->isAccessRightGiven($group_id,91)?0:1;
        $search = $this->input->post('search');
        $ostatus = $this->input->post('ostatus');
        $pstatus = $this->input->post('pstatus');
        $fdate = $this->input->post('fdate');
        $tdate = $this->input->post('tdate');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');
        
        $result = $this->Order_modal->getOrders($allOderAccess,$visibleAllSites,$search,$ostatus,$pstatus,$fdate,$tdate,$limit,$offset);

        echo json_encode($result);
    }

    public function view_order(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_order= $this->Admin_modal->isAccessRightGiven($group_id,66)?0:1;
        try{
            if ($view_order) {
                throw new Exception("You don't have the permissoin to view single order.");
            }
            if (isset($_POST['order_id'])){
                $order_id = $this->input->post('order_id');
                $data['editPay']= $this->Admin_modal->isAccessRightGiven($group_id,61)?1:0;
                $data['editDelCharge']= $this->Admin_modal->isAccessRightGiven($group_id,70)?1:0;
                $data['editDisc']= $this->Admin_modal->isAccessRightGiven($group_id,71)?1:0;
                $data['editPaidAmount']= $this->Admin_modal->isAccessRightGiven($group_id,72)?1:0;
                $data['editAddress']= $this->Admin_modal->isAccessRightGiven($group_id,58)?1:0;
                $data['updateDelCharge']= $this->Admin_modal->isAccessRightGiven($group_id,62)?1:0;
                $data['order_status']= $this->Admin_modal->isAccessRightGiven($group_id,64)?1:0;
                $data['pay_status']= $this->Admin_modal->isAccessRightGiven($group_id,68)?1:0;
                $data['view_cust']= $this->Admin_modal->isAccessRightGiven($group_id,65)?1:0;
                $data['rec_det']= $this->Admin_modal->isAccessRightGiven($group_id,73)?1:0;

                $data['order_statuses']= $this->Common_modal->getAll('order_statuses');
                $data['payment_status']= array('2'=>'Success' , '0'=>'Pending' , '-1'=>'Canceled' , '-2'=>'Failed' , '-3'=>'Chargedback');

                $data['profile']=$this->Order_modal->getProfileByOrder($order_id);
                $data['address']=$this->Order_modal->getAddressByOrder($order_id);
                $data['orderDetail']=$this->Order_modal->getOrderDetails($order_id);
                $data['orderStatus']=$this->Order_modal->getOrderStatus($order_id);
                $data['orderProducts']=$this->Order_modal->getOrderProducts($order_id);

                $data['countries'] = $this->Common_modal->getCountries();
            }else{
                throw new Exception("Somthing went wrong :(");
            }

            $this->load->view('view_order',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    function deleteOrder()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_order= $this->Admin_modal->isAccessRightGiven($group_id,60)?0:1;
            if ($delete_order) {
                throw new Exception("You don't have the permissoin to delete orders.");
            }
            $order_id= $this->input->post('order_id');
            $result = $this->Common_modal->getAllWhere('orders','order_id',$order_id);
            if ($result) {
                $order_del = $this->Common_modal->delete('orders','order_id',$order_id);
                if ($order_del) {
                    $this->Common_modal->delete('order_details','order_id',$order_id);
                    $this->Common_modal->delete('addresses','add_id',$result->add_id);
                    $this->Common_modal->delete('order_product_specs','order_id',$order_id);
                    $this->Common_modal->delete('order_payment_det','order_id',$order_id);
                    $this->Common_modal->delete('order_status_det','order_id',$order_id);
                }else{
                    throw new Exception("Order unable to delete");
                }
            }else{
                throw new Exception("Somthing went wrong :(");
            }
            
            $message = array("status" => "success","message" => "Order deleted successfully.");

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function getOrderStatus()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $order_status= $this->Admin_modal->isAccessRightGiven($group_id,64)?0:1;
            $pay_status= $this->Admin_modal->isAccessRightGiven($group_id,68)?0:1;
            if ($order_status&&$pay_status) {
                throw new Exception("You don't have the permissoin to update status");
            }
            $order_id= $this->input->post('order_id');
            $message = $this->Order_modal->getCurrentStatus($order_id);
            if ($message==false) {
                throw new Exception("Somthing went wrong :(");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function updateOrderStatus()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $order_status= $this->Admin_modal->isAccessRightGiven($group_id,64)?0:1;
            $pay_status= $this->Admin_modal->isAccessRightGiven($group_id,68)?0:1;
            if ($order_status&&$pay_status) {
                throw new Exception("You don't have the permissoin to update status");
            }
            if (!(isset($_POST['sorder_id']))&&(!(isset($_POST['spayment_status']))||!(isset($_POST['sorder_status'])))){
                throw new Exception("Somthing went wrong :(");
            }

            $order_id= $this->input->post('sorder_id');
            $date = date("Y-m-d H:i:s");
            $result = $this->Order_modal->getCurrentStatus($order_id);
            if ($result==false) {
                throw new Exception("Somthing went wrong :(");
            }
            $order_arr = array();
            $status_arr = array();
            if (isset($_POST['sorder_status'])) {
                $sorder_status= $this->input->post('sorder_status');

                if (!$order_status&&$result->os_id!=$sorder_status) {
                    $status_arr = array(
                        'order_id' => $order_id,
                        'os_id' => $sorder_status,
                        'status_date' => $date
                    );
                }
            }
            if (isset($_POST['spayment_status'])) {
                $spayment_status= $this->input->post('spayment_status');
                if (!$pay_status&&$result->payment_status!=$spayment_status) {
                    $order_arr['payment_status'] = $spayment_status;
                }
            }

            if (!$pay_status||!$order_status) {
                $update_res = $this->Order_modal->updateOrderStatus($order_id,$order_arr,$status_arr);
                if ($update_res) {
                    $message = array("status" => "success","message" => "Status Updated successfully.");
                }else{
                    throw new Exception("Unable to update status :(");
                }
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    function updateOrderPayment()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $opayDet= $this->Admin_modal->isAccessRightGiven($group_id,61)?0:1;
            $odelCharge= $this->Admin_modal->isAccessRightGiven($group_id,70)?0:1;
            $oDiscount= $this->Admin_modal->isAccessRightGiven($group_id,71)?0:1;
            $oPaidAmount= $this->Admin_modal->isAccessRightGiven($group_id,72)?0:1;
            if ($opayDet) {
                throw new Exception("You don't have the permissoin to update payment details");
            }
            $order_id= $this->input->post('order_id');

            $result = $this->Common_modal->getAllWhere('orders','order_id',$order_id);
            if ($result==false) {
                throw new Exception("Somthing went wrong :(");
            }

            $cart_total = $result->cart_total;
            if ($odelCharge) {
                $deliver_charge = $result->del_charge;
            }else{
                if (isset($_POST['oDelCharge'])) {
                    $deliver_charge = $this->input->post('oDelCharge');
                }else{
                    $deliver_charge = $result->del_charge;
                }
            }

            if ($oDiscount) {
                $discount = $result->discount;
            }else{
                if (isset($_POST['oDiscount'])) {
                    $discount = $this->input->post('oDiscount');
                }else{
                    $discount = $result->discount;
                }
            }

            if ($oPaidAmount) {
                $paid_total = $result->paid_total;
            }else{
                if (isset($_POST['oPaidTotal'])) {
                    $paid_total = $this->input->post('oPaidTotal');
                }else{
                    $paid_total = $result->paid_total;
                }
            }

            $payment_arr = array(
                'del_charge' => str_replace(',','',$deliver_charge),
                'discount' => str_replace(',','',$discount),
                'paid_total' => str_replace(',','',$paid_total)
            );
            $this->Common_modal->update('order_id',$order_id,'orders',$payment_arr);
            $message = array("status" => "success","message" => "Payment details updated successfully.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    function updateOrderAddr()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $edit_order= $this->Admin_modal->isAccessRightGiven($group_id,58)?0:1;
            $updateDelCharge= $this->Admin_modal->isAccessRightGiven($group_id,62)?1:0;
            if ($edit_order) {
                throw new Exception("You don't have the permissoin to edit delivery address.");
            }
            $order_id= $this->input->post('delorder_id');
            $add_id= $this->input->post('add_id');
            $fName= $this->input->post('fName');
            $lName= $this->input->post('lName');
            $mobile= $this->input->post('mobile');
            $address= $this->input->post('address');
            $country= $this->input->post('country');
            $region= $this->input->post('region');
            $city= $this->input->post('city');
            $change_del = 1;
            $del_arr = array();

            if ($updateDelCharge) {
                if (isset($_POST['change_del'])) {
                    $change_del= 1;
                }else{
                    $change_del = 0;
                }
            }else{
                $change_del = 1;
            }


            if ($change_del) {
                $orderPros = $this->Order_modal->getOrderProForDelcharge($order_id);
                if ($orderPros) {
                    $del_charge = 0;
                    $charge_per_kg = 0;
                    $result = $this->Common_modal->get_delivery_charge($country, $region, $city);

                    if ($result!=null) {
                        $del_charge = $result->initial_charge;
                        $charge_per_kg = $result->charge_per_kg;
                    }

                    foreach($orderPros as $row){
                        $del_charge += ($row->qty*$row->weight)*$charge_per_kg;
                    }
                    $del_arr = array(
                        'delc_id' => $result->charges_id,
                        'del_charge' => $del_charge
                    );
                }else{
                    throw new Exception("Somthing went wrong :(");
                }
            }

            $addr_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'address' => $address,
                'city_id' => $city,
                'reg_id' => $region,
                'country_id' => $country,
                'phone' => $mobile
            );
            $update_addr = $this->Order_modal->updateDelAddr($order_id,$add_id,$del_arr,$addr_array);
            if ($update_addr) {
                $message = array("status" => "success","message" => "Delivery address updated successfully.");
            }else{
                throw new Exception("Unable to update Delivery address :(");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}

