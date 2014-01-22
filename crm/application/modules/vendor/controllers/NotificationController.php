<?php

class Vendor_NotificationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

     /**
     * Index controller for notification in vendor
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param
     * @return void
     */
    public function indexAction() {
         $this->_helper->JSLibs->load_jqui_assets();
         $this->_helper->JSLibs->load_dataTable_assets();
         $this->_helper->JSLibs->load_fancy_assets();
    }

     /**
     * Function to show notifications for vendor
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param
     * @return void
     */
    public function ajaxGetNotificationsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'id' =>$this->_helper->BUtilities->getLoggedInUser(),
            'type' => 'vendor'
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'n.time',
            '1' => 'n.title',
            '2' => 'n.message'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $params['vendor_id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['page'] = $this->_getParam('page', 'upcoming');
        $records = $this->em->getRepository("BL\Entity\Notification")->getNotificationDt($params);
        echo $records;       
    }

     /**
     * Function to show particular notification
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param event id
     * @return void
     */
    public function notificationDetailsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->notifications = $this->em->getRepository("BL\Entity\Notification")->findOneBy(array('id'=>$this->_getParam('id')));
    }
        /**
     * Function to update notification last view
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param <int> user id
     * @return void
     */
    public function updateNotificationLastViewAction(){
        $this->_helper->BUtilities->setNoLayout();
        $userId=$this->_getParam('userId');
        $n_last_views = $this->em->getRepository("BL\Entity\NotificationLastView")->findOneBy(array('user_id' =>  $userId));
        $today = new Zend_Date();
        $time = new \DateTime();
        $n_last_views->time=$time;
        $this->em->persist($n_last_views);
        $this->em->flush();
        $this->em->clear();
        Zend_Session::namespaceUnset('notification');
    }
    

}

