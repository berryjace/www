<?php

/**
 * InvoiceController to do admin invoice operations
 *
 * @author Masud
 */
class Admin_InvoiceController extends Zend_Controller_Action {

    public $em;

    /**
     * Function to initialize controller
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->session = new Zend_Session_Namespace('default');
    }

    /**
     * Function is currently not using
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        // action body
    }

    /**
     * Function to edit invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function editAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $invoice_model = new Admin_Model_Invoice($this);
        $invoice_model->edit();
    }

    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveInvoiceAction() {
        $invoice_model = new Admin_Model_Invoice($this);
        $invoice_model->ajaxSaveInvoice();
    }

    /**
     * Function to email invoce to vendor
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function emailAction() {
        $this->_helper->JSLibs->do_call(array('load_tinymce_assets'));
        $invoice_model = new Admin_Model_Invoice($this);
        $invoice_model->email();
    }

    /**
     * Function to exprote invoice as a pdf format
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function exportAsPdfAction() {
        $invoice_model = new Admin_Model_Invoice($this);
		$this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $invoice_model->exportAsPdf();
    }
    /**
     * Function to export annual invoice as a pdf format
     * @author Jace
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function exportinvoiceAsPdfAction() {
        $invoice_model = new Admin_Model_Invoice($this);
                $this->view->status_array = $this->_helper->BUtilities->parseYAML(APPLICATION_PATH . '/configs/statuses.yml');
        $invoice_model->exportInvoiceAsPdf();
    }

    /**
     * Function to mark invoice as paid/partial paid/void/waive
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function markAsAction() {
        $invoice_model = new Admin_Model_Invoice($this);
        $invoice_model->markAs();
    }

    /**
     * Function to ajax mark invoice as paid and add a payment
     * @author Masud
     * @copyright iVive Labs
     * @version 0.1
     * @access public
     * @return String
     */    
    public function markAsPaidAction() {
        $invoice_model = new Admin_Model_Invoice($this);
        $invoice_model->markAsPaid();
    }
            
    /**
     * Function to ajax mark invoice as partial and add payment 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function markAsPartialPaidAction() {
        $inoice_model = new Admin_Model_Invoice($this);
        $inoice_model->markAsPartialPaid();
    }

    public function invoicePaymentReviewAction(){
    	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
    	$invoice = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => $this->_getParam('inv_id')));
    	$items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->_getParam('inv_id'));
    
    	$mark_as = $this->_getParam('mark-as');
    
    	$isFull = true;
    
    	if ($mark_as == 'partial') $isFull = false;
    
    	$this->view->isFull = $isFull;
    
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
    	
    	$trimmedItems = $items;
    	
    	if ($invoice->invoice_type != "Royalty Payments"){
    		$trimmedItems = array();
    		 
    		foreach($items as $item){
    			if ($item->amount_due - $item->amount_paid > 0){
    				//error_log("item->amount_due: " . $item->amount_due . " id: " . $item->id,3, "./errorLog.log");
    				 
    				$trimmedItems[] = $item;
    			}
    		}
    	}
    	
    	$form->populate($existing_data);
    	$this->view->form = $form;
    	$this->view->invoice = $invoice;
    	$this->view->items = $trimmedItems;
    
    	//error_log("\npaymentTotal: " . $this->session->reviewData['amount'], 3, "./errorLog.log");
    
    	$this->view->paymentTotal = $this->session->reviewData['amount'];
    	$this->view->reference_number = $this->session->reviewData['ref'];
    	$this->view->date = $this->session->reviewData['date'];
    }
    
    public function paymentRecordAction(){
    	$this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
    	$invoice = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => $this->_getParam('inv_id')));
    	$items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->_getParam('inv_id'));
    
    	$form = new Admin_Form_PayInvoice();
    	 
    	$vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>(int)$this->session->reviewData['vendor_id']));
    	 
    	$total = $this->session->reviewData['amount'];
    	
    	$total = str_replace(",", "", $total);
    	
    	$existingData = array(
    			'vendor_name'=>$invoice->company_name,
    			'inv_num'=>$invoice->invoice_number,
    			'inv_type'=>$invoice->invoice_type,
    			'ref_number'=>$this->session->reviewData['ref'],
    			'fiscal_year'=>Date("Y") . "-" . substr((Date("Y")+1), 2),
    			'quarter'=>Date("M")/3,
    			'total'=>$this->session->reviewData['amount'],
    			'inv_date'=>$invoice->invoice_date->format('M d, Y H:i a')
    	);
    	 
    	$form->populate($existingData);
    	 
    	$licenses = array();
    	$operations = array();
    	 
    	$numItems = count($this->session->paymentData);
    	 
    	for($i = 0; $i < $numItems; $i++){
    		$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$this->session->paymentData[$i]['client_id']));
    		$licenses[] = $this->em->getRepository('BL\Entity\License')->findOneBy(array('vendor_id'=>$vendor, 'client_id'=>$client));
    		$operations[] = $this->em->getRepository('BL\Entity\ClientOperation')->findOneBy(array('user_id'=>$this->session->paymentData[$i]['client_id']));
    	}
    	$this->view->licenses = $licenses;
    	$this->view->operation = $operations;
    	 
    	//if (is_null($form->vendorName))error_log("\nForm is null", 3, "./errorLog.log");
    	//else error_log("\nForm is NOT null", 3, "./errorLog.log");
    	
    	$trimmedItems = $items;
    	
    	error_log("\nNumber of items before trim: " . count($items), 3, "./errorLog.log");
    	
    	if ($invoice->invoice_type != "Royalty Payments"){
    		$trimmedItems = array();
    		 
    		foreach($items as $item){
    			//error_log("\namount due: " . $item->amount_due . " amount paid: " . $item->amount_paid, 3, "./errorLog.log");
    			
    			if ($item->amount_due - $item->amount_paid > 0){
    				foreach($this->session->paymentData as $payment){
    					if ($payment['client_id'] == $item->client_id->id){	
    						$trimmedItems[] = $item;
    						break;
    					}
    				} 
    			}
    		}
    	}

    

    	error_log("\nNumber of trimmed items: " . count($trimmedItems), 3, "./errorLog.log");
    	
    	
    	$this->view->form = $form;
    	$this->view->paymentData = $this->session->paymentData;
    	$this->view->invoice = $invoice;
    	$this->view->items = $trimmedItems;
    	$this->view->vendor_id = $vendor->id;
    	$this->view->paymentTotal = $total;
    	$this->view->reference_number = $this->session->reviewData['ref'];
    	$this->view->date = $this->session->reviewData['date'];
    	$this->view->de = $this->session->reviewData['due'];
    }
    
    public function ajaxSetPaymentDataAction(){
    	 
    	$params = $this->_getAllParams();
    	 
    	$totalPaid = $this->_getParam('total_paid');
    	$totalDue = $this->_getParam('total_remaining');
    	$type = urldecode($this->_getParam('type'));
    	 
    	$this->session->reviewData['amount'] = str_replace(",", "", str_replace("$", "", $totalPaid));
    	$this->session->reviewData['due'] = $totalDue;
    	 
    	$numRows = sizeof($params['client_id']);
    	 
    	$paymentData = array();
    	 
    	//error_log("\ntype " . $type, 3, "./errorLog.log");
    	
    	error_log("\nnum rows: " . $numRows, 3, "./errorLog.log");
    	 
    	for ($i = 0; $i < $numRows; $i++){
    		$ref = $params['ref_number'][$i];
    		$amt = $params['amt_paid'][$i];
    		$cli = $params['client_id'][$i];
    
    		$amt = (!is_null($amt))? (($amt != ''))? $amt : '0' : '0';
    
    		if ($amt > 0 || $type == "Royalty Payments"){
    			$paymentData[] = array('ref'=>$ref, 'paid'=>$amt, 'client_id'=>$cli);
    		}
    	}
    	 
    	$this->session->paymentData = $paymentData;
    	 
    	//error_log("\n end of function", 3, "./errorLog.log");
    	 
    	echo Zend_Json::encode(array('success'=>true));
    	exit;
    }
    
    public function ajaxSetReviewDataAction(){
    	 
    	$type = $this->_getParam('payment_type');
    	$ref = $this->_getParam('payment_ref');
    	$amount = $this->_getParam('payment_amount');
    	$date = $this->_getParam('payment_date');
    	$vendor_id = $this->_getParam('id');
    	$invoice_id = $this->_getParam('inv_id');
    	 
    	$sessionData = array('type'=>$type, 'ref'=>$ref, 'amount'=>$amount, 'date'=>$date, 'vendor_id'=>$vendor_id, 'invoice_id'=>$invoice_id);
    	
    	$this->session->reviewData = $sessionData;
    	 
    	echo Zend_Json::encode(array('success' => true));
    	exit;
    }
    
    public function ajaxFinalizePaymentAction(){
    	$this->_helper->BUtilities->setNoLayout();
    	 
    	$invoice_id = $this->_getParam('invoice_id');
    	$quarter = $this->_getParam('quarter');
    	$fiscal_year = $this->_getParam('fiscal_year');
    	 
    	$invoice = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id'=>$invoice_id));
    	$items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id'=>$invoice_id));
    	 
    	$paymentData = $this->session->paymentData;
    	$reviewData = $this->session->reviewData;
    	    	
    	$invoice->amount_paid = floatval(str_replace("$", "", str_replace(",", "", $reviewData['amount']))) + $invoice->amount_paid;
		if($invoice->amount_paid==$invoice->amount_due)
			$invoice->invoice_status = 'Closed';

		$status = $this->session->reviewData['type'];
		
		if($invoice->amount_paid < $invoice->amount_due){
			$status = 'Partially Paid';
		}
		
		$invoice->payment_status = $status;
    	 
    	$this->em->persist($invoice);
    	$this->em->flush();
    	$remains = $invoice->amount_due - $invoice->amount_paid;
    	 
    	foreach($items as $item){
    		$payment = null;
    		for ($i = 0; $i < count($paymentData); $i ++){
    			if ($item->client_id->id == $paymentData[$i]['client_id']){
    				$payment = $paymentData[$i];
    				break;
    			}
    		}
    
    		if (!is_null($payment)){
    			//error_log("\npayment is not null" , 3, "./errorLog.log");
    			 
    			$item->amount_paid = $payment['paid'] + $item->amount_paid;
    			if (strlen($payment['ref']) > 0){
    				if (strlen($item->check_number) <= 0){
    					$item->check_number = $payment['ref'];
    				} else {
    					$item->check_number .= ",".$payment['ref'];
    				}
    			}
    			
   				$item->payment_status = 'Received Check';
    			
    			if ($remains <= 0){
    				//error_log("\npaying off invoice lineitem " . $item->id, 3, "./errorLog.log");
    				$item->invoice_status = "Closed";
    			}
    		}
    
    		$this->em->persist($item);
    		$this->em->flush();
    	}
    	 
    	
    	if ($remains <= 0){
    		if ($invoice->invoice_type == "Application Fees" || $invoice->invoice_type == 'First Time Advance'){
    			foreach($items as $item){
    				$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('client_id'=>$item->client_id, 'vendor_id'=>$invoice->vendor_id));
    				
    				
    				
    				$license->payment_status = "paid";
    				$this->em->persist($license);
    				$this->em->flush();
    			}
    		}
    	}
    	 
	$pay = $this->em->getRepository('BL\Entity\Payment')->findOneBy(array('invoice'=>$invoice));
        if(!$pay)
    		$pay = new BL\Entity\Payment();
    	 
    	 
    	$pay->vendor = $invoice->vendor_id;
    	$pay->amount_paid = $reviewData['amount'];
    	$pay->payment_year = $fiscal_year;
    	$pay->payment_quarter = $quarter;
    	$pay->record_date = new DateTime(date('Y-m-d H:i:s'));
    	$pay->last_modified_date = new DateTime(date('Y-m-d H:i:s'));
    	$pay->payment_month = date('m');
    	$pay->amount_remaining = $remains;
    	$pay->invoice = $invoice;
		if (strlen($pay->check_num) <= 0){
                $pay->check_num = $reviewData['ref'];
        }else {
                $pay->check_num .= ",".$reviewData['ref'];
        }
    	 
    	$this->em->persist($pay);
    	$this->em->flush();
    	 
    	foreach ($paymentData as $data){
    		$license = $this->em->getRepository('BL\Entity\License')->findOneBy(array('client_id'=>$data['client_id'], 'vendor_id'=>$invoice->vendor_id->id));
    		
    		$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$data['client_id']));
    		
    		$operation = $this->em->getRepository('BL\Entity\ClientOperation')->findOneBy(array('user_id'=>$data['client_id']));
    		$payItem = new BL\Entity\PaymentLineItems();
    
    		$client = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id'=>$data['client_id']));
    
    		$payItem->payment_id = $pay->id;
    		$payItem->client = $client;
    		$payItem->recordDate = new DateTime(date('Y-m-d H:i:s'));
    		$payItem->payment_year = $fiscal_year;
    		$payItem->payment_quarter = $quarter;
    		$payItem->payment_month = date('m');

    		$sharing = '1';
    		
    		if (!is_null($operation)){
    			if ($operation->sharing == '1'){
    				$sharing = '1';
    			} else {
    				$sharing = '0';
    			}
    		}
    		
    		if (!is_null($license)){
    			if (strtolower($license->sharing) == 'yes'){
    				$sharing = '1';
    			}
    			else $sharing = '0';
    		}
    		$percent = '0';
    		
    		if ($sharing == '1'){
    			if (!is_null($operation)) $percent = $operation->commission_per;
    			else $percent = '0.3';
    		}
    		
    		if ($percent == null) $percent = '0';
    		
    		$payItem->sharing = $sharing;
    		$payItem->percent_amc = $percent;
    		$payItem->last_modified_date = new DateTime(date('Y-m-d H:i:s'));
    		$payItem->pmt_id = $pay;
    		$payItem->amount_paid = $data['paid'];
    
    		$this->em->persist($payItem);
    		$this->em->flush();
    	}
    	 
    	echo Zend_Json::encode(array('success' => true));
    }
}

