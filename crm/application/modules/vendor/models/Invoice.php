<?php

/**
 * Vendor_Model_Invoice to process vendor InvoiceController actions
 *
 * @author Masud
 */
class Vendor_Model_Invoice {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }

    /**
     * Function to show Invoices
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetInvoices() {
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
        $sorting_cols = array('0' => 'inv.amount_due', '1' => 'inv.invoice_number');
        $params['sort_key'] = $sorting_cols[$this->ct->getRequest()->getParam('iSortCol_1', 1)];
        $params['sort_dir'] = $this->ct->getRequest()->getParam('sSortDir_0');
        $params['vendor_id'] = $this->ct->getRequest()->getParam('id');

        $AllRecords = $this->ct->em->getRepository("BL\Entity\Invoice")->getInvoice($params);
        /**
         * Datatable doesn't understand json_encode and have a custom json format
         */
        $quarters = array('1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth');

        $json = '{"iTotalRecords":' . $AllRecords['total_records'] . ',
         "iTotalDisplayRecords": ' . $AllRecords['total_records'] . ',
         "aaData":';
        $currency = new Zend_Currency('en_US');
        $total_due = 0;
        $prec = array();
        foreach ($AllRecords['records'] as $v) {
            $prec[] = array(
                '<a href="javascript:;" class="invoice_link" rel="' . $v['id'] . '">' . $v['invoice_number'] . '</a>',
                //$v['invoice_number'],
                (( (is_null($v['fiscal_year']) || $v['fiscal_year'] == '' ) && $v['quarter'] == 0 ) ? 'N/A' : ((is_null($v['fiscal_year']) || $v['fiscal_year'] == '') ? 'N/A' : $v['fiscal_year']) . ' - ' . (($v['quarter'] == 0) ? 'N/A' : 'Q' . $v['quarter']) ),
                ( (!is_null($v['invoice_date'])) ? date("M d, Y", strtotime($v['invoice_date'])) : "N/A" ),
                $v['invoice_type'],
                $v['invoice_status'],
                $v['payment_status'],
                $currency->toCurrency($v['amount_due']),
                $currency->toCurrency($v['amount_paid']),
            	$currency->toCurrency((floatval($v['amount_due']) ))
                //'<a href="javascript:;" class="invoices_link" rel="' . $v['id'] . '">View</a>&nbsp; <a href="javascript:;" class="delete_invoices" rel="d-' . $v['id'] . '">Delete</a>'
            );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to show invoice details
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showInvoice() {
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => $this->ct->getRequest()->getParam('inv_id')));
        $items = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->ct->getRequest()->getParam('inv_id'));
//        $this->ct->view->BUtils()->doctrine_dump($items);
        $form = new Admin_Form_CreateInvoice();
        $existing_data = array(
            'vendor_name' => $invoice->company_name,
            'inv_num' => $invoice->invoice_number,
            'inv_type' => $invoice->invoice_type,
            'inv_term' => isset($invoice->invoice_term)? $invoice->invoice_term: "Net 15 days",
            'inv_date' => $invoice->invoice_date->format('M d, Y H:i a'),
            'email' => $invoice->email,
            'address_line_1' => $invoice->address_line1,
            'address_line_2' => $invoice->address_line2,
            'city' => $invoice->city,
            'state' => $invoice->state,
            'zip' => $invoice->zip,
            'phone_1' => $invoice->phone1,
            'phone_2' => $invoice->phone2,
            'fax' => $invoice->fax
        );
        
        $form->removeElement('inv_type');
        $form->removeElement('inv_term');
        
        $invType = new Zend_Form_Element_Text('inv_type');
        $invType->setLabel('Invoice Type')
                ->setAttribs(array('class' => 'text'))
        ->setDisableLoadDefaultDecorators(true);

        $invTerm = new Zend_Form_Element_Text('inv_term');
        $invTerm->setLabel('Invoice Term')
                ->setAttribs(array('class' => 'text'))
        ->setDisableLoadDefaultDecorators(true);
        
        $form->addElement($invType);
        $form->addElement($invTerm);
        
        $form->populate($existing_data);
        
        
        
        $this->ct->view->form = $form;
        $this->ct->view->invoice = $invoice;
        $this->ct->view->items = $items;
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
        $sorting_cols = array('0' => 'v.check_num', '1' => 'v.kp_payment');
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
         "aaData":';
        $prec = array();
        $currency = new Zend_Currency('en_US');
        foreach ($records as $v) {
            $prec[] = array(
              (!$v->check_num ? "N/A" : $v->check_num),
              $v->record_date->format("m/d/Y"),
              $quarters[$v->payment_quarter],
              $v->payment_year,
              $currency->toCurrency($v->amount_paid),
              $currency->toCurrency($v->amount_paid),
              $currency->toCurrency($v->amount_remaining),
              '<a href="javascript:;" class="vendor_payment_link" rel="' . $v->id . '">View</a>&nbsp;
                  <a href="javascript:;" class="delete_payment" rel="d-' . $v->id . '">Delete</a>'
              );
        }
        $json .= Zend_Json::encode($prec);
        $json .= '}';
        return $json;
    }

    /**
     * Function to export inovice as PDF
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function exportAsPdf() {
        $this->ct->getHelper('BUtilities')->setNoLayout();
        $invoice = $this->ct->em->getRepository("BL\Entity\Invoice")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser()));
        $lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->getLineItemsForPDF($invoice->id);
        $frontControllerDir = $this->ct->getFrontController()->getControllerDirectory('admin'); // retrieve controller dir of the mentioned MODULE
        $this->ct->view->addBasePath(realpath($frontControllerDir . '/../views')); // do NOT add the "scripts" dir as it will be added automatically so do NOT do this: '/../views/scripts'
//        print_r($this->ct->view->getScriptPaths()) ;

        $this->ct->view->invoice = $invoice;
        $this->ct->view->lineitems = $lineitems;

        $html = $this->ct->view->render('invoice/invoice-pdf-template.phtml');

if($this->ct->view->invoice->invoice_type=="Annual")
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-annual.phtml');
                elseif($this->ct->view->invoice->invoice_type=="Misc.")
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-misc.phtml');
                elseif($this->ct->view->invoice->invoice_type=="Refund")
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-refund.phtml');
                elseif($this->ct->view->invoice->invoice_type=="Quarterly Report")
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-Qreport.phtml');
                elseif($this->ct->view->invoice->invoice_type=="Late Fee")
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-LateFee.phtml');
                 elseif($this->ct->view->invoice->invoice_type=="First Time Advance"){
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-ApplicationFee.phtml');
                        $html = $this->ct->view->render('invoice/invoice-pdf-template-ApplicationFee.phtml');
                }
                elseif(true)
                        $header = $this->ct->view->render('invoice/invoice-pdf-template-header-misc.phtml');
        $params = array(
            'author' => $invoice->vendor_id->organization_name,
            'title' => 'Export invoice',
            'subject' => 'Invoice',
            'pdf_content' => $html,
            'file_name' => $invoice->invoice_number,
            'file_path' => APPLICATION_PATH . '/../tmp/',
            'output_type' => 'I',
            'header' => $header
        );
	if($this->ct->view->invoice->invoice_type=="Annual")
                        $this->ct->view->BUtils()->getMulticolumnPDF($params);
                elseif(true)
                        $this->ct->view->BUtils()->getPDF2($params);
    }

    /**
     * Function to do online payment
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function onlinePayment() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id')));
        $bank_info = $this->ct->em->getRepository('BL\Entity\BankInfo')->findOneBy(array('vendor' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser()));
//        $this->ct->view->BUtils()->doctrine_dump($invoice);
//        die('---------------------');
        if (sizeof($bank_info)) {
       //     $this->ct->view->bank_info = $bank_info;

        	$acct = "*************" . substr($bank_info->account_number, 13);
        	$routing = "*****" . substr($bank_info->routing_number, 5);
        	
        	$this->ct->view->acct = $acct;
        	$this->ct->view->routing = $routing;
        	
        }
        $this->ct->view->amount_due = $invoice->amount_due;
        $form = new Vendor_Form_OnlinePayment();
        $this->ct->view->form = $form;

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if (isset($formData['bankinfo'])) {
                $form = new Vendor_Form_OnlinePayment(true);
                $this->ct->view->form = $form;
            }
            if (true) {
                $api_params = array();
                $api_params['account_number'] = $formData['account_number'];
                $api_params['routing_number'] = $formData['routing_number'];
                $api_params['amount'] = $invoice->amount_due;

                if (isset($formData['bankinfo'])){
                	error_log("\naccount: " . strlen($bank_info->account_number) . " routing: " . strlen($bank_info->routing_number), 3, "./errorLog.log");
                	$api_params['account_number'] = $bank_info->account_number;
                	$api_params['routing_number'] = $bank_info->routing_number;
                } else {
                	error_log("\nbankinfo not set", 3, "./errorLog.log");
                }
                
                /**
                 * Bill highway api call for online payment
                 */
                $response = $this->ct->getHelper('BUtilities')->bill_highway_api($api_params);
//                print_r($response);
		$arr = (array) $response;

                /**
                 * save online payment information
                 */
		if($arr['eCheckPaymentByGroupResult']->anyType[3]=="00")
		{
			$op = new \BL\Entity\OnlinePayment();
			$op->amount = $api_params['amount'];
			$op->bank_account = $api_params['account_number'];
			$op->bank_routing = $api_params['routing_number'];
			$op->vendor = $invoice->vendor_id;
			$op->invoice = $invoice;
			$this->ct->em->persist($op);
			$this->ct->em->flush();

			$invoice->invoice_status = 'Closed';
			$invoice->payment_status = 'Received EFT';
			$invoice->amount_paid=$api_params['amount'];
			$invoice->amount_due=0;
			$this->ct->em->persist($invoice);
			$this->ct->em->flush();
			
			$inv_lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id' => (int) $invoice->id));
			$payment_ref = 'Bill Highway '.$arr['eCheckPaymentByGroupResult']->anyType[1].' '.$formData['memo'];

			foreach ($inv_lineitems as $inv_l) {
			    $inv_l->check_number = $payment_ref;
			    $inv_l->payment_status = 'Paid-Online';
			    $inv_l->amount_paid = $inv_l->amount_due;
			    $inv_l->invoice_status = 'Closed';
			    $this->ct->em->persist($inv_l);
        		}
			$this->ct->em->flush();
			$payment = new \BL\Entity\Payment();
			$payment->amount_paid = $invoice->amount_paid;
			$payment->amount_remaining = $invoice->amount_due;
			$payment->record_date = new \DateTime(date("Y-m-d H:i:s"));
		//        $payment->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
			$payment->payment_year = $invoice->fiscal_year;
			$payment->payment_quarter = $invoice->quarter;
			$payment->payment_month = date('m');
			$payment->check_num = 'Bill Highway '.$arr['eCheckPaymentByGroupResult']->anyType[1].' '.$formData['memo'];
			$payment->kp_payment = '';
			$payment->vendor = $invoice->vendor_id;
			$payment->invoice = $invoice;
			$this->ct->em->persist($payment);
			$this->ct->em->flush();

			$inv_lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id'=>(int)$invoice->id));
				
			foreach ($inv_lineitems as $item){
				$client = $item->client_id;
			
				$payItem = new BL\Entity\PaymentLineItems();
			
				$payItem->payment_id = $payment->id;
				$payItem->pmt_id = $payment;
				$payItem->client = $client;
			
				$payItem->recordDate = new DateTime(date('Y-m-d H:i:s'));
				$payItem->last_modified_date = new DateTime(date('Y-m-d H:i:s'));
				$payItem->payment_year = BL_AMC::getCurrentFiscalYear();
				$payItem->payment_quarter = BL_AMC::getCurrentQarter();
				$payItem->payment_month = date('m');
				$payItem->amount_paid = $item->amount_due - $item->amount_paid;
			
				$sharing = '1';
				$percent = '0.0';
			
				$operation = $this->ct->em->getRepository('BL\Entity\ClientOperation')->findOneBy(array('user_id'=>$client));
			
				if ($operation != NULL){
					if ($operation->sharing == '1' || $operation->sharing == '' || $operation->sharing == NULL){
						$sharing = '1';
					} else {
						$sharing = '0';
					}
				}
			
				if ($sharing){
					if ($operation->commission_per = '' && $operation->commission_per != NULL) $percent = $operation->commission_per;
					else $percent = '0.30';
				}
			
				$payItem->sharing = $sharing;
				$payItem->percent_amc = $percent;
			
				$this->ct->em->persist($payItem);
				$this->ct->em->flush();
			}
			
			$this->ct->view->result = array('success' => true, 'message' => 'Online payment performed successfully!');
			
		}
		else{
                $this->ct->view->result = array('success' => true, 'message' => 'Error '. $arr['eCheckPaymentByGroupResult']->anyType[4]);
		}
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to process check payment
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function checkPayment() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();

        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => $this->ct->getRequest()->getParam('inv_id')));

        $form = new Vendor_Form_CheckPayment();
        $this->ct->view->form = $form;

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                /**
                 * save check payment information
                 */
                $cp = new \BL\Entity\CheckPayment();
                $cp->check_date = new \DateTime(date("Y-m-d", strtotime($form->getValue('check_date'))));
                $cp->check_number = trim($form->getValue('check_number'));
                $cp->check_amount = trim($form->getValue('check_amount'));
                $cp->vendor = $invoice->vendor_id;
                $cp->invoice = $invoice;
                $this->ct->em->persist($cp);
                $this->ct->em->flush();

                /**
                 * sending email to admin
                 */
                $this->ct->view->invoice_number = $invoice->invoice_number;
                $this->ct->view->check_date = $form->getValue('check_date');
                $this->ct->view->check_number = trim($form->getValue('check_number'));
                $this->ct->view->check_amount = trim($form->getValue('check_amount'));
                $this->ct->view->username = $invoice->vendor_id->username;
                $email_body = $this->ct->view->render('invoice/check-payment-email-template.phtml');

                /*
                 * select admin information to send email
                 */
                $admin = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) 1, 'account_type' => (int) ACC_TYPE_ADMIN));
                $form_email = preg_split('/[;,]/', $admin->email);
                $params = array(
                    'to' => preg_split('/[;,]/', $invoice->vendor_id->email),
                    'to_name' => $invoice->vendor_id->first_name,
                    'from' => $form_email[0],
                    'from_name' => $admin->first_name,
                    'subject' => "Invoice Check Payment",
                    'body' => $email_body,
                );
                $this->ct->getHelper('BUtilities')->send_mail($params);
                $this->ct->view->result = array('success' => true, 'message' => 'Check paid successfully!');
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to add vendor bank information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function addBankInfo() {
        $this->ct->getHelper('BUtilities')->setEmptyLayout();

        $form = new Vendor_Form_BankInfo();
        $this->ct->view->form = $form;
        $bank_info = $this->ct->em->getRepository("BL\Entity\BankInfo")->findOneBy(array('vendor' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser()));
        $existing_data = array();
        $is_bank_info = false;

        if (sizeof($bank_info)) {
            $is_bank_info = true;
            $existing_data['account_number'] = $bank_info->account_number;
            $existing_data['account_number_re'] = $bank_info->account_number;
            $existing_data['routing_number'] = $bank_info->routing_number;
            $existing_data['routing_number_re'] = $bank_info->routing_number;
        } else {
            $bank_info = new BL\Entity\BankInfo();
            $bank_info->vendor = $this->ct->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser(), 'account_type' => ACC_TYPE_VENDOR));
        }

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $bank_info->account_number = $form->getValue('account_number');
                $bank_info->routing_number = $form->getValue('routing_number');
//                $this->ct->view->BUtils()->doctrine_dump($bank_info);
                $this->ct->em->persist($bank_info);
                $this->ct->em->flush();
                $message = "Bank info ";
                $message .= $is_bank_info ? "updated" : "added";
                $message .= " successfully!";
                $this->ct->view->result = array('success' => true, 'message' => $message);
            } else {
                $form->populate($formData);
            }
        } else {
            $form->populate($existing_data);
        }
        $this->ct->view->is_bank_info = $is_bank_info;
    }

    /**
     * Function to show vendor bank information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function bankInfo() {
        $bank_info = $this->ct->em->getRepository("BL\Entity\BankInfo")->findOneBy(array('vendor' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser()));
        $this->ct->view->is_bank_info = sizeof($bank_info) ? true : false;
        
        if (sizeof($bank_info) > 0){
			$acct = "*************" . substr($bank_info->account_number, 13);
			$this->ct->view->acct = $acct;
			
			$routing = "*****" . substr($bank_info->routing_number, 5);
			$this->ct->view->routing = $routing;
        }
        
        $this->ct->view->bank_info = $bank_info;
    }

}

