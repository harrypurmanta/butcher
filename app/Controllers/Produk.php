<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Produkmodel;
use App\Models\Kategorimodel;
use App\Models\Imagesmodel;
use App\Models\Billingmodel;
class Produk extends BaseController
{
	protected $produkmodel;
	protected $kategorimodel;
	protected $imagesmodel;
	protected $billingmodel;
	protected $session;
	public function __construct(){
		$this->produkmodel = new Produkmodel();
		$this->kategorimodel = new Kategorimodel();
		$this->imagesmodel = new Imagesmodel();
		$this->billingmodel = new Billingmodel();
		$this->session = \Config\Services::session();
		$this->session->start();
	}

	public function index() {
		$agent = $this->request->getUserAgent();
		echo json_encode($agent);exit;
		$data = [
			'title' => 'Produk',
			'subtitle' => 'Produk',
			'produk' => $this->produkmodel->getbyNormal()
		];
		return view('backend/produk', $data);
	}

	public function listmenu2(){
		$meja_id = $this->request->uri->getSegment(3);
			$data = [
			'title' => 'Menu',
			'subtitle' => 'Menu',
			'kategori' => $this->kategorimodel->getbyNormal()->getResult()
			];
			return view('frontend/listmenu2', $data);
	}

	public function listmenu(){
		$meja_id = $this->request->uri->getSegment(3);
		if ($meja_id == "") {
			return redirect('dashboard');
		} else {
			$res = $this->billingmodel->getbyMejaidcustomer($meja_id)->getResult();
			if (count($res)>0) {
				$data = [
				'title' => 'Your Bill',
				'subtitle' => 'Your Bill',
				'billing' => $res
				];
				return view('frontend/billing', $data);
			} else {
				$data = [
				'title' => 'Menu',
				'subtitle' => 'Menu',
				'kategori' => $this->kategorimodel->getbyNormal()->getResult()
				];
				return view('frontend/listmenu2', $data);
			}
		}
		
		
	}

	public function menu() {
		$kategori = $this->kategorimodel->getbyNormal();
		$meja_id = $this->request->getPost('meja_id');
		$res = $this->billingmodel->getbyMejaidcustomer($meja_id)->getResult();
		if (count($res)>0) {
			$subtotal = 0;
			$ret = "<div align='center'>
						<div class='m-b-50'><img style='max-height: 100%; width: 150px;' src='../../images/lib/logo.jpeg'></div>
						<div style='margin-top: 30px;'>
							<p>
								<span style='font-size: 30px;'>Butcher Steak & Pasta Palembang</span><br>
								<span style='font-size: 30px;'>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
								<span style='font-size: 30px;'>Sumatera Selatan, 30114, 07115626366</span>
							</p>
						</div>
						<div style='margin-top: 50px; height: 100%;' id='container_content'>
							
						</div>
					</div>";
			$ret .= "<table width='100%' style='margin-top: 20px;font-size: 30px;'>
				        <tr>
				          <td align='left'>Tanggal</td>
				          <td align='right'></td>
				        </tr>
				        <tr>
				          <td align='left'>Bill Name</td>
				          <td align='right'>Pendy</td>
				        </tr>
				        <tr>
				          <td align='left'>Collected By</td>
				          <td align='right'>Fita PS</td>
				        </tr>
				      </table>
				      <hr style='border: 1px solid red'>
				      <table style='font-size: 30px;' width='100%'>";
			foreach ($res as $key) {
				$total = $key->produk_harga * $key->qty;
				$subtotal = $subtotal + $total;
				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 30px;'>
				            $key->produk_nm
				          </td>
				        </tr>
				        <tr style='font-size: 30px;'>
				          <td align='left' width='50'>$key->qty X</td>
				          <td align='center'>@".number_format($key->produk_harga)."</td>
				          <td align='right'>".number_format($total)."</td>
				        </tr>
				        <tr style='line-height:40px;'>
				        <td>&nbsp </td>
				        <td></td>
				        <td></td>
				        </tr>";
				 }
				$ret .= "</table>
						<hr style='border: 1px solid red'>";
				$tax = $subtotal * 0.10;
				$service = $subtotal * 0.05;
				$grandtotal = $subtotal + $tax + $service;
				        
				$ret .= "<table style='font-size: 30px; margin-top:30px;' width='100%'>
				        <tr>
				          <td align='left'>Subtotal</td>
				          <td colspan='2' align='right'>Rp. ".number_format($subtotal)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Tax (10%)</td>
				          <td colspan='2' align='right'>Rp. ".number_format($tax)."</td>
				        </tr>
				        <tr>
				          <td align='left'>service (5%)</td>
				          <td colspan='2' align='right'>Rp. ".number_format($service)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Rounding Amount</td>
				          <td colspan='2' align='right'>Rp. dak tau rumusnyo</td>
				        </tr>
				        <tr>
				          <td align='left' style='font-weight:bold;'>Total</td>
				          <td colspan='2' align='right'>Rp. ".number_format($grandtotal)."</td>
				        </tr>
						</table>
						<hr style='border: 1px solid red;margin-bottom:100px;'>";
		} else {
		

	    	foreach ($kategori as $k2) {
			$ret .= "<!-- LIST MENU -->"
			. "<div style='display:none;' id='menu_".$k['kategori_id']."'>"
		  	. "<div class='table-responsive' style='margin-top: 30px;'>";
		         
		    	$produk = $this->produkmodel->getbyKatId($k['kategori_id']);
		    	foreach ($produk->getResult() as $key) {
		    	$harga = str_replace(0,'', $key->produk_harga);
		    
		      	$ret .= "<table id='myTable' align='center' style='margin-top: 5px; background-color: #dc0000;'>"
		        	. "<tr class='tr'>"
		         	. "<td width='250' height='50' align='left'>"
		         	. "<input id='qty$key->produk_id' data-produk-id='$key->produk_id' value='0' style='width: 40%; height: 55px; font-size: 40px; font-weight: bold; text-align: center; display: inline-block; top: 20px;' type='number' name='qty[]' max-length='2' min='0' max='99'/>"
		         	. "<button onclick='minus($key->produk_id)' class='btn btn-success font-weight-bold' style='font-size: 50px; height: 50px; width: 50px; line-height: 25px; display: inline-block; margin-bottom: 25px;'>-</button>"
		       		. "<button onclick='add($key->produk_id)' class='btn btn-success font-weight-bold' style='font-size: 50px; height: 50px; width: 50px; line-height: 25px; display: inline-block; margin-bottom: 25px;'>+</button>"
		         	. "</td>"
		         	. "<td width='550' align='left' style='color: white; font-weight: bold; font-size: 40px;'>$key->produk_nm</td>"
		         	. "<td width='150' align='center' style='color: white; font-weight: bold; font-size: 50px;'>$harga</td>"
		         	. "</tr>"
					. "</table>";
		    }
		       $ret .= "<hr>"
		       		. "<div align='center'>";
		        
		        $images = $this->imagesmodel->getimagebykatid($k['kategori_id']);
		        foreach ($images->getResult() as $key) {
		    	$ret .= "<div style='display: inline-block; padding: 10px;'><img src='../../images/$key->image_nm' style='height: 185px; width: 280px;'></div>";
		      	}
		       	$ret .= "</div>"
		      		. "</div>"
		    		. "</div>"
		  			. "<!-- END LIST MENU -->";
		    	
		  		$ret .= "</div>"
		  			 . "<button onclick='simpanorder()' type='button' class='btn btn-success btn-circle btn-xl' style='position: fixed; bottom: 50px; right: 20px;'><i class='fa fa-check'></i></button>";
	    	}
		}
  		return $ret;
	}

	public function getbykatid(){
		$id = $this->request->getPost('id');
		$res = $this->produkmodel->getbyKatId($id)->getResult();
		
		$ret = "<div class='table-responsive'>"
      	     . "<table id='myTable' align='center' style='margin-top: 50px;'>";
		foreach ($res as $k) {
		$harga = str_replace(0,'', $k->produk_harga);
		$ret .= "<tr class='tr'>"
    		 . "<td width='250' height='50' align='left'>"
    		 . "<input id='qty$k->produk_id' value='0' style='width: 40%; height: 55px; font-size: 40px; font-weight: bold; text-align: center; display: inline-block; top: 20px;' type='number' name='' max-length='2'/>"
    		 . "<button onclick='minus($k->produk_id') class='btn btn-success font-weight-bold' style='font-size: 50px; height: 50px; width: 50px; line-height: 25px; display: inline-block; margin-bottom: 25px;'>-</button>"
			 . "<button onclick='add($k->produk_id)' class='btn btn-success font-weight-bold' style='font-size: 50px; height: 50px; width: 50px; line-height: 25px; display: inline-block; margin-bottom: 25px;'>+</button>"
    		 . "</td>"
    		 . "<td width='550' align='left' style='color: white; font-weight: bold; font-size: 40px;'>$k->produk_nm</td>"
    		 . "<td width='150' align='center' style='color: white; font-weight: bold; font-size: 50px;'>$harga</td>"
    		 . "</tr>";
		}
		$ret .= "</table>"
			 . "<hr>"
			 . "<div align='center'>";
			 $resimg = $this->imagesmodel->getimagebykatid($id)->getResult();
			 foreach ($resimg as $key) {
			 	$ret .= "<div style='display: inline-block; padding: 10px;'><img src='../images/$key->image_nm' style='height: 185px; width: 280px;'></div>";
			 }
      	$ret .= "<div>"
      		 . "</div>"
      		 . "</div>"
      		 . "</div>";
      	return $ret;
	}


	public function save(){
		$produk_nm = $this->request->getVar('produk_nm');
		$kategori_id = $this->request->getVar('kategori_id');
		$harga = $this->request->getVar('harga');
		$produk_id = $this->request->getVar('produk_id');
		$datenow = date('Y-m-d H:i:s');

		if ($produk_id=='') {
			$data = [
			'produk_nm' => $produk_nm,
			'kategori_id' => $kategori_id,
			'produk_harga' => $harga,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$save = $this->produkmodel->save($data);
			if ($save) {
				return true;
			} else {
				return false;
			}
		} else {
			$data = [
			'produk_nm' => $produk_nm,
			'kategori_id' => $kategori_id,
			'produk_harga' => $harga,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];

			$update = $this->produkmodel->update($produk_id,$data);
			if ($update) {
				return true;
			} else {
				return false;
			}
		}	
	}

	public function simpanorder(){
		$qty = $this->request->getVar('qty');
		$produk_id = $this->request->getVar('produk_id');
		$meja_id 	= $this->request->getVar('meja_id');
		if ($qty == "") {
			return "belumorder";
		} else {
			$count = count($qty);
			$date = date('Y-m-d H:i:s');
			$data = [
			  'meja_id' => $meja_id,
			  'status_cd' => 'normal',
			  'created_dttm' => $date,
			  'created_user' => $meja_id
			];
			$billing_id = $this->billingmodel->simpanbilling($data);

			if ($billing_id != "") {
				for ($i=0; $i < $count; $i++) { 
					$produk = $this->produkmodel->getbyId($produk_id[$i])->getResult();
					$harga = $produk[0]->produk_harga * $qty[$i];
					$dataitem = [
						'billing_id' => $billing_id,
						'item_dttm'  => $date,
						'produk_id'  => $produk_id[$i],
						'produk_nm'  => $produk[0]->produk_nm,
						'qty' 		 => $qty[$i],
						'price'		 => $harga,
						'status_cd' => 'normal',
						'created_dttm' => $date,
						'created_user' => $meja_id
					];
					$bil_item = $this->billingmodel->simpanbillitem($dataitem);
				}
				return 'true';
			} else {
				return 'false';
			}
		}
	}

	public function update(){
		$id = $this->request->getVar('id');
		$Produk_nm = $this->request->getVar('Produk_nm');
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'Produk_nm' => $Produk_nm,
			'updated_dttm' => $datenow,
			'updated_user' => $this->session->user_id
			];

			$save = $this->produkmodel->update($id,$data);
			if ($save) {
				return true;
			} else {
				return false;
			}
		
	}

	public function formadd(){
		$produk_id = $this->request->getVar('id');
		$kategori = $this->kategorimodel->getbyNormal();
		if ($produk_id!=null) {
			$res = $this->produkmodel->getbyId($produk_id);
			foreach ($res->getResult('array') as $key) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan tambah data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama Produk</label>"
	            . "<input type='hidden' id='produk_id' value='$produk_id'/>"
	            . "<input type='text' class='form-control' id='produk_nm' value='".$key['produk_nm']."'>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Kategori Produk</label>"
	            . "<select class='form-control' id='kategori_id' >";
	            foreach ($kategori as $k) {
	            	$ret .= "<option value='".$k['kategori_id']."'>".$k['kategori_nm']."</option>";
	            }
	        
	        $ret .= "</select>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Harga</label>"
	            . "<input type='text' class='form-control' id='harga' value='".$key['produk_harga']."'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpan()' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
			}
				
	         return $ret;
		} else {
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan tambah data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama Produk</label>"
	            . "<input type='text' class='form-control' id='produk_nm' >"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Kategori Produk</label>"
	            . "<select class='form-control' id='kategori_id' >";
	            foreach ($kategori as $k) {
	            	$ret .= "<option value='".$k['kategori_id']."'>".$k['kategori_nm']."</option>";
	            }
	        
	        $ret .= "</select>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Harga</label>"
	            . "<input type='number' class='form-control' id='harga' >"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpan()' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
	         return $ret;
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

		$update = $this->produkmodel->update($id,$data);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}

	

}
