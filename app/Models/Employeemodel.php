<?php namespace App\Models;

use CodeIgniter\Model;

class Employeemodel extends Model
{
    protected $table      = 'employee';
    protected $primaryKey = 'employee_id';
    protected $allowedFields = ['employee_id','employee_ext_id','person_id','alias_nm','status_cd', 'created_dttm','created_user','nullified_dttm','nullified_user'];
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';

}