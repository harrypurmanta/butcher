<?php namespace App\Models;

use CodeIgniter\Model;

class Produkmodel extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'produk_id ';
    protected $allowedFields = ['produk_nm','kategori_id','produk_harga', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $produkmodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';
    


    public function getbyKatnm($produk_nm) {
        return $this->db->table('produk')
    	                ->where('produk_nm', $produk_nm)
                        ->get();
    }

    public function getbyNormal() {
        return $this->db->table('produk a')
                        ->select('a.produk_id,a.produk_nm,a.created_dttm,b.kategori_nm,c.user_nm,a.status_cd,a.produk_harga')
                        ->join('kategori_produk b', 'b.kategori_id = a.kategori_id','left')
                        ->join('users c', 'c.user_id = a.created_user','left')
                        ->where('a.status_cd','normal')
                        ->orderby('a.kategori_id','ASC')
                        ->get();
    }

    public function getbyId($id){
        return $this->db->table('produk a')
                        ->select('a.produk_id,a.produk_nm,a.created_dttm,b.kategori_nm,c.user_nm,a.status_cd,a.produk_harga')
                        ->join('kategori_produk b', 'b.kategori_id = a.kategori_id','left')
                        ->join('users c', 'c.user_id = a.created_user','left')
                        ->where('a.status_cd','normal')
                        ->where('a.produk_id',$id)
                        ->get();
    }

    public function getbyKatId($id){
        return $this->db->table('produk a')
                        ->select('a.produk_id,a.produk_nm,a.created_dttm,a.status_cd,a.produk_harga,a.kategori_id')
                        ->where('a.status_cd','normal')
                        ->where('a.kategori_id',$id)
                        ->get();
    }

    public function simpan($data) {
        return $this->db->table('produk')
                        ->insert($data);
    }

}