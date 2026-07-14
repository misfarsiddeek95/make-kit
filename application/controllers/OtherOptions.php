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
                throw new Exception("אין לך הרשאה לצפות בשיעורים.");
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
                    throw new Exception("אין לך הרשאה להוסיף שיעורים.");
                }else{
                    $check_country = $this->Other_modal->check_rate_exist($country);
                    if ($check_country) {
                        throw new Exception("שיעור מדינה כבר קיים.");
                    }
                    $rate_array['type'] = 0;
                    $type = 'Rate added successfully';
                }
            }else if ($rate_id!=0) {
                $editRate= $this->Admin_modal->isAccessRightGiven($group_id,82)?0:1;
                if ($editRate) {
                    throw new Exception("אין לך הרשאה לעדכן שיעורים.");
                }
                $type = 'Rate updated successfully';
            }
            $rate_save = $this->Other_modal->saveRate($rate_id,$rate_array);
            if ($rate_save) {
                $message = array("status" => "success","message" => $type);
            }else{
                throw new Exception("משהו השתבש. אנא נסה שוב.");
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
                    $message = array("status" => "success","message" => "שיעור נמחק בהצלחה.");
                }else{
                    throw new Exception("לא ניתן למחוק שיעור זה.");
                }
            }else{
                throw new Exception("אין לך הרשאה למחוק שיעורים.");
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
                throw new Exception("אין לך הרשאה לשנות סטטוס.");
            }
            $result = $this->Common_modal->getAllWhere('country_currency','cc_id',$rate_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                $this->Common_modal->update('cc_id',$rate_id,'country_currency',$data);
                $message = array("status" => "success","message" => "סטטוס עודכן בהצלחה.");
            }else{
                throw new Exception("משהו השתבש. אנא נסה שוב.");
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
                throw new Exception("אין לך הרשאה לשנות סוג.");
            }
            $result = $this->Common_modal->getAllWhere('country_currency','cc_id',$rate_id);
            if ($result) {
                $this->Other_modal->updateType();
                if ($result->type==0) {
                    $data['type']=1;
                    $this->Common_modal->update('cc_id',$rate_id,'country_currency',$data);
                }
                $message = array("status" => "success","message" => "סוג עודכן בהצלחה.");
            }else{
                throw new Exception("משהו השתבש. אנא נסה שוב.");
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
                throw new Exception("אין לך הרשאה לצפות בקופונים.");
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
            $coupon_id = intval($this->input->post('coupon_id'));

            if (isset($_POST['coupf'])){
                $coupf= $this->input->post('coupf');
            }else{
                $coupf = array();
            }

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $addCoupon= $this->Admin_modal->isAccessRightGiven($group_id,87)?0:1;
            if ($addCoupon) {
                throw new Exception("אין לך הרשאה להוסיף קופונים.");
            }
            if ($coupCount<=0) {
                throw new Exception("ספירת קופונים צריכה להיות יותר מ-0");
            }
            if ($coupon_type) {
                if (100<$coupAmount) {
                    throw new Exception("אחוז סכום קופון צריך להיות עד 100");
                }
            }
            if ($coupon_id > 0) {
                $existing = $this->Common_modal->getAllWhere('coupons','cp_id',$coupon_id);
                if (!$existing) {
                    throw new Exception("קופון לא קיים.");
                }
                if (strtoupper($coupon_code) !== strtoupper($existing->coupon_code)) {
                    $coupon_code = $this->couponCodeGen($coupon_code);
                }
                $coupon_data = array(
                    'coupon_code' => $coupon_code,
                    'coupon_type' => $coupon_type,
                    'coupon_amount' => $coupAmount,
                    'valid_from' => $valid_from,
                    'valid_to' => $valid_to,
                    'count_type' => $count_type,
                    'coupon_count' => $coupCount,
                    'coupon_for' => $coupon_for,
                    'coupon_for_id' => implode(',', $coupf),
                    'status' => $status
                );
                $result = $this->Common_modal->update('cp_id',$coupon_id,'coupons',$coupon_data);
                if ($result) {
                    $message = array("status" => "success","message" => "קופון עודכן בהצלחה.","coupon_ids" => array($coupon_id));
                } else {
                    throw new Exception("לא ניתן לעדכן קופון זה.");
                }
            } else {
                $coupon_array = array();
                $coupon_ids = array();
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
                        $coupon_ids[] = $this->Common_modal->insert('coupons',$coupon_array);
                    }
                    $message = array("status" => "success","message" => "קופון נוסף בהצלחה.","coupon_ids" => $coupon_ids);
                }else{
                    foreach ($coupf as $key => $value) {
                        for ($i=0; $i < $coupCount; $i++) { 
                            $coupon_array = array(
                                'coupon_code' => $this->couponCodeGen($coupon_code),
                                'coupon_type' => $coupon_type,
                                'coupon_amount' => $coupAmount,
                                'valid_from' => $valid_from,
                                'valid_to' => $valid_to,
                                'count_type' => $count_type,
                                'coupon_count' => 1,
                                'coupon_for' => $coupon_for,
                                'coupon_for_id' => $value,
                                'create_date' => $date,
                                'status' => $status
                            );
                            $coupon_ids[] = $this->Common_modal->insert('coupons',$coupon_array);
                        }
                    }
                    if (!empty($coupon_ids)) {
                        $message = array("status" => "success","message" => "קופונים נוספו בהצלחה.","coupon_ids" => $coupon_ids);
                    }else{
                        throw new Exception("לא ניתן להוסיף קופונים אלו.");
                    }
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
                throw new Exception("אין לך הרשאה לשנות סטטוס.");
            }
            $result = $this->Common_modal->getAllWhere('coupons','cp_id',$coupon_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                $this->Common_modal->update('cp_id',$coupon_id,'coupons',$data);
                $message = array("status" => "success","message" => "סטטוס עודכן בהצלחה.");
            }else{
                throw new Exception("משהו השתבש. אנא נסה שוב.");
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
                $photos = $this->Common_modal->getTablePhotos('coupons',$coupon_id);
                if ($photos) {
                    $folder = $this->folder."/photos/coupons/";
                    foreach ($photos as $row) {
                        $this->Common_modal->delete('photo','pid',$row->pid);
                        $imagename = $row->photo_path.'-org.'.$row->extension;
                        unlink( $folder . $imagename);
                    }
                }
                $coupon_deleted = $this->Common_modal->delete('coupons','cp_id',$coupon_id);
                if ($coupon_deleted) {
                    $message = array("status" => "success","message" => "קופון נמחק בהצלחה.");
                }else{
                    throw new Exception("לא ניתן למחוק קופון זה.");
                }
            }else{
                throw new Exception("אין לך הרשאה למחוק קופון.");
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

    function upload_coupon_img(){
        try {
            $coupon_ids_str = $this->input->post('coupon_ids');
            if (empty($coupon_ids_str)) {
                throw new Exception("קופון לא קיים.");
            }
            $coupon_ids = array_filter(array_map('intval', explode(',', $coupon_ids_str)));
            if (empty($coupon_ids)) {
                throw new Exception("קופון לא קיים.");
            }
            $first_coupon = $this->Common_modal->getAllWhere('coupons','cp_id',$coupon_ids[0]);
            if (!$first_coupon) {
                throw new Exception("קופון לא קיים.");
            }
            $this->load->library('aayusmain');

            $folder = $this->folder."/photos/coupons/";
            if(!is_dir($folder)){
                mkdir($folder, 0777, true);
            }

            foreach ($coupon_ids as $cid) {
                $photos = $this->Common_modal->getTablePhotos('coupons',$cid);
                if ($photos) {
                    foreach ($photos as $row) {
                        $this->Common_modal->delete('photo','pid',$row->pid);
                        $imagename = $row->photo_path.'-org.'.$row->extension;
                        if (file_exists($folder . $imagename)) {
                            unlink($folder . $imagename);
                        }
                    }
                }
            }

            if (empty($_FILES)) {
                throw new Exception("הקובץ ריק.");
            }
            $PhotoFileName = $_FILES["file"]["name"];
            $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
            $extension = pathinfo($PhotoFileName, PATHINFO_EXTENSION);
            $filetype = $extension == 'png' ? $extension : 'jpg';
            $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;

            if (!@move_uploaded_file($_FILES['file']['tmp_name'],$img_org)) {
                throw new Exception("לא ניתן להעלות את הקובץ.");
            }

            foreach ($coupon_ids as $cid) {
                $coupon = $this->Common_modal->getAllWhere('coupons','cp_id',$cid);
                $data = array(
                    'table' => 'coupons',
                    'field' => 'cp_id',
                    'field_id' => $cid,
                    'photo_path' => $PhotoFileNameMD5,
                    'extension' => $filetype,
                    'photo_title' => $coupon ? str_replace(array("-","_",".","jpg")," ", $coupon->coupon_code) : '',
                    'photo_order' => 0
                );
                $this->Common_modal->insert('photo',$data);
            }
            $message = array("status" => "success", "message" => "תמונה נוספה בהצלחה.");
        } catch(Exception $ex) {
            $message = array("status" => "error", "message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function delete_coupon_photo(){
        try {
            $coupon_ids_str = $this->input->post('coupon_ids');
            if (empty($coupon_ids_str)) {
                throw new Exception("קופון לא קיים.");
            }
            $coupon_ids = array_filter(array_map('intval', explode(',', $coupon_ids_str)));
            if (empty($coupon_ids)) {
                throw new Exception("קופון לא קיים.");
            }
            $folder = $this->folder."/photos/coupons/";
            foreach ($coupon_ids as $cid) {
                $photos = $this->Common_modal->getTablePhotos('coupons',$cid);
                if ($photos) {
                    foreach ($photos as $row) {
                        $this->Common_modal->delete('photo','pid',$row->pid);
                        $imagename = $row->photo_path.'-org.'.$row->extension;
                        if (file_exists($folder . $imagename)) {
                            unlink($folder . $imagename);
                        }
                    }
                }
            }
            $message = array("status" => "success", "message" => "תמונה הוסרה בהצלחה.");
        } catch(Exception $ex) {
            $message = array("status" => "error", "message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
}

