<?php

/**
 * Description of InvoiceController
 *
 * @author Masud
 */
class Vendor_InvoiceController extends Zend_Controller_Action {

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
    }

    /**
     * Function to show vendor invoices
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function indexAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        $this->view->vendor = $this->em->getRepository('BL\Entity\User')->findOneBy(array('id' => (int) $this->_helper->BUtilities->getLoggedInUser(), 'account_type' => ACC_TYPE_VENDOR));
//        $this->view->BUtils()->doctrine_dump($this->view->vendor);        
        $vendor_id = $this->_getParam('vendor_id');
        $vendor_id = !empty($vendor_id) ? $vendor_id : $this->_helper->BUtilities->getLoggedInUser();
        $this->view->vendor_id = $vendor_id;
    }

    /**
     * Function to get invoices by ajax call
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetInvoicesAction() {
        $this->_helper->BUtilities->setNoLayout();
        $invoice_model = new Vendor_Model_Invoice($this);
        echo $invoice_model->ajaxGetInvoices();
    }

    /**
     * Function to show invoice
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showInvoiceAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $invoice_model = new Vendor_Model_Invoice($this);
        $invoice_model->showInvoice();
    }

    /**
     * Function to show invoice lineitems
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showInvoiceLineItemsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $items = $this->em->getRepository('BL\Entity\InvoiceLineItems')->getLineItemsByInvoice($this->_getParam('id'));
        $this->view->items = $items;
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
        $invoice_model = new Vendor_Model_Invoice($this);
        $invoice_model->exportAsPdf();
    }

    /**
     * Function to show vendor payments
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function paymentsAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_dataTable_assets', 'load_fancy_assets'));
        $this->view->vendor_id = $this->_helper->BUtilities->getLoggedInUser();
    }

    /**
     * Function to get vendor payments by ajax call
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ajaxGetPaymentsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $vendor_model = new Vendor_Model_Invoice($this);
        echo $vendor_model->getVendorPayments();
    }

    /**
     * Function to 
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function showPaymentLineItemsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $items = $this->em->getRepository('BL\Entity\PaymentLineItems')->getLineItemsByPayment($this->_getParam('id'));
        $this->view->items = $items;
    }

    /**
     * Function to invoice payment through online
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function onlinePaymentAction() {
        $invoice_model = new Vendor_Model_Invoice($this);
        $invoice_model->onlinePayment();
    }

    /**
     * Function to 
     * @author Masud inovice check payment
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function checkPaymentAction() {
        $this->_helper->JSLibs->do_call(array('load_tinymce_assets'));
        $invoice_model = new Vendor_Model_Invoice($this);
        $invoice_model->checkPayment();
    }

    /**
     * Function to add bank information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */     
     public function addBankInfoAction() {
         $invoice_model = new Vendor_Model_Invoice($this);         
         $invoice_model->addBankInfo();
     }
     
    /**
     * Function to show vendor bank information
     * @author Masud
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function bankInfoAction() {
        $this->_helper->JSLibs->do_call(array('load_jqui_assets', 'load_fancy_assets'));
        $invoice_model = new Vendor_Model_Invoice($this);
        $invoice_model->bankInfo();
    }

}