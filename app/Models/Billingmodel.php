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

    public function getbyMejaid($id){
    	$query = $this->db->table('billing a');
    	$query->select('b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.billing_id,a.status_cd as statusbilling');
    	$query->join('billing_item b','b.billing_id=a.billing_id');
    	$query->join('produk c','c.produk_id=b.produk_id');
    	$query->join('kategori_produk d','d.kategori_id=c.kategori_id');
    	$query->whereIn('a.status_cd',['normal','waiting','verified']);
    	$query->where('a.meja_id',$id);
    	return $query->get();
    }

    public function getbyMejaidcustomer($id){
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm,h.person_nm as collected_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->join('person h','h.person_id=a.verified_user','left');
        $query->whereIn('a.status_cd',['verified','waiting','normal']);
        $query->where('b.status_cd','normal');
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function getbyMejaidkasir($id){
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm,g.person_nm,h.person_nm as collected_nm,i.person_nm as member_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('member e','e.member_id=a.member_id','left');
        $query->join('person i','i.person_id=e.person_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->join('person g','g.person_id=e.person_id','left');
        $query->join('person h','h.person_id=a.verified_user','left');
        $query->where('a.status_cd','verified');
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
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->where('a.status_cd','verified');
        $query->where('b.status_cd','normal');
        $query->whereIn('d.kategori_id',[7,8,9,10,11]);
        $query->where('a.meja_id',$id);
        return $query->get();
    }

    public function getfoodmenu($id) {
        $query = $this->db->table('billing a');
        $query->select('a.billing_id,a.created_dttm,a.status_cd as statusbilling,b.qty,c.produk_id,c.produk_nm,c.produk_harga,b.status_cd,b.billing_item_id,a.member_id,f.meja_nm');
        $query->join('billing_item b','b.billing_id=a.billing_id','left');
        $query->join('produk c','c.produk_id=b.produk_id','left');
        $query->join('kategori_produk d','d.kategori_id=c.kategori_id','left');
        $query->join('meja f','f.meja_id=a.meja_id','left');
        $query->where('a.status_cd','verified');
        $query->where('b.status_cd','normal');
        $query->whereIn('d.kategori_id',[1,2,3,4,5,6]);
        $query->where('a.meja_id',$id);
        return $query->get();
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

    public function getKategoriid(){

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

    public function insertbillmember($id,$data) {
        return $this->db->table('billing')
                        ->set($data)
                        ->where('billing_id',$id)
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
    
}
