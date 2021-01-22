<?php namespace App\Models;

use CodeIgniter\Model;

class Kategorimejamodel extends Model
{
    protected $table      = 'kategori_meja';
    protected $primaryKey = 'kategori_meja_id';
    protected $allowedFields = ['kategori_meja_nm','status_cd', 'created_dttm'];
    protected $kategorimejamodel;
    
    public function getbyKatnm($kategorimeja_nm) {
        return $this->db->table($this->table)
                        ->where('kategori_meja_nm',$kategorimeja_nm)
                        ->get();
    }

    public function getKatbynormal() {
        return $this->db->table('kategori_meja')
                    ->where('status_cd','normal')
                    ->get();
    }

    public function simpan($data) {
        return $this->db->table('kategori_meja')
                        ->insert($data);
    }

}