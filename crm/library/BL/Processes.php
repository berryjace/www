<?php

/**
 * Class to handle long running tasks
 *
 * @author mahbub
 */
class BL_Processes {

    public static $em;

    public static function initDoctrine() {
        $doctrineContainer = Zend_Registry::get('doctrine');
        self::$em = $doctrineContainer->getEntityManager();
    }

    public static function registerNewTask($function_name) {
        self::initDoctrine();
        $newTask = new BL\Entity\JobQueues();
        $newTask->process_id = "";
        $newTask->status = "pending";
        $newTask->percent_done = 0;
        $newTask->function_name = $function_name;
        self::$em->persist($newTask);
        self::$em->flush();
        return $newTask;
    }

    public static function sendInvoices() {
        self::initDoctrine();
        /*
         * Check if there's a pending task of such workload in DB.
         * If yes, finish it. If not, register a new job queue and process it
         */
        $pending_invoices = self::$em->getRepository("BL\Entity\JobQueues")->findOneBy(array("status" => "pending", "function_name" => "generate_adv_invoices"));

        if (!sizeof($pending_invoices)) {
            /**
             * Register new task
             */
            $pending_invoices = self::registerNewTask('generate_adv_invoices');
        }

        $front = Zend_Controller_Front::getInstance();

        $params = array('account_type' => ACC_TYPE_VENDOR, 'user_status' => 'Current');

        $vendors = self::$em->getRepository('BL\Entity\User')->getUsersByTypeAndStatus($params);



        $vendors_size = count($vendors);

        $count = 0;

        if($counter_details=unserialize($pending_invoices->process_details)){
            $count=$counter_details['count_done'];
        }

        //foreach ($vendors as $key => $vendor) {
        for ($i=$count; $i<$vendors_size; $i++) {
            if ($count++ >= 20) {
                break;
            }
            $licensed_clients = self::$em->getRepository('BL\Entity\License')->getClientsForVendorInvoice($vendors[$i]['id']);
            $vendor_obj = self::$em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $vendors[$i]['id']));
            $view = new Zend_View();
            $view->licensed_clients = $licensed_clients;
            $view->addScriptPath(APPLICATION_PATH . "/modules/admin/views/scripts/");
            $invoice_template = $view->render('/vendors/invoice-generate-adv-pmt-template.phtml');

            $invoice = new \BL\Entity\Invoice();
            $invoice->invoice_date = new DateTime();
            $invoice->created_at = new DateTime();
            $invoice->invoice_number = "INV_" . $vendor_obj->id . date('YmdHis');
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
            self::$em->persist($invoice);
            self::$em->flush();

            $invoiceSentLog = new \BL\Entity\InvoiceSentLog();
            $invoiceSentLog->quarter = $front->getRequest()->getParam('quarter');
            $invoiceSentLog->invoice_type = 'Annual';
            $invoiceSentLog->vendor_id = $vendor_obj;
            self::$em->persist($invoiceSentLog);
            self::$em->flush();

            //for saving lineitems
            foreach ($licensed_clients as $key => $client) {
                $lineItems = new \BL\Entity\InvoiceLineItems();
                $lineItems->lineitems_number = "INV_" . $client->client_id->id . date('YmdHis');
                $lineItems->invoice_number_li = $invoice->invoice_number;
                $lineItems->amount_due = 40;
                $lineItems->amount_paid = '';
                $lineItems->check_number = '';
                $lineItems->status = '';
                $lineItems->invoice_status = '';
                $lineItems->fiscal_year = BL_AMC::getCurrentFiscalYear();
                $lineItems->quarter = BL_AMC::getCurrentQarter();
                $lineItems->client_id = $client->client_id;
                self::$em->persist($lineItems);
                self::$em->flush();
            }
            self::$em->clear();

            $view->username = $vendor_obj->username;
            $view->invoice_template = $invoice_template;
            $email_body = $view->render('/vendors/invoice-email-body-template.phtml');
            $to_email = preg_split('/[;,]/', $vendor_obj->email);
            $mail_params = array(
                'to' => $to_email['0'],
                //'to' => "mahbub.blueliner@gmail.com",
                'to_name' => $vendor_obj->username,
                'from' => 'admin@greeklicensing.com',
                'from_name' => 'AMC Admin',
                'subject' => 'Greek licensing invoice notification',
                'body' => $email_body,
            );
            self::sendMail($mail_params);
            self::updateJob("generate_adv_invoices", "pending", array(
                'percent_done' => number_format(($count / $vendors_size)*100,2),
                'process_details' => serialize(array('count_done' => $count,'total_vendors'=>$vendors_size))
            ));
        }
    }

    public static function sendMail($params) {
        self::writeLog($params['to'] . "----" . $params['to_name']);
        /*
          $mail = new Zend_Mail();
          $mail->setType(Zend_Mime::MULTIPART_RELATED);
          $mail->addTo($params['to'], $params['to_name']);
          $mail->setFrom($params['from'], $params['from_name']);
          $mail->setSubject($params['subject']);
          $mail->setBodyHtml($params['body']);

          if (isset($params['cc'])) {
          $mail->addCc($params['cc']);
          }

          if (isset($params['file'])) {
          $attachment = new Zend_Mime_Part(file_get_contents($params['file']));
          $attachment->filename = basename($params['file']);
          $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
          $attachment->encoding = Zend_Mime::ENCODING_BASE64;
          $mail->addAttachment($attachment);
          }
          return $mail->send();
         */
    }


    public static function updateJob($function_name, $status, $params) {
        self::initDoctrine();
        $pending_invoices = self::$em->getRepository("BL\Entity\JobQueues")->findOneBy(array("status" => $status, "function_name" => $function_name));
        foreach ($params as $k => $v) {
            $pending_invoices->$k = $v;
        }
        self::$em->flush();
        self::$em->clear();
    }

    public static function writeLog($str) {
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/../tmp/applog.txt");
        $logger = new Zend_Log($writer);
        $logger->info($str);
    }

}