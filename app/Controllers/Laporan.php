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
use App\Models\Laporanmodel;

// require  '/home/u1102684/public_html/butcher/app/Libraries/vendor/autoload.php';
require  '/var/www/html/lavitabella/app/Libraries/vendor/autoload.php';

class Laporan extends BaseController
{

	protected $mejamodel;
	protected $laporanmodel;
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
		$this->laporanmodel = new Laporanmodel();
	}

	public function index() {
		if (session()->get('user_nm') == "") {
	        session()->setFlashdata('error', 'Anda belum login! Silahkan login terlebih dahulu');
	        return redirect()->to(base_url('/'));
	    }
		$data = [
			'title' => 'Laporan'
		];
		return view('backend/laporan', $data);
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

	public function reportclosekasir() {
		$status_cd 	= $this->request->getPost('status_cd');
		$start_dttm = $this->request->getPost('start_dttm');
		$end_dttm 	= $this->request->getPost('end_dttm');

		$topitem = $this->laporanmodel->getTopitem($status_cd,$start_dttm,$end_dttm)->getResult();
		$lattestitem = $this->laporanmodel->getLattestitem($status_cd,$start_dttm,$end_dttm)->getResult();
    	$edc = $this->laporanmodel->getPayplanEdc($status_cd,$start_dttm,$end_dttm)->getResult();
    	$tunai = $this->laporanmodel->getPayplanTunai($status_cd,$start_dttm,$end_dttm)->getResult();
		$getReport = $this->laporanmodel->getByfilter($status_cd,$start_dttm,$end_dttm)->getResult();
    	$getVoid = $this->laporanmodel->getVoid($status_cd,$start_dttm,$end_dttm)->getResult();
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
    	$ret = "<div class='col-md-12 p-0'>"
	            . "<div class='row'>"
				 . "<div class='col-md-5'>"
				 . "<div class='card'>"
                 . "<div class='card-body p-0'>"
                 . "<h3><strong>SALES</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:18px;' width='100%' data-toggle='table' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 	$ret .= "<tr>"
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

				 

				 . "</div>" // card-body
				 . "</div>" // card
				 . "</div>" // col-md-5

				. "<div class='col-md-7'>"
				 . "<div class='card' style='margin-bottom: 0px !important;'>"

				 . "<div class='card-body p-0'>" // card-body paypaln
				 . "<h3><strong>EDC</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:16px;' width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
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
				 . "<h3><strong>TUNAI</strong></h3>"
				 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:16px;' width='100%' data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
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


			$ret .= "</tbody>"
				 . "</table>"
				 . "</div>" // card-body paypaln
				 . "</div>" // card
				 . "</div>" // col-md-7

				 . "<div class='col-md-12'>"

				 . "<hr style='border: solid 1px red'/>"
				 . "<h3><strong>TOP ITEMS</strong></h3>"
                 . "<hr style='border: solid 1px red'/>"
				 . "<table style='font-size:14px;' width='100%'  data-toggle='table' data-height='250' data-mobile-responsive='true' class='table-striped'>"
				 . "<tbody>";
				 foreach ($topitem as $key) {
				 	$ret .= "<tr>"
						 . "<td width='20'>".$no++.".</td>"
						 . "<td width='50%'>$key->produk_nm</td>"
						 . "<td width='100'>$key->totalqty X</td>"
						 . "</tr>";
				}
				 

			$ret .= "</tbody>"
				 . "</table>" // TOP ITEMS

				 

				 . "</div>" // col-md-12

				 . "</div>" // row
				 . "</div>"; // col-md-12
	          

	    return $ret;
    }

    public function chartCustomer() {
    	$start_dttm = "2021-01-01 00:00:00";
    	$end_dttm = "2021-01-01 23:59:59";

    	$res = $this->laporanmodel->getCustomerbyrange($start_dttm,$end_dttm)->getResult();
    	$data = array();
    	// $data = ;

    	echo json_encode($res);
    }

}
