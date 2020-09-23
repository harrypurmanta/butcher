<?php namespace App\Models;

use CodeIgniter\Model;

class Mejamodel extends Model
{
    protected $table      = 'meja';
    protected $primaryKey = 'meja_id';
    protected $allowedFields = ['meja_nm','qrpicture','qrurl', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $mejamodel;
    
    public function getbyKatnm($meja_nm) {
        $this->mejamodel = new Mejamodel();
        
    	$meja_nm = $this->mejamodel->where('meja_nm', $meja_nm)
                            ->findAll();

        return $meja_nm;
    }

    public function getbyNormal() {
        $query = $this->db->table('meja a');
        $query->select('*');
        $query->join('imagemeja b','b.meja_id=a.meja_id');
        $query->where('a.status_cd','normal');
        return $query->get();
    }

    public function simpan($data) {
        $builder = $this->db->table('meja');
        $builder->insert($data);
        return $this->db->insertID();
    }

    public function simpanqr($data) {
        $query = $this->db->table('imagemeja');
        return $query->insert($data);
    }
}