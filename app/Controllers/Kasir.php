<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;
use App\Models\Mejamodel;
use App\Models\Billingmodel;
use App\Models\Discountmodel;
use App\Models\Membermodel;
use App\Models\Payplanmodel;
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
	protected $connector;
	protected $profile;
	protected $printer;
	protected $session;
	private $name;
	private $price;
	private $dollarSign;
	public function __construct(){
		
		$this->mejamodel = new Mejamodel();
		$this->billingmodel = new Billingmodel();
		$this->discountmodel = new Discountmodel();
		$this->membermodel = new Membermodel();
		$this->payplanmodel = new Payplanmodel();
	}
	
	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Kasir Dashboard',
			'subtitle' => 'Kasir',
			'meja' => $this->mejamodel->getbyNormal()
		];
		return view('backend/kasir', $data);

	}

	public function pembulatanratusan($uang){
	 $ratusan = substr($uang, -4);
	 $akhir = $uang + (100-$ratusan);
	 return $akhir;
	}

	public function pembulatanratusanribu($uang){
	 $ratusan = substr($uang, -6);
	 $akhir = $uang + (100000-$ratusan);
	 return $akhir;
	}

	public function getbymejaidkasir() {
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$discount_nmx = "";	 
		$discount_valuex = "";
		$discount = "";
		$discount_nm = "";
		$subtotal = 0;
		if (count($res)>0) {
		$billing_id = $res[0]->billing_id;
		list($dt,$tm) = explode(" ", $res[0]->created_dttm);
		$resdc = $this->discountmodel->getbybillid($billing_id)->getResult();
			
			$ret = "<div class='row col-md-12 m-0' id='div-item'>
				<input type='hidden' id='meja_id' value='$id'/>
				<input type='hidden' id='billing_id' value='$billing_id'/>
						<div class='col-md-12 m-0' align='center' style='margin-top: 30px;'>
							<p>
								<span style='font-size: 12px;line-height: 0px !important;'>Butcher Steak & Pasta Palembang</span><br>
								<span style='font-size: 12px;line-height: 0px !important;'>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
								<span style='font-size: 12px;line-height: 0px !important;'>Sumatera Selatan, 30114, 07115626366</span>
							</p>
						</div>
					</div>";
			$ret .= "<div class='row col-md-12 m-0'><table width='100%' style='margin-top: 20px;font-size: 12px;'>
				        <tr>
				          <td align='left'>".panjang($dt)."</td>
				          <td align='right'>".$tm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Bill Name</td>
				          <td align='right'>".$res[0]->member_nm."</td>
				        </tr>
				        <tr>
				          <td align='left'>Collected By</td>
				          <td align='right'>".$res[0]->collected_nm."</td>
				        </tr>
				      </table>
				      </div>
				      <hr style='border: 1px solid red'>
				      <div class='row col-md-12 m-0' style='overflow:auto;'><table class='active' style='font-size: 18px;' width='100%; '><tbody>";
			foreach ($res as $key) {
				$total = $key->produk_harga * $key->qty;
				if (count($resdc)>0) {
					foreach ($resdc as $dc) {
						$symb = substr($dc->value, -1);
						if ($symb == "%") {
							$percentega = str_replace("%", "", $dc->value);
							$ptotal = ($percentega/100) * $total;
							$discount = "<span>(".number_format($ptotal).")</span> <a href='#' onclick='removedcmember($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a>";
							$afterdc = $total - $ptotal;
							$subtotal = $subtotal + $afterdc;
							$discount_nmx = $dc->discount_nm;
							$discount_valuex = $dc->value;
						} 
					}
				} else {
					$subtotal = $subtotal + $total;
				}
				
				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 12px;'>
				            <span>$key->produk_nm</span>
				          </td>
				        </tr>
				        <tr style='font-size: 12px;'>
				          <td align='left' width='180'><span>$key->qty X</span><br>$discount_nmx $discount_valuex</td>
				          <td align='center'><span>@".number_format($key->produk_harga)."</span></td>
				          <td align='right'><span>".number_format($total)."<br>$discount</span></td>
				        </tr>
				        <tr style='line-height:12px;'>
				        <td>&nbsp </td>
				        <td></td>
				        <td></td>
				        </tr>";
				 }

				if (count($resdc)>0) {
					 foreach ($resdc as $dc) {
						$symb = substr($dc->value, -1);
						if ($symb != "%") {
							$discount_nm = $dc->discount_nm;
							$discount_value = $dc->value;
							$ret .= "<tr style='font-size: 12px;'>
							        <td align='left' width='80'>$discount_nm </td>
							        <td></td>
							        <td align='right'>".number_format($discount_value)." <a href='#' onclick='removedc($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a></td>
							        </tr>";
							$subtotal = $subtotal - $dc->value;
						} 
					}
				} 
				
					$tax = $subtotal * 0.10;
					$service = $subtotal * 0.05;
					$grandtotal = $subtotal + $tax + $service;
					$jmlbulat = $this->pembulatanratusan($grandtotal);
					$nilaibulat = $jmlbulat - $grandtotal;

				$ret .= "</tbody></table></div>
						<hr style='border: 1px solid red'>";
				        
				$ret .= "<table style='font-size: 12px; margin-top:30px;' width='100%'>
				        <tr>
				          <td align='left'>Subtotal</td>
				          <td colspan='2' align='right'>Rp. ".number_format($subtotal)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Tax</td>
				          <td colspan='2' align='right'>Rp. ".number_format($tax)."</td>
				        </tr>
				        <tr>
				          <td align='left'>Service</td>
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
						<hr style='border: 1px solid red;margin-bottom:20px;'>";

				$ret .= "<div class='col-md-12' align='center' style='margin:0;padding:0;'>
							<center>
							<button onclick='cetakmenu($id,this)' class='btn btn-info' style='font-size:15px;width: 40%;'>Cetak Menu</button>
							<button onclick='cetakbilling($id,this)' class='btn btn-info' style='font-size:15px;width: 40%;'>Cetak Billing</button>
							</center>
						</div>";
				$ret .= "<div class='m-t-20' align='center'>
							<button onclick='showcheckout($id,$grandtotal)' class='btn btn-info' style='font-size:20px;'>Checkout</button>
						</div>";
		} else {
			$ret = "<div align='center'><h3>TIDAK ADA PESANAN !!</h3> <button class='meja-button' type='button' onclick='backtowaiters()'>Kembali</button></div>";
		}
  		return $ret;
	}

	public function discountkasir() {
		$id = $this->request->getPost('id');
		$res = $this->discountmodel->getbyNormal()->getResult();
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan Pilih Diskon</h4>"
	            . "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div><table  width='100%'>";
	            foreach ($res as $key) {
	            $ret .= "<tr style='border-bottom: 1px solid #ccc; line-height: 60px; font-size: 25px; font-weight: bold;'>"
	            	 . "<td align='left'><button onclick='addDiscount($id,$key->discount_id)' class='btn btn-outline-primary' style='font-size: 20px; color: black; font-weight: bold;'>$key->discount_nm</button></td>"
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
		$id = $this->request->getPost('id');
		$res = $this->membermodel->getbyNormal()->getResult();
			$ret = "<div class='modal-dialog'>"
	            . "<div class='modal-content'>"
	            . "<div class='modal-header'>"
	            . "<h4 class='modal-title'>Silahkan Pilih Member</h4>"
	            . "<button type='button' class='close-xl' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<div><table  width='100%'>";
	            foreach ($res as $key) {
	            $ret .= "<tr style='border-bottom: 1px solid #ccc; line-height: 60px; font-size: 25px; font-weight: bold;'>"
	            	 . "<td align='left'><button onclick='addmember($id,$key->member_id)' class='btn btn-outline-primary' style='font-size: 20px; color: black; font-weight: bold;'>$key->person_nm</button></td>"
	            	 . "<td align='right'>$key->cellphone</td>"
	            	 . "</tr>";
	            }
	       $ret .= "</table></div>"
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
	            . "<button type='button' class='close-xl' data-dismiss='modal' aria-hidden='true'>×</button>"
	            . "</div>"
	            . "<div class='modal-body'>"
	            . "<form>"
	            . "<div><label style='color:black;'>TUNAI</label></div>";
	            $ret .= "<label class='btn btn-secondary'>
                            <input type='radio' name='payplan' id='tunai1' value='".number_format($gt)."' autocomplete='off'> ".number_format($gt)."
                        </label> ";
                $ret .= "<label class='btn btn-secondary'>
                            <input type='radio' name='payplan' id='tunai2' value='".$this->pembulatanratusanribu($gt)."' autocomplete='off'> ".$this->pembulatanratusanribu($gt)."
                        </label> ";
                $ret .= "<label class='btn btn-secondary'>
                            <input type='number' name='payplan' id='tunai3' placeholder='input disini'>
                        </label>";
                $ret .= "<div><label style='color:black;'>EDC</label></div>";
	            foreach ($res as $key) {
	            $ret .= "<label style='width: 100px; height: 70px;' class='btn btn-secondary'>
                            <input type='radio' name='payplan' value='$key->payplan_id' id='option$key->payplan_id' autocomplete='off'> <span style='justify-content:center; display:flex;  align-items:center; width:100%; height:100%;'>$key->payplan_nm</span>
                        </label> ";
	            }
	       $ret .= "<div align='right'><button data-dismiss='modal' aria-hidden='true' type='button' class='btn btn-secondary'>Batal</button> <button onclick='checkout($id,$gt)' class='btn btn-success' type='button'>Simpan</button></div>"
	       		. "</form>"
	       		. "</div>"
	            . "</div>"
	            . "</div>";
		
		return $ret;	
	}

	public function adddiscounttobill() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');

		$data = [
			'billing_id' => $id,
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

	public function addmembertobill() {
		$id = $this->request->getPost('id');
		$di = $this->request->getPost('di');

		$data = [
			'billing_id' => $id,
			'member_id' => $di,
			'updated_user' => $this->session->user_id,
			'updated_dttm' => date('Y-m-d H:i:s')
		];
		$addtobill = $this->billingmodel->insertbillmember($id,$data);
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

	public function cetakmenu() {
		$this->_getDrinksmenu($this->request->getPost('id')); 
		$this->_getFoodsmenu($this->request->getPost('id'));
	}

	private function _getFoodsmenu($id) {
		$data = $this->billingmodel->getfoodmenu($id)->getResult();
		$resdc = $this->discountmodel->getbybillid($id)->getResult();
		$subtotal = 0;
		$discount_nmx = "";
		list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		$this->profile = CapabilityProfile::load("POS-5890");
		$this->connector = new RawbtPrintConnector();
		// $this->connector = new FilePrintConnector("/dev/usb/lp0");


		$this->printer = new Printer($this->connector);
		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
		// Make sure you load a Star print connector or you may get gibberish.
		try {

		    /* Information for the receipt */
		    /* Date is kept the same for testing */
		$date = date('l jS \of F Y h:i:s A');
		   

		    /* Start the printer */
		    // $logo = EscposImage::load("images/lib/logoa.png", false);
		    // $this->printer = new Printer($this->connector, $this->profile);


		    /* Print top logo */
		    // if ($this->profile->getSupportsGraphics()) {
		    //     $this->printer->graphics($logo);
		    // }
		    // if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
		    //     $this->printer->bitImage($logo);
		    // }

		    /* Name of shop */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text("Butcher Steak & Pasta Palembang\n");
		    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
		    $this->printer->text("Sumatera Selatan, 30114, 07115626366\n");
		    $this->printer->selectPrintMode();
		    $this->printer->feed();
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    $this->printer->text($this->getAsString(32,panjang($dt),"",$tm));
		    /* Title of receipt */
		    $this->printer->setEmphasis(true);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->feed(1);
		    

		    /* Items */
		    
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    foreach ($data as $item) {
		    	$this->printer->setEmphasis(true);
				$total = $item->produk_harga * $item->qty;
				foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb == "%") {
						$percentega = str_replace("%", "", $dc->value);
						$valuedc = ($percentega/100) * $total;
						$ptotal = "(".number_format(($percentega/100) * $total).")";
						$afterdc = $total - $valuedc;
						$subtotal = $subtotal + $afterdc;
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					} else {
						$discount = "";
						$discount_nmx = "";
						$discount_valuex = "";
						$ptotal = "";
						$subtotal = $subtotal + $total;
					}
				}
		        $this->printer->text($item->produk_nm."\n");
		        $this->printer->setEmphasis(false);
		        $this->printer->text($this->getAsString(32,$item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
		        $this->printer->text($this->getAsString(32,$discount_nmx."".$discount_valuex," ",$ptotal)."\n");
		    }

		    foreach ($resdc as $dc) {
				$symb = substr($dc->value, -1);
				if ($symb != "%") {
					$discount_nm = $dc->discount_nm;
					$discount_value = $dc->value;
		        $this->printer->text($this->getAsString(32,$discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
				$subtotal = $subtotal - $dc->value;
				} 
			}
		    $this->printer->setEmphasis(false);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(false);
		    /*footer */
		    $this->printer->feed(2);
		    $this->printer->text($date . "\n");

		    /* Cut the receipt and open the cash drawer */
		    $this->printer->cut();
		    $this->printer->pulse();

		} catch (Exception $e) {
		    echo $e->getMessage();
		} finally {
		    $this->printer->close();
		}
	}

	private function _getDrinksmenu($id) {

		$data = $this->billingmodel->getdrinkmenu($id)->getResult();
		$resdc = $this->discountmodel->getbybillid($id)->getResult();
		$subtotal = 0;
		$discount_nmx = "";
		list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		$this->profile = CapabilityProfile::load("POS-5890");
		$this->connector = new RawbtPrintConnector();
		// $this->connector = new FilePrintConnector("/dev/usb/lp0");


		$this->printer = new Printer($this->connector);
		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
		// Make sure you load a Star print connector or you may get gibberish.
		try {

		    /* Information for the receipt */
		    /* Date is kept the same for testing */
		$date = date('l jS \of F Y h:i:s A');
		   

		    /* Start the printer */
		    // $logo = EscposImage::load("images/lib/logoa.png", false);
		    // $this->printer = new Printer($this->connector, $this->profile);


		    /* Print top logo */
		    // if ($this->profile->getSupportsGraphics()) {
		    //     $this->printer->graphics($logo);
		    // }
		    // if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
		    //     $this->printer->bitImage($logo);
		    // }

		    /* Name of shop */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text("Butcher Steak & Pasta Palembang\n");
		    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
		    $this->printer->text("Sumatera Selatan, 30114, 07115626366\n");
		    $this->printer->selectPrintMode();
		    $this->printer->feed();
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    $this->printer->text($this->getAsString(32,panjang($dt),"",$tm));
		    /* Title of receipt */
		    $this->printer->setEmphasis(true);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->feed(1);
		    

		    /* Items */
		    
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    foreach ($data as $item) {
		    	$this->printer->setEmphasis(true);
				$total = $item->produk_harga * $item->qty;
				foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb == "%") {
						$percentega = str_replace("%", "", $dc->value);
						$valuedc = ($percentega/100) * $total;
						$ptotal = "(".number_format(($percentega/100) * $total).")";
						$afterdc = $total - $valuedc;
						$subtotal = $subtotal + $afterdc;
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					} else {
						$discount = "";
						$discount_nmx = "";
						$discount_valuex = "";
						$ptotal = "";
						$subtotal = $subtotal + $total;
					}
				}
		        $this->printer->text($item->produk_nm."\n");
		        $this->printer->setEmphasis(false);
		        $this->printer->text($this->getAsString(32,$item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
		        $this->printer->text($this->getAsString(32,$discount_nmx."".$discount_valuex," ",$ptotal)."\n");
		    }

		    foreach ($resdc as $dc) {
				$symb = substr($dc->value, -1);
				if ($symb != "%") {
					$discount_nm = $dc->discount_nm;
					$discount_value = $dc->value;
		        $this->printer->text($this->getAsString(32,$discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
				$subtotal = $subtotal - $dc->value;
				} 
			}
		    $this->printer->setEmphasis(false);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(false);
		    
		    /*footer */
		    $this->printer->feed(2);
		    $this->printer->text($date . "\n");

		    /* Cut the receipt and open the cash drawer */
		    $this->printer->cut();
		    $this->printer->pulse();

		} catch (Exception $e) {
		    echo $e->getMessage();
		} finally {
		    $this->printer->close();
		}
	}

	public function cetakbilling() {
		$id = $this->request->getPost('id');
		$data = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$resdc = $this->discountmodel->getbybillid($id)->getResult();
		$subtotal = 0;
		$discount_nmx = "";
		list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		$this->profile = CapabilityProfile::load("POS-5890");
		$this->connector = new RawbtPrintConnector();
		// $this->connector = new FilePrintConnector("/dev/usb/lp0");


		$this->printer = new Printer($this->connector);
		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
		// Make sure you load a Star print connector or you may get gibberish.
		try {

		    /* Information for the receipt */
		    /* Date is kept the same for testing */
		$date = date('l jS \of F Y h:i:s A');
		   

		    /* Start the printer */
		    // $logo = EscposImage::load("images/lib/logoa.png", false);
		    // $this->printer = new Printer($this->connector, $this->profile);


		    /* Print top logo */
		    // if ($this->profile->getSupportsGraphics()) {
		    //     $this->printer->graphics($logo);
		    // }
		    // if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
		    //     $this->printer->bitImage($logo);
		    // }

		    /* Name of shop */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text("Butcher Steak & Pasta Palembang\n");
		    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
		    $this->printer->text("Sumatera Selatan, 30114, 07115626366\n");
		    $this->printer->selectPrintMode();
		    $this->printer->feed();
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    $this->printer->text($this->getAsString(32,panjang($dt),"",$tm));
		    $this->printer->text($this->getAsString(32,"Bill Name","",substr($data[0]->member_nm, 0,7)));
		    $this->printer->text($this->getAsString(32,"Collected by","",$data[0]->collected_nm)."\n");
		    /* Title of receipt */
		    $this->printer->setEmphasis(true);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->feed(1);
		    

		    /* Items */
		    
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    foreach ($data as $item) {
		    	$this->printer->setEmphasis(true);
				$total = $item->produk_harga * $item->qty;
				foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb == "%") {
						$percentega = str_replace("%", "", $dc->value);
						$valuedc = ($percentega/100) * $total;
						$ptotal = "(".number_format(($percentega/100) * $total).")";
						$afterdc = $total - $valuedc;
						$subtotal = $subtotal + $afterdc;
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					} else {
						$discount = "";
						$discount_nmx = "";
						$discount_valuex = "";
						$ptotal = "";
						$subtotal = $subtotal + $total;
					}
				}
		        $this->printer->text($item->produk_nm."\n");
		        $this->printer->setEmphasis(false);
		        $this->printer->text($this->getAsString(32,$item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
		        $this->printer->text($this->getAsString(32,$discount_nmx."".$discount_valuex," ",$ptotal)."\n");
		    }

		    foreach ($resdc as $dc) {
				$symb = substr($dc->value, -1);
				if ($symb != "%") {
					$discount_nm = $dc->discount_nm;
					$discount_value = $dc->value;
		        $this->printer->text($this->getAsString(32,$discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
				$subtotal = $subtotal - $dc->value;
				} 
			}
		    $this->printer->setEmphasis(false);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(false);
		    $this->printer->feed();
		    $tax = $subtotal * 0.10;
			$service = $subtotal * 0.05;
			$grandtotal = $subtotal + $tax + $service;
		    $this->printer->setEmphasis(false);
			$this->printer->text($this->getAsString(32,"Subtotal","","Rp ".number_format($subtotal))); 
			$this->printer->text($this->getAsString(32,"Tax","","Rp ".number_format($tax))); 
			$this->printer->text($this->getAsString(32,"Service","","Rp ".number_format($service))); 
			$this->printer->text($this->getAsString(32,"Rounding Amount","","Rp "."masih proses")); 
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(true);
			$this->printer->text($this->getAsString(32,"Total","","Rp ".number_format($grandtotal))); 
		    $this->printer->setEmphasis(false);
		    /*footer */
		    $this->printer->feed(2);
		    $this->printer->text($date . "\n");

		    /* Cut the receipt and open the cash drawer */
		    $this->printer->cut();
		    $this->printer->pulse();

		} catch (Exception $e) {
		    echo $e->getMessage();
		} finally {
		    $this->printer->close();
		}
	}

	public function cetakcheckout() {
		$id = $this->request->getPost('id');
		$data = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$resdc = $this->discountmodel->getbybillid($data[0]->billing_id)->getResult();

		$subtotal = 0;
		$discount_nmx = "";
		list($dt,$tm) = explode(" ", $data[0]->created_dttm);
		$this->profile = CapabilityProfile::load("POS-5890");
		$this->connector = new RawbtPrintConnector();
		// $this->connector = new FilePrintConnector("/dev/usb/lp0");


		$this->printer = new Printer($this->connector);
		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
		// Make sure you load a Star print connector or you may get gibberish.
		try {

		    /* Information for the receipt */
		    /* Date is kept the same for testing */
		$date = date('l jS \of F Y h:i:s A');
		   

		    /* Start the printer */
		    $logo = EscposImage::load("images/lib/logo.png", false);
		    // $this->printer = new Printer($this->connector, $this->profile);


		    /* Print top logo */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    
		    if ($this->profile->getSupportsGraphics()) {
		        $this->printer->graphics($logo);
		    }
		    if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
		        $this->printer->bitImage($logo);
		    }

		    /* Name of shop */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    // $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text("Butcher Steak & Pasta Palembang\n");
		    $this->printer->text("Jl. AKBP Cek Agus No. 284, Palembang\n");
		    $this->printer->text("Sumatera Selatan, 30114, 07115626366\n");
		    $this->printer->selectPrintMode();
		    $this->printer->feed();
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    $this->printer->text($this->getAsString(32,panjang($dt),"",$tm));
		    $this->printer->text($this->getAsString(32,"Bill Name","",substr($data[0]->member_nm, 0,7)));
		    $this->printer->text($this->getAsString(32,"Collected by","",$data[0]->collected_nm)."\n");
		    /* Title of receipt */
		    $this->printer->setEmphasis(true);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->feed(1);
		    

		    /* Items */
		    
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    foreach ($data as $item) {
		    	$this->printer->setEmphasis(true);
				$total = $item->produk_harga * $item->qty;
				foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb == "%") {
						$percentega = str_replace("%", "", $dc->value);
						$valuedc = ($percentega/100) * $total;
						$ptotal = "(".number_format(($percentega/100) * $total).")";
						$afterdc = $total - $valuedc;
						$subtotal = $subtotal + $afterdc;
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					} else {
						$discount = "";
						$discount_nmx = "";
						$discount_valuex = "";
						$ptotal = "";
						$subtotal = $subtotal + $total;
					}
				}
		        $this->printer->text($item->produk_nm."\n");
		        $this->printer->setEmphasis(false);
		        $this->printer->text($this->getAsString(32,$item->qty."x","@".number_format($item->produk_harga),number_format($total))); // for 58mm Font A
		        $this->printer->text($this->getAsString(32,$discount_nmx."".$discount_valuex," ",$ptotal)."\n");
		    }

		    foreach ($resdc as $dc) {
				$symb = substr($dc->value, -1);
				if ($symb != "%") {
					$discount_nm = $dc->discount_nm;
					$discount_value = $dc->value;
		        $this->printer->text($this->getAsString(32,$discount_nm,"","(".number_format($discount_value).")")."\n"); // for 58mm Font A
				$subtotal = $subtotal - $dc->value;
				} 
			}
		    $this->printer->setEmphasis(false);
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(false);
		    $this->printer->feed();
		    $tax = $subtotal * 0.10;
			$service = $subtotal * 0.05;
			$grandtotal = $subtotal + $tax + $service;
		    $this->printer->setEmphasis(false);
			$this->printer->text($this->getAsString(32,"Subtotal","","Rp ".number_format($subtotal))); 
			$this->printer->text($this->getAsString(32,"Tax","","Rp ".number_format($tax))); 
			$this->printer->text($this->getAsString(32,"Service","","Rp ".number_format($service))); 
			$this->printer->text($this->getAsString(32,"Rounding Amount","","Rp "."masih proses")); 
		    $this->printer->text("-------------------------------------\n");
		    $this->printer->setEmphasis(true);
			$this->printer->text($this->getAsString(32,"Total","","Rp ".number_format($grandtotal))); 
		    $this->printer->setEmphasis(false);
		    /*footer */
		    $this->printer->feed(2);
		    $this->printer->text($date . "\n");

		    /* Cut the receipt and open the cash drawer */
		    $this->printer->cut();
		    $this->printer->pulse();

		} catch (Exception $e) {
		    echo $e->getMessage();
		} finally {
		    $this->printer->close();
		}
	}

	public function getAsString($width = 48,$produk_nm,$produk_harga,$total)
    {
        $rightCols = 15;
        $centerCols = 10;
        $leftCols = 10;
        $left = str_pad($produk_nm, $leftCols);
        $center = str_pad($produk_harga,$centerCols, ' ', STR_PAD_LEFT);
        $right = str_pad($total, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$center$right\n";
    }
}
