<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Users extends BaseController
{
	protected $usersmodel;
	public function __construct(){
		$this->usersmodel = new Usersmodel();
		$session = \Config\Services::session();
		$session->start();

	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		return view('login');
	}

	public function save(){
		$user_nm 		= $this->request->getPost('user_nm');
		$pwd0 			= md5($this->request->getPost('pwd0'));
		$id 			= $this->request->getPost('id');
		$oldpassword 	= $this->request->getPost('oldpassword');
		$user_group 	= $this->request->getPost('user_group');
		$user_id 		= $this->request->getPost('user_id');
		if ($user_id == "") {
			$data = [
				'user_nm' => $user_nm,
				'pwd0' => $pwd0,
				'person_id' => $id,
				'user_group' => $user_group,
				'created_dttm' => date('Y-m-d H:i:s'),
				'created_user' => $this->session->user_id
			];
			$saveUsers = $this->usersmodel->save($data);
		} else {
			if ($oldpassword == $this->request->getPost('pwd0')) {
				$data = [
					'user_nm' => $user_nm,
					'person_id' => $id,
					'user_group' => $user_group,
					'update_dttm' => date('Y-m-d H:i:s'),
					'update_user' => $this->session->user_id
				];
			} else {
				$data = [
					'user_nm' => $user_nm,
					'pwd0' => $pwd0,
					'person_id' => $id,
					'user_group' => $user_group,
					'update_dttm' => date('Y-m-d H:i:s'),
					'update_user' => $this->session->user_id
				];
			}
			$saveUsers = $this->usersmodel->updateuser($user_id,$data);
		}
		
		$users = $this->usersmodel->getbyUsernm($user_nm)->getResult();
		if (count($users)>0) {
			$ret = 'already';
		} else {
			
			if ($saveUsers) {
				$ret = true;
			} else {
				$ret = false;
			}
			
		}
		
	}
}
