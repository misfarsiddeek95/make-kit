<?php

class Product_modal extends CI_Model {
    function getAllUsers(){
        $this->db->select('*');
        $this->db->from('staff_users');
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->result();
    }

    function getAttr($cate_id,$pro_id,$allAttrAccess,$tree){
        $bcate_id = $cate_id;
        foreach ($tree as $key => $value) {
            $this->db->select('*');
            $this->db->from('category_brands');
            $this->db->where("cate_id", $value);
            $query = $this->db->get();
            if (0<$query->num_rows()) {
                $bcate_id = $value;
                break;
            }
        }
        $this->db->select('*');
        $this->db->from('category_brands');
        $this->db->where('category_brands.cate_id', $bcate_id);
        $this->db->join('brands', 'brands.brand_id = category_brands.brand_id');
        $query = $this->db->get();
        $ret['brands']= $query->result();

        foreach ($tree as $key => $value) {
            $this->db->select('*');
            $this->db->from('category_attributes');
            $this->db->where("cate_id", $value);
            $query = $this->db->get();
            if (0<$query->num_rows()) {
                $cate_id = $value;
                break;
            }
        }
    	$this->db->select('attributes.*');
        $this->db->from('category_attributes');
        $this->db->where('category_attributes.cate_id', $cate_id);
        if ($allAttrAccess) {
            $this->db->where('attributes.show_to_all', 0);
        }
        $this->db->join('attributes', 'attributes.attr_id = category_attributes.attr_id');
		$query = $this->db->get();
		$ret['attributes']= $query->result();

        $this->db->select('attribute_value.av_id,attribute_value.value,attribute_value.description,attributes.attr_id');
        $this->db->from('attribute_value');
        $this->db->where('attribute_value.status', 0);
        $this->db->where('category_attributes.cate_id', $cate_id);
        $this->db->join('attributes', 'attributes.attr_id = attribute_value.attr_id', 'left');
        $this->db->join('category_attributes', 'category_attributes.attr_id = attributes.attr_id', 'left');
		$query = $this->db->get();
		$ret['attribute_val']= $query->result();

        if($pro_id!=0){
            $this->db->select('product_attr_val.*,attributes.type');
            $this->db->from('product_attr_val');
            $this->db->where('product_attr_val.pro_id', $pro_id);
            $this->db->join('attributes', 'attributes.attr_id = product_attr_val.attr_id', 'left');
            $this->db->order_by("product_attr_val.attr_id", "asc"); 
            $query = $this->db->get();
            $ret['pro_attribute_val']= $query->result();

            $this->db->select('cate_id');
            $this->db->from('product_categories');
            $this->db->where('pro_id', $pro_id);
            $this->db->where('cate_type', 0);
            $this->db->order_by("pc_id", "asc"); 
            $query = $this->db->get();
            $ret['pro_cate_val']= $query->result();
        }
		return $ret;
    }

    function getInternalAttr()
    {
        $this->db->select('attributes.*');
        $this->db->from('attributes');
        $this->db->where('attr_status', 1);
        $query = $this->db->get();
        $ret['attributes']= $query->result();

        $exist_ids = array();
        if (!(empty($ret['attributes']))) {
            foreach ($ret['attributes'] as $row) {
                $exist_ids[] = $row->attr_id;
            }
            $this->db->select('attribute_value.av_id,attribute_value.attr_id,attribute_value.value,attribute_value.description');
            $this->db->from('attribute_value');
            $this->db->where_in('attribute_value.attr_id', $exist_ids);
            $this->db->where('attribute_value.status', 0);
            $query = $this->db->get();
            $ret['attribute_val']= $query->result();
        }
        return $ret;
    }
    function save_product($pro_id,$attributes,$multiAttr,$pro_array,$proCate,$other_cates,$delete){
        $this->db->trans_start();
        $return_obj = array();
        $return_obj['sub_pro_ids'] = [];
        $type = false;
        if ($pro_id==0) {
            $this->db->insert('products',$pro_array);
            $pro_id =  $this->db->insert_id();
        }else{
            $type = true;
            $this->db->where('pro_id', $pro_id);
            $this->db->update('products', $pro_array);
        }
        $return_obj['pro_id'] = $pro_id;
        $attr_array = array();
        $exist_ids = array();
        $cate_array = array();
        $exist_cate_ids = array();
        $site_array = array();
        $exist_site_ids = array();

        $cate_arr = array(
            'pro_id' => $pro_id,
            'cate_id' => $proCate,
            'cate_type' => 1
        );
        $result = $this->chechProCates($cate_arr);
        if ($result) {
            $exist_cate_ids[] = $result->pc_id;
        }else{
            $cate_array[] = $cate_arr;
        }
        if (!(empty($other_cates))) {
            foreach ($other_cates as $key => $value) {
                if ($proCate!=$value) {
                    $cate_arr = array(
                        'pro_id' => $pro_id,
                        'cate_id' => $value,
                        'cate_type' => 0
                    );
                    $result = $this->chechProCates($cate_arr);
                    if ($result) {
                        $exist_cate_ids[] = $result->pc_id;
                    }else{
                        $cate_array[] = $cate_arr;
                    }
                }
            }
        }
        if ($type) {
            $this->db->where('pro_id', $pro_id);
            if (!(empty($exist_cate_ids))) {
                $this->db->where_not_in('pc_id', $exist_cate_ids);
            }
            $this->db->delete('product_categories');
        }
        if (!(empty($cate_array))) {
            $this->db->insert_batch('product_categories', $cate_array);
        }

        if ($type) {
            $this->db->where('pro_id', $pro_id);
            if (!(empty($exist_site_ids))) {
                $this->db->where_not_in('pvs_id', $exist_site_ids);
            }
            $this->db->delete('product_available_sites');
        }
        if (!(empty($site_array))) {
            $this->db->insert_batch('product_available_sites', $site_array);
        }

        if (!(empty($attributes))) {
            foreach ($attributes as $key => $value) {
                $attr_arr = array(
                    'pro_id' => $pro_id,
                    'attr_id' => $key,
                    'av_id' => $value
                );
                if ($type) {
                    $result = $this->chechProAttrVal($attr_arr);
                    if ($result) {
                        $exist_ids[] = $result->pav_id;
                    }else{
                        $attr_array[] = $attr_arr;
                    }
                }else{
                    $attr_array[] = $attr_arr;
                }
            }
        }
        if (!(empty($multiAttr))) {
            foreach ($multiAttr as $key => $row ) {
                foreach ($row as $value) {
                    $attr_arr = array(
                        'pro_id' => $pro_id,
                        'attr_id' => $key,
                        'av_id' => $value
                    );
                    if ($type) {
                        $result = $this->chechProAttrVal($attr_arr);
                        if ($result) {
                            $exist_ids[] = $result->pav_id;
                        }else{
                            $attr_array[] = $attr_arr;
                        }
                    }else{
                        $attr_array[] = $attr_arr;
                    }
                }
            }
        }

        if ($type) {
            $this->db->select('*');
            $this->db->from('product_attr_val');
            $this->db->where('pro_id', $pro_id);
            if (!(empty($exist_ids))) {
                $this->db->where_not_in('pav_id', $exist_ids);
            }
            $query = $this->db->get();
            $not_exists_pav = $query->result();

            foreach ($not_exists_pav as $pav) {
                $q1 = "select distinct sp.sub_pro_id from sub_pro_sepc as sps left join sub_product as sp on sps.sub_pro_id = sp.sub_pro_id
                where sp.pro_id = ".$pro_id." and sps.attr_id=".$pav->attr_id." and sps. av_id = ".$pav->av_id;
                $query = $this->db->query($q1);
                // $sub_pro_ids=$query->result();
                $sub_prod_ids=$query->result();
                $sub_pro_ids = array_map(function($o) {return (integer)$o->sub_pro_id;}, $sub_prod_ids);
                // $sub_pro_ids = array_map(function($o) use ($sub_pro_ids) {return (integer)$o->sub_pro_id;}, $sub_pro_ids);
                //$sub_pro_ids= array_map(create_function('$o', 'return (integer)$o->sub_pro_id;'), $sub_pro_ids);
                $return_obj['sub_pro_ids'] = array_merge($return_obj['sub_pro_ids'], $sub_pro_ids);
                if (!(empty($sub_pro_ids))) {
                    $this->db->where_in('sub_pro_id', $sub_pro_ids);
                    $this->db->delete('sub_pro_sepc');

                    $this->db->where_in('sub_pro_id', $sub_pro_ids);
                    $this->db->delete('sub_product');
                }
            }

            $this->db->where('pro_id', $pro_id);
            if (!(empty($exist_ids))) {
                $this->db->where_not_in('pav_id', $exist_ids);
            }
            $this->db->delete('product_attr_val');
        }
        if (!(empty($attr_array))) {
            $this->db->insert_batch('product_attr_val', $attr_array);
        }
        $this->db->trans_complete();
        return $return_obj;
    }
    function chechProAttrVal($values){
        $this->db->select('pav_id');
        $this->db->from('product_attr_val');
        $this->db->where($values);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function chechProCates($values){
        $this->db->select('pc_id');
        $this->db->from('product_categories');
        $this->db->where($values);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function checkProsites($values){
        $this->db->select('pvs_id');
        $this->db->from('product_available_sites');
        $this->db->where($values);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function getProductDetails($table,$id){
        $this->db->select('*');
        $this->db->from($table);
        if ($table=='sub_product') {
            $this->db->where("sub_pro_id", $id);
        }else{
            $this->db->where("pro_id", $id);
        }
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function saveProductImg($data){
        $this->db->insert('photo',$data);
        return $this->db->insert_id();
    } 

    function getImages($id,$table){
        $this->db->select('*');
        $this->db->from("photo");
        $this->db->where("table", $table);
        $this->db->where("field", 'pro_id');
        $this->db->where("field_id", $id);
        $this->db->where("status", 1);
        $this->db->order_by("photo_order", "asc"); 
        $query = $this->db->get();
        return $query->result();
    } 

    function getMaxOrder($id){
        $this->db->select_max('photo_order');
        $this->db->where("table", 'products');
        $this->db->where("field", 'pro_id');
        $this->db->where("field_id", $id);
        $this->db->where("status", 1);
        $query = $this->db->get('photo');
        return $query->row();
    }

    function updateImgOrder($array)
    {
        for ($i=0; $i <count($array) ; $i++) {
            $this->db->set('photo_order', $i);
            $this->db->where('pid', $array[$i]);
            $this->db->update('photo');
        }
    }

    function getProducts($user,$search,$status,$category,$brand,$limit,$offset,$types)
    {
      	$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
        $this->db->select('p.pro_id,p.pro_code,p.name,p.price,p.quantity,p.weight,p.barcode,p.view_count,p.sales_count,p.status as pro_status,q.pid,q.photo_path,q.photo_title,q.extension,q.status as img_status,s.user_id,s.fname,s.lname,s.company_name,c.cate_id,c.category,(select count(distinct pav.attr_id) from product_attr_val as pav inner join attributes as atr on pav.attr_id = atr.attr_id
where pav.pro_id=p.pro_id and atr.attr_status =0 and atr.price_effect=0) as attr_count');
        $this->db->from('products p');

        if ($user!=''||$user!=null) {
            $this->db->where('p.user_id', $user);
        }
        if ($search!=''||$search!=null) {
            $this->db->like('p.name', $search);
            $this->db->or_where('p.barcode', $search);
        }
        if ($status!=''||$status!=null) {
            $this->db->where('p.status', $status);
        }
        if (!empty($category)) {
            $this->db->where_in('p.cate_id', $category);
        }
        if ($brand!=''||$brand!=null) {
            $this->db->where('p.brand_id', $brand);
        }
        $this->db->join('(select * from photo pt1 right join
            (select pt.table as tabel2,pt.field_id as field_id2,min(pt.photo_order) as photo_order2 from photo pt 
            group by pt.table,pt.field_id) as pt2 on pt1.table=pt2.tabel2 and pt1.field_id=pt2.field_id2 
            and pt1.photo_order=pt2.photo_order2 where pt1.table = "products") q', 'q.field_id = p.pro_id', 'left outer');
        //$this->db->join('photo q', 'q.table="products" AND q.field_id = p.pro_id', 'left outer');
        $this->db->join('staff_users s', 's.user_id = p.user_id');
        $this->db->join('categories c', 'c.cate_id = p.cate_id');

        $this->db->group_by("p.pro_id");
        if ($types == 1) {
            $this->db->order_by('p.quantity', "asc");
        }
        else if($types == 2){
            $this->db->order_by('p.pro_id', "desc");

        }

        $tempdb = clone $this->db;
        $ret['rowcount'] = $tempdb->count_all_results();

        /*$this->db->where("q.table='products' OR q.table is NULL AND q.status='0' OR q.status is NULL");*/
        
        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['products']=$query->result();

        return $ret;
    }

    /*function checkProAttr($pro_id)
    {
        $this->db->select('*');
        $this->db->from('product_attr_val');
        $this->db->where('product_attr_val.pro_id', $pro_id);
        $this->db->where('attributes.price_effect', 0);
        $this->db->where('attributes.attr_status', 0);
        $this->db->group_by("product_attr_val.attr_id");
        $query = $this->db->get();
        if(0<$query->num_rows()){
          return true;
        }else{
          return false;
        }
    }*/

    function getProAttr($pro_id){
        $this->db->select('attributes.*,COUNT(product_attr_val.attr_id) as total');
        $this->db->from('product_attr_val');
        $this->db->where('product_attr_val.pro_id', $pro_id);
        $this->db->where('attributes.price_effect', 0);
        $this->db->where('attributes.attr_status', 0);
        $this->db->join('attributes', 'attributes.attr_id = product_attr_val.attr_id');
        $this->db->group_by("product_attr_val.attr_id");
        $query = $this->db->get();
        $ret['attributes']= $query->result();

        $this->db->select('product_attr_val.*,attribute_value.value,attribute_value.description');
        $this->db->from('product_attr_val');
        $this->db->where('product_attr_val.pro_id', $pro_id);
        $this->db->where('attribute_value.status', 0);
        $this->db->join('attribute_value', 'attribute_value.av_id = product_attr_val.av_id', 'left');
        $query = $this->db->get();
        $ret['attribute_val']= $query->result();
        
        return $ret;
    }

    function checkSubPro($id,$attributes){
        if (!(empty($attributes))) {
            $count = count($attributes);
            // console.log($count);

            $q1 = "select distinct spc.sub_pro_id from sub_pro_sepc spc left join sub_product sp on spc.sub_pro_id = sp.sub_pro_id where  sp.pro_id=".$id.
                "  and (";

            $first = true;
            foreach ($attributes as $key => $value) {
                if(!$first){
                    $q1= $q1." or ";
                }
                $q1= $q1."(spc.attr_id=".$key." and spc.av_id=".$value.")";
                $first = false;
            }
            $q1= $q1.")";

            $query = $this->db->query($q1);
            $sub_prod_ids=$query->result();
            //my edit
            $datcount = $query->num_rows();
            $sub_pro_ids = array_map(function($o) {return (integer)$o->sub_pro_id;}, $sub_prod_ids);
            // $sub_pro_ids = array_map(function($o) use ($sub_pro_ids) {return (integer)$o->sub_pro_id;}, $sub_pro_ids);
            //$sub_prod_ids = array_map(create_function('$o', 'return (integer)$o->sub_pro_id;'), $sub_prod_ids);
            
        //     if ($count<1){
        
        //     $is_match = true;
        //     foreach ($sub_prod_ids as $sub_prod_id) {
        //         $q2 = "select * from sub_pro_sepc where sub_pro_id =".$sub_prod_id;
        //         $query2 = $this->db->query($q2);
        //         $sub_prod_spcs=$query2->result();

                
        //         foreach ($sub_prod_spcs as $specs) {
        //             if($specs->av_id != $attributes[$specs->attr_id]){
        //                 $is_match = false;
        //                 break;
        //             }
        //         }   
        //         if(!$is_match){
        //             $is_match = true;
        //         }
        //         else{
        //             return false;
        //         }
        //     }
        // }
        // else {
            if($datcount>0){
                return false;
            }
            else{
                return true;
            }
            // elseif ($datcount>0 && $datcount>$count) {
            //     return false;
            // }
        // }
        

            // if($count > $datcount){
            //     return true;
            // }

        }
        return true;
    }

    function getSubProducts($id)
    {
        $this->db->select('*');
        $this->db->from('sub_product');
        $this->db->where('pro_id', $id);
        $this->db->join('photo', 'photo.table="sub_product" AND photo.field_id = sub_product.sub_pro_id', 'left outer');
        $this->db->group_by("sub_pro_id");
        $query = $this->db->get();
        $sub_products= $query->result();
        
        foreach ($sub_products as $sub_product) {
            $q1 = "select spc.*, att.attribute, att.identification_name,att.type, av.value, av.description
                    from sub_pro_sepc as spc 
                    inner join attributes as att on spc.attr_id=att.attr_id 
                    inner join attribute_value av on spc.av_id=av.av_id
                    where spc.sub_pro_id=".$sub_product->sub_pro_id;
            $query = $this->db->query($q1);
            $sub_product->specs = $query->result();
        }
        return $sub_products;
    }

    function chechProUsed($table,$id){
        $this->db->select('pro_id');
        $this->db->from($table);
        $this->db->where("pro_id", $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
    function getProductPhotos($table,$id){
        $this->db->select('*');
        $this->db->from('photo');
        $this->db->where('table', $table);
        $this->db->where('field_id', $id);
        $query = $this->db->get();
        if(0<$query->num_rows()){
          return $query->result();
        }else{
          return false;
        }
    }

    function updateSubProducts($product_id,$sub_products){
        $updated_ids = array();
        $inserted_ids = array();

        $this->db->trans_start();
        foreach ($sub_products as  $sub_product) {
            if($sub_product['subid']!=0){
                $update_row = array(
                    "sub_name" => $sub_product['subname'],
                    "sub_pro_code" => $sub_product['subproductCod'],
                    "stock" => str_replace(",","",$sub_product['quantity']),
                    "sub_price" => str_replace(",","",$sub_product['sub_price']),
                    "sub_price_poi" => str_replace(",","",$sub_product['poi_price'])
                );

                $this->db->where('sub_pro_id', $sub_product['subid']);
                $this->db->update('sub_product', $update_row);

                array_push($updated_ids, $sub_product['subid']);
            }else{
                $insert_row = array(
                    "pro_id" => $product_id,
                    "sub_name" => $sub_product['subname'],
                    "sub_pro_code" => $sub_product['subproductCod'],
                    "stock" => str_replace(",","",$sub_product['quantity']),
                    "sub_price" => str_replace(",","",$sub_product['sub_price']),
                    "sub_price_poi" => str_replace(",","",$sub_product['poi_price'])
                );

                $this->db->insert('sub_product', $insert_row);
                $sub_pro_id = $this->db->insert_id();

                foreach ($sub_product['specs'] as $key => $value) {
                    $spec_row = array(
                        "sub_pro_id" => $sub_pro_id,
                        "attr_id" => $key,
                        "av_id" => $value
                    );

                    $this->db->insert('sub_pro_sepc', $spec_row);
                }

                array_push($inserted_ids, $sub_pro_id);
            }
        }

        $this->db->trans_complete();
    }

    function getsubproForDel($id){
        $this->db->select('*');
        $this->db->from('sub_product');
        $this->db->where('pro_id', $id);
        $query = $this->db->get();
        if(0<$query->num_rows()){
            return $query->result();
        }else{
          return false;
        }
    }

    public function deleteSubPro($id)
    {
        $this->db->trans_start();
        $this->db->where('sub_pro_id', $id);
        $this->db->delete('sub_pro_sepc');

        $this->db->where('sub_pro_id', $id);
        $this->db->delete('sub_product');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } 
        else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function pro_available_sites($id)
    {
        $this->db->select('ws_id');
        $this->db->from('product_available_sites');
        $this->db->where('pro_id', $id);
        $query = $this->db->get();
        $available = $query->result();
        $available = array_map(function($o) use ($available) {return (integer)$o->ws_id;}, $available);
        return $available;
    }
}

