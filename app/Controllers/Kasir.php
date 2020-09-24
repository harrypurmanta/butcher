<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Usersmodel;
use App\Models\Mejamodel;
use App\Models\Billingmodel;
use App\Models\Discountmodel;
use App\Models\Membermodel;
require  '/home/u1102684/public_html/butcher/app/Libraries/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\RawbtPrintConnector;
use Mike42\Escpos\CapabilityProfile;

class Kasir extends BaseController
{
	protected $mejamodel;
	protected $billingmodel;
	protected $discountmodel;
	protected $membermodel;
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
		$this->session = \Config\Services::session();
		$this->session->start();
	}
	
	public function index() {
		$data = [
			'title' => 'Kasir Dashboard',
			'subtitle' => 'Kasir',
			'meja' => $this->mejamodel->getbyNormal()
		];
		return view('backend/kasir', $data);

	}

	public function getbymejaidkasir() {
		$id = $this->request->getPost('id');
		$res = $this->billingmodel->getbyMejaidkasir($id)->getResult();
		$resdc = $this->discountmodel->getbybillid($id)->getResult();
		
		if (count($res)>0) {
			$discount_nm = "";
			$subtotal = 0;
			$ret = "<div align='center' id='div-item'>
				<input type='hidden' id='meja_id' value='$id'/>
						<div style='margin-top: 30px;'>
							<p>
								<span style='font-size: 20px;'>Butcher Steak & Pasta Palembang</span><br>
								<span style='font-size: 20px;'>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
								<span style='font-size: 20px;'>Sumatera Selatan, 30114, 07115626366</span>
							</p>
						</div>
					</div>";
			$ret .= "<table width='100%' style='margin-top: 20px;font-size: 20px;'>
				        <tr>
				          <td align='left'>Tanggal</td>
				          <td align='right'>".$res[0]->created_dttm."</td>
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
				      <div style='overflow:auto;'><table class='active' style='font-size: 30px;' width='100%; '><tbody>";
			foreach ($res as $key) {
				$total = $key->produk_harga * $key->qty;
				foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb == "%") {
						$percentega = str_replace("%", "", $dc->value);
						$ptotal = ($percentega/100) * $total;
						$discount = "<span>(".number_format($ptotal).")</span> <a href='#' onclick='removedc($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a>";
						$afterdc = $total - $ptotal;
						$subtotal = $subtotal + $afterdc;
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					} else {
						$discount = "";
						$discount_nmx = $dc->discount_nm;
						$discount_valuex = $dc->value;
					}
				}

				if ($key->statusbilling == 'verified') {
					if ($key->status_cd == "nullified") {
						$buttonproduk = "";
						$style = "style='text-decoration: line-through;'";
					} else {
						// $subtotal = $subtotal + $total;
						$buttonproduk = "";
						$style = "";
					}
				} else {
					if ($key->status_cd == "nullified") {
						$buttonproduk = "<button onclick='enableproduk($key->billing_item_id)' type='button' class='btn btn-success'>Enable</button>";
						$style = "style='text-decoration: line-through;'";
					} else {
						// $subtotal = $subtotal + $total;
						$buttonproduk = "<button onclick='disableproduk($key->billing_item_id)' type='button' class='btn btn-danger'>Disable</button>";
						$style = "";
					}
				}

				$ret .= "<tr>
				        <td colspan='3' align='left' style='font-weight: bold;font-size: 20px;'>
				            <span ".$style.">$key->produk_nm</span> ".$buttonproduk."
				          </td>
				        </tr>
				        <tr style='font-size: 20px;'>
				          <td align='left' width='180'><span ".$style.">$key->qty X</span><br>$discount_nmx $discount_valuex</td>
				          <td align='center'><span ".$style.">@".number_format($key->produk_harga)."</span></td>
				          <td align='right'><span ".$style.">".number_format($total)."<br>$discount</span></td>
				        </tr>
				        <tr style='line-height:20px;'>
				        <td>&nbsp </td>
				        <td></td>
				        <td></td>
				        </tr>";
				 }

				 foreach ($resdc as $dc) {
					$symb = substr($dc->value, -1);
					if ($symb != "%") {
						$discount_nm = $dc->discount_nm;
						$discount_value = $dc->value;
						$ret .= "<tr style='font-size: 20px;'>
						        <td align='left' width='80'>$discount_nm </td>
						        <td></td>
						        <td align='right'>".number_format($discount_value)." <a href='#' onclick='removedc($id,$dc->billing_discount_id)'><i style='color:red;' class='fas fa-times'></i></a></td>
						        </tr>";
						$subtotal = $subtotal - $dc->value;
					} 
				}
				
					$tax = $subtotal * 0.10;
					$service = $subtotal * 0.05;
					$grandtotal = $subtotal + $tax + $service;

				$ret .= "</tbody></table></div>
						<hr style='border: 1px solid red'>";
				        
				$ret .= "<table style='font-size: 20px; margin-top:30px;' width='100%'>
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
				          <td colspan='2' align='right'>Rp. dak tau rumusnyo</td>
				        </tr>

				        <tr>
				          <td align='left' style='font-weight:bold;'>Total</td>
				          <td colspan='2' align='right'>Rp. ".number_format($grandtotal)."</td>
				        </tr>
						</table>
						<hr style='border: 1px solid red;margin-bottom:20px;'>";

				$ret .= "<div align='center'><button onclick='cetakmenu($id)' class='btn btn-info' style='font-size:30px;'>Cetak Menu</button> <button onclick='cetakbilling($id)' class='btn btn-info' style='font-size:30px;'>Cetak Billing</button> </div>";
				$ret .= "<div class='m-t-20' align='center'><button onclick='checkout($id)' class='btn btn-info' style='font-size:40px;'>Checkout</button></div>";
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
		$removedc = $this->discountmodel->removedckasir($di,$data);
		if ($removedc) {
			return "true";
		} else {
			return "false";
		}
		
	}

	public function cetakmenu() {
		$this->profile = CapabilityProfile::load("POS-5890");
		$this->connector = new RawbtPrintConnector();
		$this->printer = new Printer($this->connector, $this->profile);
		$this->printer2 = new Printer($this->connector); // dirty printer profile hack !!
		// Make sure you load a Star print connector or you may get gibberish.
		try {

		    /* Information for the receipt */
		    $subtotal = 'Subtotal';
		    $tax = 'A local tax';
		    $total = 'Total';
		    /* Date is kept the same for testing */
		// $date = date('l jS \of F Y h:i:s A');
		    $date = "Monday 6th of April 2015 02:56:25 PM";

		    /* Start the printer */
		    $logo = EscposImage::load("images/rawbtlogo.png", false);
		    $this->printer = new Printer($this->connector, $this->profile);


		    /* Print top logo */
		    if ($this->profile->getSupportsGraphics()) {
		        $this->printer->graphics($logo);
		    }
		    if ($this->profile->getSupportsBitImageRaster() && !$this->profile->getSupportsGraphics()) {
		        $this->printer->bitImage($logo);
		    }

		    /* Name of shop */
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text("ExampleMart Ltd.\n");
		    $this->printer->selectPrintMode();
		    $this->printer->text("Shop No. 42.\n");
		    $this->printer->feed();


		    /* Title of receipt */
		    $this->printer->setEmphasis(true);
		    $this->printer->text("SALES INVOICE\n");
		    $this->printer->setEmphasis(false);

		    /* Items */
		    $this->printer->setJustification(Printer::JUSTIFY_LEFT);
		    $this->printer->setEmphasis(true);
		    $this->printer->text('','','$');
		    $this->printer->setEmphasis(false);
		    $this->printer->setEmphasis(true);
		    $this->printer->text($subtotal);
		    $this->printer->setEmphasis(false);
		    $this->printer->feed();

		    /* Tax and total */
		    $this->printer->text($tax);
		    $this->printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		    $this->printer->text($total);
		    $this->printer->selectPrintMode();

		    /* Footer */
		    $this->printer->feed(2);
		    $this->printer->setJustification(Printer::JUSTIFY_CENTER);
		    $this->printer->text("Thank you for shopping\n");
		    $this->printer->text("at ExampleMart\n");
		    $this->printer->text("For trading hours,\n");
		    $this->printer->text("please visit example.com\n");
		    $this->printer->feed(2);
		    $this->printer->text($date . "\n");

		    /* Barcode Default look */

		    $this->printer->barcode("ABC", Printer::BARCODE_CODE39);
		    $this->printer->feed();
		    $this->printer->feed();


		// Demo that alignment QRcode is the same as text
		    
		    $this->printer2->setJustification(Printer::JUSTIFY_CENTER);
		    $this->printer2->qrCode("https://rawbt.ru/mike42", Printer::QR_ECLEVEL_M, 8);
		    $this->printer2->text("rawbt.ru/mike42\n");
		    $this->printer2->setJustification();
		    $this->printer2->feed();


		    /* Cut the receipt and open the cash drawer */
		    $this->printer->cut();
		    $this->printer->pulse();

		} catch (Exception $e) {
		    echo $e->getMessage();
		} finally {
		    $this->printer->close();
		}
		
	}
}
