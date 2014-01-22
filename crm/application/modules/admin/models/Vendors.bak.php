<?php

class Admin_Model_Vendors {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
	$this->ct = $ct;
    }

    /**
     * Function to show payment action
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getVendorPayments() {
	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array('0' => 'v.id', '1' => 'v.kp_payment');
	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
	$params['vendor_id'] = $this->ct->getRequest()->getParam('id');


	$records = $this->ct->em->getRepository("BL\Entity\Payment")->getVendorPayments($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\Payment")->getVendorPayments($params);
	/**
	 * Datatable doesn't understand json_encode and have a custom json format
	 */
	$quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');
	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
	$first = 0;
	$currency = new Zend_Currency('en_US');
	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    $amc = 0;
	    $paymentLineItem = $this->ct->em->getRepository("BL\Entity\PaymentLineItems")->findBy(array('pmt_id' => $v->id));
	    if (isset($paymentLineItem)) {
		foreach ($paymentLineItem as $p) {
		    if ($p->sharing == 1) {
			if ($p->amount_paid) {
			    $amount = $p->amount_paid;
			} elseif ($p->late_paid) {
			    $amount = $p->late_paid;
			} elseif ($p->adv_paid) {
			    $amount = $p->adv_paid;
			} else {
			    $amount = 0;
			}
			$amcAmount = ($amount * $p->percent_amc);
			$amc = $amc + $amcAmount;
		    }
		}
	    }

	    if (isset($v->invoice)) {

		if ($v->invoice->invoice_type == 'Refund') {
		    $json .= '[
                    "' . (!$v->id ? "N/A" : $v->id) . '",
                    "' . (!$v->invoice->payment_status ? "N/A" : $v->invoice->payment_status) . '",
                    "' . (!$v->check_num ? "N/A" : $v->check_num) . '",
                    "' . $v->invoice->id . '",
                    "' . $v->record_date->format("m/d/Y") . '",
                    "' . $currency->toCurrency($v->amount_paid) . '",
                    "' . '' . '",
                    "' . '' . '",
                    "' . '' . '",
                    "' . '' . '",
                    "<a href=\"javascript:;\" class=\"vendor_payment_link\" rel=\"' . $v->id . '\">View</a>&nbsp; <a href=\"javascript:;\" class=\"delete_payment\" rel=\"d-' . $v->id . '\">Delete</a>"
                    ]';
		} else {
		    $json .= '[
                    "' . (!$v->id ? "N/A" : $v->id) . '",
                    "' . (!$v->invoice->payment_status ? "N/A" : $v->invoice->payment_status) . '",
                    "' . (!$v->check_num ? "N/A" : $v->check_num) . '",
                    "' . $v->invoice->id . '",
                    "' . $v->record_date->format("m/d/Y") . '",
                    "' . $currency->toCurrency($v->amount_paid) . '",
                    "' . $currency->toCurrency($v->amount_paid - $amc) . '",
                    "' . $amc . '",
                    "' . $currency->toCurrency($v->amount_paid + $v->amount_remaining) . '",
                    "' . $currency->toCurrency($v->amount_remaining) . '",
                    "<a href=\"javascript:;\" class=\"vendor_payment_link\" rel=\"' . $v->id . '\">View</a>&nbsp; <a href=\"javascript:;\" class=\"delete_payment\" rel=\"d-' . $v->id . '\">Delete</a>"
                    ]';
		}
	    } else {
		 $json .= '[
                    "' . (!$v->id ? "N/A" : $v->id) . '",
                    "' . ( "N/A") . '",
                    "' . (!$v->check_num ? "N/A" : $v->check_num) . '",
                    "' . "N/A" . '",
                    "' . $v->record_date->format("m/d/Y") . '",
                    "' . $currency->toCurrency($v->amount_paid) . '",
                    "' . $currency->toCurrency($v->amount_paid - $amc) . '",
                    "' . $amc . '",
                    "' . $currency->toCurrency($v->amount_paid + $v->amount_remaining) . '",
                    "' . $currency->toCurrency($v->amount_remaining) . '",
                    "<a href=\"javascript:;\" class=\"vendor_payment_link\" rel=\"' . $v->id . '\">View</a>&nbsp; <a href=\"javascript:;\" class=\"delete_payment\" rel=\"d-' . $v->id . '\">Delete</a>"
                    ]';
	    }
	}
	$json .= ']}';
	return $json;
    }

    /**
     * Function to provide all vendors payment AJAX response
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getAllVendorPayments() {
	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array('0' => 'v.check_num', '1' => 'v.record_date');
	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
	$params['vendor_id'] = $this->ct->getRequest()->getParam('id');


	$records = $this->ct->em->getRepository("BL\Entity\Payment")->getVendorPayments($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\Payment")->getVendorPayments($params);
	/**
	 * Datatable doesn't understand json_encode and have a custom json format
	 */
	$quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');
	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
	$first = 0;
	$currency = new Zend_Currency('en_US');
	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    $json .= '[
              "<a href=\"' . $this->ct->view->baseUrl("admin/vendors/contact/id/" . $v->vendor->id) . '\">' . $v->vendor->organization_name . '</a>",
              "' . (!$v->check_num ? "N/A" : $v->check_num) . '",
              "' . $v->record_date->format("m/d/Y") . '",
              "' . $quarters[$v->payment_quarter] . '",
              "' . $v->payment_year . '",
              "' . $currency->toCurrency($v->amount_paid) . '",
              "' . $currency->toCurrency($v->amount_paid) . '",
              "' . $currency->toCurrency($v->amount_remaining) . '",
              "<a href=\"javascript:;\" class=\"vendor_payment_link\" rel=\"' . $v->id . '\">View</a>&nbsp; <a href=\"javascript:;\" class=\"delete_payment\" rel=\"d-' . $v->id . '\">Delete</a>"
              ]';
	}
	$json .= ']}';
	return $json;
    }

    /**
     * Function to provide JSON data to feed data table for licensed vendors against pstatus
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLicensedVendors() {
	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
	    'status' => $this->ct->getRequest()->getParam('status', '3'),
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array(
	    '0' => 'v.organization_name',
	    '1' => 'v.id'
	);

	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0', 'asc');

	$records = $this->ct->em->getRepository("BL\Entity\License")->getLicensedVendors($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicensedVendors($params);

	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
	$first = 0;
	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    //$this->view->BUtils()->doctrine_dump($v,1);
	    $json .= '["<a href=\"javascript:;\" class=\"vendor_license_link\" rel=\"' . $v['id'] . '\">' . str_replace(chr(13), '', str_replace(chr(10), "", $v['organization_name'])) . '</a>",
              "' . $v['total'] . '"]';
	}
	$json .= ']}';
	return $json;
    }

    /**
     * Function to get Liceses for vendors for data table in /admin/vendors/lincenses
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getLicenses() {
	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
	    'vendor_id' => $this->ct->getRequest()->getParam('vendor', ''),
	    'status' => $this->ct->getRequest()->getParam('status', '3'),
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array('0' => 'v.organization_name', '1' => 'c.organization_name', '2' => 'l.applied_date', '3' => 'l.status');
	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');

	$records = $this->ct->em->getRepository("BL\Entity\License")->getLicensesByVendor($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\License")->getLicensesByVendor($params);
	$status_array = $this->ct->getHelper('BUtilities')->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
	/**
	 * Datatable doesn't understand json_encode and have a custom json format
	 */
	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
	$first = 0;

	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    $json .= '["<a href=\"javascript:;\" class=\"vendor_link\" rel=\"' . $v->vendor_id->id . '\">' . $v->vendor_id->organization_name . '</a>",
              "' . $v->client_id->organization_name . '",
              "' . $v->applied_date->format("m/d/Y") . '",
              "' . '<a href=\"javascript:;\" class=\"lic_link\" rel=\"' . $v->id . '\">' . (isset($status_array[$v->status]) ? $status_array[$v->status] : $v->status) . '</a>"]';
	}
	$json .= ']}';
	return $json;
    }

    /**
     * Function to handle Vendor Operations
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getOperations() {
	$form = new Admin_Form_VendorOperations();
	$vendorOperation = $this->ct->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id' => (int) $this->ct->getRequest()->getParam('id')));
	/**
	 * All the fields are not plain texts, so have to use old school method for assigning.
	 */
	
	error_log("getOperations()", 3, "./errorLog.log");
	
	$existing_data = array();
	if (count($vendorOperation) <= 0) {
	    $vendor = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));
	    $vendorOperation = new \BL\Entity\VendorOperation();
	    if (count($vendor) > 0) {
		$vendorOperation->user_id = $vendor;
		$this->ct->em->persist($vendorOperation);
		$this->ct->em->flush();
	    }
	}

	$existing_data = array(
	    'vendor_status' => $vendorOperation->user_id->user_status,
	    'insurance_received' => ($vendorOperation->insurance_received != NULL && $vendorOperation->insurance_received->format("Y-m-d") != "-0001-11-30" ? $vendorOperation->insurance_received->format("Y-m-d") : 'N/A' ),
	    'insurance_expire' => ($vendorOperation->insurance_expire != NULL && $vendorOperation->insurance_expire->format("Y-m-d") != "-0001-11-30" ? $vendorOperation->insurance_expire->format("Y-m-d") : 'N/A' ),
	    'insurance_contact' => $vendorOperation->insurance_contact,
	    'insurance_company' => $vendorOperation->insurance_company,
	    'vendor_products' => $vendorOperation->vendor_products,
	    'insurance_phone' => $vendorOperation->insurance_phone,
	    'vendor_royalty_structure' => $vendorOperation->vendor_royalty_structure,
	    'vendor_reporting_type' => $vendorOperation->vendor_reporting_type,
	    'vendor_recommendation_to_client' => $vendorOperation->vendor_recommendation_to_client,
	    'default_note_to_vendor' => $vendorOperation->default_note_to_vendor,
	    'vendor_type' => explode(',', $vendorOperation->vendor_type),
	    'have_late_fee' => $vendorOperation->have_late_fee
	);

	//Zend_Debug::dump($existing_data);exit;


	$this->ct->view->form = $form;
	$this->ct->view->vendor = $this->ct->vendor;

	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
	    $this->ct->ajaxValidate($form, $formData);
	    if ($form->isValid($formData)) {
		$vendorOperation->user_id->user_status = $form->getValue('vendor_status');
		if ($form->getValue('insurance_received') != NULL && $form->getValue('insurance_received') != 'N/A') {
		    $vendorOperation->insurance_received = new \DateTime(date("Y-m-d H:i", strtotime($form->getValue('insurance_received'))));
		} else {
		    $vendorOperation->insurance_received = NULL;
		}
		if ($form->getValue('insurance_expire') != NULL && $form->getValue('insurance_expire') != 'N/A') {
		    $vendorOperation->insurance_expire = new \DateTime(date("Y-m-d H:i", strtotime($form->getValue('insurance_expire'))));
		} else {
		    $vendorOperation->insurance_expire = NULL;
		}
		
		error_log("getOperations()_post", 3, "./errorLog.log");
		
		$vendorOperation->insurance_contact = $form->getValue('insurance_contact');
		$vendorOperation->insurance_phone = $form->getValue('insurance_phone');
		$vendorOperation->vendor_royalty_structure = $form->getValue('vendor_royalty_structure');
		$vendorOperation->have_late_fee = $form->getValue('have_late_fee');
		$vendorOperation->vendor_type = implode(",", $form->getValue('vendor_type'));
		$vendorOperation->insurance_company = $form->getValue('insurance_company');
		$vendorOperation->insurance_contact = $form->getValue('insurance_contact');
		$vendorOperation->insurance_phone = $form->getValue('insurance_phone');
		$vendorOperation->vendor_reporting_type = $form->getValue('vendor_reporting_type');
		$vendorOperation->vendor_products = $form->getValue('vendor_products');
		$vendorOperation->vendor_recommendation_to_client = $form->getValue('vendor_recommendation_to_client');
		$vendorOperation->default_note_to_vendor = $form->getValue('default_note_to_vendor');
		$this->ct->em->persist($vendorOperation);
		$this->ct->em->flush();
		$this->ct->getHelper('flashMessenger')->direct("Operations Information Saved", "Info");
		$this->ct->view->BUrl()->redirect($this->ct->view->BUrl()->absoluteUrl());
	    }
	} else {
	    if (count($existing_data) > 0) {
		$form->populate($existing_data);
	    }
	}
    }

    /**
     * Function to Save Excel
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function saveExcel($data) {
	include("ThirdParty/PHPExcel/PHPExcel.php");
	$phpExcel = new PHPExcel();


	$phpExcel->getActiveSheet()->setTitle($data['title']);
	$phpExcel->setActiveSheetIndex(0);

	$sheet = $phpExcel->getActiveSheet();
	$row_counter = 1;
	$col_counter = 1;

	/*
	  echo "<pre>";
	  Zend_Debug::dump($data);
	  echo "</pre>";
	  exit;
	 *
	 */

	/**
	 * First set the colums
	 */
	foreach ($data['labels'] as $k => $v) {
	    $cell_key = $this->getNameFromNumber($col_counter++) . "" . $row_counter;
	    $sheet->setCellValue($cell_key, $v);
	    $style = $sheet->getStyle($cell_key);
	    $style->getFont()->setName('Arial')->setSize(10)->setBold();
	}
	foreach ($data['data'] as $cell_label => $cell_value) {
	    $col_counter = 1;
	    ++$row_counter;
	    foreach ($data['labels'] as $k => $v) {
		$cell_key = $this->getNameFromNumber($col_counter++) . "" . $row_counter;
		if ($cell_value[$k] instanceof DateTime) {
		    $sheet->setCellValue($cell_key, $cell_value[$k]->format("Y-m-d"));
		} else {
		    $sheet->setCellValue($cell_key, $cell_value[$k]);
		}
		$style = $sheet->getStyle($cell_key);
		$style->getFont()->setName('Arial')->setSize(10);
	    }
	}
	/** Let's style the cells * */
	$default_border = array(
	    'style' => PHPExcel_Style_Border::BORDER_THIN,
	    'color' => array('rgb' => '1006A3')
	);

	$style_header = array(
	    'borders' => array(
		'bottom' => $default_border,
		'left' => $default_border,
		'top' => $default_border,
		'right' => $default_border,
	    ),
	    'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'color' => array('rgb' => '000000'),
	    ),
	    'font' => array(
		'bold' => true,
		'color' => array('rgb' => 'FFFFFF'),
	    )
	);

	$sheet->getStyle('A1:' . $this->getNameFromNumber(count($data['labels'])) . "1")->applyFromArray($style_header);
	$file_name = $data['title'] . "-export-" . date("Y-m-d");
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"{$file_name}.xls\"");
	header("Cache-Control: max-age=0");
	$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
	$objWriter->save("php://output");
    }

    /**
     * Function to save file csv
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */

    /**
     * Function to export data to CSV
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function saveCSV($data) {
	include("ThirdParty/class.csv.php");
	$csv = new CSV();

	$labels = array();

	$labels = array_values($data['labels']);
	$label_keys = array_keys($data['labels']);

	$csv->setHeading($labels);
	foreach ($data['data'] as $EachData) {
	    $line = array();
	    foreach ($EachData as $key => $cellvalue) {
		if (array_key_exists($key, $data['labels'])) {
		    $nkey = array_search($key, $label_keys);
		    if ($cellvalue instanceof DateTime) {
			$line[$nkey] = $cellvalue->format("Y-m-d H:i:A");
		    } else {
			$line[$nkey] = $cellvalue;
		    }
		}
	    }
	    ksort($line);
	    $csv->addLine($line);
	}
	$file_name = $data['title'] . "-export-" . date("Y-m-d") . ".csv";
	$csv->output("D", $file_name);
	$csv->clear();
    }


	public function getPDF(){
		$content = 'abcd';
	        $params = array(
        	        'author' => 'Jace',
                	'title' => 'Export invoice',
	                'subject' => 'Test',
        	        'pdf_content' => $content,
                	'file_name' => 'Testing123'. date("Y-m-d"),
 	               	'file_path' => APPLICATION_PATH . '/../tmp/',
        	        'output_type' => 'I'
	        );
		$this->ct->view->BUtils()->getPDF($params);
	}


    /**
     * Function to get vendor list for Data Table feed
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getVendorList() {
	$params = array(
	    'search' => htmlspecialchars(stripslashes($this->ct->getRequest()->getParam('sSearch', '')), ENT_QUOTES),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 10),
	    'status' => $this->ct->getRequest()->getParam('status', 'all')
	);
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array(
	    '0' => 'u.organization_name',
	    '1' => 'u.created_at',
	    '2' => 'u.user_status'
	);

	$params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)];
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0', 'asc');
	$records = $this->ct->em->getRepository("BL\Entity\User")->getAllVendors($params)->getResult();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\User")->getAllVendors($params);

	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
	$prec = array();
	foreach ($records as $v) {

	    if (!is_null($v->created_at)) {
		$created_at = ( (int) $v->created_at->format("Y") > 0 ? $v->created_at->format("M d, Y H:i A") : 'N/A');
	    } else {
		$created_at = 'N/A';
	    }

	    $prec[] = array(
		'<a href="javascript:;" class="vendor_link" rel="' . $v->id . '">' . str_replace(chr(13), "", str_replace(chr(10), "", $v->organization_name)) . '</a>',
		$created_at,
		(!is_null($v->user_status) ? $v->user_status : "-")
	    );
	}
	$json .= Zend_Json::encode($prec);
	$json .= '}';
	return $json;
    }

    /**
     * Function to serve contact action
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getContact() {
	$this->ct->getVendor();
	$this->ct->view->vendor = $this->ct->vendor;
	$user = $this->ct->vendor;
	$vendor_id = $this->ct->vendor->id;

	$vp = $this->ct->em->getRepository('BL\Entity\VendorProfile')->findOneBy(array('user_id' => (int) $vendor_id));
	$form = new Admin_Form_Contact();
	$this->ct->view->form = $form;

	if (count($vp) <= 0) {
	    /* New VendorProfile */
	    $vendor = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $vendor_id));
	    $vp = new \BL\Entity\VendorProfile();
	    if (count($vendor) > 0) {
		$vp->user_id = $vendor;
		$this->ct->em->persist($vp);
		$this->ct->em->flush();
	    }
	}

	$existing_data = array(
	    'editContact' => "0",
	    'v_addNew' => "0",
	    'username' => $user->username,
	    'user_password' => $user->password,
	    'organization_name' => $user->organization_name,
	    'vendor_number' => ($user->user_code != NULL ? $user->user_code : $user->id),
	    'address_line_1' => $user->address_line1,
	    'address_line_2' => $user->address_line2,
	    'phone_1' => $user->phone,
	    'phone_2' => $user->phone2,
	    'city' => $user->city,
	    'state' => ($user->state == '' || $user->state == NULL ? 'NULL' : $user->state ),
	    'zip' => $user->zipcode,
	    'fax' => $user->fax,
	    'web_page' => $user->website,
	    'email' => $user->email,
	    'company_email' => $user->company_email
	);

	$this->ct->view->userOperation = $this->ct->em->getRepository('BL\Entity\UserContact')->findBy(array('user_id' => (int) $vendor_id));

	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
	    //if ($form->isValid($formData)){
	    if ($form->isValidPartial($formData)) {

		if ($form->getValue('editContact') == "1") {
		    $user->organization_name = $form->getValue('organization_name');
		    $user->username = $form->getValue('username');
		    if ($form->getValue('user_password') != '') {
			$user->password = md5($form->getValue('user_password'));
		    }
		    $user->address_line1 = $form->getValue('address_line_1');
		    $user->address_line2 = $form->getValue('address_line_2');
		    $user->city = $form->getValue('city');
		    $user->state = $form->getValue('state');
		    $user->zipcode = $form->getValue('zip');
		    $user->email = $form->getValue('email');
		    $user->phone = $form->getValue('phone_1');
		    $user->phone2 = $form->getValue('phone_2');
		    $user->fax = $form->getValue('fax');
		    $user->website = $form->getValue('web_page');
		    $user->user_code = $form->getValue('vendor_number');
		    $user->updated_at = new DateTime();
		    $vp->email = $form->getValue('company_email');
		    $user->company_email = $form->getValue('company_email');
		    $this->ct->em->persist($user);
		    $this->ct->em->persist($vp);
		    $this->ct->em->flush();
		    $form->reset();
		    $this->ct->getHelper('flashMessenger')->direct("Contact updated succesfully!", "Info");
		    $this->ct->view->BUrl()->redirect($this->ct->view->BUrl()->absoluteUrl());
		}
		if ($form->getValue('v_addNew') == "1") {
		    $class = 'BL\Entity\UserContact';
		    $userContact = new $class();
		    $userContact->user_id = $user;
		    $userContact->sal = $form->getValue('v_sal');
		    $userContact->first_name = $form->getValue('v_first_name');
		    $userContact->last_name = $form->getValue('v_last_name');
		    $userContact->title = $form->getValue('v_title');
		    $userContact->phone = $form->getValue('v_phone');
//                    $userContact->phone_ext = $form->getValue('v_phone_ext');
		    $userContact->fax = $form->getValue('v_fax');
		    $userContact->email = $form->getValue('v_email');
		    $userContact->mobile = $form->getValue('v_mobile');
		    $userContact->contact_type = $form->getValue('v_contact_type');
		    $this->ct->em->persist($userContact);
		    $this->ct->em->flush();
		    $this->ct->getHelper('flashMessenger')->direct("New Contact added succesfully!", "Info");
		    $this->ct->view->BUrl()->redirect($this->ct->view->BUrl()->absoluteUrl());
		}
	    }
	    if ($form->getValue('editContact') == "1") {
		$this->ct->view->editContact = $form->getValue('editContact');
		$form->getElement("editContact")->setValue("0");
	    }
	    if ($form->getValue('v_addNew') == "1") {
		$this->ct->view->v_addNew = $form->getValue('v_addNew');
		$form->populate($existing_data);
	    }
	} else {
	    $form->populate($existing_data);
	}
    }

    /**
     * Function to update vendors contact
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editContact() {
	$this->ct->getHelper('BUtilities')->setEmptyLayout();
	$form = new Admin_Form_UserContact();
	$this->ct->view->form = $form;
	$userContact = $this->ct->em->getRepository('BL\Entity\UserContact')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id'), 'user_id' => (int) $this->ct->getRequest()->getParam('vendor_id')));

	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
	    if ($form->isValid($formData)) {
		$userContact->sal = $form->getValue('sal');
		$userContact->first_name = $form->getValue('first_name');
		$userContact->last_name = $form->getValue('last_name');
		$userContact->title = $form->getValue('title');
		$userContact->address_line1 = $form->getValue('address_line1');
		$userContact->city = $form->getValue('city');
		$userContact->email = $form->getValue('email');
		$userContact->state = $form->getValue('state');
		$userContact->zipcode = $form->getValue('zipcode');
		$userContact->phone = $form->getValue('phone');
		$userContact->phone_ext = $form->getValue('phone_ext');
		$userContact->mobile = $form->getValue('mobile');
		$userContact->fax = $form->getValue('fax');
		$userContact->contact_type = $form->getValue('contact_type');
		$this->ct->em->persist($userContact);
		$this->ct->em->flush();
		$this->ct->view->msg = "Contact updated succesfully!";
//                    $this->ct->getHelper('flashMessenger')->direct("Contact updated succesfully!", "Info");
//                    $this->ct->view->BUrl()->redirect($this->ct->view->BUrl()->absoluteUrl());
	    }
	} else {
	    $existing_data = array(
		'sal' => $userContact->sal,
		'first_name' => $userContact->first_name,
		'last_name' => $userContact->last_name,
		'title' => $userContact->title,
		'address_line1' => $userContact->address_line1,
		'city' => $userContact->city,
		'email' => $userContact->email,
		'state' => $userContact->state,
		'zipcode' => $userContact->zipcode,
		'phone' => $userContact->phone,
		'phone_ext' => $userContact->phone_ext,
		'mobile' => $userContact->mobile,
		'fax' => $userContact->fax,
		'contact_type' => $userContact->contact_type
	    );
//        print_r($existing_data);
	    $form->populate($existing_data);
	}
    }

    /**
     * Function to add a vendor
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addVendor() {
	$form = new Admin_Form_AddVendor();
	$this->ct->view->form = $form;

	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
	    if ($form->isValid($formData)) {
		/**
		 * for adding new vendor
		 */
		$user = new \BL\Entity\User();
		$user->account_type = ACC_TYPE_VENDOR;
		$user->organization_name = $form->getValue('organization_name');
		$user->username = $form->getValue('username');
		$user->password = md5($form->getValue('password'));
		$user->first_name = $form->getValue('v_first_name');
		$user->last_name = $form->getValue('v_last_name');
		$user->address_line1 = $form->getValue('address_line_1');
		$user->address_line2 = $form->getValue('address_line_2');
		$user->city = $form->getValue('city');
		$user->state = $form->getValue('state');
		$user->zip = $form->getValue('zip');
		$user->email = $form->getValue('recovery_email');
		$user->phone = $form->getValue('phone_1');
		$user->phone2 = $form->getValue('phone_2');
		$user->fax = $form->getValue('fax');
		$user->website = $form->getValue('web_page');
		$user->user_status = $form->getValue('status');
		if ($form->getValue('status') == "Current") {
		    $user->reg_status = "activated";
		}
		$role = $this->ct->em->getRepository('BL\Entity\Role')->findOneBy(array('id' => ACC_TYPE_VENDOR));
		$user->roles->add($role);
		$this->ct->em->persist($user);
		$this->ct->em->flush();

		/**
		 * save vendor information to vendor_profiles table
		 */
		$vp = new \BL\Entity\VendorProfile();
		$vp->organization_name = $form->getValue('organization_name');
		$vp->address1 = $form->getValue('organization_name');
		$vp->address2 = $form->getValue('organization_name');
		$vp->city = $form->getValue('city');
		$vp->state = $form->getValue('state');
		$vp->zip = $form->getValue('zip');
		$vp->email = $form->getValue('company_email');
		$vp->phone1 = $form->getValue('phone_1');
		$vp->phone2 = $form->getValue('phone_2');
		$vp->fax = $form->getValue('fax');
		$vp->web_page = $form->getValue('web_page');
		$vp->user_id = $user;
		$this->ct->em->persist($vp);
		$this->ct->em->flush();

		/**
		 * for save user contact
		 */
		$userContact = new \BL\Entity\UserContact();
		$userContact->user_id = $user;
		$userContact->sal = $form->getValue('v_sal');
		$userContact->first_name = $form->getValue('v_first_name');
		$userContact->last_name = $form->getValue('v_last_name');
		$userContact->title = $form->getValue('v_title');
		$userContact->phone = $form->getValue('v_phone');
		$userContact->mobile = $form->getValue('v_mobile');
		$userContact->email = $form->getValue('v_email');
		$userContact->contact_type = $form->getValue('v_contact_type');
		$this->ct->em->persist($userContact);
		$this->ct->em->flush();

		$this->ct->getHelper('flashMessenger')->direct("Vendor added succesfully!", "Info");
		$this->ct->view->BUrl()->redirect('admin/vendors');
	    } else {
		$form->populate($formData);
	    }
	}
    }

    /**
     * Function to show create invoice section
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showCreateInvoice() {
	$form = new Admin_Form_CreateInvoice();
	if ($this->ct->getRequest()->isPost()) {
	    $this->saveInvoice($form);
	}
	$this->ct->view->form = $form;
    }

    /**
     * Function to
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showCreateVendorInvoice() {
	$form = new Admin_Form_CreateInvoice();
//        echo $this->ct->getRequest()->getParam('id');
	$vendor = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $this->ct->getRequest()->getParam('id'), 'user_status' => 'Current'));

	if ($this->ct->getRequest()->isPost()) {
	    $this->saveInvoice($form);
	} else {
	    $this->ct->view->vendor = $vendor;
	    $existing_data = array(
		'vendor_name' => trim($vendor->organization_name),
		'email' => $vendor->email,
		'address_line_1' => $vendor->address_line1,
		'address_line_2' => $vendor->address_line2,
		'city' => $vendor->city,
		'state' => $vendor->state,
		'zip' => $vendor->zipcode,
		'phone_1' => $vendor->phone,
		'phone_2' => $vendor->phone2,
		'fax' => $vendor->fax,
	    );
	    $form->populate($existing_data);
	}
	$this->ct->view->form = $form;
    }

    /**
     * Function to save vendor invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $form<object>
     * @return void
     */
    private function saveInvoice($form) {
	$formData = $this->ct->getRequest()->getPost();
	if ($form->isValid($formData)) {
	    $invoice = new \BL\Entity\Invoice();
	    $vendor = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => $formData['vid'], 'account_type' => ACC_TYPE_VENDOR));
	    $invoice->invoice_date = new DateTime(date($form->getValue('inv_date') . date('H:i:s')));
	    $invoice->created_at = new DateTime();
//            $invoice->updated_at = '';
//            $invoice->due_date = '';
	    $invoice->invoice_number = $form->getValue('inv_num');
	    $invoice->invoice_type = $form->getValue('inv_type');
	    $invoice->fiscal_year = BL_AMC::getCurrentFiscalYear();
	    $invoice->quarter = BL_AMC::getCurrentQarter();
	    $invoice->company_name = $form->getValue('vendor_name');
	    $invoice->webpage = '';
	    $invoice->invoice_term = $form->getValue('inv_term');
	    $invoice->address_line1 = $form->getValue('address_line_1');
	    $invoice->address_line2 = $form->getValue('address_line_2');
	    $invoice->city = $form->getValue('city');
	    $invoice->state = $form->getValue('state');
	    $invoice->zip = $form->getValue('zip');
	    $invoice->phone1 = $form->getValue('phone_1');
	    $invoice->phone2 = $form->getValue('phone_2');
	    $invoice->email = $form->getValue('email');
	    $invoice->fax = $form->getValue('fax');
	    $invoice->invoice_status = 'Open';
	    $invoice->payment_status = 'Due';
	    $invoice->display_record = '';
	    $invoice->amount_due = array_sum($formData['total']);
	    $invoice->amount_paid = '';
	    $invoice->vendor_id = $vendor;
	    
	    $invoiceID = "INV_";
	    			 
    	$id = $invoice->id . "";
    			 
    	for ($i = 0; $i < 9-strlen($id); $i++){
    		$invoiceID .= "0";
    	}
    			 
    	$invoiceID .= $id;
    			 
    	$invoice->invoice_number = $invoiceID;
    	$this->ct->em->persist($nInvoice);
    	$this->ct->em->flush();

	    if (isset($formData['save_only']) && $formData['save_only'] == "Save") {
		$this->ct->getHelper('BUtilities')->setNoLayout();
		$this->ct->em->persist($invoice);
		$this->ct->em->flush();
		$this->saveInvoiceLineItems($formData, $invoice); // save invoice lineitems
		echo Zend_Json::encode(array('success' => true, 'message' => 'Invoice saved succesfully!'));
	    }

	    if (isset($formData['invoice_save']) && $formData['invoice_save'] == "Save") {
		$this->ct->getHelper('BUtilities')->setNoLayout();

		$greek_orgs = array();
		foreach ($formData['greek_org'] as $go) {
		    $client = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $go, 'account_type' => (int) ACC_TYPE_CLIENT));
		    $greek_orgs[] = $client->organization_name;
		}

		$this->ct->view->username = $vendor->username;
		$this->ct->view->item_no = $formData['item_no'];
		$this->ct->view->description = $formData['description'];
		$this->ct->view->greek_org = $greek_orgs; //$formData['greek_org'];
		$this->ct->view->total = $formData['total'];

		$json_array = array(
		    'to' => $vendor->email,
		    'to_name' => $vendor->organization_name,
		    'from' => 'admin@greeklicensing.com',
		    'from_name' => 'Greek licenseing admin',
		    'subject' => 'Greek Licensing Invoice',
		    'body' => $this->ct->view->render('/vendors/invoice-email-template.phtml')
		);
		echo Zend_Json::encode($json_array);
	    } else if (isset($formData['invoice_save']) && $formData['invoice_save'] == "Send") {
		$invoice->email_date .= date('m-d-Y') . ',';
		$this->ct->getHelper('BUtilities')->setNoLayout();
		$this->ct->em->persist($invoice);
		$this->ct->em->flush();
		$this->saveInvoiceLineItems($formData, $invoice); // save invoice lineitems
		try {
		    $params = array(
			'to' => $formData['to_email'],
			'to_name' => $formData['to_name'],
			'from' => $formData['from_email'],
			'from_name' => $formData['from_name'],
			'subject' => $formData['subject'],
			'body' => $formData['email_body']
		    );
		    if (($formData['cc_email'] != NULL) || ($formData['cc_email'] != '')) {
			$params['cc'] = preg_split('/[;,]/', $formData['cc_email']);
		    }
		    $this->ct->getHelper('BUtilities')->send_mail($params);
		    $invoice->email_date .= date('m-d-Y') . ',';
		    $this->ct->em->persist($invoice);
		    $this->ct->em->flush();
		    echo Zend_Json::encode(array('success' => true, 'message' => 'Invoice send successfully!'));
		} catch (Zend_Mail_Exception $e) {
		    echo Zend_Json::encode(array('success' => false, 'message' => $e->getMessage()));
		}
	    }
	} else {
	    $form->getElement('inv_date')->setValue(date("m/d/Y"));
	}
    }

    /**
     * Function to insert invoice line items
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param $formData<array>
     * @param $invoice<object>
     * @return void
     */
    private function saveInvoiceLineItems($formData, $invoice) {

	for ($i = 0; $i < count($formData['item_no']); $i++) {
	    $client = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $formData['greek_org'][$i], 'account_type' => ACC_TYPE_CLIENT));

	    $lineItems = new \BL\Entity\InvoiceLineItems();
	    $lineItems->lineitems_number = $this->ct->view->BUtils()->getInvoiceNumber($client->id);
	    $lineItems->invoice_number_li = $invoice->invoice_number;
	    $lineItems->amount_due = $formData['total'][$i];
	    $lineItems->amount_paid = '';
	    $lineItems->check_number = '';
	    $lineItems->payment_status = 'Due';
	    $lineItems->invoice_status = 'Open';
	    $lineItems->fiscal_year = BL_AMC::getCurrentFiscalYear();
	    $lineItems->description = $formData['description'][$i];
	    $lineItems->quarter = BL_AMC::getCurrentQarter();
	    $lineItems->invoice_id = $invoice;
	    $lineItems->client_id = $client;
	    $this->ct->em->persist($lineItems);
	}
	$this->ct->em->flush();
    }

    /**
     * Function to get registrants vendors
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getRegistrants() {
	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$sorting_cols = array(
	    '0' => 'u.organization_name',
	    '1' => 'u.status',
	    '2' => 'u.reg_date',
	    '3' => 'u.approve_date',
	    '4' => 'u.decline_date',
	);
	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
	    'iSortCol_0' => $this->ct->getRequest()->getParam('iSortCol_0', 0),
	    'reg_status' => $this->ct->getRequest()->getParam('reg_status'),
	    'sort_dir' => $this->ct->getRequest()->getParam('sSortDir_0', 'asc'),
	    'sort_key' => $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_0', 0)]
	);
	$records = $this->ct->em->getRepository("BL\Entity\User")->getRegistrantUsers($params);
//        $this->ct->view->BUtils()->doctrine_dump($records);
//        die();
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\User")->getRegistrantUsers($params);

	$json = '{"iTotalRecords":' . $records_total . ',
             "iTotalDisplayRecords": ' . $records_total . ',
             "aaData":[';
	$first = 0;

	foreach ($records as $v) {
	    if ($first++) {
		$json .= ',';
	    }
	    $reg_date = $v->reg_date instanceof DateTime ? $v->reg_date->format('m/d/y h:i a') : '-';
	    $approve_date = $v->approve_date instanceof DateTime ? $v->approve_date->format('m/d/y h:i a') : '-';
	    $decline_date = $v->decline_date instanceof DateTime ? $v->decline_date->format('m/d/y h:i a') : '-';
	    $action = '<a class=\"view_link\" href=\"javascript:;\" id=\"' . $v->id . '\" rel=\"' . $v->id . '\">View</a>';
	    if (ucfirst($v->reg_status) == 'Pending') {
		$action = '<a class=\"review_link\" href=\"javascript:;\" id=\"' . $v->id . '\" rel=\"' . $v->id . '\">Review</a>';
	    } else if (ucfirst($v->reg_status) == 'Declined') {
		$action = '<a class=\"declined_view\" href=\"javascript:;\" id=\"' . $v->id . '\" rel=\"' . $v->id . '\">View</a>';
	    }
	    $json .= '["' . $v->organization_name . '",
                "' . ucfirst($v->reg_status) . '",
                "' . $reg_date . '",
                "' . $approve_date . '",
                "' . $decline_date . '",
                "' . $action . '"]';
	}
	$json .= ']}';
	return $json;
    }

    /**
     * Function to Get Letter Index of Excel Colums . 1=>A , 27 = AA
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getNameFromNumber($num) {
	$numeric = ($num - 1) % 26;
	$letter = chr(65 + $numeric);
	$num2 = intval(($num - 1) / 26);
	if ($num2 > 0) {
	    return $this->getNameFromNumber($num2) . $letter;
	} else {
	    return $letter;
	}
    }

    /**
     * Function to review registrant
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return void
     */
    public function reviewRegistrant() {
	if ($this->ct->getRequest()->getParam('status') == 'decline') {
	    $form = new Admin_Form_DeclineUser();
	    $this->ct->view->form = $form;

	    if ($this->ct->getRequest()->isPost()) {
		$formData = $this->ct->getRequest()->getPost();
		if ($form->isValid($formData)) {
		    $user = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));
		    $user->reason_for_declining = $form->getValue('decline_reason');
		    $user->reg_status = 'Declined';
		    $user->user_status = 'Declined';
		    $user->decline_date = new DateTime(date("Y-m-d H:i:s"));
		    $user->updated_at = new DateTime(date("Y-m-d H:i:s"));
		    $this->ct->em->persist($user);
		    $this->ct->em->flush();

		    //$this->ct->view->username = $user->username;
		    $this->ct->view->organization_name = $user->organization_name;
		    $this->ct->view->mail_body = $form->getValue('decline_reason');
		    try {
			$send = $this->ct->getHelper('BUtilities')->send_mail(array(
			    'to' => $user->email,
			    'to_name' => $user->organization_name,
			    'from' => 'registration@greeklicensing.com',
			    'from_name' => 'Greek Licensing Registration',
			    'subject' => 'Greek Licensing User Registration',
			    'body' => $this->ct->view->render('/vendors/declined-user-email.phtml')
				));
			$this->ct->view->msg = "User declined successfully!";
		    } catch (Zend_Mail_Exception $e) {
			$this->ct->view->msg = $e->getMessage();
		    }
		} else {
		    $form->populate($formData);
		}
	    }
	    $this->ct->getHelper('viewRenderer')->setRender('decline-registrant');
	} else if ($this->ct->getRequest()->getParam('status') == 'approve') {
	    $user = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));
	    $user->reg_status = 'activated';
	    $user->user_status = 'Registered';
	    $user->approve_date = new DateTime(date("Y-m-d H:i:s"));
	    $user->updated_at = new DateTime(date("Y-m-d H:i:s"));
	    $this->ct->em->persist($user);
	    $this->ct->em->flush();

//            $this->ct->view->username = $user->username;
	    $this->ct->view->organization_name = $user->organization_name;
//            $this->ct->view->mail_body = 'Your application approved succefully. Click on the link below to login-<br />';
//            $this->ct->view->mail_body .= '<a href="' . $this->ct->view->BUrl()->site_url('login') . '">' . $this->ct->view->BUrl()->site_url('login') . '</a>';
	    $this->ct->view->mail_body = 'Your request for access to the Vendor Gateway has been approved. Click on the link below and login with the username: <b>' . $user->username . '</b><br />';
	    $this->ct->view->mail_body .= '<a href="' . $this->ct->view->BUrl()->site_url('login') . '">' . $this->ct->view->BUrl()->site_url('login') . '</a>';
	    $this->ct->view->msg = '';
	    try {
		$send = $this->ct->getHelper('BUtilities')->send_mail(array(
		    'to' => $user->email,
		    'to_name' => $user->organization_name,
		    'from' => 'registration@greeklicensing.com',
		    'from_name' => 'Greek Licensing Registration',
		    'subject' => 'Greek Licensing User Registration',
		    'body' => $this->ct->view->render('/vendors/declined-user-email.phtml')
			));
		$this->ct->view->msg = "User approved successfully!";
	    } catch (Zend_Mail_Exception $e) {
		$this->ct->view->msg = $e->getMessage();
	    }
	    $this->ct->getHelper('flashMessenger')->direct($this->ct->view->msg);
	    $this->ct->view->BUrl()->redirect('admin/vendors/registrants');
	} else {
	    $user = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id')));
	    $this->ct->view->user_id = $this->ct->getRequest()->getParam('id');
	    $this->ct->view->link = $this->ct->getRequest()->getParam('link');
//            $this->ct->view->BUtils()->doctrine_dump($user);
	    $form = new Application_Form_Signup();
	    $this->ct->view->form = $form;
	    $existing_data = array(
		'username' => $user->username,
		'email' => $user->email,
		'organization_name' => $user->organization_name,
		'address_line_1' => $user->address_line1,
		'address_line_2' => $user->address_line2,
		'city' => $user->city,
		'state' => $user->state,
		'zip' => $user->zipcode,
		'phone_1' => $user->phone,
		'phone_2' => $user->phone2,
		'fax' => $user->fax,
		'web_page' => $user->website,
		'company_email' => $user->company_email
	    );
	    $form->populate($existing_data);
	    if ($this->ct->view->link === 'declined_view') {
		$this->ct->view->decline_reason = $user->reason_for_declining;
		$this->ct->getHelper('viewRenderer')->setRender('declined-reasons');
	    }
	    if ($this->ct->view->link === 'view_link') {
		$this->ct->getHelper('viewRenderer')->setRender('view-approved');
	    }
	}
    }

    /**
     * Function to show invoices by parameters
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getInvoicesByParams() {
    	error_log("\ngetInvoicesByParams() " . $this->ct->getRequest()->getParam('iDisplayStart', 0), 3, "./errorLog.log");

	$params = array(
	    'search' => $this->ct->getRequest()->getParam('sSearch', ''),
	    'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
	    'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
	    'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
	    'year' => $this->ct->getRequest()->getParam('fiscal_year'),
	    'quarter' => $this->ct->getRequest()->getParam('quarter'),
	    'invoice_type' => $this->ct->getRequest()->getParam('invoice_type'),
	    'fiscal_year' => $this->ct->getRequest()->getParam('fiscal_year'),
	    'invoice_status' => $this->ct->getRequest()->getParam('invoice_status'),
	    'payment_status' => $this->ct->getRequest()->getParam('payment_status'),
	    'vendor_status' => $this->ct->getRequest()->getParam('vendor_status')
	);

	/**
	 * Let's take care of the sorting column to be passed to doctrine.
	 * DataTable sends params like iSortCol_0.
	 */
	$params['sort_key'] = $this->ct->getRequest()->getParam('iSortCol_0', 0);
	$params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
	$params['vendor_id'] = $this->ct->getRequest()->getParam('id');


	$records = $this->ct->em->getRepository("BL\Entity\Invoice")->searchVendorInvoiceByParams($params)->getResult();
//        "<a href="\javascript:;\" class="\payment_link\" rel=\"' . $v->id . '\">Pay</a>"
	$params['show_total'] = true;
	$records_total = $this->ct->em->getRepository("BL\Entity\Invoice")->searchVendorInvoiceByParams($params);
	/**
	 * Datatable doesn't understand json_encode and have a custom json format
	 */
	$quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');
	$json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":';
	$first = 0;
	$currency = new Zend_Currency('en_US');
	
	$prec = array();
	
	foreach ($records as $v) {
		$prec[] = array(
				//$v->vendor_id->organization_name,
				str_replace(chr(13), "", str_replace(chr(10), "", $v->vendor_id->organization_name)),
				$v->vendor_id->user_status,
				$v->invoice_number,
				$v->invoice_date->format('M d, Y H:i a'),
				$this->ct->view->BUtils()->getCurrency($v->amount_due),
				$this->ct->view->BUtils()->getCurrency($v->amount_paid),
				'<a href="javascript:;" class="lineitems_link" id="' . $v->id . '" rel="' . $v->invoice_number . '">View</a>&nbsp;&nbsp;<a href="javascript:;" class="payment_link" id="' . $v->id . '" rel="' . $v->invoice_number . '">Payment</a>'
		
		);
		/*
		error_log("\nrecord: " . $first . " id: " . $v->id . " vendor: " . $v->vendor_id->id, 3, "./errorLog.log");
		
	    if ($first++) {
		$json .= ',';
	    }

	    $action = '<a href=\"javascript:;\" class=\"lineitems_link\" id=\"' . $v->id . '\" rel=\"' . $v->invoice_number . '\">View</a>&nbsp;&nbsp;';
	    $action .= '<a href=\"javascript:;\" class=\"payment_link\" id=\"' . $v->id . '\" rel=\"' . $v->invoice_number . '\">Payment</a>';

	    
	    
	    $json .= '[
              "' . $v->vendor_id->organization_name . '",
              "' . $v->vendor_id->user_status . '",
              "' . $v->invoice_number . '",
              "' . $v->invoice_date->format('M d, Y H:i a') . '",
              "' . $this->ct->view->BUtils()->getCurrency($v->amount_due) . '",
              "' . $this->ct->view->BUtils()->getCurrency($v->amount_paid) . '",
              "' . $action . '"
              ]';*/
	}
	$json .= Zend_Json::encode($prec);
	$json .= '}';
	
	error_log("\njson: " . $json, 3, "./errorLog.log");
	
	return $json;
    }
/**
     * Function to show invoices by parameters
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getInvoicesByParams2() {

        $params = array(
            'search' => $this->ct->getRequest()->getParam('sSearch', ''),
            'page_start' => $this->ct->getRequest()->getParam('iDisplayStart', 1),
            'draw_count' => $this->ct->getRequest()->getParam('sEcho', 1),
            'per_page' => $this->ct->getRequest()->getParam('iDisplayLength', 20),
            'year' => $this->ct->getRequest()->getParam('fiscal_year'),
            'quarter' => $this->ct->getRequest()->getParam('quarter'),
            'invoice_type' => $this->ct->getRequest()->getParam('invoice_type'),
            'fiscal_year' => $this->ct->getRequest()->getParam('fiscal_year'),
            'invoice_status' => $this->ct->getRequest()->getParam('invoice_status'),
            'payment_status' => $this->ct->getRequest()->getParam('payment_status'),
            'vendor_status' => $this->ct->getRequest()->getParam('vendor_status')
        );

        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $params['sort_key'] = $this->ct->getRequest()->getParam('iSortCol_0', 0);
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');
	$records = $this->ct->em->getRepository("BL\Entity\Invoice")->searchVendorInvoiceByParams($params)->getResult();
	if($this->ct->getRequest()->getParam('Export')==1){
		$data = array();
		$i=0;
		foreach ($records as $v) {
		$line = array();
		$line[0] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$v->vendor_id->organization_name);
		$line[1] = $v->invoice_type;
		$line[2] = $v->invoice_status;
		$line[3] = $v->payment_status;
		$line[4] = $this->ct->view->BUtils()->getCurrency($v->amount_due+$v->amount_paid);
		$line[5] = $this->ct->view->BUtils()->getCurrency($v->amount_due);
		$data[$i] = $line;
		$i++;
		}
		return $data;
	}
        $records = $this->ct->em->getRepository("BL\Entity\Invoice")->searchVendorInvoiceByParams($params)->getResult();
//        "<a href="\javascript:;\" class="\payment_link\" rel=\"' . $v->id . '\">Pay</a>"
        $params['show_total'] = true;
        $records_total = $this->ct->em->getRepository("BL\Entity\Invoice")->searchVendorInvoiceByParams($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');
        $json = '{"iTotalRecords":' . $records_total . ',
         "iTotalDisplayRecords": ' . $records_total . ',
         "aaData":[';
        $first = 0;
	$sumdue =0;
	$sumtobepaid =0;
        $currency = new Zend_Currency('en_US');
        foreach ($records as $v) {
            if ($first++) {
                $json .= ',';
            }

            $action = '<a href=\"javascript:;\" class=\"lineitems_link\" id=\"' . $v->id . '\" rel=\"' . $v->invoice_number . '\"></a>&nbsp;&nbsp;';
            $action .= '<a href=\"javascript:;\" class=\"payment_link\" id=\"' . $v->id . '\" rel=\"' . $v->invoice_number . '\"></a>';
	    $sumdue = $sumdue + $v->amount_due+$v->amount_paid;
	    $sumtobepaid = $sumtobepaid + $v->amount_due;

            $json .= '[
              "' . preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$v->vendor_id->organization_name) . '",
              "' . $v->invoice_type . '",
              "' . $v->invoice_status . '",
              "' . $v->payment_status . '",
              "<font color=\"red\">' . $this->ct->view->BUtils()->getCurrency($v->amount_due+$v->amount_paid) . '</font>",
              "<font color=\"red\">' . $this->ct->view->BUtils()->getCurrency($v->amount_due) . '</font>"
              ]';
        }
	if($first <> 0)
	{
	$json .= ',[
              "' . 'Total Amount' . '",
              "' . '' . '",
              "' . '' . '",
              "' . '' . '",
              "' . $this->ct->view->BUtils()->getCurrency($sumdue) . '",
              "' . $this->ct->view->BUtils()->getCurrency($sumtobepaid) . '"
              ]';
	}
        $json .= ']}';
        return $json;
    }
    /**
     * Function to get advance payment invoices
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function getInvoiceGenerateAdvPmt($fsYear) {
	ini_set('max_execution_time', 10800); //300 seconds = 5 minutes
	$params = array('account_type' => ACC_TYPE_VENDOR, 'user_status' => 'Current');
//        $vendors = $this->ct->em->getRepository('BL\Entity\User')->getVendorNames(); //for all vendors except user_status != 'Cancelled'
	$vendors = $this->ct->em->getRepository('BL\Entity\User')->getUsersByTypeAndStatus($params); //for all vendors user_status = 'Current'
	//$vendors = $this->ct->em->getRepository('BL\Entity\User')->findBy($params);
//        $this->ct->view->BUtils()->doctrine_dump($vendors);
//        echo count($vendors). " <br />";
//        die();

	$all_clients = $this->ct->em->getRepository('BL\Entity\User')->getUsersByTypeAndStatus(array('account_type'=>ACC_TYPE_CLIENT, 'user_status'=>'Current'));

	error_log("\ngetInvoiceGenerateAdvPmt() fiscal year: " . BL_AMC::getCurrentFiscalYear() . " quarter: " . BL_AMC::getCurrentQarter() . " max_execution_time " . ini_get('max_execution_time'), 3, "./errorLog.log");
	
	$count = 0;
	foreach ($vendors as $key => $vendor) {
		
		
		//if ($vendor['id'] == 9378){
		//if (true){
		error_log("\n vendor " . $vendor['id'] . " ".$count . " memory " . memory_get_usage(), 3, "./errorLog.log");	
	    
		$count++;
	   // $licensed_clients = $this->ct->em->getRepository('BL\Entity\License')->getClientsForVendorInvoice($vendor['id']);
	    
	    $licensed_clients = $this->ct->em->getRepository('BL\Entity\License')->findBy(array('vendor_id'=>(int)$vendor['id'], 'status'=>4));
	    $vendor_obj = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $vendor['id']));
	    $this->ct->view->licensed_clients = $licensed_clients;
	    $invoice_template = $this->ct->view->render('/vendors/invoice-generate-adv-pmt-template.phtml');

	    
	    $total_due = 0;
	    
	 //   foreach ($licensed_clients as $client){
	    	
	 //   	error_log("\nannual advance " . $client->annual_advance . " license id " . $client->id, 3, "./errorLog.log");
	    	
	    	//$c = $this->ct->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id'=>$client->client_id->id));
	   // 	if ($client->status == 4) $total_due += $client->default_renewal_fee;
	    //}
	    
	    
	    
	    $invoice = new \BL\Entity\Invoice();
	    $invoice->invoice_date = new DateTime(); //new DateTime(date($form->getValue('inv_date')));
	    $invoice->created_at = new DateTime();
//            $invoice->updated_at = '';
//            $invoice->due_date = '';
	    $invoice->invoice_number = $this->ct->view->BUtils()->getInvoiceNumber($vendor_obj->id);
	    $invoice->invoice_type = 'Annual';
	    $invoice->fiscal_year = $this->ct->getRequest()->getParam('year');
	    $invoice->quarter = '1';
	    $invoice->company_name = $vendor_obj->organization_name;
	    $invoice->webpage = $vendor_obj->website;
	    $invoice->invoice_term = 'Net 15 days';
	    $invoice->address_line1 = $vendor_obj->address_line1;
	    $invoice->address_line2 = $vendor_obj->address_line2;
	    $invoice->city = $vendor_obj->city;
	    $invoice->state = $vendor_obj->state;
	    $invoice->zip = $vendor_obj->zipcode;
	    $invoice->phone1 = $vendor_obj->phone;
	    $invoice->phone2 = $vendor_obj->phone2;
	    $invoice->email = $vendor_obj->email;
	    $invoice->fax = $vendor_obj->fax;
	    $invoice->payment_status = 'Due';
	    $invoice->display_record = '';
	   // $invoice->amount_due = 40 * (count($licensed_clients));
	    $invoice->amount_paid = '';
	    $invoice->vendor_id = $vendor_obj;
	    $this->ct->em->persist($invoice);
	    $this->ct->em->flush();
//            $this->ct->view->BUtils()->doctrine_dump($invoice);
	    $invoiceID = "INV_";
	     
	    $id = $invoice->id . "";
	     
	    for ($i = 0; $i < 9-strlen($id); $i++){
	    	$invoiceID .= "0";
	    }
	     
	    $invoiceID .= $id;
	     
	    $invoice->invoice_number = $invoiceID;
	    $this->ct->em->persist($invoice);
	    $this->ct->em->flush();
	    
	    $invoiceSentLog = new \BL\Entity\InvoiceSentLog();
	    $invoiceSentLog->quarter = $this->ct->getRequest()->getParam('quarter');
	    $invoiceSentLog->invoice_type = 'Annual';
	    $invoiceSentLog->vendor_id = $vendor_obj;
	    $this->ct->em->persist($invoiceSentLog);
	    $this->ct->em->flush();
	    
	    //for saving lineitems
//	    foreach ($licensed_clients as $key => $client) {

	    $number = 0;
	    
		foreach($all_clients as $client){
			if ($client['id'] != 85){
				$number ++;
		    	
		    	$c = $this->ct->em->getRepository('BL\Entity\ClientProfile')->findOneBy(array('user_id'=>$client['id']));
		    	$license = $this->ct->em->getRepository('BL\Entity\License')->findOneBy(array('client_id'=>$client['id'], 'vendor_id'=>$vendor['id']));
			    	
				$lineItems = new \BL\Entity\InvoiceLineItems();
				$lineItems->lineitems_number = $this->ct->view->BUtils()->getInvoiceNumber($vendor_obj->id);
				$lineItems->invoice_number_li = $invoice->invoice_number;
				//$lineItems->amount_due = 40;
				if (isset($license)){
					
					if ($license->status == 4){
						$lineItems->amount_due = $license->default_renewal_fee;
						$total_due += $license->default_renewal_fee;
						$lineItems->license_status = $license->status;
					}
					else{
						$lineItems->amount_due = 0;
						$lineItems->license_status = 0;
					}
				//	error_log("\nisset ". $license->default_renewal_fee . " status " . $lineItems->license_status, 3, "./errorLog.log");
				} else {
					$lineItems->amount_due = '0';
					$lineItems->license_status = 0;
				}
				$lineItems->amount_paid = '';
				$lineItems->check_number = '';
				$lineItems->status = '';
				$lineItems->invoice_status = '';
				$lineItems->fiscal_year = $fsYear;
				$lineItems->quarter = '1';
				$lineItems->invoice_id = $invoice;
				$lineItems->client_id = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$client['id']));
				$this->ct->em->persist($lineItems);
				$this->ct->em->flush();
//                $this->ct->view->BUtils()->doctrine_dump($lineItems);
//                die();
			}
	    }

	    $invoice->amount_due = $total_due;
	    $this->ct->em->persist($invoice);
	    $this->ct->em->flush();
	    $this->ct->em->clear();
	   
	    error_log("\nNumber: " . $number, 3, "./errorLog.log");

	    //for sending pdf invoice
	    /* $pdf_params = array(
	      'author'  => 'AMC Admin',
	      'title'   => 'Advance invoice payment',
	      'subject' => 'Invoice',
	      'pdf_content'  => $invoice_template,
	      'file_name'  => $invoice->invoice_number,
	      'file_path' => APPLICATION_PATH . '/../assets/files/invoices/',
	      'output_type' => 'F'
	      );
	      $save_to = $this->ct->view->BUtils()->getPDF($pdf_params);
	     */
	    $this->ct->view->username = $vendor_obj->username;
	    $this->ct->view->invoice_template = $invoice_template;
	    $email_body = $this->ct->view->render('/vendors/invoice-email-body-template.phtml');
	    $to_email = preg_split('/[;,]/', $vendor_obj->email);
	    $mail_params = array(
		//'to' => $to_email['0'],
		'to' => $to_email,
		'to_name' => $vendor_obj->username,
		'from' => 'admin@greeklicensing.com',
		'from_name' => 'AMC Admin',
		'subject' => 'Greek licensing invoice notification',
		'body' => $email_body,
//                'file' => $save_to
	    );
//            print_r($mail_params);
	  //  $this->ct->getHelper('BUtilities')->send_mail($mail_params);
	  //  if ($count === 1) {
	//	break;
	   // }
	//}
	}
	
	error_log("\ncount: {$count}", 3, "./errorLog.log");
	
    }
    
    /**
     * Function to generate late fees for vendors who have not submitted quarterly reports for every client license
     * @author Jason
     * @copyright Softura
     * @version 0.1
     * @access public
     * @return string
     */
    public function getInvoiceGenerateLateFees($quarter, $year){
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    	error_log("\ngetInvoiceGenerateLateFees()", 3, "./errorLog.log");
    	
    	$q = BL_AMC::$quarters[$quarter]['end'] + BL_AMC::GRACE;
    	
    	
    	$yr = substr($year, 0, 2) . substr($year, 5);
    	
    	$d = \DateTime::createFromFormat('Y z', "{$yr} {$q}");
		
    	if ($d == null){
    		error_log("\nd is null!", 3, "./errorLog.log");
    	}
    	
    	$cutoff = $d->getTimestamp();
    	
    	$params = array('account_type' => ACC_TYPE_VENDOR, 'user_status' => 'Current');
    	
//     	$vendors = $this->ct->em->getRepository('BL\Entity\User')->getUsersByTypeAndStatus($params); //for all vendors user_status = 'Current'
    	
    	$vendors = $this->ct->em->getRepository('BL\Entity\User')->findBy($params);
    	
    	error_log("\nyear " . $year . " quarter " . $quarter, 3, "./errorLog.log");
    	
    	$batchCount = 0;
    	
    	foreach($vendors as $vendor){
//     		if ($vendor->id != 2214){
			$operation = $this->ct->em->getRepository("BL\Entity\VendorOperation")->findOneBy(array('user_id'=>$vendor->id));
			
			if ($operation->have_late_fee){
				
				$batchCount ++;
	    		error_log("\nstart " . $vendor->id, 3, "./errorLog.log");
	    		$licenses = $this->ct->em->getRepository('BL\Entity\License')->getActiveClients($vendor->id);
	    		error_log("\nA", 3, "./errorLog.log");
//	     		$licenses = $this->ct->em->getRepository('BL\Entity\License')->findBy(array('vendor_id'=>$vendor->id)); , 'invoice_type'=>''
	    		
	    		$invoices = $this->ct->em->getRepository('BL\Entity\Invoice')->findBy(array('vendor_id'=>$vendor->id, 'quarter'=>$quarter, 'fiscal_year'=>$year, 'invoice_type'=>'Royalty Payments'));
	
	    		error_log("\nB", 3, "./errorLog.log");
	    		$totalFee = 0;
	
	    		if (sizeof($licenses)) error_log("\nlicenses set", 3, "./errorLog.log");
	    		else error_log("\nlicenses NOT set", 3, "./errorLog.log");
	    		
	    		if (sizeof($invoices)) error_log("\nInvoices set", 3, "./errorLog.log");
	    		else error_log("\nInvoices NOT set", 3, "./errorLog.log");
	
	    		foreach($licenses as $license){
	
	    			$found = false;
	    			foreach($invoices as $invoice){
	    				error_log("\nget the date of the invoice", 3, "./errorLog.log");
	    				$date = $invoice->invoice_date;

	    				error_log("\nare there any good ones?", 3, "./errorLog.log");
	    				
	    				if ($date < $cutoff){
		
		    				error_log("\nD", 3, "./errorLog.log");
		    				$lineItem = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id'=>$invoice->id, 'client_id'=>$license->client_id->id));
		    				
		    				if (sizeof($lineItem)){
		    					$found = true;
		    					error_log("\nE", 3, "./errorLog.log");
		    				}
	    				} else {
		    					error_log("\nSorry, but the invoice was just a little too late", 3, "./errorLog.log");
		    			}
	    			}
	    			
	    			if ($found == false) $totalFee += 15;
	    		}
	    		
	    		if ($totalFee > 0){
	    			$nInvoice = new \BL\Entity\Invoice();
	    			$nInvoice->vendor_id = $vendor;
	    			$nInvoice->invoice_date = new DateTime();
	    			$nInvoice->created_at = new DateTime();
	    			$nInvoice->invoice_number = $this->ct->view->BUtils()->getInvoiceNumber($vendor->id);
	    			$nInvoice->invoice_type = 'Late Fee';
	    			$nInvoice->fiscal_year = $year;
	    			$nInvoice->quarter = $quarter;
	    			$nInvoice->company_name = $vendor->organization_name;
	    			$nInvoice->webpage = $vendor->website;
	    			$nInvoice->invoice_term = 'Net 15 days';
	    			$nInvoice->address_line1 = $vendor->address_line1;
	    			$nInvoice->address_line2 = $vendor->address_line2;
	    			$nInvoice->city = $vendor->city;
	    			$nInvoice->state = $vendor->state;
	    			$nInvoice->zip = $vendor->zip;
	    			$nInvoice->phone1 = $vendor->phone;
	    			$nInvoice->phone2 = $vendor->phone2;
	    			$nInvoice->email = $vendor->email;
	    			$nInvoice->fax = $vendor->fax;
	    			$nInvoice->payment_status = 'Due';
	    			$nInvoice->display_record = '';
	    			$nInvoice->amount_due = $totalFee;
	    			$nInvoice->amount_paid = '';
	    			$this->ct->em->persist($nInvoice);
	    			$this->ct->em->flush();
	    			
	    			$invoiceID = "INV_";
	    			 
	    			$id = $nInvoice->id . "";
	    			 
	    			for ($i = 0; $i < 9-strlen($id); $i++){
	    				$invoiceID .= "0";
	    			}
	    			 
	    			$invoiceID .= $id;
	    			 
	    			$nInvoice->invoice_number = $invoiceID;
	    			$this->ct->em->persist($nInvoice);
	    			$this->ct->em->flush();
	    			
	    			
	    			foreach($licenses as $license){
	    			
	    				$found = false;
	    				foreach($invoices as $invoice){
		    				$date = $invoice->invoice_date;
		    				
		    				error_log("\nare there any good ones?", 3, "./errorLog.log");
		    				
		    				if ($date < $cutoff){
		    			
		    					error_log("\nD", 3, "./errorLog.log");
		    					$lineItem = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id'=>$invoice->id, 'client_id'=>$license->client_id->id));
		    					 
		    					if (sizeof($lineItem)){
		    						$found = true;
		    						error_log("\nE", 3, "./errorLog.log");
		    					}
		    				}
	    				}
	    			
	    				if ($found == false){
	    					$nLineItem = new \BL\Entity\InvoiceLineItems();
	    					$nLineItem->lineitems_number = $this->ct->view->BUtils()->getInvoiceNumber($vendor->id);
	    					$nLineItem->invoice_number_li = $nInvoice->invoice_number;
	    					$nLineItem->amount_due = 15;
	    					$nLineItem->license_status = $license->status;
	    					$nLineItem->amount_paid = '';
	    					$nLineItem->check_number = '';
	    					$nLineItem->status = '';
	    					$nLineItem->invoice_status = '';
	    					$nLineItem->fiscal_year = $year;
	    					$nLineItem->quarter = $quarter;
	    					$nLineItem->invoice_id = $nInvoice;
	    					$nLineItem->client_id = $license->client_id;
	    					$this->ct->em->persist($nLineItem);	
    						$this->ct->em->flush();
	    					
	    					error_log("\nPersisting", 3, "./errorLog.log");
	    				}
	    			}
	    		}
	    		
	    		error_log("\ntotalFee for vendor " . $vendor->id . " is " .$totalFee, 3, "./errorLog.log");
	    		
	    		if ($batchCount > 20) {
	    			$batchCount = 0;
	    			$this->ct->em->clear();
	    		}
    		}
    		
    	}
    	error_log("\nall Done", 3, "./errorLog.log");
    }

    /**
     * Function to add payment for an invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addPayment() {
	$invoice = $this->ct->em->getRepository("BL\Entity\Invoice")->findOneBy(array('id' => $this->ct->getRequest()->getParam('inv_id')));
	$invoice_lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->findBy(array('invoice_id' => $this->ct->getRequest()->getParam('inv_id')));
//        $this->ct->view->BUtils()->doctrine_dump($invoice_lineitems);

	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
//            print_r($formData);
	    //update invoice lineitems
	    $i = 0;
	    $total_due = 0;
	    $total_paid = 0;
	    foreach ($invoice_lineitems as $inv_li) {
		$inv_li->amount_due = ($inv_li->amount_due - $formData['amount_paid'][$i]);  //$formData['amount_due'][$i]
		$inv_li->amount_paid = ($inv_li->amount_paid + $formData['amount_paid'][$i]);
		$inv_li->check_number = $formData['check_number'][$i];
//                $inv_li->payment_status = '';
		$inv_li->invoice_status = $formData['invoice_status'][$i];
		$total_due += $inv_li->amount_due; //$formData['amount_due'][$i];
		$total_paid += $inv_li->amount_paid; //$formData['amount_paid'][$i];
		$i++;
		$this->ct->em->persist($inv_li);
	    }
	    $this->ct->em->flush();

	    //update invoice
	    $invoice->amount_due = $total_due;
	    $invoice->amount_paid = $total_paid;
	    if ($total_due === 0) {
		$invoice->invoice_status = 'Closed';
		$invoice->payment_status = 'Past Due';
	    } else if ($total_due > 0) {
		$invoice->invoice_status = 'Partially Paid';
		$invoice->payment_status = 'Received EFT';
	    }
	    $this->ct->em->persist($invoice);
	    $this->ct->em->flush();

	    //adding payment
	    $payment = new \BL\Entity\Payment();
	    $payment->amount_paid = $total_paid;
	    $payment->amount_remaining = $total_due;
	    $payment->record_date = new \DateTime(date("Y-m-d H:i:s"));
//            $payment->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
	    $payment->payment_year = $invoice->fiscal_year;
	    $payment->payment_quarter = $invoice->quarter;
	    $payment->payment_month = date('m');
	    $payment->check_num = $formData['check_number'][0];
	    $payment->kp_payment = '';
	    $payment->vendor = $invoice->vendor_id;
	    $payment->invoice = $invoice;
	    $this->ct->em->persist($payment);
	    $this->ct->em->flush();

	    //update payment lineitems
	    foreach ($invoice_lineitems as $inv_li) {
		$pmt_lineitem = new \BL\Entity\PaymentLineItems();
		$pmt_lineitem->payment_id = '';
		$pmt_lineitem->kp_lineitem = 'L' . $inv_li->id;
		$pmt_lineitem->record_date = new \DateTime(date("Y-m-d H:i:s"));
		$pmt_lineitem->payment_year = $payment->payment_year;
		$pmt_lineitem->payment_quarter = $payment->payment_quarter;
		$pmt_lineitem->payment_month = $payment->payment_month;
		$pmt_lineitem->sharing = 1;
		$pmt_lineitem->percent_amc = 0.0;
		$pmt_lineitem->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
		$pmt_lineitem->client = $inv_li->client_id;
		$pmt_lineitem->pmt_id = $payment;
		$this->ct->em->persist($pmt_lineitem);
	    }
	    $this->ct->em->flush();

	    $this->ct->getHelper('flashMessenger')->direct("Payment added successfully!", "Info");
	    $this->ct->view->BUrl()->redirect('admin/vendors/all-payments');
	} else {
	    $invoice_data = array();
	    $payment_data = array();
	    if (sizeof($invoice)) {
		$invoice_data['vendor_name'] = $invoice->vendor_id->organization_name;
		$invoice_data['inv_num'] = $invoice->invoice_number;
		$invoice_data['inv_type'] = $invoice->invoice_type;
		$invoice_data['inv_term'] = $invoice->invoice_term;
		$invoice_data['inv_date'] = $invoice->invoice_date->format('m/d/y');
		$invoice_data['email'] = $invoice->email;
		$invoice_data['address_line_1'] = $invoice->address_line1;
		$invoice_data['address_line_2'] = $invoice->address_line2;
		$invoice_data['city'] = $invoice->city;
		$invoice_data['state'] = $invoice->state;
		$invoice_data['zip'] = $invoice->zip;
		$invoice_data['phone_1'] = $invoice->phone1;
		$invoice_data['phone_2'] = $invoice->phone2;
		$invoice_data['fax'] = $invoice->fax;
		$payment_data['fiscal_year'] = $invoice->fiscal_year;
		$payment_data['quarter'] = $invoice->quarter;
		$payment_data['payment_status'] = $invoice->payment_status;
		$payment_data['invoice_status'] = $invoice->invoice_status;
	    }

	    $invoice_form = new Admin_Form_CreateInvoice();
	    $invoice_form->populate($invoice_data);
	    $this->ct->view->inv_form = $invoice_form;
//            $payment_form = new Admin_Form_Payment();
//            $payment_form->populate($payment_data);
//            $this->ct->view->pmt_form = $payment_form;
	    $this->ct->view->inv_number = $invoice->invoice_number;
	    $this->ct->view->lineitems = $invoice_lineitems;
	}
    }

    /**
     * Function to edit vendor notes
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function notesEdit() {

	$note = $this->ct->em->getRepository('BL\Entity\VendorNote')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('id', 0)));
	$form = new Admin_Form_VendorNotes();
	if ($this->ct->getRequest()->isPost()) {
	    $formData = $this->ct->getRequest()->getPost();
	    if ($form->isValid($formData)) {
		$note->note = $form->getValue('description');
		$note->updated_at = new \DateTime(date("Y-m-d H:i:s"));
		$this->ct->em->persist($note);
		$this->ct->em->flush();

		$this->ct->view->result = array('success' => true, 'message' => 'Note updated successfully!');
	    } else {
		$form->populate($formData);
	    }
	} else {
	    $form->populate(array('description' => $note->note, 'note_id' => $note->id));
	}
	$this->ct->view->form = $form;
    }

}

