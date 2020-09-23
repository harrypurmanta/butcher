<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Membertypemodel;

class Membertype extends BaseController {

	
	protected $membertypemodel;
	protected $session;
	public function __construct(){

		$this->membertypemodel = new Membertypemodel();
		$this->session = \Config\Services::session();
		$this->session->start();

	}

	public function index() {
		$data = [
			'title' => 'membertype',
			'subtitle' => 'membertype',
			'membertype' => $this->membertypemodel->getbyNormal()
		];
		return view('backend/membertype', $data);
	}

	public function tambahdata(){
		$data = [
			'title' => 'Tambah Data membertype',
			'subtitle' => 'Tambah Data membertype'
		];
		return view('backend/addmembertype', $data);
	}

	public function save(){
		$membertype_nm = $this->request->getVar('membertype_nm');
		$bykatnm = $this->membertypemodel->getbyKatnm($membertype_nm);
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'membertype_nm' => $membertype_nm,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$save = $this->membertypemodel->save($data);
			if ($save) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	public function update(){
		$id = $this->request->getVar('id');
		$membertype_nm = $this->request->getVar('membertype_nm');
		
			// $session = \Config\Services::session();
			// $session->start();
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'membertype_nm' => $membertype_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];

			$save = $this->membertypemodel->update($id,$data);
			if ($save) {
				return true;
			} else {
				return false;
			}
		
	}

	public function formedit(){
		$membertype_id = $this->request->getVar('id');
		$res = $this->membertypemodel->find($membertype_id);
		if (count($res)>0) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<input type='hidden' value='".$membertype_id."' class='form-control' id='membertype_id'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama membertype</label>"
	            . "<input type='text' class='form-control' id='membertype_nm' value='".$res['membertype_nm']."'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$membertype_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
	         return $ret;
		} else {
			
			return 'false';
		}
	}

	public function hapus(){
		$id = $this->request->getVar('id');
		$session = \Config\Services::session();
		$session->start();
		$datenow = date('Y-m-d H:i:s');
		$data = [
		'status_cd' => 'nullified',
		'nullified_dttm' => $datenow,
		'nullified_user' => $this->session->user_id
		];

		$update = $this->membertypemodel->update($id,$data);
		if ($save) {
			return true;
		} else {
			return false;
		}
	}

	

}
