<?php

class Order_modal extends CI_Model {
    function getOrders($allOderAccess,$visibleAllSites,$search,$ostatus,$pstatus,$fdate,$tdate,$limit,$offset)
    {
        if ($visibleAllSites) {
            $result = $this->getUserSites();
        }

        $this->db->select('*');
        $this->db->from('orders o');

        if ($allOderAccess) {
            $this->db->where('o.payment_status', 2);
        }
        if ($visibleAllSites) {
            if (!empty($result)) {
                $this->db->where_in('o.ws_id', $result);
            }else{
                $this->db->where('o.ws_id', 0);
            }
        }
        if ($search!=''&&$search!=null) {
            $this->db->like('o.order_code', $search);
        }
        if ($ostatus!=''||$ostatus!=null) {
            $this->db->where('s.os_id', $ostatus);
        }
        if ($pstatus!=''&&$pstatus!=null) {
            $this->db->where('o.payment_status', $pstatus);
        }

        if ($fdate!=''&&$fdate!=null&&$tdate!=''&&$tdate!=null) {
        	$fdate = date( 'Y-m-d H:i:s', strtotime( $fdate ) );
        	$tdate = date( 'Y-m-d H:i:s', strtotime( $tdate ) );
            $this->db->where("o.order_date BETWEEN '".$fdate."' AND '".$tdate."'");
        }
        $this->db->join('order_status_det s', 's.osd_id = o.order_status');
        $this->db->join('order_statuses os', 'os.os_id = s.os_id');
        $this->db->join('website w', 'w.ws_id = o.ws_id');

        $tempdb = clone $this->db;
        $ret['rowcount'] = $tempdb->count_all_results();

        $this->db->order_by('o.payment_status', "desc");
        $this->db->limit($limit,$offset);
        $query = $this->db->get();
        $ret['orders']=$query->result();

        return $ret;
    }
    function getProfileByOrder($order_id)
    {
        $this->db->select('c.cust_id,c.fname,c.lname,c.mobile,c.email,c.email_verified,a.address,co.nicename,r.region_name,ci.city_name,p.photo_path,p.photo_title');
        $this->db->from('orders o');
        $this->db->where('o.order_id', $order_id);
        $this->db->join('customers c', 'c.cust_id = o.cust_id');
        $this->db->join('addresses a', 'a.add_type=0 AND a.user_id = c.cust_id');
        $this->db->join('country as co', 'co.country_id = a.country_id', 'left');
        $this->db->join('regions as r', 'r.reg_id = a.reg_id', 'left');
        $this->db->join('cities as ci', 'ci.city_id = a.city_id', 'left');
        $this->db->join('photo p', 'p.table="customers" AND p.field_id = c.cust_id', 'left outer');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }
    public function getUserSites()
    {
        $user_id = $this->session->userdata['staff_logged_in']['user_id'];

        $this->db->select('ws_id');
        $this->db->from('staff_sites');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $available = $query->result();
        $available = array_map(function($o) use ($available) {return (integer)$o->ws_id;}, $available);
        return $available;
    }
    function getAddressByOrder($order_id)
    {
        $this->db->select('a.*,co.nicename,r.region_name,ci.city_name,a.phone,o.order_email');
        $this->db->from('orders o');
        $this->db->where('o.order_id', $order_id);
        $this->db->join('addresses a', 'a.add_id = o.add_id');
        $this->db->join('country as co', 'co.country_id = a.country_id', 'left');
        $this->db->join('regions as r', 'r.reg_id = a.reg_id', 'left');
        $this->db->join('cities as ci', 'ci.city_id = a.city_id', 'left');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function getOrderDetails($order_id)
    {
        $this->db->select('o.order_id,o.order_code,o.cart_total,o.del_charge,o.discount,o.paid_total');
        $this->db->from('orders o');
        $this->db->where('o.order_id', $order_id);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function getOrderStatus($order_id)
    {
        $this->db->select('*');
        $this->db->from('order_status_det o');
        $this->db->where('o.order_id', $order_id);
        $this->db->join('order_statuses as s', 's.os_id = o.os_id', 'left');
        $this->db->order_by('o.osd_id', "desc");
        $query = $this->db->get();
        return $query->result();
    }

    function getCurrentStatus($order_id)
    {
        $this->db->select('o.payment_status,s.os_id');
        $this->db->from('orders o');
        $this->db->where('o.order_id', $order_id);
        $this->db->join('order_status_det as s', 's.osd_id = o.order_status', 'left');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){
          return $query->row();
        }else{
          return false;
        }
    }

    function updateOrderStatus($order_id,$order_arr,$status_arr)
    {
        $this->db->trans_start();

        if (!empty($status_arr)) {
            $this->db->insert('order_status_det',$status_arr);
            $order_arr['order_status'] = $this->db->insert_id();
        }

        $this->db->where('order_id', $order_id);
        $this->db->update('orders', $order_arr);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    function getOrderProducts($id)
    {
        $q = "select * from order_details od left join products p on p.pro_id = od.pro_id left join staff_users s 
            on s.user_id = p.user_id left outer join
            (select pt2.* from photo pt2 inner join 
            (select pt1.table,pt1.field_id,min(pt1.pid) as pt1_pid from photo pt1 group by pt1.table,pt1.field_id having min(pt1.photo_order)) pt1 
            on pt2.pid=pt1.pt1_pid where pt2.table='products') pt on pt.field_id = p.pro_id
            where od.order_id =".$id;

        $query = $this->db->query($q);
        $order_det = $query->result();

        foreach ($order_det as $row) {
            if ($row->sub_pro_id!=0) {
                $this->db->select('*');
                $this->db->from('sub_product');
                $this->db->where('sub_pro_id', $row->sub_pro_id);
                $this->db->limit(1);
                $query = $this->db->get();
                if($query->num_rows() > 0){
                  $row->subProduct =  $query->row();
                }else{
                  $row->subProduct =  NULL;
                }
            }
            $this->db->select('*');
            $this->db->from('order_product_specs ods');
            $this->db->where('ods.odet_id', $row->det_id);
            $this->db->join('attributes attr', 'ods.attr_id = attr.attr_id');
            $this->db->join('attribute_value av', 'ods.av_id = av.av_id');
            $query = $this->db->get();
            $row->attributes = $query->result();
        }
        return $order_det;
    }
    public function getOrderProForDelcharge($orderId)
    {
        $this->db->select('o.qty,o.billed_unit_price,p.weight');
        $this->db->from('order_details o');
        $this->db->where('o.order_id', $orderId);
        $this->db->join('products as p', 'p.pro_id = o.pro_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }

    function updateDelAddr($order_id,$add_id,$del_arr,$addr_array)
    {
        $this->db->trans_start();

        if (!empty($addr_array)) {
            $this->db->where('add_id', $add_id);
            $this->db->update('addresses', $addr_array);
        }

        if (!empty($del_arr)) {
            $this->db->where('order_id', $order_id);
            $this->db->update('orders', $del_arr);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }
}

