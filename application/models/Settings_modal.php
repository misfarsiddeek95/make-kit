<?php

class Settings_modal extends CI_Model {
    function checkFieldUsed($selField,$table,$field,$id){
        $this->db->select($selField);
        $this->db->from($table);
        $this->db->where($field, $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }

    function getCategories()
    {
        $this->db->select('q.pid,q.photo_path,q.photo_title,c.cate_id,c.parent_id,c.category,c.tree_path,c.status,c.view_count');
        $this->db->from('categories c');

        $this->db->where("q.table='categories' OR q.table is NULL AND q.status='0' OR q.status is NULL");
        $this->db->join('photo q', 'q.table="categories" AND q.field_id = c.cate_id', 'left outer');

        $this->db->order_by('c.cate_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    function getAllAttributes(){
        $this->db->select('*');
        $this->db->from('attributes');
        $this->db->where('attr_status', 0);
        $this->db->order_by('attr_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }

    function getAttrVal($attr_id)
    {
        $this->db->select('*');
        $this->db->from('attribute_value');
        $this->db->where('attr_id', $attr_id);
        $query = $this->db->get();
        return $query->result();
    }

    function getAssignAttr($cate_id)
    {
        $this->db->select('*');
        $this->db->from('category_attributes');
        $this->db->where('category_attributes.cate_id', $cate_id);
        $this->db->where('attributes.attr_status', 0);
        $this->db->join('attributes', 'attributes.attr_id = category_attributes.attr_id');
        $query = $this->db->get();
        return $query->result();
    }
    function getAssignAttrIds($cate_id)
    {
        $this->db->select('attr_id');
        $this->db->from('category_attributes');
        $this->db->where('category_attributes.cate_id', $cate_id);
        $query = $this->db->get();
        return $query->result();
    }
    function getAttrWithoutAssign($ids){
        $this->db->select('*');
        $this->db->from('attributes');
        if (!(empty($ids))) {
            $this->db->where_not_in('attr_id', $ids);
        }
        $this->db->where('attr_status', 0);
        $this->db->order_by('attr_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    function check_cate_attr_found($cate_id,$attr_id){
        $this->db->select('ca_id');
        $this->db->from('category_attributes');
        $this->db->where('cate_id', $cate_id);
        $this->db->where('attr_id', $attr_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
    function checkCateAttrUsed($cate_id,$attr_id){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('products.cate_id', $cate_id);
        $this->db->where('product_attr_val.attr_id', $attr_id);
        $this->db->join('product_attr_val', 'product_attr_val.pro_id = products.pro_id');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }

    function getBrands() {
        $this->db->select('b.brand_id,b.brand,b.view_count,b.brand_status,q.pid,q.photo_path,q.photo_title,q.extension');
        $this->db->from('brands b');
        $this->db->where("q.table='brands' OR q.table is NULL AND q.status='0' OR q.status is NULL");
        $this->db->join('photo q', 'q.table="brands" AND q.field_id = b.brand_id', 'left outer');
        $this->db->order_by('b.brand_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    
    function getAssignBrands($cate_id)
    {
        $this->db->select('*');
        $this->db->from('category_brands');
        $this->db->where('category_brands.cate_id', $cate_id);
        $this->db->where("photo.table='brands' OR photo.table is NULL AND photo.status='0' OR photo.status is NULL");
        $this->db->join('brands', 'brands.brand_id = category_brands.brand_id');
        $this->db->join('photo', 'photo.table="brands" AND photo.field_id = brands.brand_id', 'left outer');
        $query = $this->db->get();
        return $query->result();
    }
    function getAssignBrandIds($cate_id)
    {
        $this->db->select('brand_id');
        $this->db->from('category_brands');
        $this->db->where('category_brands.cate_id', $cate_id);
        $query = $this->db->get();
        return $query->result();
    }
    function getBrandsWithoutAssign($ids){
        $this->db->select('*');
        $this->db->from('brands');
        if (!(empty($ids))) {
            $this->db->where_not_in('brand_id', $ids);
        }
        $this->db->where('brand_status ', 0);
        $this->db->order_by('brand_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    function check_cate_brand_found($cate_id,$brand_id){
        $this->db->select('cb_id');
        $this->db->from('category_brands');
        $this->db->where('cate_id', $cate_id);
        $this->db->where('brand_id', $brand_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
    function checkCateBrandUsed($cate_id,$brand_id){
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('cate_id', $cate_id);
        $this->db->where('brand_id', $brand_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
    public function check_del_charge($array) {
        $this -> db -> select('*'); 
        $this -> db -> from('delivery_charges');
        $this->db->where($array);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }
    public function get_delivery_rates($country,$region,$city,$limit,$offset)
    {
        $this->db->select('dc.*,co.nicename,r.region_name,c.city_name'); 
        $this->db->from('delivery_charges dc');
        if ($country!=''||$country!=null) {
            $this->db->where('dc.country_id', $country);
        }
        if ($region!=''||$region!=null) {
            $this->db->where('dc.state_id', $region);
        }
        if ($city!=''||$city!=null) {
            $this->db->where('dc.city_id', $city);
        }        
        $this->db->join('country as co', 'co.country_id = dc.country_id', 'left');
        $this->db->join('regions as r', 'r.reg_id = dc.state_id', 'left');
        $this->db->join('cities as c', 'c.city_id = dc.city_id', 'left');
        $this->db->order_by('dc.charges_id', "desc");

        $tempdb = clone $this->db;
        $ret['rowcount'] = $tempdb->count_all_results();

        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['del_rates']=$query->result();

        return $ret;
    }

    function getPages($page_for) {
        $this->db->select('p.page_id,p.name,p.page_title,p.headline,p.second_title,p.page_type,p.create_date,q.pid,q.photo_path,q.photo_title,q.extension');
        $this->db->from('pages p');
        $this->db->where('p.status', 0);
        $this->db->where('p.page_for', $page_for);
        $this->db->join('photo q', 'q.table="pages" AND q.field_id = p.page_id', 'left outer');
        $this->db->group_by("p.page_id");
        $this->db->order_by('p.page_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    
    function save_page($page_id,$page_array){
        $this->db->trans_start();

        if ($page_id==0) {
            $this->db->insert('pages',$page_array);
            $page_id =  $this->db->insert_id();
        }else{
            $this->db->where('page_id', $page_id);
            $this->db->update('pages', $page_array);
        }
        $this->db->trans_complete();
        return $page_id; 
    }

    function getProSimpleDet()
    {
        $this->db->select('p.pro_id,p.name');
        $this->db->from('products p');
        $this->db->where('p.status', 0);
        $this->db->join('photo q', 'q.table="products" AND q.field_id = p.pro_id');
        $this->db->group_by("p.pro_id");
        $this->db->order_by('pro_id', "ASC");
        $query = $this->db->get();
        return $query->result();
    }
    // new settinga modal function for size attribute
    function checkAttrType($cate)
    {
        $this->db->select('a.type');
        $this->db->from('attributes a');
        $this->db->join('category_attributes ca', 'ca.cate_id="'.$cate.'" AND ca.attr_id = a.attr_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_pagefor() {
        $this->db->select('page_for');
        $this->db->from('pages');
        $this->db->group_by('page_for');
        $q = $this->db->get();
        return $q->result();        
    }

    public function get_all_pages() {
        $this->db->select('page_for');
        $this->db->from('pages');
        $this->db->group_by('page_for');
        $q = $this->db->get();
        $main = $q->result();        
        foreach ($main as $row) {
            $row->pages = $this->getPages($row->page_for);
        }
        return $main;
    }

    function checkPagePhoto($id){
        $this->db->select('pid');
        $this->db->from('photo');
        $this->db->where('table','pages');
        $this->db->where('field_id', $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
            return true;
        }else{
            return false;
        }
    }
}