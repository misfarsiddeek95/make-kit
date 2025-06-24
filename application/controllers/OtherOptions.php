<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OtherOptions extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->clear_cache();
        $this->load->model("Admin_modal");
        $this->load->model("Other_modal");
        $this->load->model("Common_modal");
    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }

    public function currency_rate(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_rate= $this->Admin_modal->isAccessRightGiven($group_id,80)?0:1;
        try{
            if ($view_rate) {
                throw new Exception("You don't have the permissoin to view rates.");
            }
            $data['curRates']= $this->Other_modal->getAllRates();
            $data['countries'] = $this->Common_modal->getCountries();
            $data['currencies'] = $this->Common_modal->getAll('currency');
            $data['addRate']= $this->Admin_modal->isAccessRightGiven($group_id,81)?1:0;
            $data['editRate']= $this->Admin_modal->isAccessRightGiven($group_id,82)?1:0; 
            $data['deleteRate']= $this->Admin_modal->isAccessRightGiven($group_id,83)?1:0;
            $data['rateStatus']= $this->Admin_modal->isAccessRightGiven($group_id,84)?1:0; 
            $data['rateType']= $this->Admin_modal->isAccessRightGiven($group_id,85)?1:0;

            $this->load->view('currency_rates',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function saveCurRate(){
        try{            
            $rate_id= $this->input->post('rate_id');
            $country= $this->input->post('country');
            $currency= $this->input->post('currency');
            $rate= $this->input->post('rate');
            $status = isset($_POST['rate_status']) ? $_POST['rate_status'] : 1;
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $rate_array = array(
                'country_id' => $country,
                'currency_id' => $currency,
                'rate' => str_replace(',','',$rate),
                'status' => $status
            );

            if ($rate_id==0) {
                $addRate= $this->Admin_modal->isAccessRightGiven($group_id,81)?0:1;
                if ($addRate) {
                    throw new Exception("You don't have the permissoin to add rates.");
                }else{
                    $check_country = $this->Other_modal->check_rate_exist($country);
                    if ($check_country) {
                        throw new Exception("Country rate already exist.");
                    }
                    $rate_array['type'] = 0;
                    $type = 'Rate added successfully';
                }
            }else if ($rate_id!=0) {
                $editRate= $this->Admin_modal->isAccessRightGiven($group_id,82)?0:1;
                if ($editRate) {
                    throw new Exception("You don't have the permissoin to update rates.");
                }
                $type = 'Rate updated successfully';
            }
            $rate_save = $this->Other_modal->saveRate($rate_id,$rate_array);
            if ($rate_save) {
                $message = array("status" => "success","message" => $type);
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function deleteCurRate()
    {
        try{ 
            $rate_id= $this->input->post('rate_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $deleteRate= $this->Admin_modal->isAccessRightGiven($group_id,83)?1:0;
            if ($deleteRate) {
                $rate_deleted = $this->Common_modal->delete('country_currency','cc_id',$rate_id);
                if ($rate_deleted) {
                    $message = array("status" => "success","message" => "Rate deleted successfully.");
                }else{
                    throw new Exception("Unable to delete this rate.");
                }
            }else{
                throw new Exception("You don't have the permissoin to delete rates.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function updateCurStatus()
    {
        try{
            $rate_id= $this->input->post('rate_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,84)?0:1;
            if ($ChangeStatus) {
                throw new Exception("You don't have the permissoin to change status.");
            }
            $result = $this->Common_modal->getAllWhere('country_currency','cc_id',$rate_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                $this->Common_modal->update('cc_id',$rate_id,'country_currency',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function updateRateType()
    {
        try{
            $rate_id= $this->input->post('rate_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,85)?0:1;
            if ($ChangeStatus) {
                throw new Exception("You don't have the permissoin to change type.");
            }
            $result = $this->Common_modal->getAllWhere('country_currency','cc_id',$rate_id);
            if ($result) {
                $this->Other_modal->updateType();
                if ($result->type==0) {
                    $data['type']=1;
                    $this->Common_modal->update('cc_id',$rate_id,'country_currency',$data);
                }
                $message = array("status" => "success","message" => "Type updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function coupons(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_coupons= $this->Admin_modal->isAccessRightGiven($group_id,86)?0:1;
        try{
            if ($view_coupons) {
                throw new Exception("You don't have the permissoin to view coupons.");
            }
            $data['addCoupons']= $this->Admin_modal->isAccessRightGiven($group_id,87)?1:0;
            $data['editCoupons']= $this->Admin_modal->isAccessRightGiven($group_id,88)?1:0; 
            $data['deleteCoupons']= $this->Admin_modal->isAccessRightGiven($group_id,89)?1:0;
            $data['couponsStatus']= $this->Admin_modal->isAccessRightGiven($group_id,90)?1:0;


            $data['categories']= $this->Common_modal->getAllCate();
            $data['brands'] = $this->Common_modal->getAllWhereStr('brands','brand_status',0);
            $this->load->view('coupons',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    function getCoupons()
    {
        $search = $this->input->post('search');
        $status = $this->input->post('status');
        $fdate = $this->input->post('fdate');
        $tdate = $this->input->post('tdate');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');

        $result = $this->Other_modal->getCoupons($search,$status,$fdate,$tdate,$limit,$offset);
        echo json_encode($result);
    }

    public function saveCoupon(){
        try{            
            $coupon_code= trim($this->input->post('coupon_code'));
            $coupAmount= str_replace(',','',$this->input->post('coupAmount'));
            $coupon_type = isset($_POST['coupon_type']) ? 1 : 0;
            $valid_from= $this->input->post('valid_from');
            $valid_to= $this->input->post('valid_to');
            $coupCount= $this->input->post('coupCount');
            $count_type = isset($_POST['count_type']) ? 1 : 0;
            $status = isset($_POST['coup_status']) ? $_POST['coup_status'] : 1;
            $coupon_for = $this->input->post('coup_for');
            $date = date("Y-m-d H:i:s");

            if (isset($_POST['coupf'])){
                $coupf= $this->input->post('coupf');
            }else{
                $coupf = array();
            }

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $addCoupon= $this->Admin_modal->isAccessRightGiven($group_id,87)?0:1;
            if ($addCoupon) {
                throw new Exception("You don't have the permissoin to add coupons.");
            }
            if ($coupCount<=0) {
                throw new Exception("Coupon count should be more than 0");
            }
            if ($coupon_type) {
                if (100<$coupAmount) {
                    throw new Exception("Coupon amount percentage should be within 100");
                }
            }
            $coupon_array = array();
            if ($count_type) { 
            	foreach ($coupf as $key => $value) { 
	                $coupon_array = array(
	                    'coupon_code' => $this->couponCodeGen($coupon_code),
	                    'coupon_type' => $coupon_type, # % or Amnt
	                    'coupon_amount' => $coupAmount,
	                    'valid_from' => $valid_from,
	                    'valid_to' => $valid_to,
	                    'count_type' => $count_type, # 1
	                    'coupon_count' => $coupCount,
	                    'coupon_for' => $coupon_for,
	                    'coupon_for_id' => $value,
	                    'create_date' => $date,
	                    'status' => $status
	                );
                	$this->Common_modal->insert('coupons',$coupon_array);
	            }
                    /*$coupon_array[$key]['coupon_code'] = $this->couponCodeGen($coupon_code)
                    $coupon_array[$key]['coupon_type'] = $coupon_type
                    $coupon_array[$key]['coupon_amount'] = $coupAmount
                    $coupon_array[$key]['valid_from'] = $valid_from
                    $coupon_array[$key]['valid_to'] = $valid_to
                    $coupon_array[$key]['count_type'] = $count_type
                    $coupon_array[$key]['coupon_count'] = $coupCount
                    $coupon_array[$key]['coupon_for'] = $coupon_for
                    $coupon_array[$key]['coupon_for_id'] = $value
                    $coupon_array[$key]['create_date'] = $date
                    $coupon_array[$key]['status'] = $status*/ 
                $message = array("status" => "success","message" => "Coupon added successfully.");
                }else{
                	foreach ($coupf as $key => $value) {
	                    for ($i=0; $i < $coupCount; $i++) { 
	                        $coupon_array[$i]['coupon_code']=$this->couponCodeGen($coupon_code);
	                        $coupon_array[$i]['coupon_type']=$coupon_type;
	                        $coupon_array[$i]['coupon_amount']=$coupAmount;
	                        $coupon_array[$i]['valid_from']=$valid_from;
	                        $coupon_array[$i]['valid_to']=$valid_to;
	                        $coupon_array[$i]['count_type']=$count_type;
	                        $coupon_array[$i]['coupon_count']=1;
	                        $coupon_array[$i]['coupon_for']=$coupon_for;
	                        $coupon_array[$i]['coupon_for_id']=$value;
	                        $coupon_array[$i]['create_date']=$date;
	                        $coupon_array[$i]['status']=$status;
	                    }
                    	$result = $this->Common_modal->insert_batch('coupons',$coupon_array);
	                }
                if ($result) {
                    $message = array("status" => "success","message" => "Coupons added successfully.");
                }else{
                    throw new Exception("Unable to add this coupons.");
                }
            }

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function updateCouponsStatus()
    {
        try{
            $coupon_id= $this->input->post('coupon_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,90)?0:1;
            if ($ChangeStatus) {
                throw new Exception("You don't have the permissoin to change status.");
            }
            $result = $this->Common_modal->getAllWhere('coupons','cp_id',$coupon_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                $this->Common_modal->update('cp_id',$coupon_id,'coupons',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function deleteCoupons(){
        try{ 
            $coupon_id= $this->input->post('coupon_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $deleteCoupon= $this->Admin_modal->isAccessRightGiven($group_id,89)?1:0;
            if ($deleteCoupon) {
                $coupon_deleted = $this->Common_modal->delete('coupons','cp_id',$coupon_id);
                if ($coupon_deleted) {
                    $message = array("status" => "success","message" => "Coupon deleted successfully.");
                }else{
                    throw new Exception("Unable to delete this coupon.");
                }
            }else{
                throw new Exception("You don't have the permissoin to delete coupon.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function couponCodeGen($val){
        $chars = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $max = count($chars)-1;
        $limit = 8;
        $ret = '';
        if (strlen($val)<7&&strlen($val)!=0) {
            $limit = 8 - strlen($val);
            $ret = substr($val,0,5);
        }
        for($i=0;$i<$limit;$i++){
            $ret .= $chars[rand(0, $max)];
        }
        $result = $this->Common_modal->checkField('coupons','coupon_code',$ret);
        if ($result) {
            $this->couponCodeGen($val);
        }else{
            return $ret;
        }
    }
}

