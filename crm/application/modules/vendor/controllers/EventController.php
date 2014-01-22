<?php

class Vendor_EventController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->doctrineContainer = Zend_Registry::get('doctrine');
        $this->em = $this->doctrineContainer->getEntityManager();
        $this->_helper->JSLibs->load_fancy_assets(); //added for vendor sidebar event calendar
    }

     /**
     * Index controller for event in vendor
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
     * Function to show events for vendor
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param
     * @return void
     */
    public function ajaxGetEventsAction() {
        $this->_helper->BUtilities->setNoLayout();
        $params = array(
            'search' => $this->_getParam('sSearch', ''),
            'page_start' => $this->_getParam('iDisplayStart', 1),
            'draw_count' => $this->_getParam('sEcho', 1),
            'per_page' => $this->_getParam('iDisplayLength', 10),
            'id' =>$this->_helper->BUtilities->getLoggedInUser()
        );
        /**
         * Let's take care of the sorting column to be passed to doctrine.
         * DataTable sends params like iSortCol_0.
         */
        $sorting_cols = array(
            '0' => 'e.start_time',
            '1' => 'e.title',
            '2' => 'e.location'
        );

        $params['sort_key'] = $sorting_cols[$this->_getParam('iSortCol_0', 0)];
        $params['sort_dir'] = $this->_getParam('sSortDir_0', 'asc');
        $params['vendor_id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['page'] = $this->_getParam('page', 'upcoming');
        $json = $this->em->getRepository("BL\Entity\Event")->getVendorEvents($params);
        echo $json;        
    }

     /**
     * Function to show particular Event
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param event id
     * @return void
     */
    public function eventDetailsAction() {
        $this->_helper->BUtilities->setEmptyLayout();
        $this->_helper->JSLibs->load_jqui_assets();
        $this->view->events = $this->em->getRepository("BL\Entity\Event")->findOneBy(array('id'=>$this->_getParam('id')));        
    }
    
    /**
     * Function to get vendor events for sidebar calender
     * @author Masud
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access public
     * @param <string> $page
     * @return <array>
     */
    public function ajaxGetCalendarAction() {

        $this->_helper->BUtilities->setAjaxLayout();
        $params['id'] = $this->_helper->BUtilities->getLoggedInUser();
        $params['user_type_1'] = "all";
        $params['user_type_2'] = "all_vendor";
        $params['user_type_3'] = "random";
        $params['user_type_4'] = "v_crandom";
        $params['user_type_5'] = "c_vrandom";        
        $params['page'] = $this->_getParam('page', 1);
        $params['per_page'] = 5;
        $params['num_of_link'] = 5;
        $this->view->events = $this->em->getRepository('BL\Entity\Event')->getCalendarEvents($params);
    }
}

