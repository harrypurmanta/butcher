<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Login extends BaseController
{

	protected $usermodel;
	protected $session;
	public function __construct(){
		$this->usersmodel = new Usersmodel();
	}

	public function index() {
		if ($this->session->user_group == "waiters") {
		  	return redirect('dashboard/waiters');
		  } else if ($this->session->user_group == 'owner') {
		  	return redirect('dashboard');
		  } else if ($this->session->user_group == 'kasir') {
		  	return redirect('kasir');
		  } else if ($this->session->user_group == 'manajer') {
		  	return redirect('dashboard');
		  } else {
		  	return view('login');
		  }
	}

	public function checklogin() {
		
		$u = $this->request->getPost('username');
		$p = $this->request->getPost('password');
		$pwd0 = md5($p);
    	
		$res = $this->usersmodel->checklogin($u,$pwd0)->getResultArray();
			if (count($res) > 0) {
			  foreach ($res as $k) {
			  	$this->session->set($k);
			  }
		  if ($this->session->user_group == "waiters") {
		  	return redirect('dashboard/waiters');
		  } else if ($this->session->user_group == 'owner') {
		  	return redirect('dashboard');
		  } else if ($this->session->user_group == 'kasir') {
		  	return redirect('kasir');
		  } else if ($this->session->user_group == 'manajer') {
		  	return redirect('dashboard');
		  } else {
		  	return redirect('/');
		  }
        } else {
          return redirect('/');
        } 
	}

	public function logout() {
		$this->session->destroy();
		return redirect()->to(site_url('/'));
	}
}
