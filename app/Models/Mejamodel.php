<?php namespace App\Models;

use CodeIgniter\Model;

class Mejamodel extends Model
{
    protected $table      = 'meja';
    protected $primaryKey = 'meja_id';
    protected $allowedFields = ['meja_nm','qrpicture','qrurl', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $mejamodel;
    
    public function getbyKatnm($meja_nm) {
        return $this->db->table($this->table)
                        ->where('meja_nm',$meja_nm)
                        ->get();
    }

    public function getbyNormal() {
        return $this->db->table('meja a')
                    ->select('*')
                    ->join('imagemeja b','b.meja_id=a.meja_id','left')
                    ->where('a.status_cd','normal')
                    ->get();
    }

    public function getbyid($id) {
        return $this->db->table('meja')
                        ->where('meja_id',$id)
                        ->get();
    }

    public function simpan($data) {
               $this->db->table('meja')
                        ->insert($data);
        return $this->db->insertID();
    }

    public function simpanqr($data) {
        $query = $this->db->table('imagemeja');
        return $query->insert($data);
    }
}