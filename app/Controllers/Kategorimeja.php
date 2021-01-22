<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Kategorimejamodel;
use App\Models\Billingmodel;
use App\Models\Discountmodel;
class Kategorimeja extends BaseController {
	protected $kategorimejamodel;
	protected $billingmodel;
	protected $discountmodel;
	public function __construct(){
		$this->kategorimejamodel = new Kategorimejamodel();
		$this->billingmodel = new Billingmodel();
		$this->discountmodel = new Discountmodel();
	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'kategorimeja',
			'subtitle' => 'kategorimeja',
			'kategorimeja' => $this->kategorimejamodel->getKatbynormal()->getResult()
		];
		$tes = "tes";
		log_message("error",$tes);
		return view('backend/kategorimeja', $data);
	}

	public function save(){
		$kategori_meja_nm = $this->request->getVar('kategori_meja_nm');
		$bykatnm = $this->kategorimejamodel->getbyKatnm($kategori_meja_nm)->getResult();
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'kategori_meja_nm' => $kategori_meja_nm,
			'created_dttm' => $datenow,
			];

			$mejaid = $this->kategorimejamodel->simpan($data);
			if ($mejaid) {
		        return true;
			} else {
				return false;
			}
		}
	}

}
