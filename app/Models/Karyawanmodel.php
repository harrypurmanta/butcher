<?php namespace App\Models;

use CodeIgniter\Model;

class Karyawanmodel extends Model
{
    protected $table      = 'person';
    protected $primaryKey = 'person_id';
    protected $allowedFields = ['person_id', 'person_nm','ext_id','ext_id_txt','birth_dttm','birth_place','gender_cd','addr_txt','cellphone','status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';

  


    public function getBynm($person_nm){
        $res = $this->where('person_nm',$person_nm)
                                    ->findAll();
        return $res;
    }

    public function getbyext_id($ext_id){
        return $res = $this->where('ext_id',$ext_id)
                                    ->findAll();
    }

    public function getBylikenm($person_nm) {
        $db = db_connect('default');
        $builder = $db->table('person a');
        $builder->select('*');
        $builder->join('employee b', 'b.person_id = a.person_id','left');
        $builder->like('a.person_nm',$person_nm);
        $query = $builder->get();
        return $query->getResult();
    }

    public function getbyId($id){
        return $this->db->table('person a')
                 ->select('*')
                 ->join('employee b', 'b.person_id = a.person_id','left')
                 ->where('a.person_id',$id)
                 ->get();
    }

    public function simpan($data){
        $db = db_connect('default'); 
        $builder = $db->table('person');

        $builder->insert($data);
        return $db->insertID();
    }
}