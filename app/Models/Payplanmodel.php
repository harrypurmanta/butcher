<?php namespace App\Models;

use CodeIgniter\Model;

class Payplanmodel extends Model
{
    protected $table      = 'payplan';
    protected $primaryKey = 'payplan_id ';
    protected $allowedFields = ['payplan_nm','payplan_cd','description', 'status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $payplanmodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';
    

    public function getbyKatnm($payplan_nm) {
        $this->payplanmodel = new Payplanmodel();
        
    	$payplan_nm = $this->payplanmodel->where('payplan_nm', $payplan_nm)
                            ->findAll();

        return $payplan_nm;
    }

    public function getbyNormal() {
        $this->payplanmodel = new Payplanmodel();
        
        $payplan = $this->payplanmodel->where('status_cd', 'normal')
                            ->findAll();

        return $payplan;
    }
}