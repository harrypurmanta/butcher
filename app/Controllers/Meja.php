<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Mejamodel;
use App\Models\Billingmodel;
use App\Models\Discountmodel;
use chillerlan\QRCode\{QRCode, QROptions};
class Meja extends BaseController {
	protected $mejamodel;
	protected $billingmodel;
	protected $discountmodel;
	public function __construct(){
		$this->mejamodel = new Mejamodel();
		$this->billingmodel = new Billingmodel();
		$this->discountmodel = new Discountmodel();
	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'meja',
			'subtitle' => 'meja',
			'meja' => $this->mejamodel->getbyNormal()->getResult()
		];
		return view('backend/meja', $data);
	}

	public function billing(){
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Billing',
			'subtitle' => 'Billing'		
		];
		return view('frontend/billing', $data);
	}

	public function viewbilling(){
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Billing',
			'subtitle' => 'Billing'		
		];
		return view('backend/viewbillingmeja', $data);
	}
	
	public function pembulatanratusan($uang){
	 $nilai = round($uang);
	 $ratusan = substr($nilai, -2);
	 $akhir = $uang + (100-$ratusan);
	 return $akhir;
	}

	public function viewmejawaiters(){
	    	if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$res = $this->mejamodel->getbyNormal()->getResult();
		$ret = "<div align='center'>
				<div><h1><strong>DAFTAR MEJA</strong></h1></div><hr>";
		foreach ($res as $key) {
			$cekmejabill = $this->billingmodel->getbyMejaid($key->meja_id);
			if (count($cekmejabill->getResultArray())>0) {
				foreach ($cekmejabill->getResult() as $k) {
					if ($k->statusbilling == 'waiting') {
						$spannotif = "<span style='margin-right:20px;' class='badgex badge-dangerx'> </span>";
					} else if ($k->statusbilling == 'verified') {
						$spannotif = "<span style='margin-right:20px;' class='badgex badge-success'> </span>";
					} else {
						$spannotif = "";
					}
				}
			} else {
				$spannotif = "";
			}
			
			$ret .= "<a href='".base_url()."/meja/viewbilling/$key->meja_id' ><button type='button' class='meja-button'><span style='font-size:75px;font-weight:bold;'>$key->meja_nm</span>$spannotif</button></a>";
		}
		$ret .= "</div>";
	 	return $ret;
	}


	public function showorderbymeja(){
	    if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$meja_id = $this->request->getPost('id');
		$res = $this->billingmodel->getbyMejaid($meja_id)->getResult();
		$discount_nmx = "";	 
		$discount_valuex = "";
		$discount = "";
		$discount_nm = "";
		$discount_value = "";
		$subtotal = 0;
		$amt_before_discount = 0;
		$total = 0;
        
		if (count($res)>0) {
			$billing_id = $res[0]->billing_id;

		    if ($res[0]->member_nm != "") {
		        $member_nm = "<span style='font-size: 35px;'>".substr($res[0]->member_nm, 0,6)."</span>";
		    } else {
		        $member_nm = "Meja ".$res[0]->meja_nm;
		    }

		    if ($res[0]->statusbilling == 'verified') {
			$buttonverif = "<div style='display:inline-block; float: right; font-size: 2em;' align='right' class='alert alert-danger'>not yet paid !! </div>";
			} else {
				$buttonverif = "<div style='display:inline-block; float: right;' align='right'><button type='button' style='font-size: 2em;' class='btn btn-success' onclick='verifybilling(".$res[0]->billing_id.")'>Verifikasi</button></div>
								<div style='display:inline-block; float: right; margin-right: 10px;' align='right'><button type='button' style='font-size: 2em;' class='btn btn-danger' onclick='batalbilling(".$res[0]->billing_id.")'>Batal</button></div>";
			}

		    list($dt,$tm) = explode(" ", $res[0]->created_dttm);
		    $resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();
			
			$ret = "<div class='col-md-12 row' align='center' id='div-item' style='margin-top: 10px;margin-left: 0px;'>
			            <div class='col-md-4'>
			                <button type='button' style='font-size: 2em;' class='btn btn-info' onclick='backtowaiters()'>Kembali</button>
			            </div>
						<div class='col-md-4'>
						    <img style='max-height: 100%; width: 150px;' src='../../images/lib/logo.jpeg'>
						</div>
                        <div class='col-md-4' id='buttonverif'>
                            $buttonverif
                        </div>
                        <div class='col-md-12' style='margin-top: 30px;'>
							<p>
								<span style='font-size: 30px;'>Butcher Steak & Pasta Palembang</span><br>
								<span style='font-size: 30px;'>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
								<span style='font-size: 30px;'>Sumatera Selatan, 30114, 07115626366</span>
							</p>
						</div>
					</div>";
			$ret .= "<div class='col-md-12'>
			        <table class='active' width='100%' style='margin-top: 20px;font-size: 35px;'>
				        <tr>
				          <td align='left'>".panjang($dt)."</td>
				          <td align='right'>".$tm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Bill Name</td>
				          <td align='right'>".$member_nm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Collected By</td>
				          <td align='right'>".$res[0]->collected_nm."</td>
				        </tr>
				      </table>
				       
				      </div>
				     <hr style='border: 1px solid red !important;'>
				      <div class='col-md-12'>
				      <table  style='font-size: 40px;' width='100%'>";
			foreach ($res as $key) {
				if ($key->statusproduk == 'normal') {
					$total = $key->produk_harga * $key->qty;
					$amt_before_discount = $amt_before_discount + $total;

					if (count($resdc)>0) {
						foreach ($resdc as $dc) {
							$symb = substr($dc->value, -1);
							if ($symb == "%") {
								$percentega = str_replace("%", "", $dc->value);
								$ptotal = ($percentega/100) * $total;
								list($harga,$belakangkoma) = explode(".", $ptotal);
								$discount = "<span style='font-size: 35px;'>(".number_format($harga).")</span>";
								$afterdc = $total - $harga;
								$subtotal = $subtotal + $afterdc;
								$discount_nmx = $dc->discount_nm;
								$discount_valuex = $dc->value;
							} else {
								$subtotal = $subtotal + $total;
							}
						} 
					} else {
						$subtotal = $subtotal + $total;
					}

				} else {
					$total = $key->produk_harga * $key->qty;
				}
				

				if ($key->statusbilling == 'verified') {
					if ($key->statusproduk == "nullified") {
						$buttonproduk = "";
						$style = "style='text-decoration: line-through;'";
						$buttonqty = "";
					} else {
						$buttonproduk = "";
						$style = "";
						$buttonqty = "";
					}
				} else {
					if ($key->statusproduk == "nullified") {
						$buttonproduk = "<button onclick='enableproduk($key->billing_item_id)' type='button' class='btn btn-success'><i class='fas fa-check'></i></button>";
						$style = "style='text-decoration: line-through;'";
						$buttonqty = "";
					} else {
						$buttonproduk = "<button onclick='disableproduk($key->billing_item_id)' xtype='button' class='btn btn-danger'><i class='fas fa-times'></i></button>";
						$style = "";
						$buttonqty = "<button onclick='minus($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 80px; height: 80px; width: 60px; line-height: 25px; margin-left:5px;'>-</button>
	       		          <button onclick='add($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 50px; height: 50px; width: 80px; line-height: 25px;'>+</button>";
					}
				}

				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 40px;'>
				            <span ".$style.">$key->produk_nm</span> ".$buttonproduk."
				          </td>
				        </tr>
				        <tr style='font-size: 40px;'>
				        <input type='hidden' id='qty$key->billing_item_id' value='$key->qty'/>
				          <td align='left' width='250'><span id='spanqty$key->billing_item_id' ".$style.">$key->qty X </span> 
				          $buttonqty
	       		          </td>
				          <td align='center'><span ".$style.">@".number_format($key->produk_harga)."</span></td>
				          <td align='right'><span ".$style.">".number_format($total)."<br>$discount</span></td>
				        </tr>
				        <tr style='line-height:40px;'>
				        <td>&nbsp </td>
				        <td></td>
				        <td></td>
				        </tr>";
				 }

				if (count($notpersen)>0) {
					 foreach ($notpersen as $dc) {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
							$ret .= "<tr style='font-size: 30px;'>
							        <td align='left' width='80'>$discount_nm </td>
							        <td></td>
							        <td align='right'>(".number_format($discount_value).")</td>
							        </tr>";
							$subtotal = $subtotal - $dc->value; 
					}
				} 

				$ret .= "</table>
				        </div>
						<hr style='border: 1px solid red !important;'>";
				    $tax = $amt_before_discount * 0.10;
					$service = $amt_before_discount * 0.05;
					$grandtotal = $subtotal + $tax + $service;
					$jmlbulat = $this->pembulatanratusan($grandtotal);
					$nilaibulat = $jmlbulat - $grandtotal;
				        
				$ret .= "<div class='col-md-12'>
				        <table style='font-size: 40px; margin-top:30px;' width='100%'>
				        <tr>
				          <td align='left'>Subtotal</td>
				          <td colspan='2' align='right'>Rp. ".number_format($subtotal)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Tax</td>
				          <td colspan='2' align='right'>Rp. ".number_format($tax)."</td>
				        </tr>
				        <tr>
				          <td align='left'>service</td>
				          <td colspan='2' align='right'>Rp. ".number_format($service)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Rounding Amount</td>
				          <td colspan='2' align='right'>Rp. ".number_format($nilaibulat)."</td>
				        </tr>
				        <tr>
				          <td align='left' style='font-weight:bold;'>Total</td>
				          <td colspan='2' align='right' style='font-weight:bold;'>Rp. ".number_format($jmlbulat)."</td>
				        </tr>
						</table>
						</div>
						<input type='hidden' value='$grandtotal' id='grandtotal' />
						<hr style='border: 1px solid red;margin-bottom:100px;'>";
		} else {
			$ret = "<div class='col-md-12 row' align='center'>
					<h3>TIDAK ADA PESANAN !!</h3> 
					<button class='meja-button' type='button' onclick='backtowaiters()'>Kembali</button>
					</div>";
		}
  		return $ret;
	}

	public function billingcustomer() {
	$meja_id = $this->request->getPost('meja_id');
	$res = $this->billingmodel->getbyMejaidcustomer($meja_id)->getResult();
	$discount_nmx = "";	 
	$discount_valuex = "";
	$discount = "";
	$discount_nm = "";
	$discount_value = "";
	$subtotal = 0;
	$amt_before_discount = 0;
	if (count($res)>0) {
		if ($res[0]->member_id == 0) {
			$billname = "Meja ".$res[0]->meja_nm;
		} else {
			$billname = "<span style='font-size: 18px;'>".substr($res[0]->member_nm, 0,6)."</span>";
		}

		if ($res[0]->statusbilling == 'verified') {
			$collctedby = "<tr>
		          <td align='left'>Collected By</td>
		          <td align='right'>".$res[0]->collected_nm."</td>
		        </tr>";
		} else {
			$collctedby = "";
		}

		if ($res[0]->statusbilling == 'normal') {
			$footer = "<button onclick='cancelorder(".$res[0]->billing_id.")' type='button' class='btn btn-danger float-left' style='font-weight: bold;'>CANCEL</button>
				<button onclick='order(".$res[0]->billing_id.")' class='btn btn-success float-right' style='font-weight: bold;'>ORDER</button>";
			$buttonmenu = "<div style='display:inline-block;' class='float-left'>
				<button onclick='listmenu()' type='button' class='btn btn-info float-left' style='font-weight: bold;'>MENU</button>
				</div>";
		} else if ($res[0]->statusbilling == 'waiting') {
			$footer = "<div align='center' class='alert alert-info alert-rounded'> 
							<i class='far fa-handshake'></i> SILAHKAN TUNGGU WAITERS UNTUK KONFIRMASI PESANAN ANDA.
						</div>";

			$buttonmenu = "<div style='display:inline-block;' class='float-left'>
							<button onclick='listmenu()' type='button' class='btn btn-info float-left' style='font-weight: bold;'>MENU</button>
							</div>";
		} else if ($res[0]->statusbilling == 'verified') {
			$footer = "<div align='center' class='alert alert-success alert-rounded'> 
							<i class='far fa-handshake'></i>  PESANAN ANDA SEDANG DI PROSES. SILAHKAN TUNGGU.
						</div>";
			$buttonmenu = "";
		}
		
		
		list($dt,$tm) = explode(" ", $res[0]->created_dttm);
		$resdc = $this->discountmodel->getbybillidpersen($res[0]->billing_id)->getResult();
		$notpersen = $this->discountmodel->getbybillid($res[0]->billing_id)->getResult();
		$ret = "<div>
					<div class='row'>
						<div class='col-3'>
						$buttonmenu
						</div>
						<div class='col-6'>
						<div align='center'>
							<img style='max-height: 100%; width: 80px;' src='../../images/lib/logo.jpeg'>
						</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-md-12'>
						<div align='center' style='margin-top: 30px; font-size: 18px;'>
							<p>
								<span>Butcher Steak & Pasta Palembang</span><br>
								<span>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
								<span>Sumatera Selatan, 30114, 07115626366</span>
							</p>
						</div>
						</div>
					</div>
					
				</div>";
		$ret .= "<table id='tbitem' width='100%' style='margin-top: 20px; font-size: 18px;'>
			        <tr>
			          <td align='left'>".panjang($dt)."</td>
			          <td align='right'>$tm</td>
			        </tr>
			        <tr>
			          <td align='left'>Bill Name</td>
			          <td align='right'>".$billname."</td>
			        </tr>
			        $collctedby
			      </table>
			      <hr style='border: 1px solid red'>
			      <table style='font-size: 18px;' width='100%'>";
		foreach ($res as $key) {
			$total = $key->produk_harga * $key->qty;
			$amt_before_discount = $amt_before_discount + $total;
				if (count($resdc)>0) {
					foreach ($resdc as $dc) {
						$symb = substr($dc->value, -1);
						if ($symb == "%") {
							$percentega = str_replace("%", "", $dc->value);
							$ptotal = ($percentega/100) * $total;
							list($harga,$belakangkoma) = explode(".", $ptotal);
							$discount = "<span style='font-size: 16px;'>(".number_format($harga).")</span>";
							$afterdc = $total - $harga;
							$subtotal = $subtotal + $afterdc;
							$discount_nmx = $dc->discount_nm;
							$discount_valuex = $dc->value;
						} else {
							$subtotal = $subtotal + $total;
						}
					} 
				} else {
					$subtotal = $subtotal + $total;
				}

			if ($key->statusbilling == 'normal') {
				$buttonqty = "<button onclick='minus($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px; margin-left:5px;'>-</button>
				<button onclick='add($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px;'>+</button>";
			} else {
				$buttonqty = "";
			}
			
			$ret .= "<tr>
			        <td colspan='3' align='left' style='font-weight: bold;'>
			            $key->produk_nm
			          </td>
			        </tr>
			        <tr>
			        <input type='hidden' id='qty$key->billing_item_id' value='$key->qty'/>
			          <td align='left' ><span id='spanqty$key->billing_item_id'>$key->qty X $buttonqty</span> </td>
			          <td align='center'>@".number_format($key->produk_harga)."</td>
			          <td align='right'>".number_format($total)."<br>$discount</td>
			        </tr>
			        <tr style='line-height:40px;'>
			        <td>&nbsp </td>
			        <td></td>
			        <td></td>
			        </tr>";
			 }

			if (count($notpersen)>0) {
					 foreach ($notpersen as $dc) {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
							$ret .= "<tr style='font-size: 18px;'>
							        <td align='left' width='120'>$discount_nm </td>
							        <td></td>
							        <td align='right'>(".number_format($discount_value).")</td>
							        </tr>";
							$subtotal = $subtotal - $dc->value; 
					}
				} 

			$ret .= "</table>
					<hr style='border: 1px solid red'>";

			$tax = $amt_before_discount * 0.10;
			$service = $amt_before_discount * 0.05;
			$grandtotal = $subtotal + $tax + $service;
			$jmlbulat = $this->pembulatanratusan($grandtotal);
			$nilaibulat = $jmlbulat - $grandtotal;

			$ret .= "<table style='margin-top:30px; font-size: 18px;' width='100%'>
			        <tr>
			          <td align='left'>Subtotal</td>
			          <td colspan='2' align='right'>Rp. ".number_format($subtotal)."</td>
			        </tr>
			        <tr>
			          <td align='left'>Tax</td>
			          <td colspan='2' align='right'>Rp. ".number_format($tax)."</td>
			        </tr>
			        <tr>
			          <td align='left'>service</td>
			          <td colspan='2' align='right'>Rp. ".number_format($service)."</td>
			        </tr>
			        <tr>
			          <td align='left'>Rounding Amount</td>
			          <td colspan='2' align='right'>Rp. ".number_format($nilaibulat)."</td>
			        </tr>
			        <tr>
			          <td align='left' style='font-weight:bold;'>Total</td>
			          <td colspan='2' align='right'>Rp. ".number_format($jmlbulat)."</td>
			        </tr>
					</table>
					<hr style='border: 1px solid red;margin-bottom:100px;'>
					<div style='margin-bottom: 150px;'>
					$footer
					</div>";
	} else {
		$ret = "false";
	}
	

	return $ret;
	
	}

	public function orderbilling() {
		$id = $this->request->getPost('id');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'status_cd' => 'waiting',
			'created_dttm' => $datenow,
			'created_user' => $id
		];
		$res = $this->billingmodel->orderbilling($id,$data);
		return $res;
	}

	public function verifybilling(){
		$id = $this->request->getPost('id');
		$grandtotal = $this->request->getPost('grandtotal');
		$datenow = date('Y-m-d H:i:s');
		$data = [
			'status_cd' => 'verified',
			'amt_before_discount' => $grandtotal,
			'verified_dttm' => $datenow,
			'verified_user' => $this->session->user_id 
		];
		$res = $this->billingmodel->verifybilling($id,$data);
		return $res;
	}

	public function batalbilling(){
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->batalbilling($id);
		return $res;
	}

	public function tambahdata(){
		$data = [
			'title' => 'Tambah Data meja',
			'subtitle' => 'Tambah Data meja'
		];
		return view('backend/addmeja', $data);
	}

	public function save(){
		$meja_nm = $this->request->getVar('meja_nm');
		$bykatnm = $this->mejamodel->getbyKatnm($meja_nm)->getResult();
		if (count($bykatnm)>0) {
			return 'already';
		} else {
			$datenow = date('Y-m-d H:i:s');
			$data = [
			'meja_nm' => $meja_nm,
			'created_dttm' => $datenow,
			'created_user' => $this->session->user_id
			];

			$mejaid = $this->mejamodel->simpan($data);
			if ($mejaid) {
				require_once APPPATH.'/Libraries/vendor/autoload.php';
				$url = 'https://butcher.agete.id/produk/listmenu/'.$mejaid.'/$datenow';
		        $options = new QROptions([
					'version'      => 7,
					'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
					'eccLevel'     => QRCode::ECC_L,
					'scale'        => 5,
					'imageBase64'  => false,

				]);
		        $qrnm = str_replace(" ", "_", $meja_nm);
				$qrcode = new QRCode($options);
				$qrcode->render($url, '../public/images/qrcode/'.$qrnm.'.png');
				$dataqr = [
					'meja_id' => $mejaid,
					'image_nm' => $qrnm.'.png',
					'image_path' => '../public/images/qrcode/',
					'status_cd' => 'normal',
					'created_dttm' => $datenow,
					'created_user' => $this->session->user_id
				];
				$insertqr = $this->mejamodel->simpanqr($dataqr);
		        return true;
			} else {
				return false;
			}
		}
	}

	public function updateqty() {
		$id = $this->request->getPost('value');
		$qty = $this->request->getPost('quanty');
		$data = [
			'qty' => $qty,
			'update_dttm' => date('Y-m-d H:i:s'),
			'update_user' => '1'
		];

		$update = $this->billingmodel->updateqty($id,$data);
		if ($update) {
			return 'true';
		} else {
			return 'false';
		}
		
	}

	public function update(){
		$id = $this->request->getVar('id');
		$meja_nm = $this->request->getVar('meja_nm');
		$datenow = date('Y-m-d H:i:s');
		$data = [
		'meja_nm' => $meja_nm,
		'updated_dttm' => $datenow,
		'updated_user' => $this->session->user_id
		];

		$save = $this->mejamodel->update($id,$data);
		if ($save) {
			return true;
		} else {
			return false;
		}
	}

	public function formedit(){
		$meja_id = $this->request->getVar('id');
		$res = $this->mejamodel->find($meja_id);
		if (count($res)>0) {
				$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan ganti data</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<input type='hidden' value='".$meja_id."' class='form-control' id='meja_id'>"
	            . "<div class='form-group'>"
	            . "<label for='recipient-name' class='control-label'>Nama meja</label>"
	            . "<input type='text' class='form-control' id='meja_nm' value='".$res['meja_nm']."'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='update(".$meja_id.")' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
	         return $ret;
		} else {
			
			return 'false';
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

		$update = $this->mejamodel->update($id,$data);
		if ($update) {
			return true;
		} else {
			return false;
		}
	}

	public function setnullifieditem(){
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->setnullifieditem($id);
		if ($res) {
			return true;
		} else {
			return false;
		}
	}

	public function setnormalitem(){
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->setnormalitem($id);
		if ($res) {
			return true;
		} else {
			return false;
		}
	}

	public function cancelbilling(){
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->batalbilling($id);
		if ($res) {
			return 'true';
		} else {
			return 'false';
		}
		
	}

}
