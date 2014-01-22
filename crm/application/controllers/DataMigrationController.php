<?php

/**
 * Controller to migration AMC data
 *
 * @author Masud
 */
class DataMigrationController extends Zend_Controller_Action {

    protected $em = null;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        
    }

    /**
     * Function to migrate vendor invoice and invoice lineitems
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function vendorInvoicesAction() {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes        
//        $invoices = $this->em->getRepository('BL\Entity\Invoice')->findAll();
//        $vendor_id = 234;
//        $invoices = $this->em->getRepository('BL\Entity\Invoice')->findBy(array('vendor_id' => (int) $vendor_id));
//        echo "vendor = ".$vendor_id." total = ";
//          $start = 0;
//        $end = sizeof($invoices);        
//        $this->updateInvoice($invoices, $start, $end); 
        
        $offset = 0;
        $limit = 50;
        $invoicesDt = $this->em->getRepository('BL\Entity\Invoice')->getInvoicesDt($offset, $limit);
//        echo sizeof($invoicesDt);
//        print_r($invoicesDt);
//        echo "<br />";
//        die('--------------');

        foreach ($invoicesDt as $i) {
            $invoices = $this->em->getRepository('BL\Entity\Invoice')->findOneBy(array('id' => (int) $i['id']));            
//            for ($i = 0; $i < sizeof($invoices); $i++) {
                echo $invoices->id . "   ";
                echo $invoices->invoice_number . "<br />";
                $lineItems = $this->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_number_li' => $invoices->invoice_number));
                if (sizeof($lineItems)) {
//                $this->view->BUtils()->doctrine_dump($lineItems);                
                    foreach ($lineItems as $key => $lineItem) {
                        $lineItem->invoice_id = $invoices;
                        $this->em->persist($lineItem);
                    }
                    $this->em->flush();
                }
//            }
        }   
        echo 'success';
    }

    /**
     * Function to migrate clients license insurance template
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function insuranceTemplateAction() {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes        
        $masterTemplate = $this->em->getRepository("BL\Entity\MasterTemplate")->findOneBy(array('id'=> (int) 31));
//        $this->_helper->BUtilities->doctrine_dump($masterTemplate);
        
        //for insert
//        $clientsProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findAll();
//
//        foreach ($clientsProfile as $clientProfile) {
//            $licenseTemplate = new \BL\Entity\LicenseTemplate();
//            $licenseTemplate->client = $clientProfile->user_id;
//            $licenseTemplate->template = $masterTemplate->template;
//            $licenseTemplate->has_insurance = true;       
//            $this->em->persist($licenseTemplate);
//            $this->em->flush();
//        }
        
        
        //for update
        $licenseTemplates = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy(array('has_insurance' => (int) 1));        
//        echo count($licenseTemplates);
        
        foreach ($licenseTemplates as $licenseTemplate){
            $licenseTemplate->template = $masterTemplate->template;
            $this->em->persist($licenseTemplate);
            $this->em->flush();
        }
        echo 'success';
    }
    
    /**
     * Function to migrate clients license non insurance template
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function nonInsuranceTemplateAction() {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes        
        $masterTemplate = $this->em->getRepository("BL\Entity\MasterTemplate")->findOneBy(array('id'=> (int) 30));
//        $this->_helper->BUtilities->doctrine_dump($masterTemplate);

        //for insert
//        $clientsProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findAll();
//
//        foreach ($clientsProfile as $clientProfile) {
//            $licenseTemplate = new \BL\Entity\LicenseTemplate();
//            $licenseTemplate->client = $clientProfile->user_id;
//            $licenseTemplate->template = $masterTemplate->template;
//            $licenseTemplate->has_insurance = NULL;       
//            $this->em->persist($licenseTemplate);
//            $this->em->flush();
//        }
        
        
        //for update
        $licenseTemplates = $this->em->getRepository("BL\Entity\LicenseTemplate")->findBy(array('has_insurance' => NULL));        
//        echo count($licenseTemplates);        
        foreach ($licenseTemplates as $licenseTemplate){
            $licenseTemplate->template = $masterTemplate->template;
            $this->em->persist($licenseTemplate);
            $this->em->flush();
        }
        echo 'success';
        $this->_helper->viewRenderer->setRender('insurance-template');
    }
    
    /**
     * Function to migrate client profile id data into users table
     * @author Masud
     * @version 0.1
     * @copyright BLueliner Marketing
     * @return void
     * @access public
     */
    public function clientProfileAction() {
        $clientsProfile = $this->em->getRepository('BL\Entity\ClientProfile')->findAll();
        //print_r($clientsProfile);
        foreach ($clientsProfile as $clientProfile) {
            $clientProfile->user_id->client_profile = $clientProfile;
            $this->em->persist($clientProfile->user_id);
            $this->em->flush();
        }
        $this->_helper->viewRenderer->setRender('index');
    }  
    
    /**
     * Function to migrate client operations table
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */    
    public function clientOperationAction() {        
        $users = $this->em->getRepository("BL\Entity\User")->findBy(array('account_type' => ACC_TYPE_CLIENT));
//        $this->_helper->BUtilities->doctrine_dump($clientNotes);                
        foreach($users as $user){
            $clientOperation = new \BL\Entity\ClientOperation();
            $clientOperation->notes = "Notes for applying vendor";
            $clientOperation->user_id = $this->em->getRepository("BL\Entity\User")->findOneBy(array('id' => (int) $user->id));      
            $this->em->persist($clientOperation);   
            $this->em->flush();
        }        
        $this->em->clear();    
        echo 'success';
        $this->_helper->viewRenderer->setRender('index');
    }
    
    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function updateInvoice($invoices, $start, $end) {
        for ($i = $start; $i < $end; $i++) {
            echo $invoices[$i]->id . "   ";
            echo $invoices[$i]->invoice_number . "<br />";

            $lineItems = $this->em->getRepository('BL\Entity\InvoiceLineItems')->findBy(array('invoice_number_li' => $invoices[$i]->invoice_number));
            if (sizeof($lineItems)) {
//                $this->view->BUtils()->doctrine_dump($lineItems);                
                foreach ($lineItems as $key => $lineItem) {
                    $lineItem->invoice_id = $invoices[$i];
                    $this->em->persist($lineItem);
//                    $this->view->BUtils()->doctrine_dump($lineItem);
                }
                $this->em->flush();
//                $this->em->clear();
            }
        }
    }

}