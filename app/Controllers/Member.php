<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Membermodel;
use App\Models\Imagesmodel;

class member extends BaseController
{

	
	protected $membermodel;
	protected $imagesmodel;
	protected $session;
	public function __construct(){

		$this->membermodel = new Membermodel();
		$this->imagesmodel = new Imagesmodel();
		$this->session = \Config\Services::session();
		$this->session->start();

	}

	public function index() {
		$data = [
			'title' => 'member',
			'subtitle' => 'member',
			'member' => $this->membermodel->getbyNormal()->getResult()
		];
		return view('backend/member', $data);
	}

	public function formtambah() {
		$ret = "<div class='modal-dialog modal-xl'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Modal Heading</h4>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
                        </div>
                        <form id='forms' method='POST' enctype='multipart/form-data'>
                        <div class='modal-body'>
                            	<div class='row p-t-20'>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Nama Lengkap</label>
                                            <input type='text' id='person_nm' class='form-control' placeholder='Nama Lengkap' required=''>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>No HP</label>
                                            <input type='text' id='cellphone' class='form-control' placeholder='No HP' required=''>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Jenis Kelamin</label>
                                            <select id='gender_cd' class='form-control' required=''>
                                            <option value='l'>Laki-laki</option>
                                            <option value='m'>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Email</label>
                                            <input type='text' id='email' class='form-control' placeholder='example@gmail.com'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>No Identitas</label>
                                            <input type='text' id='ext_id' class='form-control' placeholder='No Identitas'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tempat Lahir</label>
                                            <input type='text' id='birth_place' class='form-control' placeholder='Tempat Lahir'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tanggal Lahir</label>
                                            <input type='date' id='birth_dttm' class='form-control'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Alamat</label>
                                            <textarea id='addr_txt' class='form-control' placeholder='Alamat'></textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class='modal-footer'>
                        	<button onclick='simpan()' type='button' class='btn btn-info waves-effect'>Simpan</button>
                        	<button type='button' class='btn btn-info waves-effect' data-dismiss='modal'>Close</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>";
        return $ret;
	}

	public function formedit(){
		$id = $this->request->getPost('id');
		$res = $this->membermodel->getbyid($id)->getResult();
		if (count($res)>0) {
			foreach ($res as $key) {
				$ret = "<div class='modal-dialog modal-xl'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Modal Heading</h4>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
                        </div>
                        <form id='forms' method='POST' enctype='multipart/form-data'>
                        <div class='modal-body'>
                            	<div class='row p-t-20'>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Nama Lengkap</label>
                                            <input type='text' id='person_nm' class='form-control' value='$key->person_nm' required=''>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>No HP</label>
                                            <input type='text' id='cellphone' class='form-control' value='$key->cellphone' required=''>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Jenis Kelamin</label>
                                            <select id='gender_cd' class='form-control' required=''>
                                            <option ".($key->gender_cd=="l"?"selected='selected'":"")." value='l'>Laki-laki</option>
                                            <option ".($key->gender_cd=="p"?"selected='selected'":"")." value='m'>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Email</label>
                                            <input type='text' id='email' class='form-control' value='$key->email'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>No Identitas</label>
                                            <input type='text' id='ext_id' class='form-control' value='$key->ext_id'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tempat Lahir</label>
                                            <input type='text' id='birth_place' class='form-control' value='$key->birth_place'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tanggal Lahir</label>
                                            <input type='date' id='birth_dttm' class='form-control' value='$key->birth_dttm'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Alamat</label>
                                            <textarea id='addr_txt' class='form-control' placeholder='Alamat'>$key->addr_txt</textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class='modal-footer'>
                        	<button onclick='update($key->person_id)' type='button' class='btn btn-info waves-effect' data-dismiss='modal'>Simpan</button>
                        	<button type='button' class='btn btn-info waves-effect' data-dismiss='modal'>Close</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>";
            }
		} else {
			$ret = 'false';
		}

		return $ret;
	}

	public function tambahdata(){
		$data = [
			'title' => 'Tambah Data member',
			'subtitle' => 'Tambah Data member'
		];
		return view('backend/addmember', $data);
	}

	public function option () {
		$child_nm = array();
		$options = $this->membermodel->getOptionbynormal()->getResult();
		

		$data = [
			'title' => 'Tambah Data Options',
			'subtitle' => 'Tambah Data Options',
			'options' => $options,
			'member' => $this->membermodel->getbyNormal(),
			
		];
		return view('backend/optionmember', $data);
	}

	public function save(){
		$data = [
			'person_nm' => $this->request->getPost('person_nm'),
			'cellphone' => $this->request->getPost('cellphone'),
			'gender_cd' => $this->request->getPost('gender_cd'),
			'email' => $this->request->getPost('email'),
			'ext_id' => $this->request->getPost('ext_id'),
			'birth_place' => $this->request->getPost('birth_place'),
			'birth_dttm' => $this->request->getPost('birth_dttm'),
			'addr_txt' => $this->request->getPost('addr_txt'),
			'created_dttm' => date('Y-m-d H:i:s'),
			'created_user' => $this->session->user_id
			];

			$person_id = $this->membermodel->insertperson($data);
			if ($person_id != "") {
				$empdata = [
					'person_id' => $person_id,
					'status_cd' => 'normal',
					'created_dttm' => date('Y-m-d H:i:s'),
					'created_user' => $this->session->user_id
				];

				$emp = $this->membermodel->insertmember($empdata);

				if ($emp) {
					$ret = "true";
				} else {
					$ret = "false";
				}
				
			} else {
				$ret = "false";
			}

		return $ret;
	}

	public function saveoption(){
		$option_nm = $this->request->getVar('option_nm');
		$member_id = $this->request->getVar('member_id');
		$byoption_nm = $this->membermodel->getOptionbynm($option_nm)->getresultArray();
		if (count($byoption_nm)>0) {
			return 'already';
		} else {
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'option_nm' => $option_nm,
			'member_id' => $member_id,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$saveoption = $this->membermodel->simpanoption($data);
			if ($saveoption) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	public function savechild(){
		$parent_id = $this->request->getPost('id');
		$child_nm = $this->request->getPost('child_nm');
		$datenow = date('Y-m-d H:i:s');
		$data = [
		'parent_id' => $parent_id,
		'option_nm' => $child_nm,
		'created_dttm' => $datenow,
		'created_user' => $this->session->user_id
		];

		$savechild = $this->membermodel->savechild($data);
		if ($savechild) {
			return 'true';
		} else {
			return 'false';
		}
		
	}

	public function update(){
		$id = $this->request->getPost('id');
		$res = $this->membermodel->getbyid($id)->getResult();
		if (count($res)>0) {
			$data = [
				'person_nm' => $this->request->getPost('person_nm'),
				'cellphone' => $this->request->getPost('cellphone'),
				'gender_cd' => $this->request->getPost('gender_cd'),
				'email' => $this->request->getPost('email'),
				'ext_id' => $this->request->getPost('ext_id'),
				'birth_place' => $this->request->getPost('birth_place'),
				'birth_dttm' => $this->request->getPost('birth_dttm'),
				'addr_txt' => $this->request->getPost('addr_txt'),
				'updated_dttm' => date('Y-m-d H:i:s'),
				'updated_user' => $this->session->user_id
			];

			$person_id = $this->membermodel->updateperson($id,$data);
			if ($person_id != "") {
				$empdata = [
					'updated_dttm' => date('Y-m-d H:i:s'),
					'updated_user' => $this->session->user_id
				];

				$emp = $this->membermodel->updatemember($id,$empdata);
				if ($emp) {
					$ret = "true";
				} else {
					$ret = "false1";
				}
				
			} else {
				$ret = "false2";
			}
		} else {
			$ret = "false3";
		}
		return $ret;
	}

	public function updateoption(){
		$option_nm = $this->request->getPost('option_nm');
		$option_id = $this->request->getPost('option_id');
		$member_id = $this->request->getPost('member_id');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'member_id' => $member_id,
			'option_nm' => $option_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
		];
		$save = $this->membermodel->updateoption($option_id,$data);
		if ($save) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function updatechild(){
		$id = $this->request->getPost('id');
		$child_nm = $this->request->getPost('child_nm');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'option_nm' => $child_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
		];
		$update = $this->membermodel->updatechild($id,$data);
		if ($update) {
			return 'true';
		} else {
			return 'false';
		}
	}

	

	public function formeditoption(){
		$option_id = $this->request->getVar('id');
		$res 	   = $this->membermodel->getOptionbyid($option_id)->getResult();
		$getkat    = $this->membermodel->getbyNormal();
		$opt = "";
		foreach ($getkat as $kopt) {
			$opt .= "<option value='".$kopt['member_id']."' ".($kopt['member_id']==$res[0]->member_id?"selected='selected'":"").">".$kopt['member_nm']."</option>"; 
		}
		 

		foreach ($res as $key) {
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$option_id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>member Name</label>"
	            . "<select class='form-control' name='member_id' id='member_id2'>";
	        $ret .= $opt;
	        $ret .= "</select>"
	            . "</div>"
	            . "</div>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Name :</label>"
                . "<input type='text' name='option_nm' id='option_nm2' class='form-control' value='$key->option_nm' required/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$option_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
		}
				
	         return $ret;
	}

	public function formaddchild(){
		$id = $this->request->getPost('id');
		$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Child Name :</label>"
                . "<input type='text' name='child_nm' id='child_nm' class='form-control' required/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpanchild(".$id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";

	    return $ret;
	
	}

	public function formeditchild(){
		$id = $this->request->getPost('id');
		$res = $this->membermodel->getChildbyid($id)->getResult();
		foreach ($res as $k) {
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Child Name :</label>"
                . "<input type='text' name='child_nm' id='child_nm2' class='form-control' required value='$k->option_nm'/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='updatechild(".$id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
		}
		

	    return $ret;
	
	}

	public function hapus(){
		$id = $this->request->getVar('id');
		
		$datenow = date('Y-m-d H:i:s');
		$data = [
		'status_cd' => 'nullified',
		'nullified_dttm' => $datenow,
		'nullified_user' => $this->session->user_id
		];

		$update = $this->membermodel->hapus($id,$data);
		$this->membermodel->hapusmember($id,$data);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}
}
