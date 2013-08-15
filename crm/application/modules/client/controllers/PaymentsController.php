<?php

class Client_PaymentsController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
        $this->view->path=APPLICATION_PATH . '/../tmp/';
        $this->view->reports = $this->em->getRepository("BL\Entity\ClientReport")->findBy(array('client_id' =>  (int) $this->_helper->BUtilities->getLoggedInUser()));
    }
    public function downloadReportAction() {
            $report = $this->_getParam('report');
            $save_to = APPLICATION_PATH . '/../tmp/'.$report.'.pdf';
            if($save_to)
            {
               $filename = $save_to;
                header("Pragma: public");
                header("Expires: 0"); 
                header("Pragma: no-cache"); 
                header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");  
                header("Content-Type: application/force-download"); 
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header('Content-disposition: attachment; filename=' . basename($filename));
                header("Content-Type: application/pdf");
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: ' . filesize($filename));
                @readfile($filename);
                exit(0);
            }
       }

}

