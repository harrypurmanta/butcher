<?php namespace App\Models;

use CodeIgniter\Model;

class Billingmodel extends Model
{
    protected $table      = 'billing';
    protected $primaryKey = 'billing_id ';
    protected $allowedFields = ['meja_id','member_id','discount_id','order_id','payplan_id','balance','ttl_paid','ttl_amount','ttl_discount','amt_before_discount', 'status_cd', 'created_dttm','created_user','updated_dttm','updated_user','nullified_dttm','nullified_user'];
    protected $billingmodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';

    public function getDesclim1() {
        return $this->db->table('billing')
                        ->select('billing_id')
                        ->limit(1)
                        ->orderby('billing_id','DESC')
                        ->get();
    }

    public function getbyMejaid($id){
    	$query = $this->db->table('billing a');
    	$query->select('b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd as statusproduk,b.billing_item_id,a.billing_id,a.status_cd as statusbilling, a.created_dttm as created_dttm,a.member_id,f.meja_nm, g.person_nm as member_nm,h.person_nm as collected_nm');
    	$query->join('billing_item b','b.billing_id=a.billing_id');
    	$query->join('produk c','c.produk_id=b.produk_id');
    	$query->join('kategori_produk d','d.kategori_id=c.kategori_id');
    	$query->join('member e','e.member_id=a.member_id','left');
    	$query->join('meja f','f.meja_id=a.meja_id','left');
    	$query->join('person g','g.person_id=e.person_id','left');
    	$query->join('person h','h.person_id=a.verified_user','left');
    	$query->whereIn('a.status_cd',['waiting','verified']);
    	$query->where('a.meja_id',$id);
    	return $query->get();
    }

    public function getbyMejaidcustomer($id){
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd as statusproduk,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm as member_nm,,h.person_nm as collected_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->join('person h','h.person_id=a.verified_user','left');
        $query->whereIn('a.status_cd',['verified','waiting','normal']);
        $query->where('b.status_cd','normal');
        $query->whereNotIn('b.qty',['0']);
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function getbyMejaidkasir($id){
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,a.collected_user,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm,h.person_nm as collected_nm,i.person_nm as member_nm,j.payplan_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('person i','i.person_id=e.person_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->join('person h','h.person_id=a.verified_user','left');
        $query->join('payplan j','j.payplan_id=a.payplan_id','left');
        $query->whereIn('a.status_cd',['verified','normal']);
        $query->where('b.status_cd','normal');
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function getitembyBillid($id) {
    	$query = $this->db->table('billing_item a');
    	$query->select('a.qty,b.produk_nm,b.produk_id');
    	$query->join('produk b','b.produk_id=a.produk_id');
    	$query->where('a.status_cd','normal');
    	$query->where('a.billing_id',$id);
    	return $query->get();
    }

    public function getdrinkmenu($id) {
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm as member_nm,b.description');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->where('a.status_cd','verified');
        $query->where('b.status_cd','normal');
        $query->where('b.print_status','normal');
        $query->whereIn('d.kategori_id',[7,8,9,10,11]);
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function getfoodmenu($id) {
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm as member_nm,b.description');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->where('a.status_cd','verified');
        $query->where('b.status_cd','normal');
        $query->where('b.print_status','normal');
        $query->whereIn('d.kategori_id',[1,2,3,4,5,6,12,13,14,15,16,17]);
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function cetakulangdrinks($billing_id) {
        return $this->db->table('billing a')
                        ->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,b.description')
                        ->join('billing_item b','b.billing_id=a.billing_id','left')
                        ->join('produk c','c.produk_id=b.produk_id','left')
                        ->join('kategori_produk d','d.kategori_id=c.kategori_id','left')
                        ->join('meja f','f.meja_id=a.meja_id','left')
                        ->where('a.status_cd','verified')
                        ->where('b.status_cd','normal')
                        ->whereIn('d.kategori_id',[7,8,9,10,11])
                        ->where('a.billing_id',$billing_id)
                        ->get();
    }

    public function cetakulangfoods($billing_id) {
        return $this->db->table('billing a')
                        ->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,b.description')
                        ->join('billing_item b','b.billing_id=a.billing_id','left')
                        ->join('produk c','c.produk_id=b.produk_id','left')
                        ->join('kategori_produk d','d.kategori_id=c.kategori_id','left')
                        ->join('meja f','f.meja_id=a.meja_id','left')
                        ->where('a.status_cd','verified')
                        ->where('b.status_cd','normal')
                        ->whereIn('d.kategori_id',[1,2,3,4,5,6,12,13,14,15,16,17])
                        ->where('a.billing_id',$billing_id)
                        ->get();
    }

    public function cetakulangcheckout($billing_id) {
        return $this->db->table('billing a')
                        ->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,a.collected_user,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm,h.person_nm as collected_nm,i.person_nm as member_nm,j.payplan_nm,a.payplan_id,a.ttl_paid')
                        ->join('billing_item b','b.billing_id=a.billing_id','left')
                        ->join('produk c','c.produk_id=b.produk_id','left')
                        ->join('kategori_produk d','d.kategori_id=c.kategori_id','left')
                        ->join('member e','e.member_id=a.member_id','left')
                        ->join('person i','i.person_id=e.person_id','left')
                        ->join('meja f','f.meja_id=a.meja_id','left')
                        ->join('person g','g.person_id=e.person_id','left')
                        ->join('person h','h.person_id=a.verified_user','left')
                        ->join('payplan j','j.payplan_id=a.payplan_id','left')
                        ->where('a.status_cd','finish')
                        ->where('b.status_cd','normal')
                        ->where('a.billing_id',$billing_id)
                        ->get();
    }

    public function getbybilldiscid($bi,$di) {
        return $this->db->table('billing_discount a')
                    ->join('discount b','b.discount_id=a.discount_id')
                    ->where('a.status_cd','normal')
                    ->where('a.billing_id',$bi)
                    ->where('a.discount_id',$di)
                    ->get();
    }

    public function getTotalitem($closed_dttm) {
        return $this->db->table('billing a')
                        ->selectSum('b.price')
                        ->join('billing_item b','b.billing_id=a.billing_id')
                        ->where('a.status_cd','closed')
                        ->where('closed_dttm >=',$closed_dttm.' 00:00:00')
                        ->where('closed_dttm <=',$closed_dttm.' 59:23:23')
                        ->get();
    }

    public function getTotalbill($closed_dttm) {
        return $this->db->table('billing')
                        ->selectCount('billing_id')
                        ->where('status_cd','closed')
                        ->where('closed_dttm >=',$closed_dttm.' 00:00:00')
                        ->where('closed_dttm <=',$closed_dttm.' 59:23:23')
                        ->get();
    }

    public function getVoidbill($closed_dttm) {
        return $this->db->table('billing')
                        ->selectCount('billing_id')
                        ->where('status_cd','nullified')
                        ->where('closed_dttm >=',$closed_dttm.' 00:00:00')
                        ->where('closed_dttm <=',$closed_dttm.' 59:23:23')
                        ->get();
    }

    

    public function getStatuskasir() {
        return $this->db->table('kasir_status')
                        ->limit(1)
                        ->orderby('kasir_status_id','DESC')
                        ->get();
    }

    public function getbyfinish() {
        return $this->db->table('billing a')
                        ->join('billing_item b','b.billing_id=a.billing_id','left')
                        ->join('produk c','c.produk_id=b.produk_id','left')
                        ->join('kategori_produk d','d.kategori_id=c.kategori_id','left')
                        ->join('member e','e.member_id=a.member_id','left')
                        ->join('meja f','f.meja_id=a.meja_id','left')
                        ->join('person g','g.person_id=e.person_id','left')
                        ->join('person h','h.person_id=a.verified_user','left')
                        ->join('payplan i','i.payplan_id=a.payplan_id','left')
                        ->where('a.status_cd','finish')
                        ->where('b.status_cd','normal')
                        ->orderby('a.billing_id','DESC')
                        ->groupby('a.billing_id')
                        ->get();
    }

    public function getbyverified() {
        return $this->db->table('billing a')
                        ->join('billing_item b','b.billing_id=a.billing_id','left')
                        ->join('produk c','c.produk_id=b.produk_id','left')
                        ->join('kategori_produk d','d.kategori_id=c.kategori_id','left')
                        ->join('member e','e.member_id=a.member_id','left')
                        ->join('meja f','f.meja_id=a.meja_id','left')
                        ->join('person g','g.person_id=e.person_id','left')
                        ->join('person h','h.person_id=a.verified_user','left')
                        ->join('payplan i','i.payplan_id=a.payplan_id','left')
                        ->where('a.status_cd','verified')
                        ->where('b.status_cd','normal')
                        ->orderby('a.billing_id','DESC')
                        ->groupby('a.billing_id')
                        ->get();
    }

    public function getReport($kasir_status_id) {
        return $this->db->table('billing')
                        ->select('SUM(ttl_amount) as grosssales,SUM(ttl_discount) as ttldiscount')
                        ->where('status_cd','finish')
                        ->where('kasir_status_id',$kasir_status_id)
                        ->get();
    }

    public function getTopitem($kasir_status_id) {
        return $this->db->query("SELECT b.produk_id, SUM(b.qty) AS totalqty, SUM(b.price) as totalprice, c.produk_nm
                                FROM billing a 
                                INNER JOIN billing_item b ON b.billing_id=a.billing_id 
                                INNER JOIN produk c ON c.produk_id=b.produk_id
                                WHERE a.kasir_status_id = '$kasir_status_id'
                                AND a.status_cd = 'finish'
                                AND b.status_cd = 'normal'
                                GROUP BY produk_id 
                                ORDER BY SUM(b.qty) DESC
                                LIMIT 5");
    }

    public function getPayplan($kasir_status_id) {
        return $this->db->query("SELECT a.payplan_id, SUM(a.ttl_amount) AS ttlamount, COUNT(a.billing_id) AS totalpayplan, b.payplan_nm
                                FROM billing a 
                                LEFT JOIN payplan b ON a.payplan_id=b.payplan_id 
                                WHERE a.kasir_status_id = '$kasir_status_id'
                                AND a.status_cd = 'finish'
                                GROUP BY a.payplan_id 
                                ORDER BY SUM(a.ttl_amount) DESC");
    }

    public function simpanopenkasir($data) {
        $this->db->table('kasir_status')
                        ->insert($data);
        return $this->db->insertID();
    }

    public function simpanbilling($data) {
    	$builder = $this->db->table('billing');
        $builder->insert($data);
    	return $this->db->insertID();

    }

    public function simpanbillitem($data) {
    	$builder = $this->db->table('billing_item');
        return $builder->insert($data);
    }

    public function setnullifieditem($id){
        $query = $this->db->table('billing_item');
        $query->set('status_cd','nullified');
        $query->where('billing_item_id',$id);
        return $query->update();
    }

    public function setnormalitem($id){
        $query = $this->db->table('billing_item');
        $query->set('status_cd','normal');
        $query->where('billing_item_id',$id);
        return $query->update();
    }

    public function orderbilling($id,$data) {
        $query = $this->db->table('billing');
        $query->set($data);
        $query->where('billing_id',$id);
        return $query->update();
    }

    public function verifybilling($id,$data){
        $query = $this->db->table('billing');
        $query->set($data);
        $query->where('billing_id',$id);
        return $query->update();
    }

    public function batalbilling($id){
        $query = $this->db->table('billing');
        $query->set('status_cd','cancel');
        $query->where('billing_id',$id);
        return $query->update();
    }

    public function insertbilldisct($data) {
        return $this->db->table('billing_discount')
                    ->insert($data);
    }

    public function insertpoin($data) {
        return $this->db->table('member_poin')
                        ->insert($data);
    }

    public function insertbillmember($id,$data) {
        return $this->db->table('billing')
                        ->set($data)
                        ->where('billing_id',$id)
                        ->update();
    }

    public function updateqty($id,$data) {
        return $this->db->table('billing_item')
                        ->set($data)
                        ->where('billing_item_id',$id)
                        ->update();
    }

    public function updateafterpayment($billing_id,$updatebill) {
        return $this->db->table('billing')
                        ->set($updatebill)
                        ->where('billing_id',$billing_id)
                        ->update();
    }

    public function updateStatusfoodmenu($billing_item_id) {
        return $this->db->table('billing_item')
                        ->set('print_status','printed')
                        ->whereIn('billing_item_id',$billing_item_id)
                        ->update();
    }

    public function updateStatusdrinkmenu($billing_item_id) {
        return $this->db->table('billing_item')
                        ->set('print_status','printed')
                        ->whereIn('billing_item_id',$billing_item_id)
                        ->update();
    }

    public function updatestatuskasir($kasir_status_id,$datastatuskasir) {
        return $this->db->table('kasir_status')
                        ->set($datastatuskasir)
                        ->where('kasir_status_id',$kasir_status_id)
                        ->update();
    }

    public function removedckasir($id,$data){
        return $this->db->table('billing_discount')
                    ->set($data)
                    ->where('billing_discount_id',$id)
                    ->update();
    }

    public function removemember($id,$data) {
        return $this->db->table('billing')
                        ->set($data)
                        ->where('billing_id',$id)
                        ->update();
    }

    public function getbyunclosed() {
        return $this->db->table('billing')
                        ->whereIn('status_cd',['normal','waiting','verified'])
                        ->get();
    }

    public function closedkasir($data) {
        return $this->db->table('billing')
                        ->set($data)
                        ->where('status_cd','finish')
                        ->update();
    }
    
}
