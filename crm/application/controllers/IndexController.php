<?php

class IndexController extends Zend_Controller_Action
{

    protected $em = null;

    /**
     * Init Function
     * @author Noman
     * @copyright Blueliner Marketing
     * @version 0.1
     *
     */
    public function init()
    {
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
    }

    public function indexAction()
    {	
        if(false===$this->_helper->BUtilities->isLoggedIn()){
            $this->_redirect("login");
        }
    }

    public function addLicenseAction()
    {
        
        error_reporting(E_ALL);
        $value = "Testing";
        $this->view->value = $value;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/login');
    }

    public function historyAction()
    {
        // action body
    }

    public function errorAction()
    {
	$this->view->error =  $this->_getParam('id');
    }

    public function errorcauseAction()
    {
        // action body
    }
}
