<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Products extends Admin_Controller {
    public function __construct(){
        parent::__construct();
        $this->clear_cache();
        $this->load->model("Admin_modal");
        $this->load->model("Product_modal");
        $this->load->model("Common_modal");
    }
    function clear_cache(){
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }
    public function add_product(){
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $add_pro= $this->Admin_modal->isAccessRightGiven($group_id,4)?0:1;
            $update_pro= $this->Admin_modal->isAccessRightGiven($group_id,11)?0:1;
            $allAttrAccess= $this->Admin_modal->isAccessRightGiven($group_id,6)?1:0;
            if ($allAttrAccess) {
                $data = $this->Product_modal->getInternalAttr();
            }
            if (isset($_POST['product_id'])){
                if ($update_pro) {
                    throw new Exception("You don't have the permissoin to update products.");
                }                
                $data['product']= $this->Product_modal->getProductDetails('products',$this->input->post('product_id'));
                $data['pro_sites']= $this->Product_modal->pro_available_sites($this->input->post('product_id'));
                $data['type']='Update';
            }else{
                if ($add_pro) {
                    throw new Exception("You don't have the permissoin to add products.");
                }
                $data['type']='Add'; 
            }
            $data['seo_url']= $this->Admin_modal->isAccessRightGiven($group_id,5)?1:0;
            $data['seo_det']= $this->Admin_modal->isAccessRightGiven($group_id,7)?1:0;
            $data['add_other_cate']= $this->Admin_modal->isAccessRightGiven($group_id,69)?1:0;
            $data['assign_pro']= $this->Admin_modal->isAccessRightGiven($group_id,67)?0:1;
            if ($data['assign_pro']==0) {
                $user_id = $this->session->userdata['staff_logged_in']['group_id'];
                $data['Users']= $this->Common_modal->getUsersExcept($user_id);
            }
            $data['categories']= $this->Common_modal->getAllCate();
            $data['credit_types'] = $this->Common_modal->getAll('credity_type');
            $data['discount_rates'] = $this->Common_modal->getAll('discount_list');
            $this->load->view('add_product',$data);

        }catch(Exception $ex){
            redirect(base_url());
        }
    } 

    public function view_products(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_pro= $this->Admin_modal->isAccessRightGiven($group_id,9)?0:1;
        try{
            if ($view_pro) {
                throw new Exception("You don't have the permissoin to view products.");
            }
            $data['categories']= $this->Common_modal->getAllCate();
            $data['brands']= $this->Common_modal->getAll('brands');
            $data['users']= $this->Product_modal->getAllUsers();
            $data['userAc']= $this->Admin_modal->isAccessRightGiven($group_id,10)?0:1;
            $data['editAc']= $this->Admin_modal->isAccessRightGiven($group_id,11)?0:1; 
            $data['deleteAc']= $this->Admin_modal->isAccessRightGiven($group_id,12)?0:1; 
            $data['onStatus']= $this->Admin_modal->isAccessRightGiven($group_id,13)?0:1;
            $data['editSub']= $this->Admin_modal->isAccessRightGiven($group_id,48)?0:1;
            $data['type']= 2;
            //$data['attrExist'] = $this->Product_modal->checkProAttr($pro_id)?0:1;

            $this->load->view('view_products',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function view_Outproducts(){
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $view_pro= $this->Admin_modal->isAccessRightGiven($group_id,9)?0:1;
        try{
            if ($view_pro) {
                throw new Exception("You don't have the permissoin to view products.");
            }
            $data['categories']= $this->Common_modal->getAllCate();
            $data['brands']= $this->Common_modal->getAll('brands');
            $data['users']= $this->Product_modal->getAllUsers();
            $data['userAc']= $this->Admin_modal->isAccessRightGiven($group_id,10)?0:1;
            $data['editAc']= $this->Admin_modal->isAccessRightGiven($group_id,11)?0:1; 
            $data['deleteAc']= $this->Admin_modal->isAccessRightGiven($group_id,12)?0:1; 
            $data['onStatus']= $this->Admin_modal->isAccessRightGiven($group_id,13)?0:1;
            $data['editSub']= $this->Admin_modal->isAccessRightGiven($group_id,48)?0:1;
            $data['type']= 1;
            //$data['attrExist'] = $this->Product_modal->checkProAttr($pro_id)?0:1;

            $this->load->view('view_products',$data);
        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function getAttributes()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            if (isset($_POST['pro_id'])){
                $pro_id= $this->input->post('pro_id');
            }else{
                $pro_id=0;
            }
            $allAttrAccess= $this->Admin_modal->isAccessRightGiven($group_id,6)?0:1;
            $cate_id= $this->input->post('cate_id');
            $category = array();
            if ($cate_id!=''&&$cate_id!=null&&$cate_id!=0) {
                $category = $this->Common_modal->getParentsById($cate_id);
                array_push($category, $cate_id);
                rsort($category);
            }
            /*$category = $this->Common_modal->getAllWhere('categories','cate_id',$cate_id);
            $cat_path = new ArrayIterator(array_reverse(explode("|",$category->tree_path)));*/
            $result = $this->Product_modal->getAttr($cate_id,$pro_id,$allAttrAccess,$category);
        }catch(Exception $ex){
            $result = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($result);
    }

    public function save_products()
    {
        try{    
            $pro_id= $this->input->post('pro_id');
            $pro_code= $this->input->post('proCode');
            $proCate= $this->input->post('proCate');
            $brand_id= $this->input->post('brand_name');
            $proName= $this->input->post('proName');
            $proPrice= $this->input->post('proPrice');
            $proPOIPrice= $this->input->post('proPOIPrice');
            $proQty= $this->input->post('proQty');
            $proWeight= $this->input->post('proWeight');
            $barcode= $this->input->post('barcode');
            $seoTitle= $this->input->post('seoTitle');
            $seoUrl= $this->input->post('seoUrl');
            $seoKeywords= $this->input->post('seoKeywords');
            $proShortDescription= $this->input->post('proShortDescription');
            $seoDescription= $this->input->post('seoDescription');
            $proDescription= $this->input->post('proDescription');
            $proIngredients= $this->input->post('proIngredients');
            $proUse= $this->input->post('proUse');

            $proCreditType = $this->input->post('credit_type_id');
            $proMinEligiblePoints = NULL;
            if($proCreditType == 3 || $proCreditType == 2) $proMinEligiblePoints = $this->input->post('minimum_eligiblity_value');

            $date = date("Y-m-d H:i:s");
            $delete = false;

            if (isset($_POST['user_id'])&&$_POST['user_id']!=''&&$_POST['user_id']!=null){
                $user_id= $this->input->post('user_id');
            }else{
                $user_id= $this->session->userdata['staff_logged_in']['user_id'];
            }

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $status = $this->Admin_modal->isAccessRightGiven($group_id,8);
            $status1 = $status?0:1;

            if ($pro_code==''||$pro_code==null) {
                $pro_code = $this->proCodeGen();
            }
          
            if (isset($_POST['attribute'])){
                $attribute= $this->input->post('attribute');
            }else{
                $attribute = array();
            }
            if (isset($_POST['multiAttr'])){
                $multiAttr= $this->input->post('multiAttr');
            }else{
                $multiAttr = array();
            }
            if (isset($_POST['other_cates'])){
                $other_cates= $this->input->post('other_cates');
            }else{
                $other_cates = array();
            }

            if (isset($_POST['dscrates'])){
                $dscrates = $this->input->post('dscrates');
            }else{
                $dscrates = array();
            }

            if (isset($_POST['item_count'])){
                $item_count = $this->input->post('item_count');
            }else{
                $item_count = array();
            }

            $pro_array = array(
                'brand_id' => $brand_id,
                'pro_code' => $pro_code,
                'cate_id' => $proCate,
                'user_id' => $user_id,
                'name' => $proName,
                'description' => $proDescription,
                'short_description' => $proShortDescription,
                'ingredients' => $proIngredients,
                'how_to_use' => $proUse,
                'price' => str_replace(',','',$proPrice),
                'price_poi' => str_replace(',','',$proPOIPrice),
                'quantity' => $proQty,
                'weight' => $proWeight,
                'barcode' => $barcode,
                'seo_title' => $seoTitle,
                'seo_url' => $seoUrl,
                'seo_keyword' => $seoKeywords,
                'seo_description' => $seoDescription,
                'status' => $status1,
                'credit_type_id' => $proCreditType,
                'minimum_eligiblity_value' => $proMinEligiblePoints
            );

            if ($pro_id!=0) {
                $cate = $this->Common_modal->getSingleField('products','cate_id','pro_id', $pro_id);
                if ($cate->cate_id!=$proCate) {
                    $delete = true;
                }
                $type = 'update';
            }else{
                $type = 'save';
                $pro_id=0;
                $pro_array['added_date'] = $date;

                $slugBase = url_title($proName, '-', true);
                $slugUrl = $slugBase;
                $counter = 1;

                // Loop to find a unique slug
                while ($this->Common_modal->check_value_exist('products', 'slug_url', $slugUrl)) {
                    $slugUrl = $slugBase . '-' . $counter;
                    $counter++;
                }
                $pro_array['slug_url'] = $slugUrl;
            }
            $result = $this->Product_modal->save_product($pro_id,$attribute,$multiAttr,$pro_array,$proCate,$other_cates,$delete,$dscrates,$item_count);
            if (!(empty($result['sub_pro_ids']))) {
                foreach ($result['sub_pro_ids'] as $val) {
                    $photo = $this->Product_modal->getProductPhotos('sub_product',$val);
                    if ($photo) {
                        foreach ($photo as $row) {
                            $this->Common_modal->delete('photo','pid',$row->pid);
                            $folder = $this->folder."/photos/products/";
                            $imgExt = array('org','big','med','std','thu','sma');

                            foreach ($imgExt as $value) {
                                $imagename = $row->photo_path.'-'.$value.'.jpg';
                                unlink( $folder . $imagename);
                            }
                        }
                    }
                }
            }

            $message = array("status" => "success","message" => $type,"id" => $result['pro_id']);

        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    function product_img_page()
    {
        $data['product_id']= $this->input->post('product_id');
        $data['product_table']= $this->input->post('product_table');
        if ($data['product_table']=='sub_product') {
            $data['back'] = $this->Common_modal->getSingleField('sub_product','pro_id','sub_pro_id', $data['product_id']);
        }
        $this->load->view('add_product_img',$data);
    }

    function getSpecProImg()
    {
        $id = $this->input->post('product_id');
        $table = $this->input->post('product_table');
        $result = $this->Product_modal->getImages($id,$table);
        echo json_encode($result);
    }

    function upload_pro_img(){
        if (isset($_POST['product_id'])&&isset($_POST['product_table'])){
            $product_id= $this->input->post('product_id');
            $product_table= $this->input->post('product_table');
            $result = $this->Product_modal->getProductDetails($product_table,$product_id);
            if ($result) {
                $this->load->library('aayusmain');
                if (!empty($_FILES)) {
                    $tempFile = $_FILES['file']['tmp_name'];
                    
                    $PhotoFileType = $_FILES["file"]["type"];
                    $PhotoFileName = $_FILES["file"]["name"];
                    $PhotoFileNameMD5 = md5(date('YmdHis').$PhotoFileName);

                    $extension = pathinfo($PhotoFileName, PATHINFO_EXTENSION);

                    $folder = $this->folder."/photos/products/";

                    if(!is_dir($folder)){
                        mkdir($folder, 0777, true);
                    }

                    $filetype = $extension == 'png' ? $extension : 'jpg';

                    $img_org = $folder.$PhotoFileNameMD5.'-org.'.$filetype;
                    $img_big = $folder.$PhotoFileNameMD5. '-big.'.$filetype;
                    $img_med = $folder.$PhotoFileNameMD5. '-med.'.$filetype;
                    $img_std = $folder.$PhotoFileNameMD5. '-std.'.$filetype;
                    $img_thu = $folder.$PhotoFileNameMD5. '-thu.'.$filetype;
                    $img_sma = $folder.$PhotoFileNameMD5. '-sma.'.$filetype;

                    if (!@move_uploaded_file ($_FILES['file']['tmp_name'],$img_org)) die ('Can not upload original file...');

                    /* if (pathinfo($PhotoFileName, PATHINFO_EXTENSION)=='png') {
                        $image = imagecreatefrompng($img_org);
                        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                        imagealphablending($bg, TRUE);
                        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                        imagedestroy($image);
                        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file 
                        imagejpeg($bg, $img_org, $quality);
                        imagedestroy($bg);
                    } */

                    if (pathinfo($PhotoFileName, PATHINFO_EXTENSION) == 'png') {
                        $image = imagecreatefrompng($img_org);
                    
                        // Check if image has alpha channel (transparency)
                        $hasAlpha = false;
                        $width = imagesx($image);
                        $height = imagesy($image);
                        for ($x = 0; $x < $width; $x++) {
                            for ($y = 0; $y < $height; $y++) {
                                $rgba = imagecolorat($image, $x, $y);
                                $alpha = ($rgba & 0x7F000000) >> 24;
                                if ($alpha > 0) {
                                    $hasAlpha = true;
                                    break 2; // break both loops
                                }
                            }
                        }
                    
                        $bg = imagecreatetruecolor($width, $height);
                    
                        if ($hasAlpha) {
                            // Preserve transparency
                            imagealphablending($bg, false);
                            imagesavealpha($bg, true);
                            $transparent = imagecolorallocatealpha($bg, 0, 0, 0, 127);
                            imagefilledrectangle($bg, 0, 0, $width, $height, $transparent);
                        } else {
                            // Fill with white if no transparency
                            $white = imagecolorallocate($bg, 255, 255, 255);
                            imagefilledrectangle($bg, 0, 0, $width, $height, $white);
                        }
                    
                        imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);
                        imagedestroy($image);
                    
                        imagepng($bg, $img_org, 9); // Save final image
                        imagedestroy($bg);
                    }                    

                    $this->aayusmain->make_thumb($img_org,$img_big,100,1000,1000);
                    $this->aayusmain->make_thumb($img_org,$img_med,100,700,700);
                    $this->aayusmain->make_thumb($img_org,$img_std,100,400,400);
                    $this->aayusmain->make_thumb($img_org,$img_thu,100,100,100);
                    $this->aayusmain->make_thumb($img_org,$img_sma,100,200,200);

                    $result1 = $this->Product_modal->getMaxOrder($product_id);
                    $maxo = 0;
                    if ($result1->photo_order!=0) {
                        $maxo=$result1->photo_order;
                    }
                    $data = array(
                        'table' => $product_table,
                        'field' => 'pro_id',
                        'field_id' => $product_id,
                        'photo_path' => $PhotoFileNameMD5,
                        'extension' => $filetype,
                        'photo_title' => str_replace(array("-","_",".","jpg")," ", $result->name),
                        'photo_order' => $maxo+1
                    );
                    $inserted_id = $this->Product_modal->saveProductImg($data);
                    $message = 'Photo added successfully';
                }else{
                    $this->output->set_header("HTTP/1.0 400 Bad Request");
                    $message = "File is empty.";
                }
            }else{
                $this->output->set_header("HTTP/1.0 400 Bad Request");
                $message = "Product not exist.";
            }
        }else{
            $this->output->set_header("HTTP/1.0 400 Bad Request");
            $message = "Somthing went wrong :(";
        }
        echo $message;
    }

    function deleteProImg()
    {
        $pid= $this->input->post('id');
        $result = $this->Common_modal->getAllWhere('photo','pid',$pid);
        if ($result) {
            $this->Common_modal->delete('photo','pid',$pid);
            $folder = $this->folder."/photos/products/";
            $imgExt = array('org','big','med','std','thu','sma');

            foreach ($imgExt as $value) {
                $imagename = $result->photo_path.'-'.$value.'.'.$result->extension;
                unlink( $folder . $imagename );
            }
            $message = array("status" => "success","message" => 'נמחק בהצלחה');
        }else{
            $message = array("status" => "error","message" => 'תמונה לא נמצאה.');
        }
        echo json_encode($message);
    }

    function changePhotoOrder()
    {
        $ppo_ids= $this->input->post('ppo_ids');
        $myArray = explode(',', $ppo_ids);
        $this->Product_modal->updateImgOrder($myArray);
    }
    function getProducts()
    {
        $group_id = $this->session->userdata['staff_logged_in']['group_id'];
        $userLimit = $this->Admin_modal->isAccessRightGiven($group_id,10)?1:0;
        if ($userLimit) {
            $user = $this->input->post('user');
        }else{
            $user = $this->session->userdata['staff_logged_in']['user_id'];
        }
        $search = $this->input->post('search');
        $status = $this->input->post('status');
        $brand = $this->input->post('brand');
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');
        $cate_id = (int)$this->input->post('category');
        $type = (int)$this->input->post('type');
        $category = array();
        if ($cate_id!=''&&$cate_id!=null&&$cate_id!=0) {
            $category = $this->Common_modal->getCateSubById($cate_id);
            array_push($category, $cate_id);
        }

        $result = $this->Product_modal->getProducts($user,$search,$status,$category,$brand,$limit,$offset,$type);
        echo json_encode($result);
    }

    function deleteProduct()
    {
        try{
            $pro_id= $this->input->post('pro_id');

            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $delete_pro= $this->Admin_modal->isAccessRightGiven($group_id,12)?0:1;
            if ($delete_pro) {
                throw new Exception("You don't have the permissoin to delete products.");
            }

            $order = $this->Product_modal->chechProUsed('order_details',$pro_id);
            $wish = $this->Product_modal->chechProUsed('wish_list',$pro_id);
            if ($order||$wish) {
                throw new Exception("Unable to delete orderd or wish listed products.");
            }else{
                $Subs = $this->Product_modal->getsubproForDel($pro_id);
                $pro_deleted = $this->Common_modal->delete('products','pro_id',$pro_id);
                $this->Common_modal->delete('product_available_sites','pro_id',$pro_id);
                if ($pro_deleted) {
                    $this->Common_modal->delete('product_attr_val','pro_id',$pro_id);
                    $result = $this->Product_modal->getProductPhotos('products',$pro_id);
                    if ($result) {
                        foreach ($result as $row) {
                            $this->Common_modal->delete('photo','pid',$row->pid);
                            $folder = $this->folder."/photos/products/";
                            $imgExt = array('org','big','med','std','thu','sma');

                            foreach ($imgExt as $value) {
                                $imagename = $row->photo_path.'-'.$value.'.jpg';
                                unlink( $folder . $imagename);
                            }
                        }
                    }

                    if ($Subs) {
                        foreach ($Subs as $row) {
                            $pro_deleted = $this->Common_modal->delete('sub_product','sub_pro_id',$row->sub_pro_id);
                            $this->Common_modal->delete('sub_pro_sepc','sub_pro_id',$row->sub_pro_id);
                            $result2 = $this->Product_modal->getProductPhotos('sub_product',$row->sub_pro_id);
                            if ($result2) {
                                foreach ($result2 as $row1) {
                                    $this->Common_modal->delete('photo','pid',$row1->pid);
                                    $folder = $this->folder."/photos/products/";
                                    $imgExt = array('org','big','med','std','thu','sma');

                                    foreach ($imgExt as $value) {
                                        $imagename = $row1->photo_path.'-'.$value.'.jpg';
                                        unlink( $folder . $imagename);
                                    }
                                }
                            }
                        }
                    }              

                    $message = array("status" => "success","message" => "מוצר נמחק בהצלחה.");
                }else{
                    throw new Exception("Unable to delete the product.");
                }
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function updateProStatus()
    {
        try{
            $pro_id= $this->input->post('pro_id');
            $result = $this->Common_modal->getAllWhere('products','pro_id',$pro_id);
            if ($result) {
                if ($result->status==0) {
                    $data['status']=1;
                }else{
                    $group_id = $this->session->userdata['staff_logged_in']['group_id'];
                    $onStatus= $this->Admin_modal->isAccessRightGiven($group_id,13)?1:0;
                    if ($onStatus) {
                        $data['status']=0;
                    }else{
                        throw new Exception("You don't have the permissoin to change status.");
                    }
                }
                $this->Common_modal->update('pro_id',$pro_id,'products',$data);
                $message = array("status" => "success","message" => "סטטוס עודכן בהצלחה.");
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
        }catch(Exception $ex){
            $message = array("status" => "error","message" => $ex->getMessage());
        }
        echo json_encode($message);
    }

    public function edit_sub_products()
    {
        try{
            $group_id = $this->session->userdata['staff_logged_in']['group_id'];
            $editSub= $this->Admin_modal->isAccessRightGiven($group_id,48)?0:1;
            if (isset($_POST['product_id'])){
                if ($editSub) {
                    throw new Exception("You don't have the permissoin to edit sub products.");
                }
                $data['pro_id'] = $this->input->post('product_id');
                $data['product']= $this->Product_modal->getProductDetails('products',$data['pro_id']);
                $data['subPros']= $this->Product_modal->getSubProducts($data['pro_id']);
            }else{
                throw new Exception("Something went wrong. Please try again.");
            }
            $this->load->view('edit_sub_products',$data);

        }catch(Exception $ex){
            redirect(base_url());
        }
    }

    public function getProAttr()
    {
        $pro_id= $this->input->post('pro_id');
        $result= $this->Product_modal->getProAttr($pro_id);
        echo json_encode($result);
    }

    public function checkSubPro()
    {
        try{
            $attribute= $this->input->post('attribute');
            $pro_id= $this->input->post('pro_id');
            $status = $this->Product_modal->checkSubPro($pro_id,$attribute);

            $resp = array();
            if($status){
                $resp = array("success"=>"no sub product exists.");
            }else{
                $resp = array("error"=>"Sub Product Exists.");
            }

            echo json_encode($resp);
        }catch(Exception $ex){
            echo json_encode(array("error"=>$ex->getMessage()));
        }
    }

    public function proCodeGen(){
        $chars = array(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $serial = $this->session->userdata['staff_logged_in']['user_id'];
        $max = count($chars)-1;
        for($i=0;$i<10;$i++){
            $serial .= $chars[rand(0, $max)];
        }
        $result = $this->Common_modal->checkField('products','pro_code',$serial);
        if ($result) {
            $this->proCodeGen();
        }else{
            return $serial;
        }
    }

    public function updateSubProducts()
    {
        try{
            $subproduct= $this->input->post('subproduct');
            $pro_id= $this->input->post('pro_id');

            $this->Product_modal->updateSubProducts($pro_id,$subproduct);

            $resp = array("success"=>"Sub Products updated successfully.");
            echo json_encode($resp);
        }catch(Exception $ex){
            echo json_encode(array("error"=>$ex->getMessage()));
        }
    }

    public function deleteSubProduct()
    {
        $id= $this->input->post('id');

        /*$order = $this->Common_modal->checkField('order_details','sub_pro_id',$id);
        if ($order) {
            $message = array("status" => "error","message" => "Unable to delete orderd products.");
        }else{
            
        }*/
        $pro_deleted = $this->Product_modal->deleteSubPro($id);
        if ($pro_deleted) {
            $result = $this->Product_modal->getProductPhotos('sub_product',$id);
            if ($result) {
                foreach ($result as $row) {
                    $this->Common_modal->delete('photo','pid',$row->pid);
                    $folder = $this->folder."/photos/products/";
                    $imgExt = array('org','big','med','std','thu','sma');

                    foreach ($imgExt as $value) {
                        $imagename = $row->photo_path.'-'.$value.'.jpg';
                        unlink( $folder . $imagename);
                    }
                }
            }
            $message = array("status" => "success","message" => "תת-מוצר נמחק בהצלחה.");
        }else{
            $message = array("status" => "error","message" => "לא ניתן למחוק את המוצר, אנא נסה שוב.");
        }
        echo json_encode($message);
    }

    public function convertToArray()
    {
        $subproduct= $this->input->post('subproduct');
        echo json_encode($subproduct) ;
    }
    public function updateProQtyPrice(){
        $id= $this->input->post('pro_id');
        $data['quantity']= $this->input->post('qty');
        $data['price']= $this->input->post('price');
        $result = $this->Common_modal->update('pro_id',$id,'products',$data);
        echo json_encode($result);
    }

    // NOT USED. IF NEEDED THEN WILL USE
    // upload products from csv
    public function productRun() {
        // Define the path to the processed CSV file
        $file_path = FCPATH . 'uploads/organized_products_for_import.csv';

        if (!file_exists($file_path)) {
            echo "Error: The file organized_products_for_import.csv was not found in the /uploads/ directory.";
            return;
        }

        if (($handle = fopen($file_path, "r")) !== FALSE) {
            // Skip the header row of the CSV
            fgetcsv($handle);

            $import_data = [];
            
            // Loop through the data rows
            while (($row = fgetcsv($handle)) !== FALSE) {
                // Map CSV columns from the Python script's output to the database fields
                // This order must exactly match the output of product_organizer.py
                $import_data[] = array(
                    'pro_code'                  => !empty($row[0]) ? $row[0] : NULL,
                    'user_id'                   => $row[1],
                    'cate_id'                   => $row[2],
                    'brand_id'                  => $row[3],
                    'name'                      => $row[4],
                    'description'               => !empty($row[5]) ? $row[5] : NULL,
                    'short_description'         => !empty($row[6]) ? $row[6] : NULL,
                    'ingredients'               => !empty($row[7]) ? $row[7] : NULL,
                    'how_to_use'                => !empty($row[8]) ? $row[8] : NULL,
                    'price'                     => $row[9],
                    'price_poi'                 => $row[10],
                    'quantity'                  => $row[11],
                    'weight'                    => $row[12],
                    'barcode'                   => !empty($row[13]) ? $row[13] : NULL,
                    'slug_url'                  => !empty($row[14]) ? $row[14] : NULL,
                    'seo_title'                 => !empty($row[15]) ? $row[15] : NULL,
                    'seo_url'                   => !empty($row[16]) ? $row[16] : NULL,
                    'seo_keyword'               => !empty($row[17]) ? $row[17] : NULL,
                    'seo_description'           => !empty($row[18]) ? $row[18] : NULL,
                    'view_count'                => $row[19],
                    'sales_count'               => $row[20],
                    'status'                    => $row[21],
                    'credit_type_id'            => $row[22],
                    'minimum_eligiblity_value'  => !empty($row[23]) ? $row[23] : NULL
                );
            }
            fclose($handle);

            // Insert data in batches using the model
            if (!empty($import_data)) {
                $result = $this->Common_modal->insert_batch('products',$import_data);
                if($result){
                    $rowCount = count($import_data);
                    echo "Success! " . $rowCount . " product records were imported successfully.";
                } else {
                    echo "Error: Database import failed. Please check your database logs.";
                }
            } else {
                echo "No data to import. The CSV file might be empty or contain only a header.";
            }
        } else {
            echo "Error: Could not open the CSV file.";
        }
    }

    public function generate_slugs() {
        echo "Starting slug generation process...<br>";
        
        // 1. Get all products (ID and Name)
        $products = $this->Common_modal->get_all_products_for_slugs();

        if (empty($products)) {
            echo "No products found to update.";
            return;
        }

        // 2. OPTIMIZATION: Get all existing slugs into an array once to avoid N+1 queries.
        $existing_slugs = $this->Common_modal->get_all_slugs();

        $update_data = [];
        $products_to_update_count = 0;

        // 3. Loop through products in PHP to generate slugs
        foreach ($products as $product) {
            // Use a fallback if the name is empty or results in an empty slug
            if (empty($product->name)) {
                $slugBase = 'product-' . $product->pro_id;
            } else {
                $slugBase = url_title($product->name, '-', true);
            }

            $slugUrl = $slugBase;
            $counter = 1;

            // Loop in memory (fast) instead of querying the DB
            while (in_array($slugUrl, $existing_slugs)) {
                $slugUrl = $slugBase . '-' . $counter;
                $counter++;
            }

            // Prepare the data for batch update
            $update_data[] = [
                'pro_id'   => $product->pro_id,
                'slug_url' => $slugUrl
            ];
            
            // Add the newly created slug to our lookup array to prevent duplicates within this same run
            $existing_slugs[] = $slugUrl;
            $products_to_update_count++;
        }

        // 4. Perform a single batch update query for maximum efficiency
        if (!empty($update_data)) {
            $this->Common_modal->batch_update_slugs($update_data);
            echo "Process complete! " . $products_to_update_count . " product slugs have been generated and updated.";
        } else {
            echo "No products needed an update.";
        }
    }
}