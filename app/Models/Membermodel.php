<?php namespace App\Models;

use CodeIgniter\Model;

class membermodel extends Model
{
    protected $table      = 'member';
    protected $primaryKey = 'member_id ';
    protected $allowedFields = ['member_nm','person_id', 'status_cd', 'created_dttm','created_user','updated_dttm','updated_user','nullified_dttm','nullified_user'];
    
    public function getbynormal() {
        return $this->db->table('member a')
        			->select('*')
        			->join('person b','b.person_id=a.person_id')
        			->where('a.status_cd','normal')
                    ->where('b.status_cd','normal')
                    ->orderby('b.person_nm','ASC')
        			->get();
    }

    public function getbyid($id) {
        return $this->db->table('member a')
                    ->select('*')
                    ->join('person b','b.person_id=a.person_id')
                    ->where('a.status_cd','normal')
                    ->where('a.person_id',$id)
                    ->get();
    }


    public function insertperson($data) {
        $this->db->table('person')
                 ->insert($data);
        return $this->db->insertID();
    }

    public function insertmember($data) {
        return $this->db->table('member')
                    ->insert($data);
    }

    public function updateperson($id,$data) {
        return $this->db->table('person')
                    ->set($data)
                    ->where('person_id',$id)
                    ->update();
    }

    public function updatemember($id,$data) {
        return $this->db->table('member')
                    ->set($data)
                    ->where('person_id',$id)
                    ->update();
    }

    public function hapus($id,$data) {
        return $this->db->table('person')
                    ->set($data)
                    ->where('person_id',$id)
                    ->update();
    }

    public function hapusmember($id,$data) {
        return $this->db->table('member')
                    ->set($data)
                    ->where('person_id',$id)
                    ->update();
    }

}