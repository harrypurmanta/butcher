<?php namespace App\Models;

use CodeIgniter\Model;

class Laporanmodel extends Model
{
    

    public function getByfilter($status_cd,$start_dttm,$end_dttm) {
    	return $this->db->table('billing')
                        ->select('SUM(amt_before_discount) as amt_before_discount, SUM(ttl_amount) as grosssales,SUM(ttl_discount) as ttldiscount, SUM(tax) AS totaltax, SUM(service) AS totalservice, SUM(rounding) AS ttlrounding')
                        ->where('status_cd','closed')
                        ->where('created_dttm >=',$start_dttm.' 00:00:00')
                        ->where('created_dttm <=',$end_dttm.' 23:59:59')
                        ->get();
    }

    public function getVoid($status_cd,$start_dttm,$end_dttm) {
        return $this->db->query("SELECT billing_id as bill_id, (SELECT SUM(price) FROM billing_item WHERE billing_id=bill_id AND status_cd='cancel') AS totalvoid FROM billing WHERE status_cd IN ('cancel','closed') AND created_dttm >= '$start_dttm 00:00:00' AND created_dttm <= '$end_dttm 23:59:59'");
    }

    public function getTopitem($status_cd,$start_dttm,$end_dttm) {
        return $this->db->query("SELECT b.produk_id, SUM(b.qty) AS totalqty, SUM(b.price) as totalprice, c.produk_nm
                                FROM billing a 
                                LEFT JOIN billing_item b ON b.billing_id=a.billing_id 
                                LEFT JOIN produk c ON c.produk_id=b.produk_id
                                WHERE a.status_cd = 'closed'
                                AND b.status_cd = 'normal'
                                AND a.created_dttm >= '$start_dttm 00:00:00' 
                                AND a.created_dttm <= '$end_dttm 23:59:59'
                                GROUP BY produk_id 
                                ORDER BY SUM(b.qty) DESC");
    }

    public function getLattestitem($status_cd,$start_dttm,$end_dttm) {
        return $this->db->query("SELECT b.produk_id, SUM(b.qty) AS totalqty, SUM(b.price) as totalprice, c.produk_nm
                                FROM billing a 
                                LEFT JOIN billing_item b ON b.billing_id=a.billing_id 
                                LEFT JOIN produk c ON c.produk_id=b.produk_id
                                WHERE a.status_cd = 'closed'
                                AND b.status_cd = 'normal'
                                AND a.created_dttm >= '$start_dttm 00:00:00' 
                                AND a.created_dttm <= '$end_dttm 23:59:59'
                                GROUP BY produk_id 
                                ORDER BY SUM(b.qty) ASC
                                LIMIT 10");
    }

    public function getPayplanEdc($status_cd,$start_dttm,$end_dttm) {
        return $this->db->query("SELECT a.payplan_id, SUM(a.ttl_amount) AS ttlamount, COUNT(a.billing_id) AS totalpayplan, b.payplan_nm
                                FROM billing a 
                                LEFT JOIN payplan b ON a.payplan_id=b.payplan_id 
                                WHERE b.type = 'edc'
                                AND a.status_cd = 'closed'
                                AND a.created_dttm >= '$start_dttm 00:00:00' 
                                AND a.created_dttm <= '$end_dttm 23:59:59'
                                GROUP BY a.payplan_id 
                                ORDER BY SUM(a.ttl_amount) DESC");
    }

    public function getPayplanTunai($status_cd,$start_dttm,$end_dttm) {
        return $this->db->query("SELECT a.payplan_id, SUM(a.ttl_amount) AS ttlamount, COUNT(a.billing_id) AS totalpayplan, b.payplan_nm
                                FROM billing a 
                                LEFT JOIN payplan b ON a.payplan_id=b.payplan_id 
                                WHERE b.type = 'tunai'
                                AND a.status_cd = 'closed'
                                AND a.created_dttm >= '$start_dttm 00:00:00' 
                                AND a.created_dttm <= '$end_dttm 23:59:59'
                                GROUP BY a.payplan_id 
                                ORDER BY SUM(a.ttl_amount) DESC");
    }

    public function getCustomerbyrange($start_dttm,$end_dttm) {
        return $this->db->table('billing')
                        ->select('hour(created_dttm) as label, COUNT(jumlah_customer) as y')
                        ->where('created_dttm >=', '2020-12-1 00:00:00')
                        ->where('created_dttm <=', '2020-12-1 23:59:59')
                        ->groupby('hour(created_dttm)')
                        ->get();
    }
}