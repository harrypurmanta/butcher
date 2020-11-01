<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Karyawanmodel;
use App\Models\Employeemodel;
use App\Models\Usersmodel;
class Karyawan extends BaseController
{

	
	protected $karyawanmodel;
	protected $employeemodel;
	protected $usersmodel;
	public function __construct(){
		$this->usersmodel = new Usersmodel();
		$this->karyawanmodel = new Karyawanmodel();
		$this->employeemodel = new Employeemodel();

	}

	public function index() {
        if (session()->get('user_nm') == "") {
            session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
            return redirect()->to(base_url('/'));
        }
		$data = [
			'title' => 'Karyawan',
			'subtitle' => 'Karyawan',
		];
		return view('backend/karyawan',$data);
	}

	public function formdaftarkaryawan() {
		if (session()->get('user_nm') == "") {
            session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
            return redirect()->to(base_url('/'));
        }

		$data = [
			'title' => 'Karyawan',
			'subtitle' => 'Karyawan',
			'id' => $this->request->uri->getSegment(3)
		];

		return view('backend/formdaftarkaryawan',$data);
	}

	public function cariByname(){
		$person_nm = $this->request->getVar('person_nm');
		$karyawan = $this->karyawanmodel->getBylikenm($person_nm);
		if (count($karyawan)>0) {
			$ret = "";
		      foreach ($karyawan as $key) {
		        $ret .= "<a onclick='clickpatient($key->person_id)'>"
		                . "<div style='background-color:yellow;padding:5px;border-radius:10px;margin-top: 5px;margin-bottom: 5px; border-left: 4px solid #ccc;'>"
		                . "<p style='display: inline-block; font-size: 14px;font-weight: bold;margin-left:3px;margin-bottom: 0;'>".$key->person_nm."</p>"
		                . "<p style='float:right;display: inline-block;font-size: 14px;font-weight: bold;margin-left:3px;margin-bottom: 0;'>".$key->ext_id."</p>"
		                . "<p style='font-size: 12px;margin-left:3px;margin-bottom: 0;'>".$key->addr_txt."</p>"
		                . "<p style='font-size: 12px;margin-left:3px;margin-bottom: 0;'>".$key->birth_dttm."</p>"
		                . "</div>"
		                . "</a>";
		      }
			
		} else {
			$ret = "<a>"
                . "<div style='background-color:yellow;padding:5px;border-radius:10px;margin-top: 5px;margin-bottom: 5px; border-left: 4px solid #ccc;'>"
                . "<p style='display: inline-block; font-size: 14px;font-weight: bold;margin-left:3px;margin-bottom: 0;'>DATA KARYAWAN TIDAK ADA . . .</p>"
                . "</div>"
                . "</a>";
		}
		return $ret;

	}

	public function save(){
		$person_id 		= $this->request->getPost('person_id');
		$person_nm 		= $this->request->getPost('person_nm');
		$ext_id 		= $this->request->getPost('ext_id');
		$gender_cd 		= $this->request->getPost('gender_cd');
		$birth_dttm 	= $this->request->getPost('birth_dttm');
		$birth_place	= $this->request->getPost('birth_place');
		$cellphone 		= $this->request->getPost('cellphone');
		$addr_txt 		= $this->request->getPost('addr_txt');
		$ext_idx 		= $this->karyawanmodel->getbyext_id($ext_id);
		
			$datenow = date('Y-m-d H:i:s');
			
			if ($person_id=="") {
				$data = [
					'person_nm' => $person_nm,
					'ext_id' => $ext_id,
					'gender_cd' => $gender_cd,
					'birth_dttm' => $birth_dttm,
					'birth_place' => $birth_place,
					'cellphone' => $cellphone,
					'addr_txt' => $addr_txt,
					'created_dttm' => $datenow,
					'created_user' => $this->session->user_id
					];
				$person_id = $this->karyawanmodel->simpan($data);
				if ($person_id !='') {
					$dataemployee = [
					'person_id' => $person_id,
					'created_dttm' => $datenow,
					'created_user' => $this->session->user_id
					];
					$saveEmp = $this->employeemodel->save($dataemployee);
					echo $person_id;
				} else {
					return false;
				}
			} else {
				$data = [
				'person_nm' => $person_nm,
				'ext_id' => $ext_id,
				'gender_cd' => $gender_cd,
				'birth_dttm' => $birth_dttm,
				'birth_place' => $birth_place,
				'cellphone' => $cellphone,
				'addr_txt' => $addr_txt,
				'updated_dttm' => $datenow,
				'updated_user' => $this->session->user_id
				];
				$update = $this->karyawanmodel->update($person_id,$data);
				if ($update) {
					echo $person_id;
				} else {
					return false;
				}
			}
			
			
	}

	public function profiletab(){
		$id = $this->request->getPost('id');
        if ($id == "") {
            $ret = "<div class='p-20'>"
                    . "<form action='#' class='form-horizontal'>"
                    . "<div class='form-body'>"
                    . "<h3 class='box-title'>Person Info</h3>"
                    . "<hr class='m-t-0 m-b-40'>"
                    . "<div class='row'>"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Nama Lengkap</label>"
                    . "<div class='col-md-9'>"
                    . "<input type='hidden' value='$id' id='person_id'/>"
                    . "<input type='text' class='form-control' id='person_nm'>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Nomor Identitas</label>"
                    . "<div class='col-md-9'>"
                    . "<input type='text' class='form-control' id='ext_id'>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "</div>"
                    . "<!--/row-->"
                    . "<div class='row'>"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Jenis Kelamin</label>"
                    . "<div class='col-md-9'>"
                    . "<select class='form-control custom-select' id='gender_cd'>"
                    . "<option value='m'>Laki-laki</option>"
                    . "<option value='f'>Perempuan</option>"
                    . "</select>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Tanggal Lahir</label>"
                    . "<div class='col-md-9'>"
                    . "<input type='date' class='form-control' id='birth_dttm'>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "</div>"
                    . "<!--/row-->"
                    . "<div class='row'>"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Tempat Lahir</label>"
                    . "<div class='col-md-9'>"
                    . "<input type='text' class='form-control' id='birth_place'>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "<div class='col-md-6'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>No. Telp</label>"
                    . "<div class='col-md-9'>"
                    . "<input type='text' class='form-control' id='cellphone'>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<!--/span-->"
                    . "</div>"
                    . "<div class='row'>"
                    . "<div class='col-md-9'>"
                    . "<div class='form-group row'>"
                    . "<label class='control-label text-right col-md-3'>Alamat</label>"
                    . "<div class='col-md-9'>"
                    . "<textarea type='text' class='form-control' id='addr_txt'></textarea>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<hr>"
                    . "<div class='form-actions'>"
                    . "<div class='row'>"
                    . "<div class='col-md-6'>"
                    . "<div class='row'>"
                    . "<div class='col-md-offset-3 col-md-9'>"
                    . "<button onclick='simpan()' type='button' class='btn btn-success'>Submit</button> " 
                    . "<button type='button' class='btn btn-inverse'>Cancel</button>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
                    . "<div class='col-md-6'> </div>"
                    . "</div>"
                    . "</div>"
                    . "</form>"
                    . "</div>";
        } else {
            $res = $this->karyawanmodel->getbyId($id)->getResult();
            if (count($res)>0) {
                foreach ($res as $key) {
                list($dt,$dd) = explode(' ',$key->birth_dttm);
                $newDate = date("m-d-Y", strtotime($dt));
                $date = str_replace('-','/',$newDate);

                $ret = "<div class='p-20'>"
                        . "<form action='#' class='form-horizontal'>"
                        . "<div class='form-body'>"
                        . "<h3 class='box-title'>Person Info</h3>"
                        . "<hr class='m-t-0 m-b-40'>"
                        . "<div class='row'>"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Nama Lengkap</label>"
                        . "<div class='col-md-9'>"
                        . "<input type='hidden' value='$id' id='person_id'/>"
                        . "<input type='text' class='form-control' id='person_nm' value='$key->person_nm'>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Nomor Identitas</label>"
                        . "<div class='col-md-9'>"
                        . "<input type='text' class='form-control' id='ext_id' value='$key->ext_id'>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "</div>"
                        . "<!--/row-->"
                        . "<div class='row'>"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Jenis Kelamin</label>"
                        . "<div class='col-md-9'>"
                        . "<select class='form-control custom-select' id='gender_cd'>"
                        . "<option value='m'>Laki-laki</option>"
                        . "<option value='f'>Perempuan</option>"
                        . "</select>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Tanggal Lahir</label>"
                        . "<div class='col-md-9'>"
                        . "<span class='control-label'>$date</span>"
                        . "<input type='date' class='form-control' id='birth_dttm' value='$date'>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "</div>"
                        . "<!--/row-->"
                        . "<div class='row'>"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Tempat Lahir</label>"
                        . "<div class='col-md-9'>"
                        . "<input type='text' class='form-control' id='birth_place' value='$key->birth_place'>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "<div class='col-md-6'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>No. Telp</label>"
                        . "<div class='col-md-9'>"
                        . "<input type='text' class='form-control' id='cellphone' value='$key->cellphone'>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<!--/span-->"
                        . "</div>"
                        . "<div class='row'>"
                        . "<div class='col-md-9'>"
                        . "<div class='form-group row'>"
                        . "<label class='control-label text-right col-md-3'>Alamat</label>"
                        . "<div class='col-md-9'>"
                        . "<textarea type='text' class='form-control' id='addr_txt'>$key->addr_txt</textarea>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<hr>"
                        . "<div class='form-actions'>"
                        . "<div class='row'>"
                        . "<div class='col-md-6'>"
                        . "<div class='row'>"
                        . "<div class='col-md-offset-3 col-md-9'>"
                        . "<button onclick='simpan()' type='button' class='btn btn-success'>Submit</button> " 
                        . "<button type='button' class='btn btn-inverse'>Cancel</button>"
                        . "</div>"
                        . "</div>"
                        . "</div>"
                        . "<div class='col-md-6'> </div>"
                        . "</div>"
                        . "</div>"
                        . "</form>"
                        . "</div>";
                }
            } else {
                $ret = "false";
            }   
        }
        return $ret;
	}

	public function accounttab(){
		$id = $this->request->getPost('id');
		$res = $this->usersmodel->getbyId($id)->getResult();
		$ret = "";
	foreach ($res as $key) {
		if ($key->user_nm!='') {
		$ret = "<div class='p-20'>"
                . "<form action='#' class='form-horizontal'>"
                . "<div class='form-body'>"
                . "<h3 class='box-title'>Person Info</h3>"
                . "<hr class='m-t-0 m-b-40'>"
                . "<div class='row'>"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Username</label>"
                . "<div class='col-md-9'>"
                . "<input type='hidden' value='$id' id='person_id'/>"
                . "<input type='hidden' value='$key->user_id' id='user_id'/>"
                . "<input type='text' class='form-control' id='user_nm' value='$key->user_nm'>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Password</label>"
                . "<div class='col-md-9'>"
                . "<input type='hidden' id='oldpassword' value='$key->pwd0'/>"
                . "<input type='password' class='form-control' id='pwd0' value='$key->pwd0'>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Level</label>"
                . "<div class='col-md-9'>"
                . "<select  class='form-control' id='user_group'>"
                . "<option ".($key->user_group=='owner'?"selected='selected'":"")." value='owner'>Owner</option>"
                . "<option ".($key->user_group=='manajer'?"selected='selected'":"")." value='manajer'>Manajer</option>"
                . "<option ".($key->user_group=='kasir'?"selected='selected'":"")." value='kasir'>Kasir</option>"
                . "<option ".($key->user_group=='waiters'?"selected='selected'":"")." value='waiters'>Waiters</option>"
                . "</select>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "</div>"
                . "</div>"
                . "<hr>"
                . "<div class='form-actions'>"
                . "<div class='row'>"
                . "<div class='col-md-6'>"
                . "<div class='row'>"
                . "<div class='col-md-offset-3 col-md-9'>"
                . "<button onclick='simpanuser($id)' type='button' class='btn btn-success'>Submit</button> " 
                . "<button type='button' class='btn btn-inverse'>Cancel</button>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<div class='col-md-6'> </div>"
                . "</div>"
                . "</div>"
                . "</form>"
                . "</div>";
        	
			} else {
				$ret = "<button onclick='formtambahuser($id)'>Tambah User</button>";
			}
		}
         return $ret;
        
	}

	public function formtambahuser(){
		$id = $this->request->getPost('id');

		$ret = "<div class='p-20'>"
                . "<form action='#' class='form-horizontal'>"
                . "<div class='form-body'>"
                . "<h3 class='box-title'>Person Info</h3>"
                . "<hr class='m-t-0 m-b-40'>"
                . "<div class='row'>"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Username</label>"
                . "<div class='col-md-9'>"
                . "<input type='hidden' value='$id' id='person_id'/>"
                . "<input type='text' class='form-control' id='user_nm'>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Password</label>"
                . "<div class='col-md-9'>"
                . "<input type='password' class='form-control' id='pwd0'>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "<div class='col-md-6'>"
                . "<div class='form-group row'>"
                . "<label class='control-label text-right col-md-3'>Level</label>"
                . "<div class='col-md-9'>"
                . "<select  class='form-control' id='user_group'>"
                . "<option value='owner'>Owner</option>"
                . "<option value='manajer'>Manajer</option>"
                . "<option value='kasir'>Kasir</option>"
                . "<option value='waiters'>Waiters</option>"
                . "</select>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<!--/span-->"
                . "</div>"
                . "</div>"
                . "<hr>"
                . "<div class='form-actions'>"
                . "<div class='row'>"
                . "<div class='col-md-6'>"
                . "<div class='row'>"
                . "<div class='col-md-offset-3 col-md-9'>"
                . "<button onclick='simpanuser($id)' type='button' class='btn btn-success'>Submit</button> " 
                . "<button type='button' class='btn btn-inverse'>Cancel</button>"
                . "</div>"
                . "</div>"
                . "</div>"
                . "<div class='col-md-6'> </div>"
                . "</div>"
                . "</div>"
                . "</form>"
                . "</div>";
        
		
         return $ret;
        
	}

}
