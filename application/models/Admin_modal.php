<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Admin_modal extends CI_Model {
    public function get_all_users() {
        $this -> db -> select('s.user_id,s.fname,s.lname,s.company_name,s.nic,s.dob,s.email,s.username,s.status as userStatus,a.*,p.pid,p.photo_path,p.photo_title,d.phone'); 
        $this -> db -> from('staff_users s');
        $this->db->where('d.add_type', 2);
        $this->db->join('photo p', 'p.table="staff_users" AND p.field_id = s.user_id', 'left outer');
        $this->db->join('access_groups a', 'a.group_id = s.access_group', 'left outer');
        $this->db->join('addresses d', 'd.user_id = s.user_id');
        $this->db->join('cities i', 'i.city_id = d.city_id');
        $this->db->join('regions r', 'r.reg_id = d.reg_id');
        $query = $this -> db -> get();
        $results=$query -> result();
        return $results;
    }

    public function getUserDetail($id) {
        $this -> db -> select('staff_users.*,addresses.add_id,addresses.address,addresses.city_id,addresses.reg_id,addresses.country_id,addresses.phone');
        $this -> db -> from('staff_users');
        $this->db->where('staff_users.user_id', $id);
        $this->db->where('addresses.add_type', 2);
        $this->db->join('addresses', 'addresses.user_id = staff_users.user_id');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function saveUser($user_id,$add_id,$visibleSites,$user_array,$addr_array){
        $this->db->trans_start();

        $site_array = array();
        $exist_site_ids = array();

        if ($user_id==0) {
            $this->db->insert('staff_users',$user_array);
            $user_id =  $this->db->insert_id();

            $addr_array['user_id'] = $user_id;
            $this->db->insert('addresses',$addr_array);
        }else{
            $this->db->where('user_id', $user_id);
            $this->db->update('staff_users', $user_array);

            $this->db->where('add_id', $add_id);
            $this->db->update('addresses', $addr_array);
        }

        if (!(empty($visibleSites))) {
            foreach ($visibleSites as $key => $value) {
                $site_arr = array(
                    'user_id' => $user_id,
                    'ws_id' => $value
                );
                $result = $this->checkStaffsites($site_arr);
                if ($result) {
                    $exist_site_ids[] = $result->ss_id;
                }else{
                    $site_array[] = $site_arr;
                }
            }
        }

        $this->db->where('user_id', $user_id);
        if (!(empty($exist_site_ids))) {
            $this->db->where_not_in('ss_id', $exist_site_ids);
        }
        $this->db->delete('staff_sites');

        if (!(empty($site_array))) {
            $this->db->insert_batch('staff_sites', $site_array);
        }

        $this->db->trans_complete();
    }

    function checkStaffsites($values){
        $this->db->select('ss_id');
        $this->db->from('staff_sites');
        $this->db->where($values);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    public function staff_available_sites($id)
    {
        $this->db->select('ws_id');
        $this->db->from('staff_sites');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        $available = $query->result();
        $available = array_map(function($o) use ($available) {return (integer)$o->ws_id;}, $available);
        return $available;
    }

    function chechUserUsed($table,$id){
        $this->db->select('user_id');
        $this->db->from($table);
        $this->db->where("user_id", $id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }

    function getUserPhotos($id){
        $this->db->select('*');
        $this->db->from('photo');
        $this->db->where('table', 'staff_users');
        $this->db->where('field_id', $id);
        $query = $this->db->get();
        if(0<$query->num_rows()){
          return $query->result();
        }else{
          return false;
        }
    }

    function getUserCount()
    {
        $this->db->select('user_id');
        $this->db->from('staff_users');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function deleteUserAddr($id){
        $this->db->where('user_id', $id);
        $this->db->where('add_type', 2);
        $this->db->delete('addresses');
        if($this->db->affected_rows()>0){
            return true;
        }else{
            return false;
        }
    }

    public function get_all_customers() {
        $this->db->select('*'); 
        $this->db->from('customers');
        $this->db->where("(address_type='Business' OR address_type='Personal')");
        $this->db->join('cust_address', 'cust_address.cust_id = customers.cust_id');
        $this->db->join('district', 'district.did = cust_address.district_id');
        $this->db->join('city', 'city.cid = cust_address.city_id');
        $query = $this->db->get();
        $results=$query -> result();
        return $results;
    }

    public function get_single_customer($cust_id) {
        $this->db->select('*'); 
        $this->db->from('customers');
        $this->db->where('customers.cust_id', $cust_id);
        $this->db->where("(address_type='Business' OR address_type='Personal')");
        $this->db->join('cust_address', 'cust_address.cust_id = customers.cust_id');
        $this->db->join('district', 'district.did = cust_address.district_id');
        $this->db->join('city', 'city.cid = cust_address.city_id');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        return $query->row();
    }


    public function getnewuser()
    {
        $q = "select * from customers WHERE YEAR(added_date) = ".date('Y')." AND MONTH(added_date) = ".date('m');
        $query = $this->db->query($q);
        return $query->num_rows();
    }
 
    public function newUserList()
    {
        $q = "select count(cust_id) as custlist FROM customers WHERE added_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() group by DATE(added_date)";
        $query = $this->db->query($q);
        return $query->result();
    }
    
    public function totalUserCount()
    {
        $q = "select * from customers";
        $query = $this->db->query($q);
        return $query->num_rows();
    }

    public function getnewProducts()
    {
        $q = "select * from products WHERE YEAR(added_date) = ".date('Y')." AND MONTH(added_date) = ".date('m');
        $query = $this->db->query($q);
        return $query->num_rows();
    }
    
    public function productprivuse()
    {
        $q = "select count(pro_id) as procount FROM products WHERE added_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE() group by DATE(added_date)";
        $query = $this->db->query($q);
        return $query->result();
    }

    public function totalProList()
    {
        $q = "select * from products";
        $query = $this->db->query($q);
        return $query->num_rows();
    }

    public function totalSuccessOrders()
    {
        $q = "select * from orders where payment_status=2";
        $query = $this->db->query($q);
        return $query->num_rows();
    }

    public function outOfStocks()
    {
        $q = "select * from products where quantity<2";
        $query = $this->db->query($q);
        return $query->num_rows();
    }

    public function getnewOrders()
    {
        $this->db->select('*'); 
        $this->db->from('orders');
        $this->db->where("YEAR(order_date) = ".date('Y')." AND MONTH(order_date) = ".date('m'));
        $this->db->where('payment_status', 2);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function previousOrders()
    {
        $this->db->select('count(order_id) as ordercount'); 
        $this->db->from('orders');
        $this->db->where("order_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()");
        $this->db->where('payment_status', 2);
        $this->db->group_by('DATE(order_date)');
        $query = $this->db->get();
        return $query->result();
    }
    /*public function get_sepcific_orders($branch,$group_id) {
        $this -> db -> select('*'); 
        $this -> db -> from('delivery_orders');
        if ($group_id=='3') {
            $status = array(1,2,6,9);
            $this -> db -> where('cust_address.district_id', $branch);
            $this->db->where_not_in('current_status', $status);
        }else if($group_id=='4'){
            $status = array(1,4,5,6,7,8,9 );
            $this->db->where_not_in('current_status', $status);
        }
        $this->db->join('cust_address', 'cust_address.address_id = delivery_orders.address_id');
        $this->db->join('district', 'district.did = cust_address.district_id');
        $this->db->join('city', 'city.cid = cust_address.city_id');
        $this->db->join('order_status', 'order_status.os_id = delivery_orders.current_status');

        $query = $this -> db -> get();
        $results=$query -> result();
        return $results;
    }*/

    function get_sepcific_orders($search,$branch,$group_id,$limit,$offset,$user_id){
        $q = "select do.oid,do.waybill_id as 'waybillid',c.cust_name as 'custname',cad.name as 'resname', 
        cad.address as 'resaddress',d.dname as 'disname',ct.cname as 'cityname', 
        su.staff_name as 'dispatchto', d2.dname as 'dt_dist',
        cad.phone as 'resphone',
        COALESCE(do.cod,0) as 'codamount', COALESCE(ccp.ccp_cod_paid,0) as 'codpaid',os.status as 'ostatus', os.os_id as 'ostatusid',
        do.date as 'placedate', do.weight, do.delivery_charge, do.dispatch_to from delivery_orders do  
        left join delivered_order_payments dop on do.oid = dop.oid
        left join cust_cod_payments ccp on dop.ccp_id = ccp.ccp_id 
        left join cust_delivery_charges cdc on dop.dc_id=cdc.dc_id
        left join order_status os on do.current_status=os.os_id
        left join customers c on do.cust_id=c.cust_id
        left join cust_address cad on do.address_id = cad.address_id
        left join district as d on cad.district_id=d.did
        left join staff_users as su on do.dispatch_to=su.user_id
        left join district as d2 on su.branch_id=d2.did
        left join city as ct on cad.city_id=ct.cid ";

        $has_cond = ($search=='')?false:true;
        if ($group_id=='3') {
            //$status = array(1,2,6,9);
            if($has_cond){
                $search=$search.' and ';
            }
            $search = $search .' do.current_status in (3,4,5,6,7,8,9) ';
            //$this->db->where_not_in('current_status', $status);
        }else if($group_id=='4'){
            //$status = array(1,4,5,6,7,8,9);
            if($has_cond){
                $search=$search.' and ';
            }
            $search = $search .'(do.current_status in (1,2,3))';
            //$this->db->where_not_in('current_status', $status);
        }else if($group_id=='5'){
            //$status = array(1,4,5,6,7,8,9);
            if($has_cond){
                $search=$search.' and ';
            }
            $search = $search .'do.dispatch_to in (select distinct aa.agent_id from assign_agent aa where aa.staff_id='.$user_id.')';
            $search = $search .' and do.current_status in (3,4,5,6,7,8,9)';
            //$this->db->where_not_in('current_status', $status);
        }

        if($search!=''){
            $search = ' where '.$search;
        }

        //echo($q.$search);
        $query = $this->db->query($q.$search." LIMIT ".$limit." OFFSET ".$offset);
        $ret['cust_orders']=$query->result();


        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();
        
        return $ret;
    }

    public function get_status($group_id) {
        $this -> db -> select('*'); 
        $this -> db -> from('order_status');
        if ($group_id=='3'||$group_id=='5') {
            $types = array(3,4,5,6,7,8,9);
        }else if($group_id=='4'){
            $types = array(1,2,3);
        }else{
            $types = array(1,2,3,4,5,6,7,8,9);
        }
        $this->db->where_in('os_id', $types);
        $query = $this -> db -> get();
        $results=$query -> result();
        return $results;
    }
    
    function user_name_exist_check($userName){
        $this -> db -> select('*');
        $this -> db -> from('staff_users');
        $this -> db -> where('username', $userName);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $rowCount=$query -> num_rows();
        if($rowCount == 1){
            return true;
        }else{
            return false;
        }
    }
    
    function get_user_by_id($user_id){
        $this -> db -> select('*');
        $this -> db -> from('staff_users');
        $this -> db -> where('user_id', $user_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        return $query->row();
        
     }
     
    public function update_user($user_id,$user) {
        $this->db->where('user_id', $user_id);
        $this->db->update('staff_users', $user);
    }

    public function get_order_status($orderid,$status) {
        $this -> db -> select('*'); 
        $this -> db -> from('delivery_status');
        $this->db->where('order_id', $orderid);
        $this->db->where('status_id', $status);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          return $query->row();
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

    public function check_del_charge_with_id($id,$array) {
        $this -> db -> select('*'); 
        $this -> db -> where('dc_id!=', $id);
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

    public function get_delivery_rates($rateType,$fromDis,$toDis,$limit,$offset)
    {
        $this->db->select('delivery_charges.*,d1.did as fromDisId,d1.dname as fromDisName,d2.did as toDisId,d2.dname as toDisName,c1.cid as fromCityId,c1.cname as fromCityName,c2.cid as toCityId,c2.cname as toCityName'); 
        $this->db->from('delivery_charges');
        $this->db->where('dc_type', $rateType);
        if ($fromDis!=''||$fromDis!=null) {
            $this->db->where('from_district_id', $fromDis);
        }
        if ($toDis!=''||$toDis!=null) {
            $this->db->where('to_district_id', $toDis);
        }
        
        $this->db->join('district as d1', 'd1.did = delivery_charges.from_district_id');
        $this->db->join('district as d2', 'd2.did = delivery_charges.to_district_id');
        $this->db->join('city as c1', 'c1.cid = delivery_charges.from_city_id', 'left');
        $this->db->join('city as c2', 'c2.cid = delivery_charges.to_city_id', 'left');
        $this->db->order_by('dc_id', "desc");
        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['del_rates']=$query->result();

        $this->db->select('*'); 
        $this->db->from('delivery_charges');
        $this->db->where('dc_type', $rateType);
        if ($fromDis!=''||$fromDis!=null) {
            $this->db->where('from_district_id', $fromDis);
        }
        if ($toDis!=''||$toDis!=null) {
            $this->db->where('to_district_id', $toDis);
        }
        $query1 = $this->db->get();
        $ret['rowcount'] = $query1->num_rows();
        return $ret;
    }

    public function get_customer_cod_details($search,$limit,$offset)
    {
        if ($search!=''||$search!=null) {
            $searchqr= "where c.cust_name like '".$search."%'";
        }else{
            $searchqr="";
        }
        $q = "select c.cust_id,c.cust_name,COALESCE(cod.tot_ccp_payable,0) as 'tot_ccp_payable', 
COALESCE(cod.tot_ccp_paid,0) as 'tot_ccp_paid', COALESCE(dcr.tot_cdc_receivable,0) as 'tot_cdc_receivable', 
COALESCE(dcr.tot_cdc_recovered ,0) as 'tot_cdc_recovered' from customers as c left join (select dop.cust_id as 'dop_cust_id',sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id where ccp.ccp_status=0 group by dop.cust_id) as cod on c.cust_id=cod.dop_cust_id left join (select dop2.cust_id as 'dc_cust_id',sum(cdc.cdc_charge) as 'tot_cdc_receivable',sum(cdc.cdc_charge_rcvd) as 'tot_cdc_recovered' from delivered_order_payments dop2 left join cust_delivery_charges cdc on dop2.dc_id= cdc.dc_id where cdc.cdc_status=0 group by dop2.cust_id) as dcr on c.cust_id=dcr.dc_cust_id ".$searchqr." LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->query($q);
        $ret['cust_rates']=$query->result();

        $q = "select c.cust_id,c.cust_name,cod.tot_ccp_payable, cod.tot_ccp_paid, dcr.tot_cdc_receivable from customers as c left join (select dop.cust_id as 'dop_cust_id',sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id where ccp.ccp_status=0 group by dop.cust_id) as cod on c.cust_id=cod.dop_cust_id left join (select dop2.cust_id as 'dc_cust_id',sum(cdc.cdc_charge) as 'tot_cdc_receivable' from delivered_order_payments dop2 left join cust_delivery_charges cdc on dop2.dc_id= cdc.dc_id where cdc.cdc_status=0 group by dop2.cust_id) as dcr on c.cust_id=dcr.dc_cust_id ".$searchqr;
        $query1 = $this->db->query($q);
        $ret['rowcount'] = $query1->num_rows();
        return $ret;
    }

    public function get_spec_customer_cod($cust_id)
    {
        $q = "select dop.oid,ccp.ccp_cod, ccp.ccp_cod_paid,cdc.cdc_charge,cdc.cdc_charge_rcvd,os.status,do.waybill_id,do.date,do.weight from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id = ccp.ccp_id left join cust_delivery_charges cdc on dop.dc_id=cdc.dc_id left join delivery_orders do on dop.oid=do.oid left join order_status os on do.current_status=os.os_id where ((ccp.ccp_cod - ccp.ccp_cod_paid)>0 or (cdc.cdc_charge - cdc.cdc_charge_rcvd)>0)and dop.cust_id=".$cust_id;
        $query = $this->db->query($q);
        return $query->result();
    }

    public function payment_cust_cod($main, $details)
    {
        $this->db->trans_start();

        $this->db->insert('cust_payments_main',$main);
        $paycod_main_id = $this->db->insert_id();
        
        $payment_num = 'P'.strval(1000000+$paycod_main_id);
        $this->db->where('custp_id', $paycod_main_id);
        $this->db->update('cust_payments_main', array("payment_no" => $payment_num));

        foreach ($details as $detail) {
            $detail->cpm_id = $paycod_main_id;
            $this->db->insert('cust_payments_details',$detail);

            $q = "select ccp.* from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id where dop.oid=".$detail->oid;
            $query = $this->db->query($q);
            $ccp = $query->row();

            $ccp->ccp_cod_paid=$ccp->ccp_cod_paid+$detail->paid_amount;
            $this->db->where('ccp_id', $ccp->ccp_id);
            $this->db->update('cust_cod_payments', $ccp);
        }

        $this->db->trans_complete();   
    }

    public function revert_cust_cod()
    {
        $this->db->trans_start();

        $q = "select * from cust_payments_main";
        $query = $this->db->query($q);
        $cpm_list = $query->result();

        foreach ($cpm_list as $cpm) {
            $q = "select * from cust_payments_details where cpm_id=".$cpm->custp_id;
            $query = $this->db->query($q);
            $cpd_list = $query->result();

            foreach ($cpd_list as $cpd) {
                $q = "select ccp.* from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id where dop.oid=".$cpd->oid;
                $query = $this->db->query($q);
                $ccp = $query->row();

                $ccp->ccp_cod_paid=0;
                $this->db->where('ccp_id', $ccp->ccp_id);
                $this->db->update('cust_cod_payments', $ccp);

                $this->db->where('detail_id', $cpd->detail_id);
                $this->db->delete('cust_payments_details');
            }
            $this->db->where('custp_id', $cpm->custp_id);
            $this->db->delete('cust_payments_main');
        }
        
        $this->db->trans_complete();   
    }

    public function getPickReqData($status,$cust_id,$date,$district,$limit,$offset)
    {
        $this->db->select('pickup_request.*,customers.cust_name,cust_address.address,cust_address.phone,district.dname,city.cname'); 
        $this->db->from('pickup_request');
        $this->db->where('pickup_request.status', $status);
        if ($cust_id!=''||$cust_id!=null) {
            $this->db->where('pickup_request.cust_id', $cust_id);
        }
        if ($district!=''||$district!=null) {
            $this->db->where('cust_address.district_id', $district);
        }
        if ($date!=''||$date!=null) {
            $this->db->like('pickup_request.request_date', $date);
        }
        $this->db->join('customers', 'customers.cust_id = pickup_request.cust_id');
        $this->db->join('cust_address', 'cust_address.address_id = pickup_request.pickup_add_id');
        $this->db->join('district', 'district.did = cust_address.district_id');
        $this->db->join('city', 'city.cid = cust_address.city_id');
        $this->db->order_by('pickr_id', "desc");
        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['pick_req']=$query->result();

        $this->db->select('*'); 
        $this->db->from('pickup_request');
        $this->db->where('pickup_request.status', $status);
        if ($district!=''||$district!=null) {
            $this->db->where('cust_address.district_id', $district);
        }
        if ($cust_id!=''||$cust_id!=null) {
            $this->db->where('pickup_request.cust_id', $cust_id);
        }
        if ($date!=''||$date!=null) {
            $this->db->like('pickup_request.request_date', $date);
        }
        $this->db->join('cust_address', 'cust_address.address_id = pickup_request.pickup_add_id');
        $query1 = $this->db->get();
        $ret['rowcount'] = $query1->num_rows();
        return $ret;
    }

    public function recover_del_charge($main, $details)
    {
        $this->db->trans_start();

        $this->db->insert('recovered_dc_main',$main);
        $recdel_main_id = $this->db->insert_id();
        
        $receipt_num = 'R'.strval(1000000+$recdel_main_id);
        $this->db->where('rcdm_id', $recdel_main_id);
        $this->db->update('recovered_dc_main', array("receipt_no" => $receipt_num));

        if($main['payment_type']=='COD'){
            $q = "select (cod.tot_ccp_payable-cod.tot_ccp_paid) as 'COD_outstanding'
                from customers as c left join (select dop.cust_id as 'dop_cust_id',sum(ccp.ccp_cod) as 'tot_ccp_payable',
                sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp 
                on dop.ccp_id= ccp.ccp_id where ccp.ccp_status=0 group by dop.cust_id) as cod on c.cust_id=cod.dop_cust_id
                where c.cust_id = ".$main['cust_id'];
            $query = $this->db->query($q);
            $COD_outstanding = $query->row()->COD_outstanding;
            if($main['total_received_amount']>$COD_outstanding){
                throw new Exception("Total Recovery Amount is greater than COD oustanding");
            }
 
            $cust_payments_main = array(
                'cust_id' => $main['cust_id'],
                'payment_type'=> 'Cash',
                'staff_id' => $main['staff_id'],
                'total_paid_amount' => $main['total_received_amount'],
                'note' => 'Recover Delevery Charge :: Receipt NO: '.$receipt_num,
                'paid_date' => date("Y-m-d H:i:s")
            );

            $this->db->insert('cust_payments_main',$cust_payments_main);
            $paycod_main_id = $this->db->insert_id();
            
            $payment_num = 'P'.strval(1000000+$paycod_main_id);
            $this->db->where('custp_id', $paycod_main_id);
            $this->db->update('cust_payments_main', array("payment_no" => $payment_num));

            $cods = $this->get_spec_customer_cod($main['cust_id']);
            $to_be_recovrd = $main['total_received_amount'];
            foreach ($cods as $cod) {
                $recving = 0;
                if($to_be_recovrd>=($cod->ccp_cod-$cod->ccp_cod_paid)){
                    $recving = ($cod->ccp_cod-$cod->ccp_cod_paid);
                }else{
                    $recving = $to_be_recovrd;
                }

                $to_be_recovrd = $to_be_recovrd - $recving;

                $detail_row = array(
                    'cpm_id' => $paycod_main_id,
                    'oid'    => $cod->oid,
                    'paid_amount' => $recving
                );
                $this->db->insert('cust_payments_details',$detail_row);

                if($to_be_recovrd<=0){
                    break;
                }
            }

            $this->db->where('rcdm_id', $recdel_main_id);
            $this->db->update('recovered_dc_main', array("note" => $main['note'].':: Payment NO : '.$payment_num));
        }

        foreach ($details as $detail) {
            $detail->dcmain_id = $recdel_main_id;
            $this->db->insert('recovered_dc_dtl',$detail);

            $q = "select cdc.* from delivered_order_payments dop left join cust_delivery_charges cdc on dop.dc_id=cdc.dc_id where dop.oid=".$detail->oid;
            $query = $this->db->query($q);
            $cdc = $query->row();

            $cdc->cdc_charge_rcvd=$cdc->cdc_charge_rcvd+$detail->received_amount;
            $this->db->where('dc_id', $cdc->dc_id);
            $this->db->update('cust_delivery_charges', $cdc);
        }

        $this->db->trans_complete();   
    }

    public function getPaidOrRecoveredDetails($search,$limit,$offset)
    {
        if ($search!=''||$search!=null) {
            $searchqr= "where c.cust_name like '".$search."%'";
        }else{
            $searchqr="";
        }
        $q = "select c.cust_id,c.cust_name,COALESCE(cod.tot_ccp_payable,0) as 'tot_ccp_payable', 
COALESCE(cod.tot_ccp_paid,0) as 'tot_ccp_paid', COALESCE(dcr.tot_cdc_receivable,0) as 'tot_cdc_receivable', 
COALESCE(dcr.tot_cdc_recovered ,0) as 'tot_cdc_recovered' from customers as c left join (select dop.cust_id as 'dop_cust_id',sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id where ccp.ccp_status=0 group by dop.cust_id) as cod on c.cust_id=cod.dop_cust_id left join (select dop2.cust_id as 'dc_cust_id',sum(cdc.cdc_charge) as 'tot_cdc_receivable',sum(cdc.cdc_charge_rcvd) as 'tot_cdc_recovered' from delivered_order_payments dop2 left join cust_delivery_charges cdc on dop2.dc_id= cdc.dc_id where cdc.cdc_status=0 group by dop2.cust_id) as dcr on c.cust_id=dcr.dc_cust_id ".$searchqr." LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->query($q);
        $ret['cust_rates']=$query->result();

        $q = "select c.cust_id,c.cust_name,cod.tot_ccp_payable, cod.tot_ccp_paid, dcr.tot_cdc_receivable from customers as c left join (select dop.cust_id as 'dop_cust_id',sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id where ccp.ccp_status=0 group by dop.cust_id) as cod on c.cust_id=cod.dop_cust_id left join (select dop2.cust_id as 'dc_cust_id',sum(cdc.cdc_charge) as 'tot_cdc_receivable' from delivered_order_payments dop2 left join cust_delivery_charges cdc on dop2.dc_id= cdc.dc_id where cdc.cdc_status=0 group by dop2.cust_id) as dcr on c.cust_id=dcr.dc_cust_id ".$searchqr;
        $query1 = $this->db->query($q);
        $ret['rowcount'] = $query1->num_rows();
        return $ret;
    }

    public function get_paid_customer_cod($cust_id,$search,$limit,$offset)
    {
        $q = "select cpm.*,su.staff_name from cust_payments_main cpm left join staff_users su on cpm.staff_id=su.user_id where cpm.cust_id=".$cust_id.' '.
            $search." LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->query($q);
        $arr['data'] = $query->result();

        $q = "select cpm.*,su.staff_name from cust_payments_main cpm left join staff_users su on cpm.staff_id=su.user_id where cpm.cust_id=".$cust_id.' '.
            $search;
        $query = $this->db->query($q);
        //echo($q);
        $arr['rowcount'] = $query->num_rows();
        return $arr;
    }

    public function get_cod_payment_details($payment_id)
    {
        $q = "select cpd.oid,cpd.detail_id, do.waybill_id,do.weight,do.cod, cod.tot_ccp_paid,cpd.paid_amount,do.delivery_charge from cust_payments_details cpd left join delivery_orders do on cpd.oid=do.oid
            left join (select dop.oid,sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id 
            where ccp.ccp_status=0 group by dop.oid) as cod on do.oid=cod.oid
            where cpd.cpm_id=".$payment_id;
        $query = $this->db->query($q);
        return $query->result();
    }

    public function get_rcvrd_customer_del($cust_id,$search,$limit,$offset)
    {
        $q = "select rdm.*,su.staff_name from recovered_dc_main rdm left join staff_users su on rdm.staff_id=su.user_id where rdm.cust_id=".$cust_id.' '.
            $search." LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->query($q);
        $arr['data'] = $query->result();

        $q = "select rdm.*,su.staff_name from recovered_dc_main rdm left join staff_users su on rdm.staff_id=su.user_id where rdm.cust_id=".$cust_id.' '.
            $search;
        $query = $this->db->query($q);
        //echo($q);
        $arr['rowcount'] = $query->num_rows();
        return $arr;
    }

    public function get_del_recoverd_details($receipt_id)
    {
        $q = "select rdd.oid,rdd.dcdtl_id, do.waybill_id,do.weight,do.cod, do.delivery_charge,dcr.tot_cdc_recovered,rdd.received_amount from recovered_dc_dtl rdd left join delivery_orders do on rdd.oid=do.oid
            left join (select dop.oid,sum(ccp.ccp_cod) as 'tot_ccp_payable',sum(ccp.ccp_cod_paid) as 'tot_ccp_paid' from delivered_order_payments dop left join cust_cod_payments ccp on dop.ccp_id= ccp.ccp_id 
            where ccp.ccp_status=0 group by dop.oid) as cod on do.oid=cod.oid
left join (select dop2.oid as 'dc_oid',sum(cdc.cdc_charge) as 'tot_cdc_receivable',sum(cdc.cdc_charge_rcvd) 
as 'tot_cdc_recovered' from delivered_order_payments dop2 left join cust_delivery_charges cdc on dop2.dc_id= cdc.dc_id 
where cdc.cdc_status=0 group by dop2.oid) as dcr on do.oid=dcr.dc_oid where rdd.dcmain_id=".$receipt_id;
        $query = $this->db->query($q);
        return $query->result();
    }

    public function isEditDispatchAllowed($group_id){
        return $this->isAccessRightGiven($group_id,4);
    }

    public function isAccessRightGiven($group_id,$prg_evn_id){
        $this->db->select('*'); 
        $this->db->from('group_progs');
        $this->db->where('prg_id', $prg_evn_id);
        $this->db->where('group_id', $group_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1){
          return true;
        }else{
          return false;
        }
    }

    public function updateDeliveryOrders($order_id,$delivery_order,$order_status,$dop_arr, $ccp_arr, $cdc_arr){
        $this->db->trans_start();

        $this->db->where('oid', $order_id);
        $this->db->update('delivery_orders', $delivery_order);

        if($order_status!=NULL){
            $this->db->insert('delivery_status',$order_status);
        }

        if($ccp_arr!=NULL && $cdc_arr!=NULL && $dop_arr!=NULL){
            $this->db->insert('cust_cod_payments', $ccp_arr); 
            $ccp_id = $this->db->insert_id();

            $this->db->insert('cust_delivery_charges', $cdc_arr); 
            $cdc_id = $this->db->insert_id();

            $dop_arr['ccp_id'] = $ccp_id ;
            $dop_arr['dc_id'] = $cdc_id ;
            $this->db->insert('delivered_order_payments', $dop_arr); 
        }

        $this->db->trans_complete(); 
    }

    public function getDeliveryCharge($order_id){
        $this->db->select('*'); 
        $this->db->from('delivery_orders');
        $this->db->where('oid', $order_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $delivery_order = $query->row();

        $this->db->select('*'); 
        $this->db->from('customers');
        $this->db->where('cust_id', $delivery_order->cust_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $customer = $query->row();

        $pickup_address = NULL;

        $this->db->select('*'); 
        $this->db->from('cust_address');
        $this->db->where('cust_id', $customer->cust_id);
        $this->db->where('address_type', 'Pickup');
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $pickup_address = $query->row();

        if($pickup_address==NULL){
            $this->db->select('*'); 
            $this->db->from('cust_address');
            $this->db->where('cust_id', $customer->cust_id);
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            $pickup_address = $query->row();
        }

        if($pickup_address==NULL){
            return NULL;
        }

        $this->db->select('*'); 
        $this->db->from('cust_address');
        $this->db->where('address_id',$delivery_order->address_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $delivery_address = $query->row();

        $this->db->select('*'); 
        $this->db->from('delivery_charges');
        $this->db->where('dc_type',$customer->delcharge_type);
        $this->db->where('from_district_id',$pickup_address->district_id);
        $this->db->where('to_district_id',$delivery_address->district_id);
        $this->db->where('from_city_id',$pickup_address->city_id);
        $this->db->where('to_city_id',$delivery_address->city_id);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $delivery_charge = $query->row();
        
        if($delivery_charge==NULL){
            $this->db->select('*'); 
            $this->db->from('delivery_charges');
            $this->db->where('dc_type',$customer->delcharge_type);
            $this->db->where('from_district_id',$pickup_address->district_id);
            $this->db->where('to_district_id',$delivery_address->district_id);
            $this->db->where('from_city_id',$pickup_address->city_id);
            $this->db->where('to_all_of_district',1);
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            $delivery_charge = $query->row();
        }

        if($delivery_charge==NULL){
            $this->db->select('*'); 
            $this->db->from('delivery_charges');
            $this->db->where('dc_type',$customer->delcharge_type);
            $this->db->where('from_district_id',$pickup_address->district_id);
            $this->db->where('to_district_id',$delivery_address->district_id);
            $this->db->where('from_all_of_district',1);
            $this->db->where('to_city_id',$delivery_address->city_id);
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            $delivery_charge = $query->row();
        }

        if($delivery_charge==NULL){
            $this->db->select('*'); 
            $this->db->from('delivery_charges');
            $this->db->where('dc_type',$customer->delcharge_type);
            $this->db->where('from_district_id',$pickup_address->district_id);
            $this->db->where('to_district_id',$delivery_address->district_id);
            $this->db->where('from_all_of_district',1);
            $this->db->where('to_all_of_district',1);
            $this -> db -> limit(1);
            $query = $this -> db -> get();
            $delivery_charge = $query->row();
        }

        // $this->db->select('*'); 
        // $this->db->from('delivery_charges');
        // $this->db->where('dc_type',$customer->delcharge_type);
        // $this->db->where('from_district_id',$pickup_address->district_id);
        // $this->db->where('to_district_id',$delivery_address->district_id);
        // $this->db->where('(from_city_id='.$pickup_address->city_id.' or from_all_of_district=1)');
        // $this->db->where('(to_city_id='.$delivery_address->city_id.' or to_all_of_district=1)');
        // $this -> db -> limit(1);
        // $query = $this -> db -> get();
        // $delivery_charge = $query->row();
        return $delivery_charge;
    }

    public function getAgents(){
        $q = "select su.user_id,su.staff_name,d.dname,d.did from staff_users su left join district d on su.branch_id=d.did
                where su.is_agent=1";
        $query = $this->db->query($q);
        return $query->result();
    }

    public function getAgentsForSupervisor($spv_id){
        $q = "select su.user_id,su.staff_name,d.dname,d.did from staff_users su left join district d on su.branch_id=d.did
                where su.is_agent=1 and su.user_id in (select distinct aa.agent_id from assign_agent aa where aa.staff_id=".$spv_id.")";
        $query = $this->db->query($q);
        return $query->result();
    }

    public function getSupervisors(){
        $q = "select su.user_id,su.staff_name,d.dname from staff_users su left join district d on su.branch_id=d.did
                where su.is_agent=0 and su.access_group=5";
        $query = $this->db->query($q);
        return $query->result();
    }

    public function getStaffs(){
        $q = "select su.user_id,su.staff_name,d.dname from staff_users su left join district d on su.branch_id=d.did
                where su.is_agent=0";
        $query = $this->db->query($q);
        return $query->result();
    }

    public function assignAgent($assign_agent){
        $this->db->trans_start();

        $this->db->select('*'); 
        $this->db->from('assign_agent');
        $this->db->where('agent_id',$assign_agent['agent_id']);
        $this -> db -> limit(1);
        $query = $this -> db -> get();
        $count = $query->num_rows();
        if($count>0){
            throw new Exception("Agent already assingned.");
        }

        $this->db->insert('assign_agent', $assign_agent); 
            
        $this->db->trans_complete(); 
    }

    function get_assigned_agents($search,$limit,$offset){
        $q = "select aa.assign_agent_id,aa.agent_id,aa.staff_id,aa.district_id, 
            s.staff_name as 'staffname', ag.staff_name as 'agentname',d.dname as 'branch'  
            from assign_agent aa 
            left join staff_users s on aa.staff_id=s.user_id
            left join staff_users ag on aa.agent_id=ag.user_id
            left join district d on aa.district_id = d.did";

        if($search!=''){
            $search = ' where '.$search;
        }

        //echo($q.$search);
        $query = $this->db->query($q.$search." LIMIT ".$limit." OFFSET ".$offset);
        $ret['data']=$query->result();


        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();
        
        return $ret;
    }

    public function deleteAssignedAgent($aaid){
        $this->db->where('assign_agent_id', $aaid);
        $this->db->delete('assign_agent');
    }

    function get_cod_for_agent_deposit($search,$branch,$group_id,$limit,$offset,$user_id){
        $q = "select do.oid,do.waybill_id as 'waybillid',c.cust_name as 'custname',cad.name as 'resname', 
        cad.address as 'resaddress',d.dname as 'disname',ct.cname as 'cityname', 
        su.staff_name as 'dispatchto', d2.dname as 'dt_dist',
        cad.phone as 'resphone',(select max(dss.status_date) from delivery_status dss where dss.order_id=do.oid) as 'deliverydate',
        COALESCE(do.cod,0) as 'codamount', COALESCE(ccp.ccp_cod_paid,0) as 'codpaid', COALESCE(ccp.rcvd_from_agent,0) as 'agentdeposite',
        os.status as 'ostatus', os.os_id as 'ostatusid',
        do.date as 'placedate', do.weight, do.delivery_charge, do.dispatch_to from delivery_orders do  
        left join delivered_order_payments dop on do.oid = dop.oid
        left join cust_cod_payments ccp on dop.ccp_id = ccp.ccp_id 
        left join cust_delivery_charges cdc on dop.dc_id=cdc.dc_id
        left join order_status os on do.current_status=os.os_id
        left join customers c on do.cust_id=c.cust_id
        left join cust_address cad on do.address_id = cad.address_id
        left join district as d on cad.district_id=d.did
        left join staff_users as su on do.dispatch_to=su.user_id
        left join district as d2 on su.branch_id=d2.did
        left join city as ct on cad.city_id=ct.cid ";

        $has_cond = ($search=='')?false:true;
        if($group_id=='5'){
            //$status = array(1,4,5,6,7,8,9);
            if($has_cond){
                $search=$search.' and ';
            }
            $search = $search .'do.dispatch_to in (select distinct aa.agent_id from assign_agent aa where aa.staff_id='.$user_id.')';
            //$this->db->where_not_in('current_status', $status);
        }

        if($search!=''){
            $search = ' where '.$search;
        }

        $search = $search." order by (select max(dss.status_date) from delivery_status dss where dss.order_id=do.oid) asc";
        //echo($q.$search);
        $query = $this->db->query($q.$search." LIMIT ".$limit." OFFSET ".$offset);
        $ret['cust_orders']=$query->result();


        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();
        
        return $ret;
    }

    function get_cod_total_outstanding($group_id,$user){
        $q = "select sum(ccp.ccp_cod-ccp.rcvd_from_agent) as 'totaloutstanding' from delivery_orders do  
        left join delivered_order_payments dop on do.oid = dop.oid
        left join cust_cod_payments ccp on dop.ccp_id = ccp.ccp_id 
        Where do.current_status =6 and ccp.ccp_status=0 and (ccp.ccp_cod-ccp.rcvd_from_agent)>0";

        
        if($group_id=='5'){
            $q = $q .' and do.dispatch_to in (select distinct aa.agent_id from assign_agent aa where aa.staff_id='.$user->user_id.')';
        }

        if($user->is_agent==1){
            $q = $q." and do.dispatch_to = ".$user->user_id;
        }

        $q = $q." order by (select max(dss.status_date) from delivery_status dss where dss.order_id=do.oid) asc";
        //echo($q.$search);
        $query = $this->db->query($q);
        return $query->row()->totaloutstanding;
    }

    public function make_deposit($staff_id, $agent_id,$deposite_amount,$remark){
        $this->db->trans_start();

        $acd_arr =array(
            'agent_id' => $agent_id,
            'total_paid_amount' => $deposite_amount,
            'date' => date("Y-m-d H:i:s"),
            'remark' => $remark,
            'app_or_reject' => 'P',
            'user_id' => $staff_id
        );

        $this->db->insert('agent_cod_deposit', $acd_arr); 
        $acd_id =  $this->db->insert_id();

        $q = "select do.oid, do.cod, COALESCE(acd.paid_amt,0) as 'paid_amt' from delivery_orders do 
            left join 
            (select acdd.oid, sum(acdd.paid_amount) as 'paid_amt' from agent_cod_deposit_dtl acdd left join agent_cod_deposit acd on acdd.acd_id=acd.acd_id where acd.app_or_reject!='R' group by acdd.oid) acd 
            on do.oid=acd.oid where do.dispatch_to=".$agent_id." and do.cod>0 and (do.cod-COALESCE(acd.paid_amt,0))>0 
            order by (select max(dss.status_date) from delivery_status dss where dss.order_id=do.oid) asc";

        //echo($q.$search);
        $query = $this->db->query($q);
        $outstanding_orders=$query->result();

        foreach ($outstanding_orders as $order) {
            $available_amt = $order->cod-$order->paid_amt;
            $row = array();
            $recovering_amt=0;
            if($available_amt>=$deposite_amount){
                $recovering_amt=$deposite_amount;
                $deposite_amount = 0;
            }else{
                $recovering_amt = $available_amt;
                $deposite_amount = $deposite_amount-$recovering_amt;
            }

            $row = array(
                'acd_id'=> $acd_id,
                'oid' => $order->oid,
                'paid_amount' => $recovering_amt
            );
            $this->db->insert('agent_cod_deposit_dtl', $row); 

            if($deposite_amount==0){
                break;
            }
        }

        if($deposite_amount!=0){
            throw new Exception("Not enough COD to Deposit.");
        }
        $this->db->trans_complete(); 
    }

    public function get_agent_summery($search,$limit,$offset, $user, $group_id)
    {
        $searchqr= "ag.is_agent=1";
        $has_filtr = true;
        if ($search!=''||$search!=null) {
            $searchqr= "(ag.staff_name like '%".$search."%' or sup.staff_name like '%".$search."%')";
            $has_filtr = true;
        }

        if($user->is_agent==1){
            if($has_filtr){
                $searchqr=$searchqr." and ";
            }
            $searchqr=$searchqr."ag.user_id=".$user->user_id;
        }else if($group_id==5){
            if($has_filtr){
                $searchqr=$searchqr." and ";
            }
            $searchqr=$searchqr."ag.user_id in (select distinct aa.agent_id from assign_agent aa where aa.staff_id=".$user->user_id.")";
        }

        $q = "SELECT ag.user_id as 'agent_id', ag.staff_name as 'agentname', COALESCE(sup.staff_name,'N/A') as 'supervisorname',
                COALESCE(pmnt.total_cod,0) as 'total_cod',COALESCE(pmnt.rcvd_from_agent,0) as 'rcvd_from_agent' ,
                COALESCE(dep.pendingamount,0) as 'pendingamount'
                FROM staff_users ag 
                left join assign_agent aa on ag.user_id = aa.agent_id
                left join staff_users sup on aa.staff_id=sup.user_id
                left join (select do.dispatch_to,sum(COALESCE(ccp.ccp_cod,0)) as 'total_cod',
                sum(COALESCE(ccp.rcvd_from_agent,0)) as 'rcvd_from_agent' 
                from delivery_orders do 
                left join delivered_order_payments dop on do.oid=dop.oid
                left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id
                group by do.dispatch_to) pmnt on ag.user_id = pmnt.dispatch_to
                left join (select acd.agent_id as 'agent_id', sum(acdd.paid_amount) 'pendingamount' from agent_cod_deposit_dtl acdd 
                left join agent_cod_deposit acd on acdd.acd_id= acd.acd_id
                where acd.app_or_reject='P' group by acd.agent_id) dep on ag.user_id = dep.agent_id
                where ".$searchqr." LIMIT ".$limit." OFFSET ".$offset;
        $query = $this->db->query($q);
        $ret['data']=$query->result();

        $q = "SELECT ag.user_id, ag.staff_name as 'agentname', sup.staff_name as 'supervisorname',
                COALESCE(pmnt.total_cod,0) as 'total_cod',COALESCE(pmnt.rcvd_from_agent,0) as 'rcvd_from_agent' ,
                COALESCE(dep.pendingamount,0) as 'pendingamount'
                FROM staff_users ag 
                left join assign_agent aa on ag.user_id = aa.agent_id
                left join staff_users sup on aa.staff_id=sup.user_id
                left join (select do.dispatch_to,sum(COALESCE(ccp.ccp_cod,0)) as 'total_cod',
                sum(COALESCE(ccp.rcvd_from_agent,0)) as 'rcvd_from_agent' 
                from delivery_orders do 
                left join delivered_order_payments dop on do.oid=dop.oid
                left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id
                group by do.dispatch_to) pmnt on ag.user_id = pmnt.dispatch_to
                left join (select acd.agent_id as 'agent_id', sum(acdd.paid_amount) 'pendingamount' from agent_cod_deposit_dtl acdd 
                left join agent_cod_deposit acd on acdd.acd_id= acd.acd_id
                where acd.app_or_reject='P' group by acd.agent_id) dep on ag.user_id = dep.agent_id
                where ".$searchqr;
        $query1 = $this->db->query($q);
        $ret['rowcount'] = $query1->num_rows();
        return $ret;
    }

    function get_agent_depo($agent_id){
        $q = "select acd.*,su.staff_name from agent_cod_deposit acd 
        left join staff_users su on acd.user_id=su.user_id where acd.app_or_reject='P' and acd.agent_id=".$agent_id." order by acd.date desc";

        //echo($q.$search);
        $query = $this->db->query($q);
        return $query->result();
    }

    function get_agent_depo_details($deposite_id){
        $q = "select do.waybill_id,ccp.ccp_cod,ccp.rcvd_from_agent,acdd.paid_amount from agent_cod_deposit_dtl acdd
            left join delivery_orders do on acdd.oid = do.oid
            left join delivered_order_payments dop on acdd.oid=dop.oid
            left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id where acdd.acd_id=".$deposite_id;

        //echo($q.$search);
        $query = $this->db->query($q);
        return $query->result();
    }

    public function approve_or_reject_deposit($acd_id,$status,$staff_id){
        $this->db->trans_start();

        $this->db->where('acd_id', $acd_id);
        $this->db->update('agent_cod_deposit', array('app_or_reject'=>$status, 'app_or_rej_by'=> $staff_id));

        if($status=='A'){
            $this->db->select('*'); 
            $this->db->from('agent_cod_deposit_dtl');
            $this->db->where('acd_id',$acd_id);
            $query = $this -> db -> get();
            $acdd_array = $query->result();

            foreach ($acdd_array as $acdd) {
                $q = "select ccp.* from delivered_order_payments dop 
                left join cust_cod_payments ccp on dop.ccp_id=ccp.ccp_id where dop.oid=".$acdd->oid;

                //echo($q.$search);
                $query = $this->db->query($q);
                $ccp = $query->row();

                $ccp->ccp_status=1;
                $ccp->rcvd_from_agent = $ccp->rcvd_from_agent+$acdd->paid_amount;

                $this->db->where('ccp_id', $ccp->ccp_id);
                $this->db->update('cust_cod_payments', $ccp);
            }
        }
        
        $this->db->trans_complete(); 
    }

    function get_orders_report($search,$branch,$group_id,$user_id,$limit,$offset,$isExport=0){
        $q = "select c.cust_id,c.cust_name as 'cust_name', do.waybill_id, 
            do.delivery_charge ,COALESCE(cdc.cdc_charge_rcvd,0) as 'delchargercvd', 
            do.cod, COALESCE(ccp.ccp_cod,0) as 'cod_received', COALESCE(ccp.rcvd_from_agent,0) as 'cod_deposited',COALESCE(ccp.ccp_cod_paid,0) as 'cod_paid', 
            ag.staff_name as 'agent',do.date as 'order_date',
            ca.address as 'address',ct.cname as 'city',d.dname as 'district',os.status as 'status',do.current_status
            from delivery_orders do
            left join delivered_order_payments dop on do.oid = dop.oid
            left join cust_cod_payments ccp on dop.ccp_id = ccp.ccp_id 
            left join cust_delivery_charges cdc on dop.dc_id=cdc.dc_id
            left join customers c on do.cust_id=c.cust_id
            left join cust_address ca on do.address_id=ca.address_id
            left join city ct on ct.cid=ca.city_id
            left join district d on ca.district_id=d.did
            left join order_status os on do.current_status=os.os_id
            left join staff_users ag on do.dispatch_to=ag.user_id ";

        $has_cond = ($search=='')?false:true;
        if($group_id=='5'){
            if($has_cond){
                $search=$search.' and ';
            }
            $search = $search .'do.dispatch_to in (select distinct aa.agent_id from assign_agent aa where aa.staff_id='.$user_id.')';
        }

        if($search!=''){
            $search = ' where '.$search;
        }

        //echo($q.$search);
        $q2 = $q.$search;
        if($isExport==0){
           $q2 = $q2." LIMIT ".$limit." OFFSET ".$offset;
        }
        $query = $this->db->query($q2);
        $ret['cust_orders']=$query->result();
        
        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();

        return $ret;
    }

    function get_cod_report($report_type,$search,$limit,$offset,$isExport=0){
        $q = "";
        if($report_type=="P"){
            $q = "select c.cust_name,cpm.payment_no,cpm.payment_type,cpm.paid_date,
            cpm.total_paid_amount as 'paid_amount', cpm.note, su.staff_name as 'paid_by' 
            from cust_payments_main cpm
            left join customers c on cpm.cust_id=c.cust_id
            left join staff_users su on cpm.staff_id=su.user_id ";
        }else{
            $q = "select c.cust_name,cpm.payment_no,cpm.payment_type,cpm.paid_date, do.waybill_id,
            do.cod, cpd.paid_amount as 'paying_amount', cpm.note, su.staff_name as 'paid_by'
            from cust_payments_details cpd
            left join cust_payments_main cpm on cpd.cpm_id=cpm.custp_id
            left join customers c on cpm.cust_id=c.cust_id
            left join delivery_orders do on cpd.oid=do.oid
            left join staff_users su on cpm.staff_id=su.user_id ";
        }

        if($search!=''){
            $search = ' where '.$search;
        }

        //echo($q.$search);
        $q2 = $q.$search;
        if($isExport==0){
           $q2 = $q2." LIMIT ".$limit." OFFSET ".$offset;
        }
        $query = $this->db->query($q2);
        $ret['data']=$query->result();
        
        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();

        return $ret;
    }

    function get_del_charge_report($report_type,$search,$limit,$offset,$isExport=0){
        $q = "";
        if($report_type=="R"){
            $q = "select c.cust_name,rdm.receipt_no, rdm.payment_type, rdm.received_date,rdm.total_received_amount,
            rdm.note, su.staff_name as 'recovered_by'
            from recovered_dc_main rdm
            left join customers c on rdm.cust_id=c.cust_id
            left join staff_users su on rdm.staff_id=su.user_id ";
        }else{
            $q = "select c.cust_name,rdm.receipt_no, rdm.payment_type, rdm.received_date, do.waybill_id,
            do.delivery_charge, rdd.received_amount as 'receiving_amount', rdm.note, su.staff_name as 'recovered_by' 
            from recovered_dc_dtl rdd
            left join recovered_dc_main rdm on rdd.dcmain_id=rdm.rcdm_id
            left join customers c on rdm.cust_id=c.cust_id
            left join delivery_orders do on rdd.oid=do.oid
            left join staff_users su on rdm.staff_id=su.user_id ";
        }

        if($search!=''){
            $search = ' where '.$search;
        }

        //echo($q.$search);
        $q2 = $q.$search;
        if($isExport==0){
           $q2 = $q2." LIMIT ".$limit." OFFSET ".$offset;
        }
        $query = $this->db->query($q2);
        $ret['data']=$query->result();
        
        $query1 = $this->db->query($q.$search);
        $ret['rowcount'] = $query1->num_rows();

        return $ret;
    }
}

