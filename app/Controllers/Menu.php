<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;

class Menu extends BaseController
{

	public function index() {
		return view('frontend/menu');
	}

}
