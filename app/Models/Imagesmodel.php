<?php namespace App\Models;

use CodeIgniter\Model;

class Imagesmodel extends Model
{
    protected $table      = 'image';
    protected $primaryKey = 'image_id';
    protected $foreignKey = 'kategori_id';
    protected $allowedFields = ['kategori_id','image_nm','image_path','status_cd', 'created_dttm','created_user','update_dttm','update_user','nullified_dttm','nullified_user'];
    protected $imagemodel;
    
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_dttm';
    // protected $updatedField  = 'update_dttm';
    // protected $deletedField  = 'nullified_dttm';
   

    public function getimagebykatid($id){
        $query = $this->db->table($this->table)
                ->where('kategori_id',$id)
                ->where('status_cd','normal')
                ->get();
        return $query;
    }

    public function simpanimage($data){
        $db = db_connect('default'); 
        $builder = $db->table('image');
        $builder->insert($data);
    }

    public function updateimage($id,$dataimg){
    	$query = $this->db->table($this->table)
    			->where('kategori_id',$id)
    			->set($dataimg)
    			->update();
        return $query;
    }
}