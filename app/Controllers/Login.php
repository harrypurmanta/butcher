<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Login extends BaseController
{

	protected $usermodel;
	protected $session;
	public function __construct(){
		$this->usersmodel = new Usersmodel();
		$this->session = \Config\Services::session();
		$this->session->start();
	}

	public function index() {
		return view('login');
	}

	public function checklogin() {
		
		$u = $this->request->getVar('username');
		$p = $this->request->getVar('password');
		$pwd0 = md5($p);
    	
		$res = $this->usersmodel->checklogin($u,$pwd0);
			if (count($res) > 0) {
			  foreach ($res as $k) {
			  	$this->session->set($k);
			  }
		  if ($res[0]['user_group'] == "waiters") {
		  	return redirect('dashboard/waiters');
		  } else if ($this->session->user_group == 'owner') {
		  	return redirect('dashboard');
		  } else if ($this->session->user_group == 'kasir') {
		  	return redirect('dashboard/kasir');
		  } else {
		  	return redirect('login');
		  }
        } else {
          return redirect('login');
        } 
	}
}
