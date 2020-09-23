<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Payplanmodel;

class Payplan extends BaseController {

	
	protected $payplanmodel;
	protected $session;
	public function __construct(){

		$this->payplanmodel = new Payplanmodel();
		$this->session = \Config\Services::session();
		$this->session->start();

	}

	public function index() {
		$data = [
			'title' => 'payplan',
			'subtitle' => 'payplan',
			'payplan' => $this->payplanmodel->getbyNormal()
		];
		return view('backend/payplan', $data);
	}

	public function tambahdata(){
		$data = [
			'title' => 'Tambah Data payplan',
			'subtitle' => 'Tambah Data payplan'
		];
		return view('backend/addpayplan', $data);
	}

	public function save(){
		$payplan_nm = $this->request->getVar('payplan_nm');
		$bykatnm = $this->payplanmodel->getbyKatnm($payplan_nm);
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'payplan_nm' => $payplan_nm,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$save = $this->payplanmodel->save($data);
			if ($save) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	public function update(){
		$id = $this->request->getVar('id');
		$payplan_nm = $this->request->getVar('payplan_nm');
		
			// $session = \Config\Services::session();
			// $session->start();
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'payplan_nm' => $payplan_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];

			$save = $this->payplanmodel->update($id,$data);
			if ($save) {
				return true;
			} else {
				return false;
			}
		
	}

	public function formedit(){
		$payplan_id = $this->request->getVar('id');
		$res = $this->payplanmodel->find($payplan_id);
		if (count($res)>0) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<input type='hidden' value='".$payplan_id."' class='form-control' id='payplan_id'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama payplan</label>"
	            . "<input type='text' class='form-control' id='payplan_nm' value='".$res['payplan_nm']."'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$payplan_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
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

		$update = $this->payplanmodel->update($id,$data);
		if ($save) {
			return true;
		} else {
			return false;
		}
	}

	

}
