<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Kategorimodel;
use App\Models\Imagesmodel;

class Kategori extends BaseController
{

	
	protected $kategorimodel;
	protected $imagesmodel;
	protected $session;
	public function __construct(){

		$this->kategorimodel = new Kategorimodel();
		$this->imagesmodel = new Imagesmodel();
	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Kategori',
			'subtitle' => 'Kategori',
			'kategori' => $this->kategorimodel->getbyNormal()
		];
		return view('backend/kategori', $data);
	}

	public function getChildbyparent($id){
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$id = $this->request->uri->getSegments('3');
		$res = $this->kategorimodel->getChildbyprntid($id)->getResult();
		return $res;
	}

	public function tambahdata(){
		$data = [
			'title' => 'Tambah Data Kategori',
			'subtitle' => 'Tambah Data Kategori'
		];
		return view('backend/addkategori', $data);
	}

	public function option () {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$child_nm = array();
		$options = $this->kategorimodel->getOptionbynormal()->getResult();
		

		$data = [
			'title' => 'Tambah Data Options',
			'subtitle' => 'Tambah Data Options',
			'options' => $options,
			'kategori' => $this->kategorimodel->getbyNormal(),
			
		];
		return view('backend/optionkategori', $data);
	}

	public function save(){
		$kategori_nm = $this->request->getVar('kategori_nm');
		$bykatnm = $this->kategorimodel->getbyKatnm($kategori_nm);
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'kategori_nm' => $kategori_nm,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$kat_id = $this->kategorimodel->simpan($data);
			if ($kat_id) {
				if($imagefile = $this->request->getFiles()){
				   foreach($imagefile['photo'] as $img){
				      if ($img->isValid() && ! $img->hasMoved()){
				           $newName = $img->getRandomName();
				           $img->move('../public/images', $newName);
				           $dataimg = [
				           	'kategori_id' => $kat_id,
				           	'image_nm' => $newName,
				           	'image_path' => '../public/images',
				           	'created_dttm' => $datenow,
							'created_user' => $this->session->user_id
				       		];
				       		$insertimage = $this->kategorimodel->simpanimage($dataimg);

				      	}
					}
					return true;
				}
				
			} else {
				return false;
			}
		}
	}

	public function saveoption(){
		$option_nm = $this->request->getVar('option_nm');
		$kategori_id = $this->request->getVar('kategori_id');
		$byoption_nm = $this->kategorimodel->getOptionbynm($option_nm)->getresultArray();
		if (count($byoption_nm)>0) {
			return 'already';
		} else {
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'option_nm' => $option_nm,
			'kategori_id' => $kategori_id,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$saveoption = $this->kategorimodel->simpanoption($data);
			if ($saveoption) {
				return true;
			} else {
				return false;
			}
			
		}
	}

	public function savechild(){
		$parent_id = $this->request->getPost('id');
		$child_nm = $this->request->getPost('child_nm');
		$datenow = date('Y-m-d H:i:s');
		$data = [
		'parent_id' => $parent_id,
		'option_nm' => $child_nm,
		'created_dttm' => $datenow,
		'created_user' => $this->session->user_id
		];

		$savechild = $this->kategorimodel->savechild($data);
		if ($savechild) {
			return 'true';
		} else {
			return 'false';
		}
		
	}

	public function update(){
		$id = $this->request->getVar('kategori_id');
		$kategori_nm = $this->request->getVar('kategori_nm');
		
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'kategori_nm' => $kategori_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];
			$save = $this->kategorimodel->update($id,$data);
			if ($save) {
				if($imagefile = $this->request->getFiles()){
				   foreach($imagefile['files'] as $img){
				      if ($img->isValid() && ! $img->hasMoved()){
				           $newName = $img->getRandomName();
				           $img->move('../public/images', $newName);
				           $retimg = $this->imagesmodel->getimagebykatid($id)->getResult();
							$dataimg = [
								'kategori_id' => $id,
								'image_nm' => $newName,
								'image_path' => '../public/images',
								'created_dttm' => $datenow,
								'created_user' => $this->session->user_id
							];
							$insertimage = $this->imagesmodel->insert($dataimg);
				      	} else {
				      		return 'false2';
				      	}
					}
					if ($insertimage) {
						return 'true';
					} else {
						return 'false';
					}
					
					
				} else {
					return 'false3';
				}
			} else {
				return 'false4';
			}
		
	}

	public function updateoption(){
		$option_nm = $this->request->getPost('option_nm');
		$option_id = $this->request->getPost('option_id');
		$kategori_id = $this->request->getPost('kategori_id');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'kategori_id' => $kategori_id,
			'option_nm' => $option_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
		];
		$save = $this->kategorimodel->updateoption($option_id,$data);
		if ($save) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function updatechild(){
		$id = $this->request->getPost('id');
		$child_nm = $this->request->getPost('child_nm');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'option_nm' => $child_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
		];
		$update = $this->kategorimodel->updatechild($id,$data);
		if ($update) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function formedit(){
		$kategori_id = $this->request->getVar('id');
		$res = $this->kategorimodel->find($kategori_id);
		$resimage = $this->imagesmodel->getimagebykatid($kategori_id)->getResult();
		if (count($res)>0) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$kategori_id."' class='form-control' id='kategori_id'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama Kategori</label>"
	            . "<input type='text' class='form-control' id='kategori_nm' value='".$res['kategori_nm']."'>"
	            . "</div>"
	            . "<div class='col-md-4'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Gambar :</label>"
                . "<div>";
                foreach ($resimage as $key) {
                $ret .= "<div style='display:inline-block;'><img class='card-img-top img-responsive' src='../images/".$key->image_nm."' style='max-height:100%;width:100px;'/></div>";
		        }

                $ret .= "</div>"
                . "<input type='file' name='files[]' class='dropify fotokategori' multiple=''/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$kategori_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
	         return $ret;
		} else {
			
			return 'false';
		}
	}

	public function formeditoption(){
		$option_id = $this->request->getVar('id');
		$res 	   = $this->kategorimodel->getOptionbyid($option_id)->getResult();
		$getkat    = $this->kategorimodel->getbyNormal();
		$opt = "";
		foreach ($getkat as $kopt) {
			$opt .= "<option value='".$kopt['kategori_id']."' ".($kopt['kategori_id']==$res[0]->kategori_id?"selected='selected'":"").">".$kopt['kategori_nm']."</option>"; 
		}
		 

		foreach ($res as $key) {
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$option_id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Kategori Name</label>"
	            . "<select class='form-control' name='kategori_id' id='kategori_id2'>";
	        $ret .= $opt;
	        $ret .= "</select>"
	            . "</div>"
	            . "</div>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Name :</label>"
                . "<input type='text' name='option_nm' id='option_nm2' class='form-control' value='$key->option_nm' required/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$option_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
		}
				
	         return $ret;
	}

	public function formaddchild(){
		$id = $this->request->getPost('id');
		$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Child Name :</label>"
                . "<input type='text' name='child_nm' id='child_nm' class='form-control' required/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpanchild(".$id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";

	    return $ret;
	
	}

	public function formeditchild(){
		$id = $this->request->getPost('id');
		$res = $this->kategorimodel->getChildbyid($id)->getResult();
		foreach ($res as $k) {
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='update-file' method='POST' enctype='multipart/form-data'>"
	            . "<input type='hidden' value='".$id."' class='form-control' id='option_id2'>"
	            . "<div class='col-md-12'>"
                . "<div class='form-group'>"
                . "<label class='control-label'>Option Child Name :</label>"
                . "<input type='text' name='child_nm' id='child_nm2' class='form-control' required value='$k->option_nm'/>"
                . "</div>"
                . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='updatechild(".$id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
		}
		

	    return $ret;
	
	}

	public function hapus(){
		$id = $this->request->getVar('id');
		$type = $this->request->getVar('t');
		if ($type == 'kategori') {
			$tables = 'kategori_produk';
			$key    = 'kategori_id';
		} else if ($type == 'options' || $type == 'child') {
			$tables = 'options';
			$key    = 'option_id';
		}
		

		$datenow = date('Y-m-d H:i:s');
		$data = [
		'status_cd' => 'nullified',
		'nullified_dttm' => $datenow,
		'nullified_user' => $this->session->user_id
		];

		$update = $this->kategorimodel->hapus($id,$data,$tables,$key);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}
}
