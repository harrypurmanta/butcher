<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\fpdf\fpdf;
use App\Models\Usersmodel;
use App\Models\Mejamodel;
use App\Models\Billingmodel;
use App\Models\Discountmodel;
use App\Models\Membermodel;
use App\Models\Payplanmodel;
use App\Models\Kategorimodel;
use App\Models\Produkmodel;
// require  '/home/u1102684/public_html/butcher/app/Libraries/vendor/autoload.php';
require  '/var/www/html/lavitabella/app/Libraries/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\RawbtPrintConnector;
use Mike42\Escpos\CapabilityProfile;
// use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;
// use Mike42\Escpos\Printer;

class Kasir extends BaseController
{
	protected $mejamodel;
	protected $billingmodel;
	protected $discountmodel;
	protected $membermodel;
	protected $payplanmodel;
	protected $kategorimodel;
	protected $produkmodel;
	protected $connector;
	protected $profile;
	protected $printer;
	protected $session;
	public function __construct(){
		
		
		$this->mejamodel = new Mejamodel();
		$this->billingmodel = new Billingmodel();
		$this->discountmodel = new Discountmodel();
		$this->membermodel = new Membermodel();
		$this->payplanmodel = new Payplanmodel();
		$this->kategorimodel = new Kategorimodel();
		$this->produkmodel = new Produkmodel();
	}
	
	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Kasir Dashboard',
			'subtitle' => 'Kasir',
		];
		return view('backend/kasir', $data);

	}

	public function cardbodymeja() {
		$ret = "";
		$kasirstatus = $this->billingmodel->getStatuskasir()->getResult();
		if (count($kasirstatus)>0) {
			if ($kasirstatus[0]->status_cd == "open") {
				$meja = $this->mejamodel->getbyNormal()->getResult();
				foreach ($meja  as $key) {
					$billing = $this->billingmodel->getbyMejaid($key->meja_id)->getResult();	
					if (count($billing)>0) {
					  foreach ($billing as $k) {
						if ($k->statusbilling == 'waiting') {
						  $btnclass = "btn btn-warning";
						} else if ($k->statusbilling == 'verified') {
						  $btnclass = "btn btn-danger";
					  	} else {
						  $btnclass = "btn btn-info";
						}
					  }
					} else {
					  $btnclass = "btn btn-info";
					}
					
					$ret .= "<div style='display: inline-block; margin: 5px;'>
								<button onclick='clickmejabutton($key->meja_id)' class='$btnclass font-weight-bold' style='font-size: 20px; padding: 10px;'>$key->meja_nm</button>
							</div>";	   
				}
			} else {
				$ret .= "<div align='center' style='display: inline-block; margin: 5px;'>
							<button onclick='openkasir()' class='btn btn-info font-weight-bold' style='font-size: 20px; padding: 10px;'>OPEN KASIR</button>
						</div>";	
			}
		} else {
			$ret .= "<div align='center' style='display: inline-block; margin: 5px;'>
						<button onclick='openkasir()' class='btn btn-info font-weight-bold' style='font-size: 20px; padding: 10px;'>OPEN KASIR</button>
					</div>";	
		}
		return $ret;                       
	}

	public function pembulatanratusan($uang){
	 $nilai = round($uang);
	 $ratusan = substr($nilai, -3);
	 $akhir = $uang + (1000-$ratusan);
	 return $akhir;
	}

	public function pembulatanratusanribu($uang){
	 $nilai = round($uang);
	 $ratusan = substr($nilai, -2);
	 $akhir = $uang + (50000-$ratusan);
	 return $akhir;
	}

	public function daftarkategorikasir() {
		$res = $this->kategorimodel->getbyNormal()->getResult();
		$ret = "<div>"
			. "<button style='float: left !important;' type='button' class='btn btn-info' onclick='listmejakasir()'><i class='fas fa-chevron-left'></i></button>"
			. "<span style='margin-left: 20px; font-size: 22px; font-weight: bold;'>PILIH KATEGORI PRODUK</span>"
			. "<hr/>"
			. "<div style='margin-top:20px; height: 350px; overflow:auto;'>";
				foreach ($res as $key) {
					$ret .= "<div onclick='clickkategori($key->kategori_id)' class='m-t-10' style='background-color: #e9ecef; border-radius:5px; padding: .75rem 1rem;'><span style='font-size: 22px;'>$key->kategori_nm</span> <i style='float:right;' class='fas fa-chevron-right'></i></div>";
				}
		$ret .= "</div></div>";
		return $ret;
	}

	public function getprodukbykategori() {
		$id = $this->request->getPost('id');
		$res = $this->produkmodel->getbyKatId($id)->getResult();
		$ret = "<div>"
			. "<button style='float: left !important;' type='button' class='btn btn-danger' onclick='btntambahpesanan()'><i class='fas fa-chevron-left'></i></button>"
			. "<span style='margin-left: 20px; font-size: 22px; font-weight: bold;'>PILIH ITEM</span>"
			. "<hr/>"
			. "<div style='margin-top:20px; height: 350px; overflow:auto;'>";
				foreach ($res as $key) {
					$ret .= "<div onclick='addproduk($key->produk_id)' class='m-t-10' style='background-color: #e9ecef; border-radius:5px; padding: .75rem 1rem;'><span style='font-size: 22px;'>$key->produk_nm</span> <span style='font-size: 22px; float:right;'>Rp. ".number_format($key->produk_harga)."</span></i></div>";
				}
		$ret .= "</div></div>";
		return $ret;
	}

	public function clickmejabutton() {
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$discount_nmx 			= "";	 
		$discount_valuex 		= "";
		$discount 				= "";
		$discount_nm 			= "";
		$discount_value 		= "";
		$subtotal 				= 0;
		$amt_before_discount 	= 0;
		if (count($res)>0) {
			$billing_id = $res[0]->billing_id;
			if ($res[0]->member_nm != "") {
		        $member_nm = "<span style='font-size: 16px;'>".$res[0]->member_nm."</span> <a href='#' onclick='removemember($id,$billing_id)'><i style='color:red;' class='fas fa-times'></i></a>";
		    } else {
		        $member_nm = "MEJA ".$res[0]->meja_nm;
		    }
		
			list($dt,$tm) = explode(" ", $res[0]->created_dttm);
			$resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();
			$ret = "<div><div class='row col-md-12 m-0' id='div-item'>
					<input type='hidden' id='meja_id' value='$id'/>
					<input type='hidden' id='billing_id' value='$billing_id'/>
						<!-- <div class='col-md-12 m-0 text-center' align='center' style='margin-top: 30px;'>
							
								<p style='font-size: 16px;margin-block-end: -5px;'>Butcher Steak & Pasta Palembang</p>
								<p style='font-size: 16px;margin-block-end: -5px;'>Jl. AKBP Cek Agus No. 284, Palembang</p>
								<p style='font-size: 16px;margin-block-end: -5px;'>Sumatera Selatan, 30114, 07115626366</p>
							
						</div> -->
					</div>";
			$ret .= "<div class='row col-md-12 m-0'>
					  <table width='100%' style='font-size: 18px;'>
				        <tr>
				          <td align='left'>".panjang($dt)."</td>
				          <td align='right'>".$tm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Bill Name</td>
				          <td align='right'><button onclick='showpindahmeja($billing_id,$id,this)' class='btn btn-danger waves-effect waves-light' type='button'><span class='btn-label'><i class='fas fa-angle-double-left'></i></span>Pindah</button> $member_nm</td>
				        </tr>
				        <tr>
				          <td align='left'>Collected By</td>
				          <td align='right'>".$res[0]->collected_user."</td>
				        </tr>
				      </table>
				      </div>
				      <hr style='border: 1px solid red'>
				      <div class='row col-md-12 m-0' style='overflow:auto;'>
				      <table class='active' width='100%'><tbody>";
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
							$discount = "<span style='font-size: 16px;'>(".number_format($harga).")</span> <a href='#' onclick='removedcmember($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a>";
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

				if ($key->statusbilling == 'verified') {
					$buttonqty = "<button onclick='minusitem($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px; margin-left:5px;'>-</button>
					<button onclick='additem($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px;'>+</button>";
				} else {
					$buttonqty = "";
				}
				
				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 20px;'>
				            <span>".strtoupper($key->produk_nm)." <a style='float: right;' onclick='removeitem($id,$key->billing_item_id,$billing_id,this)'><i style='color:red;' class='fas fa-times'></i></a></span>
				          </td>
				        </tr>
				        <tr style='font-size: 18px;'>
				        <input type='hidden' id='inputqty$key->billing_item_id' value='$key->qty'/>
				          <td align='left' width='180'><span id='jumlahitem$key->billing_item_id'>$key->qty X $buttonqty</span><br>$discount_nmx $discount_valuex</td>
				          <td align='center'><span>@".number_format($key->produk_harga)."</span></td>
				          <td align='right'><span>".number_format($total)."<br>$discount</span></td>
				        </tr>
				        <tr style='line-height:12px;'>
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
							        <td align='left' width='80'>$discount_nm </td>
							        <td></td>
							        <td align='right'>(".number_format($discount_value).") <a href='#' onclick='removedc($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a></td>
							        </tr>";
							$subtotal = $subtotal - $dc->value; 
					}
				} 
				
				    
					$servicex = $amt_before_discount * 0.05;
					if (strpos($servicex,'.') == TRUE) {
						list($service,$belakangkomas) = explode(".", $servicex);
					} else {
						$service = $servicex;
					}

					$taxx = ($amt_before_discount + $service) * 0.10;
					if (strpos($taxx,'.') == TRUE) {
						list($tax,$belakangkoma) = explode(".", $taxx);
					} else {
						$tax = $taxx;
					}
					
					
					$grandtotal = $subtotal + $tax + $service;
					$jmlbulat = $this->pembulatanratusan($grandtotal);
					$nilaibulat = $jmlbulat - $grandtotal;
					

				$ret .= "</tbody></table></div>
						<hr style='border: 1px solid red'>";
				        
				$ret .= "<table style='font-size: 18px;' width='100%'>
				        <tr>
				          <td align='left'>Subtotal</td>
				          <td colspan='2' align='right'>".number_format($subtotal)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Tax</td>
				          <td colspan='2' align='right'>".number_format($tax)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Service</td>
				          <td colspan='2' align='right'>".number_format($service)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Rounding Amount</td>
				          <td colspan='2' align='right'>".number_format($nilaibulat)."</td>
				        </tr>

				        <tr>
				          <td align='left' style='font-weight:bold;'>Total</td>
				          <td colspan='2' align='right'>".number_format($jmlbulat)."</td>
				        </tr>
						</table>
						<hr style='border: 1px solid red;margin-bottom:20px;'>";

				$ret .= "<div class='col-md-12' align='center' style='margin:0;padding:0;'>
							<center>
							<button onclick='cetakmenudrinks($id,this)' class='btn btn-warning' style='font-size:16px;width: 20%;'>Cetak Drinks</button>
							<button onclick='cetakmenufood($id,this)' class='btn btn-danger' style='font-size:16px;width: 20%;'>Cetak Foods</button>
							<button onclick='cetakbilling($id,this)' class='btn btn-info' style='font-size:16px;width: 40%;'>Cetak Billing</button>
							</center>
						</div>";
				$ret .= "<div class='m-t-20' align='center'>
							<button onclick='showcheckout($id,$jmlbulat)' class='btn btn-success' style='font-size:20px;'>Payment</button>
						</div>";
				$return = $ret;

				$res = $this->kategorimodel->getbyNormal()->getResult();
				$produk = "<div><input type='hidden' id='meja_id' value='$id'/>"
					. "<button style='float: left;' type='button' class='btn btn-info' onclick='listmejakasir()'><i class='fas fa-chevron-left'></i></button>"
					. "<span style='margin-left: 20px; font-size: 22px; font-weight: bold;'>PILIH KATEGORI PRODUK</span>"
					. "<hr/>"
					. "<div style='margin-top:20px; height: 350px; overflow:auto;'>";
						foreach ($res as $key) {
							$produk .= "<div onclick='clickkategori($key->kategori_id)' class='m-t-10' style='background-color: #e9ecef; border-radius:5px; padding: .75rem 1rem;'><span style='font-size: 22px;'>$key->kategori_nm</span> <i style='float:right;' class='fas fa-chevron-right'></i></div>";
						}
				$produk .= "</div></div></div>";
				$return = array('status' => 'billing','billing' => $ret, 'produk' => $produk);
		} else {
			$meja = $this->mejamodel->getbyid($id)->getResult();
			$ret = "<div class='modal-dialog'>"
		            . "<div class='modal-content'>"
		            . "<div class='modal-header'>"
		            . "<h4 class='modal-title'>Jumlah Tamu</h4>"
		            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>x</button>"
		            . "</div>"
		            . "<div class='modal-body'>"
		            . "<form>"
		            . "<div class='col-md-12'>"
		            . "<div class='row'>"
		            . "<div class='form-group col-md-12'>"
		            . "<label for='jumlah_customer' class='control-label'>Masukkan jumlah tamu di <strong>MEJA ".$meja[0]->meja_nm."</strong></label>"
		            . "<div class='input-group col-md-8'>"
		            . "<input type='number' class='form-control' id='jumlahtamu' placeholder='0'>"
		            . "</div>"
		            . "</div>"

		            . "<div class='form-group col-md-12'>"
		            . "<label for='jumlah_customer' class='control-label'><strong>PETUGAS :</strong></label>"
		            . "<div class='row' style='align-items: center;'>"
		            . "<div class='input-group col-md-8'>"
		            . "<input type='text' class='form-control' id='collected_user' placeholder='nama'>"
		            . "</div>"
		            . "</div>"
		            . "</div>"

		            . "</div>"
		            . "</div>" //row
		            . "</form>"
		            . "</div>"
		            . "<div class='modal-footer'>"
		            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
		            . "<button onclick='simpanjumlahcustomer($id)' type='button' class='btn btn-info waves-effect waves-light'>Simpan</button>"
		            . "</div>"
		            . "</div>"
		            . "</div>";

		    $return = array('status' => 'form','form' => $ret);
		}
		echo json_encode($return,JSON_UNESCAPED_SLASHES);
	}

	public function getbymejaidkasir() {
		$id 	= $this->request->getPost('id');
		$res 	= $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$discount_nmx 			= "";	 
		$discount_valuex 		= "";
		$discount 				= "";
		$discount_nm 			= "";
		$discount_value 		= "";
		$subtotal 				= 0;
		$amt_before_discount 	= 0;
		if (count($res)>0) {
			$billing_id = $res[0]->billing_id;
			if ($res[0]->member_nm != "") {
		        $member_nm = "<span style='font-size: 16px;'>".$res[0]->member_nm."</span> <a href='#' onclick='removemember($id,$billing_id)'><i style='color:red;' class='fas fa-times'></i></a>";
		    } else {
		        $member_nm = "MEJA ".$res[0]->meja_nm;
		    }
		
			list($dt,$tm) = explode(" ", $res[0]->created_dttm);
			$resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();
			$ret = "<div><div class='row col-md-12 m-0' id='div-item'>
					<input type='hidden' id='meja_id' value='$id'/>
					<input type='hidden' id='billing_id' value='$billing_id'/>
						<!-- <div class='col-md-12 m-0 text-center' align='center' style='margin-top: 30px;'>
							
								<p style='font-size: 16px;margin-block-end: -5px;'>Butcher Steak & Pasta Palembang</p>
								<p style='font-size: 16px;margin-block-end: -5px;'>Jl. AKBP Cek Agus No. 284, Palembang</p>
								<p style='font-size: 16px;margin-block-end: -5px;'>Sumatera Selatan, 30114, 07115626366</p>
							
						</div> -->
					</div>";
			$ret .= "<div class='row col-md-12 m-0'>
					  <table width='100%' style='font-size: 18px;'>
				        <tr>
				          <td align='left'>".panjang($dt)."</td>
				          <td align='right'>".$tm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Bill Name</td>
				          <td align='right'>$member_nm</td>
				        </tr>
				        <tr>
				          <td align='left'>Collected By</td>
				          <td align='right'>".$res[0]->collected_user."</td>
				        </tr>
				      </table>
				      </div>
				      <hr style='border: 1px solid red'>
				      <div class='row col-md-12 m-0' style='overflow:auto;'>
				      <table class='active' width='100%'><tbody>";
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
							$discount = "<span style='font-size: 16px;'>(".number_format($harga).")</span> <a href='#' onclick='removedcmember($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a>";
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

				if ($key->statusbilling == 'verified') {
					$buttonqty = "<button onclick='minusitem($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px; margin-left:5px;'>-</button>
					<button onclick='additem($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 35px; line-height: 0px;'>+</button>";
				} else {
					$buttonqty = "";
				}
				
				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 20px;'>
				            <span>$key->produk_nm <a style='float: right;' onclick='removeitem($id,$key->billing_item_id,$billing_id,this)'><i style='color:red;' class='fas fa-times'></i></a></span>
				          </td>
				        </tr>
				        <tr style='font-size: 18px;'>
				        <input type='hidden' id='inputqty$key->billing_item_id' value='$key->qty'/>
				          <td align='left' width='180'><span id='jumlahitem$key->billing_item_id'>$key->qty X $buttonqty</span><br>$discount_nmx $discount_valuex</td>
				          <td align='center'><span>@".number_format($key->produk_harga)."</span></td>
				          <td align='right'><span>".number_format($total)."<br>$discount</span></td>
				        </tr>
				        <tr style='line-height:12px;'>
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
							        <td align='left' width='80'>$discount_nm </td>
							        <td></td>
							        <td align='right'>(".number_format($discount_value).") <a href='#' onclick='removedc($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a></td>
							        </tr>";
							$subtotal = $subtotal - $dc->value; 
					}
				} 
				
				    
					$servicex = $amt_before_discount * 0.05;
					if (strpos($servicex,'.') == TRUE) {
						list($service,$belakangkomas) = explode(".", $servicex);
					} else {
						$service = $servicex;
					}

					$taxx = ($amt_before_discount + $service) * 0.10;
					if (strpos($taxx,'.') == TRUE) {
						list($tax,$belakangkoma) = explode(".", $taxx);
					} else {
						$tax = $taxx;
					}
					
					
					$grandtotal = $subtotal + $tax + $service;
					$jmlbulat = $this->pembulatanratusan($grandtotal);
					$nilaibulat = $jmlbulat - $grandtotal;
					

				$ret .= "</tbody></table></div>
						<hr style='border: 1px solid red'>";
				        
				$ret .= "<table style='font-size: 18px;' width='100%'>
				        <tr>
				          <td align='left'>Subtotal</td>
				          <td colspan='2' align='right'>".number_format($subtotal)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Tax</td>
				          <td colspan='2' align='right'>".number_format($tax)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Service</td>
				          <td colspan='2' align='right'>".number_format($service)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Rounding Amount</td>
				          <td colspan='2' align='right'>".number_format($nilaibulat)."</td>
				        </tr>

				        <tr>
				          <td align='left' style='font-weight:bold;'>Total</td>
				          <td colspan='2' align='right'>".number_format($jmlbulat)."</td>
				        </tr>
						</table>
						<hr style='border: 1px solid red;margin-bottom:20px;'>";

				$ret .= "<div class='col-md-12' align='center' style='margin:0;padding:0;'>
							<center>
							<button onclick='cetakmenudrinks($id,this)' class='btn btn-warning' style='font-size:16px;width: 20%;'>Cetak Drinks</button>
							<button onclick='cetakmenufood($id,this)' class='btn btn-danger' style='font-size:16px;width: 20%;'>Cetak Foods</button>
							<button onclick='cetakbilling($id,this)' class='btn btn-info' style='font-size:16px;width: 40%;'>Cetak Billing</button>
							</center>
						</div>";
				$ret .= "<div class='m-t-20' align='center'>
							<button onclick='showcheckout($id,$jmlbulat)' class='btn btn-success' style='font-size:20px;'>Payment</button>
						</div>";
				$return = $ret;

				$res = $this->kategorimodel->getbyNormal()->getResult();
				$produk = "<div><input type='hidden' id='meja_id' value='$id'/>"
					. "<button style='float: left;' type='button' class='btn btn-info' onclick='listmejakasir()'><i class='fas fa-chevron-left'></i></button>"
					. "<span style='margin-left: 20px; font-size: 22px; font-weight: bold;'>PILIH KATEGORI PRODUK</span>"
					. "<hr/>"
					. "<div style='margin-top:20px; height: 350px; overflow:auto;'>";
						foreach ($res as $key) {
							$produk .= "<div onclick='clickkategori($key->kategori_id)' class='m-t-10' style='background-color: #e9ecef; border-radius:5px; padding: .75rem 1rem;'><span style='font-size: 22px;'>$key->kategori_nm</span> <i style='float:right;' class='fas fa-chevron-right'></i></div>";
						}
				$produk .= "</div></div></div>";
				$return = array('status' => 'billing','billing' => $ret, 'produk' => $produk);
		} else {
			$res = $this->kategorimodel->getbyNormal()->getResult();
			$produk = "<div><input type='hidden' id='meja_id' value='$id'/>"
					. "<button style='float: left;' type='button' class='btn btn-info' onclick='listmejakasir()'><i class='fas fa-chevron-left'></i></button>"
					. "<span style='margin-left: 20px; font-size: 22px; font-weight: bold;'>PILIH KATEGORI PRODUK</span>"
					. "<hr/>"
					. "<div style='margin-top:20px; height: 350px; overflow:auto;'>";
						foreach ($res as $key) {
							$produk .= "<div onclick='clickkategori($key->kategori_id)' class='m-t-10' style='background-color: #e9ecef; border-radius:5px; padding: .75rem 1rem;'><span style='font-size: 22px;'>$key->kategori_nm</span> <i style='float:right;' class='fas fa-chevron-right'></i></div>";
						}
				$produk .= "</div></div>";
				$return = array('status' => 'kategori','billing' => "", 'produk' => $produk);
		}
		echo json_encode($return,JSON_UNESCAPED_SLASHES);
	}

	public function showpindahmeja() {
		$billing_id = $this->request->getPost('billing_id');
		$meja_id = $this->request->getPost('meja_id');

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

	public function setnullifieditem(){
		$id = $this->request->getPost('value');
		$billing_id = $this->request->getPost('billing_id');

		$data = $this->billingmodel->getCountitem($billing_id)->getResult();
		echo json_encode($billing_id);
		if ($data[0]->jumlahitem <= "1") {
			$res = $this->billingmodel->setnullifiedbilling($billing_id);
			if ($res) {
				$res = $this->billingmodel->setnullifieditem($id);
				return true;
			} else {
				return false;
			}
		} else {
			$res = $this->billingmodel->setnullifieditem($id);
			if ($res) {
				return true;
			} else {
				return false;
			}
		}
		
	}

	public function discountkasir() {
		$res = $this->discountmodel->getbyNormal()->getResult();
			$ret = "<div class='modal-dialog modal-lg'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='btn btn-info' onclick='formtambahdiskon()' style='font-size: 25px;'>+</button>"
	            . "<h4 class='modal-title'>Pilih Diskon</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div><table  width='100%'>";
	            foreach ($res as $key) {
	            $ret .= "<tr style='border-bottom: 1px solid #ccc; line-height: 60px; font-size: 25px; font-weight: bold;'>"
	            	 . "<td align='left'><button onclick='addDiscount($key->discount_id)' class='btn btn-outline-primary' style='font-size: 20px; color: black; font-weight: bold;'>$key->discount_nm</button></td>"
	            	 . "<td align='right'>$key->value</td>"
	            	 . "</tr>";
	            }
	       $ret .= "</table></div>"
	       		. "</div>"
	            . "</div>"
	            . "</div>";
		
		return $ret;
	}

	public function memberkasir() {
		$res = $this->membermodel->getbyNormal()->getResult();
			$ret = "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<button type='button' class='btn btn-info' onclick='formtambahmember()' style='font-size: 25px;'>+</button>"
	            . "<h2 class='modal-title'>SILAHKAN PILIH MEMBER</h2>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div class='row'>"
                . "<div class='col-md-12'>"
	            . "<div class='card'>"
                . "<div class='card-body'>"
                . "<div class='col-md-12'>"
                . "<div class='table-responsive'>"
	            . "<table width='100%' class='table-striped' data-toggle='table' data-height='300' data-mobile-responsive='true'>"
	            . "<thead>"
	            . "<tr>"
	            . "<th>NAMA</th>"
	            . "<th>NO HP</th>"
	            . "</tr>"
	            . "</thead>"
	            . "<tbody>";
	            foreach ($res as $key) {
	            $ret .= "<tr style='font-size: 20px; font-weight: bold;'>"
	            	 . "<td align='left'><button onclick='addmember($key->member_id);' class='btn btn-outline-primary' style='font-size: 16px; color: black; font-weight: bold;'>$key->person_nm</button></td>"
	            	 . "<td align='right'>$key->cellphone</td>"
	            	 . "</tr>";
	            }
	       $ret .= "</tbody></table>"
	       		. "</div>" // class table-responsive
                // . "<div class='form-group' style='margin-bottom: 10px;'>"
                // . "<select class='select2 form-control custom-select' style='width: 100%; height:36px;'>";
                // foreach ($res as $key) {
                // 	$ret .= "<option value='$key->member_id'>$key->person_nm</option>";
                // }
                // $ret .= "</select>"
                // . "</div>"
                . "</div>"
	            . "</div>"
	            . "</div>" //card
	            . "</div>"
	            . "</div>" //row

	       		. "</div>" //modal-body
	            . "</div>"
	            . "</div>";
	            $ret .= "<script src='../assets/plugins/bootstrap-table/dist/bootstrap-table.min.js'></script>";
		
		return $ret;
	}


	public function formtambahmember() {
		$ret = "<div class='modal-dialog modal-xl'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h4 class='modal-title' id='myModalLabel'>Modal Heading</h4>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
                        </div>
                        <form id='forms' method='POST' enctype='multipart/form-data'>
                        <div class='modal-body'>
                            	<div class='row p-t-20'>
                                    <div class='col-md-3'>
                                        <div class='form-group'>
                                            <label class='control-label'>Nama Lengkap</label>
                                            <input type='text' id='person_nm' class='form-control' placeholder='Nama Lengkap' required=''>
                                        </div>
                                    </div>
                                    <div class='col-md-3'>
                                        <div class='form-group'>
                                            <label class='control-label'>Kode Member</label>
                                            <input type='text' id='member_cd' class='form-control' placeholder='Kode Member'>
                                        </div>
                                    </div>
                                    <div class='col-md-3'>
                                        <div class='form-group'>
                                            <label class='control-label'>No HP</label>
                                            <input type='text' id='cellphone' class='form-control' placeholder='No HP'>
                                        </div>
                                    </div>
                                    <div class='col-md-3'>
                                        <div class='form-group'>
                                            <label class='control-label'>Jenis Kelamin</label>
                                            <select id='gender_cd' class='form-control' required=''>
                                            <option value='l'>Laki-laki</option>
                                            <option value='m'>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Email</label>
                                            <input type='text' id='email' class='form-control' placeholder='example@gmail.com'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>No Identitas</label>
                                            <input type='text' id='ext_id' class='form-control' placeholder='No Identitas'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tempat Lahir</label>
                                            <input type='text' id='birth_place' class='form-control' placeholder='Tempat Lahir'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Tanggal Lahir</label>
                                            <input type='date' id='birth_dttm' class='form-control'>
                                        </div>
                                    </div>
                                    <div class='col-md-4'>
                                        <div class='form-group'>
                                            <label class='control-label'>Alamat</label>
                                            <textarea id='addr_txt' class='form-control' placeholder='Alamat'></textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class='modal-footer'>
                        	<button onclick='simpan()' type='button' class='btn btn-info waves-effect'>Simpan</button>
                        	<button type='button' class='btn btn-info waves-effect' data-dismiss='modal'>Close</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>";
        return $ret;
	}

	public function formtambahdiskon() {
		$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Tambah Diskon</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<div class='form-group'>"
	            . "<label for='namadiscount' class='control-label'>Nama discount</label>"
	            . "<input type='text' class='form-control' id='namadiscount'>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label class='control-label'>Nilai discount</label>"
	            . "<input type='text' class='form-control' id='nilaidiscount'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpandiskon()' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";

	    return $ret;
	}

	public function showcheckout(){
		$id = $this->request->getPost('id');
		$gt = $this->request->getPost('gt');
		$res = $this->payplanmodel->_getbynormaledc()->getResult();
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Pilih Cara Bayar</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<div><label style='color:black !important;'>TUNAI</label></div>"
	            . "<div class='btn-group btn-group-toggle' data-toggle='buttons' style='display:block !important;'>";
	            $ret .= "<label class='btn btn-outline-primary' style='font-size: 20px; margin:5px !important; color:black !important;'>
                            <input class='radiopayment' type='radio' name='payplan' id='tunai1' data-payplan-id='1' value='".number_format($gt)."' autocomplete='off'>".number_format($gt)."
                        </label>";
                $ret .= "<input style='height:45px; border-radius: 5px; margin-left: 10px; font-size: 20px; text-align: center;' type='text' name='paymen_tunai' id='tunai3' data-paymen-id='1' placeholder='0' data-mask='#.##0' data-mask-reverse='true' data-mask-maxlength='false'/>";
                $ret .= "<div style='margin-top: 20px;'><label style='color:black !important;'>EDC</label></div>";
	            foreach ($res as $key) {
	            $ret .= "<label style='width: 100px; height: 70px; margin: 5px; color:black !important;' class='btn btn-outline-primary radiopayment'>
                            <input type='radio' name='payplan' data-payplan-id='$key->payplan_id' value='".number_format($gt)."' id='edc$key->payplan_id' autocomplete='off'><span style='justify-content:center; display:flex;  align-items:center; width:100%; height:100%;'>$key->payplan_nm</span>
                        </label> ";
	            }
	            $ret .= "</div> ";
	       $ret .= "<div align='right'><button data-dismiss='modal' aria-hidden='true' type='button' class='btn btn-secondary'>Batal</button> <button onclick='checkout($id,$gt,this)' class='btn btn-success' type='button'>Simpan</button></div>"
	       		. "</form>"
	       		. "</div>"
	            . "</div>"
	            . "</div>";
	        $ret .= "<script src='../assets/js/jquery.mask.js'></script>";
		
		return $ret;	
	}

	public function showadddetail() {
		$produk_id = $this->request->getPost('produk_id');
			$ret = "<div class='modal-dialog modal-lg'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>MASUKKAN JUMLAH ITEM</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div class='col-md-6'>"
	            // . "<form>"
	            . "<div class='form-group'>"
	            . "<label for='jumlah' class='control-label'>Jumlah </label>"
	            . "<div class='input-group'>"
                . "<input id='jumlah' class='form-control' type='number' value='1' name='jumlah' style='font-weight: bold; font-size: 20px;'>"
                . "<button type='button' onclick='minusjumlah()' class='btn btn-success font-weight-bold' style='font-size: 40px; height: 40px; width: 40px; line-height: 25px; margin-left:5px;'>-</button>"
                . "<button type='button' onclick='addjumlah()' class='btn btn-success font-weight-bold' style='font-size: 27px; height: 40px; width: 40px; line-height: 25px; margin-left: 5px;'>+</button>"
                . "</div>"
                . "<div class='form-group'>"
	            . "<label for='jumlah' class='control-label'>Catatan </label>"
	            . "<div class='input-group'>"
                . "<textarea id='catatan' class='form-control' style='font-weight: bold; font-size: 20px;'></textarea>"
                . "</div>"
                . "</div>"
                . "<div>"
                . "<button onclick='simpanproduk($produk_id,this)' class='btn btn-info'>Simpan</button>"
                . "</div>"
                // . "</form>"
                . "</div>"
	       		. "</div>" // modal body
	            . "</div>"
	            . "</div>";
		
		return $ret;
	}

	public function billinghistoryfinish() {
		$history = $this->billingmodel->getbyfinish()->getResult();
		$ret = "";
		$no = 1;
		if (count($history)>0) {
			$ret .= "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>History Transaksi</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
				 . "<div class='row'>"
				 . "<div class='col-md-12'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"
				 . "<table width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<thead>"
				 . "<tr>"
				 . "<th>No.</th>"
				 . "<th>Billing Kode</th>"
				 . "<th>Meja</th>"
				 . "<th>Grand Total</th>"
				 . "<th>Cetak Payments</th>"
				 . "</tr>"
				 . "</thead>"
				 . "<tbody>";
				 foreach ($history as $key) {
				 	$ret .= "<tr>"
						 . "<td>".$no++."</td>"
						 . "<td>$key->billing_cd</td>"
						 . "<td>MEJA - $key->meja_nm</td>"
						 . "<td>".number_format($key->ttl_amount)."</td>"
						 . "<td><button onclick='cetakulangcheckout($key->meja_id,$key->billing_id,this)' class='btn btn-info'>Cetak Payments</button></td>"
						 . "</tr>";
				 }

			$ret .= "</tbody>"
				 . "</table>"
				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>" // row
	       		. "</div>" // modal body
	            . "</div>"
	            . "</div>";

	        $ret .= "<script src='../assets/plugins/bootstrap-table/dist/bootstrap-table.min.js'></script>";
		} else {
			$ret .= "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>History Transaksi</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
				 . "<div class='row'>"
				 . "<div class='col-md-12'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"
				 . "<div align='center'><h3><strong>BELUM ADA HISTORY ORDER</strong></h3></div>"
				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>" // row
	       		. "</div>" // modal body
	            . "</div>"
	            . "</div>";
		}

		return $ret;
	}

	public function billinghistoryverified() {
		$history = $this->billingmodel->getbyverified()->getResult();
		$ret = "";
		$no = 1;
		if (count($history)>0) {
			$ret .= "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>History Transaksi</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
				 . "<div class='row'>"
				 . "<div class='col-md-12'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"
				 . "<table width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<thead>"
				 . "<tr>"
				 . "<th>No.</th>"
				 . "<th>Billing Kode</th>"
				 . "<th>Meja</th>"
				 . "<th>Grand Total</th>"
				 . "<th>Drinks</th>"
				 . "<th>Foods</th>"
				 . "</tr>"
				 . "</thead>"
				 . "<tbody>";
				 foreach ($history as $key) {
				 	$ret .= "<tr>"
						 . "<td>".$no++."</td>"
						 . "<td>$key->billing_cd</td>"
						 . "<td>MEJA - $key->meja_nm</td>"
						 . "<td>$key->ttl_amount</td>"
						 . "<td align='center'><button onclick='cetakulangdrinks($key->meja_id,$key->billing_id,this)' class='btn btn-warning'>Cetak Drinks</button></td>"
						 . "<td><button onclick='cetakulangfoods($key->meja_id,$key->billing_id,this)' class='btn btn-danger'>Cetak Foods</button></td>"
						 . "</tr>";
				 }

			$ret .= "</tbody>"
				 . "</table>"
				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>" // row
	       		. "</div>" // modal body
	            . "</div>"
	            . "</div>";

	        $ret .= "<script src='../assets/plugins/bootstrap-table/dist/bootstrap-table.min.js'></script>";
		} else {
			$ret .= "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>History Transaksi</h4>"
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
				 . "<div class='row'>"
				 . "<div class='col-md-12'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"
				 . "<div align='center'><h3><strong>BELUM ADA ACTIVITY</strong></h3></div>"
				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>" // row
	       		. "</div>" // modal body
	            . "</div>"
	            . "</div>";
		}

		return $ret;
	}

	public function tambah_nol($angka,$jumlah)
    {
       $jumlah_nol = strlen($angka);
       $angka_nol = $jumlah - $jumlah_nol;
       $nol = "";
       for($i=1;$i<=$angka_nol;$i++)
       {
          $nol .= '0';
       }
       return $nol.$angka;
    }

    public function openkasir() {
    	$date = date('Y-m-d');
    	$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Open Kasir</h4>"
	             . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form id='openkasir'>"
	            . "<div class='form-group'>"
	            . "<label for='namadiscount' class='control-label'>Tanggal Buka</label>"
	            . "<input type='date' class='form-control' id='open_dttm' value='$date'>"
	            . "</div>"
	            . "<div class='form-group'>"
	            . "<label class='control-label'>Modal Awal</label>"
	            . "<input type='text' placeholder='0' class='form-control' id='nilaimodal' data-mask='#.##0' data-mask-reverse='true' data-mask-maxlength='false'>"
	            . "</div>"
	            . "</form>"
	            . "</div>"
	            . "<div class='modal-footer'>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpanopenkasir()' type='button' class='btn btn-danger waves-effect waves-light'>Simpan</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";
	    $ret .= "<script src='../assets/js/jquery.mask.js'></script>";
	    return $ret;
    }

    public function closekasir() {
    	$kasir_status = $this->billingmodel->getStatuskasir()->getResult();
    	$kasir_status_id = $kasir_status[0]->kasir_status_id;
    	$topitem = $this->billingmodel->getTopitem($kasir_status_id)->getResult();
    	$edc = $this->billingmodel->getPayplanEdc($kasir_status_id)->getResult();
    	$tunai = $this->billingmodel->getPayplanTunai($kasir_status_id)->getResult();
    	$getReport = $this->billingmodel->getReport($kasir_status_id)->getResult();
    	$getVoid = $this->billingmodel->getVoid($kasir_status_id)->getResult();
    	$qtyvoid = count($getVoid);
    	$ttlvoid = 0;
    	if ($qtyvoid > 0) {
    		foreach ($getVoid as $void) {
    			$ttlvoid = $ttlvoid + $void->totalvoid;
    		}
    	}
    	$netsales = $getReport[0]->grosssales - $getReport[0]->ttldiscount;
		$ret = "";
		$no = 1;
		$nopayplan = 1;
		$notunai = 1;
		$totaltunai = 0;
    	$date = date('Y-m-d');
    	$ret = "<div class='modal-dialog modal-xl'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            // . "<h4 class='modal-title'>Close Kasir</h4>"
	            . "<h4 for='namadiscount' class='control-label modal-title'>Tanggal Tutup</h4>"
	            . "<div class='col-md-3'>"
	            . "<form>"
	            . "<div>"
	            . "<input style='font-size: 20px; font-weight: bold;' type='date' class='form-control' id='closed_dttm' value='$date'>"
	            . "</div>"
	            . "</form>"
	            . "</div>" //col-md-4
	            . "<button type='button' class='btn btn-warning' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div class='row'>"
				 . "<div class='col-md-5'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"
                 . "<h3><strong>SALES</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 	$ret .= "<tr>"
						 . "<td width='150'>Gross Sales</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->grosssales)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Discounts</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->ttldiscount)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Net Sales</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($netsales)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Service</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->totalservice)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Tax</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->totaltax)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Void (".$qtyvoid.")</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($ttlvoid)."</td>"
						 . "</tr>";
				 

			$ret .= "</tbody>"
				 . "</table>" // GROSS SALES

				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>TOP ITEMS</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%'  data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($topitem as $key) {
				 	$ret .= "<tr>"
						 . "<td width='20'>".$no++.".</td>"
						 . "<td width='50%'>$key->produk_nm</td>"
						 . "<td width='50'>$key->totalqty X</td>"
						 . "<td align='right'>Rp. ".number_format($key->totalprice)."</td>"
						 . "</tr>";
				}
				 

			$ret .= "</tbody>"
				 . "</table>" // TOP ITEMS

				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-12

				. "<div class='col-md-7'>"
				 . "<div class='card' style='margin-bottom: 0px !important;'>"

				 . "<div class='card-body'>" // card-body paypaln
				 . "<h3><strong>EDC</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($edc as $kedc) {
					 	$ret .= "<tr>"
							 . "<td width='20'>".$nopayplan++.".</td>"
							 . "<td width='50%'>$kedc->payplan_nm</td>"
							 . "<td align='right'>$kedc->totalpayplan</td>"
							 . "<td align='right'>Rp. ".number_format($kedc->ttlamount)."</td>"
							 . "</tr>";
					}
				 

			$ret .= "</tbody>"
				 . "</table>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>PAYMENT</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($tunai as $ktunai) {
					 	$ret .= "<tr>"
							 . "<td width='20'></td>"
							 . "<td width='50%'>$ktunai->payplan_nm</td>"
							 . "<td align='right'>$ktunai->totalpayplan</td>"
							 . "<td align='right'>Rp. ".number_format($ktunai->ttlamount)."</td>"
							 . "</tr>";
							 $totaltunai = $ktunai->ttlamount;
					}


			$ret .= "<tr>"
				 . "<td width='20'></td>"
				 . "<td width='50%'>MODAL</td>"
				 . "<td align='right'> : </td>"
				 . "<td align='right'>Rp. ".number_format($kasir_status[0]->modal)."</td>"
				 . "</tr>"
				 . "<tr>"
				 . "<td width='20'></td>"
				 . "<td width='50%' style='font-weight:bold; border-top: 1px solid #000;'>TOTAL CASH COLLECTED</td>"
				 . "<td align='right'> : </td>"
				 . "<td align='right' style='font-weight:bold; border-top: 1px solid #000;'>Rp. ".number_format($kasir_status[0]->modal + $totaltunai)."</td>"
				 . "</tr>"
				 . "</tbody>"
				 . "</table>"
				 . "</div>" // card-body paypaln
				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>" // row
	            . "</div>"
	            . "<div class='modal-footer'>"
	            
	            . "<div class='switch'>"
                . "<label>OFF<input type='checkbox' id='checkboxprintclosekasir'><span class='lever'></span>ON</label>"
                . "</div>"
	            . "<button type='button' class='btn btn-default waves-effect' data-dismiss='modal'>Close</button>"
	            . "<button onclick='simpanclosekasir()' type='button' class='btn btn-danger waves-effect waves-light'>Tutup Kasir</button>"
	            . "</div>"
	            . "</div>"
	            . "</div>";

	    return $ret;
    }

     public function simpanclosekasir() {
     	$closed_dttm = $this->request->getPost('closed_dttm');
     	$checkprint = $this->request->getPost('checkprint');
    	$email = \Config\Services::email();
    	$kasir_status = $this->billingmodel->getStatuskasir()->getResult();
    	$kasir_status_id = $kasir_status[0]->kasir_status_id;
    	
    	$topitem = $this->billingmodel->getTopitem($kasir_status_id)->getResult();
    	$edc = $this->billingmodel->getPayplanEdc($kasir_status_id)->getResult();
    	$tunai = $this->billingmodel->getPayplanTunai($kasir_status_id)->getResult();
    	$getReport = $this->billingmodel->getReport($kasir_status_id)->getResult();
    	$getVoid = $this->billingmodel->getVoid($kasir_status_id)->getResult();
    	$qtyvoid = count($getVoid);
    	$ttlvoid = 0;
    	if ($qtyvoid > 0) {
    		foreach ($getVoid as $void) {
    			$ttlvoid = $ttlvoid + $void->totalvoid;
    		}
    	}
    	$netsales = $getReport[0]->grosssales - $getReport[0]->ttldiscount;

		$ret = "";
		$no = 1;
		$nopayplan = 1;
    	$pesan = "<div class='row'>"
				 . "<div class='col-md-5'>"
				 . "<div class='card'>"
                 . "<div class='card-body'>"

                 . "<hr style='border: solid 1px red'/>"
                 . "<h3><strong>SALES</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%'  data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 	$pesan .= "<tr>"
						 . "<td width='150'>Gross Sales</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->grosssales)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Discounts</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->ttldiscount)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Net Sales</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($netsales)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Service</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->totalservice)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Tax</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($getReport[0]->totaltax)."</td>"
						 . "</tr>"

						 . "<tr>"
						 . "<td width='150'>Void (".$qtyvoid.")</td>"
						 . "<td width='20'>:</td>"
						 . "<td align='right'>Rp. ".number_format($ttlvoid)."</td>"
						 . "</tr>";
				 

			$pesan .= "</tbody>"
				 . "</table>" // GROSS SALES

				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>TOP ITEMS</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($topitem as $key) {
				 	$pesan .= "<tr>"
						 . "<td width='20'>".$no++.".</td>"
						 . "<td width='50%'>$key->produk_nm</td>"
						 . "<td width='50'>$key->totalqty X</td>"
						 . "<td align='right'>Rp. ".number_format($key->totalprice)."</td>"
						 . "</tr>";
				}
				 

			$pesan .= "</tbody>"
				 . "</table>" // TOP ITEMS

				 
				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-5

				. "<div class='col-md-7'>"
				 . "<div class='card' style='margin-bottom: 0px !important;'>"
				 . "<div class='card-body'>" // card-body paypaln
				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>EDC</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($edc as $kedc) {
					 	$pesan .= "<tr>"
							 . "<td width='20'>".$nopayplan++.".</td>"
							 . "<td width='50%'>$kedc->payplan_nm</td>"
							 . "<td>$kedc->totalpayplan</td>"
							 . "<td align='right'>Rp. ".number_format($kedc->ttlamount)."</td>"
							 . "</tr>";
					}
				 

			$pesan .= "</tbody>"
				 . "</table>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>TUNAI</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:22px;' width='100%' data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($tunai as $ktunai) {
					 	$pesan .= "<tr>"
							 . "<td width='50%'>$ktunai->payplan_nm</td>"
							 . "<td>$ktunai->totalpayplan</td>"
							 . "<td align='right'>Rp. ".number_format($ktunai->ttlamount)."</td>"
							 . "</tr>";
					}
				 

			$pesan .= "<tr>"
				 . "<td width='50%'>MODAL</td>"
				 . "<td align='right'> : </td>"
				 . "<td align='right'>Rp. ".number_format($kasir_status[0]->modal)."</td>"
				 . "</tr>"
				 . "<tr>"
				 . "<td width='50%' style='font-weight:bold; border-top: 1px solid #000;'>TOTAL CASH COLLECTED</td>"
				 . "<td align='right'> : </td>"
				 . "<td align='right' style='font-weight:bold; border-top: 1px solid #000;'>Rp. ".number_format($kasir_status[0]->modal + $ktunai->ttlamount)."</td>"
				 . "</tr>"
				 . "</tbody>"
				 . "</table>"
				 . "</div>" // card-body paypaln

				 . "</div>" // card
				 . "</div>" // col-md-12
				 . "</div>"; // row

	   	$email->setTo('harrypurmanta@gmail.com');
		$email->setFrom('lavitabellakasir@gmail.com','MAIL REPORT');
		$email->setSubject('SALES REPORT '.$closed_dttm);
		$email->setMessage($pesan);
		$sendit = $email->send();
		
    		$cekunclosed = $this->billingmodel->getbyunclosed()->getResult();
	    	$jam = date('H:i:s');
	    	if (count($cekunclosed)>0) {
	    		$ret = "belumfinish";
	    	} else {

	    		$datastatuskasir = [
					'status_cd' => 'closed',
					'closed_dttm' => $closed_dttm.' '.$jam,
					'closed_user' => $this->session->user_id,
	 			];

	 			$data = [
				  'status_cd' => 'closed',
				  'closed_dttm' => $closed_dttm.' '.$jam,
				  'closed_user' => $this->session->user_id,
				];

	    		if ($checkprint == "true") {
		    		$ret = $this->cetakPrintclosekasir($kasir_status_id,$kasir_status[0]->modal);
		    		$this->billingmodel->updatestatuskasir($kasir_status_id,$datastatuskasir);
		    		$this->billingmodel->closedkasir($kasir_status_id,$data);
		    	} else {
		    		$this->billingmodel->updatestatuskasir($kasir_status_id,$datastatuskasir);
		    		$this->billingmodel->closedkasir($kasir_status_id,$data);
		    	}

		    	$this->reportTopdf($closed_dttm);
	    	}
	    	echo $ret;
		
    }


    public function reportTopdf($closed_dttm) {
    	$totalbilling = $this->billingmodel->getTotalbill($closed_dttm)->getResult();
    	$totalvoid = $this->billingmodel->getVoidbill($closed_dttm)->getResult();
    	$totalitem = $this->billingmodel->getTotalitem($closed_dttm)->getResult();
    	$pdf = new FPDF();
        $pdf->AddPage('P', 'A4');
		$pdf->SetFont('Arial','B',12);
		$pdf->Image('images/lib/logo.png',10,6,30);
		$pdf->SetX(45);
		$pdf->Cell(40,5,'Butcher Steak & Pasta Palembang');
		$pdf->SetX(45);
		$pdf->Cell(40,15,'Jl. AKBP Cek Agus No. 284, Palembang');
		$pdf->SetX(45);
		$pdf->Cell(40,25,'Sumatera Selatan, 30114, 07115626366');
		$pdf->SetLineWidth(0.5);
        $pdf->Line(6.5, 35, 204, 35);
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',14);
        $pdf->SetX(70);
		$pdf->Cell(40,50,'LAPORAN PENJUALAN',0,0,'c');
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->SetX(15);
		$pdf->Cell(40,50,'SUMMARY',0,0,'L');
		$pdf->SetFont('Arial','',12);
		$pdf->SetX(15);
		$pdf->Cell(20,65,'Billing',0,0,'L');
		$pdf->Cell(5,65,':',0,0,'L');
		$pdf->Cell(20,65,$totalbilling[0]->billing_id,0,0,'L');
		$pdf->SetX(15);
		$pdf->Cell(20,75,'Item',0,0,'L');
		$pdf->Cell(5,75,':',0,0,'L');
		$pdf->Cell(20,75,'Rp. '.number_format($totalitem[0]->price),0,0,'L');
		$pdf->SetX(15);
		$pdf->Cell(20,85,'Service',0,0,'L');
		$pdf->Cell(5,85,':',0,0,'L');
		$pdf->Cell(20,85,'total',0,0,'L');
		$pdf->SetX(15);
		$pdf->Cell(20,95,'Tax',0,0,'L');
		$pdf->Cell(5,95,':',0,0,'L');
		$pdf->Cell(20,95,'total',0,0,'L');
		$pdf->SetX(15);
		$pdf->Cell(20,105,'Void',0,0,'L');
		$pdf->Cell(5,105,':',0,0,'L');
		$pdf->Cell(20,105,$totalvoid[0]->billing_id,0,0,'L');
		$pdf->SetX(15);
		$pdf->Cell(20,115,'Revenue',0,0,'L');
		$pdf->Cell(5,115,':',0,0,'L');
		$pdf->Cell(20,115,'total',0,0,'L');
		$pdf->Output('report/Laporan_penjualan_'.$closed_dttm.'.pdf', 'F');
		exit();
    }

    public function simpanopenkasir() {
    	$jam = date('H:i:s');
    	$open_dttm = $this->request->getPost('open_dttm');
    	$nilaimodal = str_replace(".", "", $this->request->getPost('nilaimodal'));
	    	$data = [
	    	  'modal' => $nilaimodal,
			  'status_cd' => 'open',
			  'open_dttm' => $open_dttm.' '.$jam,
			  'open_user' => $this->session->user_id,
			];
			$res = $this->billingmodel->simpanopenkasir($data);
			if ($res != "") {
				$this->session->set('kasir_status_id',$res);
				$ret = "true";
			} else {
				$ret = "false";
			}
    	
		return $ret;
    }

   


	public function addproduktobill() {
		$meja_id 			= $this->request->getPost('meja_id');
		$produk_id 			= $this->request->getPost('produk_id');
		$qty 				= $this->request->getPost('jumlah');
		$catatan 			= $this->request->getPost('catatan');
		$jumlah_customer 	= $this->request->getPost('jumlah_customer');
		$collected_user 	= $this->request->getPost('collected_user');
		$date 				= date('Y-m-d H:i:s');
		$getbill 			= $this->billingmodel->getbyMejaidkasir($meja_id)->getResult();
		$kasir_status 		= $this->billingmodel->getStatuskasir()->getResult();
		$kasirstatus 		= $kasir_status[0]->kasir_status_id;
		
		if (count($getbill)>0) {
			$billing_id = $getbill[0]->billing_id;
		} else {
			$desclimit 	= $this->billingmodel->getDesclim1()->getResult();
			if (count($desclimit)>0) {
				$code = $desclimit[0]->billing_id + 1;
			} else {
				$code = 1;
			}

			$zero = str_pad($code, 4, "0", STR_PAD_LEFT);
			$billing_cd = "LAV$zero";

			$data = [
				'kasir_status_id' => $kasirstatus,
			  	'meja_id' => $meja_id,
			  	'billing_cd' => $billing_cd,
			  	'jumlah_customer' => $jumlah_customer,
			  	'status_cd' => 'verified',
			  	'collected_user' => $collected_user,
			  	'created_dttm' => $date,
			  	'created_user' => $meja_id
			];
			$billing_id = $this->billingmodel->simpanbilling($data);
		}

		if ($billing_id != "") {
			$produk = $this->produkmodel->getbyId($produk_id)->getResult();
			$harga = $produk[0]->produk_harga * $qty;
			$dataitem = [
				'billing_id' 	=> $billing_id,
				'item_dttm'  	=> $date,
				'produk_id'  	=> $produk_id,
				'produk_nm'  	=> $produk[0]->produk_nm,
				'qty' 		 	=> $qty,
				'price'		 	=> $harga,
				'description' 	=> $catatan,
				'status_cd' 	=> 'normal',
				'created_dttm' 	=> $date,
				'created_user' 	=> $meja_id
			];
			$bil_item = $this->billingmodel->simpanbillitem($dataitem);
			return 'true';
		} else {
			return 'false';
		}

	}

	public function adddiscounttobill() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');
        $bi = $this->request->getPost('bi');
        $cek = $this->billingmodel->getbybilldiscid($bi,$di)->getResult();
        if (count($cek)>0) {
        	return 'already';
        } else {
        	$data = [
			'billing_id' => $bi,
			'discount_id' => $di,
			'status_cd' => 'normal',
			'created_user' => $this->session->user_id,
			'created_dttm' => date('Y-m-d H:i:s')
			];
			$addtobill = $this->billingmodel->insertbilldisct($data);
			if ($addtobill) {
				return 'true';
			} else {
				return 'false';
			}
        }
	}

	public function addmembertobill() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');
        $bi = $this->request->getPost('bi');
		$data = [
			'member_id' => $di,
			'updated_user' => $this->session->user_id,
			'updated_dttm' => date('Y-m-d H:i:s')
		];
		$addtobill = $this->billingmodel->insertbillmember($bi,$data);
		if ($addtobill) {
			return 'true';
		} else {
			return 'false';
		}
	}

	public function removedc() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');

		$data = [
			'status_cd' => 'nullified',
			'nullified_dttm' => date('Y-m-d H:i:s'),
			'nullified_user' => $this->session->user_id
		];
		$removedc = $this->billingmodel->removedckasir($di,$data);
		if ($removedc) {
			return "true";
		} else {
			return "false";
		}
	}

	public function removedcmember() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');

		$data = [
			'status_cd' => 'nullified',
			'nullified_dttm' => date('Y-m-d H:i:s'),
			'nullified_user' => $this->session->user_id
		];
		$removedc = $this->billingmodel->removedckasir($di,$data);

		$databill = [
			'member_id' => 0,
		];
		$updatemember = $this->billingmodel->removemember($id,$databill);
		if ($removedc) {
			return "true";
		} else {
			return "false";
		}
	}

	public function removemember() {
		$id = $this->request->getPost('id');
		$billing_id = $this->request->getPost('billing_id');

		$data = [
			'member_id' => '0',
			'updated_dttm' => date('Y-m-d H:i:s'),
			'updated_user' => $this->session->user_id
		];
		$removemember = $this->billingmodel->removemember($billing_id,$data);
		if ($removemember) {
			return "true";
		} else {
			return "false";
		}
	}

	public function cetakmenudrinks() {
		return $this->_getDrinksmenu($this->request->getPost('id')); 
	}
	
	public function cetakmenufood(){
	    return $this->_getFoodsmenu($this->request->getPost('id'));
	}

	private function _getFoodsmenu($id) {
		$billing_item_id = array();
		$data = $this->billingmodel->getfoodmenu($id)->getResult();
		if  (count($data)>0) {
    		$this->profile = CapabilityProfile::load("POS-5890");
    		$this->connector = new RawbtPrintConnector();
    		// $this->connector = new FilePrintConnector("/dev/usb/lp0");
    		$member_nm = "MEJA ".$data[0]->meja_nm;
		    
    
    		$this->printer = new Printer($this->connector);
    		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
    		// Make sure you load a Star print connector or you may get gibberish.
    		try {
    
    		$date = date('Y-m-d H:i');
    		   
    		    /* Items */
    		    $this->printer->feed(5);
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->setFont(Printer::FONT_A);
    		    $this->printer->setEmphasis(true);
    		    $this->printer->setTextSize(2, 1);
    		    	$this->printer->setJustification(Printer::JUSTIFY_RIGHT);
    		    $this->printer->text($date."\n");
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->text($member_nm."\n");
    		    $this->printer->setTextSize(1, 2);
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($data as $item) {
    		    	$this->printer->setEmphasis(true);
    		        // $this->printer->text($item->produk_nm."\n");
    		        $this->printer->setEmphasis(false);
    		        $this->printer->text($this->getAsString(32,$item->qty."x",strtoupper($item->produk_nm))); // for 58mm Font A
    		        $this->printer->text($item->description."\n");
    		        $billing_item_id[] = $item->billing_item_id;

    		    }
    		    $this->printer->setEmphasis(false);
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->setEmphasis(false);
    		    $this->printer->feed(7);
    			
    		    /* Cut the receipt and open the cash drawer */
    		    // $this->printer->cut();
    		    // $this->printer->pulse();
    		$this->billingmodel->updateStatusfoodmenu($billing_item_id);
    		} catch (Exception $e) {
    		    echo $e->getMessage();
    		} finally {
    		    $this->printer->close();
    		}
    		
		} else {
		    echo json_encode(false);
		}
		
	}

	public function _getDrinksmenu($id) {
		$billing_item_id = array();
		$data = $this->billingmodel->getdrinkmenu($id)->getResult();
		if (count($data)>0) {
    		$this->profile = CapabilityProfile::load("POS-5890");
    		$this->connector = new RawbtPrintConnector();
    		// $this->connector = new FilePrintConnector("/dev/usb/lp0");
    		$member_nm = "MEJA ".$data[0]->meja_nm;
    
    		$this->printer = new Printer($this->connector);
    		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
    		// Make sure you load a Star print connector or you may get gibberish.
    		try {
    
    		    /* Information for the receipt */
    		    /* Date is kept the same for testing */
    		$date = date('Y-m-d H:i');
    		    /* Items */
    		    $this->printer->feed(5);
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->setFont(Printer::FONT_A);
    		    $this->printer->setEmphasis(true);
    		    $this->printer->setTextSize(2, 1);
    		    $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
    		    $this->printer->text($date."\n");
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->text($member_nm."\n");
    		    $this->printer->setTextSize(1, 2);
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($data as $item) {
    		    	$this->printer->setEmphasis(true);
    		        // $this->printer->text($item->produk_nm."\n");
    		        $this->printer->setEmphasis(false);
    		        $this->printer->text($this->getAsString(32,$item->qty."x",strtoupper($item->produk_nm))); // for 58mm Font A
    		        $this->printer->text($item->description."\n");
    		        $billing_item_id[] = $item->billing_item_id;
    		    }
    		    $this->printer->setEmphasis(false);
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->setEmphasis(false);
    		     $this->printer->feed(7);
    		 
    
    		    /* Cut the receipt and open the cash drawer */
    		    // $this->printer->cut();
    		    // $this->printer->pulse();
    		$this->billingmodel->updateStatusdrinkmenu($billing_item_id);
    		} catch (Exception $e) {
    		    echo $e->getMessage();
    		} finally {
    		    $this->printer->close();
    		}
    		
		} else {
		    echo json_encode(false);
		}
		
	}

	public function cetakulangdrinks() {
		$billing_id = $this->request->getPost('bi');
		$data = $this->billingmodel->cetakulangdrinks($billing_id)->getResult();
		if (count($data)>0) {
    		$this->profile = CapabilityProfile::load("POS-5890");
    		$this->connector = new RawbtPrintConnector();
    		// $this->connector = new FilePrintConnector("/dev/usb/lp0");
    		$member_nm = "MEJA ".$data[0]->meja_nm;
    
    		$this->printer = new Printer($this->connector);
    		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
    		// Make sure you load a Star print connector or you may get gibberish.
    		try {
    
    		    /* Information for the receipt */
    		    /* Date is kept the same for testing */
    		$date = date('Y-m-d H:i');
    		    /* Items */
    		    $this->printer->feed(5);
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->setFont(Printer::FONT_A);
    		    $this->printer->setEmphasis(true);
    		    $this->printer->setTextSize(2, 1);
    		    $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
    		    $this->printer->text($date."\n");
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->text($member_nm."\n");
    		    $this->printer->setTextSize(1, 2);
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($data as $item) {
    		    	$this->printer->setEmphasis(true);
    		        // $this->printer->text($item->produk_nm."\n");
    		        $this->printer->setEmphasis(false);
    		        $this->printer->text($this->getAsString(32,$item->qty."x",strtoupper($item->produk_nm))); // for 58mm Font A
    		        $this->printer->text($item->description."\n");
    		    }
    		    $this->printer->setEmphasis(false);
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->setEmphasis(false);
    		    $this->printer->feed(7);
    		 
    
    		    /* Cut the receipt and open the cash drawer */
    		    // $this->printer->cut();
    		    // $this->printer->pulse();
    		} catch (Exception $e) {
    		    echo $e->getMessage();
    		} finally {
    		    $this->printer->close();
    		}
    		
		} else {
		    echo json_encode(false);
		}
	}

	public function cetakulangfoods() {
		$billing_id = $this->request->getPost('bi');
		$data = $this->billingmodel->cetakulangfoods($billing_id)->getResult();
		if (count($data)>0) {
    		$this->profile = CapabilityProfile::load("POS-5890");
    		$this->connector = new RawbtPrintConnector();
    		// $this->connector = new FilePrintConnector("/dev/usb/lp0");
    		$member_nm = "MEJA ".$data[0]->meja_nm;
    
    		$this->printer = new Printer($this->connector);
    		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
    		// Make sure you load a Star print connector or you may get gibberish.
    		try {
    
    		    /* Information for the receipt */
    		    /* Date is kept the same for testing */
    		$date = date('Y-m-d H:i');
    		    /* Items */
    		    $this->printer->feed(5);
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->setFont(Printer::FONT_A);
    		    $this->printer->setEmphasis(true);
    		    $this->printer->setTextSize(2, 1);
    		    $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
    		    $this->printer->text($date."\n");
    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->text($member_nm."\n");
    		    $this->printer->setTextSize(1, 2);
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($data as $item) {
    		    	$this->printer->setEmphasis(true);
    		        // $this->printer->text($item->produk_nm."\n");
    		        $this->printer->setEmphasis(false);
    		        $this->printer->text($this->getAsString(32,$item->qty."x",strtoupper($item->produk_nm))); // for 58mm Font A
    		        $this->printer->text($item->description."\n");
    		    }
    		    $this->printer->setEmphasis(false);
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->setEmphasis(false);
    		    $this->printer->feed(7);
    		 
    
    		    /* Cut the receipt and open the cash drawer */
    		    // $this->printer->cut();
    		    // $this->printer->pulse();
    		} catch (Exception $e) {
    		    echo $e->getMessage();
    		} finally {
    		    $this->printer->close();
    		}
    		
		} else {
		    echo json_encode(false);
		}
	}

	public function cetakulangcheckout() {
		$billing_id = $this->request->getPost('bi');
		$data = $this->billingmodel->cetakulangcheckout($billing_id)->getResult();
		$discount_nmx = "";	 
		$discount_valuex = "";
		$discount = "";
		$discount_nm = "";
		$subtotal = 0;
		$ptotal = "";
		$ttl_discount = 0;
		$amt_before_discount = 0;
		$poinmb = 0;
		$nilaidiskon = "";
		if (count($data)>0) {
			
		    $member_nm = "MEJA ".$data[0]->meja_nm;
		    list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		    $resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();
			
			$this->profile = CapabilityProfile::load("POS-5890");
			$this->connector = new RawbtPrintConnector();
			// $this->connector = new FilePrintConnector("/dev/usb/lp0");


			$this->printer = new Printer($this->connector);
			$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
			// Make sure you load a Star print connector or you may get gibberish.
			try {

			    /* Information for the receipt */
			    /* Date is kept the same for testing */
			// $date = date('l jS \of F Y h:i:s A');
			   

			    /* Start the printer */
			    // $logo = EscposImage::load("images/lib/logo.png", false);
			    // $this->printer = new Printer($this->connector, $this->profile);


			    /* Print top logo */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    
			    // if ($this->profile->getSupportsGraphics()) {
			    //     $this->printer->graphics($logo);
			    // }
			    // if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
			    //     $this->printer->bitImage($logo);
			    // }

			    /* Name of shop */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text("Butcher Steak & Pasta\n");
			    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
			    $this->printer->text("07115626366\n");
			    $this->printer->selectPrintMode();
			    $this->printer->feed();
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text($this->buatBaris4Kolom(panjang($dt),"",$tm));
			    $this->printer->text($this->buatBaris4Kolom("Bill Name","",substr($member_nm, 0,8)));
			   $this->printer->text($this->buatBaris4Kolom("Collected by","",substr($data[0]->collected_user, 0,8)));
			    /* Title of receipt */
			    $this->printer->setEmphasis(true);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->feed(1);
			    

			    /* Items */
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    foreach ($data as $item) {
			    	$this->printer->setEmphasis(true);
					$total = $item->produk_harga * $item->qty;
					$amt_before_discount = $amt_before_discount + $total;
					if (count($resdc)>0) {
						foreach ($resdc as $dc) {
							$symb = substr($dc->value, -1);
							if ($symb == "%") {
								$percentega = str_replace("%", "", $dc->value);
								$ptotal = ($percentega/100) * $total;
								list($harga,$belakangkoma) = explode(".", $ptotal);
								$nilaidiskon = "(".$harga.")";
								$afterdc = $total - $harga;
								$subtotal = $subtotal + $afterdc;
								$discount_nmx = $dc->discount_nm;
								$discount_valuex = $dc->value;
								$ttl_discount = $ttl_discount + $ptotal; 
								$poinmb = $poinmb + $harga;
							} else {
								$subtotal = $subtotal + $total;
							}
						} 
					} else {
						$subtotal = $subtotal + $total;
					}

			        $this->printer->text(strtoupper($item->produk_nm)."\n");
			        $this->printer->setEmphasis(false);
			        $this->printer->text($this->buatBaris4Kolom($item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
			        $this->printer->text($this->buatBaris4Kolom($discount_nmx."".$discount_valuex," ",$nilaidiskon)."\n");
			    }

			    if (count($notpersen)>0) {
					foreach ($notpersen as $dc) {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
						$this->printer->text($this->buatBaris4Kolom($discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
						$subtotal = $subtotal - $dc->value;
						$ttl_discount = $ttl_discount + $dc->value; 
					}
				} 
			    $this->printer->setEmphasis(false);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(false);
			    $this->printer->feed();



			    $servicex = $amt_before_discount * 0.05;
				if (strpos($servicex,'.') == TRUE) {
					list($service,$belakangkomas) = explode(".", $servicex);
				} else {
					$service = $servicex;
				}

				$taxx = ($amt_before_discount + $service) * 0.10;
				if (strpos($taxx,'.') == TRUE) {
					list($tax,$belakangkoma) = explode(".", $taxx);
				} else {
					$tax = $taxx;
				}

				$grandtotal = $subtotal + $tax + $service;
				$jmlbulat = $this->pembulatanratusan($grandtotal);
				$nilaibulat = $jmlbulat - $grandtotal;

				if ($data[0]->payplan_id == 1) {
					$ttl_paid = $data[0]->ttl_paid;
				} else {
					$ttl_paid = $jmlbulat;
				}

				$kembalian = $ttl_paid - $jmlbulat;

			    $this->printer->setEmphasis(false);
				$this->printer->text($this->buatBaris4Kolom("Subtotal","",number_format($subtotal))); 
				$this->printer->text($this->buatBaris4Kolom("Tax","",number_format($tax))); 
				$this->printer->text($this->buatBaris4Kolom("Service","",number_format($service))); 
				$this->printer->text($this->buatBaris4Kolom("Rounding","",number_format($nilaibulat))); 
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(true);
				$this->printer->text($this->buatBaris4Kolom("Total","",number_format($jmlbulat))); 
				$this->printer->text($this->buatBaris4Kolom($data[0]->payplan_nm,"",""));
				$this->printer->text($this->buatBaris4Kolom("Kembalian","",number_format($kembalian)));
			    $this->printer->setEmphasis(false);
			    /*footer */
			    $this->printer->feed(2);
			    // $this->printer->text($date . "\n");
				$this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    $this->printer->text("TERIMA KASIH\n");
			    /* Cut the receipt and open the cash drawer */
			    // $this->printer->cut();
			    // $this->printer->pulse();

			} catch (Exception $e) {
			    echo $e->getMessage();
			} finally {
			    $this->printer->close();
			}
		} else {
			return 'false';
		}
	}

	public function cetakPrintclosekasir($kasir_status_id,$modal) {
    	$topitem = $this->billingmodel->getTopitem($kasir_status_id)->getResult();
    	$edc = $this->billingmodel->getPayplanEdc($kasir_status_id)->getResult();
    	$tunai = $this->billingmodel->getPayplanTunai($kasir_status_id)->getResult();
    	$getReport = $this->billingmodel->getReport($kasir_status_id)->getResult();
    	$getVoid = $this->billingmodel->getVoid($kasir_status_id)->getResult();
    	$qtyvoid = count($getVoid);
    	$ttlvoid = 0;
    	if ($qtyvoid > 0) {
    		foreach ($getVoid as $void) {
    			$ttlvoid = $ttlvoid + $void->totalvoid;
    		}
    	}
    	$netsales = $getReport[0]->grosssales - $getReport[0]->ttldiscount;
    	$date = date('Y-m-d H:i:s');

		
		if (count($getReport)>0) {
    		$this->profile = CapabilityProfile::load("POS-5890");
    		$this->connector = new RawbtPrintConnector();
    		// $this->connector = new FilePrintConnector("/dev/usb/lp0");
    
    		$this->printer = new Printer($this->connector);
    		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
    		// Make sure you load a Star print connector or you may get gibberish.
    		try {
    
    		    /* Information for the receipt */
    		    /* Date is kept the same for testing */
    		$date = date('Y-m-d H:i');
    		    /* Items */
    		    $this->printer->feed(1);
    		  	$this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->setTextSize(2, 2);
    		    $this->printer->text("SALES REPORT"."\n");
			    $this->printer->selectPrintMode();
			    $this->printer->setTextSize(1, 1);
			    $this->printer->feed();
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->feed(2);
    		    $this->printer->text("SALES"."\n");
    		    $this->printer->text("--------------------------------\n");

    		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
    		    $this->printer->setEmphasis(true);
    		    $this->printer->text($this->buatBaris4Kolom("Gross Sales",":",number_format($getReport[0]->grosssales)));
    		    $this->printer->text($this->buatBaris4Kolom("Discounts",":",number_format($getReport[0]->grosssales)));
    		    $this->printer->text($this->buatBaris4Kolom("Net Sales",":",number_format($netsales)));
    		    $this->printer->text($this->buatBaris4Kolom("Service",":",number_format($getReport[0]->totalservice)));
    		    $this->printer->text($this->buatBaris4Kolom("Tax",":",number_format($getReport[0]->totaltax)));
    		    $this->printer->text($this->buatBaris4Kolom("Void".$qtyvoid.")",":",number_format($ttlvoid)));

    		    $this->printer->feed();
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->feed(2);
    		    $this->printer->text("EDC"."\n");
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($edc as $kedc) {
    		    	$this->printer->text($this->buatBaris4Kolom($kedc->payplan_nm,$kedc->totalpayplan,number_format($kedc->ttlamount)));
    		    }

    		    $this->printer->feed();
    		    $this->printer->text("--------------------------------\n");
    		    $this->printer->feed(2);
    		    $this->printer->text("TUNAI"."\n");
    		    $this->printer->text("--------------------------------\n");
    		    foreach ($tunai as $ktunai) {
    		    	$this->printer->text($this->buatBaris4Kolom($ktunai->payplan_nm,$ktunai->totalpayplan,number_format($ktunai->ttlamount)));
    		    }
    		    $this->printer->text($this->buatBaris4Kolom("MODAL",":",number_format($modal)));
    		    $this->printer->text($this->buatBaris4Kolom("TOTAL CASH COLLECTED",":",number_format($modal + $ktunai->ttlamount)));

    		    $this->printer->setEmphasis(false);
    		    $this->printer->text($date);
    		    $this->printer->setEmphasis(false);
    		    $this->printer->feed(2);
    		} catch (Exception $e) {
    		    echo $e->getMessage();
    		} finally {
    		    $this->printer->close();
    		}
    		
		} else {
		    echo json_encode(false);
		}
	}

	public function cetakbilling() {
		$id = $this->request->getPost('id');
		$billing_id = $this->request->getPost('billing_id');
		$data = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$discount_nmx = "";	 
		$discount_valuex = "";
		$discount = "";
		$discount_nm = "";
		$subtotal = 0;
		$ptotal = "";
		$ttl_discount = 0;
		$amt_before_discount = 0;
		$nilaidiskon = "";

		if (count($data)>0) {
			$member_nm = "Meja ".$data[0]->meja_nm;

		    list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		    $resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();

			$this->profile = CapabilityProfile::load("POS-5890");
			$this->connector = new RawbtPrintConnector();
			// $this->connector = new FilePrintConnector("/dev/usb/lp0");


			$this->printer = new Printer($this->connector);
			$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
			// Make sure you load a Star print connector or you may get gibberish.
			try {
				$logo = EscposImage::load("images/lib/logo.png", false);
			    /* Name of shop */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text("Butcher Steak & Pasta\n");
			    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
			    $this->printer->text("07115626366\n");
			    $this->printer->selectPrintMode();
			    $this->printer->feed();
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text($this->buatBaris4Kolom(panjang($dt),"",$tm));
			    $this->printer->text($this->buatBaris4Kolom("Bill Name","",substr($member_nm, 0,8)));
			   $this->printer->text($this->buatBaris4Kolom("Collected by","",substr($data[0]->collected_user, 0,8)));
			    /* Title of receipt */
			    $this->printer->setEmphasis(true);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->feed(1);
			    

			    /* Items */
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    foreach ($data as $item) {
			    	$this->printer->setEmphasis(true);
					$total = $item->produk_harga * $item->qty;
					$amt_before_discount = $amt_before_discount + $total;
					if (count($resdc)>0) {
						foreach ($resdc as $dc) {
							$symb = substr($dc->value, -1);
							if ($symb == "%") {
								$percentega = str_replace("%", "", $dc->value);
								$ptotal = ($percentega/100) * $total;
								list($harga,$belakangkoma) = explode(".", $ptotal);
								$nilaidiskon = "(".$harga.")";
								$afterdc = $total - $harga;
								$subtotal = $subtotal + $afterdc;
								$discount_nmx = $dc->discount_nm;
								$discount_valuex = $dc->value;
								$ttl_discount = $ttl_discount + $ptotal; 
							} else {
								$subtotal = $subtotal + $total;
							}
						} 
					} else {
						$subtotal = $subtotal + $total;
					}
			        $this->printer->text(strtoupper($item->produk_nm)."\n");
			        $this->printer->setEmphasis(false);
			        $this->printer->text($this->buatBaris4Kolom($item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
			        $this->printer->text($this->buatBaris4Kolom($discount_nmx."".$discount_valuex," ",$nilaidiskon)."\n");
			    }


	            if (count($notpersen)>0) {
					foreach ($notpersen as $dc) {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
						$this->printer->text($this->buatBaris4Kolom($discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
						$subtotal = $subtotal - $dc->value;
						$ttl_discount = $ttl_discount + $dc->value; 
					}
				} 
			    $this->printer->setEmphasis(false);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(false);
			    $this->printer->feed();
			    
				// $taxx = $amt_before_discount * 0.10;
				// list($tax,$belakangkoma) = explode(".", $taxx);
				// $servicex = $amt_before_discount * 0.05;
				// list($service,$belakangkoma) = explode(".", $servicex);
				$servicex = $amt_before_discount * 0.05;
				if (strpos($servicex,'.') == TRUE) {
					list($service,$belakangkomas) = explode(".", $servicex);
				} else {
					$service = $servicex;
				}

				$taxx = ($amt_before_discount + $service) * 0.10;
				if (strpos($taxx,'.') == TRUE) {
					list($tax,$belakangkoma) = explode(".", $taxx);
				} else {
					$tax = $taxx;
				}

				$grandtotal = $subtotal + $tax + $service;
				$jmlbulat = $this->pembulatanratusan($grandtotal);
				$nilaibulat = $jmlbulat - $grandtotal;
						
			    $this->printer->setEmphasis(false);
				$this->printer->text($this->buatBaris4Kolom("Subtotal","",number_format($subtotal))); 
				$this->printer->text($this->buatBaris4Kolom("Tax","",number_format($tax))); 
				$this->printer->text($this->buatBaris4Kolom("Service","",number_format($service))); 
				$this->printer->text($this->buatBaris4Kolom("Rounding","",number_format($nilaibulat))); 
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(true);
				$this->printer->text($this->buatBaris4Kolom("Total","",number_format($jmlbulat))); 
			    $this->printer->setEmphasis(false);
			    /*footer */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    // $this->printer->text("TERIMA KASIH\n");
			    /* Cut the receipt and open the cash drawer */
			    // $this->printer->cut();
			    // $this->printer->pulse();

			} catch (Exception $e) {
			    // echo $e->getMessage();
			} finally {
			    $this->printer->close();
			}

		} else {
			return 'false';
		}
		
	}

	public function cetakcheckout() {
		$id 		= $this->request->getPost('id');
		$gt 		= $this->request->getPost('gt');
		$meja_id 	= $this->request->getPost('meja_id');
		$billing_id = $this->request->getPost('billing_id');
		$payplan_id = $this->request->getPost('payplan_id');
		$paid 		= $this->request->getPost('paid');
		
		$data = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$discount_nmx = "";	 
		$discount_valuex = "";
		$discount = "";
		$discount_nm = "";
		$subtotal = 0;
		$ptotal = "";
		$ttl_discount = 0;
		$amt_before_discount = 0;
		$poinmb = 0;
		$nilaidiskon = "";
		if (count($data)>0) {
			
		    $member_nm = "MEJA ".$data[0]->meja_nm;
		    list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		    $resdc = $this->discountmodel->getbybillidpersen($billing_id)->getResult();
			$notpersen = $this->discountmodel->getbybillid($billing_id)->getResult();
			
			$this->profile = CapabilityProfile::load("POS-5890");
			$this->connector = new RawbtPrintConnector();
			// $this->connector = new FilePrintConnector("/dev/usb/lp0");


			$this->printer = new Printer($this->connector);
			$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
			// Make sure you load a Star print connector or you may get gibberish.
			try {

			    /* Information for the receipt */
			    /* Date is kept the same for testing */
			// $date = date('l jS \of F Y h:i:s A');
			   

			    /* Start the printer */
			    // $logo = EscposImage::load("images/lib/logo.png", false);
			    // $this->printer = new Printer($this->connector, $this->profile);


			    /* Print top logo */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    
			    // if ($this->profile->getSupportsGraphics()) {
			    //     $this->printer->graphics($logo);
			    // }
			    // if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
			    //     $this->printer->bitImage($logo);
			    // }

			    /* Name of shop */
			    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text("Butcher Steak & Pasta\n");
			    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
			    $this->printer->text("07115626366\n");
			    $this->printer->selectPrintMode();
			    $this->printer->feed();
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->text($this->buatBaris4Kolom(panjang($dt),"",$tm));
			    $this->printer->text($this->buatBaris4Kolom("Bill Name","",substr($member_nm, 0,8)));
			   $this->printer->text($this->buatBaris4Kolom("Collected by","",substr($data[0]->collected_user, 0,8)));
			    /* Title of receipt */
			    $this->printer->setEmphasis(true);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->feed(1);
			    

			    /* Items */
			    $this->printer->setFont(Printer::FONT_A);
			    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
			    foreach ($data as $item) {
			    	$this->printer->setEmphasis(true);
					$total = $item->produk_harga * $item->qty;
					$amt_before_discount = $amt_before_discount + $total;
					if (count($resdc)>0) {
						foreach ($resdc as $dc) {
							$symb = substr($dc->value, -1);
							if ($symb == "%") {
								$percentega = str_replace("%", "", $dc->value);
								$ptotal = ($percentega/100) * $total;
								list($harga,$belakangkoma) = explode(".", $ptotal);
								$nilaidiskon = "(".$harga.")";
								$afterdc = $total - $harga;
								$subtotal = $subtotal + $afterdc;
								$discount_nmx = $dc->discount_nm;
								$discount_valuex = $dc->value;
								$ttl_discount = $ttl_discount + $ptotal; 
								$poinmb = $poinmb + $harga;
							} else {
								$subtotal = $subtotal + $total;
							}
						} 
					} else {
						$subtotal = $subtotal + $total;
					}

			        $this->printer->text(strtoupper($item->produk_nm)."\n");
			        $this->printer->setEmphasis(false);
			        $this->printer->text($this->buatBaris4Kolom($item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
			        $this->printer->text($this->buatBaris4Kolom($discount_nmx."".$discount_valuex," ",$nilaidiskon)."\n");
			    }

			    if (count($notpersen)>0) {
					foreach ($notpersen as $dc) {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
						$this->printer->text($this->buatBaris4Kolom($discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
						$subtotal = $subtotal - $dc->value;
						$ttl_discount = $ttl_discount + $dc->value; 
					}
				} 
			    $this->printer->setEmphasis(false);
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(false);
			    $this->printer->feed();



			    $servicex = $amt_before_discount * 0.05;
				if (strpos($servicex,'.') == TRUE) {
					list($service,$belakangkomas) = explode(".", $servicex);
				} else {
					$service = $servicex;
				}

				$taxx = ($amt_before_discount + $service) * 0.10;
				if (strpos($taxx,'.') == TRUE) {
					list($tax,$belakangkoma) = explode(".", $taxx);
				} else {
					$tax = $taxx;
				}

				$grandtotal = $subtotal + $tax + $service;
				$jmlbulat = $this->pembulatanratusan($grandtotal);
				$nilaibulat = $jmlbulat - $grandtotal;

				if ($payplan_id == 1) {
					$ttlpaid = $paid;
					$ttl_paid = str_replace(".","",$ttlpaid);
				} else {
					$ttlpaid = $jmlbulat;
					$ttl_paid = $ttlpaid;
				}
				$kembalian = $ttl_paid - $jmlbulat;

			    $this->printer->setEmphasis(false);
				$this->printer->text($this->buatBaris4Kolom("Subtotal","",number_format($subtotal))); 
				$this->printer->text($this->buatBaris4Kolom("Tax","",number_format($tax))); 
				$this->printer->text($this->buatBaris4Kolom("Service","",number_format($service))); 
				$this->printer->text($this->buatBaris4Kolom("Rounding","",number_format($nilaibulat))); 
			    $this->printer->text("--------------------------------\n");
			    $this->printer->setEmphasis(true);
				$this->printer->text($this->buatBaris4Kolom("Total","",number_format($jmlbulat))); 
				$this->printer->text($this->buatBaris4Kolom($data[0]->payplan_nm,"",""));
				$this->printer->text($this->buatBaris4Kolom("Kembalian","",number_format($kembalian)));
			    $this->printer->setEmphasis(false);
			    /*footer */
			    $this->printer->feed(2);
			    // $this->printer->text($date . "\n");
			    
			    $updatebill = [
					'payplan_id' 			=> $payplan_id,
					'ttl_paid' 				=> $ttl_paid,
					'ttl_amount' 			=> $jmlbulat,
					'ttl_discount' 			=> $ttl_discount,
					'amt_before_discount' 	=> $amt_before_discount,
					'tax' 					=> $tax,
					'service' 				=> $service,
					'finish_dttm' 			=> date('Y-m-d H:i:s'),
					'finish_user' 			=> $this->session->user_id,
					'status_cd' 			=> 'finish'
				];
				$updatebill = $this->billingmodel->updateafterpayment($billing_id,$updatebill);

				if ($data[0]->member_nm != "") {
					$datapoin = [
						'member_id' => $data[0]->member_id,
						'poin_value' => $poinmb,
						'deposit_dttm' => date('Y-m-d H:i:s'),
						'trans_type' => 'deposit',
 					];
					$insertpoin = $this->billingmodel->insertpoin($datapoin);
				}


				$this->printer->setJustification(Printer::JUSTIFY_CENTER);
			    $this->printer->text("TERIMA KASIH\n");
			    /* Cut the receipt and open the cash drawer */
			    // $this->printer->cut();
			    // $this->printer->pulse();

			} catch (Exception $e) {
			    echo $e->getMessage();
			} finally {
			    $this->printer->close();
			}
		} else {
			return 'false';
		}
	}
	
	public function buatBaris4Kolom($kolom1, $kolom2, $kolom3) {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 12;
            $lebar_kolom_2 = 8;
            $lebar_kolom_3 = 10;
 
            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            $kolom3 = wordwrap($kolom3, $lebar_kolom_3, "\n", true);
 
            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
            $kolom3Array = explode("\n", $kolom3);
 
            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array), count($kolom3Array));
 
            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();
 
            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
 
                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
 
                // memberikan rata kanan pada kolom 3 dan 4 karena akan kita gunakan untuk harga dan total harga
                $hasilKolom3 = str_pad((isset($kolom3Array[$i]) ? $kolom3Array[$i] : ""), $lebar_kolom_3, " ", STR_PAD_LEFT);
 
                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2 . " " . $hasilKolom3;
            }
 
            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n",$hasilBaris) . "\n";
        }

    public function barisdapur($kolom1, $kolom2) {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 4;
            $lebar_kolom_2 = 30;
 
            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
 
            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array));
 
            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();
 
            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
 
                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
 
                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2;
            }
 
            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n",$hasilBaris) . "\n";
        }

	public function getAsString($width = 48,$qty,$produk_nm)
    {
        $rightCols = 10;
        $leftCols = 5;
        $left = str_pad($qty, $leftCols);
        $right = str_pad($produk_nm, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}
