<?php

/**
 * Admin_Model_Invoice is to process vendor InvoiceController actions
 *
 * @author Masud
 */
class Admin_Model_Invoice {

    protected $ct;

    public function __construct(Zend_Controller_Action $ct) {
        $this->ct = $ct;
    }

    /**
     * Function to edit invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function edit() {
        $form = new Admin_Form_CreateInvoice();
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id')));
        $lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id' => (int) $this->ct->getRequest()->getParam('inv_id')));
        /**
         * Select all clients
         */
        $clients = $this->ct->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT), array('organization_name' => 'asc'));
        /**
         * Select licensed clients
         */
//        $clients = $this->ct->em->getRepository('BL\Entity\License')->getClientsForVendorInvoice((int) $invoice->vendor_id->id);
//        $this->ct->view->BUtils()->doctrine_dump($clients);
        $greek_org = array();
        foreach ($clients as $c) {
            $greek_org[$c->id] = $c->organization_name; //for all clients
//            $greek_org[$c->client_id->id] = $c->client_id->organization_name; //for licensed clients only
        }
        $existing_data = array(
            'vendor_name' => $invoice->company_name,
            'inv_num' => $invoice->invoice_number,
            'inv_type' => $invoice->invoice_type,
            'inv_term' => $invoice->invoice_term,
            'inv_date' => $invoice->invoice_date->format('d/m/Y'),
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
        $form->populate($existing_data);
        $this->ct->view->form = $form;
        $this->ct->view->invoice = $invoice;
        $this->ct->view->lineitems = $lineitems;
        $this->ct->view->clients = $greek_org;
    }

    /**
     * Function to
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxSaveInvoice() {
        $this->ct->getHelper('BUtilities')->setNoLayout();
        $form = new Admin_Form_CreateInvoice();
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id')));
//        $lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id' => (int) $this->ct->getRequest()->getParam('inv_id')));


        if ($this->ct->getRequest()->isPost()) {
        	error_log("\nIs Post", 3, "./errorLog.log");
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValidPartial($formData)) {
                $i = 0;
                
                $items = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id'=>$invoice->id));
                $allItems = array();
                foreach($items as $item){
                	$allItems[$item->id] = $item;
                }
                
                foreach ($formData['lineitem_id'] as $id) {
                    if (!empty($id)) {
                        $lt = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findOneBy(array('id' => (int) $id));
                        $lt->amount_due = $formData['amount_due'][$i];
                        $lt->description = $formData['description'][$i];
                        $lt->client_id = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => $formData['clients'][$i]));
                        $this->ct->em->persist($lt);

                        unset($allItems[$id]);
                    } else {
                        $lt = new \BL\Entity\InvoiceLineItems();
                        $lt->lineitems_number = $this->ct->view->BUtils()->getInvoiceNumber($formData['clients'][$i]);
                        $lt->invoice_number_li = $invoice->invoice_number;
                        $lt->amount_due = $formData['amount_due'][$i];
                        $lt->amount_paid = '';
                        $lt->check_number = '';
                        $lt->payment_status = 'Due';
                        $lt->invoice_status = 'Open';
                        $lt->fiscal_year = $invoice->fiscal_year;
                        $lt->description = $formData['description'][$i];
                        $lt->quarter = $invoice->quarter;
                        $lt->client_id = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $formData['clients'][$i]));
                        $lt->invoice_id = $invoice;
                        $this->ct->em->persist($lt);
                    }
                    $i++;
                }
                
                foreach($allItems as $item){
                	$this->ct->em->remove($item);
                	$this->ct->em->flush();
                }
                
                $invoice->amount_due = array_sum($formData['amount_due']);
                $this->ct->em->persist($invoice);
                $this->ct->em->flush();

                if ($formData['save_type'] === 'save') {
                    echo Zend_Json::encode(array('success' => true, 'message' => 'Invoice updated successfully!'));
                } elseif ($formData['save_type'] === 'save_n_send') {
                    echo Zend_Json::encode(array('success' => true, 'message' => 'Invoice updated successfully!', 'vendor' => $invoice->vendor_id->id, 'inv_id' => $invoice->id));
                }
            } else {
                $form->populate($formData);
            }
        }
    }

    /**
     * Function to email invoce to vendor
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function email() {
        $this->ct->getHelper('BUtilities')->setPopupLayout();
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
        $form = new Admin_Form_EmailInvoice();
        $this->ct->view->form = $form;

        if ($this->ct->getRequest()->isPost()) {
            $formData = $this->ct->getRequest()->getPost();
            if ($form->isValid($formData)) {
                /**
                 * send invoice email to vendor
                 */
                $params = array(
                    'to' => $form->getValue('to_email'),
                    'to_name' => $form->getValue('to_name'),
                    'from' => $form->getValue('from_email'),
                    'from_name' => $form->getValue('from_name'),
                    'subject' => $form->getValue('subject'),
                    'body' => $form->getValue('email_body'),
                );
                if ($form->getValue('cc_email') != null) {
                    $params['cc'] = preg_split('/[;, ]/', $form->getValue('cc_email'));
                }
                $this->ct->getHelper('BUtilities')->send_mail($params);
                $invoice->email_date .= date('m-d-Y') . ",";
                $this->ct->em->persist($invoice);
                $this->ct->em->flush();
                $this->ct->view->result = array('success' => true, 'message' => 'Invoice email send successfully!');
            } else {
                $form->populate($formData);
            }
        } else {
            $lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id' => (int) $invoice->id));
            $admin = $this->ct->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->ct->getHelper('BUtilities')->getLoggedInUser(), 'account_type' => (int) ACC_TYPE_ADMIN));
            $this->ct->view->invoice = $invoice;
            $this->ct->view->lineitems = $lineitems;
            $this->ct->view->invoice_template = $this->ct->view->render('/invoice/invoice-template.phtml');
            $this->ct->view->username = $invoice->vendor_id->username;
            $email_body = $this->ct->view->render('/invoice/invoice-email-body.phtml');
            $to_email = preg_split('/[;,]/', $invoice->vendor_id->email);
            $form_email = preg_split('/[;,]/', $admin->email);
            $populate_data = array(
                'to_email' => $to_email['0'],
                'to_name' => $invoice->vendor_id->first_name,
                'from_email' => $form_email['0'],
                'from_name' => $admin->first_name,
                'subject' => 'Greek Licensing Invoice',
                'email_body' => $email_body
            );
            $form->populate($populate_data);
        }
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
        $invoice = $this->ct->em->getRepository("BL\Entity\Invoice")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
        $lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->getLineItemsForPDF($invoice->id);
        $this->ct->view->invoice = $invoice;
        $this->ct->view->lineitems = $lineitems;
        $html = $this->ct->view->render('invoice/invoice-pdf-template.phtml');
	$header = $this->ct->view->render('invoice/invoice-pdf-template-header.phtml');
//        print_r($html);
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
        $this->ct->view->BUtils()->getPDF2($params);
    }
    /**
     * Function to export Annual inovice as PDF
     * @author Jace
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
     public function exportInvoiceAsPdf(){
 	$this->ct->getHelper('BUtilities')->setNoLayout();
        $invoice = $this->ct->em->getRepository("BL\Entity\Invoice")->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
        $lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->getLineItemsForPDF($invoice->id);
        $this->ct->view->invoice = $invoice;
        $this->ct->view->lineitems = $lineitems;
        $html = $this->ct->view->render('invoice/invoice-pdf-template.phtml');
	if($this->ct->view->invoice->invoice_type=="Annual")
        	$header = $this->ct->view->render('invoice/invoice-pdf-template-header-annual.phtml');
	elseif(true)
		$header = $this->ct->view->render('invoice/invoice-pdf-template-header.phtml');
//        print_r($html);
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
     * Function to mark invoice as paid/partial paid/void/waive
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function markAs() {
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
//        $this->ct->view->BUtils()->doctrine_dump($invoice);

        if ($this->ct->getRequest()->getParam('mark') === 'paid') {
            $this->ct->getHelper('BUtilities')->setEmptyLayout();

            $form = new Admin_Form_PartialPayment();
            $this->ct->view->form = $form;
            $this->ct->view->total_due = $invoice->amount_due;
            //$form->getElement('payment_amount')->setAttrib('disable', array('value'));
            $form->getElement('payment_amount')->setValue($invoice->amount_due - $invoice->amount_paid);
            $this->ct->view->invoice = $invoice;
            $form->populate(array('payment_date' => date('m/d/Y')));
            $this->ct->getHelper('viewRenderer')->setRender('mark-as-paid');
            /*
            $this->ct->getHelper('BUtilities')->setNoLayout();
            $invoice->invoice_status = 'Closed';
//            $invoice->payment_status = 'Due';
            $this->ct->em->persist($invoice);
            $this->ct->em->flush();
            $this->markAsPaid($invoice);
            echo Zend_Json::encode(array('success' => true, 'message' => 'Payment in full has been recorded for this invoice. This invoice in now closed.'));
            */
        } else if ($this->ct->getRequest()->getParam('mark') === 'partial_paid') {
            $this->ct->getHelper('BUtilities')->setEmptyLayout();

            $form = new Admin_Form_PartialPayment();
            $this->ct->view->form = $form;
            $this->ct->view->total_due = $invoice->amount_due;
             $this->ct->view->total_remaining = $invoice->amount_due - $invoice->amount_paid;
             $this->ct->view->invoice = $invoice;
            //$form->getElement('payment_amount')->setValue($invoice->amount_due - $invoice->amount_paid);
            $form->populate(array('payment_date' => date('m/d/Y')));
            $this->ct->getHelper('viewRenderer')->setRender('partial-payment');
//            $invoice->invoice_status = 'Partially Paid';
//            $this->ct->em->persist($invoice);
//            $this->ct->em->flush();
/*
            if ($this->ct->getRequest()->isPost()) {
                $formData = $this->ct->getRequest()->getPost();
//                print_r($formData);
                if ($form->isValid($formData)) {
                    $this->ct->view->form_data = $formData;
                    $this->ct->view->invoice = $invoice;
                    $this->ct->view->lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($invoice->id);

		    $this->ct->getHelper('viewRenderer')->setRender('partial-payment-lineitems');

                } else {
                    $form->populate($formData);
                }
            }//*/
        } else if ($this->ct->getRequest()->getParam('mark') === 'void') {
            $this->ct->getHelper('BUtilities')->setNoLayout();

            $invoice->invoice_status = 'Voided';
            $this->ct->em->persist($invoice);
            $this->ct->em->flush();

            echo Zend_Json::encode(array('success' => true, 'message' => 'Payment in full has been recorded for this invoice. This invoice in now void.'));
        } else if ($this->ct->getRequest()->getParam('mark') === 'waive') {
            $this->ct->getHelper('BUtilities')->setNoLayout();

            $invoice->invoice_status = 'Waived';
            $this->ct->em->persist($invoice);
            $this->ct->em->flush();

            echo Zend_Json::encode(array('success' => true, 'message' => 'Payment in full has been recorded for this invoice. This invoice in now waive.'));
        }
    }
    
    public function reviewPaid(){
    	 
    }
    
    /**
     * Function to add payment
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @param
     * @return void
     */
    public function markAsPaid(){   //markAsPaid($invoice) Object $invoice Invoice details

        $this->ct->getHelper('BUtilities')->setNoLayout();
//        print_r($this->ct->getRequest());
        $payment_ref = $this->ct->getRequest()->getParam('payment_ref');
        $payment_type = $this->ct->getRequest()->getParam('payment_type');
        $payment_amount = $this->ct->getRequest()->getParam('payment_amount');
        $payment_date = $this->ct->getRequest()->getParam('payment_date');
//        echo $payment_ref." ".$payment_type." ".$payment_amount;
//        die('---------');
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
        $inv_lineitems = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_id' => (int) $invoice->id));
//        $this->ct->view->BUtils()->doctrine_dump($invoice);

        /**
         * update invoice
         */
        //$invoice->amount_paid = isset($invoice->amount_paid) ? ((float) $invoice->amount_paid + (float) $payment_amount) : (float) $payment_amount;
        //$invoice->amount_due = isset($invoice->amount_due) ? ((float) $invoice->amount_paid - (float) $payment_amount) : ($invoice->amount_due - $payment_amount);
        if ($invoice->invoice_type=='Refund')
        {
            $invoice->amount_paid= - $invoice->amount_due;
        }
        else
        {
            $invoice->amount_paid=$invoice->amount_due;
        }
        $invoice->amount_due=0;
        $invoice->invoice_status = 'Closed';
        $invoice->payment_status = $payment_type;
        $this->ct->em->persist($invoice);
        $this->ct->em->flush();

        //update lineitems
        foreach ($inv_lineitems as $inv_l) {
            $inv_l->check_number = $payment_ref;
            $inv_l->payment_status = $payment_type;
            $inv_l->amount_paid = $inv_l->amount_due;
            $inv_l->invoice_status = 'Closed';
            $this->ct->em->persist($inv_l);
        }
        $this->ct->em->flush();

        /**
         * add payment
         */
        $payment = new \BL\Entity\Payment();
        $payment->amount_paid = $invoice->amount_paid;
        $payment->amount_remaining = $invoice->amount_due;
        $payment->record_date = new \DateTime(date("Y-m-d H:i:s"));
//        $payment->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
        $payment->payment_year = $invoice->fiscal_year;
        $payment->payment_quarter = $invoice->quarter;
        $payment->payment_month = date('m');
        $payment->check_num = $payment_ref;
        $payment->kp_payment = '';
        $payment->vendor = $invoice->vendor_id;
        $payment->invoice = $invoice;
        $this->ct->em->persist($payment);
        $this->ct->em->flush();

        $vendorId=$invoice->vendor_id->id;
        //update payment lineitems
        foreach ($inv_lineitems as $inv_li) {
            /**
             * select license for royalty commission
             */
            //$license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => (int) $invoice->vendor_id->id, 'status' => (int) 4));
            $clientOperation = $this->ct->em->getRepository("BL\Entity\ClientOperation")->findOneBy(array('user_id' => (int) $inv_li->client_id->id));

            $license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => $vendorId));
            $pmt_lineitem = new \BL\Entity\PaymentLineItems();
            $pmt_lineitem->payment_id = '';
            $pmt_lineitem->kp_lineitem = 'L' . $inv_li->id;
            $pmt_lineitem->record_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->payment_year = $payment->payment_year;
            $pmt_lineitem->payment_quarter = $payment->payment_quarter;
            $pmt_lineitem->payment_month = $payment->payment_month;
            $pmt_lineitem->sharing = $license->sharing=='yes' ? 1: $clientOperation->sharing;
            $pmt_lineitem->percent_amc = isset($clientOperation) ? $clientOperation->commission_per : 0;
            $pmt_lineitem->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->client = $inv_li->client_id;
            $pmt_lineitem->pmt_id = $payment;
            if($invoice->invoice_type == 'Late Fee')
            {
                 $pmt_lineitem->late_paid = $inv_li->amount_paid;
            }
            elseif($invoice->invoice_type == 'Annual')
            {
                $pmt_lineitem->adv_paid = $inv_li->amount_paid;
            }
            else
            {
                $pmt_lineitem->amount_paid = $inv_li->amount_paid;
            }

            $this->ct->em->persist($pmt_lineitem);
        }
        $this->ct->em->flush();

        echo Zend_Json::encode(array('success' => true, 'message' => 'Payment in full has been recorded for this invoice. This invoice in now closed.'));
    }

    /**
     * Function to ajax mark invoice as partial and add payment
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function markAsPartialPaid() {
        $this->ct->getHelper('BUtilities')->setNoLayout();
        $invoice = $this->ct->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor_id' => (int) $this->ct->getRequest()->getParam('id')));
//        print_r($this->ct->getRequest());
//        print_r($this->ct->getRequest()->getParam('payment_amount'));
        $lineitem_id = $this->ct->getRequest()->getParam('lineitem_id');
        $amount_allocate = $this->ct->getRequest()->getParam('amount_allocate');
        $payment_ref = $this->ct->getRequest()->getParam('payment_ref');
        $payment_type = $this->ct->getRequest()->getParam('payment_type');
        $payment_amount = $this->ct->getRequest()->getParam('payment_amount');
        $payment_date = $this->ct->getRequest()->getParam('payment_date');
//        print_r($lineitem_id);
//        print_r($amount_allocate);
//        print_r($payment_ref);
//        print_r($payment_type);
//        print_r($payment_amount);
//        print_r($payment_date);

        $i = 0;
        $total_due = 0;
        $total_paid = 0;
        //find and update lineitems
        foreach ($lineitem_id as $l_id) {
            $lineitem = $this->ct->em->getRepository('BL\Entity\InvoiceLineItems')->findOneBy(array('id' => (int) $l_id));
            //$lineitem->amount_due = ($lineitem->amount_due - $amount_allocate[$i]);
             $lineitem->amount_due = ($lineitem->amount_due);
            $lineitem->amount_paid = ($lineitem->amount_paid + $amount_allocate[$i]);
            $lineitem->check_number = $payment_ref;
            $lineitem->payment_status = $payment_type;
            $lineitem->invoice_status = 'Partially Paid';
            $total_due += $lineitem->amount_due;
            $total_paid += $lineitem->amount_paid;
            $i++;
            $this->ct->em->persist($lineitem);
        }
        $this->ct->em->flush();

        //update invoice
        $invoice->amount_due = $total_due;
        $invoice->amount_paid = $total_paid;
        $invoice->invoice_status = 'Partially Paid';
        $invoice->payment_status = $payment_type;
        $this->ct->em->persist($invoice);
        $this->ct->em->flush();

        //update payment
        $payment = $this->ct->em->getRepository('BL\Entity\Payment')->findOneBy(array('invoice' => (int) $this->ct->getRequest()->getParam('inv_id'), 'vendor' => (int) $this->ct->getRequest()->getParam('id')));
        //echo "<pre>";        print_r($payment); exit;
        //$payment = new \BL\Entity\Payment();
        if(!empty($payment))
        {
        $payment->amount_paid = $total_paid;
        $payment->amount_remaining = $total_due - $total_paid;
        $payment->record_date = new \DateTime(date('Y-m-d', strtotime($payment_date)));
//            $payment->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
        $payment->payment_year = $invoice->fiscal_year;
        $payment->payment_quarter = $invoice->quarter;
        $payment->payment_month = date('m');
        $payment->check_num = $payment_ref;
        $payment->kp_payment = '';
        $payment->vendor = $invoice->vendor_id;
        $payment->invoice = $invoice;
        $this->ct->em->persist($payment);
        $this->ct->em->flush();

        $invoice_lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->getLineItemsByInvoice($this->ct->getRequest()->getParam('inv_id'));

        $this->ct->em->getRepository("BL\Entity\PaymentLineItems")->deletePaymentLineItem($payment->id);

         //update payment lineitems
        foreach ($invoice_lineitems as $inv_li) {
            //select license for royalty commission
           // $license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => (int) $invoice->vendor_id->id, 'status' => (int) 4));
            $clientOperation = $this->ct->em->getRepository("BL\Entity\ClientOperation")->findOneBy(array('user_id' => (int) $inv_li->client_id->id));
            $vendorId=$invoice->vendor_id->id;
            $license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => (int) $vendorId));

            $pmt_lineitem = new \BL\Entity\PaymentLineItems();
            $pmt_lineitem->payment_id = '';
            $pmt_lineitem->kp_lineitem = 'L' . $inv_li->id;
            $pmt_lineitem->record_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->payment_year = $payment->payment_year;
            $pmt_lineitem->payment_quarter = $payment->payment_quarter;
            $pmt_lineitem->payment_month = $payment->payment_month;
            $pmt_lineitem->sharing = isset($license) && $license->sharing=='yes' ? 1: $clientOperation->sharing;
            $pmt_lineitem->percent_amc = isset($clientOperation) ? $clientOperation->commission_per : 0;
            $pmt_lineitem->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->client = $inv_li->client_id;
            $pmt_lineitem->pmt_id = $payment;
            $pmt_lineitem->amount_paid = $inv_li->amount_paid;
            $this->ct->em->persist($pmt_lineitem);
        }
        $this->ct->em->flush();

        // end of update payment
        }
        else{
         //add payment
        $payment = new \BL\Entity\Payment();
        $payment->amount_paid = $total_paid;
        $payment->amount_remaining = $total_due - $total_paid;
        $payment->record_date = new \DateTime(date('Y-m-d', strtotime($payment_date)));
//            $payment->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
        $payment->payment_year = $invoice->fiscal_year;
        $payment->payment_quarter = $invoice->quarter;
        $payment->payment_month = date('m');
        $payment->check_num = $payment_ref;
        $payment->kp_payment = '';
        $payment->vendor = $invoice->vendor_id;
        $payment->invoice = $invoice;
        $this->ct->em->persist($payment);
        $this->ct->em->flush();

        $invoice_lineitems = $this->ct->em->getRepository("BL\Entity\InvoiceLineItems")->findBy(array('invoice_id' => $this->ct->getRequest()->getParam('inv_id')));

         //update payment lineitems
        foreach ($invoice_lineitems as $inv_li) {
            //select license for royalty commission
           // $license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => (int) $invoice->vendor_id->id, 'status' => (int) 4));
            $clientOperation = $this->ct->em->getRepository("BL\Entity\ClientOperation")->findOneBy(array('user_id' => (int) $inv_li->client_id->id));
            $vendorId=$invoice->vendor_id->id;
            $license = $this->ct->em->getRepository("BL\Entity\License")->findOneBy(array('client_id' => (int) $inv_li->client_id->id, 'vendor_id' => (int) $vendorId));

            $pmt_lineitem = new \BL\Entity\PaymentLineItems();
            $pmt_lineitem->payment_id = '';
            $pmt_lineitem->kp_lineitem = 'L' . $inv_li->id;
            $pmt_lineitem->record_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->payment_year = $payment->payment_year;
            $pmt_lineitem->payment_quarter = $payment->payment_quarter;
            $pmt_lineitem->payment_month = $payment->payment_month;
            $pmt_lineitem->sharing = $license->sharing=='yes' ? 1: $clientOperation->sharing;
            $pmt_lineitem->percent_amc = isset($clientOperation) ? $clientOperation->commission_per : 0;
            $pmt_lineitem->last_modified_date = new \DateTime(date("Y-m-d H:i:s"));
            $pmt_lineitem->client = $inv_li->client_id;
            $pmt_lineitem->pmt_id = $payment;
            $pmt_lineitem->amount_paid = $inv_li->amount_paid;
            $this->ct->em->persist($pmt_lineitem);
        }
        $this->ct->em->flush();
        }

        echo Zend_Json::encode(array('success' => true, 'message' => 'The partial payment has been recorded, the payment has been allocated to Greek Organizations and the invoice has be marked as partially paid.'));
    }

}
