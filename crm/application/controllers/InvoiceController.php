<?php

/**
 * controller to handle commandline request with Gearman for long running invoice generation
 */
class InvoiceController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        $this->_helper->BUtilities->setNoLayout();
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function cliAction() {
        $this->getInvoiceGenerateAdvPmt();
    }

    public function getInvoiceGenerateAdvPmt() {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $params = array('account_type' => ACC_TYPE_VENDOR, 'user_status' => 'Current');

        $vendors = $this->em->getRepository('BL\Entity\User')->getUsersByTypeAndStatus($params);

        $count = 0;
        foreach ($vendors as $key => $vendor) {
            $count++;
            $licensed_clients = $this->em->getRepository('BL\Entity\License')->getClientsForVendorInvoice($vendor['id']);
            $vendor_obj = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $vendor['id']));
            $this->view->licensed_clients = $licensed_clients;
            $this->view->addScriptPath(APPLICATION_PATH. "/modules/admin/views/scripts/");
            $invoice_template = $this->view->render('/vendors/invoice-generate-adv-pmt-template.phtml');

            $invoice = new \BL\Entity\Invoice();
            $invoice->invoice_date = new DateTime(); //new DateTime(date($form->getValue('inv_date')));
            $invoice->created_at = new DateTime();
            $invoice->invoice_number = $this->view->BUtils()->getInvoiceNumber($vendor_obj->id);
            $invoice->invoice_type = 'Annual';
            $invoice->fiscal_year = BL_AMC::getCurrentFiscalYear();
            $invoice->quarter = BL_AMC::getCurrentQarter();
            $invoice->company_name = $vendor_obj->organization_name;
            $invoice->webpage = $vendor_obj->website;
            $invoice->invoice_term = 'Net 15 days';
            $invoice->address = $vendor_obj->address_line1 . " " . $vendor_obj->address_line2;
            $invoice->city = $vendor_obj->city;
            $invoice->state = $vendor_obj->state;
            $invoice->zip = $vendor_obj->zipcode;
            $invoice->phone1 = $vendor_obj->phone;
            $invoice->phone2 = $vendor_obj->phone2;
            $invoice->email = $vendor_obj->email;
            $invoice->fax = $vendor_obj->fax;
            $invoice->payment_status = 'Due';
            $invoice->display_record = '';
            $invoice->amount_due = 40 * (count($licensed_clients));
            $invoice->amount_paid = '';
            $invoice->vendor_id = $vendor_obj;
            $this->em->persist($invoice);
            $this->em->flush();

            $invoiceSentLog = new \BL\Entity\InvoiceSentLog();
            $invoiceSentLog->quarter = $this->getRequest()->getParam('quarter');
            $invoiceSentLog->invoice_type = 'Annual';
            $invoiceSentLog->vendor_id = $vendor_obj;
            $this->em->persist($invoiceSentLog);
            $this->em->flush();

            //for saving lineitems
            foreach ($licensed_clients as $key => $client) {
                $lineItems = new \BL\Entity\InvoiceLineItems();
                $lineItems->lineitems_number = $this->view->BUtils()->getInvoiceNumber($client->client_id->id);
                $lineItems->invoice_number_li = $invoice->invoice_number;
                $lineItems->amount_due = 40;
                $lineItems->amount_paid = '';
                $lineItems->check_number = '';
                $lineItems->status = '';
                $lineItems->invoice_status = '';
                $lineItems->fiscal_year = BL_AMC::getCurrentFiscalYear();
                $lineItems->quarter = BL_AMC::getCurrentQarter();
                $lineItems->client_id = $client->client_id;
                $this->em->persist($lineItems);
                $this->em->flush();
            }
            $this->em->clear();

            $this->view->username = $vendor_obj->username;
            $this->view->invoice_template = $invoice_template;
            $email_body = $this->view->render('/vendors/invoice-email-body-template.phtml');
            $to_email = preg_split('/[;,]/', $vendor_obj->email);
            $mail_params = array(
                //'to' => $to_email['0'],
                'to' => "mahbub.blueliner@gmail.com",
                'to_name' => $vendor_obj->username,
                'from' => 'admin@greeklicensing.com',
                'from_name' => 'AMC Admin',
                'subject' => 'Greek licensing invoice notification',
                'body' => $email_body,
//                'file' => $save_to
            );
            $this->getHelper('BUtilities')->send_mail($mail_params);
            if ($count === 1) {
                break;
            }
        }
    }

}
