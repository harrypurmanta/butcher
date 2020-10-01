<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Dashboard extends BaseController
{

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Admin Dashboard'
		];
		return view('backend/dashboard', $data);
	}

	public function waiters() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Waiters Dashboard'
		];
		return view('backend/waitersdashboard', $data);
	}

	public function kasir() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Waiters Dashboard'
		];
		return view('backend/waitersdashboard', $data);
	}

	public function error() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Error Dashboard'
		];
		return view('erros/cli/error_exception');
	}

}
