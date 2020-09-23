<?php namespace App\Models;

use CodeIgniter\Model;

class Membertypemodel extends Model
{
    protected $table      = 'membertype';
    protected $primaryKey = 'membertype_id ';
    protected $allowedFields = ['membertype_nm','membertype_cd','description', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $membertypemodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';
    

    public function getbyKatnm($membertype_nm) {
        $this->membertypemodel = new Membertypemodel();
        
    	$membertype_nm = $this->membertypemodel->where('membertype_nm', $membertype_nm)
                            ->findAll();

        return $membertype_nm;
    }

    public function getbyNormal() {
        $this->membertypemodel = new Membertypemodel();
        
        $membertype = $this->membertypemodel->where('status_cd', 'normal')
                            ->findAll();

        return $membertype;
    }
}