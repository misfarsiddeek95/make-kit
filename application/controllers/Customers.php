<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    // require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Customers extends Admin_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model("Common_modal");
        $this->load->model("Admin_modal");
        $this->load->model("Customers_Modal");
    }

    public function index(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_cust= $this->Admin_modal->isAccessRightGiven($group_id,74)?0:1;
        try{
            if ($view_cust) {
                throw new Exception("You don't have the permissoin to view customers.");
            }
            $data['customers'] = $this->Customers_Modal->get_all_cust();
            $data['add_cust']= $this->Admin_modal->isAccessRightGiven($group_id,75)?1:0;
            $data['edit_cust']= $this->Admin_modal->isAccessRightGiven($group_id,77)?1:0;
            $data['delete_cust']= $this->Admin_modal->isAccessRightGiven($group_id,78)?1:0;
            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,76)?1:0;
            $this->load->view('customers',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function updateCustomerStatus()
    {
        try{
            $cust_id= $this->input->post('cust_id');
            $result = $this->Common_modal->getAllWhere('customers','cust_id',$cust_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $group_id = $this->session->userdata['staff_logged_in']['group_id'];
                    $ChangeStatus= $this->Admin_modal->isAccessRightGiven($group_id,76)?1:0;
                    if ($ChangeStatus) {
                        $data['status']=0;
                    }else{
                        throw new Exception("You don't have the permissoin to change status.");
                    }
                }
                $this->Common_modal->update('cust_id',$cust_id,'customers',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function deleteCustomer()
    {
        try{ 
            $cust_id= $this->input->post('cust_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_cust= $this->Admin_modal->isAccessRightGiven($group_id,78)?1:0;
            if ($delete_cust) {
                $user_deleted = $this->Common_modal->delete('customers','cust_id',$cust_id);
                if ($user_deleted) {
                    $this->Customers_Modal->deleteCustomerAddr($cust_id);
                    $data['cust_id']=0;
                    $this->Common_modal->update('cust_id',$cust_id,'orders',$data);
                    $result = $this->Customers_Modal->getCustPhotos($cust_id);
                    if ($result) {
                        foreach ($result as $row) {
                            $this->Common_modal->delete('photo','pid',$row->pid);
                            $folder = $this->folder."/photos/customers/";
                            $imgExt = array('org','big','med','std','thu','sma');

                            foreach ($imgExt as $value) {
                                $imagename = $row->photo_path.'-'.$value.'.jpg';
                                unlink( $folder . $imagename);
                            }
                        }
                    }
                    $message = array("status" => "success","message" => "Customer deleted successfully.");
                }else{
                    throw new Exception("Unable to delete this customer.");
                }
            }else{
                throw new Exception("You don't have the permissoin to delete customers.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function addCustomer(){
        try {
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_cust= $this->Admin_modal->isAccessRightGiven($group_id,75)?0:1;
            $edit_cust= $this->Admin_modal->isAccessRightGiven($group_id,77)?1:0;
            $data['type']='Add';
            if (isset($_POST['cust_id'])){
                if ($edit_cust) {
                    $data['customer']= $this->Customers_Modal->getCustDetail($this->input->post('cust_id'));
                    $data['type']='Update';
                }else{
                    throw new Exception("You don't have the permissoin to update user.");
                }
            }else if ($add_cust){
                throw new Exception("You don't have the permissoin to add user.");
            }

            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,76)?0:1;
            $data['countries'] = $this->Common_modal->getCountries();
            $this->load->view('add_customer',$data);
        } catch (Exception $ex){
            redirect(base_url());
        }
    }

    public function saveCustomer(){
        try{            
            $cust_id= $this->input->post('cust_id');
            $add_id= $this->input->post('add_id');
            $fName= $this->input->post('fName');
            $lName= $this->input->post('lName');
            $email= $this->input->post('email');
            $mobile= $this->input->post('mobile');
            $address= $this->input->post('address');
            $country= $this->input->post('country');
            $region= $this->input->post('region');
            $city= $this->input->post('city');
            $password= trim($this->input->post('password'));
            $status = isset($_POST['status']) ? $_POST['status'] : 1;
            $date = date("Y-m-d H:i:s");
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $user_array = array(
                'fname' => $fName,
                'lname' => $lName,
                'mobile' => $mobile,
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
                'phone' => $mobile
            );

            if ($password!='') {
                $user_array['password'] = $this->get_encrypted_password($password);
            }

            if ($cust_id==0&&$add_id==0) {
                $add_cust= $this->Admin_modal->isAccessRightGiven($group_id,75)?0:1;
                if ($add_cust) {
                    throw new Exception("You don't have the permissoin to add customers.");
                }else{
                    $checkuser = $this->Customers_Modal->cust_email_exist_check($email);
                    if ($checkuser) {
                        throw new Exception("Email already exist.");
                    }else{
                        $addr_array['add_type'] = 0;
                        $user_array['email'] = $email;
                        $user_array['added_date'] = $date;
                        $type = 'save';
                    }
                }
            }else if ($cust_id!=0&&$add_id!=0) {
                $edit_cust= $this->Admin_modal->isAccessRightGiven($group_id,77)?0:1;
                if ($edit_cust) {
                    throw new Exception("You don't have the permissoin to update customers.");
                }
                $type = 'update';
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
            $cust_save = $this->Customers_Modal->saveCust($cust_id,$add_id,$user_array,$addr_array);
            if ($cust_save) {
                $message = array("status" => "success","message" => $type);
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function send_Mail(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $offer = $this->input->post('msg');

        if ($group_id==1) {
            $customers = $this->Common_modal->getAll('subscription_email');
            $count = sizeof($customers);
            $result = '';
            $a = 1;
            foreach ($customers as $customer) {
                $result .= $customer->email;
                if ($count == $a) {
                    break;
                }
                $result .= ",";
                $a++;
            }

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: FANCYPOINT<info@fancypoint.com>' . "\r\n";
                $headers .= 'BCC: '.$result. "\r\n";
                $mail_temp = file_get_contents(base_url().'assets/mail/message_template.html');
                $array_replace = array (
                 '[client_name_box]' => $offer
                );
                $mail_temp = strtr($mail_temp, $array_replace);
                $status = mail(null,'FANCYPOINT - Password Reset Request',$mail_temp,$headers);
           
                if ($status) {                    
                    $message = array("status" => "success","message" => "message sent");   
                }else{
                    $message = array("status" => "error","message" => "message not sent");
                }


        }else{
            $message = array("status" => "error","message" => "No access to send");
        }
            echo json_encode($message);       
    }

    function downloadData(){
        // require 'vendor/autoload.php';

        // use PhpOffice\PhpSpreadsheet\Spreadsheet;
        // use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');

        $message = array("status" => "success","message" => "downloaded");
        echo json_encode($message);
    }
}