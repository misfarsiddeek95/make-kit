<?php

class Common_modal extends CI_Model {
    function getAll($table){
		$this->db->select('*');
		$this->db->from($table);
		$query = $this->db->get();
		return $query->result();
	}
	function insert($table,$data){
		$this->db->insert($table, $data); 
		return  $this->db->insert_id();
	}
    function insert_batch($table,$data){
        $this->db->trans_start();
        $this->db->insert_batch($table, $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
	function update($idName,$id,$table,$data){
        $this->db->trans_start();
		$this->db->where($idName, $id);
		$this->db->update($table, $data); 
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
	}
	function delete($table,$idName,$val){
        $this->db->where($idName, $val);
        $this->db->delete($table);
        if($this->db->affected_rows()>0){
            return $val;
        }else{
            return false;
        }
    }
	function getAllWhere($table,$idName,$val){
        $this->db->select('*'); 
        $this->db->from($table);
        $this->db->where($idName, $val);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function getAllWhereStr($table,$idName,$val){
        $this->db->select('*'); 
        $this->db->from($table);
        $this->db->where($idName, $val);
        $query = $this->db->get();
        return $query->result();
    }
    // new common modal function for size attribute
    function getOneWhereStr($data,$table,$idName,$val){
        $this->db->select($data); 
        $this->db->from($table);
        $this->db->where($idName, $val);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    function getCountries(){
		$this->db->select('country_id,nicename');
		$this->db->from('country');
		$this->db->order_by('nicename', "asc");
		$query = $this->db->get();
		return $query->result();
	}
    function getRegion($country){
		$this->db->select('reg_id,region_name');
		$this->db->from('regions');
	   	$this->db->where('country_id', $country);
		$this->db->order_by('region_name', "asc");
		$query = $this->db->get();
		return $query->result();
	}
	function getCities($region){
		$this->db->select('city_id,city_name');
		$this->db->from('cities');
	   	$this->db->where('reg_id', $region);
		$this->db->order_by('city_name', "asc");
		$query = $this->db->get();
		return $query->result();
	}
	function checkField($table,$id,$value){
		$this->db->select($id);
		$this->db->from($table);
	   	$this->db->where($id, $value);
	   	$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() == 1){
			return true;
		}else{
			return false;
		}
	}
	function getSingleField($table,$col,$id, $value){
        $this->db->select($col);
        $this->db->from($table);
        $this->db->where($id, $value);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function getAllCate(){
        $this -> db -> select('*'); 
        $this -> db -> from('categories');
        $this -> db -> where('parent_id', 0);
        $this -> db -> where('status', 0);
        $this->db->order_by("cate_id", "asc");
        $query = $this -> db -> get();
        $main_cats = $query -> result();

        foreach ($main_cats as $main_cat) {
            $child = $this->get_child_categories($main_cat->cate_id);
            
            $main_cat->sub_cat = $child;
        }
        return $main_cats;
    }

    function cateWithSub($tree){
        $this -> db -> select('cate_id'); 
        $this -> db -> from('categories');
        $this->db->where("tree_path REGEXP '([[:blank:][:punct:]]|^)".$tree."([[:blank:][:punct:]]|$)'");
        //$this -> db -> like('tree_path', $tree, 'before');
        $this -> db -> where('status', 0);
        $this->db->order_by("cate_id", "asc");
        return $this->db->get()->result();
    }
    
    private function get_child_categories($parent_id) {
        $this -> db -> select('*'); 
        $this -> db -> from('categories');
        $this -> db -> where('parent_id', $parent_id);
        $this -> db -> where('status', 0);
        $this->db->order_by("cate_id", "asc");
        $query = $this -> db -> get();
        $child_cats = $query -> result();
        
        foreach ($child_cats as $child_cat) {
            $child_2 = $this->get_child_categories($child_cat->cate_id);
            
            $child_cat->sub_cat = $child_2;
        }
        return $child_cats;
    }

    function getCateSubById($id){
        $this -> db -> select('cate_id'); 
        $this -> db -> from('categories');
        $this -> db -> where('parent_id', $id);
        $this -> db -> where('status', 0);
        $query = $this -> db -> get();
        $result = $query -> result();
        $cate_ids = array_map(function($o) use ($result) {return (integer)$o->cate_id;}, $result);
        //$cate_ids = array_map(create_function('$o', 'return (integer)$o->cate_id;'), $result);
        foreach ($cate_ids as $cid) {
            $child = $this->getCateSubById($cid);
            $cate_ids = array_merge($child, $cate_ids);
        }
        return $cate_ids;
    }

    function getParentsById($id){
        $this -> db -> select('cate_id,parent_id'); 
        $this -> db -> from('categories');
        $this -> db -> where('cate_id', $id);
        $this -> db -> where('status', 0);
        $query = $this -> db -> get();
        $result = $query->row();
        $cate_ids = array();
        if ($result->parent_id!=0) {
            array_push( $cate_ids,$result->parent_id);
            $parents = $this->getParentsById($result->parent_id);
            $cate_ids = array_merge ( $cate_ids, $parents);
        }
        return $cate_ids;
    }

    function getMaxId($field,$table)
    {
    	$maxid = 0;
		$row = $this->db->query('SELECT MAX('.$field.') AS maxid FROM '.$table)->row();
		if ($row) {
		    $maxid = $row->maxid; 
		}
		return $maxid;
    }

	function getTablePhotos($table,$id){
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
    
    function getImages($table,$field,$id){
        $this->db->select('*');
        $this->db->from("photo");
        $this->db->where("table", $table);
        $this->db->where("field", $field);
        $this->db->where("field_id", $id);
        $this->db->where("status", 1);
        $this->db->order_by("photo_order", "asc"); 
        $query = $this->db->get();
        return $query->result();
    }
    
    function getMaxOrder($table,$field,$id){
        $this->db->select_max('photo_order');
        $this->db->where("table", $table);
        $this->db->where("field", $field);
        $this->db->where("field_id", $id);
        $this->db->where("status", 0);
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
    function getUsersExcept($id)
    {
        $this->db->select('*');
        $this->db->from('staff_users');
        $this->db->where_not_in('user_id', $id);
        $this->db->where('status', 0);
        $query = $this->db->get();
        return $query->result();
    }
    
    function get_delivery_charge($country_id, $region_id, $city_id){
        $this -> db -> select('*');
        $this -> db -> from('delivery_charges');
        $this -> db -> where('country_id', $country_id);
        $this -> db -> where('state_id', $region_id);
        $this -> db -> where('city_id', $city_id);
        $this -> db -> limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
            $this -> db -> select('*');
            $this -> db -> from('delivery_charges');
            $this -> db -> where('country_id', $country_id);
            $this -> db -> where('state_id', $region_id);
            $this -> db -> where('all_of_state', 1);
            $this -> db -> limit(1);
            $this -> db -> limit(1);
            $query1 = $this->db->get();
            if($query1->num_rows() == 1){
                return $query1->row();
            }else{
                $this -> db -> select('*');
                $this -> db -> from('delivery_charges');
                $this -> db -> where('country_id', $country_id);
                $this -> db -> where('all_of_country', 1);
                $this -> db -> limit(1);
                $query1 = $this->db->get();
                if($query1->num_rows() == 1){
                    return $query1->row();
                }else{
                    return null;
                }
            }
        }
    }

    function getWebsites(){
        $this->db->select('*');
        $this->db->from("website");
        $this->db->where("ws_status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function insert_me($id,$arr,$table,$whereField) {
        $this->db->trans_start();

        if ($id==0) {
            $this->db->insert($table,$arr);
            $id =  $this->db->insert_id();
        }else{
            $this->db->where($whereField, $id);
            $this->db->update($table, $arr);
        }
        $this->db->trans_complete();
        return $id ; 
    }

    public function checkUsedForDelete($field,$table,$field_same,$id) {
        $this->db->select($field);
        $this->db->from($table);
        $this->db->where($field_same, $id); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
            return false;
        }else{
            return true;
        }
    }

    public function checkExistForUpdate($id,$table,$values) {
        $this->db->select($id);
        $this->db->from($table);
        $this->db->where($values);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
}

