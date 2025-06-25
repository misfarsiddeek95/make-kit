<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->clear_cache();
        $this->load->model("Admin_modal");
        $this->load->model("Settings_modal");
        $this->load->model("Common_modal");
    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }

    public function categories() {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_cat= $this->Admin_modal->isAccessRightGiven($group_id,23)?0:1;
            if ($manage_cat) {
                throw new Exception("You don't have the permissoin to manage categories.");
            }
            $data['categories']= $this->Common_modal->getAllCate();
            $data['allCates']= $this->Settings_modal->getCategories();
            $data['attributes']= $this->Settings_modal->getAllAttributes();
            $data['add_cat']= $this->Admin_modal->isAccessRightGiven($group_id,24)?1:0;
            $data['edit_cat']= $this->Admin_modal->isAccessRightGiven($group_id,25)?1:0;
            $data['delete_cat']= $this->Admin_modal->isAccessRightGiven($group_id,26)?1:0;
            $data['changeStatus']= $this->Admin_modal->isAccessRightGiven($group_id,27)?1:0;
            $data['imageUpload']= $this->Admin_modal->isAccessRightGiven($group_id,28)?1:0;
            $data['manage_attr']= $this->Admin_modal->isAccessRightGiven($group_id,38)?1:0;
            $data['assign_attr']= $this->Admin_modal->isAccessRightGiven($group_id,39)?1:0;
            $data['remove_attr']= $this->Admin_modal->isAccessRightGiven($group_id,40)?1:0;
            $data['manage_brands']= $this->Admin_modal->isAccessRightGiven($group_id,45)?1:0;
            $data['assign_brands']= $this->Admin_modal->isAccessRightGiven($group_id,46)?1:0;
            $data['remove_brands']= $this->Admin_modal->isAccessRightGiven($group_id,47)?1:0;
            $this->load->view('categories',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }
    public function deleteCategories()
    {
        try{
            $cate_id= $this->input->post('cate_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $usedCate = $this->Settings_modal->checkFieldUsed('cate_id','categories','parent_id',$cate_id);
            $usedPro = $this->Settings_modal->checkFieldUsed('cate_id','products','cate_id',$cate_id);
            $delete_cat= $this->Admin_modal->isAccessRightGiven($group_id,26)?0:1;
            if ($delete_cat) { throw new Exception("You don't have the permissoin to delete categories."); }
            if ($usedCate) { throw new Exception("Category have sub categories."); }
            if($usedPro){ throw new Exception("Category used in products."); }
            $this->Common_modal->delete('categories','cate_id',$cate_id);
            $this->Common_modal->delete('category_attributes','cate_id',$cate_id);
            $photos = $this->Common_modal->getTablePhotos('categories',$cate_id);
            if ($photos) {
                foreach ($photos as $row) {
                    $this->Common_modal->delete('photo','pid',$row->pid);
                    $folder = $this->folder."/photos/categories/";
                    $imgExt = array('org','big','med','std','thu','sma');

                    foreach ($imgExt as $value) {
                        $imagename = $row->photo_path.'-'.$value.'.jpg';
                        unlink( $folder . $imagename);
                    }
                }
            }
            $message = array("status" => "success","message" => "Category deleted successfully.");
            
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function updateCateStatus()
    {
        try{
            $cate_id= $this->input->post('cate_id');
            $result = $this->Common_modal->getAllWhere('categories','cate_id',$cate_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $group_id = $this->session->userdata['staff_logged_in']['group_id'];
                    $onStatus= $this->Admin_modal->isAccessRightGiven($group_id,27)?1:0;
                    if ($onStatus) {
                        $data['status']=0;
                    }else{
                        throw new Exception("You don't have the permissoin to change status.");
                    }
                }
                $this->Common_modal->update('cate_id',$cate_id,'categories',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function addCategory()
    {
        try{
            $cate_id= $this->input->post('cate_id');
            $allCategories= $this->input->post('allCategories');
            $category= $this->input->post('category');

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_cat= $this->Admin_modal->isAccessRightGiven($group_id,24)?0:1;
            $edit_cat= $this->Admin_modal->isAccessRightGiven($group_id,25)?0:1;
            $onStatus= $this->Admin_modal->isAccessRightGiven($group_id,27)?1:0;

            $data = array();
            $data['category']= $category;
            if ($cate_id==0) {
                if ($add_cat) {
                    throw new Exception("You don't have the permissoin to add categories.");
                }
                $cate_id = ($this->Common_modal->getMaxId('cate_id','categories'))+1;
                $data['cate_id']= $cate_id;
                if ($allCategories=="") {
                    $data['parent_id']= 0;
                    $data['tree_path']= $cate_id;
                }else{
                    $result = $this->Common_modal->getAllWhere('categories','cate_id',$allCategories);
                    $data['parent_id'] = $allCategories;
                    $data['tree_path']= $result->tree_path.'|'.$cate_id;
                }
                if ($onStatus) {
                    $data['status']=0;
                }else{
                   $data['status']=1; 
                }
                $cate_id = $this->Common_modal->insert('categories',$data);
                $message = array("status" => "success","message" => "Category added successfully.");
            }else{
                if ($edit_cat) {
                    throw new Exception("You don't have the permissoin to edit categories.");
                }
                $this->Common_modal->update('cate_id',$cate_id,'categories',$data);
                $message = array("status" => "success","message" => "Category updated successfully.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function upload_single_img(){
        if (isset($_POST['field_id'])){
            $field_id= $this->input->post('field_id');
            $result = $this->Common_modal->getAllWhere('categories','cate_id',$field_id);
            if ($result) {
                $this->load->library('aayusmain');
                $photos = $this->Common_modal->getTablePhotos('categories',$field_id);
                if ($photos) {
                    foreach ($photos as $row) {
                        $this->Common_modal->delete('photo','pid',$row->pid);
                        $folder = $this->folder."/photos/categories/";
                        $imgExt = array('org','big','med','std','thu','sma');

                        foreach ($imgExt as $value) {
                            $imagename = $row->photo_path.'-'.$value.'.jpg';
                            unlink( $folder . $imagename);
                        }
                    }
                }
                if (!empty($_FILES)) {
                    $tempFile = $_FILES['file']['tmp_name'];
                    
                    $PhotoFileType = $_FILES["file"]["type"];
                    $PhotoFileName = $_FILES["file"]["name"];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);

                    $filetype = 'jpg';
                    $folder = $this->folder."/photos/categories/";
                    $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;
                    $img_big = $folder.$PhotoFileNameMD5. '-big.'.$filetype;
                    $img_med = $folder.$PhotoFileNameMD5. '-med.'.$filetype;
                    $img_std = $folder.$PhotoFileNameMD5. '-std.'.$filetype;
                    $img_thu = $folder.$PhotoFileNameMD5. '-thu.'.$filetype;
                    $img_sma = $folder.$PhotoFileNameMD5. '-sma.'.$filetype;

                    if (!@move_uploaded_file ($_FILES['file']['tmp_name'],$img_org)) die ('Can not upload original file...');

                    $this->aayusmain->make_thumb($img_org,$img_big,100,1000,1000);
                    $this->aayusmain->make_thumb($img_org,$img_med,100,700,700);
                    $this->aayusmain->make_thumb($img_org,$img_std,100,400,400);
                    $this->aayusmain->make_thumb($img_org,$img_thu,100,100,100);
                    $this->aayusmain->make_thumb($img_org,$img_sma,100,200,200);

                    $data = array(
                        'table' => 'categories',
                        'field' => 'cate_id',
                        'field_id' => $result->cate_id,
                        'photo_path' => $PhotoFileNameMD5,
                        'photo_title' => str_replace(array("-","_",".","jpg")," ", $result->category),
                        'photo_order' => 0
                    );
                    $inserted_id = $this->Common_modal->insert('photo',$data);
                    $message = 'Photo added successfully';
                }else{
                    $this->output->set_header("HTTP/1.0 400 Bad Request");
                    $message = "File is empty.";
                }
            }else{
                $this->output->set_header("HTTP/1.0 400 Bad Request");
                $message = "Category not exist.";
            }
        }else{
            $this->output->set_header("HTTP/1.0 400 Bad Request");
            $message = "Somthing went wrong :(";
        }
        echo $message;
    }
    public function attributes() {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_attr= $this->Admin_modal->isAccessRightGiven($group_id,29)?0:1;
            if ($manage_attr) {
                throw new Exception("You don't have the permissoin to manage attributes.");
            }
            $data['attributes']= $this->Settings_modal->getAllAttributes();
            $data['add_attr']= $this->Admin_modal->isAccessRightGiven($group_id,30)?1:0;
            $data['edit_attr']= $this->Admin_modal->isAccessRightGiven($group_id,31)?1:0;
            $data['delete_attr']= $this->Admin_modal->isAccessRightGiven($group_id,32)?1:0;
            $data['manage_attr_val']= $this->Admin_modal->isAccessRightGiven($group_id,33)?1:0;
            $data['add_attr_val']= $this->Admin_modal->isAccessRightGiven($group_id,34)?1:0;
            $data['edit_attr_val']= $this->Admin_modal->isAccessRightGiven($group_id,35)?1:0;
            $data['delete_attr_val']= $this->Admin_modal->isAccessRightGiven($group_id,36)?1:0;
            $data['attr_val_status']= $this->Admin_modal->isAccessRightGiven($group_id,37)?1:0;
            $this->load->view('attributes',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }
    public function deleteAttribute()
    {
        try{
            $attr_id= $this->input->post('attr_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $usedInProduct = $this->Settings_modal->checkFieldUsed('attr_id','product_attr_val','attr_id',$attr_id);

            $delete_attr= $this->Admin_modal->isAccessRightGiven($group_id,32)?0:1;
            if ($delete_attr) { throw new Exception("You don't have the permissoin to delete attributes."); }
            if ($usedInProduct) { throw new Exception("Attribute used in products."); }
            $this->Common_modal->delete('attributes','attr_id',$attr_id);
            $this->Common_modal->delete('attribute_value','attr_id',$attr_id);
            $message = array("status" => "success","message" => "Attribute deleted successfully.");
            
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function addAttributes()
    {
        try{
            $attr_id= $this->input->post('attr_id');
            $identification_name= $this->input->post('identification_name');
            $attribute= $this->input->post('attribute');
            $attrType= $this->input->post('attrType');
            $show_to_all= $this->input->post('show_to_all');
            $price_effect= $this->input->post('price_effect');

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_attr= $this->Admin_modal->isAccessRightGiven($group_id,30)?0:1;
            $edit_attr= $this->Admin_modal->isAccessRightGiven($group_id,31)?0:1;

            $data = array(
                'identification_name' => $identification_name,
                'attribute' => $attribute,
                'type' => $attrType,
                'show_to_all' => $show_to_all,
                'price_effect' => $price_effect,
                'attr_status' => 0
            );
            if ($attr_id==0) {
                if ($add_attr) {
                    throw new Exception("You don't have the permissoin to add attributes.");
                }
                
                $this->Common_modal->insert('attributes',$data);
                $message = array("status" => "success","message" => "Attribute added successfully.");
            }else{
                if ($edit_attr) {
                    throw new Exception("You don't have the permissoin to edit attributes.");
                }
                $this->Common_modal->update('attr_id',$attr_id,'attributes',$data);
                $message = array("status" => "success","message" => "Attribute updated successfully.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function AttrValues()
    {
        $attr_id= $this->input->post('attr_id');
        $result['attr_values']= $this->Settings_modal->getAttrVal($attr_id);
        echo json_encode($result);
    }

    public function updateAttrValStatus()
    {
        try{
            $av_id= $this->input->post('av_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $changeStatus= $this->Admin_modal->isAccessRightGiven($group_id,37)?0:1;
            if ($changeStatus) {
                throw new Exception("You don't have the permissoin to change status.");
            }
            $result = $this->Common_modal->getAllWhere('attribute_value','av_id',$av_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $data['status']=0;
                }
                $this->Common_modal->update('av_id',$av_id,'attribute_value',$data);
                $message = array("status" => "success","message" => "Status updated successfully.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function deleteAttributeVal()
    {
        try{
            $av_id= $this->input->post('av_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $usedInProduct = $this->Settings_modal->checkFieldUsed('av_id','product_attr_val','av_id',$av_id);
            $delete_attr_val= $this->Admin_modal->isAccessRightGiven($group_id,36)?0:1;
            if ($delete_attr_val) { throw new Exception("You don't have the permissoin to delete attribute values."); }
            if ($usedInProduct) { throw new Exception("Attribute value used in products."); }
            $this->Common_modal->delete('attribute_value','av_id',$av_id);
            $message = array("status" => "success","message" => "Attribute value deleted successfully.");
            
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function addAttributeVal()
    {
        try{           
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_val= $this->Admin_modal->isAccessRightGiven($group_id,34)?0:1;
            $edit_val= $this->Admin_modal->isAccessRightGiven($group_id,35)?0:1;
            $statusChange= $this->Admin_modal->isAccessRightGiven($group_id,37)?1:0;

            if ($_POST['val_attr_id']==0&&!(isset($_POST['val_attr_id']))) {
                throw new Exception("Somthing went wrong, Reload and try again.");
            }

            $attr_id= $this->input->post('val_attr_id');
            $av_id= $this->input->post('attr_val_id');
            $attrVal= $this->input->post('attrVal');
            $attrValDesc= $this->input->post('attrValDesc');

            $data = array(
                'value' => $attrVal,
                'description' => $attrValDesc
            );

            if (isset($_POST['av_status'])&&$statusChange) {
                $data['status'] = $this->input->post('av_status');
            }else{
                if ($av_id==0) {
                    $data['status'] = 1;
                }
            }

            if ($av_id==0) {
                if ($add_val) {
                    throw new Exception("You don't have the permissoin to add attribute values.");
                }
                $data['attr_id'] = $attr_id;

                $this->Common_modal->insert('attribute_value',$data);
                $message = array("status" => "success","message" => "Attribute value added successfully.");
            }else{
                if ($edit_val) {
                    throw new Exception("You don't have the permissoin to edit attribute values.");
                }
                $this->Common_modal->update('av_id',$av_id,'attribute_value',$data);
                $message = array("status" => "success","message" => "Attribute value updated successfully.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }


    // add attributes from add product
    public function addAttributeVal_prod()
    {
        try{           
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_val= $this->Admin_modal->isAccessRightGiven($group_id,34)?0:1;
            $edit_val= $this->Admin_modal->isAccessRightGiven($group_id,35)?0:1;

            if ($_POST['val_attr_id']==0&&!(isset($_POST['val_attr_id']))) {
                throw new Exception("Somthing went wrong, Reload and try again.");
            }

            $attr_id= $this->input->post('val_attr_id');
            $av_id= $this->input->post('attr_val_id');
            $attrVal= $this->input->post('attrVal');
            $attrValDesc= $this->input->post('attrValDesc');

            $data = array(
                'value' => $attrVal,
                'description' => $attrValDesc,
                'status' => 0
            );

            if ($av_id==0) {
                if ($add_val) {
                    throw new Exception("You don't have the permissoin to add attribute values.");
                }
                $data['attr_id'] = $attr_id;

                $inserted_id = $this->Common_modal->insert('attribute_value',$data);
                $inserted_val = $this->Common_modal->getAllWhere('attribute_value','av_id',$inserted_id);
                $message = array("status" => "success","message" => "Attribute value added successfully.","inserted_val"=>$inserted_val);
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }



    public function cateAssignAttr() {
        $cate_id= $this->input->post('cate_id');
        $result['assign_attr']= $this->Settings_modal->getAssignAttr($cate_id);
        echo json_encode($result);
    }
    public function removeAssignAttr()
    {
        try{
            $ca_id= $this->input->post('ca_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $remove_attr= $this->Admin_modal->isAccessRightGiven($group_id,40)?0:1;
            if ($remove_attr) { throw new Exception("You don't have the permissoin to remove attributes."); }

            $result = $this->Common_modal->getAllWhere('category_attributes','ca_id',$ca_id);
            $usedInProduct = $this->Settings_modal->checkCateAttrUsed($result->cate_id,$result->attr_id);
            if ($usedInProduct) { throw new Exception("Attribute used in products."); }
            $this->Common_modal->delete('category_attributes','ca_id',$ca_id);
            $message = array("status" => "success","message" => "Attribute removed successfully.");
            
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function getAttributes()
    {
        $cate_id= $this->input->post('cate_id');
        $ids= $this->Settings_modal->getAssignAttrIds($cate_id);
        $data = array();
        foreach ($ids as $row) {
            $data[]=$row->attr_id;
        }
        $result['attributes']= $this->Settings_modal->getAttrWithoutAssign($data); 
        echo json_encode($result);
    }
    public function assignCateAttr()
    {
        try{           
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $assign_attr= $this->Admin_modal->isAccessRightGiven($group_id,39)?0:1;
            if ($assign_attr) {
                throw new Exception("You don't have the permissoin to assign attributes.");
            }
            if ($_POST['assign_cate_id']==0&&!(isset($_POST['assign_cate_id']))) {
                throw new Exception("Somthing went wrong, Reload and try again.");
            }

            $cate_id= $this->input->post('assign_cate_id');
            $attr_id= $this->input->post('assign_attr');
            $result = $this->Settings_modal->check_cate_attr_found($cate_id,$attr_id);

            //new atrribute starts here
            $getAssignAttrType = $this->Common_modal->getOneWhereStr('type','attributes','attr_id',$attr_id);
            $checkAttr = $this->Settings_modal->checkAttrType($cate_id);

            if ($getAssignAttrType->type == 1 || $getAssignAttrType->type == 2) {
                foreach ($checkAttr as $row) {
                    if ($row->type == 1 || $row->type == 2) {
                        throw new Exception("Size attribute already assigned.");
                    }
                }
            }
            //new atrribute ends here

            if ($result) {
                throw new Exception("Attribute already assigned to this category.");
            }

            $data = array(
                'cate_id' => $cate_id,
                'attr_id' => $attr_id
            );
            $this->Common_modal->insert('category_attributes',$data);
            $message = array("status" => "success","message" => "Attribute value added successfully.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function brands() {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_brands= $this->Admin_modal->isAccessRightGiven($group_id,41)?0:1;
            if ($manage_brands) {
                throw new Exception("You don't have the permissoin to manage brands.");
            }
            $data['brands']= $this->Settings_modal->getBrands();
            $data['add_brands']= $this->Admin_modal->isAccessRightGiven($group_id,42)?1:0;
            $data['edit_brands']= $this->Admin_modal->isAccessRightGiven($group_id,43)?1:0;
            $data['delete_brands']= $this->Admin_modal->isAccessRightGiven($group_id,44)?1:0;
            $this->load->view('brands',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }
    public function addBrand()
    {
        try{
            $brand_id= $this->input->post('brand_id');
            $brandName= $this->input->post('brandName');

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_brands= $this->Admin_modal->isAccessRightGiven($group_id,42)?0:1;
            $edit_brands= $this->Admin_modal->isAccessRightGiven($group_id,43)?0:1;

            $data = array(
                'brand' => $brandName
            );

            if ($brand_id==0) {
                if ($add_brands) {
                    throw new Exception("You don't have the permissoin to add brands.");
                }
                
                $inserted_id = $this->Common_modal->insert('brands',$data);
                $message = array("status" => "insert","message" => "Brand added successfully.","id" => $inserted_id);
            }else{
                if ($edit_brands) {
                    throw new Exception("You don't have the permissoin to edit brands.");
                }
                $this->Common_modal->update('brand_id',$brand_id,'brands',$data);
                $message = array("status" => "update","message" => "Brand updated successfully.","id" => $brand_id);
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function upload_brand_img(){
        if (isset($_POST['field_id'])){
            $field_id= $this->input->post('field_id');
            $result = $this->Common_modal->getAllWhere('brands','brand_id',$field_id);
            if ($result) {
                $this->load->library('aayusmain');

                $folder = $this->folder."/photos/brands/";
                if(!is_dir($folder)){
                    mkdir($folder, 0777, true);
                }

                $photos = $this->Common_modal->getTablePhotos('brands',$field_id);
                if ($photos) {
                    foreach ($photos as $row) {
                        $this->Common_modal->delete('photo','pid',$row->pid);
                        $imagename = $row->photo_path.'-org.jpg';
                        unlink( $folder . $imagename);
                    }
                }
                if (!empty($_FILES)) {
                    $tempFile = $_FILES['file']['tmp_name'];
                    
                    $PhotoFileType = $_FILES["file"]["type"];
                    $PhotoFileName = $_FILES["file"]["name"];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);
                    $extension = pathinfo($PhotoFileName, PATHINFO_EXTENSION);

                    $filetype = $extension == 'png' ? $extension : 'jpg';

                    $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;

                    if (!@move_uploaded_file ($_FILES['file']['tmp_name'],$img_org)) die ('Can not upload original file...');

                    $data = array(
                        'table' => 'brands',
                        'field' => 'brand_id',
                        'field_id' => $result->brand_id,
                        'photo_path' => $PhotoFileNameMD5,
                        'extension' => $filetype,
                        'photo_title' => str_replace(array("-","_",".","jpg")," ", $result->brand),
                        'photo_order' => 0
                    );
                    $inserted_id = $this->Common_modal->insert('photo',$data);
                    $message = 'Photo added successfully';
                }else{
                    $this->output->set_header("HTTP/1.0 400 Bad Request");
                    $message = "File is empty.";
                }
            }else{
                $this->output->set_header("HTTP/1.0 400 Bad Request");
                $message = "Brand not exist.";
            }
        }else{
            $this->output->set_header("HTTP/1.0 400 Bad Request");
            $message = "Somthing went wrong :(";
        }
        echo $message;
    }
    public function deleteBrand()
    {
        try{
            $brand_id= $this->input->post('brand_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $usedPro = $this->Settings_modal->checkFieldUsed('brand_id','products','brand_id',$brand_id);
            $delete_brand= $this->Admin_modal->isAccessRightGiven($group_id,44)?0:1;
            if ($delete_brand) { throw new Exception("You don't have the permissoin to delete categories."); }
            if($usedPro){ throw new Exception("Brand used in products."); }
            $this->Common_modal->delete('brands','brand_id',$brand_id);
            $this->Common_modal->delete('category_brands','brand_id',$brand_id);
            $photos = $this->Common_modal->getTablePhotos('brands',$brand_id);
            if ($photos) {
                $folder = $this->folder."/photos/brands/";
                foreach ($photos as $row) {
                    $this->Common_modal->delete('photo','pid',$row->pid);
                    $imagename = $row->photo_path.'-org.jpg';
                    unlink( $folder . $imagename);
                }
            }
            $message = array("status" => "success","message" => "Brand deleted successfully.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function cateAssignBrands() {
        $cate_id= $this->input->post('cate_id');
        $result['assign_brands']= $this->Settings_modal->getAssignBrands($cate_id);
        echo json_encode($result);
    }
    public function getBrands()
    {
        $cate_id= $this->input->post('cate_id');
        $ids= $this->Settings_modal->getAssignBrandIds($cate_id);
        $data = array();
        foreach ($ids as $row) {
            $data[]=$row->brand_id;
        }
        $result['brands']= $this->Settings_modal->getBrandsWithoutAssign($data); 
        echo json_encode($result);
    }

    public function assignCateBrand()
    {
        try{           
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $assign_brands= $this->Admin_modal->isAccessRightGiven($group_id,46)?0:1;
            if ($assign_brands) {
                throw new Exception("You don't have the permissoin to assign brands.");
            }
            if ($_POST['bassign_cate_id']==0&&!(isset($_POST['bassign_cate_id']))) {
                throw new Exception("Somthing went wrong, Reload and try again.");
            }

            $cate_id= $this->input->post('bassign_cate_id');
            $brand_id= $this->input->post('assign_brand');
            $result = $this->Settings_modal->check_cate_brand_found($cate_id,$brand_id);
            if ($result) {
                throw new Exception("Brand already assigned to this category.");
            }

            $data = array(
                'cate_id' => $cate_id,
                'brand_id' => $brand_id
            );
            $this->Common_modal->insert('category_brands',$data);
            $message = array("status" => "success","message" => "Brand assigned successfully.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function removeAssignBrand()
    {
        try{
            $cb_id= $this->input->post('cb_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $remove_attr= $this->Admin_modal->isAccessRightGiven($group_id,47)?0:1;
            if ($remove_attr) { throw new Exception("You don't have the permissoin to remove brands."); }

            $result = $this->Common_modal->getAllWhere('category_brands','cb_id',$cb_id);
            $usedInProduct = $this->Settings_modal->checkCateBrandUsed($result->cate_id,$result->brand_id);
            if ($usedInProduct) { throw new Exception("Brand used in products."); }
            $this->Common_modal->delete('category_brands','cb_id',$cb_id);
            $message = array("status" => "success","message" => "Brand removed successfully.");
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function delrates() {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_del= $this->Admin_modal->isAccessRightGiven($group_id,49)?0:1;
            if ($manage_del) {
                throw new Exception("You don't have the permissoin to manage delivery rates.");
            }
            $data['countries'] = $this->Common_modal->getCountries();
            $data['add_rate']= $this->Admin_modal->isAccessRightGiven($group_id,50)?1:0;
            $data['edit_rate']= $this->Admin_modal->isAccessRightGiven($group_id,51)?1:0;
            $data['delete_rate']= $this->Admin_modal->isAccessRightGiven($group_id,52)?1:0;
            $this->load->view('delrates',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function getDelCharges()
    {
        $country= $this->input->post('country');
        $region= $this->input->post('region');
        $city= $this->input->post('city');
        $limit= $this->input->post('limit');
        $offset= $this->input->post('offset');
        $result = $this->Settings_modal->get_delivery_rates($country,$region,$city,$limit,$offset);
        echo json_encode($result);
    }

    public function addDelCharges()
    {
        try{

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_rate= $this->Admin_modal->isAccessRightGiven($group_id,50)?0:1;
            $edit_rate= $this->Admin_modal->isAccessRightGiven($group_id,51)?0:1;


            $rateMId= $this->input->post('rateMId');
            $initialCharge= $this->input->post('initialCharge');
            $chargePerKG= $this->input->post('chargePerKG');
            $rate_array = array(
                'initial_charge' => str_replace(',','',$initialCharge),
                'charge_per_kg' => str_replace(',','',$chargePerKG)
            );
            if ($rateMId!=''||$rateMId!=null) {
                if ($edit_rate) {
                    throw new Exception("You don't have the permissoin to update rates.");
                }
                $this->Common_modal->update('charges_id',$rateMId,'delivery_charges',$rate_array);
                $message = array("status" => "success","message" => "Delivery rate updated successfully");
            }else{
                $fromCountry= $this->input->post('fromCountry');
                $fromCountryAll = isset($_POST['fromCountryAll']) ? 1 : 0;
                $fromRegion= $this->input->post('fromRegion');
                $fromRegionAll = isset($_POST['fromRegionAll']) ? 1 : 0;
                $fromCity= $this->input->post('fromCity');
                if ($fromCountryAll==1||$fromRegion==''||$fromRegion==null) {
                    $fromRegion = 0;
                    $fromCity = 0;
                }
                if ($fromRegionAll==1||$fromCity==''||$fromCity==null) {
                    $fromCity = 0;
                }
                $insert_array = array(
                    'country_id' => $fromCountry,
                    'all_of_country' => $fromCountryAll,
                    'state_id' => $fromRegion,
                    'all_of_state' => $fromRegionAll,
                    'city_id' => $fromCity
                );
                $delRateRes = $this->Settings_modal->check_del_charge($insert_array);  
                
                if ($delRateRes) {
                    throw new Exception("Rate already Exist, Please try to update");
                }else{
                    if ($add_rate) {
                        throw new Exception("You don't have the permissoin to add rates.");
                    }
                    $insert_array = array_merge($insert_array,$rate_array);
                    $inserted_id = $this->Common_modal->insert('delivery_charges',$insert_array);
                    $message = array("status" => "success","message" => "Delivery rate added successfully");
                }
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }
    public function deleteDelCharge()
    {
        try{
            $charges_id= $this->input->post('rate_id');
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];

            $usedInOrders = $this->Settings_modal->checkFieldUsed('delc_id','orders','delc_id',$charges_id);
            $delete_rate= $this->Admin_modal->isAccessRightGiven($group_id,52)?0:1;

            if ($delete_rate) { throw new Exception("You don't have the permissoin to delete rates."); }
            if ($usedInOrders) { throw new Exception("Rate used in orders."); }
            $result = $this->Common_modal->delete('delivery_charges','charges_id',$charges_id);
            if ($result) {
                $message = array("status" => "success","message" => "Delivery rate deleted successfully");
            }else{
                throw new Exception("Something went wrong. Please try again");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function pages() {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $manage_del= $this->Admin_modal->isAccessRightGiven($group_id,11)?0:1;
            if ($manage_del) {
                throw new Exception("You don't have the permissoin to manage pages.");
            }
            $data['pages'] = $this->Settings_modal->get_all_pages();
            $data['add_page']= $this->Admin_modal->isAccessRightGiven($group_id,54)?1:0;
            $data['edit_page']= $this->Admin_modal->isAccessRightGiven($group_id,55)?1:0;
            $data['add_photo']= $this->Admin_modal->isAccessRightGiven($group_id,56)?1:0;
            $this->load->view('pages',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function add_page(){
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_page= $this->Admin_modal->isAccessRightGiven($group_id,54)?0:1;
            $edit_page= $this->Admin_modal->isAccessRightGiven($group_id,55)?0:1;
            if (isset($_POST['page_id'])){
                if ($edit_page) {
                    throw new Exception("You don't have the permissoin to edit pages.");
                }                
                $data['page']= $this->Common_modal->getAllWhere('pages','page_id',$this->input->post('page_id'));
                $data['type']='Edit';
            }else{
                if ($add_page) {
                    throw new Exception("You don't have the permissoin to add pages.");
                }
                $data['type']='Add';
            }
            $data['page_for'] = $this->Settings_modal->get_pagefor();
            $this->load->view('add_pages',$data);

        }catch(Exception $ex){
            redirect(base_url());
        }
    } 

    public function save_page() {
        try{    
            $page_id= $this->input->post('page_id');
            $pageName= $this->input->post('pageName');
            $page_type= $this->input->post('page_type');
            $headline= $this->input->post('headline');
            $secondTitle= $this->input->post('secondTitle');
            $pageTitle= $this->input->post('pageTitle');
            $seoTitle= $this->input->post('seoTitle');
            $seoKeywords= $this->input->post('seoKeywords');
            $seoDescription= $this->input->post('seoDescription');
            $seoUrl= $this->input->post('seoUrl');
            $pageText= $this->input->post('pageText');
            $date = date("Y-m-d H:i:s");

            $page_for = $this->input->post('page_for');
            $page_new = '';
            if ($page_for == 'create_new') {
                $page_new = $this->input->post('new_page_for');
            }else{
                $page_new = $page_for;
            }

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_page= $this->Admin_modal->isAccessRightGiven($group_id,54)?0:1;
            $edit_page= $this->Admin_modal->isAccessRightGiven($group_id,55)?0:1;

            $page_array = array(
                'page_for' => $page_new,
                'name' => $pageName,
                'page_title' => $pageTitle,
                'seo_keywords' => $seoKeywords,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'seo_url' => $seoUrl,
                'headline' => $headline,
                'second_title' => $secondTitle,
                'page_text' => $pageText
            );

            if ($page_id!=0) {
                if ($edit_page) {
                    throw new Exception("You don't have the permissoin to update pages.");
                }
                $type = 'edit';
            }else{
                if ($add_page) {
                    throw new Exception("You don't have the permissoin to add pages.");
                }
                $type = 'save';
                $page_id=0;
                $page_array['page_type'] = $page_type;
                $page_array['create_date'] = $date;
                $page_array['status'] = 1;
            }
            $insert_id = $this->Settings_modal->save_page($page_id,$page_array);
            $message = array("status" => "success","message" => $type,"id" => $insert_id);

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function page_img_page() {
        $data['page_id']= $this->input->post('page_id');
        $data['pages']= $this->Common_modal->getAllWhere('pages','page_id',$data['page_id']);
        $this->load->view('add_page_img',$data);
    }

    function getSpecPageImg() {
        $id = $this->input->post('page_id');
        $result = $this->Common_modal->getImages('pages','page_id',$id);
        echo json_encode($result);
    }

    function upload_page_img(){
        if (isset($_POST['page_id'])){
            $page_id= $this->input->post('page_id');
            $headerone='';
            $headertwo='';
            $sliderdesc=''; 
            
            $result = $this->Common_modal->getAllWhere('pages','page_id',$page_id);
            $checkPhExst = $this->Settings_modal->checkPagePhoto($page_id);

            if ($result->page_type==1) {
                $headerone= $this->input->post('img_headone');
                $headertwo= $this->input->post('img_headtwo');
                $sliderdesc= $this->input->post('img_desc'); 
            }

            if ($checkPhExst && ($result->page_type==0 || $result->page_type==2)) {
                $this->output->set_header("HTTP/1.0 400 Bad Request");
                $message = "Unable to upload multiple Images.";
            }else{
                if ($result) {
                    $photo_title = $result->name;
                    $this->load->library('aayusmain');
                    if (!empty($_FILES)) {
                        $tempFile = $_FILES['file']['tmp_name'];
                        
                        $PhotoFileType = $_FILES["file"]["type"];
                        $PhotoFileName = $_FILES["file"]["name"];
                        $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);

                        $extension = pathinfo($PhotoFileName, PATHINFO_EXTENSION);

                        $folder = $this->folder."/photos/pages/";

                        if(!is_dir($folder)){
                            mkdir($folder, 0777, true);
                        }
                        
                        $filetype = $extension == 'png' ? $extension : 'jpg';

                        $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;
                        $img_std = $folder.$PhotoFileNameMD5. '-std.'.$filetype;
                        $img_sma = $folder.$PhotoFileNameMD5. '-sma.'.$filetype;

                        if (!@move_uploaded_file ($_FILES['file']['tmp_name'],$img_org)) die ('Can not upload original file...');

                        $this->aayusmain->make_thumb($img_org,$img_std,100,400,400);
                        $this->aayusmain->make_thumb($img_org,$img_sma,100,200,200);

                        $result1 = $this->Common_modal->getMaxOrder('pages','page_id',$page_id);
                        $maxo = 0;
                        if ($result1->photo_order!=0) {
                            $maxo=$result1->photo_order;
                        }
                        $data = array(
                            'table' => 'pages',
                            'field' => 'page_id',
                            'field_id' => $result->page_id,
                            'photo_path' => $PhotoFileNameMD5,
                            'extension' => $filetype,
                            'photo_title' => $photo_title,
                            'photo_order' => $maxo+1,
                            'photo_header' => $headerone,
                            'psub_header' => $headertwo,
                            'pdescription' => $sliderdesc,
                            'photo_type' => $result->page_type
                        );
                        $inserted_id = $this->Common_modal->insert('photo',$data);
                        $message = 'Photo added successfully';
                    }else{
                        $this->output->set_header("HTTP/1.0 400 Bad Request");
                        $message = "File is empty.";
                    }
                }else{
                    $this->output->set_header("HTTP/1.0 400 Bad Request");
                    $message = "Page not exist.";
                }
            } 
        }else{
            $this->output->set_header("HTTP/1.0 400 Bad Request");
            $message = "Somthing went wrong :(";
        }
        echo $message;
    }

    function deletePageImg() {
        $pid= $this->input->post('id');
        $result = $this->Common_modal->getAllWhere('photo','pid',$pid);
        if ($result) {
            $this->Common_modal->delete('photo','pid',$pid);
            $folder = $this->folder."/photos/pages/";
            $imgExt = array('org','std','sma');

            foreach ($imgExt as $value) {
                $imagename = $result->photo_path.'-'.$value.'.'.$result->extension;
                unlink( $folder . $imagename );
            }
            $message = array("status" => "success","message" => 'Deleted successfully');
        }else{
            $message = array("status" => "error","message" => 'Photo not found.');
        }
        echo json_encode($message);
    }

    function changePhotoOrder()
    {
        $ppo_ids= $this->input->post('ppo_ids');
        $myArray = explode(',', $ppo_ids);
        $this->Common_modal->updateImgOrder($myArray);
    }
}
