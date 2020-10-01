<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Users extends BaseController
{
	protected $usersmodel;
	public function __construct(){
		$this->usersmodel = new Usersmodel();

	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		return view('login');
	}

	public function save(){
		$user_nm 	= $this->request->getVar('user_nm');
		$pwd0 		= md5($this->request->getVar('pwd0'));
		$id 		= $this->request->getVar('id');

		$session = \Config\Services::session();
		$session->start();
		$datenow = date('Y-m-d H:i:s');
		$users = $this->usersmodel->getbyUsernm($user_nm);
		if (count($users)>0) {
			$ret = 'already';
		} else {
			$data = [
			'user_nm' => $user_nm,
			'pwd0' => $pwd0,
			'person_id' => $id,
			'created_dttm' => $datenow,
			'created_user' => $session->user_id
			];
			$saveUsers = $this->usersmodel->save($data);
			if ($saveUsers) {
				$ret = true;
			} else {
				$ret = false;
			}
			
		}
		
	}
}
