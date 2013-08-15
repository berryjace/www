<?php

/**
 * Royalty Controller to deal with Vendors Royalty related routines
 *
 * @package  Vendor
 * @author   Mahbubur Rahman <mahbub@bluelinerbangla.com>
 * @version  $Revision: 0.1
 * @access   public
 */
class Vendor_RoyaltyController extends Zend_Controller_Action {

    public function init() {
	$this->doctrineContainer = Zend_Registry::get('doctrine');
	$this->em = $this->doctrineContainer->getEntityManager();
	$this->session = new Zend_Session_Namespace('default');
	$this->session->summaryData;
    }

    public function indexAction() {
	$current_year = date("Y");
	$vendor_id = $this->_helper->BUtilities->getLoggedInUser();
	//$last_quarter = BL_AMC::getLastQarter();
	$current_quarter = date("m");
	$current_year = ($current_quarter < 7) ? $current_year - 1 : $current_year;
	$fiscal_year = $current_year . "-" . substr(($current_year + 1), 2);
	$this->view->last_quarter_submissions = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getReportHistory($vendor_id, $fiscal_year, $current_quarter);

	$savedReports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->getRowsByVendorId($vendor_id);
	
	$licenses = $this->em->getRepository("BL\Entity\License")->findBy(array('vendor_id'=>$vendor_id, 'status'=>4));
	
	$bContainsOtherType = false;
	
	foreach($licenses as $license){
		if (isset($license->royalty_commission_type) && $license->royalty_commission_type != "%"){
			$bContainsOtherType = true;
		}
	}
	
	if ($bContainsOtherType == true){
		$this->view->licenseType = 2;
	} else {
		$this->view->licenseType = 1;
	}

	$this->view->savedReportsCount = count($savedReports);

	$this->_helper->JSLibs->do_call(array('load_fancy_assets'));
    }

    /**
     * Function to get ropyalty report submission history by year
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetReportHistoryAction() {
	$this->_helper->BUtilities->setAjaxLayout();
	$fiscal_year = $this->_getParam('fiscal_year');
	$vendor = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
	$reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getReportHistory($vendor->id, $fiscal_year);
	$this->view->reports = $reports;
    }

    public function historyAction() {
	$this->view->fiscalYear = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getDistinctFiscalYear();
    }

    /**
     * Function to get report history by ajax call
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxReportHistoryAction() {
	$this->_helper->BUtilities->setAjaxLayout();
	$reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getReportHistory((int) $this->_helper->BUtilities->getLoggedInUser(), $this->_getParam('fiscal_year'), $this->_getParam('quarter'), 'form');
	if (sizeof($reports)) {
	    $i = $j = 0;
	    $history_data = array();
	    $vendor = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser()));
	    foreach ($reports as $r) {
		$history_data['fiscal_year'][$i] = $r->year;
		$history_data['quarter'][$i] = $r->quarter;
		$history_data['submission_date'][$i] = $r->uploaded_on->format('d/m/Y');
		$report_details = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findBy(array('vendor' => (int) $this->_helper->BUtilities->getLoggedInUser(), 'submission_type' => $r->submission_type, 'year' => $r->year, 'quarter' => $r->quarter));
		foreach ($report_details as $d) {
		    $history_data['gross_sales'][$i][$j] = $d->gross_sales;
		    $history_data['organizations'][$i][$j] = $d->client->organization_name;
		    $history_data['royalty_commission'][$i][$j] = $d->royalty_commission;
		    $history_data['annual_advance'][$i][$j] = $d->annual_advance;
		    $history_data['royalty_before_adv'][$i][$j] = $d->royalty_before_adv;
		    $history_data['royalty_after_adv'][$i][$j] = $d->royalty_after_adv;
		    $j++;
		}
		$j = 0;
		$i++;
	    }
	    $this->view->organization = $vendor->organization_name;
	    $this->view->history_data = $history_data;
	} else {
	    $this->_helper->viewRenderer->setRender('no-report-history');
	}
    }

    public function submitAction() {
    	error_log("\nsubmitAction", 3, "./errorLog.log");
	/**
	 * todo: Pull the greek orgs
	 */
	//$this->view->clients = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
	/**
	 * todo: Pull the greek orgs with only the vendor is licensed with
	 */
	//        $this->view->clients = $this->em->getRepository("BL\Entity\License")->getClientsForVendorInvoice((int) $this->_helper->BUtilities->getLoggedInUser());

	$submission_hash = sha1(md5(microtime()));
	
	$this->view->submission_hash = $submission_hash;
	
	$vendor_id = (int)$this->_helper->BUtilities->getLoggedInUser();
	$this->view->vendor_id = $vendor_id;
	
	$saveId = $this->_getParam('id');
	$this->view->saveId = $this->_getParam('id');
	//echo $saveId; exit;
	
	if (empty($saveId)){
		if (!empty($this->session->saveId)){
			$saveId = $this->session->saveId;
		}
	}
	
	if (!empty($saveId)) {

	    $vendor = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());
	    //$savedReport = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->findOneBy(array('save_id' => $saveId));
	    $savedReport = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->getRowsBySaveId($saveId);

	    if (count($savedReport) > 0) {
		if ($savedReport[0]->saved_from_tab == 1) {
		    $this->view->savedRows = $savedReport;
		} else {
		    foreach ($savedReport as $report) {
			$i = $report->client->id;
			$arr[$i][] = serialize($report);
		    }
		    $this->view->savedRows = $arr;
		}
		$this->view->year = $savedReport[0]->year;
		$this->view->quarter = $savedReport[0]->quarter;
		$this->view->tab = $savedReport[0]->saved_from_tab;
	    }
	}
	
	$this->session->saveId = null;
	
	//$clients = array();
	
	$licenses = $this->em->getRepository("BL\Entity\License")->findBy(array('vendor_id'=>$vendor_id, 'status'=>4));
	
	$bContainsOtherType = false;
	
	foreach($licenses as $license){
		if (isset($license->royalty_commission_type) && $license->royalty_commission_type != "%"){
			$bContainsOtherType = true;
		}
		
		//$clients[] = $license->client_id;
	}
	
	//$this->view->clients = $clients;
	
	$this->view->clients = $this->em->getRepository('BL\Entity\User')->findBy(array('account_type' => (int) ACC_TYPE_CLIENT, 'user_status'=>"Current" ), array('organization_name' => 'asc'));
	
	if ($bContainsOtherType == true){
		$this->view->licenseType = 2;
	} else {
		$this->view->licenseType = 1;
	}
    }
    
    public function ajaxSaveReportType2Action(){
    	error_log("\najaxSaveReportType2Action()",3, "./errorLog.log");
    	
    	echo Zend_Json::encode(array('success' =>false, 'message'=>'Royalty report has not been saved yet'));
    	exit;
    }
    
    /**
     * Function to Process reports submitted via ajax
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return void
     */
    public function ajaxSubmitReportAction() {
    	error_log("\najaxSubmitReportAction()", 3, "./errorLog.log");

	$user = Zend_Auth::getInstance()->getIdentity();
	$isAdmin = ($user->account_type == 1) ? 1 : 0;

	$this->_helper->BUtilities->setNoLayout();

	$vendor_id = $this->_getParam('vendor_id');
	$vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();

	$vendor = $this->em->find("BL\Entity\User", (int) $vendor_id);

	$vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));

	/**
	 * Things to note : Both reports by sales and report by Greek orgs have the same
	 * structure of request params but presented in a different way in the front-end.
	 * So the processing will be same here.
	 */
	$params = $this->_getAllParams();

	//Zend_Debug::dump($params); exit;

	$params['year'] = isset($params['year']) ? $params['year'] : $params['fiscal_year'];

	$submission_hash = !empty($params['submission_hash']) ? $params['submission_hash'] : sha1(md5(microtime()));

	$summary_data['fiscal_year'] = $params['year'];
	$summary_data['quarter'] = $params['quarter'];
	$summary_data['clients'] = array();
	$summary_data['quantity'] = array();
	$summary_data['gross_sales'] = array();
	//$summary_data['sales_revenue'] = array();
	$summary_data['royalty_commission'] = array();
	$summary_data['royalty_commission_type'] = array();
	$summary_data['annual_advance'] = array();
	$summary_data['royalty_before_adv'] = array();
	$summary_data['royalty_after_adv'] = array();

	//        $this->session->summaryData = $this->_getAllParams();
	$num_rows = sizeof($params['greek_org']);

	$totalDue = 0;

	for ($i = 0; $i < $num_rows; $i++) {

	    $client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $params['greek_org'][$i], 'account_type' => (int) ACC_TYPE_CLIENT));
	    $license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $params['greek_org'][$i], 'vendor_id' => $vendor_id, 'status' => (int) 4));
		$submissions = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findBy(array('vendor'=>$vendor_id, 'client'=>(int)$params['greek_org'][$i], 'year'=>$params['year']));
		
	    $clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $client->id));

	    $report_row = new BL\Entity\VendorRoyaltyReportSubmissions();
	    $report_row->submission_type = "form";
	    $report_row->submission_hash = $submission_hash;
	    $report_row->quarter = $params['quarter'];
	    $report_row->status = "Pending Review";
	    $report_row->year = $params['year'];
	    $report_row->product_sold_to = (isset($params['product_sold_to'][$i])) ? $params['product_sold_to'][$i] : '';
	    //$report_row->invoice_date = new \DateTime(date("Y-m-d", strtotime($params['invoice_date'][$i])));
	    //$report_row->invoice_num = $params['invoice_number'][$i];
	    $report_row->unit_price = isset($params['price'][$i]) ? $params['price'][$i] : '';

	if (isset($params['qty'][$i])) {
			$report_row->quantity = $params['qty'][$i];
	    }

	    if (isset($params['gross_sales'][$i])) {
			$report_row->gross_sales = $params['gross_sales'][$i];
			error_log("\ngorss_sales is set: " . $params['gross_sales'][$i], 3, "./errorLog.log");
	    }
	    
	    $returns = 0;
	    
	    if (array_key_exists('returnsDeclared', $params)){
	    	$returns = $params['returnsDeclared'][$i];
	    }
	    
	    /*
	      if(isset($params['royalty_due'][$i])){
	      $report_row->sales_revenue = $params['sales_revenue'][$i];
	      }
	     */
		
	    $annual_advance = isset($license) ? $license->default_renewal_fee : 0;
	    
	    //$report_row->gross_sales = $params['gross_sales'][$i];
	    //$report_row->royalty_commission = isset ($license) ? $license->royalty_commission : $clientProfile->royalty_commission_per;
	    $report_row->annual_advance = $annual_advance;

	    if(isset($license)) {

		$payment_params = array(
		    'id'		=>  $client->id,
		    'fiscal_year'	=>  $params['year']
		);

		$payments = $this->em->getRepository('BL\Entity\Payment')
				->getClientPaymentReports($payment_params);
		if(count($payments) < 1) {
		    $report_row->annual_advance = 0;
		}
	    }
	    
	    /**
	     * set default royalty commission to 100 if vendor roylalty commission is NULL
	     * set default annual advance to 0 if vendor annual advance fee is NULL
	     */
	    /*
	      $report_row->royalty_before_adv = ($params['gross_sales'][$i] * ((is_null($report_row->royalty_commission) ? 100 : $report_row->royalty_commission) / 100));
	      $report_row->royalty_after_adv = ($report_row->royalty_before_adv - (is_null($report_row->annual_advance) ? 0 : $report_row->annual_advance));
	     */

	    if (isset($license) && ($vendorOperation->vendor_reporting_type == 1 || $vendorOperation->vendor_reporting_type == 2 )) {
		$report_row->royalty_commission = $license->royalty_commission;
		
		
		error_log("\nlicense is set", 3, "./errorLog.log");
		if ($license->royalty_commission_type == '%') {
		    $report_row->royalty_before_adv = ($report_row->gross_sales - $returns) * $report_row->royalty_commission / 100;
		} else {
		    $report_row->royalty_before_adv = $report_row->quantity * $report_row->royalty_commission;
		}
	    } else {
	    	
	    	error_log("\nlicense is not set",3, "./errorLog.log");
	    	
	    	if (isset($clientProfile->royalty_commission)){
	    		error_log("\nroyalty commission per: " . $clientProfile->royalty_commission, 3, "./errorLog.log");
	    	} else {
	    		error_log("\nroyalty commission per not set", 3, "./errorLog.log");
	    	}

		if ($vendorOperation->vendor_reporting_type == 1) {
		    	error_log("\nA", 3, "./errorLog.log");
		    $report_row->royalty_commission = isset($clientProfile->royalty_commission)? $client_profile->royalty_commission: 0;
		    $report_row->royalty_before_adv = ($report_row->gross_sales - $returns) * $report_row->royalty_commission / 100;
		} elseif ($vendorOperation->vendor_reporting_type == 2) {
		    	error_log("\nB", 3, "./errorLog.log");
		    $report_row->royalty_commission = $clientProfile->royalty_commission_amt;
		    $report_row->royalty_before_adv = $report_row->quantity * $report_row->royalty_commission;
		} elseif ($vendorOperation->vendor_reporting_type == 3) {
		    //$report_row->royalty_commission = $clientProfile->royalty_commission_amt;
		    	error_log("\nD", 3, "./errorLog.log");
		    if (!empty($params['royalty_due'][$i])) {
				$report_row->royalty_before_adv = $params['royalty_due'][$i];
		    } else {
			/**
			 * @todo if the vendor reporting type is 3 and the vendor fills the Form for royalty
			 * Then use the gross sales method
			 */
			$report_row->royalty_commission = $clientProfile->royalty_commission;
			$report_row->royalty_before_adv = ($report_row->gross_sales - $returns) * $report_row->royalty_commission / 100;
		    }
		} else {
			$report_row->royalty_commission = 8.5;
			$report_row->royalty_before_adv = ($report_row->gross_sales - $returns) * (8.5/100);
			error_log("\nF", 3, "./errorLog.log");
		}
	    }
	    $report_row->royalty_after_adv = $report_row->royalty_before_adv; // - $report_row->annual_advance;

	    
	    $report_row->client = $client; //$license->client_id;
	    $report_row->vendor = $vendor; //$license->vendor_id;
	    
	    error_log("\npersisting row", 3, "./errorLog.log");
	    
	    $this->em->persist($report_row);

	    $summary_data['clients'][] = $report_row->client->organization_name;
	    $summary_data['clients_id'][] = $client->id;
	    
	  	if (array_key_exists('returnsDeclared', $params)){
	    	$summary_data['gross_sales'][] = $report_row->gross_sales - $returns;
	    	$summary_data['returns'][] = $returns;
	  	}
	  	else {
	  		$summary_data['gross_sales'][] = $report_row->gross_sales;
	  		$summary_data['returns'][] = 0;
	  	}
	    //$summary_data['sales_revenue'][] = $report_row->sales_revenue;
	  	
	    $summary_data['quantity'][] = $report_row->quantity;
	    $summary_data['royalty_commission'][] = $report_row->royalty_commission;
	    $summary_data['royalty_commission_type'][] = (isset($license)) ? $license->royalty_commission_type : ($vendorOperation->vendor_reporting_type == 1) ? '%' : '$';
	    $summary_data['annual_advance'][] = $report_row->annual_advance;
	    $summary_data['royalty_before_adv'][] = $report_row->royalty_before_adv;
	    $summary_data['royalty_after_adv'][] = $report_row->royalty_after_adv;

	    $totalDue += $report_row->royalty_after_adv;
	}
	
	
	
	$this->em->flush();

	//Zend_Debug::dump($summary_data); exit;


	//$invoice->amount_due = $totalDue;
	//$this->em->persist($invoice);

	$this->em->flush();
	$summary_data['vendor_reporting_type'] = $vendorOperation->vendor_reporting_type;
	$summary_data['vendor_id'] = $vendor_id;
	$summary_data['submission_hash'] = $submission_hash;


	error_log("\nSetting summary data", 3, "./errorLog.log");
	
	$this->session->summaryData = $summary_data;
	$this->view->summary_data = $summary_data;
	$this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;
	$this->view->fiscal_year = $params['year'];
	$this->view->quarter = $params['quarter'];
	$this->view->vendor = $vendor;




	/*
	 * Generate the Summary PDF
	 */
	$targetDir = APPLICATION_PATH . '/../assets/files/reports/vendors/submissions/';
	$url = '/assets/files/reports/vendors/submissions/';
	$organizationName = str_replace(' ', '_', $vendor->organization_name);
	$fiscalYear = str_replace('-', '', $params['year']);
	$quarter = $params['quarter'];
	$dt = date('m') . '.' . date('d') . '.' . date('Y');
	$type = 'Summary';
	$ext = 'pdf';

	$arr = compact("organizationName", "fiscalYear", "quarter", "type", "dt");
	$imp = implode('_', $arr);
	//$imp .= '.'.$ext;

	$html = $this->view->render('royalty/summary-pdf-template.phtml');
	$pdf_params = array(
	    'author'	    => 'AMC',
	    'title'	    => 'Royalty Summary',
	    'subject'	    => 'Summary',
	    'pdf_content'   => $html,
	    'file_name'	    => $imp,
	    'file_path'	    => $targetDir,
	    'output_type'   => 'F'
	);
	$save_to = $this->view->BUtils()->getPDF($pdf_params);
	$params['file'] = $save_to;

	$report = new BL\Entity\VendorRoyaltySubmissions();
	$report->year = $params['year'];
	$report->quarter = $params['quarter'];
	$report->uploaded_on = new \DateTime(date("Y-m-d"));
	$report->submission_hash = $submission_hash;
	$report->type = 'Summary';
	$report->file_url = $url . $imp . '.' . $ext;
	$report->vendor = $vendor;

	
	$this->em->persist($report);
	$this->em->flush();

	
	error_log("\nflushed report", 3, "./errorLog.log");	
	/*
	 * End Summary PDF generation code
	 */

	$saveId = $this->_getParam('id');
	if (!empty($saveId)) {
	    $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->deleteBySaveId($saveId);
	}

	error_log("\n" . Zend_Json::encode(array('success' => true, 'message' => 'Royalty report submitted successfully!', 'submission_hash' => $submission_hash)), 3, "./errorLog.log");
	
	echo Zend_Json::encode(array('success' => true, 'message' => 'Royalty report calculated successfully!', 'submission_hash' => $submission_hash));
	exit;
    }
    
    public function ajaxCreateInvoiceAction(){
    	error_log("\najaxCreateInvoiceAction()", 3, "./errorLog.log");

    	$this->_helper->BUtilities->setNoLayout();
    	
    	$vendor_id = $this->_getParam('vendor_id');
    	$vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
    //	$submission_hash = !empty($this->_getParam('submission_hash')) ? $this->_getParam('submission_hash') : sha1(md5(microtime()));
    	$submission_hash = $this->_getParam('submission_hash');
    	
    	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));
    	
    	$reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findBy(array('submission_hash'=>$submission_hash));
    	
    	$num_rows = sizeof($reports);
    	
    	error_log("\nnum_rows: " . $num_rows, 3, "./errorLog.log");
    	$totalDue = 0;
    	foreach($reports as $r){
    		$totalDue += is_null($r->royalty_after_adv)? (float)0 : (float) $r->royalty_after_adv;
    	}	
    	
    	if ($num_rows > 0) {
    		$invoice = new BL\Entity\Invoice;
    		$invoice->vendor_id = $vendor;
    		$invoice->created_at = new DateTime();
    		$invoice->updated_at = new DateTime();
    		$invoice->invoice_date = new DateTime();
    		$invoice->due_date = new DateTime();
    		$invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($vendor->id);
    		$invoice->email = $vendor->email;
    		$invoice->address_line1 = $vendor->address_line1;
    		$invoice->address_line2 = $vendor->address_line2;
    		$invoice->city = $vendor->city;
    		$invoice->state = $vendor->state;
    		$invoice->zip = $vendor->zipcode;
    		$invoice->phone1 = $vendor->phone;
    		$invoice->phone2 = $vendor->phone2;
    		$invoice->fiscal_year = $this->_getParam('year');
    		$invoice->quarter = $this->_getParam('quarter');
    		$invoice->invoice_status = 'Current';
    		$invoice->invoice_type = 'Royalty Payments';
    		$invoice->company_name = $vendor->organization_name;
    		$invoice->fax = $vendor->fax;
    		$invoice->amount_due = $totalDue;
    		$this->em->persist($invoice);
    		$this->em->flush();
    		
    		$invoiceID = "INV_";
    		 
    		$id = $invoice->id . "";
    		 
    		for ($i = 0; $i < 9-strlen($id); $i++){
    			$invoiceID .= "0";
    		}
    		 
    		$invoiceID .= $id;
    		 
    		$invoice->invoice_number = $invoiceID;
    		$this->em->persist($invoice);
    		$this->em->flush();
    	}
    	
    	
    	foreach ($reports as $r) {
    		$lineItems = new \BL\Entity\InvoiceLineItems();
    		$lineItems->lineitems_number = $this->view->BUtils()->getInvoiceNumber($r->client->id);
    		$lineItems->invoice_number_li = $invoice->invoice_number;
    		$lineItems->amount_due = is_null($r->royalty_after_adv) ? (float) 0 : (float) $r->royalty_after_adv;
    		$lineItems->amount_paid = '';
    		$lineItems->check_number = '';
    		$lineItems->payment_status = 'Due';
    		$lineItems->invoice_status = 'Open';
    		$lineItems->fiscal_year = $r->year;
    		$lineItems->description = '';
    		$lineItems->quarter = $r->quarter;
    		$lineItems->invoice_id = $invoice;
    		$lineItems->client_id = $r->client;
    		$this->em->persist($lineItems);
    	}

    	$this->em->flush();

    	$invoice_id = 0;
    	if ($invoice->id > 0) $invoice_id = $invoice->id;
    	
    	echo Zend_Json::encode(array('success' => true, 'message' => 'Royalty report submitted successfully!', 'submission_hash' => $submission_hash, 'invoice_id'=>$invoice_id));
    	exit;
    }

    /**
     * Function to Process reports submitted via ajax
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return void
     */
    public function ajaxSaveReportAction() {
    	error_log("\najaxSaveReportAction()", 3, "./errorLog.log");
	$this->_helper->BUtilities->setNoLayout();
	$vendor = $this->em->find("BL\Entity\User", (int) $this->_helper->BUtilities->getLoggedInUser());


	/*
	 * Get default royalty commission %age from the application.inin file
	 */
	$bootstrap = $this->getInvokeArg('bootstrap');
	$options = $bootstrap->getOptions();
	$defaultCommissionForUnlicensed = $options['site']['default']['royalty_commission'];



	/**
	 * Things to note : Both reports by sales and report by Greek orgs have the same
	 * structure of request params but presented in a different way in the front-end.
	 * So the processing will be same here.
	 */
	$params = $this->_getAllParams();

	//        $this->session->summaryData = $this->_getAllParams();
	$num_rows = isset($params['greek_org']) ? sizeof($params['greek_org']) : 0;

	$saveId = $this->_getParam('id');
	if (empty($saveId)) {
	    $saveId = sha1(md5(microtime()));
	} else {
	    $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->deleteBySaveId($saveId);
	}
	$this->view->saveId = $saveId;
	$this->session->saveId = $saveId;

	for ($i = 0; $i < $num_rows; $i++) {

	    $client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $params['greek_org'][$i], 'account_type' => (int) ACC_TYPE_CLIENT));
	    $license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $params['greek_org'][$i], 'vendor_id' => $this->_helper->BUtilities->getLoggedInUser(), 'status' => (int) 4));

	    $report_row = new BL\Entity\VendorRoyaltyReportSave();
	    $report_row->submission_type = "form";
	    $report_row->quarter = $params['quarter'];
	    $report_row->status = "Pending Review";
	    $report_row->year = $params['year'];
	    $report_row->product_sold_to = $params['product_sold_to'][$i];
	    $report_row->product_desc = $params['product_description'][$i];
	    $report_row->invoice_date = new \DateTime(date("Y-m-d", strtotime($params['invoice_date'][$i])));
	    $report_row->invoice_num = $params['invoice_number'][$i];
	    $report_row->unit_price = $params['price'][$i];
	    $report_row->quantity = $params['qty'][$i];
	    $report_row->gross_sales = $params['gross_sales'][$i];
	    $report_row->royalty_commission = isset($license) ? $license->royalty_commission : $defaultCommissionForUnlicensed;
	    $report_row->annual_advance = isset($license) ? $license->annual_advance : NULL;
	    $report_row->save_id = $saveId;
	    $report_row->saved_from_tab = $params['tab'];
	    /**
	     * set default royalty commission to 100 if vendor roylalty commission is NULL
	     * set default annual advance to 0 if vendor annual advance fee is NULL
	     */
	    $report_row->royalty_before_adv = ($params['gross_sales'][$i] * ((is_null($report_row->royalty_commission) ? 100 : $report_row->royalty_commission) / 100));
	    $report_row->royalty_after_adv = ($report_row->royalty_before_adv - (is_null($report_row->annual_advance) ? 0 : $report_row->annual_advance));
	    $report_row->client = $client; //$license->client_id;
	    $report_row->vendor = $vendor; //$license->vendor_id;


	    $this->em->persist($report_row);
	}
	$this->em->flush();
	echo Zend_Json::encode(array('success' => "true", 'message' => 'Royalty report saved successfully!', 'id' => $saveId, 'tab' => $params['tab']));
	exit;
    }

    /**
     * Function to show vendor license clients list
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @return void
     */
    public function listSavedAction() {
    	error_log("\nlistSavedAction()", 3, "./errorLog.log");
	Zend_Session::namespaceUnset('default');
	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
	$from_review = $this->_getParam('confirm', '');
	if ($from_review != '') {
	    $client_name = $this->_getParam('client_name', '');
	    $client_name = str_replace("&apos;", "'", $client_name);
	    if ($from_review == "approved") {
		$this->_helper->flashMessenger->addMessage("Signed a License Agreement – A license agreement has now been sent to " . $client_name . " for countersignature.", "Approved");
	    } else if ($from_review == "cancel") {
		$this->_helper->flashMessenger->addMessage("A license agreement has been declined between you and " . $client_name, "Declined");
	    }
	    $this->view->license_msg_header = $from_review;
	    $session_message_header = new Zend_Session_Namespace('msg_header');
	    $session_message_header->message_header = $from_review;
	    //$this->_helper->redirector('');
	    $this->_redirect('vendor/license/index');
	}
    }

    /**
     * Function to provide JSON data to feed data table
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetSavedListAction() {
	$params = array(
	    'search' => $this->_getParam('sSearch', ''),
	    'page_start' => $this->_getParam('iDisplayStart', 1),
	    'draw_count' => $this->_getParam('sEcho', 1),
	    'per_page' => $this->_getParam('iDisplayLength', 10),
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	/*
	  $sorting_cols = array(
	  '0' => 'users.organization_name',
	  '1' => 'client_profiles.greek_name',
	  '2' => 'licenses.applied_date',
	  '3' => 'licenses.status'
	  );
	 *
	 */

	//$params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
	//$params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
	$params['vendor_id'] = $this->_helper->BUtilities->getLoggedInUser();
	$params['l_status'] = $this->_getParam('l_status', 'all');
	$params['iSortCol_0'] = $this->_getParam('iSortCol_0', 3);
	$reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->getAllRoyaltyReports($params);
	$this->_helper->BUtilities->setNoLayout();
	echo $reports;
    }

    /**
     * Function to show vendor royalty report summary
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function summaryAction() {
    	error_log("\nsummaryAction()", 3, "./errorLog.log");
	if (!is_null($this->session->summaryData)) {
	    $this->view->summary_data = $this->session->summaryData;
	    $vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $this->_helper->BUtilities->getLoggedInUser()));
	    $this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;

	    Zend_Session::namespaceUnset('default');
	} else {
	    $this->_helper->flashMessenger("You have no longer submitted royalty report.");
	    $this->_redirect('vendor/royalty/index');
	}
    }
    
    public function saleRevenueType2Action(){
    	error_log("\nsaleRevenueType2Action()", 3, "./errorLog.log");
    	
    	$submission_hash = $this->_getParam('submission_hash');
    	
    	$save_id = $this->_getParam('save_id');
    	$vendor_id = $this->_getParam('vendor_id');
    	$vendor_id = !empty($vendor_id)? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
    	
    	$licenses = $this->em->getRepository('BL\Entity\License')->findBy(array('vendor_id'=>$vendor_id, 'status'=>4));
    	/*
    	$clients = array();
    	
    	foreach($licenses as $license){
    		$clients[] = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$license->client_id));
    	}
    	
    	$this->view->clients = $clients; //*/
	
		$this->view->clients = $this->em->getRepository('BL\Entity\User')->findBy(array('account_type' => (int) ACC_TYPE_CLIENT, 'user_status'=>"Current" ), array('organization_name' => 'asc'));
    	
    	$this->view->year = $this->_getParam('year');
    	$this->view->quarter = $this->_getParam('quarter');

    	if (!is_null($this->session->summaryData)) {
    		$this->view->summary_data = $this->session->summaryData;
    	}
    	
    	$vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));
    	
    	if ($vendorOperation == null){
    		error_log("\nvendor operation is null A user_id:" . $vendor_id, 3, "./errorLog.log");
    	} else {
    		error_log("\nvendor operation is NOT null A user_id:" . $vendor_id . " reporting type: " . $vendorOperation->vendor_reporting_type, 3, "./errorLog.log");
    		$this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;
    	}
    	
    	if ($save_id != null){
    		$saves = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->findBy(array('save_id'=>$save_id));
    	
    		$submission_info = array();
    	
    		foreach($saves as $save){
    			$submission_info[] = array('gross_sales'=>$save->gross_sales, 'client_id'=>$save->client->id);
    				
    			error_log("\nadding item to array " . $save->gross_sales . " " . $save->client->id, 3, "./errorLog.log");
    		}
    	
    		$this->view->submission_info = $submission_info;
    	}
    }

    /**
     * Function to submit sale revenue
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access publicsummary_data
     * @return String
     */
    public function saleRevenueAction() {
    	error_log("\nsaleRevenueAction()", 3, "./errorLog.log");
	$submission_hash = $this->_getParam('submission_hash');
	/**
	 * todo: Pull the greek orgs
	 */
	
	$save_id = $this->_getParam('save_id');

	$vendor_id = $this->_getParam('vendor_id');
	$vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
	
	$licenses = $this->em->getRepository('BL\Entity\License')->findBy(array('vendor_id'=>$vendor_id, 'status'=>4));
	
	/*$clients = array();
	
	foreach($licenses as $lice){
		$clients[] = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$lice->client_id));
	}*/
	
	//$clients = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => (int) ACC_TYPE_CLIENT, ))
	
	$this->view->clients = $this->em->getRepository('BL\Entity\User')->findBy(array('account_type' => (int) ACC_TYPE_CLIENT, 'user_status'=>"Current" ), array('organization_name' => 'asc'));
	//$this->view->clients = $clients;
	
	/**
	 * todo: Pull the greek orgs with only the vendor is licensed with
	 */
	//        $this->view->clients = $this->em->getRepository("BL\Entity\License")->getClientsForVendorInvoice((int) $this->_helper->BUtilities->getLoggedInUser());

	$this->view->year = $this->_getParam('year');
	$this->view->quarter = $this->_getParam('quarter');
	$this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));

	if (!is_null($this->session->summaryData)) {
		if ($submission_hash == $this->session->summaryData['submission_hash']){
		    $this->view->summary_data = $this->session->summaryData;
		    //$this->view->submission_info = $this->session->summaryData;
		}
	} else {
		$summary_data = array();
		$summary_data['fiscal_year'] = $this->_getParam('year');
		$summary_data['quarter'] = $this->_getParam('quarter');
		$this->view->summary_data = $summary_data;
	}
	
	$vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));
	
	if ($vendorOperation == null){
	   	error_log("\nvendor operation is null A user_id:" . $vendor_id, 3, "./errorLog.log");
	} else {
	    error_log("\nvendor operation is NOT null A user_id:" . $vendor_id . " reporting type: " . $vendorOperation->vendor_reporting_type, 3, "./errorLog.log");
		$this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;
	}
	
	if ($save_id != null){
		$saves = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSave")->findBy(array('save_id'=>$save_id));
		
		$submission_info = array();
		
		foreach($saves as $save){
			//$submission_info[] = array('gross_sales'=>$save->gross_sales, 'client_id'=>$save->client->id);
			
			if (!isset($submission_info[$save->client->id])){
				$submission_info[$save->client->id] = $save->gross_sales;
			} else {
				$submission_info[$save->client->id] += $save->gross_sales;
			}
			
			error_log("\nadding item to array " . $save->gross_sales . " " . $save->client->id, 3, "./errorLog.log");
		}
		
		$this->view->submission_info = $submission_info;
	}

	if ($this->getRequest()->isPost()) {
		error_log(" is post", 3, "./errorLog.log");
	    $formData = $this->getRequest()->getPost();
	    $total = 0;
	    $summary_data = array();
	    $num_rows = sizeof($formData['greek_org']);
	    $vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $vendor_id, 'account_type' => (int) ACC_TYPE_VENDOR));

	    $vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));
	    
	    if ($vendorOperation == null){
	    	error_log("\nvendor operation is null B", 3, "./errorLog.log");
	    } else {

	    	error_log("\nvendor operation is NOT null B", 3, "./errorLog.log");
	    }
	    
	    $this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;

	    $params = $this->_getAllParams();
	    $summary_data['fiscal_year'] = $params['year'];
	    $summary_data['quarter'] = $params['quarter'];
	    $summary_data['clients'] = array();
	    $summary_data['quantity'] = array();
	    $summary_data['gross_sales'] = array();
	    $summary_data['royalty_commission'] = array();
	    $summary_data['royalty_commission_type'] = array();
	    $summary_data['annual_advance'] = array();
	    $summary_data['royalty_before_adv'] = array();
	    $summary_data['royalty_after_adv'] = array();
	    $summary_data['submission_hash'] = $params['submission_hash'];

	    /*
	     * Create Invoice Record
	     */
	    if ($num_rows > 0) {
			$invoice = new BL\Entity\Invoice;
			$invoice->vendor_id = $vendor;
			$invoice->created_at = new DateTime();
			$invoice->updated_at = new DateTime();
			$invoice->invoice_date = new DateTime();
			$invoice->due_date = new DateTime();
			$invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($vendor->id);
			$invoice->email = $vendor->email;
			$invoice->address_line1 = $vendor->address_line1;
			$invoice->address_line2 = $vendor->address_line2;
			$invoice->city = $vendor->city;
			$invoice->state = $vendor->state;
			$invoice->zip = $vendor->zipcode;
			$invoice->phone1 = $vendor->phone;
			$invoice->phone2 = $vendor->phone2;
			$invoice->fiscal_year = $params['year'];
			$invoice->quarter = $params['quarter'];
			$invoice->invoice_status = 'Current';
			$invoice->company_name = $vendor->organization_name;
			$invoice->fax = $vendor->fax;
			$this->em->persist($invoice);
			$this->em->flush();
		    $invoiceID = "INV_";
		    			 
	    	$id = $invoice->id . "";
	    			 
	    	for ($i = 0; $i < 9-strlen($id); $i++){
	    		$invoiceID .= "0";
	    	}
	    			 
	    	$invoiceID .= $id;
	    			 
	    	$invoice->invoice_number = $invoiceID;
	    	$this->ct->em->persist($nInvoice);
	    	$this->ct->em->flush();
	    	
	    }

	    $totalDue = 0;
	    

	    for ($i = 0; $i < $num_rows; $i++) {
			$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $formData['greek_org'][$i], 'account_type' => (int) ACC_TYPE_CLIENT));
			$license = $this->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $formData['greek_org'][$i], 'vendor_id' => $vendor_id, 'status' => (int) 4));
	
			$clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $client->id));
			
			$report_row = new BL\Entity\VendorRoyaltyReportSubmissions();
			$report_row->submission_type = "form";
			$report_row->quarter = $formData['quarter'];
			$report_row->status = "Pending Review";
			$report_row->year = $formData['fiscal_year'];
			//                $report_row->product_sold_to = NULL;
			$report_row->invoice_date = new \DateTime(date("Y-m-d"));
			//                $report_row->invoice_num = NULL;
			//                $report_row->unit_price = NULL;
	
	
			if ($vendorOperation->vendor_reporting_type == 2) {
			    $report_row->quantity = $formData['royalty_due'][$i];
			} else {
			    $report_row->gross_sales = $formData['royalty_due'][$i];
			}
	
			$report_row->submission_hash = $submission_hash;
			$report_row->sales_revenue = 'Y';
			$report_row->annual_advance = isset($license) ? $license->annual_advance : 0;
	
			$report_row->client = $client; //$license->client_id;
			$report_row->vendor = $vendor; //$license->vendor_id;
	
			$report_row->annual_advance = isset($license) ? $license->default_renewal_fee : $clientProfile->greek_default_renewal_fee;
	
			if (isset($license)) {
			    $report_row->royalty_commission = $license->royalty_commission;
			    if ($license->royalty_commission_type == '%') {
				$report_row->royalty_before_adv = $formData['royalty_due'][$i] * $report_row->royalty_commission / 100;
			    } else {
				$report_row->royalty_before_adv = $formData['royalty_due'][$i] * $report_row->royalty_commission;
			    }
			} else {
	
			    if ($vendorOperation->vendor_reporting_type == 1) {
				$report_row->royalty_commission = $clientProfile->royalty_commission;
				$report_row->royalty_before_adv = $formData['royalty_due'][$i] * $report_row->royalty_commission / 100;
			    } else {
				$report_row->royalty_commission = $clientProfile->royalty_commission_amt;
				$report_row->royalty_before_adv = $formData['royalty_due'][$i] * $report_row->royalty_commission;
			    }
			}
			$report_row->royalty_after_adv = $report_row->royalty_before_adv - $report_row->annual_advance;
	
	
			$total += $report_row->royalty_after_adv;
			//$summary[] = $report_row;as
			
			$invoice_li_row = new BL\Entity\InvoiceLineItems;
			$invoice_li_row->invoice_number_li = $invoice->invoice_number;
			$invoice_li_row->lineitems_number = $invoice->invoice_number;
			$invoice_li_row->client_id = $client;
			$invoice_li_row->amount_due = $report_row->royalty_after_adv;
			$invoice_li_row->created_at = new DateTime(date('Y-m-d'));
			$invoice_li_row->updated_at = new DateTime(date('Y-m-d'));
			$invoice_li_row->fiscal_year = $report_row->year;
			$invoice_li_row->quarter = $report_row->quarter;
			$invoice_li_row->invoice_id = $invoice;
			$invoice_li_row->invoice_status = 'Current';
			$this->em->persist($invoice_li_row);
	
			$summary_data['clients'][] = $report_row->client->organization_name;
			$summary_data['gross_sales'][] = $report_row->gross_sales;
			$summary_data['quantity'][] = $report_row->quantity;
			$summary_data['royalty_commission'][] = $report_row->royalty_commission;
			$summary_data['royalty_commission_type'][] = isset($license) ? $license->royalty_commission_type : $vendorOperation->vendor_reporting_type;
			$summary_data['annual_advance'][] = $report_row->annual_advance;
			$summary_data['royalty_before_adv'][] = $report_row->royalty_before_adv;
			$summary_data['royalty_after_adv'][] = $report_row->royalty_after_adv;
	
			$this->em->persist($report_row);
	    }

	    $invoice->amount_due = $total;
	    $this->em->persist($invoice);

	    $this->em->flush();

	    $this->view->total = $total;
	    $this->view->fiscal_year = $formData['fiscal_year'];
	    $this->view->quarter = $formData['quarter'];
	    $this->view->message = "Sales revenue submitted successfully!";

	    $this->view->summary_data = $summary_data;
	    $this->view->vendor_id = $vendor_id;
	    $this->view->invoice_id = $invoice->id;
	    $vendorRoyaltySubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltySubmissions')
		    ->findBy(array('submission_hash' => $submission_hash));

	    $this->view->vendorRoyaltySubmissions = $vendorRoyaltySubmissions;

	    $session = new Zend_Session_Namespace();
	    $session->sessionData = $summary_data;
	    //            print_r($this->view->summary_data);
	    //Zend_Session::namespaceUnset('default');

	    $this->_helper->viewRenderer->setRender('sale-revenue-summary');
	}
    }

    
    public function ajaxSubmitReportType2Action(){
    	error_log("\najaxSubmitReportType2Action()", 3, "./errorLog.log");

    	$summary_data = array();
    	$params = $this->_getAllParams();
    	$formData = $this->getRequest()->getPost();

    	$numb_rows = sizeof($formData['greek_org']);
    	
    	$vendor_id = $this->_getParam('vendor_id');
    	$vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
    	$submission_hash = $this->_getParam('submission_hash');
		$vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));
    	
    	if (!isset($submission_hash))
			$submission_hash = sha1(md5(microtime()));
    	
    	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>(int)$vendor_id, 'account_type'=>(int)ACC_TYPE_VENDOR));

    	error_log("\nnum", 3, "./errorLog.log");
    	if ($numb_rows > 0) {
    		error_log("\nAB", 3, "./errorLog.log");
    		
    		$invoice = new BL\Entity\Invoice;
    		$invoice->vendor_id = $vendor;
    		$invoice->created_at = new DateTime();
    		$invoice->updated_at = new DateTime();
    		$invoice->invoice_date = new DateTime();
    		$invoice->due_date = new DateTime();
    		$invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($vendor->id);
    		$invoice->email = $vendor->email;
    		$invoice->address_line1 = $vendor->address_line1;
    		$invoice->address_line2 = $vendor->address_line2;
    		$invoice->city = $vendor->city;
    		$invoice->state = $vendor->state;
    		$invoice->zip = $vendor->zipcode;
    		$invoice->phone1 = $vendor->phone;
    		$invoice->phone2 = $vendor->phone2;
    		$invoice->fiscal_year = $params['year'];
    		$invoice->quarter = $params['quarter'];
    		$invoice->invoice_status = 'Current';
			$invoice->invoice_type = 'Royalty Payments';
    		$invoice->company_name = $vendor->organization_name;
    		$invoice->fax = $vendor->fax;
    		$this->em->persist($invoice);
    		$this->em->flush();

    		$invoiceID = "INV_";
    		 
    		$id = $invoice->id . "";
    		
    		for ($i = 0; $i < 9-strlen($id); $i++){
    			$invoiceID .= "0";
    		}
    		
    		$invoiceID .= $id;
    		
    		$invoice->invoice_number = $invoiceID;
    		$this->ct->em->persist($nInvoice);
    		$this->ct->em->flush();
    		
    	}

    	error_log("\nB", 3, "./errorLog.log");
    	$total = 0;
    	
    	for ($i = 0; $i < $numb_rows; $i++){
    		$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>(int)$formData['greek_org'][$i], 'account_type'=>(int)ACC_TYPE_CLIENT));
    		$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('client_id' => (int) $formData['greek_org'][$i], 'vendor_id' => $vendor_id, 'status' => (int) 4));
			$clientProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id' => $client->id));

			error_log("\nC", 3, "./errorLog.log");
			$report_row = new BL\Entity\VendorRoyaltyReportSubmissions;
			
			$report_row->submission_type = "form";
			$report_row->quarter = $formData['quarter'];
			$report_row->status = "Pending Review";
			$report_row->year = $formData['fiscal_year'];
			$report_row->invoice_date = new DateTime(date("Y-m-d"));
			$report_row->gross_sales = $formData['royalty_due'][$i];


			$report_row->submission_hash = $submission_hash;
			$report_row->sales_revenue = 'Y';
			$report_row->annual_advance = isset($license) ? $license->default_renewal_fee : $clientProfile->greek_default_renewal_fee;
			$report_row->client = $client;
			$report_row->vendor = $vendor;
			
			$report_row->royalty_after_adv = $formData['royalty_due'][$i];
			$total += $report_row->royalty_after_adv;

			error_log("\nD", 3, "./errorLog.log");
			$invoice_li_row = new BL\Entity\InvoiceLineItems;
			$invoice_li_row->invoice_number_li = $invoice->invoice_number;
			$invoice_li_row->lineitems_number = $invoice->invoice_number;
			$invoice_li_row->client_id = $client;
			$invoice_li_row->amount_due = $report_row->royalty_after_adv;
			$invoice_li_row->created_at = new DateTime(date('Y-m-d'));
			$invoice_li_row->updated_at = new DateTime(date('Y-m-d'));
			$invoice_li_row->fiscal_year = $report_row->year;
			$invoice_li_row->quarter = $report_row->quarter;
			$invoice_li_row->invoice_id = $invoice;
			$invoice_li_row->invoice_status = 'Current';
			$this->em->persist($invoice_li_row);
	
			$summary_data['clients'][] = $report_row->client->organization_name;
			$summary_data['gross_sales'][] = $report_row->gross_sales;
			$summary_data['quantity'][] = $report_row->quantity;
			$summary_data['royalty_commission'][] = $report_row->royalty_commission;
			$summary_data['royalty_commission_type'][] = isset($license) ? $license->royalty_commission_type : $vendorOperation->vendor_reporting_type;
			$summary_data['annual_advance'][] = $report_row->annual_advance;
			$summary_data['royalty_before_adv'][] = $report_row->royalty_before_adv;
			$summary_data['royalty_after_adv'][] = $report_row->royalty_after_adv;
	
			$this->em->persist($report_row);
    	}
    	error_log("\nE", 3, "./errorLog.log");
	    $invoice->amount_due = $total;
	    $this->em->persist($invoice);

	    $this->em->flush();
	    
	    error_log("\nF", 3, "./errorLog.log");
	    
	    $session = new Zend_Session_Namespace();
	    $session->sessionData = $summary_data;
	    
	    error_log("\nSuccess", 3, "./errorLog.log");
    	
    	echo Zend_Json::encode(array('success' => true, 'message' => 'Royalty report submitted successfully!', 'id'=>$invoice->id));
    	exit;
    
    }
    
    public function saleRevenueSummaryAction() {
    	error_log("\nsaleReveueSummaryAction()", 3, "./errorLog.log");
    	
		$this->_helper->JSLibs->do_call(array('load_fancy_assets'));
		
		$invoice_id = $this->_getParam('invoice_id');
		$this->view->invoice_id = $invoice_id;
	
		$summaryData = $this->session->summaryData;
		$this->view->summary_data = $this->session->summaryData;
		$submission_hash = $this->session->summaryData['submission_hash'];
		
		$vendor_id = $this->session->summaryData['vendor_id'];
		
		$invoices = $this->em->getRepository('BL\Entity\Invoice')->findBy(array('vendor_id'=>$vendor_id));
		
		$this->view->prevInvoices = $invoices;
		$this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));
	
		$this->view->vendor_reporting_type = $this->session->summaryData['vendor_reporting_type'];
	
		$vendorRoyaltySubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltySubmissions')
			->findBy(array('submission_hash' => $submission_hash, 'type'	=>  'Detail'));
		$this->view->vendorRoyaltySubmissions = $vendorRoyaltySubmissions;
		$this->view->fiscal_year = $summaryData['fiscal_year'];
		$this->view->quarter = $summaryData['quarter'];
	
		unset($this->session->summaryData);
    }

    public function saleRevenueReviewAction(){
    	error_log("\nsaleReveueReviewAction()", 3, "./errorLog.log");
    	 
    	$this->_helper->JSLibs->do_call(array('load_fancy_assets'));
    	
    	$invoice_id = $this->_getParam('invoice_id');
    	$this->view->invoice_id = $invoice_id;
    	
    	$summaryData = $this->session->summaryData;
    	$this->view->summary_data = $this->session->summaryData;
    	$submission_hash = $this->session->summaryData['submission_hash'];
    	
    	$this->view->submission_hash = $submission_hash;
    	$vendor_id = $this->session->summaryData['vendor_id'];
    	
    	$invoices = $this->em->getRepository('BL\Entity\Invoice')->findBy(array('vendor_id'=>$vendor_id));
    	
    	$this->view->prevInvoices = $invoices;
    	$this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$vendor_id));
    	
    	$this->view->vendor_reporting_type = $this->session->summaryData['vendor_reporting_type'];
    	
    	$vendorRoyaltySubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltySubmissions')
    	->findBy(array('submission_hash' => $submission_hash, 'type'	=>  'Detail'));
    	$this->view->vendorRoyaltySubmissions = $vendorRoyaltySubmissions;
    	$this->view->fiscal_year = $summaryData['fiscal_year'];
    	$this->view->quarter = $summaryData['quarter'];
    }

    public function ajaxDeleteReportAction() {
		$this->_helper->BUtilities->setNoLayout();
		$hash = $this->_getParam('submission_hash');
	
		if ($this->_request->isPost()) {
		    $vendorRoyaltySubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltySubmissions')
			    ->deleteBySubmissionHash($hash);
	
		    $vendorRoyaltyReportSubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltyReportSubmissions')
			    ->deleteBySubmissionHash($hash);
		    echo Zend_Json::encode(array('code' => 'success', 'message' => 'Report deleted successfully'));
		}
    }

    public function downloadAction() {
	// action body
    }

    public function uploadAction() {
    	error_log("\nuploadAction()", 3, "./errorLog.log");
	/**
	 * Todo
	 * 4. Add to DB
	 */
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	$targetDir = APPLICATION_PATH . '/../assets/files/reports/vendors/submissions/';

	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 50 * 3600; // Temp file age in seconds

	@set_time_limit(5 * 60);

	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
	$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

	$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);




	// Make sure the fileName is unique but only if chunking is disabled
	if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
	    $ext = strrpos($fileName, '.');
	    $fileName_a = substr($fileName, 0, $ext);
	    $fileName_b = substr($fileName, $ext);

	    $count = 1;
	    while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
		$count++;

	    $fileName = $fileName_a . '_' . $count . $fileName_b;
	}

	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

	// Create target dir
	if (!file_exists($targetDir))
	    @mkdir($targetDir);

	// Remove old temp files
	if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
	    while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
		    @unlink($tmpfilePath);
		}
	    }

	    closedir($dir);
	} else
	    die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
	    $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

	if (isset($_SERVER["CONTENT_TYPE"]))
	    $contentType = $_SERVER["CONTENT_TYPE"];

	if (strpos($contentType, "multipart") !== false) {
	    if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
		// Open temp file
		$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		    // Read binary input stream and append it to temp file
		    $in = fopen($_FILES['file']['tmp_name'], "rb");

		    if ($in) {
			while ($buff = fread($in, 4096))
			    fwrite($out, $buff);
		    } else
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		    fclose($in);
		    fclose($out);
		    @unlink($_FILES['file']['tmp_name']);
		} else
		    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	    } else
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
	    // Open temp file
	    $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
	    if ($out) {
		// Read binary input stream and append it to temp file
		$in = fopen("php://input", "rb");

		if ($in) {
		    while ($buff = fread($in, 4096))
			fwrite($out, $buff);
		} else
		    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

		fclose($in);
		fclose($out);
	    } else
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}
	if (!$chunks || $chunk == $chunks - 1) {
	    rename("{$filePath}.part", $filePath);
	}
	
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    public function ajaxGetHistoryAction() {
	// action body
    }

    public function popupUploadAction() {
    	error_log("\npopupUploadAction()", 3, "./errorLog.log");
	$this->_helper->JSLibs->do_call(array('load_plupload_assets'));
	$this->_helper->BUtilities->setBlankLayout();

	$vendor_id = $this->_getParam('vendor_id');
	$this->view->vendor_id = (!empty($vendor_id)) ? $vendor_id : '';

	$submission_hash = sha1(md5(microtime()));
	$this->view->submission_hash = $submission_hash;
    }

    public function downloadRoyaltyPdfAction() {

    }

    public function uploadReportAction() {
    	error_log("uploadReportAction()", 3, "./errorLog.log");
	$vendor_id = $this->_getParam('vendor_id');
	$this->view->vendor_id = !empty($vendor_id) ? $vendor_id : '';
	// action body
    }

    /**
     * Function to submit the uploaded file information and save via ajax
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveReportSubmissionAction() {
    	error_log("\najaxSaveReportSubmissionAction()", 3, "./errorLog.log");
	$this->_helper->BUtilities->setNoLayout();

	$vendor_id = $this->_getParam('vendor_id');
	$vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();


	$vendor = $this->em->find("BL\Entity\User", (int) $vendor_id);

	$oldFileNames = $this->_getParam('upload_file_name');
	$oldFileNamesArray = explode(',', $oldFileNames);

	/*
	 * Rename the report as per the protoshare documentation
	 */
	$targetDir = APPLICATION_PATH . '/../assets/files/reports/vendors/submissions/';
	$url = '/assets/files/reports/vendors/submissions/';
	$organizationName = str_replace(' ', '_', $vendor->organization_name);
	$fiscalYear = str_replace('-', '', $this->_getParam('fiscal_year'));
	$quarter = $this->_getParam('quarter');
	$dt = date('m') . '.' . date('d') . '.' . date('Y');
	$type = 'Detail';

	$arr = compact("organizationName", "fiscalYear", "quarter", "type", "dt");
	$imp = implode('_', $arr);

	$vendorRoyalitySubmissions = $this->em->getRepository('BL\Entity\VendorRoyaltySubmissions')
		->getReportsUploadedByVendor($vendor_id);
	
	$index = 0;
	if (!empty($vendorRoyalitySubmissions)) {
	    $lastItem = $vendorRoyalitySubmissions[0]->file_url;
	    $x = explode($imp . '_', $lastItem);
	    if (count($x) > 1) {
		$pi = pathinfo($x[1]);
		$index = ((int) $pi['filename']);
	    }
	}
	
	foreach ($oldFileNamesArray as $oldFileName) {

	    if(file_exists($targetDir.$oldFileName)) {
			$ext = pathinfo($oldFileName, PATHINFO_EXTENSION);

			$newFileName = $imp . '_' . (++$index) . "." . $ext;
			rename($targetDir . $oldFileName, $targetDir . $newFileName);

			$report = new BL\Entity\VendorRoyaltySubmissions();
			$report->year = $this->_getParam('fiscal_year');
			$report->quarter = $this->_getParam('quarter');
			$report->status = 'Pending Review';
			$report->uploaded_on = new \DateTime(date("Y-m-d"));
			$report->submission_hash = $this->_getParam('submission_hash');
			$report->type = 'Detail';
			$report->file_url = $url . $newFileName;
			$report->vendor = $vendor;
			$this->em->persist($report);
	    }
	}
	
	error_log("\npreFlush", 3, "./errorLog.log");

	$this->em->flush();
	
	error_log("\npostFlush", 3, "./errorLog.log");

	if ($report->id > 0) {
		error_log("\nsuccess!", 3, "./errorLog.log");
	    echo Zend_Json::encode(array("code" => "success", 'quarter' => $quarter, 'fiscal_year' => $this->_getParam('fiscal_year'), 'vendor_id' => $vendor_id, 'submission_hash' => $this->_getParam('submission_hash')));
	} else {
		error_log("\nerror", 3, "./errorLog.log");
	    echo Zend_Json::encode(array("code" => "error"));
	}
	exit;
    }

    /**
     * Function to check if royalty report was submitted earlier or not
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxCheckEarlierSubmissionAction() {
	$this->_helper->BUtilities->setNoLayout();
	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$this->_helper->BUtilities->getLoggedInUser()));
	
	$already_submitted = $this->em->getRepository('BL\Entity\VendorRoyaltyReportSubmissions')
		->findOneBy(
		array('year' => $this->_getParam('fiscal_year'), 'quarter' => $this->_getParam('quarter'), 'status' => 'Pending Review', 'vendor'=>$vendor)
	);
	if (sizeof($already_submitted)) {
	    echo Zend_Json::encode(array('submitted_earlier' => 'yes'));
	} else {
	    echo Zend_Json::encode(array('submitted_earlier' => 'no'));
	}
	exit;
    }

}

