<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Discountmodel;
use App\Models\Billingmodel;

class Discount extends BaseController
{

	
	protected $discountmodel;
	protected $billingmodel;
	protected $session;
	public function __construct(){

		$this->discountmodel = new Discountmodel();
		$this->billingmodel = new Billingmodel();

	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Discount',
			'subtitle' => 'Discount',
			'discount' => $this->discountmodel->getbyNormal()->getResult()
		];
		return view('backend/discount', $data);
	}

	public function tambahdata(){
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Tambah Data discount',
			'subtitle' => 'Tambah Data discount'
		];
		return view('backend/adddiscount', $data);
	}

	public function save(){
		$discount_nm = $this->request->getPost('discount_nm');
		$bykatnm = $this->discountmodel->getbyKatnm($discount_nm)->getResult();
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'discount_nm' => $discount_nm,
			'value' => $this->request->getPost('nilaidiscount'),
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$save = $this->discountmodel->save($data);
			if ($save) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	public function update(){
		$id = $this->request->getVar('id');
		$discount_nm = $this->request->getVar('discount_nm');
		
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'discount_nm' => $discount_nm,
			'value' => $this->request->getPost('nilaidiskon'),
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];

			$save = $this->discountmodel->update($id,$data);
			if ($save) {
				return true;
			} else {
				return false;
			}
		
	}

	public function formedit(){
		$discount_id = $this->request->getPost('id');
		$res = $this->discountmodel->getbyid($discount_id)->getResult();
		if (count($res)>0) {
			foreach ($res as $key) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<input type='hidden' value='".$discount_id."' class='form-control' id='discount_id'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama discount</label>"
	            . "<input type='text' class='form-control' id='discount_nm' value='$key->discount_nm'>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label class='control-label'>Nilai discount</label>"
	            . "<input type='text' class='form-control' id='nilaidiskon' value='$key->value'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$discount_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
			}
				
	         return $ret;
		} else {
			
			return 'false';
		}
	}

	

	public function memberkasir() {
		$id = $this->request->getPost('id');
		$res = $this->discountmodel->getmemberbynormal()->getResult();
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan Pilih Diskon</h4>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div><table  width='100%'>";
	            foreach ($res as $key) {
	            $ret .= "<tr style='border-bottom: 1px solid #ccc; line-height: 60px; font-size: 25px; font-weight: bold;'>"
	            	 . "<td align='left'><button onclick='addDiscount($id,$key->discount_id)' class='btn btn-outline-primary' style='font-size: 20px; color: black; font-weight: bold;'>$key->discount_nm</button></td>"
	            	 . "<td align='right'>$key->value</td>"
	            	 . "</tr>";
	            }
	       $ret .= "</table></div>"
	       		. "</div>"
	            . "</div>"
	            . "</div>";
		
		return $ret;
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

		$update = $this->discountmodel->update($id,$data);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}

	

}
