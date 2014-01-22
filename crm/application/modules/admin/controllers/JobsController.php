<?php

class Admin_JobsController extends Zend_Controller_Action {

    protected $em;

    public function init() {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction() {
        $this->view->pending_jobs = $this->em->getRepository("BL\Entity\JobQueues")->findBy(array('status' => 'pending'));
        $this->view->all_jobs = $this->em->getRepository("BL\Entity\JobQueues")->findAll();
    }

    /**
     * Function to terminate a running or pending job
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function terminateAction() {
        $this->_helper->BUtilities->setNoLayout();
        $job_id = $this->_getParam('id');
        $job = $this->em->find("BL\Entity\JobQueues", (int) $job_id);
        if ($job->status <> "terminated") {
            $job->status = "terminated";
            $job->process_details = "Terminated at " . date("Y-m-d H:i:s");
            $this->em->flush($job);
        }
        $this->redirect("admin/jobs");
    }

}

