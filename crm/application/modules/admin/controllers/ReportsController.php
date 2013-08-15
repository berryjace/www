<?php

class Admin_ReportsController extends Zend_Controller_Action {

    protected $em;

    public function init() {
	/* Initialize action controller here */
	$this->doctrineContainer = Zend_Registry::get('doctrine');
	$this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_dataTable_assets'));
	$this->view->fiscalYear = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getDistinctFiscalYear();
    }

    public function reportDetailsAction() {
	//        print_r($this->_getAllParams());
	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_dataTable_assets'));
	$this->_helper->BUtilities->setEmptyLayout();
	$submission_hash = $this->_getParam('submission_hash');

	//        $this->view->reports = $this->em->getRepository('BL\Entity\VendorRoyaltyReportSubmissions')->findOneBy(array('id' => $report_id));
	//$this->view->reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getReportHistory((int) $this->_getParam('vid'), $this->_getParam('year'), NULL, NULL, $submission_hash);
	$this->view->reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getReportHistory(null, null, NULL, NULL, $submission_hash);
	$this->view->organization = ($this->view->reports[0]->vendor instanceof \BL\Entity\User) ?
	$this->view->reports[0]->vendor->organization_name : 'N/A';
	$this->view->vendor_id = $this->_getParam('vid');
	$this->view->report_status = $this->_getParam('status');
	$this->view->submission_hash = $this->_getParam('submission_hash');

	$detailReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getDetailReportsBySubmissionHash($submission_hash);
	$summaryReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getSummaryReportsBySubmissionHash($submission_hash);

	$this->view->detailReports = $detailReportRecords;
	$this->view->summaryReports = $summaryReportRecords;


    }

    /**
     * Function to view summery of a royalty report
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function summaryAction() {
	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));

	$submission_hash = $this->_getParam('submission_hash');
	$report = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findOneBy(array('submission_hash' => $submission_hash));
	$reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findBy(array('submission_hash' => $submission_hash));



	if(!empty($submission_hash)) {
	    $detailReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getDetailReportsBySubmissionHash($submission_hash);
	    $summaryReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getSummaryReportsBySubmissionHash($submission_hash);

	    $this->view->summaryReports = $summaryReportRecords;
	    $this->view->detailReports = $detailReportRecords;
	}

	if(($report->vendor instanceof \BL\Entity\User)) {
	    $vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $report->vendor->id));
	    if ($vendorOperation != null)
	   		$this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;
	}
	$summary_data = array();
	$i = 0;
	if (sizeof($reports)) {
	    foreach ($reports as $r) {


		    $summary_data['gross_sales'][$i] = $r->gross_sales;
		    $summary_data['quantity'][$i] = $r->quantity;
		    $summary_data['organizations'][$i] = ($r->client instanceof \BL\Entity\User) ? $r->client->organization_name : 'N/A';
		    $summary_data['royalty_commission'][$i] = $r->royalty_commission;
		    $summary_data['annual_advance'][$i] = $r->annual_advance;
		    $summary_data['royalty_before_adv'][$i] = $r->royalty_before_adv;
		    $summary_data['royalty_after_adv'][$i] = $r->royalty_after_adv;
		    $i++;
		    if ($r->submission_type == "file") {
			$summary_data['file'][] = $r->file_url;
		    }

	    }
	}

	//Zend_Debug::dump($summary_data);

	$this->view->status_msg = '';
	if ($report->status === 'Approved') {
	    $this->view->status_msg = 'Royalty report details given bellow is previous <b>approved</b> by admin';
	} else if ($report->status === 'Rejected') {
	    $this->view->status_msg = 'Royalty report details given bellow is previous <b>rejected</b> by admin';
	} else {
	    $name = ($report->vendor instanceof \BL\Entity\User) ? $report->vendor->organization_name : 'N/A';
	    $this->view->status_msg = 'Royalty report submitted by <b>' . $name . '</b> is waiting for admin approval';
	}
	$this->view->report = $report;
	$this->view->summary_data = $summary_data;
    }

    public function getReportsAction() {
	$this->_helper->JSLibs->do_call(array('load_fancy_assets'));
	$vendor_id = $this->_getParam('vendor_id', '');

	$this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('account_type' => ACC_TYPE_VENDOR, 'id' => $vendor_id));
	$records = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getRoyaltyReportByVendor($vendor_id);

	$detailReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getDetailReportsByVendor($vendor_id);
	$summaryReportRecords = $this->em->getRepository("BL\Entity\VendorRoyaltySubmissions")->getSummaryReportsByVendor($vendor_id);

	$this->view->summaryReports = $summaryReportRecords;
	$this->view->detailReports = $detailReportRecords;

	$this->view->vendor_id = $vendor_id;
	$this->view->records = $records;
    }

    /**
     * Function to approve or reject vendor royalty report
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function approveOrRejectAction() {
	$this->_helper->BUtilities->setPopupLayout();
	$this->_helper->JSLibs->load_tinymce_assets();
	//        print_r($this->_getAllParams());


	$report = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->findOneBy(array('id' => $this->_getParam('rid')));

	$rtype = $this->_getParam('rtype');
	if (empty($rtype)) {
	    $reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")
		    ->findBy(array('vendor' => (int) $report->vendor->id, "year" => $report->year, "quarter" => $report->quarter, "submission_type" => $report->submission_type));
	} else {
	    $reports = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")
		    ->findBy(array('submission_hash' => $this->_getParam('rid')));
	}

	$report = $reports[0];

	if ($this->getRequest()->isPost()) {
	    $formData = $this->getRequest()->getPost();
	    foreach ($reports as $r) {
		$r->status = trim($this->_getParam('type'));
		$this->em->persist($r);
	    }
	    $this->em->flush();

	    $admin = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $this->_helper->BUtilities->getLoggedInUser(), 'account_type' => ACC_TYPE_ADMIN));
	    $form_email = preg_split('/[;,]/', $admin->email);
	    $params = array(
		'to' => preg_split('/[;,]/', $report->vendor->email),
		'to_name' => $report->vendor->first_name,
		'from' => $form_email[0],
		'from_name' => $admin->first_name,
		'subject' => $formData['subject'],
		'body' => $formData['email_body'],
	    );

	    $this->view->success = "";
	    if (trim($this->_getParam('type') === "Approved")) {
		/**
		 * TODO: create invoice
		 */
		$amount_due = 0;
		foreach ($reports as $r) {
		    $amount_due += $r->royalty_after_adv;
		}
		$invoice = new \BL\Entity\Invoice();
		$invoice->invoice_date = new DateTime();
		$invoice->created_at = new DateTime();
		//                $invoice->updated_at = '';
		//                $invoice->due_date = '';
		$invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($report->vendor->id);
		$invoice->invoice_type = 'Quarterly Report';
		$invoice->fiscal_year = $report->year;
		$invoice->quarter = $report->quarter;
		$invoice->company_name = $report->vendor->organization_name;
		$invoice->webpage = '';
		$invoice->invoice_term = 'Due Now';
		$invoice->address_line1 = $report->vendor->address_line1;
		$invoice->address_line2 = $report->vendor->address_line2;
		$invoice->city = $report->vendor->city;
		$invoice->state = $report->vendor->state;
		$invoice->zip = $report->vendor->zipcode;
		$invoice->phone1 = $report->vendor->phone;
		$invoice->phone2 = $report->vendor->phone2;
		$invoice->email = $report->vendor->email;
		$invoice->fax = $report->vendor->fax;
		$invoice->invoice_status = 'Open';
		$invoice->payment_status = 'Due';
		$invoice->display_record = '';
		$invoice->amount_due = $amount_due;
		$invoice->amount_paid = '';
		$invoice->vendor_id = $report->vendor;
		$this->em->persist($invoice);
		$this->em->flush();

		$this->view->invoice_id = $invoice->id;
		/**
		 * TODO: Save invoice lineitems
		 */
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

		/**
		 * TODO: create invoice pdf
		 */
		$lineitems = $this->em->getRepository("BL\Entity\InvoiceLineItems")->getLineItemsForPDF($invoice->id);
		$this->view->invoice = $invoice;
		$this->view->lineitems = $lineitems;
		$html = $this->view->render('reports/invoice-pdf-template.phtml');
		//                print_r($html);
		//                die('---------');
		$pdf_params = array(
		    'author' => $invoice->vendor_id->organization_name,
		    'title' => 'Create invoice',
		    'subject' => 'Invoice',
		    'pdf_content' => $html,
		    'file_name' => $invoice->invoice_number,
		    'file_path' => APPLICATION_PATH . '/../tmp/',
		    'output_type' => 'F'
		);
		$save_to = $this->view->BUtils()->getPDF($pdf_params);
		$params['file'] = $save_to;
		/**
		 * end pdf create
		 */
		$this->view->success = "Quarterly royalty report approved successfully!";
	    }
	    if (trim($this->_getParam('type')) === "Rejected") {
		$this->view->success = "Quarterly royalty report declined successfully!";
	    }

	    if (APPLICATION_ENV != 'local') {
		$this->_helper->BUtilities->send_mail($params);
	    }
	    $invoice->email_date .= date('m-d-Y') . ',';

	    foreach ($reports as $r) {
		$r->email_date = new DateTime(date('Y-m-d'));
		$this->em->persist($r);
	    }

	    $this->em->flush();
	} else {
	    $this->view->title = 'Your quarterly royalty report has been accepted';
	    $this->view->subject = 'Your quarterly royalty report has been accepted';
	    $this->view->email_body = "Dear " . $report->vendor->first_name . ",<br /><br />";
	    if (trim($this->_getParam('type')) === 'Approved') {
		$this->view->report = $report;
		$this->view->reports = $reports;
		$vendor_id = $report->vendor->id;

		//$this->view->vendor_reporting_type =
		$vendorOperation = $this->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => $vendor_id));
		$this->view->vendor_reporting_type = $vendorOperation->vendor_reporting_type;

		$this->view->title = 'Write the email to let the vendor know that their royalty report was accepted';
		$this->view->subject = 'Your quarterly royalty report has been accepted';
		$this->view->email_body .="The royalty report that you recently submitted has been accepted by Affinity Consultants.<br /><br />";
		$this->view->email_body .= $this->view->render('/reports/report-email-body.phtml');
	    } else if (trim($this->_getParam('type')) === 'Rejected') {
		$this->view->title = 'Please state the reason for declining the report in the emailed to the vendor';
		$this->view->subject = 'Your quarterly royalty report has been declined.';
		$this->view->email_body .="The royalty report that you recently submitted has been declined by Affinity Consultants.<br /><br />";
		$this->view->email_body .= "<b>Reason for decline:</b><br />";
	    }

	    $this->view->email_body .= "<br /><br />Thank you, <br /><br />
					Accounting Department<br />
					e: accounting@greeklicensing.com<br />
					p: 760-734-6764<br />
					f: 707-202-0532<br />";
	}
    }

    /**
     * @author Unknown
     * currently this funciton is not using
     */
    public function addAction() {
	$this->_helper->JSLibs->load_jqui_assets();
	//$this->_helper->JSLibs->load_calender_assets();
	$term = '';
	$form = new Admin_Form_Report();
	$form->report_year->setMultiOptions(range(date('Y', strtotime('-10years')), date('Y')));
	$this->view->form = $form;
	$clients = $this->em->getRepository("BL\Entity\User")->getClientNames();
	$vendors = $this->em->getRepository("BL\Entity\User")->getVendorNames();
	if ($clients) {
	    foreach ($clients as $data) {
		$val[] = '{value:' . $data['id'] . ",label:'" . trim(addslashes($data['client_greek_name'])) . '\'}';
	    }
	    $this->view->greek_names = @implode(',', $val);
	}
	if ($vendors) {
	    foreach ($vendors as $data) {
		$vendor[] = '{value:' . $data['id'] . ",label:'" . trim(addslashes($data['organization_name'])) . '\'}';
	    }
	    $this->view->vendor_names = @implode(',', $vendor);
	}
	if ($this->getRequest()->isPost()) {
	    $formData = $this->getRequest()->getPost();
	    //$formData['vendor_id'] = 2;
	    //$formData['year'] = '2011';
	    //$formData['quarter'] = 'Q2';
	    $report_class = 'BL\Entity\Report';
	    $report = new $report_class();
	    $report->vendor = $this->em->find("BL\Entity\User", (int) $formData['vendor_id']);
	    $report->year = $formData['year'];
	    $report->quarter = $formData['quarter'];
	    $this->em->persist($report);
	    $this->em->flush();

	    $class = 'BL\Entity\ReportContent';
	    foreach ($formData['product_sold_to'] as $key => $product_sold_to) {
		$report_content = new $class();
		$report_content->product_sold_to = $formData['product_sold_to'][$key];
		$report_content->invoice_number = $formData['invoice_number'][$key];
		$report_content->invoice_date = $formData['invoice_date'][$key];
		if ($report_content->invoice_date) {
		    list($m, $d, $y) = explode('/', $report_content->invoice_date);
		    $report_content->invoice_date = new DateTime("$y-$m-$d");
		}
		$report_content->product_description = $formData['product_description'][$key];
		$report_content->quantity = $formData['quantity'][$key];
		$report_content->price_per_unit = $formData['price_per_unit'][$key];
		$report_content->gross_sales = $formData['gross_sales'][$key];
		$report_content->user = $this->em->find("BL\Entity\User", (int) $formData['greek_client_id'][$key]);
		$report_content->report = $this->em->find("BL\Entity\Report", (int) $report->id);
		//print_r($report_content);
		$this->em->persist($report_content);
	    }
	    $this->em->flush();
	}
    }

    public function saveAction() {
	if ($this->getRequest()->isPost()) {
	    $formData = $this->getRequest()->getPost();
	    //$formData['vendor_id'] = 2;
	    //$formData['year'] = '2011';
	    //$formData['quarter'] = 'Q2';
	    $report_class = 'BL\Entity\Report';
	    $report = new $report_class();
	    $report->vendor = $this->em->find("BL\Entity\User", (int) $formData['vendor_id']);
	    $report->year = $formData['year'];
	    $report->quarter = $formData['quarter'];
	    $this->em->persist($report);
	    $this->em->flush();

	    $class = 'BL\Entity\ReportContent';
	    foreach ($formData['product_sold_to'] as $key => $product_sold_to) {
		$report_content = new $class();
		$report_content->product_sold_to = $formData['product_sold_to'][$key];
		$report_content->invoice_number = $formData['invoice_number'][$key];
		$report_content->invoice_date = $formData['invoice_date'][$key];
		if ($report_content->invoice_date) {
		    list($m, $d, $y) = explode('/', $report_content->invoice_date);
		    $report_content->invoice_date = new DateTime("$y-$m-$d");
		}
		$report_content->product_description = $formData['product_description'][$key];
		$report_content->quantity = $formData['quantity'][$key];
		$report_content->price_per_unit = $formData['price_per_unit'][$key];
		$report_content->gross_sales = $formData['gross_sales'][$key];
		$report_content->user = $this->em->find("BL\Entity\User", (int) $formData['greek_client_id'][$key]);
		$report_content->report = $this->em->find("BL\Entity\Report", (int) $report->id);
		//print_r($report_content);
		$this->em->persist($report_content);
	    }
	    $this->em->flush();
	    //            if ($form->isValid($formData)) {
	    //
			//            } else {
	    //                //$form->populate($formData);
	    //            }
	}
    }

    public function deleteAction() {
	// action body
    }

    public function browseAction() {
	$vendors = $this->em->getRepository("BL\Entity\User")->getUsersByRole(2, $this->_getParam('page', 1));
	//die($this->view->vendors->getTotalItemCount().'===='.sizeof($this->view->vendors));
	$data->page = $this->_getParam('page', 1);
	$data->rows = $vendors;
	$data->record = $vendors->getTotalItemCount();
	echo json_encode($data);
	exit(0);
    }

    /**
     * Function to get Royalty Reports List for Data Table feed
     * @author Tanzim
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetReportsListAction() {

	$this->_helper->BUtilities->setNoLayout();
	$params = array(
	    'search' => $this->_getParam('sSearch', ''),
	    'page_start' => $this->_getParam('iDisplayStart', 1),
	    'draw_count' => $this->_getParam('sEcho', 1),
	    'per_page' => $this->_getParam('iDisplayLength', 10),
	    'year_status' => $this->_getParam('year', ''),
	    'quarter_status' => $this->_getParam('quarter', 0),
	    'report_status' => $this->_getParam('report', ''),
	);
	
	error_log("\nreport status: {$this->_getParam('report', 'none')}", 3, "./errorLog.log");
	
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array(
	    '0' => 'v.organization_name',
	    '1' => 'r.status',
	    '2' => 'r.year',
	    '3' => 'r.quarter',
	    '4' => 'r.uploaded_on'
	);

	$params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_4', 4)];
	$params['sort_dir'] = $this->_getParam('sSortDir_4', 'asc');
	$records = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getRoyaltyReports($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->em->getRepository("BL\Entity\VendorRoyaltyReportSubmissions")->getRoyaltyReports($params);

	$json = '{"iTotalRecords":' . $records_total . ',
			"iTotalDisplayRecords": ' . $records_total . ',
					"aaData":[';
	$first = 0;
	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    //$json .= '["<a href=\"javascript:;\" class=\"report_link\" rel=\"y:' . $v->year . ',s:' . $v->status . ',v:' . $v->vendor->id . ',r:' . $v->id . '\">' . str_replace(chr(13), "", str_replace(chr(10), "", $v->vendor->organization_name)) . '</a>",
	    $json .= '["<a href=\"'.$this->view->baseUrl('/admin/vendors/contact/id/'.$v->vendor->id).'\">' . str_replace(chr(13), "", str_replace(chr(10), "", $v->vendor->organization_name)) . '</a>",'.
		    '"' . (!is_null($v->status) ? '<span class=\'\'>' . '<a href=\"javascript:;\" class=\"fancy_link\" rel=\"/admin/reports/report-details/submission_hash/'.$v->submission_hash.'\">' .  $v->status . '</a></span>' : "<span>N/A</span>") . '",
						"' . (!is_null($v->year) ? $v->year : "N/A") . '",

								"' . (!is_null($v->quarter) ? 'Q' . $v->quarter : "-") . '",
								"' . (!is_null($v->uploaded_on) ?  $v->uploaded_on->format('m-d-Y h:i:s') : "") . '",
										"' . '<a href=\"' . $this->view->url(array('module' => 'admin', 'controller' => 'reports', 'action' => 'get-reports', 'vendor_id' => $v->vendor->id), null, true) . '\">View All Reports of this Vendor</a>  ' . '"]';
	}
	$json .= ']}';

	echo $json;
    }

    public function revenueUploadAction() {
	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets', 'load_dataTable_assets'));
	$this->_helper->BUtilities->setEmptyLayout();
	//        print_r($this->_getAllParams());
    }

}