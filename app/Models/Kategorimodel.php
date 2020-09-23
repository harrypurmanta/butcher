<?php namespace App\Models;

use CodeIgniter\Model;

class Kategorimodel extends Model
{
    protected $table      = 'kategori_produk';
    protected $primaryKey = 'kategori_id ';
    protected $allowedFields = ['kategori_nm', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $kategorimodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';
    
    
    public function getbyKatnm($kategori_nm) {
        $this->kategorimodel = new Kategorimodel();
    	$kategori_nm = $this->kategorimodel->where('kategori_nm', $kategori_nm)
                            ->findAll();

        return $kategori_nm;
    }

    public function getbyNormal() {
        return $this->db->table('kategori_produk a')
                    ->select('a.kategori_id,a.kategori_nm,b.image_nm,b.image_path,a.status_cd,a.created_dttm,a.created_user')
                    ->join('image b','b.kategori_id=a.kategori_id','left')
                    ->where('a.status_cd','normal')
                    ->groupby('a.kategori_id')
                    ->get();
    }

    public function getOptionbynormal(){
        return $this->db->table('options a')
                ->join('kategori_produk b','b.kategori_id=a.kategori_id')
                ->where('a.status_cd','normal')
                ->get();
    }

    public function getOptionbynm($optnm){
        return $this->db->table('options')->where('option_nm',$optnm)->get();
    }

    public function getOptionbykatid($id){
        return $this->db->table('options a')
                ->join('kategori_produk b','b.kategori_id=a.kategori_id')
                ->where('a.kategori_id',$id)
                ->get();
    }

    public function getOptionbyid($id){
        return $this->db->table('options a')
                ->join('kategori_produk b','b.kategori_id=a.kategori_id')
                ->where('a.option_id',$id)
                ->get();
    }

    public function getimagebyid($id){
        $db = db_connect('default');
        $builder = $db->table('image')->where(['kategori_id',$id]);
        return $builder;
    }

    public function getChildbyprntid($id){
        return $this->db->table('options')->where('parent_id',$id)->get();
    }

    public function getChildbyid($id) {
        return $this->db->table('options')->where('option_id',$id)->get();
    }

    public function simpan($data){
        $builder = $this->db->table('kategori_produk');
        $builder->insert($data);
        return $this->db->insertID();
    }

    public function simpanimage($data){
        $db = db_connect('default'); 
        $builder = $db->table('image');
        $builder->insert($data);
    }

    public function simpanoption($data){
        return $this->db->table('options')->insert($data);
    }

    public function savechild($data){
        return $this->db->table('options')->insert($data);
    }

    public function updateoption($id,$data){
        return $this->db->table('options')->where('option_id',$id)->set($data)->update();
    }

    public function updatechild($id,$data){
        return $this->db->table('options')->where('option_id',$id)->set($data)->update();
    }

    public function hapus($id,$data,$tables,$key){
        return $this->db->table($tables)->where($key,$id)->set($data)->update();
    }
}